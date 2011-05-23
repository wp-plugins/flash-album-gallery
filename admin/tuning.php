<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

function flag_tune($show_error=true) {
	/* Move skins outside the plugin folder */
	$flag_options = get_option('flag_options');
	$skins_dir = str_replace("\\","/", WP_PLUGIN_DIR . '/flagallery-skins/' );
	$old_skins_dir = FLAG_ABSPATH . 'skins/';

	$flag_options['skinsDirABS'] = $skins_dir;
	$flag_options['skinsDirURL'] = WP_PLUGIN_URL . '/flagallery-skins/';
	update_option('flag_options', $flag_options);
	
	$errors = '';
	// check for main folder
	if ( !wp_mkdir_p( $skins_dir ) ) {
			$errors .= __('Directory <strong>"', 'flag').$skins_dir.__('"</strong> doesn\'t exist. Please create first the <strong>"flagallery-skins"</strong> folder!', 'flag').'<br />';
	} else {
		// check for permission settings, Safe mode limitations are not taken into account. 
		if ( !is_writeable( $skins_dir ) ) {
			$errors .= __('Directory <strong>"', 'flag').$skins_dir.__('"</strong> is not writeable!', 'flag').'<br />';
		} else {
			
			// Files in flash-album-gallery/skins directory
			$open_old_skins_dir = @opendir( $old_skins_dir);
			if ( $open_old_skins_dir ) {
				while (($file = readdir( $open_old_skins_dir ) ) !== false ) {
					if ( substr($file, 0, 1) == '.' )
						continue;
					if ( is_dir( $old_skins_dir.$file ) ) {
						if( is_dir( $skins_dir.$file ) ) {
							flagGallery::flagFolderDelete( $skins_dir.$file );
						}
						if ( !@rename($old_skins_dir.$file, $skins_dir.$file) ) {
							$errors .= sprintf(__('Failed to move file %1$s to %2$s','flag'), 
								'<strong>'.$old_skins_dir.$file.'</strong>', $skins_dir.$file).'<br />';
						}
					}
				}
			}
			@closedir( $open_old_skins_dir );

			// Files in flagallery-skins directory
			$open_skins_dir = @opendir( $skins_dir);
			if ( $open_skins_dir ) {
				while (($file = readdir( $open_skins_dir ) ) !== false ) {
					if ( substr($file, 0, 1) == '.' )
						continue;
					if ( is_dir( $skins_dir.$file ) ) {
						$skins_subdir = @opendir( $skins_dir.$file );
						if ( $skins_subdir ) {
							while (($subfile = readdir( $skins_subdir ) ) !== false ) {
								if ( substr($subfile, 0, 1) == '.' )
									continue;
								if ( basename($subfile, '.png') == $file ) {
									if($show_error) {
										if ( !@rename($skins_dir.$file.'/'.$subfile, $skins_dir.$file.'/screenshot.png') ) {
											$errors .= sprintf(__('Failed to rename %1$s to %2$s','flag'), 
												'<strong>'.$skins_dir.$file.'/'.$subfile.'</strong>', $skins_dir.$file.'/screenshot.png').'<br />';
										} 
									} else {
										$errors = 1;
										break;
									}
								}
							}
							if( !file_exists( $skins_dir.$file.'/settings.php' ) ) {
								if( file_exists( $skins_dir.$file.'xml.php' ) ) {
									if($show_error) {
										if ( !@copy($skins_dir.'default/old_colors.php', $skins_dir.$file.'/colors.php') ) {
											$errors .= sprintf(__('Failed to copy and rename %1$s to %2$s','flag'), 
												$skins_dir.'<strong>default/old_colors.php</strong>', $skins_dir.'<strong>'.$file.'/colors.php</strong>').'<br />';
										}
										$content = file_get_contents($skins_dir.$file.'/xml.php');
										$pos = strpos($content,'/../../flash-album-gallery/flag-config.php');
										if($pos === false) {
											$content = str_replace('/../../flag-config.php','/../../flash-album-gallery/flag-config.php',$content);
											$fp = fopen($skins_dir.$file.'/xml.php','w');
											if( fwrite($fp,$content) === FALSE ) {
												$errors .= sprintf(__("Failed to search string '/../../flag-config.php' and replace with '/../../flash-album-gallery/flag-config.php' in file '%1$s'",'flag'), 
													$skins_dir.$file.'/xml.php').'<br />';
											}
											fclose($fp);
										}
									} else {
										$errors = 1;
										break;
									}
								}
							}
						}
					}
				}
			}
			@closedir( $open_skins_dir );
			@closedir( $skins_subdir );

		}
	}

	if ( $errors != '') { 
		if($show_error)
			flagGallery::show_error($errors); 
			
		echo $errors;
		return false;
	}

		echo $errors;
	return true;
}
?>