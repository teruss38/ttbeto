<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PlpAppController extends PrliBaseController {
  public function load_hooks() {
    add_action('admin_menu', array($this, 'admin_menu'));
    add_action('prli_load_admin_scripts', array($this, 'enqueue_admin_scripts'), 10, 2);
    add_action('init', array($this,'route_standalone_request'));
  }

  public function admin_menu() {
    global $plp_options;

    $role = 'administrator';

    if(isset($plp_options->min_role)) {
      $role = $plp_options->min_role;
    }

    do_action('plp_admin_menu', $role);
  }

  public function enqueue_admin_scripts($hook, $page_vars) {
    global $prli_link, $prli_options, $prli_link_meta, $current_screen;

    extract($page_vars);

    if( preg_match('/_page_plp-reports$/', $hook) ) {
      wp_enqueue_style('plp-reports', PLP_CSS_URL.'/admin_reports.css', null, PRLI_VERSION);
      wp_register_script('plp-google-vis', 'https://www.gstatic.com/charts/loader.js', array(), PRLI_VERSION);

      // TODO: We actually want localize script for the data and separate js files for these
      if(isset($_GET['action']) && $_GET['action'] == 'display-custom-report') {
        global $plp_report;
        wp_enqueue_script('plp-draw-report', PLP_JS_URL.'/admin_draw_report.js', array('jquery','plp-google-vis'), PRLI_VERSION);
        wp_localize_script('plp-draw-report', 'PlpReport', $plp_report->custom_report_vars());
      }
      else if(isset($_GET['action']) && $_GET['action'] == 'display-split-test-report') {
        global $plp_report;
        wp_enqueue_script('plp-draw-report', PLP_JS_URL.'/admin_draw_report.js', array('jquery','plp-google-vis'), PRLI_VERSION);
        wp_localize_script('plp-draw-report', 'PlpReport', $plp_report->split_test_report_vars());
      }
      else {
        wp_enqueue_script('plp-reports', PLP_JS_URL.'/admin_reports.js', array('jquery'), PRLI_VERSION);
      }
    }

    if( preg_match('/_page_pretty-link-options$/', $hook) ) {
      wp_register_style('plp-spectrum', PLP_VENDOR_URL.'/spectrum/spectrum.min.css', array(), '1.8.0');
      wp_register_script('plp-spectrum', PLP_VENDOR_URL.'/spectrum/spectrum.min.js', array(), '1.8.0', true);

      wp_enqueue_style('plp-options', PLP_CSS_URL.'/admin_options.css', array('pl-options','plp-spectrum'), PRLI_VERSION);
      wp_enqueue_script('plp-options', PLP_JS_URL.'/admin_options.js', array('jquery','pl-options','plp-spectrum'), PRLI_VERSION);

      // Enqueue WordPress Media Library for QR logo upload
      wp_enqueue_media();
      wp_enqueue_script('plp-qr-logo', PLP_JS_URL.'/admin_qr_logo.js', array('jquery', 'wp-util'), PRLI_VERSION, true);
    }

    if( preg_match('/_page_pretty-link-tools$/', $hook) ) {
      wp_enqueue_style('plp-bookmarklet', PLP_CSS_URL.'/admin_bookmarklet.css', null, PRLI_VERSION);
      wp_enqueue_script('plp-bookmarklet', PLP_JS_URL.'/admin_bookmarklet.js', array('jquery'), PRLI_VERSION);
      wp_localize_script('plp-bookmarklet', 'PlpBookmarklet', array( 'url' => site_url("index.php?action=prli_bookmarklet&k={$prli_options->bookmarklet_auth}") ));
    }

    if( in_array( $hook, array('post-new.php','post.php') ) ) {
      global $post;
      wp_enqueue_style('plp-post', PLP_CSS_URL.'/admin_post.css', null, PRLI_VERSION);
      wp_register_script('jquery-tooltipster', PRLI_JS_URL.'/tooltipster.bundle.min.js', array('jquery'), PRLI_VERSION);
      wp_enqueue_script('plp-post', PLP_JS_URL.'/admin_post.js', array('jquery', 'jquery-tooltipster'), PRLI_VERSION);
      wp_localize_script('plp-post', 'PlpPost', array( 'post_id' => $post->ID ));
    }

    if($current_screen && $current_screen->id == 'edit-pretty-link') {
      wp_register_script('plp-admin-link-list', PLP_JS_URL . '/admin_link_list.js', array('jquery', 'jquery-tooltipster'), PRLI_VERSION);
      wp_enqueue_script('plp-admin-link-list');
    }

    if($current_screen && strstr($current_screen->id, 'pretty-link') !== false) {
      wp_enqueue_style(
        'plp-admin-shared',
        PLP_CSS_URL . '/admin_shared.css',
        null,
        PRLI_VERSION
      );
    }

    if( $is_link_edit_page || $is_link_new_page ) {
      global $post;

      wp_enqueue_style('jquery-ui-timepicker-addon', PLP_CSS_URL.'/jquery-ui-timepicker-addon.css', array('pl-ui-smoothness'), PRLI_VERSION);

      wp_register_script('plp-timepicker-js', PLP_JS_URL.'/jquery-ui-timepicker-addon.js', array('jquery-ui-datepicker'));
      wp_register_script('plp-datepicker', PLP_JS_URL.'/date_picker.js', array('plp-timepicker-js'), PRLI_VERSION);

      ob_start();
      PlpLinksHelper::rotation_row('',0);
      $rotation_row_html = ob_get_clean();

      ob_start();
      PlpLinksHelper::geo_row();
      $geo_row_html = ob_get_clean();

      ob_start();
      PlpLinksHelper::tech_row();
      $tech_row_html = ob_get_clean();

      ob_start();
      PlpLinksHelper::time_row();
      $time_row_html = ob_get_clean();

      $link_id = 0;
      if(isset($post) && isset($post->ID)) {
        $link_id = $prli_link->get_link_from_cpt($post->ID);
      }

      if(!empty($link_id) &&
         empty($_POST['prli_geo_url']) &&
         empty($_POST['prli_geo_countries'])) {
        $geo_url = $prli_link_meta->get_link_meta($link_id, 'geo_url');
        $geo_countries = $prli_link_meta->get_link_meta($link_id, 'geo_countries');
      }
      else {
        $geo_url = isset($_POST['prli_geo_url']) && is_array($_POST['prli_geo_url']) ? array_map('esc_url_raw', array_map('trim', wp_unslash($_POST['prli_geo_url']))) : array();
        $geo_countries = isset($_POST['prli_geo_countries']) && is_array($_POST['prli_geo_countries']) ? array_map('sanitize_text_field', wp_unslash($_POST['prli_geo_countries'])) : array();
      }

      if(!empty($link_id) && empty($_POST['prli_tech_url']) &&
         empty($_POST['prli_tech_device']) &&
         empty($_POST['prli_tech_os']) &&
         empty($_POST['prli_tech_browser'])) {
        $tech_url = $prli_link_meta->get_link_meta($link_id, 'tech_url');
        $tech_device = $prli_link_meta->get_link_meta($link_id, 'tech_device');
        $tech_os = $prli_link_meta->get_link_meta($link_id, 'tech_os');
        $tech_browser = $prli_link_meta->get_link_meta($link_id, 'tech_browser');
      }
      else {
        $tech_url = isset($_POST['prli_tech_url']) && is_array($_POST['prli_tech_url']) ? array_map('esc_url_raw', array_map('trim', wp_unslash($_POST['prli_tech_url']))) : array();
        $tech_device = isset($_POST['prli_tech_device']) && is_array($_POST['prli_tech_device']) ? array_map('sanitize_key', wp_unslash($_POST['prli_tech_device'])) : array();
        $tech_os = isset($_POST['prli_tech_os']) && is_array($_POST['prli_tech_os']) ? array_map('sanitize_key', wp_unslash($_POST['prli_tech_os'])) : array();
        $tech_browser = isset($_POST['prli_tech_browser']) && is_array($_POST['prli_tech_browser']) ? array_map('sanitize_key', wp_unslash($_POST['prli_tech_browser'])) : array();
      }

      if(!empty($link_id) && empty($_POST['prli_time_url']) &&
         empty($_POST['prli_time_start']) &&
         empty($_POST['prli_time_end'])) {
        $time_url = $prli_link_meta->get_link_meta($link_id, 'time_url');
        $time_start = $prli_link_meta->get_link_meta($link_id, 'time_start');
        $time_end = $prli_link_meta->get_link_meta($link_id, 'time_end');
      }
      else {
        $time_url = isset($_POST['prli_time_url']) && is_array($_POST['prli_time_url']) ? array_map('esc_url_raw', array_map('trim', wp_unslash($_POST['prli_time_url']))) : array();
        $time_start = isset($_POST['prli_time_start']) && is_array($_POST['prli_time_start']) ? array_map('sanitize_text_field', wp_unslash($_POST['prli_time_start'])) : array();
        $time_end = isset($_POST['prli_time_end']) && is_array($_POST['prli_time_end']) ? array_map('sanitize_text_field', wp_unslash($_POST['prli_time_end'])) : array();
      }

      wp_enqueue_script('plp-admin-links', PLP_JS_URL.'/admin_links.js', array('jquery','prli-admin-links','suggest','plp-datepicker'), PRLI_VERSION);
      wp_localize_script('plp-admin-links', 'PlpLink', array(
        'l10n_print_after' => 'PlpLink = ' . wp_json_encode(compact('rotation_row_html','geo_row_html','geo_url','geo_countries','tech_row_html','tech_url','tech_device','tech_os','tech_browser','time_row_html','time_url','time_start','time_end'))
      ));
    }
  }

  public function route_standalone_request() {
    $plugin     = (isset($_REQUEST['plugin'])?sanitize_key(stripslashes($_REQUEST['plugin'])):'');
    $controller = (isset($_REQUEST['controller'])?sanitize_key(stripslashes($_REQUEST['controller'])):'');
    $action     = (isset($_REQUEST['action'])?sanitize_key(stripslashes($_REQUEST['action'])):'');

    if( $plugin && $plugin=='pretty-link-pro' && $controller && $action ) {
      if($controller && $controller=='links') {
        if($action && $action=='prettybar') {
          PlpPrettyBarHelper::render_prettybar(sanitize_text_field(stripslashes($_REQUEST['s'])));
        }
      }
      exit;
    }
    else if( $action == 'prli_endpoint_url' ) {
      global $prli_options;

      $key = sanitize_key(stripslashes($_REQUEST['k']));
      $url = esc_url_raw(trim(stripslashes($_REQUEST['url'])));

      if($key == $prli_options->bookmarklet_auth) {
        $pretty_link_id = prli_create_pretty_link( $url );
        if( $pretty_link = prli_get_pretty_link_url( $pretty_link_id ) ) {
          echo esc_url($pretty_link);
        }
        else {
          esc_html_e('ERROR: Your Pretty Link was unable to be created', 'pretty-link');
        }
      }
      else {
        esc_html_e('Unauthorized', 'pretty-link');
      }

      exit;
    }
  }
}
