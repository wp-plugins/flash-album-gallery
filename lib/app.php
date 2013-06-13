<?php
// include the flag function
@ require_once (dirname(dirname(__FILE__)). '/flag-config.php');

//$account_data='{"status":"KO"}';

if(isset($_REQUEST['account'])){
	global $wpdb, $flagdb;
	$account = json_decode(stripslashes($_REQUEST['account']));
	$flag_options = get_option ('flag_options');
	if($account->access_key != $flag_options['access_key']){ die('{"status":"key_error"}'); }
	if(isset($account->gid)){
		$gid = $wpdb->get_var($wpdb->prepare("SELECT gid FROM $wpdb->flaggallery WHERE gid = %d", $account->gid));
		if(!$gid){ die('{"status":"gallery_error"}'); }
		if(isset($account->delete_item_id)){
			$pid = intval($account->delete_item_id);
			$image = $flagdb->find_image( $pid );
			if ($image) {
				@unlink($image->imagePath);
				@unlink($image->thumbPath);
				$wpdb->query("DELETE FROM $wpdb->flagpictures WHERE pid = '{$image->pid}'");
			}
		}
		if(isset($_GET['account']) && isset($GLOBALS[ 'HTTP_RAW_POST_DATA' ])){
			$path = $wpdb->get_var("SELECT path FROM $wpdb->flaggallery WHERE gid = $gid");
			$file = ABSPATH . trailingslashit($path) . str_replace(' ', '_', current_time('mysql')) . '.jpg';
			$filename = basename($file);
			// Open temp file
			$out = fopen( $file, "wb" );
			if ( $out ) {
				if(fwrite( $out, $GLOBALS[ 'HTTP_RAW_POST_DATA' ] )){

					$alttext = isset($_GET['alttext'])? $wpdb->escape(flagallery_utf8_urldecode($_GET['alttext'])) : '';
					$description = isset($_GET['description'])? $wpdb->escape(flagallery_utf8_urldecode($_GET['description'])) : '';
					$exclude = intval($account->exclude);
					$location = $wpdb->escape($account->location);

					$wpdb->query( "INSERT INTO `{$wpdb->flagpictures}` (`galleryid`, `filename`, `alttext`, `description`, `exclude`, `location`) VALUES ('$gid', '$filename', '$alttext', '$description', '$exclude', '$location')" );

					// and give me the new id
					$pic_id = (int) $wpdb->insert_id;

					@ require_once (dirname(dirname(__FILE__)). '/admin/functions.php');
					// add the metadata
					flagAdmin::import_MetaData($pic_id);

					// action hook for post process after the image is added to the database
					$image = array( 'id' => $pic_id, 'filename' => $filename, 'galleryID' => $gid);
					do_action('flag_added_new_image', $image);

					$thumb = flagAdmin::create_thumbnail($pic_id);
					if($thumb == '1') {
						do_action('flag_thumbnail_created', $picture);
					} else {
						fclose( $out );
						die('{"status":"thumb_error: '.$thumb.'"}');
					}

				} else {
					@unlink($file);
					fclose( $out );
					die('{"status":"fwrite_error"}');
				}
				fclose( $out );
			} else {
				die('{"status":"fopen_error"}');
			}
		}
		$r['data'] = $wpdb->get_results("SELECT pid, galleryid, filename, description, alttext, link, UNIX_TIMESTAMP(imagedate) AS imagedate, UNIX_TIMESTAMP(modified) AS modified, sortorder, exclude, location, hitcounter, total_value, total_votes FROM $wpdb->flagpictures WHERE galleryid = '{$gid}' ORDER BY pid DESC");
		echo json_encode($r);
		die();
	} elseif(isset($account->updated_item)){
		$args = get_object_vars($account->updated_item);
		$pid = intval($args['pid']);
		$image = $flagdb->find_image( $pid );
		if ($image) {
			$flagdb->update_picture($args);
			$gid = intval($args['galleryid']);
			$r['data'] = $wpdb->get_results("SELECT pid, galleryid, filename, description, alttext, link, UNIX_TIMESTAMP(imagedate) AS imagedate, UNIX_TIMESTAMP(modified) AS modified, sortorder, exclude, location, hitcounter, total_value, total_votes FROM $wpdb->flagpictures WHERE galleryid = '{$gid}' ORDER BY pid DESC");
			echo json_encode($r);
			die();
		}
		die('{"status":"item_error"}');
	} elseif(isset($account->add_category)){
		$args = get_object_vars($account->add_category);
		$args['title'] = esc_attr( trim($args['title']) );
		if ( empty($args['title']) ) {
			$args['title'] = str_replace(' ', '_', current_time('mysql'));
		}
		@ require_once (dirname(dirname(__FILE__)). '/admin/functions.php');
		$defaultpath = $flag->options['galleryPath'];
		if(!flagAdmin::create_gallery($args, $defaultpath, $output = false)) {
			die('{"status":"gallery_error"}');
		}
	}

	//$account_data='{"status":"OK"}';
	$gallerylist = $wpdb->get_results( "SELECT * FROM $wpdb->flaggallery ORDER BY gid DESC", ARRAY_A );
	$r['data'] = array();
	if(count($gallerylist)){
		foreach($gallerylist as $gallery){
			$gid = (int) $gallery['gid'];
			$thepictures = $wpdb->get_var("SELECT filename FROM $wpdb->flagpictures WHERE galleryid = '{$gid}' ORDER BY pid DESC");
			$r['data'][] = $gallery + array( 'thumbnail' => $thepictures );
		}
	}
	echo json_encode($r);
	die();
}

function flagallery_utf8_urldecode($str) {
	$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
	return html_entity_decode($str,null,'UTF-8');
}
