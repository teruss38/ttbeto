<tr valign="top">
  <th scope="row">
    <label for="<?php echo esc_attr($plp_options->base_slug_prefix_str); ?>"><?php esc_html_e('Base Slug Prefix', 'pretty-link'); ?></label>
    <?php PrliAppHelper::info_tooltip('prli-base-slug-prefix',
                                      esc_html__('Base Slug Prefix', 'pretty-link'),
                                      sprintf(
                                        // translators: %1$s: open b tag, %2$s close b tag
                                        esc_html__('Use this to prefix all newly generated pretty links with a directory of your choice. For example set to %1$sout%2$s to make your pretty links look like http://site.com/%1$sout%2$s/xyz. Changing this option will NOT affect existing pretty links. If you do not wish to use a directory prefix, leave this text field blank. Whatever you type here will be sanitized and modified to ensure it is URL-safe. So %1$sHello World%2$s might get changed to something like %1$shello-world%2$s instead. Lowercase letters, numbers, dashes, and underscores are allowed.', 'pretty-link'),
                                        '<b>',
                                        '</b>'
                                      ));
    ?>
  </th>
  <td>
    <input type="text" name="<?php echo esc_attr($plp_options->base_slug_prefix_str); ?>" class="regular-text" value="<?php echo esc_attr(stripslashes($plp_options->base_slug_prefix)); ?>" />
  </td>
</tr>

<tr valign="top">
  <th scope="row">
    <label for="<?php echo esc_attr($plp_options->num_slug_chars_str); ?>"><?php esc_html_e('Slug Character Count', 'pretty-link'); ?></label>
    <?php PrliAppHelper::info_tooltip('prli-num-slug-chars',
                                      esc_html__('Slug Character Count', 'pretty-link'),
                                      esc_html__("The number of characters to use when auto-generating a random slug for pretty links. The default is 4. You cannot use less than 2.", 'pretty-link'));
    ?>
  </th>
  <td>
    <input type="number" min="2" name="<?php echo esc_attr($plp_options->num_slug_chars_str); ?>" value="<?php echo esc_attr(stripslashes($plp_options->num_slug_chars)); ?>" />
  </td>
</tr>

<?php /*<tr valign="top">
  <th scope="row">
    <label for="<?php echo esc_attr($plp_options->google_tracking_str); ?>"><?php esc_html_e('Enable Google Analytics', 'pretty-link') ?></label>
    <?php PrliAppHelper::info_tooltip('prli-options-use-ga', esc_html__('Enable Google Analytics', 'pretty-link'),
                                      esc_html__("Requires Google Analyticator, Google Analytics by MonsterInsights (formerly Yoast), or the Google Analytics Plugin to be installed and configured on your site.", 'pretty-link'));
    ?>
  </th>
  <td>
    <input type="checkbox" name="<?php echo esc_attr($plp_options->google_tracking_str); ?>" id="<?php echo esc_attr($plp_options->google_tracking_str); ?>" <?php checked($plp_options->google_tracking); ?>/>
  </td>
</tr> */ ?>

<tr valign="top">
  <th scope="row">
    <label for="<?php echo esc_attr($plp_options->generate_qr_codes_str); ?>">
      <?php
        printf(
          // translators: %1s: open link tag, %2$s: close link tag
          esc_html__('Enable %1sQR Codes%2$s', 'pretty-link'),
          '<a href="http://en.wikipedia.org/wiki/QR_code">',
          '</a>'
        );
      ?>
    </label>
    <?php PrliAppHelper::info_tooltip('prli-options-generate-qr-codes',
                                      esc_html__('Generate QR Codes', 'pretty-link'),
                                      esc_html__("This will enable a link in your pretty link admin that will allow you to automatically download a QR Code for each individual Pretty Link.", 'pretty-link'));
    ?>
  </th>
  <td>
    <input class="prli-toggle-checkbox" type="checkbox" data-box="pretty-link-qr-logo-options" name="<?php echo esc_attr($plp_options->generate_qr_codes_str); ?>" id="<?php echo esc_attr($plp_options->generate_qr_codes_str); ?>" <?php checked($plp_options->generate_qr_codes); ?>/>
  </td>
</tr>
</table>

<div class="prli-sub-box pretty-link-qr-logo-options">
  <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($plp_options->qr_logo_attachment_id_str); ?>">
            <?php esc_html_e('QR Code Logo', 'pretty-link'); ?>
          </label>
          <?php PrliAppHelper::info_tooltip('prli-qr-logo',
                                            esc_html__('QR Code Logo Overlay', 'pretty-link'),
                                            esc_html__('Upload or select a logo to display in the center of your QR codes. The logo will be overlaid at 15-30% of the QR code size with high error correction to maintain scannability. Leave empty for no logo.', 'pretty-link'));
          ?>
        </th>
        <td>
          <input type="hidden"
                 name="<?php echo esc_attr($plp_options->qr_logo_attachment_id_str); ?>"
                 id="<?php echo esc_attr($plp_options->qr_logo_attachment_id_str); ?>"
                 value="<?php echo esc_attr($plp_options->qr_logo_attachment_id); ?>" />

          <button type="button" class="button prli-qr-logo-upload" id="prli-qr-logo-upload-btn">
            <?php echo $plp_options->qr_logo_attachment_id > 0 ? esc_html__('Change Logo', 'pretty-link') : esc_html__('Select Logo', 'pretty-link'); ?>
          </button>

          <button type="button" class="button prli-qr-logo-remove" id="prli-qr-logo-remove-btn" <?php echo $plp_options->qr_logo_attachment_id > 0 ? '' : 'style="display:none;"'; ?>>
            <?php esc_html_e('Remove Logo', 'pretty-link'); ?>
          </button>

          <div id="prli-qr-logo-preview" style="margin-top: 10px;">
            <?php if($plp_options->qr_logo_attachment_id > 0 && wp_attachment_is_image($plp_options->qr_logo_attachment_id)):
              $image_url = wp_get_attachment_image_url($plp_options->qr_logo_attachment_id, 'thumbnail');
              if($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width: 150px; height: auto; border: 1px solid #ddd; padding: 5px;" />
              <?php endif;
            endif; ?>
          </div>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($plp_options->qr_logo_size_percent_str); ?>">
            <?php esc_html_e('Logo Size', 'pretty-link'); ?>
          </label>
          <?php PrliAppHelper::info_tooltip('prli-qr-logo-size',
                                            esc_html__('QR Code Logo Size', 'pretty-link'),
                                            esc_html__('Percentage of QR code width for the logo (10-30%). Recommended: 15-20% for best balance between visibility and scannability.', 'pretty-link'));
          ?>
        </th>
        <td>
          <input type="number"
                 min="10"
                 max="30"
                 name="<?php echo esc_attr($plp_options->qr_logo_size_percent_str); ?>"
                 id="<?php echo esc_attr($plp_options->qr_logo_size_percent_str); ?>"
                 value="<?php echo esc_attr($plp_options->qr_logo_size_percent); ?>"
                 style="width: 80px;" />
          <span class="description">%</span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($plp_options->qr_logo_white_background_str); ?>">
            <?php esc_html_e('White Background', 'pretty-link'); ?>
          </label>
          <?php PrliAppHelper::info_tooltip('prli-qr-logo-white-bg',
                                            esc_html__('Add White Background Behind Logo', 'pretty-link'),
                                            esc_html__('Adds a white rectangular "safe area" behind the logo for better contrast and improved scannability. Recommended to keep enabled.', 'pretty-link'));
          ?>
        </th>
        <td>
          <input type="checkbox"
                 name="<?php echo esc_attr($plp_options->qr_logo_white_background_str); ?>"
                 id="<?php echo esc_attr($plp_options->qr_logo_white_background_str); ?>"
                 <?php checked($plp_options->qr_logo_white_background != 0); ?>/>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<table class="form-table">
<tr valign="top">
  <th scope="row">
  <label for="<?php echo esc_attr($plp_options->enable_link_health_str); ?>"><?php esc_html_e('Enable Link Health', 'pretty-link'); ?></label>
    <?php PrliAppHelper::info_tooltip('prli-options-enable-link-health',
                                      esc_html__('Enable Link Health', 'pretty-link'),
                                      esc_html__('Enable this option to be notified when your links are broken.', 'pretty-link'));
    ?>
  </th>
  <td>
    <input class="prli-toggle-checkbox" type="checkbox" data-box="pretty-link-health-options" name="<?php echo esc_attr($plp_options->enable_link_health_str); ?>" id="<?php echo esc_attr($plp_options->enable_link_health_str); ?>" <?php checked($plp_options->enable_link_health); ?>/>
  </td>
</tr>
</table>

<div class="prli-sub-box pretty-link-health-options">
  <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($plp_options->enable_link_health_emails_str); ?>"><?php esc_html_e('Enable Link Health Emails', 'pretty-link'); ?></label>
            <?php PrliAppHelper::info_tooltip('prli-options-enable-link-health-emails',
                                              esc_html__('Enable Link Health Emails', 'pretty-link'),
                                              esc_html__('Enable this option to be notified of broken links through email.', 'pretty-link'));
            ?>
        </th>
        <td>
          <input class="prli-toggle-checkbox" type="checkbox" data-box="prli-link-health-emails" name="<?php echo esc_attr($plp_options->enable_link_health_emails_str); ?>" id="<?php echo esc_attr($plp_options->enable_link_health_emails_str); ?>" <?php checked($plp_options->enable_link_health_emails); ?>/>
        </td>
      </tr>
    </tbody>
  </table>

  <div class="prli-sub-box-white prli-link-health-emails">
    <div class="prli-arrow prli-white prli-up prli-sub-box-arrow"> </div>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row" class="prlipro-link-health-emails">
            <label for="<?php echo esc_attr($plp_options->link_health_emails_str); ?>"><?php esc_html_e('Link Health Email Addresses', 'pretty-link'); ?></label>
              <?php PrliAppHelper::info_tooltip('prli-options-link-health-emails',
                                              esc_html__('Link Health Email Addresses', 'pretty-link'),
                                              esc_html__('Comma separated list of email addresses that will receive notifications for broken links. This defaults to your admin email set in "Settings" -> "General" -> "Administration Email Address"', 'pretty-link'));
              ?>
          </th>
          <td>
            <input class="regular-text" type="text" name="<?php echo esc_attr($plp_options->link_health_emails_str); ?>" id="<?php echo esc_attr($plp_options->link_health_emails_str); ?>" value="<?php echo stripslashes($plp_options->link_health_emails); ?>"/>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<table class="form-table">
<tr valign="top">
  <th scope="row">
    <label for="<?php echo esc_attr($plp_options->global_head_scripts_str); ?>"><?php esc_html_e('Global Head Scripts', 'pretty-link'); ?></label>
    <?php PrliAppHelper::info_tooltip('prli-options-global-head-scripts',
                                      esc_html__('Global Head Scripts', 'pretty-link'),
                                      sprintf(
                                        // translators: %1$s: br tag, %2$s: open b tag, %3$s close b tag
                                        esc_html__('Useful for adding Google Analytics tracking, Facebook retargeting pixels, or any other kind of tracking script to the HTML head.%1$s%1$sWhat you enter in this box will be applied to all supported pretty links.%1$s%1$s%2$sNOTE:%3$s This does NOT work with 301, 302 and 307 type redirects.', 'pretty-link'),
                                        '<br>',
                                        '<b>',
                                        '</b>'
                                      ));
    ?>
  </th>
  <td>
    <textarea name="<?php echo esc_attr($plp_options->global_head_scripts_str); ?>" id="<?php echo esc_attr($plp_options->global_head_scripts_str); ?>" class="large-text"><?php echo esc_textarea(stripslashes($plp_options->global_head_scripts)); ?></textarea>
  </td>
</tr>

