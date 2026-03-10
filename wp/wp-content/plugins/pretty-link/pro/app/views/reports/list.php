<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="wrap">
  <?php PrliAppHelper::page_title(__('Link Reports', 'pretty-link')); ?>
  <a href="<?php echo esc_url(admin_url('admin.php?page=plp-reports&action=new')); ?>" class="page-title-action"><?php esc_html_e('Add Report', 'pretty-link'); ?></a>
  <hr class="wp-header-end">

  <?php if($record_count <= 0): ?>
    <div class="updated notice notice-success is-dismissible"><p><?php echo $prli_message; ?></p></div>
  <?php endif; ?>

  <div id="search_pane" style="float: right;">
    <form class="form-fields" name="report_form" method="post" action="">
      <?php wp_nonce_field('prlipro-reports'); ?>
      <input type="hidden" name="sort" id="sort" value="<?php echo esc_attr($sort_str); ?>" />
      <input type="hidden" name="sdir" id="sort" value="<?php echo esc_attr($sdir_str); ?>" />
      <input type="text" name="search" id="search" value="<?php echo esc_attr($search_str); ?>" style="display:inline;"/>
      <div class="submit" style="display: inline;padding-bottom: 0;"><input class="button button-primary" type="submit" name="Submit" value="<?php esc_attr_e('Search', 'pretty-link'); ?>"/>
      <?php if(!empty($search_str)): ?>
      &nbsp; <a href="<?php echo esc_url(admin_url('admin.php?page=plp-reports&action=list')); ?>" class="button"><?php esc_html_e('Reset', 'pretty-link'); ?></a>
      <?php endif; ?>
      </div>
    </form>
  </div>

  <?php require(PRLI_VIEWS_PATH.'/shared/table-nav.php'); ?>
  <table class="widefat post fixed" cellspacing="0">
    <thead>
      <tr>
        <th class="manage-column" width="35%">
          <a href="<?php echo esc_url(admin_url('admin.php?page=plp-reports&action=list&sort=name') . (($sort_str == 'name' && $sdir_str == 'asc')?'&sdir=desc':'')); ?>">
            <?php esc_html_e('Name', 'pretty-link'); ?><?php echo (($sort_str == 'name')?'&nbsp;&nbsp;&nbsp;<img src="'.esc_url(PRLI_IMAGES_URL.'/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png')).'"/>':'') ?>
          </a>
        </th>
        <th class="manage-column" width="35%">
          <a href="<?php echo esc_url(admin_url('admin.php?page=plp-reports&action=list&sort=goal_link_name') . (($sort_str == 'goal_link_name' and $sdir_str == 'asc')?'&sdir=desc':'')); ?>">
            <?php esc_html_e('Goal Link', 'pretty-link'); ?><?php echo (($sort_str == 'goal_link_name')?'&nbsp;&nbsp;&nbsp;<img src="'.esc_url(PRLI_IMAGES_URL.'/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png')).'"/>':'') ?>
          </a>
        </th>
        <th class="manage-column" width="10%">
          <a href="<?php echo esc_url(admin_url('admin.php?page=plp-reports&action=list&sort=link_count') . (($sort_str == 'link_count' and $sdir_str == 'asc')?'&sdir=desc':'')); ?>">
            <?php esc_html_e('Links', 'pretty-link'); ?><?php echo (($sort_str == 'link_count')?'&nbsp;&nbsp;&nbsp;<img src="'.esc_url(PRLI_IMAGES_URL.'/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png')).'"/>':'') ?>
          </a>
        </th>
        <th class="manage-column" width="20%">
          <a href="<?php echo esc_url(admin_url('admin.php?page=plp-reports&action=list&sort=created_at') . (($sort_str == 'created_at' and $sdir_str == 'asc')?'&sdir=desc':'')); ?>">
            <?php esc_html_e('Created', 'pretty-link'); ?><?php echo ((empty($sort_str) or $sort_str == 'created_at')?'&nbsp;&nbsp;&nbsp;<img src="'.esc_url(PRLI_IMAGES_URL.'/'.((empty($sort_str) or $sdir_str == 'desc')?'arrow_down.png':'arrow_up.png')).'"/>':'') ?>
          </a>
        </th>
      </tr>
    </thead>
    <?php

    if($record_count <= 0) {
        ?>
      <tr>
        <td colspan="4"><?php esc_html_e('No Pretty Link Reports were found', 'pretty-link'); ?></td>
      </tr>
      <?php
    }
    else {
      $row_index=0;
      foreach($reports as $report) {
        $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
        ?>
        <tr id="record_<?php echo esc_attr($report->id); ?>" class="<?php echo esc_attr($alternate); ?>">
          <td class="edit_report">
          <a class="report_name" href="<?php echo esc_url(admin_url("admin.php?page=plp-reports&action=edit&id={$report->id}")); ?>" title="<?php echo esc_attr(sprintf(__('Edit %s', 'pretty-link'), stripslashes($report->name))); ?>"><?php echo esc_html(stripslashes($report->name)); ?></a>
            <br/>
            <div class="report_actions">
              <a href="<?php echo esc_url(admin_url("admin.php?page=plp-reports&action=edit&id={$report->id}")); ?>" title="<?php echo esc_attr(sprintf(__('Edit %s', 'pretty-link'), stripslashes($report->name))); ?>"><?php esc_html_e('Edit', 'pretty-link'); ?></a>&nbsp;|
              <a href="<?php echo esc_url(admin_url("admin.php?page=plp-reports&action=destroy&id={$report->id}")); ?>" onclick="return confirm('<?php echo esc_attr(sprintf(__('Are you sure you want to delete your %s Pretty Link Report?', 'pretty-link'), stripslashes($report->name))); ?>');" title="<?php echo esc_attr(sprintf(__('Delete %s', 'pretty-link'), stripslashes($report->name))); ?>"><?php esc_html_e('Delete', 'pretty-link'); ?></a>&nbsp;|
              <a href="<?php echo esc_url(admin_url("admin.php?page=plp-reports&action=display-custom-report&id={$report->id}")); ?>" title="<?php echo esc_attr(sprintf(__('View report for %s', 'pretty-link'), stripslashes($report->name))); ?>"><?php esc_html_e('View', 'pretty-link'); ?></a>
            </div>
          </td>
          <td><?php echo isset($report->goal_link_name) ? esc_html(stripslashes($report->goal_link_name)) : ''; ?></td>
          <td><?php echo esc_html($report->link_count); ?></td>
          <td><?php echo esc_html($report->created_at); ?></td>
        </tr>
        <?php
      }
    }
    ?>
      <tfoot>
      <tr>
        <th class="manage-column"><?php esc_html_e('Name', 'pretty-link'); ?></th>
        <th class="manage-column"><?php esc_html_e('Goal Link', 'pretty-link'); ?></th>
        <th class="manage-column"><?php esc_html_e('Links', 'pretty-link'); ?></th>
        <th class="manage-column"><?php esc_html_e('Created', 'pretty-link'); ?></th>
      </tr>
      </tfoot>
  </table>
  <?php require(PRLI_VIEWS_PATH.'/shared/table-nav.php'); ?>
</div>
