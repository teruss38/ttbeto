<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="wrap">
  <?php PrliAppHelper::page_title(__('Link Split-Test Report', 'pretty-link')); ?>
  <h3><?php esc_html_e('For Link:', 'pretty-link'); ?> "<?php echo esc_html(stripslashes($link->name)); ?>"</h3>
  <?php if( !empty($goal_link_id) and $goal_link_id ) { ?>
    <h4><?php esc_html_e('Goal Link:', 'pretty-link'); ?> "<?php echo esc_html(stripslashes($goal_link->name)); ?>"</h4>
  <?php } ?>
  <a href="<?php echo esc_url(admin_url('edit.php?post_type=pretty-link')); ?>" style="font-size:16px;">&laquo <?php esc_html_e('Back to Links', 'pretty-link'); ?></a>&nbsp;|&nbsp;<a href="#" style="display:inline;" class="filter_toggle"><?php esc_html_e('Customize Report', 'pretty-link'); ?></a>
  <div class="filter_pane">
    <form class="form-fields" name="form2" method="post" action="">
      <?php wp_nonce_field('prli-reports'); ?>
      <span><?php esc_html_e('Date Range:', 'pretty-link'); ?></span>
      <div id="dateselectors" style="display: inline;">
        <input type="text" name="sdate" id="sdate" value="<?php echo esc_attr($params['sdate']); ?>" style="display:inline;"/>&nbsp;to&nbsp;<input type="text" name="edate" id="edate" value="<?php echo esc_attr($params['edate']); ?>" style="display:inline;"/>
      </div>
      <br/>
      <br/>
      <div class="submit" style="display: inline;"><input type="submit" name="Submit" value="<?php esc_attr_e('Customize', 'pretty-link'); ?>" class="button button-primary" /> &nbsp; <a href="#" class="filter_toggle button"><?php esc_html_e('Cancel', 'pretty-link'); ?></a></div>
    </form>
  </div>
  <div id="clicks_chart" style="margin-top:15px;"></div>
  <br/><br/>
  <table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
      <th class="manage-column" width="40%"><?php esc_html_e('Link Rotation URL', 'pretty-link'); ?></th>
      <th class="manage-column" width="15%"><?php esc_html_e('Clicks', 'pretty-link'); ?></th>
      <th class="manage-column" width="15%"><?php esc_html_e('Uniques', 'pretty-link'); ?></th>
      <?php if( !empty($goal_link_id) and $goal_link_id ) { ?>
      <th class="manage-column" width="15%"><?php esc_html_e('Conversions', 'pretty-link'); ?></th>
      <th class="manage-column" width="15%"><?php esc_html_e('Conv Rate', 'pretty-link'); ?></th>
      <?php } ?>
    </tr>
    </thead>
  <?php

  for($i=0;$i<count($links);$i++) {
    $label        = stripslashes($labels[$i]);
    $hit_count    = $hits[$i];
    $unique_count = $uniques[$i];
    $conv_count   = $conversions[$i];
    $conv_rate    = $conv_rates[$i];
    ?>
    <tr>
      <td><strong><?php echo esc_html($label); ?></strong></td>
      <td<?php echo (((float)$hit_count == (float)$top_hits)?' style="font-weight: bold;"':'') ?>><?php echo esc_html($hit_count); ?></td>
      <td<?php echo (((float)$unique_count == (float)$top_uniques)?' style="font-weight: bold;"':'') ?>><?php echo esc_html($unique_count); ?></td>
    <?php if( !empty($goal_link_id) and $goal_link_id ) { ?>
      <td<?php echo (((float)$conv_count == (float)$top_conversions)?' style="font-weight: bold;"':'') ?>><?php echo esc_html($conv_count); ?></td>
      <td<?php echo (((float)$conv_rate == (float)$top_conv_rate)?' style="font-weight: bold;"':'') ?>><?php echo esc_html($conv_rate); ?>%</td>
    <?php } ?>
    </tr>
    <?php
  }
  ?>
    <tfoot>
    <tr>
      <th class="manage-column"><?php esc_html_e('Rotation URL', 'pretty-link'); ?></th>
      <th class="manage-column"><?php esc_html_e('Clicks', 'pretty-link'); ?></th>
      <th class="manage-column"><?php esc_html_e('Uniques', 'pretty-link'); ?></th>
      <?php if( !empty($goal_link_id) and $goal_link_id ) { ?>
      <th class="manage-column"><?php esc_html_e('Conversions', 'pretty-link'); ?></th>
      <th class="manage-column"><?php esc_html_e('Conv Rate', 'pretty-link'); ?></th>
      <?php } ?>
    </tr>
    </tfoot>
  </table>
</div>
