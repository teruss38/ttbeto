<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
  $tech_url = empty($tech_url)?'{{tech_url}}':$tech_url;
  $tech_device = empty($tech_device)?'{{tech_device}}':$tech_device;
  $tech_os = empty($tech_os)?'{{tech_os}}':$tech_os;
  $tech_browser = empty($tech_browser)?'{{tech_browser}}':$tech_browser;
?>
<li>
  <div class="prli-sub-box-white prli-tech-row">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <?php esc_html_e('Device:', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'prli-link-pro-tech-redirects-device',
                    esc_html__('Technology Redirection Device', 'pretty-link'),
                    sprintf(
                      // translators: %1$s: open b tag, %2$s: close b tag, %3$s: br tag
                      esc_html__('%1$sDesktop%2$s will match on any conventional laptop or desktop computer.%3$s%3$s%1$sMobile%2$s will match on any phone, tablet or other portable device.%3$s%3$s%1$sPhone%2$s will match on any phone or similarly small device.%3$s%3$s%1$sTablet%2$s will match on any tablet sized device.', 'pretty-link'),
                      '<b>',
                      '</b>',
                      '<br>'
                    )
                  ); ?>
          </th>
          <td>
            <select name="prli_tech_device[]" class="prli_tech_device">
              <option value="any" <?php selected($tech_device,'any'); ?>><?php esc_html_e('Any', 'pretty-link'); ?></option>
              <option value="desktop" <?php selected($tech_device,'desktop'); ?>><?php esc_html_e('Desktop', 'pretty-link'); ?></option>
              <option value="mobile" <?php selected($tech_device,'mobile'); ?>><?php esc_html_e('Mobile', 'pretty-link'); ?></option>
              <option value="phone" <?php selected($tech_device,'phone'); ?>><?php esc_html_e('Phone', 'pretty-link'); ?></option>
              <option value="tablet" <?php selected($tech_device,'tablet'); ?>><?php esc_html_e('Tablet', 'pretty-link'); ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php esc_html_e('Operating System:', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'prli-link-pro-tech-redirects-os',
                    esc_html__('Technology Redirection OS', 'pretty-link'),
                    esc_html__('Use this dropdown to select which Operating System this redirect will match on.', 'pretty-link')
                  ); ?>
          </th>
          <td>
            <select name="prli_tech_os[]" class="prli_tech_os">
              <option value="any" <?php selected($tech_os,'any'); ?>><?php esc_html_e('Any', 'pretty-link'); ?></option>
              <option value="android" <?php selected($tech_os,'android'); ?>><?php esc_html_e('Android', 'pretty-link'); ?></option>
              <option value="ios" <?php selected($tech_os,'ios'); ?>><?php esc_html_e('iOS', 'pretty-link'); ?></option>
              <option value="linux" <?php selected($tech_os,'linux'); ?>><?php esc_html_e('Linux', 'pretty-link'); ?></option>
              <option value="macosx" <?php selected($tech_os,'macosx'); ?>><?php esc_html_e('Mac', 'pretty-link'); ?></option>
              <option value="win" <?php selected($tech_os,'win'); ?>><?php esc_html_e('Windows', 'pretty-link'); ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php esc_html_e('Browser:', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'prli-link-pro-tech-redirects-browser',
                    esc_html__('Technology Redirection Browser', 'pretty-link'),
                    esc_html__('Use this dropdown to select which Browser this redirect will match on.', 'pretty-link')
                  ); ?>
          </th>
          <td>
            <select name="prli_tech_browser[]" class="prli_tech_browser">
              <option value="any" <?php selected($tech_browser,'any'); ?>><?php esc_html_e('Any', 'pretty-link'); ?></option>
              <option value="silk" <?php selected($tech_browser,'silk'); ?>><?php esc_html_e('Amazon Silk', 'pretty-link'); ?></option>
              <option value="android" <?php selected($tech_browser,'android'); ?>><?php esc_html_e('Android', 'pretty-link'); ?></option>
              <option value="chrome" <?php selected($tech_browser,'chrome'); ?>><?php esc_html_e('Chrome', 'pretty-link'); ?></option>
              <option value="chromium" <?php selected($tech_browser,'chromium'); ?>><?php esc_html_e('Chromium', 'pretty-link'); ?></option>
              <option value="edge" <?php selected($tech_browser,'edge'); ?>><?php esc_html_e('Edge', 'pretty-link'); ?></option>
              <option value="firefox" <?php selected($tech_browser,'firefox'); ?>><?php esc_html_e('Firefox', 'pretty-link'); ?></option>
              <option value="ie" <?php selected($tech_browser,'ie'); ?>><?php esc_html_e('Internet Explorer', 'pretty-link'); ?></option>
              <option value="kindle" <?php selected($tech_browser,'kindle'); ?>><?php esc_html_e('Kindle', 'pretty-link'); ?></option>
              <option value="opera" <?php selected($tech_browser,'opera'); ?>><?php esc_html_e('Opera', 'pretty-link'); ?></option>
              <option value="coast" <?php selected($tech_browser,'coast'); ?>><?php esc_html_e('Opera Coast', 'pretty-link'); ?></option>
              <option value="safari" <?php selected($tech_browser,'safari'); ?>><?php esc_html_e('Safari', 'pretty-link'); ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php esc_html_e('URL:', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'prli-link-pro-tech-redirects-url',
                    esc_html__('Technology Redirection URL', 'pretty-link'),
                    esc_html__('This is the URL that this Pretty Link will redirect to if the visitor\'s device, os and browser match the settings here.', 'pretty-link')
                  ); ?>
          </th>
          <td>
            <input type="text" name="prli_tech_url[]" class="prli_tech_url large-text" value="<?php echo esc_attr($tech_url); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
    <div><a href="" class="prli_tech_row_remove"><?php esc_html_e('Remove', 'pretty-link'); ?></a></div>
  </div>
</li>


