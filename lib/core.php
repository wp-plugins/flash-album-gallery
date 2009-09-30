<?php
/**
* Main PHP class for the WordPress plugin Flash Album Gallery
* 
*/
class flagGallery {
	
	/**
	* Show a error messages
	*/
	function show_error($message) {
		echo '<div class="wrap"><h2></h2><div class="error" id="error"><p>' . $message . '</p></div></div>' . "\n";
	}
	
	/**
	* Show a system messages
	*/
	function show_message($message) {
		echo '<div class="wrap"><h2></h2><div class="updated fade" id="message"><p>' . $message . '</p></div></div>' . "\n";
	}

	/**
	* get the thumbnail url to the image
	*/
	function get_thumbnail_url($imageID, $picturepath = '', $fileName = ''){
	
		// get the complete url to the thumbnail
		global $wpdb;
		
		// safety first
		$imageID = (int) $imageID;
		
		// get gallery values
		if ( empty($fileName) ) {
			list($fileName, $picturepath ) = $wpdb->get_row("SELECT p.filename, g.path FROM $wpdb->flagpictures AS p INNER JOIN $wpdb->flaggallery AS g ON (p.galleryid = g.gid) WHERE p.pid = '$imageID' ", ARRAY_N);
		}
		
		if ( empty($picturepath) ) {
			$picturepath = $wpdb->get_var("SELECT g.path FROM $wpdb->flagpictures AS p INNER JOIN $wpdb->flaggallery AS g ON (p.galleryid = g.gid) WHERE p.pid = '$imageID' ");
		}
		
		// set gallery url
		$folder_url 	= get_option ('siteurl') . '/' . $picturepath.flagGallery::get_thumbnail_folder($picturepath, FALSE);
		$thumbnailURL	= $folder_url . 'thumbs_' . $fileName;
		
		return $thumbnailURL;
	}
	
	/**
	* get the complete url to the image
	*/
	function get_image_url($imageID, $picturepath = '', $fileName = '') {		
		global $wpdb;

		// safety first
		$imageID = (int) $imageID;
		
		// get gallery values
		if (empty($fileName)) {
			list($fileName, $picturepath ) = $wpdb->get_row("SELECT p.filename, g.path FROM $wpdb->flagpictures AS p INNER JOIN $wpdb->flaggallery AS g ON (p.galleryid = g.gid) WHERE p.pid = '$imageID' ", ARRAY_N);
		}

		if (empty($picturepath)) {
			$picturepath = $wpdb->get_var("SELECT g.path FROM $wpdb->flagpictures AS p INNER JOIN $wpdb->flaggallery AS g ON (p.galleryid = g.gid) WHERE p.pid = '$imageID' ");
		}
		
		// set gallery url
		$imageURL 	= get_option ('siteurl') . '/' . $picturepath . '/' . $fileName;
		
		return $imageURL;	
	}

	/**
	* flagGallery::get_thumbnail_folder()
	* 
	* @param mixed $gallerypath
	* @param bool $include_Abspath
	* @return string $foldername
	*/
	function create_thumbnail_folder($gallerypath, $include_Abspath = TRUE) {
		if (!$include_Abspath) {
			$gallerypath = WINABSPATH . $gallerypath;
		}
		
		if (!file_exists($gallerypath)) {
			return FALSE;
		}
		
		if (is_dir($gallerypath . '/thumbs/')) {
			return '/thumbs/';
		}
		
		if (is_admin()) {
			if (!is_dir($gallerypath . '/thumbs/')) {
				if ( !wp_mkdir_p($gallerypath . '/thumbs/') ) {
					if (SAFE_MODE) {
						flagAdmin::check_safemode($gallerypath . '/thumbs/');	
					} else {
						flagGallery::show_error(__('Unable to create directory ', 'flag') . $gallerypath . '/thumbs !');
					}
					return FALSE;
				}
				return '/thumbs/';
			}
		}
		
		return FALSE;
		
	}

	/**
	* flagGallery::get_thumbnail_folder()
	* 
	* @param mixed $gallerypath
	* @param bool $include_Abspath
	* @deprecated use create_thumbnail_folder() if needed;
	* @return string $foldername
	*/
	function get_thumbnail_folder($gallerypath, $include_Abspath = TRUE) {
		return flagGallery::create_thumbnail_folder($gallerypath, $include_Abspath);
	}
	
	/**
	* flagGallery::get_thumbnail_prefix() - obsolete
	* 
	* @param string $gallerypath
	* @param bool   $include_Abspath
	* @deprecated prefix is now fixed to "thumbs_";
	* @return string  "thumbs_";
	*/
	function get_thumbnail_prefix($gallerypath, $include_Abspath = TRUE) {
		return 'thumbs_';		
	}
	
	/**
	 * flagGallery::graphic_library() - switch between GD and ImageMagick
	 * 
	 * @return path to the selected library
	 */
	function graphic_library() {
		
		return FLAG_ABSPATH . '/lib/gd.thumbnail.inc.php';
		
	}
	
	/**
	 * Support for i18n with polyglot or qtrans
	 * 
	 * @param string $in
	 * @return string $in localized
	 */
	function i18n($in) {
		
		if ( function_exists( 'langswitch_filter_langs_with_message' ) )
			$in = langswitch_filter_langs_with_message($in);
				
		if ( function_exists( 'polyglot_filter' ))
			$in = polyglot_filter($in);
		
		if ( function_exists( 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage' ))
			$in = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($in);
		
		$in = apply_filters('localization', $in);
		
		return $in;
	}
	
	/**
	 * Check the memory_limit and calculate a recommended memory size
	 * 
	 * @return string message about recommended image size
	 */
	function check_memory_limit() {

		if ( (function_exists('memory_get_usage')) && (ini_get('memory_limit')) ) {
			
			// get memory limit
			$memory_limit = ini_get('memory_limit');
			if ($memory_limit != '')
				$memory_limit = substr($memory_limit, 0, -1) * 1024 * 1024;
			
			// calculate the free memory 	
			$freeMemory = $memory_limit - memory_get_usage();
			
			// build the test sizes
			$sizes = array();
			$sizes[] = array ( 'width' => 800, 'height' => 600);
			$sizes[] = array ( 'width' => 1024, 'height' => 768);
			$sizes[] = array ( 'width' => 1280, 'height' => 960);  // 1MP	
			$sizes[] = array ( 'width' => 1600, 'height' => 1200); // 2MP
			$sizes[] = array ( 'width' => 2016, 'height' => 1512); // 3MP
			$sizes[] = array ( 'width' => 2272, 'height' => 1704); // 4MP
			$sizes[] = array ( 'width' => 2560, 'height' => 1920); // 5MP
			
			// test the classic sizes
			foreach ($sizes as $size){
				// very, very rough estimation
				if ($freeMemory < round( $size['width'] * $size['height'] * 5.09 )) {
                	$result = sprintf(  __( 'Note : Based on your server memory limit you should not upload larger images then <strong>%d x %d</strong> pixel' ), $size['width'], $size['height']); 
					return $result;
				}
			}
		}
		return;
	}
	
}

?>