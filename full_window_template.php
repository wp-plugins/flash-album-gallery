<?php
preg_match('|^(.*?/)(wp-content)/|i', str_replace('\\', '/', __FILE__), $_m);
require_once( $_m[1] . 'wp-load.php');
global $post;
$flag_custom = get_post_custom($post->ID);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php wp_title(''); ?></title>
<style type="text/css">
html, body { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; min-height: 200px; min-width: 320px; }
div#page, .flashalbum { width: 100%; height: 100%; position: relative; z-index: 1; }
.flag_alternate { margin: 0 !important; }
<?php if(isset($flag_custom['mb_bg_link'][0]) && !empty($flag_custom['mb_bg_link'][0])) { ?>
div.flashalbum { background-image: url(<?php echo $flag_custom['mb_bg_link'][0]; ?>); background-position: <?php echo $flag_custom['mb_bg_pos'][0]; ?>; background-repeat: <?php echo $flag_custom['mb_bg_repeat'][0]; ?>; }
<?php } ?>
</style>
<script language="JavaScript" src="<?php echo plugins_url('/flash-album-gallery/admin/js/jquery.js'); ?>" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo plugins_url('/flash-album-gallery/admin/js/swfobject.js'); ?>" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo plugins_url('/flash-album-gallery/admin/js/swfaddress.js'); ?>" type="text/javascript"></script>
</head>
<body id="fullwindow">
<div id="page">
<?php
if ( post_password_required( $post ) ) {
	the_content();
} else {
	$scode = $flag_custom["mb_scode"][0];
	echo do_shortcode($scode);
} ?>
</div>
<script language="JavaScript" src="<?php echo plugins_url('/flash-album-gallery/admin/js/flagscroll.js'); ?>" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo plugins_url('/flash-album-gallery/admin/js/script.js'); ?>" type="text/javascript"></script>
</body>
</html>