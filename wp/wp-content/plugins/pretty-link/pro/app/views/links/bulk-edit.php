<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');
?>
<div class="inline-edit-group advanced-link-options">
  <h4><?php esc_html_e('Advanced Link Options', 'pretty-link'); ?></h4>
  <div id="prli_google_analytics" style="display: none;">
  <?php
    if($ga_info = PlpUtils::ga_installed()):
      PrliLinksHelper::bulk_action_checkbox_dropdown('bu[google_tracking]', __('Google Analytics', 'pretty-link'), 'bulk-edit-select'); ?>
    <?php endif; ?>
  </div>
</div>
<?php /*
<div class="inline-edit-group keyword-replacements">
  <?php global $plp_options; ?>
  <?php if( $plp_options->keyword_replacement_is_on ): ?>
    <h4><?php esc_html_e('Keyword Replacements', 'pretty-link'); ?></h4>
    <?php global $plp_options; ?>
    <input type="text" name="bu[keywords]" class="bulk-edit-text" />
    <br/>
    <h4><?php esc_html_e('URL Replacements', 'pretty-link'); ?></h4>
    <input type="text" name="bu[url_replacements]" class="bulk-edit-text" />
    <br/>
  <?php endif; ?>
  <div id="prli_time_delay" style="display: none;">
    <h4><?php esc_html_e('Redirect Delay', 'pretty-link'); ?></h4>
    <input type="text" name="delay" value="0" />
  </div>
</div>
  */ ?>
