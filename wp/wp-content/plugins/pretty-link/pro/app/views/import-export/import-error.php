<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="wrap">
  <?php PrliAppHelper::page_title(__('Import Error', 'pretty-link')); ?>
  <div class="error inline">
    <p>
      <?php echo esc_html($error); ?>
      <a href="<?php echo esc_url(admin_url('edit.php?post_type=' . PrliLink::$cpt . '&page=plp-import-export')); ?>"><?php esc_html_e('&larr; Go back', 'pretty-link'); ?></a>
    </p>
  </div>
</div>
