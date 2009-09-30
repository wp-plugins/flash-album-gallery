<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

/* Move skins outside the plugin folder */
$flag_options = get_option('flag_options');
$skins_dir = WP_PLUGIN_DIR . '/flagallery-skins';
$old_skins_dir = FLAG_ABSPATH . 'skins';
$flag_options = get_option('flag_options');
// look first at the old place and move it
if ( is_dir( $old_skins_dir ) ) {
	@rename($old_skins_dir, $skins_dir);
}
// If it's successful then we return the new path
if ( is_dir($skins_dir) ) {
	$flag_options['skinsDirABS'] = $skins_dir.'/';
	$flag_options['skinsDirURL'] = WP_PLUGIN_URL . '/flagallery-skins/';
} else {
	$flag_options['skinsDirABS'] = FLAG_ABSPATH . 'skins/'; 
	$flag_options['skinsDirURL'] = FLAG_URLPATH . 'skins/'; 
}
update_option('flag_options', $flag_options);

?>