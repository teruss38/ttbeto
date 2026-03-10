<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="wrap">
<?php PrliAppHelper::page_title(__('Import Results', 'pretty-link')); ?>
<p><?php echo esc_html(sprintf(__('Total Rows: %s', 'pretty-link'), number_format_i18n($total_row_count))); ?></p>

<p><?php echo esc_html(sprintf(__('%s Pretty Links were Successfully Created', 'pretty-link'), number_format_i18n($successful_create_count))); ?></p>
<p><?php echo esc_html(sprintf(__('%s Pretty Links were Successfully Updated', 'pretty-link'), number_format_i18n($successful_update_count))); ?></p>

<?php
if(count($creation_errors) > 0) {
?>
  <p><?php echo esc_html(sprintf(__('Pretty Links were unable to be Created: %s', 'pretty-link'), number_format_i18n(count($creation_errors)))); ?></p>
<?php
  foreach($creation_errors as $creation_error) {
    ?>
    <p class="wp-error"><?php echo esc_html(sprintf(__('Error(s) for Pretty Link with Slug: %s', 'pretty-link'), $creation_error['slug'])); ?><br/>
    <?php
      foreach( $creation_error['errors'] as $error ) {
        ?>
        &nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_html($error); ?><br/>
        <?php
      }
    ?>
    </p>
    <?php
  }
}

if(count($update_errors) > 0) {
?>
  <p><?php echo esc_html(sprintf(__('Pretty Links were unable to be Updated: %s', 'pretty-link'), number_format_i18n(count($update_errors)))); ?></p>
<?php
  foreach($update_errors as $update_error) {
    ?>
    <p class="wp-error"><?php echo esc_html(sprintf(__('Error(s) for Pretty Link with id: %s', 'pretty-link'), $update_error['id'])); ?><br/>
    <?php
      foreach( $update_error['errors'] as $error ) {
        ?>
        &nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_html($error); ?><br/>
        <?php
      }
    ?>
    </p>
    <?php
  }
}
?>

</div>

