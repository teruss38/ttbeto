<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PlpLinksController extends PrliBaseController {
  public function load_hooks() {
    global $plp_options;

    add_action('prli_link_fields',   array($this,'display_link_options'));
    add_action('prli_record_click',  array($this,'record_rotation_click'));
    add_action('prli_update_link',   array($this,'update_link_options'));
    add_filter('prli_validate_link', array($this,'validate_link_options'));
    add_filter('prli_target_url',    array($this,'customize_target_url'), 99);
    add_action('wp_head', array($this,'shorturl_autodiscover'));

    add_action('prli_redirection_types', array($this,'redirection_types'), 10, 2);
    add_action('prli_issue_cloaked_redirect', array($this,'issue_cloaked_redirect'), 10, 4);
    add_action('prli_default_redirection_types',array($this,'default_redirection_options'));
    add_action('prli_delete_link', array($this,'delete_link'));
    add_action('prli_custom_link_options', array($this,'custom_link_options'));
    add_filter('prli-validate-options', array($this, 'validate_options'));
    add_action('prli-store-options', array($this,'store_link_options'));
    add_action('prli-create-link', array($this,'create_link'), 10, 2);

    add_filter('prli-check-if-slug', array($this,'generate_qr_code'),10,2);

    add_action('prli_list_end_icon', array($this,'link_list_end_icons'));

    add_action('prli-redirect-header', array($this, 'maybe_add_scripts_to_head'));

    add_action('wp_ajax_prli_search_countries', array($this, 'ajax_search_countries'));
    add_action('wp_ajax_prli_search_links', array($this, 'ajax_search_links'));

    add_filter('cron_schedules', array($this, 'intervals'));

    add_filter('prli_admin_links_columns', array($this, 'add_pro_columns'));
    add_action('prli_admin_links_column_values', array($this, 'render_pro_columns'), 10, 2);

    if($plp_options->enable_link_health) {
      add_action('load-edit.php', array($this, 'filter_by_broken_links'));
      add_filter('prli_quick_links', array($this, 'views'));
      add_filter('post_class', array($this, 'add_broken_link_class'), 10, 3);
      add_action('prli_before_update_link', array($this, 'maybe_check_link_status'));
      add_action('add_meta_boxes', array($this, 'add_link_health_metabox'));
      add_action('wp_ajax_plp_check_single_link_health', array($this, 'check_single_link_health'));
    }

    $this->manage_cron_events();
  }

  public function maybe_add_scripts_to_head() {
    global $wpdb, $plp_options, $prli_link, $prli_link_meta;

    //Global scripts
    if(!empty($plp_options->global_head_scripts)) {
      echo stripslashes($plp_options->global_head_scripts) . "\n";
    }

    //Per link scripts
    $request_uri = preg_replace('#/(\?.*)?$#', '$1', rawurldecode($_SERVER['REQUEST_URI']));

    if($link_info = $prli_link->is_pretty_link($request_uri, false)) {
      $link_id = $link_info['pretty_link_found']->id;
      $head_scripts = stripslashes((string) $prli_link_meta->get_link_meta($link_id, 'head_scripts', true));

      if(!empty($head_scripts)) {
        echo stripslashes($head_scripts);
      }
    }
  }

  /************ DISPLAY & UPDATE PRO LINK OPTIONS ************/
  public function display_link_options($link_id) {
    global $prli_link, $prli_link_meta, $plp_keyword, $plp_link_rotation, $plp_options;

    if(empty($_REQUEST['prettypay']) && $link_id) {
      $link = $prli_link->getOne($link_id);
      $prettypay_link = $link && $link->prettypay_link == 1;
    } else {
      $prettypay_link = !empty($_REQUEST['prettypay']);
    }

    if($plp_options->keyword_replacement_is_on || $plp_options->url_replacement_is_on) {
      if(empty($_POST['keywords']) && $link_id) {
        $keywords = $plp_keyword->getTextByLinkId( $link_id );
      }
      else {
        $keywords = isset($_POST['keywords']) && is_string($_POST['keywords']) ? sanitize_text_field(stripslashes($_POST['keywords'])) : '';
      }

      if(empty($_POST['url_replacements']) && $link_id) {
        $url_replacements = $prli_link_meta->get_link_meta( $link_id, 'prli-url-replacements' );

        if(is_array($url_replacements)) {
          $url_replacements = implode(', ', $url_replacements);
        }
        else {
          $url_replacements = '';
        }
      }
      else {
        $url_replacements = isset($_POST['url_replacements']) && is_string($_POST['url_replacements']) ? sanitize_text_field(stripslashes($_POST['url_replacements'])) : '';
      }
    }

    if(empty($_POST['head-scripts']) && $link_id) {
      $head_scripts = ! empty( $prli_link_meta->get_link_meta( $link_id, 'head_scripts', true ) ) ? stripslashes( $prli_link_meta->get_link_meta($link_id, 'head_scripts', true ) ) : '' ;
    }
    else {
      $head_scripts = isset($_POST['head-scripts']) && is_string($_POST['head-scripts']) ? PrliUtils::sanitize_html(stripslashes($_POST['head-scripts'])) : '';
    }

    if(empty($_POST['dynamic_redirection']) && $link_id) {
      $dynamic_redirection = $prli_link_meta->get_link_meta($link_id, 'prli_dynamic_redirection', true);

      // Ensure reverse compatibility
      if(empty($dynamic_redirection) &&
         $plp_link_rotation->there_are_rotations_for_this_link($link_id)) {
        $dynamic_redirection = 'rotate';
      }
    }
    else {
      $dynamic_redirection = isset($_POST['dynamic_redirection']) && is_string($_POST['dynamic_redirection']) ? sanitize_key(stripslashes($_POST['dynamic_redirection'])) : 'none';
    }

    if(empty($_POST['url_rotations']) && $link_id) {
      $url_rotations = $plp_link_rotation->get_rotations( $link_id );
      $url_rotation_weights   = $plp_link_rotation->get_weights( $link_id );

      if(!is_array($url_rotations)) {
        $url_rotations = array('','','','');
      }

      if(!is_array($url_rotation_weights)) {
        $url_rotation_weights = array('','','','');
      }
    }
    else {
      $url_rotations = isset($_POST['url_rotations']) && is_array($_POST['url_rotations']) ? array_map('esc_url_raw', array_map('trim', wp_unslash($_POST['url_rotations']))) : array();
      $url_rotation_weights = isset($_POST['url_rotation_weights']) && is_array($_POST['url_rotation_weights']) ? array_map(function ($value) { return PrliUtils::clamp((int) $value, 0, 100); }, wp_unslash($_POST['url_rotation_weights'])) : array();
    }

    if(empty($_POST['url']) && $link_id) {
      $link = $prli_link->getOne($link_id);
      $target_url = $link->url;
    }
    else {
      $target_url = isset($_POST['url']) && is_string($_POST['url']) ? esc_url_raw(trim(stripslashes($_POST['url']))) : '';
    }

    if(!$link_id || !($target_url_weight = $prli_link_meta->get_link_meta($link_id, 'prli-target-url-weight', true))) {
      $target_url_weight = 0;
    }

    if(!empty($_POST) && !isset($_POST['enable_split_test']) || (empty($link_id) || !$link_id)) {
      $enable_split_test = isset($_POST['enable_split_test']);
    }
    else {
      $enable_split_test = $prli_link_meta->get_link_meta($link_id, 'prli-enable-split-test', true);
    }

    if(isset($_POST['split_test_goal_link']) || (empty($link_id) || !$link_id)) {
      $split_test_goal_link = isset($_POST['split_test_goal_link']) && is_numeric($_POST['split_test_goal_link']) ? (int) $_POST['split_test_goal_link'] : '';
    }
    else {
      $split_test_goal_link = $prli_link_meta->get_link_meta($link_id, 'prli-split-test-goal-link', true);
    }

    $selected_goal_link = null;
    if(!empty($split_test_goal_link)) {
      $selected_goal_link = $prli_link->getOne($split_test_goal_link, OBJECT, false, false);
    }

    if(isset($_POST['enable_expire']) || (empty($link_id) || !$link_id)) {
      $enable_expire = isset($_POST['enable_expire']);
    }
    else {
      $enable_expire = $prli_link_meta->get_link_meta($link_id, 'enable_expire', true);
    }

    if(isset($_POST['expire_type']) || (empty($link_id) || !$link_id)) {
      $expire_type = isset($_POST['expire_type']) && is_string($_POST['expire_type']) ? sanitize_key(stripslashes($_POST['expire_type'])) : 'date';
    }
    else {
      $expire_type = $prli_link_meta->get_link_meta($link_id, 'expire_type', true);
    }

    if(isset($_POST['expire_date']) || (empty($link_id) || !$link_id)) {
      $expire_date = isset($_POST['expire_date']) && is_string($_POST['expire_date']) ? sanitize_text_field(stripslashes($_POST['expire_date'])) : '';
    }
    else {
      $expire_date = $prli_link_meta->get_link_meta($link_id, 'expire_date', true);
    }

    if(isset($_POST['expire_clicks']) || (empty($link_id) || !$link_id)) {
      $expire_clicks = isset($_POST['expire_clicks']) && is_numeric($_POST['expire_clicks']) ? (int) $_POST['expire_clicks'] : 0;
    }
    else {
      $expire_clicks = $prli_link_meta->get_link_meta($link_id, 'expire_clicks', true);
    }

    if(isset($_POST['enable_expired_url']) || (empty($link_id) || !$link_id)) {
      $enable_expired_url = isset($_POST['enable_expired_url']);
    }
    else {
      $enable_expired_url = $prli_link_meta->get_link_meta($link_id, 'enable_expired_url', true);
    }

    if(isset($_POST['expired_url']) || (empty($link_id) || !$link_id)) {
      $expired_url = isset($_POST['expired_url']) && is_string($_POST['expired_url']) ? esc_url_raw(trim(stripslashes($_POST['expired_url']))) : '';
    }
    else {
      $expired_url = $prli_link_meta->get_link_meta($link_id, 'expired_url', true);
    }

    require_once(PLP_VIEWS_PATH.'/links/form.php');
  }

  public function validate_link_options($errors) {
    global $prli_link_meta, $plp_options;

    if($plp_options->url_replacement_is_on) {
      if(!empty($_POST['url_replacements'])) {
        $replacements = explode(',', $_POST['url_replacements']);
        foreach($replacements as $replacement) {
          if(!PrliUtils::is_url(trim($replacement))) {
            $errors[] = __('Your URL Replacements must be formatted as a comma separated list of properly formatted URLs (http[s]://example.com/whatever)', 'pretty-link');
            break;
          }
        }
      }
    }

    if(isset($_POST['enable_expire'])) {
      if(isset($_POST['expire_type']) && $_POST['expire_type']=='date') {
        $_POST['expire_date'] = trim($_POST['expire_date']);
        if(!PrliUtils::is_date($_POST['expire_date'])) {
          $errors[] = __('Date must be valid and formatted YYYY-MM-DD.', 'pretty-link');
        }
      }
      else if(isset($_POST['expire_type']) && $_POST['expire_type']=='clicks') {
        $_POST['expire_clicks'] = trim($_POST['expire_clicks']);

        // If they have clicks set here then we force tracking on for the link
        // TODO: Is this the best way to do this?
        $_POST['track_me'] = 'on';

        if( !is_numeric($_POST['expire_clicks']) ||
            (int)$_POST['expire_clicks'] <= 0 ) {
          $errors[] = __('Expire Clicks must be a number greater than zero.', 'pretty-link');
        }
      }

      if(isset($_POST['enable_expired_url'])) {
        $_POST['expired_url'] = isset($_POST['expired_url']) && is_string($_POST['expired_url']) ? trim($_POST['expired_url']) : '';
        if(!PrliUtils::is_url($_POST['expired_url'])) {
          $errors[] = __('Expired URL must be a valid URL.', 'pretty-link');
        }
      }
    }

    if( !empty($_POST['dynamic_redirection']) && $_POST['dynamic_redirection']=='rotate' ) {
      if( !empty($_POST[ 'url_rotations' ]) ) {
        $num_active_links = 0;
        $weight_sum = (int)$_POST['target_url_weight'];
        foreach($_POST['url_rotations'] as $i => $rotation) {
          if(!empty($rotation)) {
            if(!PrliUtils::is_url($rotation)) {
              $errors[] = __('Your URL Rotations must all be properly formatted URLs.', 'pretty-link');
            }

            $num_active_links++;
            $weight_sum += (int)$_POST['url_rotation_weights'][$i];
          }
        }

        if($num_active_links > 0 && $weight_sum != 100) {
          $errors[] = __('Your Link Rotation Weights must add up to 100%.', 'pretty-link');
        }
      }
    }

    if( !empty($_POST['dynamic_redirection']) && $_POST['dynamic_redirection']=='geo' ) {
      if( !empty($_POST['prli_geo_url']) ) {
        foreach($_POST['prli_geo_url'] as $i => $geo_url) {
          if(!empty($geo_url)) {
            if(!PrliUtils::is_url($geo_url)) {
              $errors[] = __('Your Geographic Redirect URLs must all be properly formatted.', 'pretty-link');
            }
          }
          else {
            $errors[] = __('Your Geographic Redirects URLs must not be empty.', 'pretty-link');
          }

          if(empty($_POST['prli_geo_countries']) || empty($_POST['prli_geo_countries'][$i])) {
            $errors[] = __('Your Geographic Redirect Countries must not be empty.', 'pretty-link');
          }
        }
      }
    }

    if( !empty($_POST['dynamic_redirection']) && $_POST['dynamic_redirection']=='tech' ) {
      if( !empty($_POST['prli_tech_url']) ) {
        foreach($_POST['prli_tech_url'] as $i => $tech_url) {
          if(!empty($tech_url)) {
            if(!PrliUtils::is_url($tech_url)) {
              $errors[] = __('Your Technology Redirect URLs must all be properly formatted.', 'pretty-link');
            }
          }
          else {
            $errors[] = __('Your Technology Redirects URLs must not be empty.', 'pretty-link');
          }
        }
      }
    }

    if( !empty($_POST['dynamic_redirection']) && $_POST['dynamic_redirection']=='time' ) {
      if( !empty($_POST['prli_time_url']) ) {
        foreach($_POST['prli_time_url'] as $i => $time_url) {
          if(!empty($time_url)) {
            if(!PrliUtils::is_url($time_url)) {
              $errors[] = __('Your Time Period Redirect URLs must all be properly formatted.', 'pretty-link');
            }
          }
          else {
            $errors[] = __('Your Time Period Redirects URLs must not be empty.', 'pretty-link');
          }

          if(!empty($_POST['prli_time_start'])) {
            if(empty($_POST['prli_time_start'][$i])) {
              $errors[] = __('Your Time Period Redirect start time must not be empty.', 'pretty-link');
            }
            else if(!PrliUtils::is_date($_POST['prli_time_start'][$i])) {
              $errors[] = __('Your Time Period Redirect start time must be a properly formatted date.', 'pretty-link');
            }
          }

          if(!empty($_POST['prli_time_end'])) {
            if(empty($_POST['prli_time_end'][$i])) {
              $errors[] = __('Your Time Period Redirect end time must not be empty.', 'pretty-link');
            }
            else if(!PrliUtils::is_date($_POST['prli_time_end'][$i])) {
              $errors[] = __('Your Time Period Redirect end time must be a properly formatted date.', 'pretty-link');
            }
          }

          if(!empty($_POST['prli_time_start']) && !empty($_POST['prli_time_end']) &&
             PrliUtils::is_date($_POST['prli_time_start'][$i]) && PrliUtils::is_date($_POST['prli_time_end'][$i]) &&
             ($time_start = strtotime($_POST['prli_time_start'][$i])) && ($time_end = strtotime($_POST['prli_time_end'][$i])) &&
             $time_start > $time_end ) {
            $errors[] = __('Your Time Period Redirect start time must come before the end time.', 'pretty-link');
          }
        }
      }
    }

    if(isset($_POST['delay']) && !empty($_POST['delay'])) {
      if(!is_numeric($_POST['delay'])) {
        $errors[] = __('Delay Redirect must be a number', 'pretty-link');
      }
    }

    return $errors;
  }

  /**
   * Runs link health check if the link is inactive and the target URL
   * has been updated.
   *
   * @access public
   * @param int $link_id ID of the link being updated.
   * @return void
   */
  public function maybe_check_link_status($link_id) {
    global $prli_link_meta, $prli_link;

    $health_status = $prli_link_meta->get_link_meta($link_id, 'health_status', true);
    $pretty_link = $prli_link->getOne($link_id);

    if($health_status == 'inactive' && $pretty_link->url != $_POST['url']) {
      // Set the link target URL to the one we're checking temporarily.
      $pretty_link->url = $_POST['url'];

      $this->detect_broken_link($pretty_link);
    }
  }

  /**
   * Adds a "Link Health" metabox on the Pretty Links CPT edit screen.
   *
   * @access public
   * @return void
   */
  public function add_link_health_metabox() {
    global $post, $prli_link;

    $cpt = PrliLink::$cpt;

    if(!$post || $post->post_type != $cpt || $post->post_status != 'publish') {
      return;
    }

    $pretty_link_id = $prli_link->get_link_from_cpt($post->ID);
    $pretty_link = $prli_link->getOne($pretty_link_id);

    // Make sure we aren't rendering the metabox for links using the Pixel redirect type, or PrettyPay links.
    if(in_array($pretty_link->redirect_type, array('pixel', 'prettypay_link_stripe'), true)) {
      return;
    }

    add_meta_box('plp-link-health', esc_html__('Link Health', 'pretty-link'), array($this, 'render_link_health_metabox'), $cpt, 'side');
  }

  /**
   * Renders the content of the "Link Health" metabox.
   *
   * @access public
   * @param object $post The current post/pretty link being edited.
   * @return void
   */
  public function render_link_health_metabox($post) {
    global $prli_link, $prli_link_meta;

    $link_id = $prli_link->get_link_from_cpt($post->ID);
    $status = $prli_link_meta->get_link_meta($link_id, 'health_status', true);
    $last_checked = $prli_link_meta->get_link_meta($link_id, 'health_last_checked', true);
    $markup = $this->get_link_health_status($status, $last_checked);

    require_once PLP_VIEWS_PATH . '/metaboxes/link-health.php';
  }

  /**
   * Checks the status of an individual link. The request is processed via Ajax.
   *
   * @access public
   * @return void
   */
  public function check_single_link_health() {
    $link_id = isset($_POST['link_id']) ? intval($_POST['link_id']) : 0;

    if(!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'plp_check_single_link_health_' . $link_id)) {
      $response = array('status' => 'fail', 'error' => esc_html__('You are not allowed to do this.', 'pretty-link'));
    } else {
      global $prli_link, $prli_link_meta;

      $pretty_link = $prli_link->getOne($link_id);

      $this->detect_broken_link($pretty_link);

      $status = $prli_link_meta->get_link_meta($link_id, 'health_status', true);
      $last_checked = $prli_link_meta->get_link_meta($link_id, 'health_last_checked', true);
      $markup = $this->get_link_health_status($status, $last_checked);
      $return_broken_link_count = isset($_POST['return_broken_link_count']) ? (bool) $_POST['return_broken_link_count'] : 0;

      $response = array('status' => 'success', 'markup' => $markup);

      if($return_broken_link_count) {
        $response['broken_link_count'] = PlpLink::get_broken_link_count();
      }
    }

    wp_send_json($response);
  }

  public function update_link_options($link_id) {
    global $prli_link_meta, $plp_link_rotation, $plp_keyword, $plp_options;

    if($plp_options->keyword_replacement_is_on || $plp_options->url_replacement_is_on) {
      if (isset($_POST['keywords']) && is_string($_POST['keywords'])) {
        //Keywords first
        $plp_keyword->updateLinkKeywords($link_id, sanitize_text_field(stripslashes($_POST['keywords'])));
      }

      if (isset($_POST['url_replacements']) && is_string($_POST['url_replacements'])) {
        //Now URL replacements
        $replacements = explode(',', $_POST['url_replacements']);

        for ($i = 0; $i < count($replacements); $i++) {
          $replacements[$i] = esc_url_raw(trim($replacements[$i]));
        }

        //No point filling the meta table with a bunch of empty crap
        if (count($replacements) == 1 && empty($replacements[0])) {
          $prli_link_meta->delete_link_meta($link_id, 'prli-url-replacements');
        } else {
          $prli_link_meta->update_link_meta($link_id, 'prli-url-replacements', $replacements);
        }
      }
    }

    $dynamic_redirection = (isset($_POST['dynamic_redirection']) && is_string($_POST['dynamic_redirection']) ? sanitize_key(stripslashes($_POST['dynamic_redirection'])) : 'none');
    $prli_link_meta->update_link_meta($link_id, 'prli_dynamic_redirection', $dynamic_redirection);

    $target_url_weight = 100;
    $url_rotations = $url_rotation_weights = array();
    $enable_split_test = false;
    $split_test_goal_link = '';

    if($dynamic_redirection == 'rotate') {
      $target_url_weight = isset($_POST['target_url_weight']) && is_numeric($_POST['target_url_weight']) ? PrliUtils::clamp((int) $_POST['target_url_weight'], 0, 100) : 100;
      $url_rotations = isset($_POST['url_rotations']) && is_array($_POST['url_rotations']) ? array_map('esc_url_raw', array_map('trim', wp_unslash($_POST['url_rotations']))) : array();
      $url_rotation_weights = isset($_POST['url_rotation_weights']) && is_array($_POST['url_rotation_weights']) ? array_map(function ($value) { return PrliUtils::clamp((int) $value, 0, 100); }, wp_unslash($_POST['url_rotation_weights'])) : array();
      $enable_split_test = isset($_POST['enable_split_test']);
      $split_test_goal_link = isset($_POST['split_test_goal_link']) && is_numeric($_POST['split_test_goal_link']) ? (int) $_POST['split_test_goal_link'] : '';
    }

    $prli_link_meta->update_link_meta($link_id, 'prli-target-url-weight', $target_url_weight);
    $plp_link_rotation->updateLinkRotations($link_id, $url_rotations, $url_rotation_weights);
    $prli_link_meta->update_link_meta($link_id, 'prli-enable-split-test', $enable_split_test);
    $prli_link_meta->update_link_meta($link_id, 'prli-split-test-goal-link', $split_test_goal_link);

    $geo_url = $geo_countries = array();

    if($dynamic_redirection == 'geo') {
      $geo_url = isset($_POST['prli_geo_url']) && is_array($_POST['prli_geo_url']) ? array_map('esc_url_raw', array_map('trim', wp_unslash($_POST['prli_geo_url']))) : array();
      $geo_countries = isset($_POST['prli_geo_countries']) && is_array($_POST['prli_geo_countries']) ? array_map('sanitize_text_field', wp_unslash($_POST['prli_geo_countries'])) : array();
    }

    $prli_link_meta->update_link_meta($link_id, 'geo_url', $geo_url);
    $prli_link_meta->update_link_meta($link_id, 'geo_countries', $geo_countries);

    $tech_url = $tech_device = $tech_os = $tech_browser = array();

    if($dynamic_redirection == 'tech') {
      $tech_url = isset($_POST['prli_tech_url']) && is_array($_POST['prli_tech_url']) ? array_map('esc_url_raw', array_map('trim', wp_unslash($_POST['prli_tech_url']))) : array();
      $tech_device = isset($_POST['prli_tech_device']) && is_array($_POST['prli_tech_device']) ? array_map('sanitize_key', wp_unslash($_POST['prli_tech_device'])) : array();
      $tech_os = isset($_POST['prli_tech_os']) && is_array($_POST['prli_tech_os']) ? array_map('sanitize_key', wp_unslash($_POST['prli_tech_os'])) : array();
      $tech_browser = isset($_POST['prli_tech_browser']) && is_array($_POST['prli_tech_browser']) ? array_map('sanitize_key', wp_unslash($_POST['prli_tech_browser'])) : array();
    }

    $prli_link_meta->update_link_meta($link_id, 'tech_url', $tech_url);
    $prli_link_meta->update_link_meta($link_id, 'tech_device', $tech_device);
    $prli_link_meta->update_link_meta($link_id, 'tech_os', $tech_os);
    $prli_link_meta->update_link_meta($link_id, 'tech_browser', $tech_browser);

    $time_url = $time_start = $time_end = array();

    if($dynamic_redirection == 'time') {
      $time_url = isset($_POST['prli_time_url']) && is_array($_POST['prli_time_url']) ? array_map('esc_url_raw', array_map('trim', wp_unslash($_POST['prli_time_url']))) : array();
      $time_start = isset($_POST['prli_time_start']) && is_array($_POST['prli_time_start']) ? array_map('sanitize_text_field', wp_unslash($_POST['prli_time_start'])) : array();
      $time_end = isset($_POST['prli_time_end']) && is_array($_POST['prli_time_end']) ? array_map('sanitize_text_field', wp_unslash($_POST['prli_time_end'])) : array();
    }

    $prli_link_meta->update_link_meta($link_id, 'time_url', $time_url);
    $prli_link_meta->update_link_meta($link_id, 'time_start', $time_start);
    $prli_link_meta->update_link_meta($link_id, 'time_end', $time_end);

    $prli_link_meta->update_link_meta($link_id, 'google_tracking', isset($_POST['google_tracking']));
    $prli_link_meta->update_link_meta($link_id, 'delay', isset($_POST['delay']) && is_numeric($_POST['delay']) ? (int) $_POST['delay'] : 0);
    $prli_link_meta->update_link_meta($link_id, 'head_scripts', isset($_POST['head-scripts']) && is_string($_POST['head-scripts']) ? PrliUtils::sanitize_html(stripslashes($_POST['head-scripts'])) : '');

    $prli_link_meta->update_link_meta($link_id, 'enable_expire', isset($_POST['enable_expire']));
    $prli_link_meta->update_link_meta($link_id, 'expire_type', isset($_POST['expire_type']) && is_string($_POST['expire_type']) ? sanitize_key(stripslashes($_POST['expire_type'])) : 'date');
    $prli_link_meta->update_link_meta($link_id, 'expire_date', isset($_POST['expire_date']) && is_string($_POST['expire_date']) ? sanitize_text_field(stripslashes($_POST['expire_date'])) : '');
    $prli_link_meta->update_link_meta($link_id, 'expire_clicks', isset($_POST['expire_clicks']) && is_numeric($_POST['expire_clicks']) ? (int) $_POST['expire_clicks'] : 0);
    $prli_link_meta->update_link_meta($link_id, 'enable_expired_url', isset($_POST['enable_expired_url']));
    $prli_link_meta->update_link_meta($link_id, 'expired_url', isset($_POST['expired_url']) && is_string($_POST['expired_url']) ? esc_url_raw(trim(stripslashes($_POST['expired_url']))) : '');
  }

  /** This is where we do link rotation or geolocated redirects */
  public function customize_target_url($target) {
    global $plp_link_rotation, $prli_link_meta, $prli_utils, $prli_link;

    if(($expired_url = PlpUtils::is_link_expired($target['link_id']))) {
      if($expired_url==404) {
        // TODO: Not totally sure how to ensure this will use the WordPress 404 mechanism...figure it out
        // For now just throw a 404 and render our page here
        status_header(404);

        wp_register_style('prli-bootstrap', PRLI_VENDOR_LIB_URL . '/bootstrap/bootstrap.min.css', array(), '3.3.6');
        wp_register_style('prli-bootstrap-theme', PRLI_VENDOR_LIB_URL . '/bootstrap/bootstrap-theme.min.css', array('prli-bootstrap'), '3.3.6');

        wp_add_inline_style('prli-bootstrap-theme', 'body { background-color: #dedede; } p { font-size: 120%; }');

        require(PLP_VIEWS_PATH.'/links/link-has-expired.php');
        exit;
      }
      else {
        return array('url' => $expired_url, 'link_id' => $target['link_id']);
      }
    }

    $dynamic_redirection = $prli_link_meta->get_link_meta($target['link_id'], 'prli_dynamic_redirection', true);

    if((empty($dynamic_redirection) || $dynamic_redirection=='rotate') &&
       $plp_link_rotation->there_are_rotations_for_this_link($target['link_id'])) {
      return array('url' => $plp_link_rotation->get_target_url($target['link_id']), 'link_id' => $target['link_id']);
    }
    else if(!empty($dynamic_redirection) && $dynamic_redirection=='geo') {
      $lookup = $this->get_country_lookup($target['link_id']);
      $country = PlpUtils::country_by_ip($prli_utils->get_current_client_ip());

      if(!empty($country) && isset($lookup[$country]) && !empty($lookup[$country])) {
        return array('url' => $lookup[$country], 'link_id' => $target['link_id']);
      }
    }
    else if(!empty($dynamic_redirection) && $dynamic_redirection=='tech') {
      $binfo = $prli_utils->php_get_browser();

      $tech_urls = $prli_link_meta->get_link_meta($target['link_id'], 'tech_url');
      $tech_devices = $prli_link_meta->get_link_meta($target['link_id'], 'tech_device');
      $tech_oses = $prli_link_meta->get_link_meta($target['link_id'], 'tech_os');
      $tech_browsers = $prli_link_meta->get_link_meta($target['link_id'], 'tech_browser');

      if(is_array($tech_urls) && !empty($tech_urls)) {
        $ti = $this->get_tech_info($binfo);
        foreach($tech_urls as $i => $tech_url) {
          if(in_array($tech_devices[$i],$ti['devices']) &&
             in_array($tech_oses[$i],$ti['oses']) &&
             in_array($tech_browsers[$i],$ti['browsers'])) {
            return array('url' => $tech_url, 'link_id' => $target['link_id']);
          }
        }
      }
    }
    else if(!empty($dynamic_redirection) && $dynamic_redirection=='time') {
      if(($time_url = PlpUtils::is_link_time_redirect_active($target['link_id']))) {
        return array('url' => $time_url, 'link_id' => $target['link_id']);
      }
    }

    return $target;
  }

  /** Return a single array able to lookup a target url from a country code based
    * on the values entered with the geo-location specific redirects.
    */
  private function get_country_lookup($link_id) {
    global $prli_link_meta;

    $dynamic_redirection = $prli_link_meta->get_link_meta($link_id, 'prli_dynamic_redirection', true);
    if(!empty($dynamic_redirection) && $dynamic_redirection=='geo') {
      $geo_url = $prli_link_meta->get_link_meta($link_id, 'geo_url');
      $geo_countries = $prli_link_meta->get_link_meta($link_id, 'geo_countries');

      $lookup = array();
      foreach($geo_countries as $i => $cstr) {
        $cs = explode(',', $cstr);
        foreach($cs as $ci => $country) {
          if(!empty($country) &&
             preg_match('/\[([a-zA-Z]+)\]/i', $country, $m) &&
             !empty($m[1]) &&
             !isset($lookup[$m[1]])) { // First country set wins
            $lookup[strtoupper($m[1])] = $geo_url[$i];
          }
        }
      }

      return $lookup;
    }

    return false;
  }

  private function get_tech_info($info) {
    // Devices
    $devices=array('any');

    if($info['ismobiledevice']===true ||
       $info['ismobiledevice']==='true') {
      $devices[]='mobile';
    }

    if($info['istablet']===true ||
       $info['istablet']==='true') {
      $devices[]='tablet';
    }
    if(($info['istablet']===false ||
        $info['istablet']==='false') &&
       ($info['ismobiledevice']===true ||
        $info['ismobiledevice']==='true')) {
      $devices[]='phone';
    }

    if(($info['istablet']===false ||
        $info['istablet']==='false') &&
       ($info['ismobiledevice']===false ||
        $info['ismobiledevice']==='false')) {
      $devices[]='desktop';
    }

    // Operating Systems
    $oses = array('any');
    $info_os = strtolower($info['platform']);
    $windows_oses = array( 'win10', 'win32', 'win7', 'win8', 'win8.1', 'winnt', 'winvista' );
    $other_oses = array('android', 'linux', 'ios', 'macosx');

    // map macos to macosx for now
    $info_os = (($info_os=='macos') ? 'macosx' : $info_os);

    if(in_array($info_os, $other_oses)) {
      $oses[] = $info_os;
    }
    else if(in_array($info_os, $windows_oses)) {
      $oses[] = 'win';
    }

    $browsers = array('any');
    $info_browser = strtolower($info['browser']);
    $android_browsers = array('android', 'android webview');
    $ie_browsers = array('fake ie', 'ie');
    $other_browsers = array('chrome', 'chromium', 'coast', 'edge', 'firefox', 'opera', 'safari', 'silk', 'kindle');

    if(in_array($info_browser, $other_browsers)) {
      $browsers[] = $info_browser;
    }
    else if(in_array($info_browser, $ie_browsers)) {
      $browsers[] = 'ie';
    }
    else if(in_array($info_browser, $android_browsers)) {
      $browsers[] = 'android';
    }

    return compact('devices','oses','browsers');
  }

  public function record_rotation_click($args) {
    $link_id    = $args['link_id'];
    $click_id   = $args['click_id'];
    $target_url = $args['url'];

    global $plp_link_rotation;
    if($plp_link_rotation->there_are_rotations_for_this_link($link_id)) {
      $plp_link_rotation->record_click($click_id,$link_id,$target_url);
    }
  }

  /***** ADD SHORTLINK AUTO-DISCOVERY *****/
  public function shorturl_autodiscover() {
    global $post;

    if(!is_object($post)) { return; }

    $pretty_link_id = PrliUtils::get_prli_post_meta($post->ID,"_pretty-link",true);

    if($pretty_link_id && (is_single() || is_page())) {
      $shorturl = prli_get_pretty_link_url($pretty_link_id);

      if($shorturl && !empty($shorturl)) {
        ?><link rel="shorturl" href="<?php echo esc_url($shorturl); ?>" /><?php
      }
    }
  }

  /***************** ADD PRETTY BAR, PIXEL and CLOAKED REDIRECTION *********************/
  public function redirection_types($v, $selected = false) {
    $prettybar   = isset($v['redirect_type']['prettybar'])   ? $v['redirect_type']['prettybar']   : '';
    $cloak       = isset($v['redirect_type']['cloak'])       ? $v['redirect_type']['cloak']       : '';
    $pixel       = isset($v['redirect_type']['pixel'])       ? $v['redirect_type']['pixel']       : '';
    $metarefresh = isset($v['redirect_type']['metarefresh']) ? $v['redirect_type']['metarefresh'] : '';
    $javascript  = isset($v['redirect_type']['javascript'])  ? $v['redirect_type']['javascript']  : '';

    ?>
      <?php if(get_option('prlipro_prettybar_active')): ?>
        <option value="prettybar"<?php echo $prettybar; ?> <?php if($selected) { selected('prettybar', $selected); } ?>><?php esc_html_e('Pretty Bar', 'pretty-link'); ?>&nbsp;</option>
      <?php endif; ?>
      <option value="cloak"<?php echo $cloak; ?> <?php if($selected) { selected('cloak', $selected); } ?>><?php esc_html_e('Cloaked', 'pretty-link'); ?>&nbsp;</option>
      <option value="pixel"<?php echo $pixel; ?> <?php if($selected) { selected('pixel', $selected); } ?>><?php esc_html_e('Pixel', 'pretty-link'); ?>&nbsp;</option>
      <option value="metarefresh"<?php echo $metarefresh; ?> <?php if($selected) { selected('metarefresh', $selected); } ?>><?php esc_html_e('Meta Refresh', 'pretty-link'); ?>&nbsp;</option>
      <option value="javascript"<?php echo $javascript; ?> <?php if($selected) { selected('javascript', $selected); } ?>><?php esc_html_e('Javascript', 'pretty-link'); ?>&nbsp;</option>
    <?php
  }

  public function issue_cloaked_redirect($redirect_type, $pretty_link, $pretty_link_url, $param_string) {
    global $prli_blogurl, $prli_link_meta, $prli_blogname;

    // Added July 17, 2023 - Deprecating Google Tracking.
    //$google_tracking = (($prli_link_meta->get_link_meta($pretty_link->id, 'google_tracking', true) == 1)?true:false);

    $google_tracking = false;

    $delay = $prli_link_meta->get_link_meta($pretty_link->id, 'delay', true);

    header("Content-Type: text/html", true);
    header("HTTP/1.1 200 OK", true);

    switch($redirect_type) {
      case 'pixel':
        break;
      case 'prettybar':
        require_once(PLP_VIEWS_PATH . '/links/prettybar-redirect.php');
        break;
      case 'cloak':
        wp_register_style('prli-cloaked-redirect', PRLI_CSS_URL . '/cloaked-redirect.css', array(), PRLI_VERSION);
        require_once(PLP_VIEWS_PATH . '/links/cloaked-redirect.php');
        break;
      case 'metarefresh':
        require_once(PLP_VIEWS_PATH . '/links/metarefresh-redirect.php');
        break;
      case 'javascript':
        wp_register_script('plp-javascript-redirect', PLP_JS_URL . '/javascript-redirect.js', array(), PRLI_VERSION);
        wp_localize_script('plp-javascript-redirect', 'plpJsRedirectL10n', array(
          'url' => esc_url_raw($pretty_link_url . $param_string),
          'delay' => ((int) $delay) * 1000
        ));
        require_once(PLP_VIEWS_PATH . '/links/javascript-redirect.php');
        break;
      default:
        wp_redirect("{$pretty_link_url}{$param_string}", 302);
        exit;
    }
  }

  public function default_redirection_options($link_redirect_type) {
  ?>
    <option value="prettybar" <?php echo (($link_redirect_type == 'prettybar')?' selected="selected"':''); ?>><?php esc_html_e('Pretty Bar', 'pretty-link'); ?></option>
    <option value="cloak" <?php echo (($link_redirect_type == 'cloak')?' selected="selected"':''); ?>><?php esc_html_e('Cloak', 'pretty-link'); ?></option>
    <option value="pixel" <?php echo (($link_redirect_type == 'pixel')?' selected="selected"':''); ?>><?php esc_html_e('Pixel', 'pretty-link'); ?></option>
    <option value="metarefresh" <?php echo (($link_redirect_type == 'metarefresh')?' selected="selected"':''); ?>><?php esc_html_e('Meta Refresh', 'pretty-link'); ?></option>
    <option value="javascript" <?php echo (($link_redirect_type == 'javascript')?' selected="selected"':''); ?>><?php esc_html_e('Javascript', 'pretty-link'); ?></option>
  <?php
  }

  /** Deletes all the pro-specific meta about a link right before the link is deleted.
    * TODO: Relocate most of this to a model asap
    */
  public function delete_link($id) {
    global $wpdb, $plp_keyword, $plp_report, $plp_link_rotation;
    $query = $wpdb->prepare("DELETE FROM {$plp_keyword->table_name} WHERE link_id=%d", $id);
    $wpdb->query($query);

    $query = $wpdb->prepare("UPDATE {$plp_report->table_name} SET goal_link_id=NULL WHERE goal_link_id=%d", $id);
    $wpdb->query($query);

    $query = $wpdb->prepare("DELETE FROM {$plp_report->links_table_name} WHERE link_id=%d", $id);
    $wpdb->query($query);

    $query = $wpdb->prepare("DELETE FROM {$plp_link_rotation->table_name} WHERE link_id=%d", $id);
    $wpdb->query($query);

    $query = $wpdb->prepare("DELETE FROM {$plp_link_rotation->cr_table_name} WHERE link_id=%d", $id);
    $wpdb->query($query);

    $query = $wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE meta_key=%s AND meta_value=%s", '_pretty-link', $id);
    $wpdb->query($query);
  }

  public function custom_link_options() {
    global $plp_options;
    require( PLP_VIEWS_PATH . '/links/link-options.php');
  }

  /**
   * Validates options for the "Link" tab on the PL options page.
   *
   * @access public
   * @param array $errors Errors to display on the options page.
   * @return array
   */
  public function validate_options($errors) {
    if( isset($_POST['prlipro-link-health-emails']) &&
        !preg_match('#^\s*[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,20}(,\s*[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,20})*$#', $_POST['prlipro-link-health-emails']) ) {
      $errors[] = __('The Link Health Email Addresses field must contain 1 or more valid email addresses.', 'pretty-link');
    }

    return $errors;
  }

  public function store_link_options() {
    global $plp_options;

    $plp_options->google_tracking = (int)isset($_REQUEST[$plp_options->google_tracking_str]);
    $plp_options->generate_qr_codes = (int)isset($_REQUEST[$plp_options->generate_qr_codes_str]);
    $plp_options->qr_code_links = (int)isset($_REQUEST[$plp_options->qr_code_links_str]);
    $plp_options->enable_link_health = (int)isset($_REQUEST[ $plp_options->enable_link_health_str]);
    $plp_options->enable_link_health_emails = (int)isset($_REQUEST[ $plp_options->enable_link_health_emails_str]);
    $plp_options->link_health_emails = isset($_REQUEST[$plp_options->link_health_emails_str]) && is_string($_REQUEST[$plp_options->link_health_emails_str]) ? stripslashes($_REQUEST[$plp_options->link_health_emails_str]) : get_option('admin_email');
    $plp_options->global_head_scripts = isset($_REQUEST[$plp_options->global_head_scripts_str]) && is_string($_REQUEST[$plp_options->global_head_scripts_str]) ? PrliUtils::sanitize_html(stripslashes($_REQUEST[$plp_options->global_head_scripts_str])) : '';
    $plp_options->base_slug_prefix = isset($_REQUEST[$plp_options->base_slug_prefix_str]) && is_string($_REQUEST[$plp_options->base_slug_prefix_str]) ? sanitize_title(stripslashes($_REQUEST[$plp_options->base_slug_prefix_str]), '') : '';
    $plp_options->num_slug_chars = isset($_REQUEST[$plp_options->num_slug_chars_str]) && is_numeric($_REQUEST[$plp_options->num_slug_chars_str]) ? PrliUtils::clamp((int) $_REQUEST[$plp_options->num_slug_chars_str], 0, 500) : 4;

    // Save the posted value in the database
    $plp_options->store();
  }

  public function create_link($link_id, $values) {
    global $plp_options, $prli_link_meta;

    if(!isset($values['google_tracking'])) {
      $prli_link_meta->update_link_meta($link_id, 'google_tracking', $plp_options->google_tracking);
    }
  }

  public function qr_code_link($pretty_link_id) {
    global $plp_options;
    $pretty_link_url = prli_get_pretty_link_url($pretty_link_id);

    ob_start();

/* NO LONGER A SETTING FOR THIS - NOT WORKING ANYWAYS
    if($plp_options->qr_code_links):
      ?><a href="<?php echo esc_url($pretty_link_url . '/gen_qr_png'); ?>" title="<?php echo esc_attr(sprintf(__('View QR Code for this link: %s', 'pretty-link'), $pretty_link_url)); ?>" target="_blank"><?php esc_html_e('QR Code', 'pretty-link'); ?></a><?php
    endif;
*/

    if($plp_options->generate_qr_codes):
      ?><a href="<?php echo esc_url($pretty_link_url . '/gen_qr_png?download=' . wp_create_nonce('prli-generate-qr-code')); ?>" title="<?php echo esc_attr(sprintf(__('Download QR Code for this link: %s', 'pretty-link'), $pretty_link_url)); ?>"><?php esc_html_e('QR Code', 'pretty-link'); ?></a><?php
    endif;

    return ob_get_clean();
  }

  /**
   * Renders the HTML for the "Check Health" link on the Pretty Links page.
   *
   * @access public
   * @param int $pretty_link_id The ID of the pretty link being rendered.
   * @return string
   */
  public function health_status_link($pretty_link_id) {
    $pretty_link_url = prli_get_pretty_link_url($pretty_link_id);
    ob_start();
    ?>
      <a
        href=""
        title="<?php echo esc_attr(sprintf(__('Check health status for this link: %s', 'pretty-link'), $pretty_link_url)); ?>"
        data-link-text="<?php esc_attr_e('Check Health', 'pretty-link'); ?>"
        data-link-alt="<?php esc_attr_e('Checking...', 'pretty-link'); ?>"
        data-linkid="<?php echo esc_attr($pretty_link_id); ?>"
        data-nonce="<?php echo wp_create_nonce('plp_check_single_link_health_' . $pretty_link_id) ?>"
        id="trigger-manual-health-check">
        <?php esc_html_e('Check Health', 'pretty-link'); ?>
      </a>
    <?php

    return ob_get_clean();
  }

  public function generate_qr_code($pretty_link_id, $slug) {
    global $prli_link, $plp_options;

    if( $plp_options->qr_code_links or
      ( $plp_options->generate_qr_codes and
      isset($_REQUEST['download']) and
      wp_verify_nonce($_REQUEST['download'], 'prli-generate-qr-code') ) ) {

      $qr_regexp = '#/gen_qr_png$#';

      if(!$pretty_link_id and preg_match($qr_regexp, $slug)) {
        $slug_sans_qr = preg_replace($qr_regexp, '', $slug);

        if($pretty_link = $prli_link->getOneFromSlug( $slug_sans_qr )) {
          $pretty_link_url = prli_get_pretty_link_url($pretty_link->id);

          // Determine download filename
          $download_filename = null;
          if(isset($_REQUEST['download']) and wp_verify_nonce($_REQUEST['download'], 'prli-generate-qr-code')) {
            $download_filename = $slug_sans_qr . "_qr.png";
          }

          // Clean the output buffer so we don't end up with corrupted files
          // Need to ensure we clear all levels of buffering, not just the top level
          while(ob_get_level()) {
            ob_end_clean();
          }

          // Check if logo overlay is enabled and GD is available
          $has_logo = ($plp_options->qr_logo_attachment_id > 0);
          $use_helper = $has_logo && class_exists('PlpQrCodeHelper') && PlpQrCodeHelper::is_gd_available();

          if($use_helper) {
            // Include QR library
            @include_once PLP_VENDOR_PATH."/phpqrcode/qrlib.php";

            // Use helper for logo overlay
            require_once(PLP_HELPERS_PATH . '/PlpQrCodeHelper.php');

            $qr_image = PlpQrCodeHelper::generate_qr_with_logo(
              $pretty_link_url,
              $plp_options->qr_logo_attachment_id,
              $plp_options->qr_logo_size_percent,
              $plp_options->qr_logo_white_background
            );

            if($qr_image) {
              PlpQrCodeHelper::output_png($qr_image, $download_filename);
              exit;
            }

            // Fallback to basic QR if logo overlay failed
          }

          // Standard QR code generation (no logo or GD unavailable)
          @include PLP_VENDOR_PATH."/phpqrcode/qrlib.php";

          // Use high error correction if logo was requested but failed
          // (gives user better experience if they upload logo later)
          $error_level = $has_logo ? QR_ECLEVEL_H : QR_ECLEVEL_L;

          header("Content-Type: image/png");

          if($download_filename) {
            header("HTTP/1.1 200 OK");
            header("Content-Disposition: attachment;filename=\"" . $download_filename . "\"");
            header("Content-Transfer-Encoding: binary");
          }

          QRcode::png($pretty_link_url, false, $error_level, 20, 2);

          exit;
        }
      }
    }

    return $pretty_link_id;
  }

  public function link_list_end_icons($link) {
    global $prli_link_meta, $plp_link_rotation;

    $dynamic_redirection = $prli_link_meta->get_link_meta($link->id, 'prli_dynamic_redirection', true);
    $enable_expire = $prli_link_meta->get_link_meta($link->id, 'enable_expire', true);
    $expire_type = $prli_link_meta->get_link_meta($link->id, 'expire_type', true);

    // Ensure reverse compatibility
    if(empty($dynamic_redirection)) {
      $dynamic_redirection = 'none';

      if($plp_link_rotation->there_are_rotations_for_this_link($link->id)) {
        $dynamic_redirection = 'rotate';
      }
    }

    if(empty($enable_expire) || empty($expire_type)) {
      $enable_expire = false;
      $expire_type = 'none';
    }

    if($enable_expire) {
      if($expire_type=='date') {
        $expire_date = $prli_link_meta->get_link_meta($link->id, 'expire_date', true);
        $expire_icon = 'history';
        $expire_class = '';

        if(($expired_url = PlpUtils::is_link_expired($link->id))) {
          $expire_class = 'prli-red';
          if($expired_url==404) {
            $expire_message = sprintf(__('This link expired on %1$s and will now cause a 404 error when visited', 'pretty-link'), $expire_date);
          }
          else {
            $expire_message = sprintf(__('This link expired on %1$s and now redirects to %2$s', 'pretty-link'), $expire_date, $expired_url);
          }
        }
        else {
          $expire_message = sprintf(__('This link is set to expire after the date %s', 'pretty-link'), $expire_date);
        }
      }
      else if($expire_type=='clicks') {
        $expire_clicks = $prli_link_meta->get_link_meta($link->id, 'expire_clicks', true);
        $expire_icon = 'ccw';
        $expire_class = '';

        if(($expired_url = PlpUtils::is_link_expired($link->id))) {
          $expire_class = 'prli-red';
          if($expired_url==404) {
            $expire_message = sprintf(__('This link expired after %d clicks and will now cause a 404 error when visited', 'pretty-link'), $expire_clicks);
          }
          else {
            $expire_message = sprintf(__('This link expired after %1$d clicks and now redirects to %2$s', 'pretty-link'), $expire_clicks, $expired_url);
          }
        }
        else {
          $expire_message = sprintf(__('This link is set to expire after %d clicks', 'pretty-link'), $expire_clicks);
        }
      }

      ?><i class="pl-list-icon pl-icon-<?php echo $expire_icon; ?> <?php echo $expire_class; ?>" title="<?php echo esc_attr($expire_message); ?>"></i><?php
    }

    if($dynamic_redirection=='rotate') {
      ?><i class="pl-list-icon pl-icon-shuffle" title="<?php esc_attr_e('This link has additional Target URL rotations', 'pretty-link'); ?>"></i><?php
    }
    else if($dynamic_redirection=='geo') {
      ?><i class="pl-list-icon pl-icon-globe" title="<?php esc_attr_e('This link has additional Geographic Target URLs', 'pretty-link'); ?>"></i><?php
    }
    else if($dynamic_redirection=='tech') {
      ?><i class="pl-list-icon pl-icon-mobile" title="<?php esc_attr_e('This link has additional Technology Dependent Conditional Target URLs', 'pretty-link'); ?>"></i><?php
    }
    else if($dynamic_redirection=='time') {
      $time_class = '';
      if(($time_url = PlpUtils::is_link_time_redirect_active($link->id))) {
        $time_message = sprintf(__('A Time Period Redirect is currently active for this link. When visited it will currently redirect to %s rather than the Target URL unless the link is expired.', 'pretty-link'), $time_url);
        $time_class = 'prli-green';
      }
      else {
        $time_message = __('Time Period Redirects have been setup for this link but the current time is not within any of them currently.', 'pretty-link');
      }

      ?><i class="pl-list-icon pl-icon-clock <?php echo $time_class; ?>" title="<?php echo esc_attr($time_message); ?>"></i><?php
    }
  }

  public function ajax_search_countries() {
    if(!PrliUtils::is_authorized()) {
      echo "Why you creepin?";
      die();
    }

    if(isset($_REQUEST['q']) && !empty($_REQUEST['q']) && is_string($_REQUEST['q'])) {
      $res = '';
      $countries = apply_filters('plp_search_countries_list', PrliUtils::countries());

      $q = sanitize_text_field(stripslashes($_REQUEST['q']));

      foreach($countries as $code => $name) {
        if(preg_match('/'.preg_quote($q).'/i', $code) ||
           preg_match('/'.preg_quote($q).'/i', $name)) {
          $res .= "{$name} [{$code}]\n";
        }
      }

      echo $res;
    }

    exit;
  }

  public function ajax_search_links() {
    if(!PrliUtils::is_authorized() || !check_ajax_referer('prli_search_links', false, false)) {
      wp_send_json_error('Unauthorized');
    }

    global $prli_link, $wpdb;

    $search = sanitize_text_field(wp_unslash($_REQUEST['q'] ?? ''));
    $page = max(1, (int) $_REQUEST['page'] ?? 1);
    $per_page = 30;
    $offset = ($page - 1) * $per_page;

    // Build the where clause for search.
    $where = '';
    if(!empty($search)) {
      $search_term = '%' . $wpdb->esc_like($search) . '%';
      $where = $wpdb->prepare(
        "(li.name LIKE %s OR li.slug LIKE %s OR li.id LIKE %s)",
        $search_term,
        $search_term,
        $search_term
      );
    }

    // Get links with pagination.
    $links = $prli_link->getAll($where, $wpdb->prepare(" ORDER BY li.name LIMIT %d OFFSET %d", $per_page, $offset));

    $results = [];
    foreach($links as $link) {
      $results[] = [
        'id' => $link->id,
        'text' => sprintf(
          __('id: %1$s | slug: %3$s | name: %2$s', 'pretty-link'),
          $link->id,
          mb_substr(stripslashes($link->name), 0, 50),
          $link->slug
        )
      ];
    }

    // Check if there are more results.
    $more = count($links) === $per_page;

    wp_send_json_success([
      'results' => $results,
      'pagination' => ['more' => $more]
    ]);
  }

  /**
   * Modifies the default "Broken Links" quick link.
   *
   * @access public
   * @param array $views Array of current views.
   * @return array
   */
  public function views($views) {
    if(!empty($_REQUEST['prettypay'])) {
      return $views;
    }

    $class = isset($_GET['link_status']) && $_GET['link_status'] == 'broken' ? 'current' : '';

    $views['prli_broken_links'] = sprintf(
      '<a href="%1$s" class="%2$s">%3$s<span class="count">(%4$d)</span></a>',
      esc_url(admin_url('/edit.php?link_status=broken&post_type=' . PrliLink::$cpt)),
      esc_attr($class),
      esc_html__('Find Broken Links', 'pretty-link'),
      PlpLink::get_broken_link_count()
    );
    return $views;
  }

  /**
   * Hooks into the posts_where filter when viewing the Pretty Links page
   * in order to filter by broken links.
   *
   * @access public
   * @return void
   */
  public function filter_by_broken_links() {
    global $typenow;

    if($typenow != 'pretty-link') {
      return;
    }

    add_filter('posts_where', array($this, 'posts_where'));
  }

  /**
   * Adds a WHERE clause on the Pretty Links page in order to filter
   * by broken links.
   *
   * @access public
   * @param string $where Current WHERE clause in the query.
   * @return string
   */
  public function posts_where($where) {
    global $prli_link_meta;

    if(isset($_GET['link_status']) && $_GET['link_status'] == 'broken') {
      // The links table is already joined with the posts, so we can use its alias here.
      $where .= " AND (SELECT lm.meta_value FROM {$prli_link_meta->table_name} AS lm
                  WHERE lm.meta_key=\"health_status\" AND lm.link_id=li.id) = \"inactive\"";
    }

    return $where;
  }

  /**
   * Detects whether or not the pretty link's target URL is broken.
   *
   * @access public
   * @param object $pretty_link The current pretty link object.
   * @return void
   */
  public function detect_broken_link($pretty_link) {
    global $plp_options, $prli_link_meta;

    // Bail early if Link Health isn't enabled or the redirect type is Pixel or PrettyPay link.
    if(!$plp_options->enable_link_health || in_array($pretty_link->redirect_type, array('pixel', 'prettypay_link_stripe'), true)) {
      return;
    }

    $pretty_link_target = apply_filters('prli_target_url', array('url' => $pretty_link->url, 'link_id' => $pretty_link->id, 'redirect_type' => $pretty_link->redirect_type));
    $target_url = $pretty_link_target['url'];

    // Make sure we have a URL using a valid protocol.
    if(!wp_http_validate_url($target_url)) {
      return;
    }

    $prli_link_meta->update_link_meta($pretty_link->id, 'health_last_checked', current_time('mysql'));

    // Make a GET request to the target URL and check its response.
    $response = wp_remote_get(
      $target_url,
      apply_filters(
        'prli_link_health_request_args',
        [
          'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36',
        ]
      )
    );

    $response_code = wp_remote_retrieve_response_code($response);

    $invalid_response_codes = apply_filters('prli_invalid_response_codes', array('404'));

    if(in_array($response_code, $invalid_response_codes)) {
      $prli_link_meta->update_link_meta($pretty_link->id, 'health_status', 'inactive');
    } else {
      $prli_link_meta->update_link_meta($pretty_link->id, 'health_status', 'active');
    }
  }

  /**
   * Looks at the most active links and checks if they're broken.
   * This method is executed every 15 minutes through a cron.
   *
   * @access public
   * @return void
   */
  public function detect_broken_links() {
    $links = PlpLink::get_active_links();

    if(count($links) <= 0) {
      return;
    }

    foreach($links as $link) {
      $this->detect_broken_link($link);
    }
  }

  /**
   * Checks if there are any broken links which need an email sent out.
   * This method is executed every week through a cron.
   *
   * @access public
   * @return void
   */
  public function send_broken_link_emails() {
    global $plp_options;

    $args = apply_filters('prli_link_health_email_args', array(
      'to' => explode(',', $plp_options->link_health_emails),
      'subject' => __('[Pretty Links] Broken Links Weekly Report', 'pretty-link'),
      'message' => $this->get_broken_link_message_content()
    ));

    extract($args);
    PlpUtils::send_email($to, $subject, $message);
  }

  /**
   * Adds a "prli-broken-link" class to a link classified as being broken.
   *
   * @access public
   * @param array $classes Array of classes applied to the post.
   * @param array $class Array of additional class names.
   * @param int $post_id ID of the post.
   * @return array
   */
  public function add_broken_link_class($classes, $class, $post_id) {
    global $prli_link_meta, $prli_link;

    if(!is_admin()) {
      return $classes;
    }

    $screen = get_current_screen();
    $link = $prli_link->get_one_by('link_cpt_id', $post_id);

    if($screen && $screen->id != 'edit-pretty-link') {
      return $classes;
    }

    if($link && !in_array($link->redirect_type, array('pixel', 'prettypay_link_stripe'), true)) {
      $health_status = $prli_link_meta->get_link_meta($link->id, 'health_status', true);

      if($health_status == 'inactive') {
        $classes[] = 'prli-broken-link';
      }
    }

    return $classes;
  }

  /**
   * Adds cron schedules needed for the PlpLinks controller.
   *
   * @access public
   * @param array $schedules Array of existing cron schedules.
   * @return array
   */
  public function intervals($schedules) {
    global $plp_options;

    if($plp_options->enable_link_health) {
      $schedules['prli_fifteen_minutes'] = array(
        'interval' => 15 * MINUTE_IN_SECONDS,
        'display' => 'Every 15 Minutes'
      );

      if($plp_options->enable_link_health_emails) {
        $schedules['prli_weekly'] = array(
          'interval' => 604800,
          'display' => 'Weekly'
        );
      }
    }

    return $schedules;
  }

  /**
   * Adds Pro-only columns to the Pretty Links listing page.
   *
   * @access public
   * @param array $columns Array of existing columns on the Pretty Links listing page.
   * @return array
   */
  public function add_pro_columns($columns) {
    global $plp_options;

    if($plp_options->enable_link_health && empty($_REQUEST['prettypay'])) {
      $columns['health_status'] = esc_html__('Status', 'pretty-link');
    }

    return $columns;
  }

  /**
   * Renders the content for Pro-only columns on the Pretty Links listing page.
   *
   * @access public
   * @param string $column The current column being rendered.
   * @param object $link The current pretty link being rendered.
   * @return void
   */
  public function render_pro_columns($column, $link) {
    global $plp_options, $prli_link_meta;

    if($plp_options->enable_link_health && empty($_REQUEST['prettypay']) && $column == 'health_status') {
      $status = $prli_link_meta->get_link_meta($link->id, 'health_status', true);
      $last_checked = $prli_link_meta->get_link_meta($link->id, 'health_last_checked', true);

      $this->get_link_health_status($status, $last_checked, true);
    }
  }

  /**
   * Handles managing the cron events for the PlpLinks controller.
   *
   * @access private
   * @return void
   */
  private function manage_cron_events() {
    global $plp_options;

    if($plp_options->enable_link_health) {
      if(!wp_next_scheduled('prli_broken_link_check')) {
        wp_schedule_event(time(), 'prli_fifteen_minutes', 'prli_broken_link_check');
      }

      add_action('prli_broken_link_check', array($this, 'detect_broken_links'));

      if($plp_options->enable_link_health_emails) {
        if(!wp_next_scheduled('prli_send_broken_link_emails')) {
          wp_schedule_event(time() + 604800, 'prli_weekly', 'prli_send_broken_link_emails');
        }

        add_action('prli_send_broken_link_emails', array($this, 'send_broken_link_emails'));
      }
    }

    // Remove any scheduled cron events if Link Health or Link Health emails are disabled.
    if(!$plp_options->enable_link_health) {
      wp_clear_scheduled_hook('prli_broken_link_check');
      wp_clear_scheduled_hook('prli_send_broken_link_emails');
    }

    if(!$plp_options->enable_link_health_emails) {
      wp_clear_scheduled_hook('prli_send_broken_link_emails');
    }
  }

  /**
   * Returns the HTML for the broken links email message.
   *
   * @access private
   * @return string
   */
  private function get_broken_link_message_content() {
    $broken_link_url = esc_url(admin_url('edit.php?link_status=broken&post_type=pretty-link'));
    $broken_link_count = PlpLink::get_broken_link_count();
    $ad = PlpUtils::get_ad();

    ob_start();
    include_once(PLP_VIEWS_PATH . '/emails/broken-links.php');
    return ob_get_clean();
  }

  /**
   * Retrieves the markup for a link's health status.
   *
   * @access private
   * @param string $status Link's health status.
   * @param string $last_checked Date link's health was last checked.
   * @param bool Whether or not to print the status output. Defaults to false.
   * @return string
   */
  private function get_link_health_status($status, $last_checked, $print = false) {
    if(!$status) {
      $tooltip = esc_html__('Not yet checked.', 'pretty-link');
    } elseif($last_checked) {
      $tooltip = sprintf(
        __('Last checked on %s.', 'pretty-link'),
        $last_checked
      );
    } else {
      $tooltip = esc_html__('Last checked unknown.', 'pretty-link');
    }

    $statuses = array(
      '' => esc_html__('Waiting', 'pretty-link'),
      'active' => esc_html__('Okay', 'pretty-link'),
      'inactive' => esc_html__('Broken', 'pretty-link')
    );

    $markup = '<span class="tooltip ' . esc_attr($status) . '" title="' . esc_attr($tooltip) . '">' . esc_html($statuses[$status]) . '</span>';

    if($print) {
      echo $markup;
    } else {
      return $markup;
    }
  }
}
