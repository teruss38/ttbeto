<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo esc_html(stripslashes($pretty_link->name)); ?></title>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="description" content="<?php echo esc_attr(stripslashes($pretty_link->description)); ?>" />
    <meta name="robots" content="noindex" />

    <?php wp_print_styles('prli-cloaked-redirect'); ?>

    <?php if(!empty($google_tracking) && $google_tracking && ($ga_info = PlpUtils::ga_installed())) { echo PlpUtils::ga_tracking_code($ga_info['slug']); } ?>

    <?php do_action('prli-redirect-header'); ?>
  </head>
  <body>
    <iframe src="<?php echo esc_url($pretty_link_url.$param_string); ?>">
      <?php esc_html_e('Your browser does not support frames.', 'pretty-link'); ?> Click <a href="<?php echo esc_url($pretty_link_url.$param_string); ?>">here</a> to view the page.
    </iframe>
  </body>
</html>
