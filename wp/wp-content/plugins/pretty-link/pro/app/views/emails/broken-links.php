<?php if(!defined('ABSPATH')) { exit; } ?>

<p><?php esc_html_e('Howdy! Here\'s your weekly broken links report:', 'pretty-link'); ?></p>

<?php if($broken_link_count): ?>
  <p><?php printf(__('Pretty Links has detected %s broken link(s) on your site.', 'pretty-link'), $broken_link_count); ?></p>

  <p><?php printf(__('To fix them, visit the <a href="%s">Broken Links</a> page and edit the link\'s target URL.', 'pretty-link'), $broken_link_url); ?></p>
<?php else: ?>
  <p><?php esc_html_e('No broken links have been detected at this time.', 'pretty-link'); ?></p>
<?php endif; ?>

<?php if($ad): ?>
  <?php echo $ad; ?>
<?php endif; ?>