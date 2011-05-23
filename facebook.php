<?php
preg_match('|^(.*?/)(wp-content)/|i', str_replace('\\', '/', __FILE__), $_m);
require_once( $_m[1] . 'wp-load.php');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?> - <?php bloginfo('description'); ?> </title>
<?php wp_head(); ?>
</head>
<body style="margin: 0; padding: 0;">
<div id="page">
<?php $flag_options = get_option('flag_options');
if(isset($_GET['l'])) {
	$post = (int) $flag_options['flashHeight'];
} else {
	$posts = get_posts(array("showposts" => 1));
	$post = $posts[0]->ID;
}
query_posts('p='.$post);
while ( have_posts() ) : the_post();

if(isset($_GET['i'])) {
	$gid = str_replace('_',',',$_GET['i']);
	$skin = '';
	if(isset($_GET['f'])){
		$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$_GET['f'];
		if(is_dir($skinpath))
			$skin = " skin=".$_GET['f'];
	}
	$h = isset($_GET['h'])? $_GET['h'] : (int) $flag_options['flashHeight'];
	echo do_shortcode('[flagallery gid='.$gid.' h='.$h.' w=100% name=Gallery'.$skin.']');
}
?>

<?php 
if(isset($_GET['m'])) {
	$playlistpath = $flag_options['galleryPath'].'playlists/'.$_GET['m'].'.xml';
	if(file_exists($playlistpath))
		echo do_shortcode('[grandmusic playlist='.$_GET['m'].']');
	else
		_e("Can't find playlist");
}

endwhile;
?>
</div>
<?php wp_footer(); ?>
</body>
</html>