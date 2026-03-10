<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="wrap">
  <?php PrliAppHelper::page_title(__('Reports', 'pretty-link')); ?>

  <ul style="list-style-type: none;">
    <li><a href="<?php echo esc_url(str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '&action=list'); ?>"><?php esc_html_e('Link Reports', 'pretty-link'); ?></a></li>
  </ul>
</div>
