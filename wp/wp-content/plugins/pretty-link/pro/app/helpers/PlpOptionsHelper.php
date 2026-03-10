<?php if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

class PlpOptionsHelper {
  public static function autocreate_post_options($post_type, $option, $category, $socbtns) {
    // For reverse-compatibility
    if($post_type=='post' || $post_type=='page') {
      $option_name  = "prli_{$post_type}s_auto";
      $category_name   = "prli_{$post_type}s_category";
      $socbtns_name = "prli_social_{$post_type}s_buttons";
    }
    else {
      $option_name  = "prli_autocreate[{$post_type}][enabled]";
      $category_name   = "prli_autocreate[{$post_type}][category]";
      $socbtns_name = "prli_autocreate[{$post_type}][socbtns]";
    }

    $p = get_post_type_object($post_type);

    require(PLP_VIEWS_PATH . '/options/autocreate.php');
  }

  public static function autocreate_all_cpt_options() {
    global $plp_options;

    $post_types = $plp_options->get_post_types(false);

    foreach($post_types as $post_type) {
      $option  = !empty($plp_options->autocreate[$post_type]['enabled']);
      $category   = !empty($plp_options->autocreate[$post_type]['category']) ? $plp_options->autocreate[$post_type]['category'] : '';
      $socbtns = !empty($plp_options->autocreate[$post_type]['socbtns']);

      self::autocreate_post_options($post_type, $option, $category, $socbtns);
    }
  }

  /**
   * Renders the dropdown field used for various options on the Replacements tab.
   *
   * @access public
   * @param string $option_name The name of the option to use in the field.
   * @return void
   */
  public static function render_replacements_dropdown($option_name) {
    global $plp_options;

    $option_prop_name = str_replace('prli_', '', $option_name);

    ?>
    <select name="<?php echo esc_attr($option_name); ?>">
      <option value="none" <?php selected($plp_options->{$option_prop_name}, 'none'); ?>><?php esc_html_e('None', 'pretty-link'); ?></option>
      <option value="keywords" <?php selected($plp_options->{$option_prop_name}, 'keywords'); ?>><?php esc_html_e('Keywords', 'pretty-link'); ?></option>
      <option value="urls" <?php selected($plp_options->{$option_prop_name}, 'urls'); ?>><?php esc_html_e('URL\'s', 'pretty-link'); ?></option>
      <option value="both" <?php selected($plp_options->{$option_prop_name}, 'both'); ?>><?php esc_html_e('Both', 'pretty-link'); ?></option>
    </select>
    <?php
  }

  /**
   * Renders the multi-select chip field used for enabling/disabling keyword and URL replacements per CPT.
   *
   * @access public
   * @param string $option_name The name of the option to use in the field.
   * @return void
   */
  public static function render_cpt_chip_field($option_name) {
    global $plp_options;

    $option_prop_name = str_replace('prli_', '', $option_name);
    $post_types = get_post_types(array('public' => true), 'objects', 'and');

    unset($post_types['attachment']); // We don't want replacements for attachments.

    ?>
    <?php foreach($post_types as $post_type): ?>
      <div class="prlipro-chip <?php echo in_array($post_type->name, $plp_options->{$option_prop_name}) ? 'selected' : ''; ?>" data-title="<?php echo esc_attr($post_type->name); ?>">
        <input type="checkbox" name="<?php echo esc_attr($option_name); ?>[]" value="<?php echo esc_attr($post_type->name); ?>" <?php checked(in_array($post_type->name, $plp_options->{$option_prop_name})); ?>>
        <label><?php echo esc_html($post_type->label); ?></label>
      </div>
    <?php endforeach;
  }

  /**
   * Retrieves the site URL in its different variants (http, https, www, and non-www).
   *
   * @access public
   * @return string
   */
  public static function get_site_url_variants() {
    $url = get_site_url();
    $variants = array();
    $trimmed_url = preg_replace('/^(https?:\/\/)?(www\.)?/i', '', $url);

    $variants[] = 'http://' . $trimmed_url;
    $variants[] = 'https://' . $trimmed_url;
    $variants[] = 'http://www.' . $trimmed_url;
    $variants[] = 'https://www.' . $trimmed_url;

    return implode("\n", $variants);
  }
}

