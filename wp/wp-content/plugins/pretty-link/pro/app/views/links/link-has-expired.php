<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php echo get_option( 'blog_charset' ); ?>" />
  <meta name="robots" content="noindex,nofollow" />
  <title><?php esc_html_e('Link Not Found', 'pretty-link'); ?></title>
  <?php wp_print_styles(array('prli-bootstrap', 'prli-bootstrap-theme')); ?>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div class="panel panel-default">
          <div class="panel-body">
            <center><img src="<?php echo esc_url(PRLI_IMAGES_URL . "/pl-logo-horiz-RGB.svg"); ?>" width="60%" /></center>
            <div>&nbsp;</div>
            <center><h1><?php esc_html_e('Sorry, this link has expired', 'pretty-link'); ?></h1></center>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

