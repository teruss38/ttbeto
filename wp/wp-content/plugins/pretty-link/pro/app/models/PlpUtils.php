<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PlpUtils {
  public static function sort_by_stringlen($word_array,$dir = 'ASC')
  {
    if( $dir == "ASC" ) {
      uasort($word_array, 'PlpUtils::compare_stringlen_asc');
    }
    else if( $dir == "DESC" ) {
      uasort($word_array, 'PlpUtils::compare_stringlen_desc');
    }

    return $word_array;
  }

  /**
    * This function expects an array of weights in integer
    * form [ 35, 25, 15, 50 ] that add up to 100.
    */
  public static function w_rand($weights) {
    $r = mt_rand(1,1000);
    $offset = 0;
    foreach ($weights as $k => $w) {
      $offset += $w*10;
      if ($r <= $offset) { return $k; }
    }
  }

  public static function ga_installed() {
    if(!function_exists('is_plugin_active')) {
      require(ABSPATH . '/wp-admin/includes/plugin.php');
    }

    if(is_plugin_active('google-analyticator/google-analyticator.php')) {
      return array('name' => __('Google Analyticator', 'pretty-link'), 'slug' => 'google-analyticator');
    }
    // DEPRECATED support on Jan 2020
    // else if(is_plugin_active('google-analytics-for-wordpress/googleanalytics.php')) {
      // return array('name' => __('Google Analytics by MonsterInsights', 'pretty-link'), 'slug' => 'google-analytics-for-wordpress');
    // }
    // else if(is_plugin_active('googleanalytics/googleanalytics.php')) {
      // return array('name' => __('Google Analytics', 'pretty-link'), 'slug' => 'google-analytics');
    // }
    else {
      return false;
    }
  }

  public static function ga_tracking_code($ga_plugin_slug) {
    ob_start();

    if($ga_plugin_slug == 'google-analyticator' && function_exists('add_google_analytics')) {
      add_google_analytics();
    }
    // DEPRECATED support on Jan 2020
    // elseif($ga_plugin_slug == 'google-analytics-for-wordpress' && class_exists('Yoast_GA_JS') && class_exists('Yoast_GA_Options') && class_exists('Yoast_GA_Universal')) {
      // //Working as of vs 5.4.9 of the GA plugin by MonsterInsights -- they keep changing this though :(
      // $yoast_ops = Yoast_GA_Options::instance()->options;

      // if(isset($yoast_ops->options['enable_universal']) && $yoast_ops->options['enable_universal'] == 1) {
        // $tracking = new Yoast_GA_Universal;
      // } else {
        // $tracking = new Yoast_GA_JS;
      // }

      // $tracking->tracking();
    // }
    // elseif($ga_plugin_slug == 'google-analytics' && class_exists('Ga_Frontend')) {
      // Ga_Frontend::googleanalytics();
    // }

    return ob_get_clean();
  }

  // Utility functions not part of this class //
  public static function compare_stringlen_asc($val_1, $val_2) {
    // initialize the return value to zero
    $retVal = 0;

    // compare lengths
    $firstVal = strlen($val_1);
    $secondVal = strlen($val_2);

    if($firstVal > $secondVal) {
      $retVal = 1;
    }
    else if($firstVal < $secondVal) {
      $retVal = -1;
    }

    return $retVal;
  }

  public static function compare_stringlen_desc($val_1, $val_2) {
    // initialize the return value to zero
    $retVal = 0;

    // compare lengths
    $firstVal = strlen($val_1);
    $secondVal = strlen($val_2);

    if($firstVal > $secondVal) {
      $retVal = -1;
    }
    else if($firstVal < $secondVal) {
      $retVal = 1;
    }

    return $retVal;
  }

  public static function locate_by_ip($ip=null, $source='caseproof') {
    global $prli_utils;
    $ip = (is_null($ip)?$prli_utils->get_current_client_ip():$ip);

    if(!self::is_ip($ip)) { return false; }

    $lockey = 'pl_locate_by_ip_' . md5($ip.$source);
    $loc = get_transient($lockey);

    if(false===$loc) {
      if($source=='caseproof') {
        $url    = "https://cspf-locate.herokuapp.com?ip={$ip}";
        $cindex = 'country_code';
      }
      elseif($source=='freegeoip') {
        $url    = "https://freegeoip.net/json/{$ip}";
        $cindex = 'country_code';
      }
      else { // geoplugin
        $url    = "http://www.geoplugin.net/json.gp?ip={$ip}";
        $cindex = 'geoplugin_countryCode';
      }

      $res = wp_remote_get($url);
      if(is_wp_error($res)) { return ''; }
      $obj = json_decode($res['body']);
      $country = (isset($obj->{$cindex})?$obj->{$cindex}:'');

      $loc = apply_filters('plp_locate_by_ip', (object)compact('country'), $lockey, $obj);

      set_transient($lockey,$loc,DAY_IN_SECONDS);
    }

    return $loc;
  }

  public static function is_ip($ip, $version='any') {
    $ipv4_pattern = '#^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$#i';
    $ipv6_pattern = '/^(((?=.*(::))(?!.*\3.+\3))\3?|([\dA-F]{1,4}(\3|:\b|$)|\2))(?4){5}((?4){2}|(((2[0-4]|1\d|[1-9])?\d|25[0-5])\.?\b){4})\z/i';

    return (
      ($version==4 && preg_match($ipv4_pattern,$ip)) ||
      ($version==6 && preg_match($ipv6_pattern,$ip)) ||
      ($version=='any' && (preg_match($ipv4_pattern,$ip) || preg_match($ipv6_pattern,$ip)))
    );
  }

  public static function country_by_ip($ip=null, $source='geoplugin') {
    return (($loc = self::locate_by_ip()) ? $loc->country : '' );
  }

  public static function base36_encode($base10) {
    return base_convert($base10, 10, 36);
  }

  public static function base36_decode($base36) {
    return base_convert($base36, 36, 10);
  }

  public static function is_link_expired($link_id) {
    global $prli_link, $prli_link_meta;

    $expire_enabled = $prli_link_meta->get_link_meta($link_id, 'enable_expire', true);

    if(!empty($expire_enabled)) {
      $expire_type = $prli_link_meta->get_link_meta($link_id, 'expire_type', true);
      $expired = false;

      if($expire_type=='date') {
        $expire_date = $prli_link_meta->get_link_meta($link_id, 'expire_date', true);
        $now_ts = strtotime(gmdate('c')); //Make sure it's UTC
        $expire_ts = strtotime($expire_date); // Expire AFTER the date
        $expired = ($now_ts > $expire_ts);
      }
      else if($expire_type=='clicks') {
        $expire_clicks = $prli_link_meta->get_link_meta($link_id, 'expire_clicks', true);
        $link = $prli_link->getOne($link_id, OBJECT, true);
        $num_clicks = $link->uniques;
        //echo "Num Clicks: {$num_clicks} / Expire Clicks: {$expire_clicks}"; exit;
        $expired = ($num_clicks >= $expire_clicks);
      }

      if($expired) {
        $enable_expired_url = $prli_link_meta->get_link_meta($link_id, 'enable_expired_url', true);
        $expired_url = $prli_link_meta->get_link_meta($link_id, 'expired_url', true);

        if(!empty($enable_expired_url)) {
          return $expired_url;
        }
        else {
          return 404;
        }
      }
    }

    return false;
  }

  public static function is_link_time_redirect_active($link_id) {
    global $prli_link_meta;

    $time_urls = $prli_link_meta->get_link_meta($link_id, 'time_url');
    $time_starts = $prli_link_meta->get_link_meta($link_id, 'time_start');
    $time_ends = $prli_link_meta->get_link_meta($link_id, 'time_end');

    $now = time();
    foreach($time_urls as $i => $time_url) {
      $time_start = strtotime($time_starts[$i]);
      $time_end = strtotime($time_ends[$i]);
      if($time_start <= $now && $time_end >= $now) {
        return $time_url;
      }
    }

    return false;
  }

  /** Get link categories
   *
   *  @param $link_id The id of the link we want to get categories for
   *
   *  @return Array of strings -- category slugs
   */
  public static function get_link_categories($link_id) {
    return self::get_link_terms($link_id, PlpLinkCategoriesController::$ctax);
  }

  /** Get link tags
   *
   *  @param $link_id The id of the link we want to get tags for
   *
   *  @return Array of strings -- tag slugs
   */
  public static function get_link_tags($link_id) {
    return self::get_link_terms($link_id, PlpLinkTagsController::$ctax);
  }

  /**
   * Sends out an email from the site.
   *
   * @access public
   * @param array $recipients Array of recipients.
   * @param string $subject Email subject.
   * @param string $message Email message body.
   * @param array $headers Array of email headers.
   * @param array $attachments Array of email attachments.
   * @return bool
   */
  public static function send_email($recipients, $subject, $message, $headers = array(), $attachments = array()) {
    $from_name = apply_filters('prli_email_from_name', get_bloginfo('name'));
    $from_email = apply_filters('prli_email_from_email', get_option('admin_email'));

    $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
    $headers[] = 'Content-Type: text/html; charset=' . get_option('blog_charset');

    return wp_mail($recipients, $subject, $message, $headers, $attachments);
  }

  /**
   * Get a random ad or educational tip.
   *
   * @return string
   */
  public static function get_ad() {
    $url = add_query_arg(array(
      'ad-group' => 4,
      'orderby' => 'rand',
    ), 'https://sg-assets.caseproof.com/wp-json/wp/v2/ads');

    $response = wp_remote_get($url);
    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    if($code == 200 && $body) {
      $ads = json_decode($body, true);

      if(is_array($ads) && isset($ads[0]['rendered_ad'])) {
        return $ads[0]['rendered_ad'];
      }
    }

    return '';
  }

  /** Get link terms
   *
   *  @param $link_id The id of the link we want to get terms for
   *  @param $taxonomy The name of the taxonomy
   *
   *  @return Array of strings -- term slugs
   */
  private static function get_link_terms($link_id, $taxonomy) {
    global $prli_link;

    $link = $prli_link->getOne($link_id);
    $terms = wp_get_object_terms(array($link->link_cpt_id), array($taxonomy));

    if(is_wp_error($terms)) {
      error_log('** Error in PlpUtils::get_link_terms: '. $terms->get_error_message());
      $terms = array();
    }

    return array_map(
      function ($term) {
        return $term->slug;
      },
      $terms
    );
  }

} //End class

