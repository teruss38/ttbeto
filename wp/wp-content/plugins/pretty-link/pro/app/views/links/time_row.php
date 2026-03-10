<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
  $time_url = empty($time_url)?'{{time_url}}':$time_url;
  $time_start = empty($time_start)?'{{time_start}}':$time_start;
  $time_end = empty($time_end)?'{{time_end}}':$time_end;
?>
<li>
  <div class="prli-sub-box-white prli-time-row">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <?php esc_html_e('Start Time:', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'prli-link-pro-start-time-redirects-period',
                    esc_html__('Start of Time Period', 'pretty-link'),
                    esc_html__('This is where you\'ll enter the beginning of the time period for this redirect', 'pretty-link')
                  ); ?>
          </th>
          <td>
            <input type="text" name="prli_time_start[]" class="prli_time_start prli-date-picker regular-text" value="<?php echo esc_attr($time_start); ?>" />
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php esc_html_e('End Time:', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'prli-link-pro-end-time-redirects-period',
                    esc_html__('End of Time Period', 'pretty-link'),
                    esc_html__('This is where you\'ll enter the end of the time period for this redirect', 'pretty-link')
                  ); ?>
          </th>
          <td>
            <input type="text" name="prli_time_end[]" class="prli_time_end prli-date-picker regular-text" value="<?php echo esc_attr($time_end); ?>" />
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php esc_html_e('URL:', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'prli-link-pro-time-redirects-url',
                    esc_html__('Time Period Redirect URL', 'pretty-link'),
                    esc_html__('This is the URL that this Pretty Link will redirect to when the visitor visits the link in the associated time period.', 'pretty-link')
                  ); ?>
          </th>
          <td>
            <input type="text" name="prli_time_url[]" class="prli_time_url large-text" value="<?php echo esc_attr($time_url); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
    <div><a href="" class="prli_time_row_remove"><?php esc_html_e('Remove', 'pretty-link'); ?></a></div>
  </div>
</li>
