<?php

add_action('wp_ajax_flag_ajax_operation', 'flag_ajax_operation' );

function flag_ajax_operation() {
		
		global $wpdb;

		// if nonce is not correct it returns -1
		check_ajax_referer( "flag-ajax" );
		
		// check for correct capability
		if ( !is_user_logged_in() )
			die('-1');
		
		// check for correct FlAG capability
		if ( !current_user_can('FlAG Upload images') || !current_user_can('FlAG Manage gallery') ) 
			die('-1');	

		// include the flag function
		include_once (dirname (__FILE__). '/functions.php');

		// Get the image id
		if ( isset($_POST['image'])) {
			$id = (int) $_POST['image'];
			// let's get the image data
			$picture = flagdb::find_image($id);
			// what do you want to do ?		
			switch ( $_POST['operation'] ) {
				case 'create_thumbnail' :
					$result = flagAdmin::create_thumbnail($picture);
				break;
				case 'resize_image' :
					$result = flagAdmin::resize_image($picture);
				break;
				case 'set_watermark' :
					$result = flagAdmin::set_watermark($picture);
				break;
				default :
					die('-1');	
				break;		
			}
			// A success should return a '1'
			die ($result);
		}
		
		// The script should never stop here
		die('0');
}

add_action('wp_ajax_createNewThumb', 'createNewThumb');
	
	function createNewThumb() {
		
		global $wpdb;
		
		// check for correct capability
		if ( !is_user_logged_in() )
			die('-1');
		// check for correct FlAG capability
		if ( !current_user_can('FlAG Manage gallery') ) 
			die('-1');	
			
		require_once( dirname( dirname(__FILE__) ) . '/flag-config.php');
		include_once( flagGallery::graphic_library() );
		
		$flag_options=get_option('flag_options');
		
		$id 	 = (int) $_POST['id'];
		$picture = flagdb::find_image($id);

		$x = round( $_POST['x'] * $_POST['rr'], 0);
		$y = round( $_POST['y'] * $_POST['rr'], 0);
		$w = round( $_POST['w'] * $_POST['rr'], 0);
		$h = round( $_POST['h'] * $_POST['rr'], 0);
		
		$thumb = new flag_Thumbnail($picture->imagePath, TRUE);
		
		$thumb->crop($x, $y, $w, $h);
		
		$thumb_filename = $picture->thumbPath;

		if ($flag_options['thumbFix'])  {
			if ($thumb->currentDimensions['height'] > $thumb->currentDimensions['width']) {
				$thumb->resize($flag_options['thumbWidth'], 0);
			} else {
				$thumb->resize(0,$flag_options['thumbHeight']);	
			}
		} else {
			$thumb->resize($flag_options['thumbWidth'],$flag_options['thumbHeight'],$flag_options['thumbResampleMode']);	
		}
		
		if ( $thumb->save($thumb_filename,100)) {
			echo "OK";
		} else {
			header('HTTP/1.1 500 Internal Server Error');			
			echo "KO";
		}
		
		exit();
		
	}
	
?>