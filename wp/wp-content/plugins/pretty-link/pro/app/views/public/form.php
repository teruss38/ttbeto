<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

global $post;

$target_url = (isset($_GET['url'])) && is_string($_GET['url']) ? esc_url_raw(trim(stripslashes($_GET['url']))) : '';

?>
<div id="prli_create_public_link">
  <form name="prli_public_form" class="prli_public_form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
    <input type="hidden" name="action" value="plp-create-public-link" />
    <input type="hidden" name="referral-url" value="<?php echo esc_attr(PrliUtils::current_page_url()); ?>"/>
    <input type="hidden" name="redirect_type" value="<?php echo esc_attr($redirect_type); ?>"/>
    <input type="hidden" name="track" value="<?php echo esc_attr($track); ?>"/>
    <input type="hidden" name="category" value="<?php echo esc_attr($category); ?>"/>

    <?php
      wp_nonce_field('plp-create-public-link', '_wpnonce', false);

      if(isset($_GET['errors'])):
        $errors = unserialize(stripslashes($_GET['errors']));

        if( is_array($errors) && count($errors) > 0 ):
          ?>
          <div class="error">
            <ul>
              <?php foreach( $errors as $error ): ?>
                <li><strong><?php esc_html_e('ERROR:', 'pretty-link'); ?></strong> <?php echo esc_html($error); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php
        endif;

      endif;
    ?>

    <p class="prli_create_link_fields">
      <div class="plp-create-link-label"><?php echo esc_html($label); ?></div>
      <div class="plp-create-link-input"><input type="text" name="url" value="<?php echo esc_attr($target_url); ?>" /></div>

      <?php if(!empty($button)): ?>
        <div class="plp-create-link-submit"><input type="submit" name="Submit" value="<?php echo esc_attr($button); ?>" /></div>
      <?php endif; ?>
    </p>
  </form>
</div>

