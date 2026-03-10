<?php if(!defined('ABSPATH')) die('You are not allowed to call this page directly.'); ?>

<div class="plp-link-health-metabox">
  <span class="status-wrap">
    <strong><?php esc_html_e('Status:', 'pretty-link'); ?></strong>
    <?php echo $markup; ?>
  </span>

  <button
    id="trigger-manual-health-check"
    type="button"
    class="button"
    data-button-text="<?php esc_attr_e('Check Health', 'pretty-link'); ?>"
    data-button-alt="<?php esc_attr_e('Checking...', 'pretty-link'); ?>"
    data-linkid="<?php echo esc_attr($link_id); ?>"
    data-nonce="<?php echo wp_create_nonce('plp_check_single_link_health_' . $link_id); ?>"
  >
    <?php esc_html_e('Check Health', 'pretty-link'); ?>
  </button>
</div>