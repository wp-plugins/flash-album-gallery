<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

/**
 * flagAdmin - Class for admin operation
 */
class flagAdmin{

	/**
	 * create a new gallery & folder
	 */
	function create_gallery($gallerytitle, $defaultpath) {
		// create a new gallery & folder
		global $wpdb, $user_ID;
 
		// get the current user ID
		get_currentuserinfo();

		//cleanup pathname
		$galleryname = apply_filters('flag_gallery_name', $gallerytitle);
		$gallerytitle = attribute_escape($gallerytitle);
		$flagpath = $defaultpath . $galleryname;
		$flagRoot = WINABSPATH . $defaultpath;
		$txt = '';
		
		// No gallery name ?
		if (empty($galleryname)) {	
			flagGallery::show_error( __('No valid gallery name!', 'flag') );
			return false;
		}
		
		// check for main folder
		if ( !is_dir($flagRoot) ) {
			if ( !wp_mkdir_p( $flagRoot ) ) {
				$txt  = __('Directory', 'flag').' <strong>' . $defaultpath . '</strong> '.__('didn\'t exist. Please create first the main gallery folder ', 'flag').'!<br />';
				$txt .= __('Check this link, if you didn\'t know how to set the permission :', 'flag').' <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a> ';
				flagGallery::show_error($txt);
				return false;
			}
		}

		// check for permission settings, Safe mode limitations are not taken into account. 
		if ( !is_writeable( $flagRoot ) ) {
			$txt  = __('Directory', 'flag').' <strong>' . $defaultpath . '</strong> '.__('is not writeable !', 'flag').'<br />';
			$txt .= __('Check this link, if you didn\'t know how to set the permission :', 'flag').' <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a> ';
			flagGallery::show_error($txt);
			return false;
		}
		
		// 1. Create new gallery folder
		if ( !is_dir(WINABSPATH . $flagpath) ) {
			if ( !wp_mkdir_p (WINABSPATH . $flagpath) ) 
				$txt  = __('Unable to create directory ', 'flag').$flagpath.'!<br />';
		}
		
		// 2. Check folder permission
		if ( !is_writeable(WINABSPATH . $flagpath ) )
			$txt .= __('Directory', 'flag').' <strong>'.$flagpath.'</strong> '.__('is not writeable !', 'flag').'<br />';

		// 3. Now create "thumbs" folder inside
		if ( !is_dir(WINABSPATH . $flagpath . '/thumbs') ) {				
			if ( !wp_mkdir_p ( WINABSPATH . $flagpath . '/thumbs') ) 
				$txt .= __('Unable to create directory ', 'flag').' <strong>' . $flagpath . '/thumbs !</strong>';
		}
		
		if (SAFE_MODE) {
			$help  = __('The server setting Safe-Mode is on !', 'flag');	
			$help .= '<br />'.__('If you have problems, please create directory', 'flag').' <strong>' . $flagpath . '</strong> ';	
			$help .= __('and the thumbnails directory', 'flag').' <strong>' . $flagpath . '/thumbs</strong> '.__('with permission 777 manually !', 'flag');
			flagGallery::show_message($help);
		}
		
		// show an error message			
		if ( !empty($txt) ) {
			if (SAFE_MODE) {
			// for safe_mode , better delete folder, both folder must be created manually
				@rmdir(WINABSPATH . $flagpath . '/thumbs');
				@rmdir(WINABSPATH . $flagpath);
			}
			flagGallery::show_error($txt);
			return false;
		}
		
		$result = $wpdb->get_var("SELECT name FROM $wpdb->flaggallery WHERE name = '$galleryname' ");
		
		if ($result) {
			flagGallery::show_error( __ngettext( 'Gallery', 'Galleries', 1, 'flag' ) .' <strong>' . $galleryname . '</strong> '.__('already exists', 'flag'));
			return false;			
		} else { 
			$result = $wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->flaggallery (name, path, title, author) VALUES (%s, %s, %s, %s)", $galleryname, $flagpath, $gallerytitle , $user_ID) );
			if ($result) {
				$message  = __('Gallery \'%1$s\' successfully created.<br/>You can show this gallery with the tag %2$s.<br/>','flag');
				$message  = sprintf($message, stripcslashes($gallerytitle), '[flagallery gid=' . $wpdb->insert_id . ' name="' . stripcslashes($gallerytitle) . '"]');
				$message .= '<a href="' . admin_url() . 'admin.php?page=flag-manage-gallery&mode=edit&gid=' . $wpdb->insert_id . '" >';
				$message .= __('Edit gallery','flag');
				$message .= '</a>';
				
				flagGallery::show_message($message); 
			}
			return true;
		} 
	}
	
	/**
	 * flagAdmin::import_gallery()
	 * TODO: Check permission of existing thumb folder & images
	 * 
	 * @class flagAdmin
	 * @param string $galleryfolder contains relative path
	 * @return
	 */
	function import_gallery($galleryfolder) {
		
		global $wpdb, $user_ID;

		// get the current user ID
		get_currentuserinfo();
		
		$created_msg = '';
		
		// remove trailing slash at the end, if somebody use it
		$galleryfolder = rtrim($galleryfolder, '/');
		$gallerypath = WINABSPATH . $galleryfolder;
		
		if (!is_dir($gallerypath)) {
			flagGallery::show_error(__('Directory', 'flag').' <strong>'.$gallerypath.'</strong> '.__('doesn&#96;t exist!', 'flag'));
			return ;
		}
		
		// read list of images
		$new_imageslist = flagAdmin::scandir($gallerypath);
		if (empty($new_imageslist)) {
			flagGallery::show_message(__('Directory', 'flag').' <strong>'.$gallerypath.'</strong> '.__('contains no pictures', 'flag'));
			return;
		}
		
		// check & create thumbnail folder
		if ( !flagGallery::get_thumbnail_folder($gallerypath) )
			return;
		
		// take folder name as gallery name		
		$galleryname = basename($galleryfolder);
		$galleryname = apply_filters('flag_gallery_name', $galleryname);
		
		// check for existing gallery folder
		$gallery_id = $wpdb->get_var("SELECT gid FROM $wpdb->flaggallery WHERE path = '$galleryfolder' ");

		if (!$gallery_id) {
			$result = $wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->flaggallery (name, path, title, author) VALUES (%s, %s, %s, %s)", $galleryname, $galleryfolder, $galleryname , $user_ID) );
			if (!$result) {
				flagGallery::show_error(__('Database error. Could not add gallery!','flag'));
				return;
			}
			$created_msg = __ngettext( 'Gallery', 'Galleries', 1, 'flag' ) . ' <strong>' . $galleryname . '</strong> ' . __('successfully created!','flag') . '<br />';
			$gallery_id  = $wpdb->insert_id;  // get index_id
		}
		
		// Look for existing image list
		$old_imageslist = $wpdb->get_col("SELECT filename FROM $wpdb->flagpictures WHERE galleryid = '$gallery_id' ");
		
		// if no images are there, create empty array
		if ($old_imageslist == NULL) 
			$old_imageslist = array();
			
		// check difference
		$new_images = array_diff($new_imageslist, $old_imageslist);
		
		// all images must be valid files
		foreach($new_images as $key => $picture) {
			if (!@getimagesize($gallerypath . '/' . $picture) ) {
				unset($new_images[$key]);
				@unlink($gallerypath . '/' . $picture);				
			}
		}
				
		// add images to database		
		$image_ids = flagAdmin::add_Images($gallery_id, $new_images);
		
		//add the preview image if needed
		flagAdmin::set_gallery_preview ( $gallery_id );

		// now create thumbnails
		flagAdmin::do_ajax_operation( 'create_thumbnail' , $image_ids, __('Create new thumbnails','flag') );
		
		//TODO:Message will not shown, because AJAX routine require more time, message should be passed to AJAX
		flagGallery::show_message( $created_msg . count($image_ids) .__(' picture(s) successfully added','flag') );
		
		return;

	}

	// **************************************************************
	function scandir($dirname = '.') { 
		// thx to php.net :-)
		$ext = array('jpeg', 'jpg', 'png', 'gif'); 
		$files = array(); 
		if($handle = opendir($dirname)) { 
		   while(false !== ($file = readdir($handle))) 
		       for($i=0;$i<sizeof($ext);$i++) 
		           if(stristr($file, '.' . $ext[$i])) 
		               $files[] = utf8_encode($file); 
		   closedir($handle); 
		} 
		sort($files);
		return ($files); 
	} 
	
	/**
	 * flagAdmin::createThumbnail() - function to create or recreate a thumbnail
	 * 
	 * @param object | int $image contain all information about the image or the id
	 * @return string result code
	 */
	function create_thumbnail($image) {
		
		global $flag;
		
		if(! class_exists('flag_Thumbnail'))
			require_once( flagGallery::graphic_library() );
		
		if ( is_numeric($image) )
			$image = flagdb::find_image( $image );

		if ( !is_object($image) ) 
			return __('Object didn\'t contain correct data','flag');
		
		// check for existing thumbnail
		if (file_exists($image->thumbPath))
			if (!is_writable($image->thumbPath))
				return $image->filename . __(' is not writeable ','flag');

		$thumb = new flag_Thumbnail($image->imagePath, TRUE);

		// skip if file is not there
		if (!$thumb->error) {
			if ($flag->options['thumbCrop']) {
				
				// THX to Kees de Bruin, better thumbnails if portrait format
				$width = $flag->options['thumbWidth'];
				$height = $flag->options['thumbHeight'];
				$curwidth = $thumb->currentDimensions['width'];
				$curheight = $thumb->currentDimensions['height'];
				if ($curwidth > $curheight) {
					$aspect = (100 * $curwidth) / $curheight;
				} else {
					$aspect = (100 * $curheight) / $curwidth;
				}
				$width = round(($width * $aspect) / 100);
				$height = round(($height * $aspect) / 100);

				$thumb->resize($width,$height);
				$thumb->cropFromCenter($width);
			} 
			elseif ($flag->options['thumbFix'])  {
				// check for portrait format
				if ($thumb->currentDimensions['height'] > $thumb->currentDimensions['width']) {
					$thumb->resize($flag->options['thumbWidth'], 0);
					// get optimal y startpos
					$ypos = ($thumb->currentDimensions['height'] - $flag->options['thumbHeight']) / 2;
					$thumb->crop(0, $ypos, $flag->options['thumbWidth'],$flag->options['thumbHeight']);	
				} else {
					$thumb->resize(0,$flag->options['thumbHeight']);	
					// get optimal x startpos
					$xpos = ($thumb->currentDimensions['width'] - $flag->options['thumbWidth']) / 2;
					$thumb->crop($xpos, 0, $flag->options['thumbWidth'],$flag->options['thumbHeight']);	
				}
			} else {
				$thumb->resize($flag->options['thumbWidth'],$flag->options['thumbHeight']);	
			}
			
			// save the new thumbnail
			$thumb->save($image->thumbPath, $flag->options['thumbQuality']);
			flagAdmin::chmod ($image->thumbPath); 
		} 
				
		$thumb->destruct();
		
		if ( !empty($thumb->errmsg) )
			return ' <strong>' . $image->filename . ' (Error : '.$thumb->errmsg .')</strong>';
		
		// success
		return '1'; 
	}
	
	/**
	 * flagAdmin::resize_image() - create a new image, based on the height /width
	 * 
	 * @param object | int $image contain all information about the image or the id
	 * @param integer $width optional 
	 * @param integer $height optional
	 * @return string result code
	 */
	function resize_image($image, $width = 0, $height = 0) {
		
		global $flag;
		
		if(! class_exists('flag_Thumbnail'))
			require_once( flagGallery::graphic_library() );

		if ( is_numeric($image) )
			$image = flagdb::find_image( $image );
		
		if ( !is_object($image) ) 
			return __('Object didn\'t contain correct data','flag');	

		// if no parameter is set, take global settings
		$width  = ($width  == 0) ? $flag->options['imgWidth']  : $width;
		$height = ($height == 0) ? $flag->options['imgHeight'] : $height;
		
		if (!is_writable($image->imagePath))
			return ' <strong>' . $image->filename . __(' is not writeable','flag') . '</strong>';
		
		$file = new flag_Thumbnail($image->imagePath, TRUE);

		// skip if file is not there
		if (!$file->error) {
			$file->resize($width, $height, 4);
			$file->save($image->imagePath, $flag->options['imgQuality']);
			$file->destruct();
		} else {
            $file->destruct();
			return ' <strong>' . $image->filename . ' (Error : ' . $file->errmsg . ')</strong>';
		}

		return '1';
	}

	// **************************************************************
	function add_Images($galleryID, $imageslist) {
		// add images to database		
		global $wpdb;
		
		$alttext = '';
		$image_ids = array();
		
		if ( is_array($imageslist) ) {
			foreach($imageslist as $picture) {
				$result = $wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->flagpictures (galleryid, filename, alttext) VALUES (%s, %s, %s)", $galleryID, $picture, $alttext) );
				$pic_id = (int) $wpdb->insert_id;
				if ($result) 
					$image_ids[] = $pic_id;

				// add the metadata
				flagAdmin::import_MetaData($pic_id);

				// action hook for post process after the image is added to the database
				$image = array( 'id' => $pic_id, 'filename' => $picture, 'galleryID' => $galleryID);
				do_action('flag_added_new_image', $image);
									
			} 
		} // is_array
		
		return $image_ids;
		
	}

	/**
	 * Import some metadata into the database (if avialable)
	 * 
	 * @class flagAdmin
	 * @param array|int $imagesIds
	 * @return bool
	 */
	function import_MetaData($imagesIds) {
			
		global $wpdb;
		
		require_once(FLAG_ABSPATH . '/lib/image.php');
		
		if (!is_array($imagesIds))
			$imagesIds = array($imagesIds);
		
		foreach($imagesIds as $pic_id) {
			$picture = flagdb::find_image($pic_id);
			if (!$picture->error) {

				$meta = flagAdmin::get_MetaData($picture->imagePath);
				
				// get the title
				if (!$alttext = $meta['title'])
					$alttext = $picture->alttext;
				// get the caption / description field
				if (!$description = $meta['caption'])
					$description = $picture->description;
				// get the file date/time from exif
				$timestamp = $meta['timestamp'];
				// update database
				$result = $wpdb->query( $wpdb->prepare("UPDATE $wpdb->flagpictures SET alttext = %s, description = %s, imagedate = %s WHERE pid = %d", attribute_escape($alttext), attribute_escape($description), $timestamp, $pic_id) );
			}// error check
		}
		
		return true;
		
	}

	/**
	 * flagAdmin::get_MetaData()
	 * 
	 * @class flagAdmin
	 * @require Meta class
	 * @param string $picPath must be Gallery absPath + filename
	 * @return array metadata
	 */
	function get_MetaData($picPath) {
		
		require_once(FLAG_ABSPATH . '/lib/meta.php');
		
		$meta = array();

		$pdata = new flagMeta($picPath);
		$meta['title'] = $pdata->get_META('title');		
		$meta['caption'] = $pdata->get_META('caption');	
		$meta['timestamp'] = $pdata->get_date_time();	
		
		return $meta;
		
	}


	// **************************************************************
	function getOnlyImages($p_event, $p_header)	{
		
		$info = pathinfo($p_header['filename']);
		// check for extension
		$ext = array('jpeg', 'jpg', 'png', 'gif'); 
		if ( in_array( strtolower($info['extension']), $ext) ) {
			// For MAC skip the ".image" files
			if ($info['basename']{0} ==  '.' ) 
				return 0;
			else 
				return 1;
		}
		// ----- all other files are skipped
		else {
		  return 0;
		}
	}

	/**
	 * Function for uploading of images via the upload form
	 * 
	 * @class flagAdmin
	 * @return void
	 */
	function upload_images() {
		
		global $wpdb;
		
		// Images must be an array
		$imageslist = array();

		// get selected gallery
		$galleryID = (int) $_POST['galleryselect'];

		if ($galleryID == 0) {
			flagGallery::show_error(__('No gallery selected !','flag'));
			return;	
		}

		// get the path to the gallery	
		$gallery = flagdb::find_gallery($galleryID);

		if ( empty($gallery->path) ){
			flagGallery::show_error(__('Failure in database, no gallery path set !','flag'));
			return;
		} 
				
		// read list of images
		$dirlist = flagAdmin::scandir(WINABSPATH.$gallerypath);
		
		$imagefiles = $_FILES['imagefiles'];
		
		if (is_array($imagefiles)) {
			foreach ($imagefiles['name'] as $key => $value) {

				// look only for uploded files
				if ($imagefiles['error'][$key] == 0) {
					
					$temp_file = $imagefiles['tmp_name'][$key];
					
					//clean filename and extract extension
					$filepart = flagGallery::fileinfo( $imagefiles['name'][$key] );
					$filename = $filepart['basename'];
						
					// check for allowed extension and if it's an image file
					$ext = array('jpg', 'png', 'gif'); 
					if ( !in_array($filepart['extension'], $ext) || !@getimagesize($temp_file) ){ 
						flagGallery::show_error('<strong>' . $imagefiles['name'][$key] . ' </strong>' . __('is no valid image file!','flag'));
						continue;
					}
	
					// check if this filename already exist in the folder
					$i = 0;
					while ( in_array( $filename, $dirlist ) ) {
						$filename = $filepart['filename'] . '_' . $i++ . '.' .$filepart['extension'];
					}
					
					$dest_file = $gallery->abspath . '/' . $filename;
					
					//check for folder permission
					if ( !is_writeable($gallery->abspath) ) {
						$message = sprintf(__('Unable to write to directory %s. Is this directory writable by the server?', 'flag'), $gallery->abspath);
						flagGallery::show_error($message);
						return;				
					}
					
					// save temp file to gallery
					if ( !@move_uploaded_file($temp_file, $dest_file) ){
						flagGallery::show_error(__('Error, the file could not moved to : ','flag') . $dest_file);
						flagAdmin::check_safemode( $gallery->abspath );		
						continue;
					} 
					if ( !flagAdmin::chmod($dest_file) ) {
						flagGallery::show_error(__('Error, the file permissions could not set','flag'));
						continue;
					}
					
					// add to imagelist & dirlist
					$imageslist[] = $filename;
					$dirlist[] = $filename;
				}
			}
		}
		
		if (count($imageslist) > 0) {
			
			// add images to database		
			$image_ids = flagAdmin::add_Images($galleryID, $imageslist);

			//create thumbnails
			flagAdmin::do_ajax_operation( 'create_thumbnail' , $image_ids, __('Create new thumbnails','flag') );
			//add the preview image if needed
			flagAdmin::set_gallery_preview ( $galleryID );
			
			flagGallery::show_message( count($image_ids) . __(' Image(s) successfully added','flag'));
		}
		
		return;

	} // end function
	
	// **************************************************************
	function swfupload_image($galleryID = 0) {
		// This function is called by the swfupload
		global $wpdb;
		
		if ($galleryID == 0) {
			@unlink($temp_file);		
			return __('No gallery selected !','flag');;
		}

		// Check the upload
		if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) 
			return __('Invalid upload. Error Code : ','flag') . $_FILES["Filedata"]["error"];

		// get the filename and extension
		$temp_file = $_FILES["Filedata"]['tmp_name'];
		$filepart = pathinfo ( strtolower($_FILES['Filedata']['name']) );
		// required until PHP 5.2.0
		$filepart['filename'] = substr($filepart['basename'],0 ,strlen($filepart['basename']) - (strlen($filepart["extension"]) + 1) );
		$filename = sanitize_title($filepart['filename']) . '.' . $filepart['extension'];

		// check for allowed extension
		$ext = array('jpeg', 'jpg', 'png', 'gif'); 
		if (!in_array($filepart['extension'], $ext))
			return $_FILES[$key]['name'] . __('is no valid image file!','flag');

		// get the path to the gallery	
		$gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->flaggallery WHERE gid = '$galleryID' ");
		if (!$gallerypath){
			@unlink($temp_file);		
			return __('Failure in database, no gallery path set !','flag');
		} 

		// read list of images
		$imageslist = flagAdmin::scandir( WINABSPATH.$gallerypath );

		// check if this filename already exist
		$i = 0;
		while (in_array($filename,$imageslist)) {
			$filename = sanitize_title($filepart['filename']) . '_' . $i++ . '.' . $filepart['extension'];
		}
		
		$dest_file = WINABSPATH . $gallerypath . '/' . $filename;
				
		// save temp file to gallery
		if ( !@move_uploaded_file($_FILES["Filedata"]['tmp_name'], $dest_file) ){
			flagAdmin::check_safemode(WINABSPATH.$gallerypath);	
			return __('Error, the file could not moved to : ','flag').$dest_file;
		} 
		
		if ( !flagAdmin::chmod($dest_file) )
			return __('Error, the file permissions could not set','flag');
		
		return '0';
	}	
	
	// **************************************************************
	function chmod($filename = '') {
		// Set correct file permissions (taken from wp core)
		$stat = @ stat(dirname($filename));
		$perms = $stat['mode'] & 0007777;
		$perms = $perms & 0000666;
		if ( @chmod($filename, $perms) )
			return true;
			
		return false;
	}
	
	function check_safemode($foldername) {
		// Check UID in folder and Script
		// Read http://www.php.net/manual/en/features.safe-mode.php to understand safe_mode
		if ( SAFE_MODE ) {
			
			$script_uid = ( ini_get('safe_mode_gid') ) ? getmygid() : getmyuid();
			$folder_uid = fileowner($foldername);

			if ($script_uid != $folder_uid) {
				$message  = sprintf(__('SAFE MODE Restriction in effect! You need to create the folder <strong>%s</strong> manually','flag'), $foldername);
				$message .= '<br />' . sprintf(__('When safe_mode is on, PHP checks to see if the owner (%s) of the current script matches the owner (%s) of the file to be operated on by a file function or its directory','flag'), $script_uid, $folder_uid );
				flagGallery::show_error($message);
				return false;
			}
		}
		
		return true;
	}
	
	function can_manage_this_gallery($check_ID) {
		// check is the ID fit's to the user_ID'
		global $user_ID, $current_user, $wp_roles;
		
		// get the current user ID
		get_currentuserinfo();
 		
		if ( !current_user_can('FlAG Manage others gallery') ) {
			if ( $user_ID != $check_ID && $current_user->user_level != 10) {
				return false;
			}
		}
		
		return true;
	
	}
	
	/**
	 * Move images from one folder to another
	 *
	 * @param array|int $pic_ids ID's of the images
	 * @param int $dest_gid destination gallery
	 * @return void
	 */
	function move_images($pic_ids, $dest_gid) {

		$errors = '';
		$count = 0;

		if (!is_array($pic_ids))
			$pic_ids = array($pic_ids);
		
		// Get destination gallery
		$destination  = flagdb::find_gallery( $dest_gid );
		$dest_abspath = WINABSPATH . $destination->path;
		
		if ( $destination == null ) {
			flagGallery::show_error(__('The destination gallery does not exist','flag'));
			return;
		}
		
		// Check for folder permission
		if ( !is_writeable( $dest_abspath ) ) {
			$message = sprintf(__('Unable to write to directory %s. Is this directory writable by the server?', 'flag'), $dest_abspath );
			flagGallery::show_error($message);
			return;				
		}
				
		// Get pictures
		$images = flagdb::find_images_in_list($pic_ids);

		foreach ($images as $image) {		
			
			$i = 0;
			$tmp_prefix = '';
			
			$destination_file_name = $image->filename;
			// check if the filename already exist, then we add a copy_ prefix
			while (file_exists( $dest_abspath . '/' . $destination_file_name)) {
				$tmp_prefix = 'copy_' . ($i++) . '_';
				$destination_file_name = $tmp_prefix . $image->filename;
			}
			
			$destination_path = $dest_abspath . '/' . $destination_file_name;
			$destination_thumbnail = $dest_abspath . '/thumbs/thumbs_' . $destination_file_name;

			// Move files
			if ( !@rename($image->imagePath, $destination_path) ) {
				$errors .= sprintf(__('Failed to move image %1$s to %2$s','flag'), 
					'<strong>' . $image->filename . '</strong>', $destination_path) . '<br />';
				continue;				
			}
			
			// Move the thumbnail, if possible
			!@rename($image->thumbPath, $destination_thumbnail);
			
			// Change the gallery id in the database , maybe the filename
			if ( flagdb::update_image($image->pid, $dest_gid, $destination_file_name) )
				$count++;

		}

		if ( $errors != '' )
			flagGallery::show_error($errors);

		$link = '<a href="' . admin_url() . 'admin.php?page=flag-manage-gallery&mode=edit&gid=' . $destination->gid . '" >' . $destination->title . '</a>';
		$messages  = sprintf(__('Moved %1$s picture(s) to gallery : %2$s .','flag'), $count, $link);
		flagGallery::show_message($messages);

		return;
	}
	
	/**
	 * Copy images to another gallery
	 */
	function copy_images($pic_ids, $dest_gid) {
		
		$errors = $messages = '';
		
		if (!is_array($pic_ids))
			$pic_ids = array($pic_ids);
		
		// Get destination gallery
		$destination = flagdb::find_gallery( $dest_gid );
		if ( $destination == null ) {
			flagGallery::show_error(__('The destination gallery does not exist','flag'));
			return;
		}
		
		// Check for folder permission
		if (!is_writeable(WINABSPATH.$destination->path)) {
			$message = sprintf(__('Unable to write to directory %s. Is this directory writable by the server?', 'flag'), WINABSPATH.$destination->path);
			flagGallery::show_error($message);
			return;				
		}
				
		// Get pictures
		$images = flagdb::find_images_in_list($pic_ids);
		$destination_path = WINABSPATH . $destination->path;
		
		foreach ($images as $image) {		
			
			$i = 0;
			$tmp_prefix = ''; 
			$destination_file_name = $image->filename;
			while (file_exists($destination_path . '/' . $destination_file_name)) {
				$tmp_prefix = 'copy_' . ($i++) . '_';
				$destination_file_name = $tmp_prefix . $image->filename;
			}
			
			$destination_file_path = $destination_path . '/' . $destination_file_name;
			$destination_thumb_file_path = $destination_path . '/' . $image->thumbFolder . $image->thumbPrefix . $destination_file_name;

			// Copy files
			if ( !@copy($image->imagePath, $destination_file_path) ) {
				$errors .= sprintf(__('Failed to copy image %1$s to %2$s','flag'), 
					$image->filename, $destination_file_path) . '<br />';
				continue;				
			}
			
			// Copy the thumbnail if possible
			!@copy($image->thumbPath, $destination_thumb_file_path);
			
			// Create new database entry for the image
			$new_pid = flagdb::insert_image( $destination->gid, $destination_file_name, $image->alttext, $image->description);

			if (!isset($new_pid)) {				
				$errors .= sprintf(__('Failed to copy database row for picture %s','flag'), $image->pid) . '<br />';
				continue;				
			}
				
			if ( $tmp_prefix != '' ) {
				$messages .= sprintf(__('Image %1$s (%2$s) copied as image %3$s (%4$s) &raquo; The file already existed in the destination gallery.','flag'),
					 $image->pid, $image->filename, $new_pid, $destination_file_name) . '<br />';
			} else {
				$messages .= sprintf(__('Image %1$s (%2$s) copied as image %3$s (%4$s)','flag'),
					 $image->pid, $image->filename, $new_pid, $destination_file_name) . '<br />';
			}

		}
		
		// Finish by showing errors or success
		if ( $errors == '' ) {
			$link = '<a href="' . admin_url() . 'admin.php?page=flag-manage-gallery&mode=edit&gid=' . $destination->gid . '" >' . $destination->title . '</a>';
			$messages .= '<hr />' . sprintf(__('Copied %1$s picture(s) to gallery: %2$s .','flag'), count($images), $link);
		} 

		if ( $messages != '' )
			flagGallery::show_message($messages);

		if ( $errors != '' )
			flagGallery::show_error($errors);

		return;
	}
	
	function do_ajax_operation( $operation, $image_array, $title = '' ) {
		
		if ( !is_array($image_array) || empty($image_array) )
			return;

		$js_array  = implode('","', $image_array);
		
		// send out some JavaScript, which initate the ajax operation
		?>
		<script type="text/javascript">

			Images = new Array("<?php echo $js_array; ?>");

			flagAjaxOptions = {
				operation: "<?php echo $operation; ?>",
				ids: Images,		
			  	header: "<?php echo $title; ?>",
			  	maxStep: Images.length
			};
			
			jQuery(document).ready( function(){ 
				flagProgressBar.init( flagAjaxOptions );
				flagAjax.init( flagAjaxOptions );
			} );
		</script>
		
		<div id="progressbar_container" class="wrap"></div>
		
		<?php	
	}
	
	/**
	 * flagAdmin::set_gallery_preview() - define a preview pic after the first upload, can be changed in the gallery settings
	 * 
	 * @param int $galleryID
	 * @return
	 */
	function set_gallery_preview( $galleryID ) {
		
		global $wpdb;
		
		$imageID = $wpdb->get_var("SELECT previewpic FROM $wpdb->flaggallery WHERE gid = '$galleryID' ");
		
		// in the case no preview image is setup, we do this now
		if ($imageID == 0) {
			$firstImage = $wpdb->get_var("SELECT pid FROM $wpdb->flagpictures WHERE galleryid = '$galleryID' ORDER by pid DESC limit 0,1");
			if ($firstImage)
				$wpdb->query("UPDATE $wpdb->flaggallery SET previewpic = '$firstImage' WHERE gid = '$galleryID'");
		}
		
		return;
	}

} // END class flagAdmin

?>