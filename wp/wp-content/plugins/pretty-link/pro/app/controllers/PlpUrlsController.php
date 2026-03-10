<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PlpUrlsController extends PrliBaseController {
  public function load_hooks() {
    global $plp_options;

    // Go no further if URL replacements are off
    if(!$plp_options->url_replacement_is_on) { return; }

    // NOTE - This priority must be lower than social buttons bar
    $priority = apply_filters('prli_urls_content_filter_priority', 998);
    add_filter('the_content', array($this, 'replace_urls'), $priority);

    // BBPress integration
    add_filter('bbp_get_reply_content', array($this, 'replace_woo_bbpress_urls'), 11, 1);

    // WooCommerce short descriptions
    add_filter('woocommerce_short_description', array($this, 'replace_woo_bbpress_urls'), 11);

    if($plp_options->replace_keywords_in_feeds != 'none') {
      add_filter('the_content_feed', array($this,'replace_urls'), 1);
    }

    if($plp_options->replace_keywords_in_comments != 'none') {
      add_filter('comment_text', array($this,'replace_urls_in_comments'), 1);
    }

    if($plp_options->replace_keywords_in_feeds != 'none' && $plp_options->replace_keywords_in_comments != 'none') {
      add_filter('comment_text_rss', array($this,'replace_urls_in_comments'), 1);
    }

    add_filter('get_the_excerpt', array($this, 'excerpt_remove_url_replacement'), 1);

    $index_keywords = get_option('plp_index_keywords', false);
    if($plp_options->url_replacement_is_on && $index_keywords) {
      add_filter('cron_schedules', array($this,'intervals'));

      $num_builders = 2;
      $separation_t = MINUTE_IN_SECONDS;

      for($i=0; $i<$num_builders; $i++) {
        if (!wp_next_scheduled("plp_post_build_url_index{$i}")) {
          wp_schedule_event( (time() + ($separation_t * $i)), 'plp_post_build_url_index_interval', "plp_post_build_url_index{$i}" );
        }

        add_action("plp_post_build_url_index{$i}", array($this,'post_index_builder'));
      }

      if($plp_options->replace_keywords_in_comments != 'none') {
        add_action('wp_insert_comment', array($this, 'comment_inserted'), 10, 2);
        add_action('wp_set_comment_status', array($this, 'set_comment_status'), 10, 2);
      }
    }
  }

  public function intervals ($schedules) {
    $interval = 1 * MINUTE_IN_SECONDS;
    return array_merge(
      $schedules,
      array(
        'plp_post_build_url_index_interval' => array(
          'interval' => $interval,
          'display' => 'Pretty Link Post Build URL Index'
        ),
      )
    );
  }

  public function set_comment_status ($comment_id, $status) {
    if($status=='approve') {
      delete_comment_meta($comment_id, '_plp_comment_urls_updated_at');
    }
  }

  public function comment_inserted ($comment_id, $c) {
    if($c->comment_approved) {
      delete_comment_meta($comment_id, '_plp_comment_urls_updated_at');
    }
  }

  public function post_index_builder () {
    global $plp_options, $plp_url_replacement;

    $max_count = 2;

    $index_keywords = get_option('plp_index_keywords', false);
    if($plp_options->url_replacement_is_on && $index_keywords) {
      // Index URLs for Posts
      $post_ids = $plp_url_replacement->get_indexable_posts($max_count);
      if(!empty($post_ids)) {
        for ($i=0; ($i < count($post_ids)); $i++) {
          $plp_url_replacement->index_post($post_ids[$i]);
        }
        return; // Short circuit
      }

      if($plp_options->replace_keywords_in_comments == 'both' || $plp_options->replace_keywords_in_comments == 'urls') {
        // Index URLs for Comments
        $comment_ids = $plp_url_replacement->get_indexable_comments($max_count);
        if(!empty($comment_ids)) {
          for ($i=0; ($i < count($comment_ids)); $i++) {
            $plp_url_replacement->index_comment($comment_ids[$i]);
          }
          return; // Short circuit
        }
      }
    }
  }

  /**
   * Performs URL replacements in WC and bbPress post types.
   *
   * @access public
   * @param string $content The current content for the post.
   * @return string
   */
  public function replace_woo_bbpress_urls($content) {
    global $post, $plp_options;

    $current_filter = current_filter();

    // Make sure we're running this for the appropriate hooks.
    if($current_filter != 'bbp_get_reply_content' && $current_filter != 'woocommerce_short_description') {
      return $content;
    }

    if(isset($post->post_type) && in_array($post->post_type, $plp_options->url_replacement_cpts)) {
      return $this->replace_urls($content,'',false);
    }

    return $content;
  }

  public function replace_urls($content, $request_uri = '', $allow_header_footer = true) {
    global $post, $prli_link, $prli_blogurl, $plp_url_replacement, $plp_options;

    if(!isset($post) || !isset($post->ID)) { return $content; }

    //*************************** the_content static caching ***************************//
    // the_content CAN be run more than once per page load
    // so this static var prevents stuff from happening twice
    // like cancelling a subscr or resuming etc...
    static $already_run = array();
    static $new_content = array();
    static $content_length = array();

    //Init this post's static values
    if(!isset($new_content[$post->ID]) || empty($new_content[$post->ID])) {
      $already_run[$post->ID] = false;
      $new_content[$post->ID] = '';
      $content_length[$post->ID] = -1;
    }

    //Have we been here before?
    if($already_run[$post->ID] && strlen($content) == $content_length[$post->ID]) {
      return $new_content[$post->ID];
    }

    $content_length[$post->ID] = strlen($content);
    $already_run[$post->ID] = true;
    //************************* end the_content static caching *************************//

    //Needed to get around an issue with some plugins and themes that add random &nbsp;'s all over the place
    if(apply_filters('plp_keywords_replace_nbsp', false)) {
      $content = str_replace('&nbsp;', ' ', $content);
    }

    //Revert WP apostrophe and ampersand formatting
    $content = str_replace(array('&#8217;'), array("'"), $content);
    $content = str_replace(array('&amp;'), array("&"), $content); //Keywords with & will finally work

    $replacements_happened = false;

    if($plp_options->url_replacement_is_on) {
      $plp_post_options = PlpPostOptions::get_options($post->ID);

      // Make sure URL replacements haven't been disabled on this page / post
      if(!$plp_post_options->disable_url_replacements) {
        // If post password required and it doesn't match the cookie.
        // Just return the content unaltered -- we don't want to cache the password form.
        if(post_password_required($post)) {
          $new_content[$post->ID] = $content;
          return $new_content[$post->ID];
        }

        // If we're replacing in a feed, then make sure URL replacements can run.
        if(is_feed() && $plp_options->replace_keywords_in_feeds != 'both' && $plp_options->replace_keywords_in_feeds != 'urls') {
          $new_content[$post->ID] = $content;
          return $new_content[$post->ID];
        }

        // do a keyword replacement per post and per request_uri
        // so we can handle <!--more--> tags, feeds, etc.
        if($request_uri == '') {
          $request_uri = $_SERVER['REQUEST_URI'];
        }

        // Grab allowed post types for URL replacements.
        $allowed_url_cpts = $plp_options->url_replacement_cpts;

        // URL Replacements go first
        if(in_array($post->post_type, $allowed_url_cpts) && ($urls_to_links = $plp_url_replacement->getURLToLinksArray())) {
          foreach($urls_to_links as $url => $links) {
            $urlrep = $links[array_rand($links)];

            // if the url is blank then skip it
            if(preg_match("#^\s*$#",$url)) { continue; }

            $urlregex = '#'.preg_quote($url,'#').'#';

            // If any url matches then we know there were replacements
            if(!$replacements_happened && preg_match( $urlregex, $content )) {
              $replacements_happened = true;
            }

            $content = preg_replace($urlregex, $urlrep, $content);
          }
        }

        // Any remaining non-pretty links will now be pretty linked if url/pretty link
        // replacement has been enabled on this blog
        if(in_array($post->post_type, $allowed_url_cpts) && $plp_options->replace_urls_with_pretty_links) {
          $using_elementor = get_post_meta($post->ID, '_elementor_edit_mode', true);

          if(apply_filters('prli_replace_urls_decode_content', (!$using_elementor), $content)) {
            $content = html_entity_decode(rawurldecode($content));
          }

          preg_match_all('#<a.*?href\s*?=\s*?[\'"](https?://.*?)[\'"]#mi', $content, $matches);

          //Filter out our blacklist domains so they don't get replaced
          if(!empty($plp_options->replace_urls_with_pretty_links_blacklist) && !empty($matches[1])) {
            $blacklist = preg_split('/[\r\n]+/', $plp_options->replace_urls_with_pretty_links_blacklist, -1, PREG_SPLIT_NO_EMPTY);

            foreach($blacklist as $bl_url) {
              $bl_url_host = parse_url($bl_url, PHP_URL_HOST);

              foreach($matches[1] as $key => $rep_url) {
                $rep_url_host = parse_url($rep_url, PHP_URL_HOST);

                if($bl_url_host == $rep_url_host) {
                  unset($matches[1][$key]);
                }
              }
            }

            //reindex the array
            $matches[1] = array_values($matches[1]);
          }

          $prli_lookup = $prli_link->get_target_to_pretty_urls( $matches[1], true );

          if($prli_lookup !== false && is_array($prli_lookup)) {
            //Using this one to prevent partial url replacements -- seems to be working but I'm not 100% sure about the # of escapes on the double quote's
            $url_patterns = array_map(
              function($target_url) {
                return '#["\']' . preg_quote($target_url, '#') . '["\']#';
              },
              array_keys($prli_lookup)
            );

            $url_replacements = array_values(array_map(
              function($pretty_urls) {
                return $pretty_urls[0];
              },
              $prli_lookup
            ));

            if($plp_options->url_links_open_new_window) {
              $url_patterns[] = "#<a\s#";
              $url_replacements[] = '<a target="_blank" ';
            }

            $content = preg_replace($url_patterns, $url_replacements, $content);
          }
        }
      }
    }

    if($allow_header_footer && $replacements_happened && $plp_options->enable_link_to_disclosures) {
      ob_start();

      ?>
      <div class="prli-link-to-disclosures">
        <a href="<?php echo esc_url($plp_options->disclosures_link_url); ?>"><?php echo esc_html($plp_options->disclosures_link_text); ?></a>
      </div>
      <?php

      $disclosure_link = ob_get_clean();

      if(!preg_match('/prli-link-to-disclosures/', $content)) {
        if($plp_options->disclosures_link_position=='top') {
          $content = $disclosure_link.$content;
        }
        else if($plp_options->disclosures_link_position=='top_and_bottom') {
          $content = $disclosure_link.$content.$disclosure_link;
        }
        else {
          $content = $content.$disclosure_link;
        }
      }
    }

    $new_content[$post->ID] = $content;
    return $new_content[$post->ID];
  }

  public function replace_urls_in_comments( $content ) {
    global $plp_options;

    if($plp_options->replace_keywords_in_comments == 'both' || $plp_options->replace_keywords_in_comments == 'urls') {
      // We don't care if it's a real uri -- it's used as an index
      //$request_uri = "#prli-comment-{$comment->comment_ID}";
      $request_uri = '#prli-comment-' . PlpUtils::base36_encode(mt_rand());

      return $this->replace_urls( $content, $request_uri, false );
    }

    return $content;
  }

  // Removes URL replacement from excerpts
  public function excerpt_remove_url_replacement($excerpt) {
    remove_filter('the_content', array($this, 'replace_urls'));
    return $excerpt;
  }
}
