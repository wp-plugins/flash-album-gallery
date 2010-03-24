<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

function flag_tune() {
	/* Move skins outside the plugin folder */
	$flag_options = get_option('flag_options');
	$skins_dir = str_replace("\\","/", WP_PLUGIN_DIR . '/flagallery-skins/' );
	$old_skins_dir = FLAG_ABSPATH . 'skins/';
	$flag_options = get_option('flag_options');

	// check for main folder
	if ( !is_dir($skins_dir) ) {
		if ( !wp_mkdir_p( $skins_dir ) ) {
			$txt  = __('Directory <strong>"', 'flag').$skins_dir.__('"</strong> doesn\'t exist. Please create first the <strong>"flagallery-skins"</strong> folder!', 'flag');
			flagGallery::show_error($txt);
		}
	} else {
		// check for permission settings, Safe mode limitations are not taken into account. 
		if ( !is_writeable( $skins_dir ) ) {
			$txt  = __('Directory <strong>"', 'flag').$skins_dir.__('"</strong> is not writeable!', 'flag');
			flagGallery::show_error($txt);
			return false;
		}
	}
	// Files in flash-album-gallery/skins directory
	$open_old_skins_dir = @ opendir( $old_skins_dir);
	if ( $open_old_skins_dir ) {
		while (($file = readdir( $open_old_skins_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' )
				continue;
			if ( is_dir( $old_skins_dir.$file ) ) {
				if( is_dir( $skins_dir.$file ) ) {
					flagGallery::flagFolderDelete( $skins_dir.$file );
				}
				@ rename($old_skins_dir.$file, $skins_dir.$file);
			}
		}
	}
	@ closedir( $open_old_skins_dir );

	// Files in flagallery-skins directory
	$open_skins_dir = @ opendir( $skins_dir);
	if ( $open_skins_dir ) {
		while (($file = readdir( $open_skins_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' )
				continue;
			if ( is_dir( $skins_dir.$file ) ) {
				$skins_subdir = @ opendir( $skins_dir.$file );
				if ( $skins_subdir ) {
					while (($subfile = readdir( $skins_subdir ) ) !== false ) {
						if ( substr($subfile, 0, 1) == '.' )
							continue;
						if ( substr($subfile, -4) == '.png' )
							@ rename($skins_dir.$file.'/'.$subfile, $skins_dir.$file.'/screenshot.png');
						if( !file_exists( $skins_dir.$file.'/settings.php' ) ) {
							@ unlink($skins_dir.$file.'/colors.php');
							@ copy($skins_dir.'default/old_colors.php', $skins_dir.$file.'/colors.php');
							$content = file_get_contents($skins_dir.$file.'/xml.php');
							$pos = strpos($content,'/../../flash-album-gallery/flag-config.php');
							if($pos === false) {
								$content = str_replace('/../../flag-config.php','/../../flash-album-gallery/flag-config.php',$content);
								$fp = fopen($skins_dir.$file.'/xml.php','w');
								fwrite($fp,$content);
								fclose($fp);
							}
						}
					}
				}
			}
		}
	}
	@closedir( $open_skins_dir );
	@closedir( $skins_subdir );

	$flag_options['skinsDirABS'] = $skins_dir;
	$flag_options['skinsDirURL'] = WP_PLUGIN_URL . '/flagallery-skins/';
	update_option('flag_options', $flag_options);

	return true;
}
?>