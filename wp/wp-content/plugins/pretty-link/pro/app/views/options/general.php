<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo esc_attr($use_prettylink_url); ?>">
          <a href="https://prettylinks.com/plp/options/um/shortlink-url"><?php esc_html_e('Use Shortlink URL', 'pretty-link'); ?></a>
          <?php PrliAppHelper::info_tooltip('prli-use-shortlink-url',
                                            esc_html__('Use an Alternate Shortlink URL', 'pretty-link'),
                                            esc_html__('Use this option if you want to substitute your actual blog\'s url with another URL. You must have another valid domain name pointing to this WordPress install before you enable this option. If you are using this option to just get rid of the www in the beginning of your url that is fine -- just make sure your domain works without the www before enabling this option.', 'pretty-link'));
          ?>
        </label>
      </th>
      <td>
        <input class="prli-toggle-checkbox" data-box="prettylink-url" type="checkbox" name="<?php echo esc_attr($use_prettylink_url); ?>" <?php checked($plp_options->use_prettylink_url != 0); ?>/>
      </td>
    </tr>
  </tbody>
</table>

<div class="prli-sub-box prettylink-url">
  <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($prettylink_url); ?>"><?php esc_html_e('Shortlink URL', 'pretty-link'); ?>
          <?php PrliAppHelper::info_tooltip('prli-shortlink-url',
                                            esc_html__('Shortlink URL', 'pretty-link'),
                                            esc_html__('Enter a valid base url that points at this WordPress install. Make sure this URL does not end with a slash (/).', 'pretty-link'));
          ?>
        </th>
        <td>
          <input type="text" class="regular-text" name="<?php echo esc_attr($prettylink_url); ?>" value="<?php echo esc_attr($plp_options->prettylink_url); ?>" />
        </td>
      </tr>
    </tbody>
  </table>
</div>

<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo esc_attr($minimum_access_role); ?>">
          <?php esc_html_e('Minimum Admin Role', 'pretty-link'); ?>
          <?php PrliAppHelper::info_tooltip('prli-use-shortlink-url',
                                            esc_html__('Set Minimum Role Required To Access Pretty Link', 'pretty-link'),
                                            esc_html__('Use this option to set the minimum role of users who can access the Admin interface for Pretty Link.', 'pretty-link'));
          ?>
        </label>
      </th>
      <td>
        <select name="<?php echo esc_attr($minimum_access_role); ?>">
          <option value="manage_options" <?php selected($plp_options->min_role, 'manage_options'); ?>><?php esc_html_e('Administrator', 'pretty-link'); ?></option>
          <option value="delete_pages" <?php selected($plp_options->min_role, 'delete_pages'); ?>><?php esc_html_e('Editor', 'pretty-link'); ?></option>
          <option value="publish_posts" <?php selected($plp_options->min_role, 'publish_posts'); ?>><?php esc_html_e('Author', 'pretty-link'); ?></option>
          <option value="edit_posts" <?php selected($plp_options->min_role, 'edit_posts'); ?>><?php esc_html_e('Contributor', 'pretty-link'); ?></option>
        </select>
      </td>
    </tr>
  </tbody>
</table>

