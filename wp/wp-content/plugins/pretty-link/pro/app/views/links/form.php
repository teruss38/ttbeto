<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<?php if(!$prettypay_link) : ?>
  <table class="form-table">
    <tr>
      <th scope="row">
        <?php esc_html_e('Expire', 'pretty-link'); ?>
        <?php PrliAppHelper::info_tooltip(
                'plp-expire',
                esc_html__('Expire Link', 'pretty-link'),
                esc_html__('Set this link to expire after a specific date or number of clicks.', 'pretty-link')
              ); ?>
      </th>
      <td>
        <input class="prli-toggle-checkbox" data-box="plp-expire" type="checkbox" name="enable_expire" <?php checked($enable_expire != 0); ?> />
      </td>
    </tr>
  </table>
  <div class="prli-sub-box plp-expire">
    <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
    <table class="form-table">
        <tr>
          <th scope="row">
            <?php esc_html_e('Expire After', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'plp-expire-type',
                    esc_html__('Expiration Type', 'pretty-link'),
                    sprintf(
                      // translators: %1$s: br tag, %2$s: open b tag, %3$s close b tag
                      esc_html__('Select the type of expiration you want for this link.%1$s%1$s%2$sDate%3$s Select this option if you\'d like to expire your link after a certain date.%1%s%1$s%2$sClicks%3$s: Select this option to expire this link after it has been clicked a specific number of times.', 'pretty-link'),
                      '<br>',
                      '<b>',
                      '</b>'
                    )
                  ); ?>
          </th>
          <td>
            <select id="plp_expire_type" name="expire_type" class="prli-toggle-select" data-date-box="plp-date-expire" data-clicks-box="plp-clicks-expire">
              <option value="date" <?php selected($expire_type, 'date'); ?>><?php esc_html_e('Date', 'pretty-link'); ?></option>
              <option value="clicks" <?php selected($expire_type, 'clicks'); ?>><?php esc_html_e('Clicks', 'pretty-link'); ?></option>
            </select>
          </td>
        </tr>
    </table>
    <div class="prli-sub-box-white plp-clicks-expire">
      <div class="prli-arrow prli-white prli-up prli-sub-box-arrow"> </div>
      <table class="form-table">
          <tr>
            <th scope="row">
              <?php esc_html_e('Clicks', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                      'plp-clicks-expire',
                      esc_html__('Number of Clicks', 'pretty-link'),
                      sprintf(
                        // translators: %1$s: br tag, %2$s: open b tag, %3$s close b tag
                        esc_html__('Enter the number of times this link can be clicked before it expires.%1$s%1$s%2$sNote: Expirations based on clicks wouldn\'t work properly if you had tracking turned off for this link so as long as this is set to Clicks, Pretty Link will ensure tracking is turned on for this link as well.%3$s', 'pretty-link'),
                        '<br>',
                        '<b>',
                        '</b>'
                      )
                    ); ?>
            </th>
            <td>
              <input type="number" name="expire_clicks" class="small-text" value="<?php echo esc_attr($expire_clicks); ?>" />
            </td>
          </tr>
      </table>
    </div>
    <div class="prli-sub-box-white plp-date-expire">
      <div class="prli-arrow prli-white prli-up prli-sub-box-arrow"> </div>
      <table class="form-table">
          <tr>
            <th scope="row">
              <?php esc_html_e('Date', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                      'plp-expire-date',
                      esc_html__('Expiration Date', 'pretty-link'),
                      esc_html__('Enter a date here in the format YYYY-MM-DD to set when this link should expire.', 'pretty-link')
                    ); ?>
            </th>
            <td>
              <input type="text" class="prli-date-picker regular-text" name="expire_date" value="<?php echo esc_attr($expire_date); ?>" />
            </td>
          </tr>
      </table>
    </div>


    <table class="form-table">
        <tr>
          <th scope="row">
            <?php esc_html_e('Expired Redirect', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'plp-enable-expired-url',
                    esc_html__('Redirect to URL when Expired', 'pretty-link'),
                    sprintf(
                      // translators: %1$s: br tag, %2$s: open b tag, %3$s close b tag
                      esc_html__('When this link expires, do you want to redirect to a specific URL. You can use this to redirect to a page you\'ve setup to indicate that the link is expired.%1$s%1$s%2$sNote: If this is not set the link will throw a 404 error when expired%3$s.', 'pretty-link'),
                      '<br>',
                      '<b>',
                      '</b>'
                    )
                  ); ?>
          </th>
          <td>
            <input class="prli-toggle-checkbox" data-box="plp-expired-url" type="checkbox" name="enable_expired_url" <?php checked($enable_expired_url != 0); ?> />
          </td>
        </tr>
    </table>
    <div class="prli-sub-box-white plp-expired-url">
      <div class="prli-arrow prli-white prli-up prli-sub-box-arrow"> </div>
      <table class="form-table">
          <tr>
            <th scope="row">
              <?php esc_html_e('URL', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                      'plp-expired-url',
                      esc_html__('Expired URL', 'pretty-link'),
                      esc_html__('This is the URL that this link will redirect to after the expiration date above.', 'pretty-link')
                    ); ?>
            </th>
            <td>
              <input type="text" name="expired_url" class="large-text" value="<?php echo esc_attr($expired_url); ?>" />
            </td>
          </tr>
      </table>
    </div>
  </div>
<?php endif; ?>

<?php if($plp_options->keyword_replacement_is_on || $plp_options->url_replacement_is_on): ?>
<table class="form-table">
    <?php if($plp_options->keyword_replacement_is_on): ?>
      <tr>
        <th scope="row">
          <?php esc_html_e('Keywords', 'pretty-link'); ?>
          <?php PrliAppHelper::info_tooltip(
                  'prli-link-pro-options-keywords',
                  esc_html__('Auto-Replace Keywords', 'pretty-link'),
                  esc_html__('Enter a comma separated list of keywords / keyword phrases that you\'d like to replace with this link in your Posts &amp; Pages.', 'pretty-link')); ?>
        </th>
        <td>
          <input type="text" name="keywords" class="large-text" value="<?php echo esc_attr($keywords); ?>" />
        </td>
      </tr>
    <?php endif; ?>
    <?php if($plp_options->url_replacement_is_on): ?>
      <tr>
        <th scope="row">
          <?php esc_html_e('URL Replacements', 'pretty-link'); ?>
          <?php PrliAppHelper::info_tooltip(
                  'prli-link-pro-options-url-replacements',
                  esc_html__('Auto-Replace URLs', 'pretty-link'),
                  sprintf(
                    // translators: %1$s: open code tag, %2$s: close code tag
                    esc_html__('Enter a comma separated list of the URLs that you\'d like to replace with this Pretty Link in your Posts &amp; Pages. These must be formatted as URLs for example: %1$shttp://example.com%2$s or %1$shttp://example.com?product_id=53%2$s', 'pretty-link'),
                    '<code>',
                    '</code>'
                  )
                ); ?>
        </th>
        <td>
          <input type="text" name="url_replacements" class="large-text" value="<?php echo esc_attr($url_replacements); ?>" />
        </td>
      </tr>
    <?php endif; ?>
</table>
<?php endif; ?>

<?php if(!$prettypay_link) : ?>
  <table class="form-table">
      <tr>
        <th scope="row">
          <?php esc_html_e('Head Scripts', 'pretty-link'); ?>
          <?php PrliAppHelper::info_tooltip(
                  'prli-link-pro-options-head-scripts',
                  esc_html__('Head Scripts', 'pretty-link'),
                  sprintf(
                    // translators: %1$s: br tag, %2$s: open b tag, %3$s close b tag
                    esc_html__('Useful for adding Google Analytics tracking, Facebook retargeting pixels, or any other kind of tracking script to the HTML head for this pretty link.%1$s%1$sThese scripts will be in addition to any global one\'s you\'ve defined in the options.%1$s%1$s%2$sNOTE:%3$s This does NOT work with 301, 302 and 307 type redirects.', 'pretty-link'),
                    '<br>',
                    '<b>',
                    '</b>'
                  )
                ); ?>
        </th>
        <td>
          <textarea name="head-scripts" class="large-text"><?php echo esc_textarea($head_scripts); ?></textarea>
        </td>
      </tr>
  </table>

  <table class="form-table">
      <tr>
        <th scope="row">
          <?php esc_html_e('Dynamic Redirection', 'pretty-link'); ?>
          <?php PrliAppHelper::info_tooltip(
                  'prli-link-pro-options-dynamic-redirection-options',
                  esc_html__('Dynamic Redirection Options', 'pretty-link'),
                  esc_html__('These powerful options are available to give you dynamic control over redirection for this pretty link.', 'pretty-link')
                ); ?>
        </th>
        <td>
          <select id="plp_dynamic_redirection" name="dynamic_redirection" class="prli-toggle-select" data-rotate-box="prli-link-rotate" data-geo-box="prli-link-geo" data-tech-box="prli-link-tech" data-time-box="prli-link-time">
            <option value="none" <?php selected($dynamic_redirection, 'none'); ?>><?php esc_html_e('None', 'pretty-link'); ?></option>
            <option value="rotate" <?php selected($dynamic_redirection, 'rotate'); ?>><?php esc_html_e('Rotation', 'pretty-link'); ?></option>
            <option value="geo" <?php selected($dynamic_redirection, 'geo'); ?>><?php esc_html_e('Geographic', 'pretty-link'); ?></option>
            <option value="tech" <?php selected($dynamic_redirection, 'tech'); ?>><?php esc_html_e('Technology', 'pretty-link'); ?></option>
            <option value="time" <?php selected($dynamic_redirection, 'time'); ?>><?php esc_html_e('Time', 'pretty-link'); ?></option>
          </select>
        </td>
      </tr>
  </table>

  <div class="prli-sub-box prli-link-rotate">
    <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
    <h3>
      <?php esc_html_e('Target URL Rotations', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-pro-target-url-rotations',
              esc_html__('Target URL Rotations', 'pretty-link'),
              sprintf(
                // translators: %1$s: open code tag, %2$s: close code tag
                esc_html__('Enter the Target URLs that you\'d like to rotate through when this Pretty Link is Clicked. These must be formatted as URLs example: %1$shttp://example.com%2$s or %1$shttp://example.com?product_id=53%2$s', 'pretty-link'),
                '<code>',
                '</code>'
              )
            ); ?>
    </h3>
    <ol id="prli_link_rotations">
      <li>
        <input readonly="true" type="text" class="regular-text" value="<?php echo (!empty($target_url)?esc_attr($target_url):esc_attr__('Target URL (above)', 'pretty-link')); ?>" />
        <?php esc_html_e('weight:', 'pretty-link'); ?>
        <?php PlpLinksHelper::rotation_weight_dropdown((($target_url_weight == 0 || !empty($target_url_weight))?$target_url_weight:'100'),'target_url_weight'); ?>
      </li>
      <?php
        for($i=0;$i<count($url_rotations);$i++) {
          $rotation = ((isset($url_rotations[$i]) && !empty($url_rotations[$i]))?$url_rotations[$i]:'');
          $weight   = (isset($url_rotation_weights[$i])?$url_rotation_weights[$i]:0);
          PlpLinksHelper::rotation_row($rotation, $weight, 'url_rotations[]', 'url_rotation_weights[]');
        }
      ?>
    </ol>
    <div><a id="prli_add_link_rotation" href=""><?php esc_html_e('Add Link Rotation', 'pretty-link'); ?></a></div>

    <table class="form-table">
        <tr>
          <th scope="row">
            <?php esc_html_e('Split Test', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'prli-link-pro-split-test',
                    esc_html__('Split Test This Link', 'pretty-link'),
                    esc_html__('Split testing will enable you to track the effectiveness of several links against each other. This works best when you have multiple link rotation URLs entered.', 'pretty-link')
                  ); ?>
          </th>
          <td>
            <input class="prli-toggle-checkbox" data-box="prlipro-split-test-goal-link" type="checkbox" name="enable_split_test" <?php checked($enable_split_test != 0); ?> />
          </td>
        </tr>
    </table>

    <div class="prli-sub-box-white prlipro-split-test-goal-link">
      <div class="prli-arrow prli-white prli-up prli-sub-box-arrow"> </div>
      <table class="form-table">
          <tr>
            <th scope="row">
              <?php esc_html_e('Goal Link', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                      'prli-link-pro-split-test-goal-link',
                      esc_html__('Goal Link for Split Test', 'pretty-link'),
                      esc_html__('This is the goal link for your split test.', 'pretty-link')
                    ); ?>
            </th>
            <td>
              <select name="split_test_goal_link" id="split_test_goal_link">
                <?php
                  if(!empty($selected_goal_link)) {
                    printf(
                      '<option value="%s" selected="selected">%s</option>',
                      esc_attr($selected_goal_link->id),
                      esc_html(
                        sprintf(
                          __('id: %1$s | slug: %3$s | name: %2$s', 'pretty-link'),
                          $selected_goal_link->id,
                          mb_substr(stripslashes($selected_goal_link->name), 0, 50),
                          $selected_goal_link->slug
                        )
                      )
                    );
                  }
                ?>
              </select>
            </td>
          </tr>
      </table>
    </div>
  </div>
  <div class="prli-sub-box prli-link-geo">
    <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
    <h3>
      <?php esc_html_e('Geographic Redirects', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-pro-geo-redirects',
              esc_html__('Geographic Redirects', 'pretty-link'),
              esc_html__('This will enable you to setup specific target urls that this pretty link will redirect to based on the country of the person visiting the url.', 'pretty-link')
            ); ?>
    </h3>
    <ul class="prli_geo_rows">
    </ul>
    <div><a href="" class="prli_geo_row_add"><?php esc_html_e('Add', 'pretty-link'); ?></a></div>
  </div>
  <div class="prli-sub-box prli-link-tech">
    <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
    <h3>
      <?php esc_html_e('Technology Redirects', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-pro-tech-redirects',
              esc_html__('Technology Redirects', 'pretty-link'),
              esc_html__('This will allow you to redirect based on your visitor\'s device, operating system and/or browser', 'pretty-link')
            ); ?>
    </h3>
    <ul class="prli_tech_rows">
    </ul>
    <div><a href="" class="prli_tech_row_add"><?php esc_html_e('Add', 'pretty-link'); ?></a></div>
  </div>
  <div class="prli-sub-box prli-link-time">
    <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
    <h3>
      <?php esc_html_e('Time Period Redirects', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-pro-time-redirects',
              esc_html__('Time Period Redirects', 'pretty-link'),
              sprintf(
                // translators: %1$s: br tag, %2$s: open b tag, %3$s close b tag
                esc_html__('This will allow you to redirect based on the time period in which your visitor visits this link.%1$s%1$s%2$sNote: If your visitor doesn\'t visit the link during any of the specified time periods set here, they\'ll simply be redirected to the main target url.%3$s', 'pretty-link'),
                '<br>',
                '<b>',
                '</b>'
              )
            ); ?>
    </h3>
    <ul class="prli_time_rows">
    </ul>
    <div><a href="" class="prli_time_row_add"><?php esc_html_e('Add', 'pretty-link'); ?></a></div>
  </div>
<?php endif; ?>
