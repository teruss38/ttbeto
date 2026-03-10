<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="description" content="<?php echo esc_attr(stripslashes($link->description)); ?>" />
  <title><?php echo esc_html(stripslashes($link->name)); ?></title>
  <meta name="robots" content="noindex" />

  <?php wp_print_styles(array('fontello-animation', 'fontello-pretty-link', 'prli-prettybar')); ?>

  <?php do_action('prli-pretty-bar-head'); ?>

</head>
<body>

  <?php do_action('prli-pretty-bar-header'); ?>

  <div id="prettybar">
    <table width="100%" height="65px">
      <tr>
      <td class="blog-image" valign="top">
        <div class="pb-cell">
        <a href="<?php echo esc_url($prli_blogurl); ?>" target="_top"><img src="<?php echo esc_url($bar_image); ?>" width="48px" height="48px" border="0"/></a></div>
      </td>
      <td class="blog-title" valign="top">
        <div class="pb-cell">
          <h2>
          <?php if( $bar_show_title ) { ?>
          <a href="<?php echo esc_url($prli_blogurl); ?>" title="<?php echo esc_attr($shortened_title); ?>" target="_top"><?php echo esc_html($shortened_title); ?></a>
          <?php } else echo "&nbsp;"; ?>
          </h2>
          <?php if( $bar_show_description ) { ?>
          <p title="<?php echo esc_attr($prli_blogdescription); ?>"><?php echo esc_html($shortened_desc); ?></p>
          <?php } else echo "&nbsp;"; ?>
        </div>
      </td>
      <td class="retweet" valign="top">
        <div class="pb-cell">
          <h4>
          <?php if( $bar_show_target_url_link ) { ?>
            <a href="<?php echo esc_url($target_url); ?>" title="<?php echo esc_attr(sprintf(__('You\'re viewing: %s', 'pretty-link'), $target_url)); ?>" target="_top"><?php printf(esc_html__('Viewing: %s', 'pretty-link'), esc_url($shortened_link)); ?></a>
          <?php } else echo "&nbsp;"; ?>
          </h4>
          <h4>
          <?php if( $bar_show_share_links ) { ?>
            <a href="<?php echo esc_url('https://twitter.com/intent/tweet?url=' . urlencode($prli_blogurl . PrliUtils::get_permalink_pre_slug_uri() . $slug));?>" target="_top"><?php esc_html_e('Share on Twitter', 'pretty-link'); ?></a>
          <?php } else echo "&nbsp;"; ?>
          </h4>
        </div>
      </td>
      <td valign="top">
        <div class="pb-cell right_container">
          <table width="100%" cellpadding="0" cellspacing="0" style="padding: 0px; margin: 0px;">
            <tr>
              <td>
                <p class="closebutton"><a href="<?php echo esc_url($target_url); ?>" target="_top"><i class="pl-icon pl-icon-cancel-circled pl-16"> </i></a></p>
              </td>
            </tr>
            <tr>
              <td>
                <?php ob_start(); ?>

                <p class="powered-by small-text"><?php esc_html_e('Powered by', 'pretty-link'); ?> <a href="https://prettylinks.com/plp/pretty-bar/powered-by" target="_top"><img src="<?php echo PRLI_IMAGES_URL; ?>/pretty-link-small.png" width="12px" height="12px" border="0"/> <?php esc_html_e('Pretty Links', 'pretty-link'); ?></a></p>
                <?php

                echo apply_filters('prli-display-attrib-link',ob_get_clean());
              ?>
              </td>
            </tr>
          </table>
        </div>
      </td>
      </tr>
    </table>
  </div>

  <?php do_action('prli-pretty-bar-footer'); ?>

</body>
</html>
