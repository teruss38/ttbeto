<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="robots" content="noindex" />
  <title><?php echo esc_html($prli_blogname) ?></title>
<?php
  if(!empty($google_tracking) && $google_tracking && ($ga_info = PlpUtils::ga_installed())) {
    echo PlpUtils::ga_tracking_code($ga_info['slug']);
  }

  do_action('prli-redirect-header');
  wp_print_scripts('plp-javascript-redirect');
?>
</head>
<body>

</body>
</html>
