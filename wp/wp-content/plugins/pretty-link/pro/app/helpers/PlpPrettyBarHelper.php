<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

// PrettyBar stuff here of course
class PlpPrettyBarHelper {
  public static function render_prettybar($slug) {
    global $prli_blogurl, $prli_link, $prli_options, $prli_blogname, $prli_blogdescription, $target_url;

    if($link = $prli_link->getOneFromSlug( $slug )) {
      $bar_image = $prli_options->prettybar_image_url;
      $bar_background_image = $prli_options->prettybar_background_image_url;
      $bar_color = $prli_options->prettybar_color;
      $bar_text_color = $prli_options->prettybar_text_color;
      $bar_link_color = $prli_options->prettybar_link_color;
      $bar_visited_color = $prli_options->prettybar_visited_color;
      $bar_hover_color = $prli_options->prettybar_hover_color;
      $bar_show_title = $prli_options->prettybar_show_title;
      $bar_show_description = $prli_options->prettybar_show_description;
      $bar_show_share_links = $prli_options->prettybar_show_share_links;
      $bar_show_target_url_link = $prli_options->prettybar_show_target_url_link;
      $bar_title_limit = (int)$prli_options->prettybar_title_limit;
      $bar_desc_limit = (int)$prli_options->prettybar_desc_limit;
      $bar_link_limit = (int)$prli_options->prettybar_link_limit;

      $target_url = $link->url;

      $shortened_title = stripslashes(substr($prli_blogname,0,$bar_title_limit));
      $shortened_desc  = stripslashes(substr($prli_blogdescription,0,$bar_desc_limit));
      $shortened_link  = stripslashes(substr($target_url,0,$bar_link_limit));

      if(strlen($prli_blogname) > $bar_title_limit) {
        $shortened_title .= "...";
      }

      if(strlen($prli_blogdescription) > $bar_desc_limit) {
        $shortened_desc .= "...";
      }

      if(strlen($target_url) > $bar_link_limit) {
        $shortened_link .= "...";
      }

      wp_register_style('fontello-animation', PRLI_VENDOR_LIB_URL.'/fontello/css/animation.css', array(), PRLI_VERSION);
      wp_register_style('fontello-pretty-link', PRLI_VENDOR_LIB_URL.'/fontello/css/pretty-link.css', array(), PRLI_VERSION);
      wp_register_style('prli-prettybar', PLP_CSS_URL.'/prettybar.css', array(), PRLI_VERSION);

      $css = '';

      if(!empty($bar_background_image) && $bar_background_image) {
        $css .= sprintf('html, body { background-image: url(%s); background-repeat: repeat-x; }', esc_url($bar_background_image));
      } else {
        $css .= sprintf('html, body { background-color: %s; }', esc_html($bar_color));
      }

      $css .= sprintf('html, body { color: %s; }', esc_html($bar_text_color));
      $css .= sprintf('a { color: %s; }', esc_html($bar_link_color));
      $css .= sprintf('a:visited { color: %s; }', esc_html($bar_visited_color));
      $css .= sprintf('a:hover { color: %s; }', esc_html($bar_hover_color));

      wp_add_inline_style('prli-prettybar', $css);

      require(PLP_VIEWS_PATH . '/links/prettybar.php');
    }
  }
}

