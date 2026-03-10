<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

$name = isset($_POST['name']) ? sanitize_text_field(stripslashes($_POST['name'])) : '';
?>

<div class="wrap">
  <?php PrliAppHelper::page_title(__('Add Link Report', 'pretty-link')); ?>

  <?php require(PRLI_VIEWS_PATH.'/shared/errors.php'); ?>

  <form name="form1" method="post" action="<?php echo esc_url(admin_url('admin.php?page=plp-reports')); ?>">
    <input type="hidden" name="action" value="create">
    <?php wp_nonce_field('update-options'); ?>

    <table class="form-table">
      <tr class="form-field">
        <td width="75px" valign="top"><?php esc_html_e('Name*:', 'pretty-link'); ?> </td>
        <td><input type="text" name="name" value="<?php echo esc_attr($name); ?>" size="75">
          <br/><span class="description"><?php esc_html_e("This is how you'll identify your Report.", 'pretty-link'); ?></span>
        </td>
      </tr>
    </table>
    <table class="form-table">
      <tr class="form-field" valign="top">
        <td width="50%" valign="top">
          <h3><?php esc_html_e('Select Links to Analyze in this Report:', 'pretty-link'); ?></h3>
          <div style="height: 400px; width: 95%; border: 1px solid #8cbdd5; overflow: auto;">
            <ul width="100%">
              <?php for ($i = 0; $i < count($links); $i++) :
                $link = $links[$i];
              ?>
                <li class="link-list-item" style="<?php echo (($i%2)?'background-color: #efefef; ':'background-color: #dedede; '); ?>padding: 5px; margin: 0px; "><input type="checkbox" style="width: 15px;" name="link[<?php echo esc_attr($link->id); ?>]" <?php echo (((isset($_POST['link'][$link->id]) and $_POST['link'][$link->id] == 'on'))?'checked="true"':''); ?>/>&nbsp;<?php echo esc_html(substr(stripslashes($link->name),0,50)) . " <strong>(" . esc_html(stripslashes($link->slug)) . ")</strong>"; ?></li>
              <?php endfor; ?>
            </ul>
          </div>
          <span class="description"><?php esc_html_e('Select some links to be analyzed in this report.', 'pretty-link'); ?></span>
        </td>
        <td valign="top" width="50%">
          <h3><?php esc_html_e('Select Your Goal Link (optional):', 'pretty-link'); ?> </h3>
          <div style="height: 400px; width: 95%; border: 1px solid #8cbdd5; overflow: auto;">
            <table width="100%" cellspacing="0">
              <thead style="background-color: #dedede; padding: 0px; margin: 0px; line-height: 8px; font-size: 14px;">
                <tr>
                  <th width="100%" style="padding-left: 5px; margin: 0px;"><strong><?php esc_html_e('Name', 'pretty-link'); ?></strong></th>
                </tr>
              </thead>
              <?php
              for($i = 0; $i < count($links); $i++) {
                $link = $links[$i];
                ?>
                <tr <?php echo (($i%2)?' style="background-color: #efefef;"':''); ?>>
                  <td style="padding: 5px; margin: 0px;" width="50%"><input type="radio" style="width: 15px;" name="goal_link_id" value="<?php echo esc_attr($link->id); ?>" <?php echo (((isset($_POST['goal_link_id']) and $_POST['goal_link_id'] == $link->id))?'checked="true"':''); ?>/>&nbsp;<?php echo esc_html(substr(stripslashes($link->name),0,25)) . " <strong>(" . esc_html(stripslashes($link->slug)) . ")</strong>"; ?></td>
                </tr>
                <?php

              }
              ?>
            </table>
          </div>
          <span class="description"><?php esc_html_e('If you want to enable conversion tracking in this report then select a goal link.', 'pretty-link'); ?></span>
        </td>
      </tr>
    </table>

    <p class="submit">
      <input type="submit" name="Submit" value="<?php esc_attr_e('Create', 'pretty-link'); ?>" class="button button-primary" /> &nbsp; <a href="<?php echo esc_url(admin_url('admin.php?page=plp-reports&action=list')); ?>" class="button"><?php esc_html_e('Cancel', 'pretty-link'); ?></a>
    </p>

  </form>
</div>
