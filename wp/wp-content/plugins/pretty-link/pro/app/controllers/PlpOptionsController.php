<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PlpOptionsController extends PrliBaseController {
  public $opt_fields;

  public function load_hooks() {
    add_action('prli_admin_general_options', array($this, 'general'));
    add_action('prli_admin_options_pages', array($this, 'display'));

    add_filter( 'prli-validate-options', array($this, 'validate') );
    add_filter( 'prli-update-options', array($this, 'update') );
    add_filter( 'prli-store-options', array($this, 'store') );

    $this->opt_fields = array(
      'hidden_field_name' => 'prlipro_update_options',
      'pages_auto' => 'prli_pages_auto',
      'posts_auto' => 'prli_posts_auto',
      'pages_category' => 'prli_pages_category',
      'posts_category' => 'prli_posts_category',
      'autocreate' => 'prli_autocreate',
      'social_buttons' => 'prli_social_buttons',
      'social_buttons_placement' => 'prli_social_buttons_placement',
      'social_buttons_padding' => 'prli_social_buttons_padding',
      'social_buttons_show_in_feed' => 'prli_social_buttons_show_in_feed',
      'social_posts_buttons' => 'prli_social_posts_buttons',
      'social_pages_buttons' => 'prli_social_pages_buttons',
      'keyword_replacement_is_on' => 'prli_keyword_replacement_is_on',
      'keywords_per_page' => 'prli_keywords_per_page',
      'keyword_links_per_page' => 'prli_keyword_links_per_page',
      'keyword_links_open_new_window' => 'prli_keyword_links_open_new_window',
      'keyword_links_nofollow' => 'prli_keyword_links_nofollow',
      'keyword_links_sponsored' => 'prli_keyword_links_sponsored',
      'set_keyword_thresholds' => 'prli_set_keyword_thresholds',
      'url_replacement_is_on' => 'prli_url_replacement_is_on',
      'replace_urls_with_pretty_links' => 'prli_replace_urls_with_pretty_links',
      'url_links_open_new_window' => 'prli_url_links_open_new_window',
      'replace_urls_with_pretty_links_blacklist' => 'prli_replace_urls_with_pretty_links_blacklist',
      'url_replacement_cpts' => 'prli_url_replacement_cpts',
      'replace_keywords_in_comments' => 'prli_replace_keywords_in_comments',
      'replace_keywords_in_feeds' => 'prli_replace_keywords_in_feeds',
      'enable_link_to_disclosures' => 'prli_enable_link_to_disclosures',
      'disclosures_link_url' => 'prli_disclosures_link_url',
      'disclosures_link_text' => 'prli_disclosures_link_text',
      'disclosures_link_position' => 'prli_disclosures_link_position',
      'enable_keyword_link_disclosures' => 'prli_enable_keyword_link_disclosures',
      'keyword_link_disclosure' => 'prli_keyword_link_disclosure',
      'keyword_replacement_cpts' => 'prli_keyword_replacement_cpts',
      'use_prettylink_url' => 'prli_use_prettylink_url',
      'prettylink_url' => 'prli_prettylink_url',
      'minimum_access_role' => 'prli_min_role',
      'allow_public_link_creation' => 'prli_allow_public_link_creation',
      'use_public_link_display_page' => 'prli_use_public_link_display_page',
      'public_link_display_page' => 'prli_public_link_display_page',
      'prettybar_image_url' => 'prli_prettybar_image_url',
      'prettybar_background_image_url' => 'prli_prettybar_background_image_url',
      'prettybar_color' => 'prli_prettybar_color',
      'prettybar_text_color' => 'prli_prettybar_text_color',
      'prettybar_link_color' => 'prli_prettybar_link_color',
      'prettybar_hover_color' => 'prli_prettybar_hover_color',
      'prettybar_visited_color' => 'prli_prettybar_visited_color',
      'prettybar_show_title' => 'prli_prettybar_show_title',
      'prettybar_show_description' => 'prli_prettybar_show_description',
      'prettybar_show_share_links' => 'prli_prettybar_show_share_links',
      'prettybar_show_target_url_link' => 'prli_prettybar_show_target_url_link',
      'prettybar_title_limit' => 'prli_prettybar_title_limit',
      'prettybar_desc_limit' => 'prli_prettybar_desc_limit',
      'prettybar_link_limit' => 'prli_prettybar_link_limit',
      'prettybar_hide_attrib_link' => 'prettybar_hide_attrib_link',
      'prettybar_attrib_url' => 'prettybar_attrib_url',
      'qr_logo_attachment_id' => 'prlipro-qr-logo-attachment-id',
      'qr_logo_size_percent' => 'prlipro-qr-logo-size-percent',
      'qr_logo_white_background' => 'prlipro-qr-logo-white-background'
    );
  }

  public function display() {
    global $prli_options, $plp_options;

    extract($this->opt_fields);

    $index_keywords = get_option('plp_index_keywords', false);

    require_once(PLP_VIEWS_PATH.'/options/form.php');
  }

  public function general() {
    global $plp_options;

    extract($this->opt_fields);

    require_once(PLP_VIEWS_PATH.'/options/general.php');
  }

  public function validate($errors=array()) {
    extract( $this->opt_fields );

    // Validate This
    //if( !empty($params[ $prettybar_link_limit ]) && !preg_match( "#^[0-9]*$#", $params[ $prettybar_link_limit ] ) )
    //  $errors[] = __("PrettyBar Link Character Limit must be a number", 'pretty-link');

    if( isset($_POST[$set_keyword_thresholds]) && empty($_POST[ $keywords_per_page ]) ) {
      $errors[] = __('Keywords Per Page is required', 'pretty-link');
    }

    if( isset($_POST[$set_keyword_thresholds]) && empty($_POST[ $keyword_links_per_page ]) ) {
      $errors[] = __('Keyword Links Per Page is required', 'pretty-link');
    }

    if( isset($_POST[ $use_prettylink_url ]) &&
        !preg_match('/^http.?:\/\/.*\..*[^\/]$/', $_POST[ $prettylink_url ] )) {
      $errors[] = __('You need to enter a valid Pretty Link Base URL now that you have selected "Use an alternate base url for your Pretty Links"', 'pretty-link');
    }

    if( isset($_POST[ $use_public_link_display_page ]) &&
        !preg_match('/^http.?:\/\/.*\..*[^\/]$/', $_POST[ $public_link_display_page ] ) ) {
      $errors[] = __('You need to enter a valid Public Link Display URL now that you have selected "Use a custom public link display page"', 'pretty-link');
    }

    if( get_option('prlipro_prettybar_active') &&
        !isset($_POST['prettybar_hide_attrib_link']) &&
        !empty($_POST['prlipro-attrib-url']) &&
        !preg_match('/^http.?:\/\/.*\..*$/', $_POST['prlipro-attrib-url'] ) ) {
      $errors[] = __("Pretty Bar Attribution URL must be a correctly formatted URL", 'pretty-link');
    }

    // QR Logo validation
    if( isset($_POST[$qr_logo_attachment_id]) && !empty($_POST[$qr_logo_attachment_id]) ) {
      $attachment_id = (int)$_POST[$qr_logo_attachment_id];
      if( $attachment_id > 0 && !wp_attachment_is_image($attachment_id) ) {
        $errors[] = __('QR Logo must be a valid image file (JPG, PNG, GIF, or WebP)', 'pretty-link');
      }

      // Prevent silent failure: WebP overlay requires GD WebP support
      if ( $attachment_id > 0 ) {
        $mime = get_post_mime_type($attachment_id);
        if ( $mime === 'image/webp' && !function_exists('imagecreatefromwebp') ) {
          $errors[] = __('Your server does not support WebP logos for QR overlay. Please contact your host or use PNG/JPG/GIF instead.', 'pretty-link');
        }
      }
    }

    if( isset($_POST[$qr_logo_size_percent]) ) {
      $size = (int)$_POST[$qr_logo_size_percent];
      if( $size < 10 || $size > 30 ) {
        $errors[] = __('QR Logo Size must be between 10% and 30% for reliable scanning', 'pretty-link');
      }
    }

    return $errors;
  }

  public function update($params) {
    global $prli_options, $plp_options;

    extract( $this->opt_fields );

    // Read their posted value
    $plp_options->pages_auto = (int)isset($params[ $pages_auto ]);
    $plp_options->posts_auto = (int)isset($params[ $posts_auto ]);
    $plp_options->pages_category = isset($params[ $pages_category ]) && is_numeric($params[ $pages_category ]) ? (int) $params[ $pages_category ] : '';
    $plp_options->posts_category = isset($params[ $posts_category ]) && is_numeric($params[ $posts_category ]) ? (int) $params[ $posts_category ] : '';

    // This won't be set if the user has no public CPTs in place
    $plp_options->autocreate = array();

    if (isset($params[$autocreate]) && is_array($params[$autocreate])) {
      foreach ($params[$autocreate] as $post_type => $fields) {
        $post_type = sanitize_key($post_type);

        if ($post_type) {
          $plp_options->autocreate[$post_type] = array(
            'enabled' => isset($fields['enabled']),
            'category' => isset($fields['category']) && is_numeric($fields['category']) ? (int) $fields['category'] : '',
            'socbtns' => isset($fields['socbtns'])
          );
        }
      }
    }

    //$plp_options->social_buttons = $params[ $social_buttons ];
    $new_social_buttons = $plp_options->default_social_buttons;
    foreach( $new_social_buttons as $btn_name => $btn ) {
      $new_social_buttons[$btn_name]['checked']=false;
    }

    if(isset($params[$social_buttons]) && is_array($params[$social_buttons])) {
      foreach( array_reverse( $params[ $social_buttons ] ) as $btn_name => $btn_checked ) {
        if (isset($new_social_buttons[$btn_name])) {
          $btn = $new_social_buttons[$btn_name];
          $btn['checked'] = true;
          unset($new_social_buttons[$btn_name]);
          array_unshift($new_social_buttons, $btn);
        }
      }
    }

    $plp_options->social_buttons = array_values($new_social_buttons);
    $plp_options->social_buttons_placement = isset($params[ $social_buttons_placement ]) && is_string($params[ $social_buttons_placement ]) ? sanitize_key(stripslashes($params[ $social_buttons_placement ])) : '';
    $plp_options->social_buttons_show_in_feed = (int)isset($params[ $social_buttons_show_in_feed ]);
    //$plp_options->social_buttons_padding = $params[ $social_buttons_padding ];
    $plp_options->social_posts_buttons = (int)isset($params[ $social_posts_buttons ]);
    $plp_options->social_pages_buttons = (int)isset($params[ $social_pages_buttons ]);
    $plp_options->keyword_replacement_is_on = (int)isset($params[ $keyword_replacement_is_on ]);
    $plp_options->keyword_links_open_new_window = (int)isset($params[$keyword_links_open_new_window]);
    $plp_options->keyword_links_nofollow = (int)isset($params[$keyword_links_nofollow]);
    $plp_options->keyword_links_sponsored = (int)isset($params[$keyword_links_sponsored]);
    $plp_options->url_replacement_is_on = (int)isset($params[$url_replacement_is_on]);
    $plp_options->replace_urls_with_pretty_links = (int)isset($params[ $replace_urls_with_pretty_links ]);
    $plp_options->url_links_open_new_window = (int)isset($params[$url_links_open_new_window]);

    $plp_options->replace_urls_with_pretty_links_blacklist = isset($params[ $replace_urls_with_pretty_links_blacklist ]) && is_string($params[ $replace_urls_with_pretty_links_blacklist ]) ? sanitize_textarea_field(stripslashes($params[ $replace_urls_with_pretty_links_blacklist ])) : '';
    $this->filter_domain_blacklist(); //Get rid of user entered garbage to make sure out input is valid

    $plp_options->url_replacement_cpts = isset($params[$url_replacement_cpts]) && is_array($params[$url_replacement_cpts]) ? array_values($params[$url_replacement_cpts]) : array('post');

    $plp_options->replace_keywords_in_comments = isset($params[$replace_keywords_in_comments]) && is_string($params[$replace_keywords_in_comments]) ? sanitize_key(stripslashes($params[$replace_keywords_in_comments])) : 'both';
    $plp_options->replace_keywords_in_feeds = isset($params[$replace_keywords_in_feeds]) && is_string($params[$replace_keywords_in_feeds]) ? sanitize_key(stripslashes($params[$replace_keywords_in_feeds])) : 'both';
    $plp_options->enable_link_to_disclosures = (int)isset($params[ $enable_link_to_disclosures ]);
    $plp_options->disclosures_link_url = isset($params[ $disclosures_link_url ]) && is_string($params[ $disclosures_link_url ]) ? esc_url_raw(trim(stripslashes($params[ $disclosures_link_url ]))) : '';
    $plp_options->disclosures_link_text = isset($params[ $disclosures_link_text ]) && is_string($params[ $disclosures_link_text ]) ? sanitize_text_field(stripslashes($params[ $disclosures_link_text ])) : '';
    $plp_options->disclosures_link_position = isset($params[ $disclosures_link_position ]) && is_string($params[ $disclosures_link_position ]) ? sanitize_key(stripslashes($params[ $disclosures_link_position ])) : '';
    $plp_options->enable_keyword_link_disclosures = (int)isset($params[$enable_keyword_link_disclosures]);
    $plp_options->keyword_link_disclosure = isset($params[$keyword_link_disclosure]) && is_string($params[$keyword_link_disclosure]) ? sanitize_text_field(stripslashes($params[$keyword_link_disclosure])): '';
    $plp_options->keyword_replacement_cpts = isset($params[$keyword_replacement_cpts]) && is_array($params[$keyword_replacement_cpts]) ? array_values($params[$keyword_replacement_cpts]) : array('post');
    $plp_options->set_keyword_thresholds = (int)isset($params[ $set_keyword_thresholds ]);
    $plp_options->keywords_per_page = isset($params[ $keywords_per_page ]) && is_numeric($params[ $keywords_per_page ]) ? max((int) $params[ $keywords_per_page ], 0) : 3;
    $plp_options->keyword_links_per_page = isset($params[ $keyword_links_per_page ]) && is_numeric($params[ $keyword_links_per_page ]) ? max((int) $params[ $keyword_links_per_page ], 0) : 2;
    $plp_options->use_prettylink_url = (int)isset($params[ $use_prettylink_url ]);
    $plp_options->prettylink_url = isset($params[ $prettylink_url ]) && is_string($params[ $prettylink_url ]) ? esc_url_raw(trim(stripslashes($params[ $prettylink_url ]))) : '';
    $plp_options->min_role = isset($params[ $minimum_access_role ]) && is_string($params[ $minimum_access_role ]) ? sanitize_key(stripslashes($params[ $minimum_access_role ])) : 'manage_options';
    $plp_options->allow_public_link_creation = (int)isset($params[ $allow_public_link_creation ]);
    $plp_options->use_public_link_display_page = (int)isset($params[ $use_public_link_display_page ]);
    $plp_options->public_link_display_page = isset($params[ $public_link_display_page ]) && is_string($params[ $public_link_display_page ]) ? esc_url_raw(trim(stripslashes($params[ $public_link_display_page ]))) : '';

    if(get_option('prlipro_prettybar_active')) {
      $prli_options->prettybar_image_url = isset($_POST[ $prettybar_image_url ]) && is_string($_POST[ $prettybar_image_url ]) ? esc_url_raw(trim(stripslashes($_POST[ $prettybar_image_url ]))) : '';
      $prli_options->prettybar_background_image_url = isset($_POST[ $prettybar_background_image_url ]) && is_string($_POST[ $prettybar_background_image_url ]) ? esc_url_raw(trim(stripslashes($_POST[ $prettybar_background_image_url ]))) : '';
      $prli_options->prettybar_color = isset($_POST[ $prettybar_color ]) && is_string($_POST[ $prettybar_color ]) ? sanitize_hex_color(stripslashes($_POST[ $prettybar_color ])) : '';
      $prli_options->prettybar_text_color = isset($_POST[ $prettybar_text_color ]) && is_string($_POST[ $prettybar_text_color ]) ? sanitize_hex_color(stripslashes($_POST[ $prettybar_text_color ])) : '';
      $prli_options->prettybar_link_color = isset($_POST[ $prettybar_link_color ]) && is_string($_POST[ $prettybar_link_color ]) ? sanitize_hex_color(stripslashes($_POST[ $prettybar_link_color ])) : '';
      $prli_options->prettybar_hover_color = isset($_POST[ $prettybar_hover_color ]) && is_string($_POST[ $prettybar_hover_color ]) ? sanitize_hex_color(stripslashes($_POST[ $prettybar_hover_color ])) : '';
      $prli_options->prettybar_visited_color = isset($_POST[ $prettybar_visited_color ]) && is_string($_POST[ $prettybar_visited_color ]) ? sanitize_hex_color(stripslashes($_POST[ $prettybar_visited_color ])) : '';
      $prli_options->prettybar_show_title = (int)isset($_POST[ $prettybar_show_title ]);
      $prli_options->prettybar_show_description = (int)isset($_POST[ $prettybar_show_description ]);
      $prli_options->prettybar_show_share_links = (int)isset($_POST[ $prettybar_show_share_links ]);
      $prli_options->prettybar_show_target_url_link = (int)isset($_POST[ $prettybar_show_target_url_link ]);
      $prli_options->prettybar_title_limit = isset($_POST[ $prettybar_title_limit ]) && is_numeric($_POST[ $prettybar_title_limit ]) ? (int) $_POST[ $prettybar_title_limit ] : 25;
      $prli_options->prettybar_desc_limit = isset($_POST[ $prettybar_desc_limit ]) && is_numeric($_POST[ $prettybar_desc_limit ]) ? (int) $_POST[ $prettybar_desc_limit ] : 30;
      $prli_options->prettybar_link_limit = isset($_POST[ $prettybar_link_limit ]) && is_numeric($_POST[ $prettybar_link_limit ]) ? (int) $_POST[ $prettybar_link_limit ] : 30;
      $plp_options->prettybar_hide_attrib_link = (int)isset($params[ $prettybar_hide_attrib_link ]);
      $plp_options->prettybar_attrib_url = isset($params[ $prettybar_attrib_url ]) && is_string($params[ $prettybar_attrib_url ]) ? esc_url_raw(trim(stripslashes($params[ $prettybar_attrib_url ]))) : '';
    }

    // QR Logo settings
    $plp_options->qr_logo_attachment_id = isset($params[$qr_logo_attachment_id]) && is_numeric($params[$qr_logo_attachment_id]) ? absint($params[$qr_logo_attachment_id]) : 0;
    $plp_options->qr_logo_size_percent = isset($params[$qr_logo_size_percent]) && is_numeric($params[$qr_logo_size_percent]) ? max(10, min(30, (int)$params[$qr_logo_size_percent])) : 20;
    $plp_options->qr_logo_white_background = (int)isset($params[$qr_logo_white_background]);

    update_option('plp_index_keywords', isset($params['plp_index_keywords']));
    wp_cache_delete('alloptions', 'options');
  }

  //Filters the user entered garbage to make sure these are valid domains
  public function filter_domain_blacklist() {
    global $plp_options;

    $new_ops = '';
    $lines = preg_split('/[\r\n]+/', $plp_options->replace_urls_with_pretty_links_blacklist, -1, PREG_SPLIT_NO_EMPTY);

    if(!empty($lines)) {
      foreach($lines as $line) {
        if($domain = parse_url($line, PHP_URL_HOST)) {
          $scheme = parse_url($line, PHP_URL_SCHEME) . "://";
          $new_ops .= $scheme.$domain."\n";
        }
      }
    }

    $plp_options->replace_urls_with_pretty_links_blacklist = $new_ops;
  }

  public function store() {
    global $plp_options;

    // Save the posted value in the database
    $plp_options->store();
  }
} //End class
