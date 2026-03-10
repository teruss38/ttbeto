<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

class PlpOptions {
  public $pages_auto;
  public $posts_auto;
  public $pages_category;
  public $posts_category;
  public $autocreate;

  public $default_social_buttons;
  public $social_buttons;
  public $social_buttons_placement;
  public $social_buttons_show_in_feed;
  public $social_buttons_padding;
  public $social_posts_buttons;
  public $social_pages_buttons;

  public $keyword_replacement_is_on;
  public $keywords_per_page;
  public $keyword_links_per_page;
  public $keyword_links_open_new_window;
  public $keyword_links_nofollow;
  public $keyword_links_sponsored;
  public $set_keyword_thresholds;
  public $keyword_enable_content_cache; // DEPRECATED
  public $url_replacement_is_on;
  public $replace_urls_with_pretty_links;
  public $url_links_open_new_window;
  public $replace_urls_with_pretty_links_blacklist;
  public $url_replacement_cpts;
  public $replace_keywords_in_comments;
  public $replace_keywords_in_feeds;
  public $enable_link_to_disclosures;
  public $disclosures_link_url;
  public $disclosures_link_text;
  public $disclosures_link_position;
  public $enable_keyword_link_disclosures;
  public $keyword_link_disclosure;
  public $keyword_replacement_cpts;
  public $keyword_link_hover_custom_css;
  public $keyword_link_custom_css;
  public $twitter_handle;
  public $posts_group;
  public $pages_group;
  public $twitter_oauth_tokens;
  public $twitter_hash_tags;
  public $twitter_posts_button;
  public $twitter_pages_button;
  public $twitter_auto_post_post;
  public $twitter_auto_post_page;
  public $twitter_badge_style;
  public $twitter_badge_placement;
  public $twitter_badge_hidden;
  public $twitter_badge_hidden_on_homepage;
  public $twitter_badge_show_in_feed;
  public $twitter_posts_comments;
  public $twitter_pages_comments;
  public $twitter_comments_header;
  public $twitter_comments_height;

  public $use_prettylink_url;
  public $prettylink_url;

  public $min_role;

  public $allow_public_link_creation;
  public $use_public_link_display_page;
  public $public_link_display_page;

  public $prettybar_hide_attrib_link;
  public $prettybar_attrib_url;

  public $google_tracking;
  public $google_tracking_str;

  public $generate_qr_codes_str;
  public $generate_qr_codes;

  public $qr_code_links_str;
  public $qr_code_links;

  // QR Code Logo Settings
  public $qr_logo_attachment_id;
  public $qr_logo_attachment_id_str;
  public $qr_logo_size_percent;
  public $qr_logo_size_percent_str;
  public $qr_logo_white_background;
  public $qr_logo_white_background_str;

  public $enable_link_health_str;
  public $enable_link_health;
  public $enable_link_health_emails;
  public $enable_link_health_emails_str;
  public $link_health_emails;
  public $link_health_emails_str;

  public $global_head_scripts_str;
  public $global_head_scripts;

  //Use a base slug prefix on all new links like out/ or go/ etc.
  public $base_slug_prefix_str;
  public $base_slug_prefix;

  //The number of characters to use in random slug generation.
  public $num_slug_chars_str;
  public $num_slug_chars;

  public function __construct($options_array=array()) {
    // Set values from array
    foreach($options_array as $key => $value) {
      $this->{$key} = $value;
    }

    $this->set_default_options();
  }

  public function set_default_options() {
    if(!isset($this->pages_auto))
      $this->pages_auto = 0;

    if(!isset($this->posts_auto))
      $this->posts_auto = 0;

    if(!isset($this->pages_category))
      $this->pages_category= '';

    if(!isset($this->posts_category))
      $this->posts_category = '';

    if(!isset($this->autocreate)) {
      $this->autocreate = array();
    }

    $this->default_social_buttons = array(
      'facebook' => array(
        'label' => __('Facebook', 'pretty-link'),
        'checked' => false,
        'slug' => 'facebook',
        'icon' => 'pl-icon-facebook',
        'url' => 'http://www.facebook.com/sharer.php?u={{encoded_url}}&t={{encoded_title}}'
      ),
      'twitter' => array(
        'label' => __('Twitter', 'pretty-link'),
        'checked' => false,
        'slug' => 'twitter',
        'icon' => 'pl-icon-twitter',
        'url' => 'https://twitter.com/intent/tweet?text={{tweet_message}}'
      ),
      'gplus' => array(
        'label' => __('Google+', 'pretty-link'),
        'checked' => false,
        'slug' => 'gplus',
        'icon' => 'pl-icon-gplus',
        'url' => 'https://plus.google.com/share?url={{encoded_url}}'
      ),
      'pinterest' => array(
        'label' => __('Pinterest', 'pretty-link'),
        'checked' => false,
        'slug' => 'pinterest',
        'icon' => 'pl-icon-pinterest',
        'url' => 'http://pinterest.com/pin/create/button/?url={{encoded_url}}&description={{encoded_title}}"'
      ),
      'linkedin' => array(
        'label' => __('LinkedIn', 'pretty-link'),
        'checked' => false,
        'slug' => 'linkedin',
        'icon' => 'pl-icon-linkedin',
        'url' => 'http://www.linkedin.com/shareArticle?mini=true&url={{encoded_url}}&title={{encoded_title}}'
      ),
      'reddit' => array(
        'label' => __('Reddit', 'pretty-link'),
        'checked' => false,
        'slug' => 'reddit',
        'icon' => 'pl-icon-reddit',
        'url' => 'http://reddit.com/submit?url={{encoded_url}}&title={{encoded_title}}'
      ),
      'stumbleupon' => array(
        'label' => __('StumbleUpon', 'pretty-link'),
        'checked' => false,
        'slug' => 'stumbleupon',
        'icon' => 'pl-icon-stumbleupon',
        'url' => 'http://www.stumbleupon.com/submit?url={{encoded_url}}&title={{encoded_title}}'
      ),
      'digg' => array(
        'label' => __('Digg', 'pretty-link'),
        'checked' => false,
        'slug' => 'digg',
        'icon' => 'pl-icon-digg',
        'url' => 'http://digg.com/submit?phase=2&url={{encoded_url}}&title={{encoded_title}}'
      ),
      'email' => array(
        'label' => __('Email', 'pretty-link'),
        'checked' => false,
        'slug' => 'email',
        'icon' => 'pl-icon-email',
        'url' => 'mailto:?subject={{encoded_title}}&body={{encoded_title}}%20{{encoded_url}}'
      )
    );

    if(!isset($this->social_buttons)) {
      $this->social_buttons = array_values( $this->default_social_buttons );
    }
    else {
      // If it's the old-style array then refactor it
      if( isset($this->social_buttons['facebook']) ) {
        $new_social_buttons = array_values( $this->default_social_buttons );

        foreach( $new_social_buttons as $i => $values ) {
          if( isset( $this->social_buttons[$values['slug']] ) ) {
            $new_social_buttons[$i]['checked'] = ($values==='on');
          }
        }

        $this->social_buttons = $new_social_buttons;
      }
    }

    if(!isset($this->social_buttons_placement))
      $this->social_buttons_placement = 'bottom';

    if(!isset($this->social_buttons_show_in_feed))
      $this->social_buttons_show_in_feed = 0;

    if(!isset($this->social_buttons_padding))
      $this->social_buttons_padding = '10';

    if(!isset($this->social_posts_buttons))
      $this->social_posts_buttons = 0;

    if(!isset($this->social_pages_buttons))
      $this->social_pages_buttons = 0;

    if(!isset($this->keyword_replacement_is_on))
      $this->keyword_replacement_is_on = 1;

    if(!isset($this->keywords_per_page))
      $this->keywords_per_page = 3;

    if(!isset($this->keyword_links_per_page))
      $this->keyword_links_per_page = 2;

    if(!isset($this->keyword_links_open_new_window))
      $this->keyword_links_open_new_window = 0;

    if(!isset($this->keyword_links_nofollow))
      $this->keyword_links_nofollow = 0;

    if(!isset($this->keyword_links_sponsored))
      $this->keyword_links_sponsored = 0;

    if(!isset($this->set_keyword_thresholds))
      $this->set_keyword_thresholds = 0;

    // DEPRECATED
    $this->keyword_enable_content_cache = 0;

    if(!isset($this->url_replacement_is_on))
      $this->url_replacement_is_on = $this->keyword_replacement_is_on;
    if(!isset($this->replace_urls_with_pretty_links))
      $this->replace_urls_with_pretty_links = 0;
    if(!isset($this->url_links_open_new_window))
      $this->url_links_open_new_window = 0;
    if(!isset($this->replace_urls_with_pretty_links_blacklist))
      $this->replace_urls_with_pretty_links_blacklist = PlpOptionsHelper::get_site_url_variants();
    if(!isset($this->url_replacement_cpts) || empty($this->url_replacement_cpts))
      $this->url_replacement_cpts = array('post');
    if(!isset($this->replace_keywords_in_comments) || $this->replace_keywords_in_comments === 1)
      $this->replace_keywords_in_comments = 'both';
    if(!isset($this->replace_keywords_in_feeds) || $this->replace_keywords_in_feeds === 1)
      $this->replace_keywords_in_feeds = 'both';
    if(!isset($this->enable_link_to_disclosures)) {
      $this->enable_link_to_disclosures = 0;
    }
    if(!isset($this->disclosures_link_url)) {
      $this->disclosures_link_url = '';
    }
    if(!isset($this->disclosures_link_text)) {
      $this->disclosures_link_text = __('Affiliate Link Disclosures','pretty-link');
    }
    if(!isset($this->disclosures_link_position)) {
      $this->disclosures_link_position = 'bottom';
    }
    if(!isset($this->enable_keyword_link_disclosures)) {
      $this->enable_keyword_link_disclosures = 0;
    }
    if(!isset($this->keyword_link_disclosure)) {
      $this->keyword_link_disclosure = __('(aff)', 'pretty-link');
    }
    if(!isset($this->keyword_replacement_cpts) || empty($this->keyword_replacement_cpts)) {
      $this->keyword_replacement_cpts = array('post');
    }

    if(!isset($this->use_prettylink_url))
      $this->use_prettylink_url = 0;

    if(!isset($this->prettylink_url))
      $this->prettylink_url = '';

    //manage_options = ADMIN
    //delete_pages = EDITOR
    //publish_posts = AUTHOR
    //edit_posts = CONTRIBUTOR
    //read = SUBSCRIBER
    if(!isset($this->min_role) || $this->min_role == 'add_users' || $this->min_role == 'read' ) {
      $this->min_role = 'manage_options';
    }

    if(!isset($this->allow_public_link_creation))
      $this->allow_public_link_creation = 0;

    if(!isset($this->use_public_link_display_page))
      $this->use_public_link_display_page = 0;

    if(!isset($this->public_link_display_page))
      $this->public_link_display_page = '';

    if(!isset($this->prettybar_hide_attrib_link))
      $this->prettybar_hide_attrib_link = 0;

    if(!isset($this->prettybar_attrib_url))
      $this->prettybar_attrib_url = '';

    $this->google_tracking_str = 'prlipro-google-tracking';
    if(!isset($this->google_tracking))
      $this->google_tracking = 0;

    $this->generate_qr_codes_str = 'prlipro-generate-qr-codes';
    if(!isset($this->generate_qr_codes))
      $this->generate_qr_codes = 0;

    $this->qr_code_links_str = 'prlipro-code-links';
    $this->qr_code_links = 0;

    // QR Code Logo Settings
    $this->qr_logo_attachment_id_str = 'prlipro-qr-logo-attachment-id';
    if(!isset($this->qr_logo_attachment_id))
      $this->qr_logo_attachment_id = 0; // 0 = no logo

    $this->qr_logo_size_percent_str = 'prlipro-qr-logo-size-percent';
    if(!isset($this->qr_logo_size_percent))
      $this->qr_logo_size_percent = 20; // 20% default (recommended range 15-20%)

    $this->qr_logo_white_background_str = 'prlipro-qr-logo-white-background';
    if(!isset($this->qr_logo_white_background))
      $this->qr_logo_white_background = 1; // Enabled by default for better scannability

    $this->enable_link_health_str = 'prlipro-enable-link-health';
    $this->enable_link_health_emails_str = 'prlipro-enable-link-health-emails';
    $this->link_health_emails_str = 'prlipro-link-health-emails';

    if(!isset($this->enable_link_health))
      $this->enable_link_health = 0;
    if(!isset($this->enable_link_health_emails))
      $this->enable_link_health_emails = 0;
    if(!isset($this->link_health_emails) || empty($this->link_health_emails))
      $this->link_health_emails = get_option('admin_email');

    /* TODO: We're going to just comment this out for now
    if(!isset($this->qr_code_links))
      $this->qr_code_links = 0;
    */

    $this->global_head_scripts_str = 'prlipro-global-head-scripts';
    if(!isset($this->global_head_scripts) || empty($this->global_head_scripts))
      $this->global_head_scripts = '';

    $this->base_slug_prefix_str = 'prlipro-base-slug-prefix';
    if(!isset($this->base_slug_prefix))
      $this->base_slug_prefix = '';

    $this->num_slug_chars_str = 'prlipro-num-slug-chars';
    if(!isset($this->num_slug_chars))
      $this->num_slug_chars = 4;
  }

  public function store() {
    $storage_array = (array)$this;
    update_option( 'prlipro_options', $storage_array );
    wp_cache_delete('alloptions', 'options');
  }

  public function autocreate_option($post_type='post') {
    $opt = array(
      'enabled' => false,
      'category' => '',
      'socbtns' => false
    );

    if($post_type=='post') {
      $opt['enabled'] = !empty($this->posts_auto);
      $opt['category']   = $this->posts_category;
      $opt['socbtns'] = !empty($this->social_posts_buttons);
    }
    else if($post_type=='page') {
      $opt['enabled'] = !empty($this->pages_auto);
      $opt['category']   = $this->pages_category;
      $opt['socbtns'] = !empty($this->social_pages_buttons);
    }
    else {
      if(isset($this->autocreate[$post_type])) {
        $ac = $this->autocreate[$post_type];
        $opt['enabled'] = isset($ac['enabled']) && !empty($ac['enabled']);
        $opt['category']   = isset($ac['category']) ? $ac['category'] : '';
        $opt['socbtns'] = isset($ac['socbtns']) && !empty($ac['socbtns']);
      }
    }

    return (object)$opt;
  }

  public function get_post_types($include_page_and_post=true) {
    $post_types = get_post_types(array('_builtin'=>false,'public'=>true),'names','and');

    if($include_page_and_post) {
      $post_types['post'] = 'post';
      $post_types['page'] = 'page';
    }

    return $post_types;
  }

  public static function get_options() {
    $plp_options = get_option('prlipro_options');

    if($plp_options) {
      if(is_string($plp_options)) {
        $plp_options = unserialize($plp_options);
      }

      if(is_object($plp_options) && is_a($plp_options,'PlpOptions')) {
        $plp_options->set_default_options();
        $plp_options->store(); // store will convert this back into an array
      }
      else if(is_array($plp_options)) {
        $plp_options = new PlpOptions($plp_options);
      }
      else {
        $plp_options = new PlpOptions();
      }
    }
    else {
      $plp_options = new PlpOptions();
    }

    return $plp_options;
  }
}
