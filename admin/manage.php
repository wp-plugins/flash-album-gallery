<?php  

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

class flagManageGallery {

	var $mode = 'main';
	var $gid = false;
	var $pid = false;
	var $base_page = 'admin.php?page=flag-manage-gallery';
	
	// initiate the manage page
	function flagManageGallery() {

		// GET variables
		$this->gid  = (int) $_GET['gid'];
		$this->pid  = (int) $_GET['pid'];	
		$this->mode = trim ($_GET['mode']);
		
		//Look for POST process
		if ( !empty($_POST) || !empty($_GET) )
			$this->processor();
	
	}

	function controller() {

		switch($this->mode) {
			case 'sort':
				include_once (dirname (__FILE__) . '/manage-sort.php');
				flag_sortorder($this->gid);
			break;
			case 'edit':
				include_once (dirname (__FILE__) . '/manage-images.php');
				flag_picturelist();	
			break;
 			case 'add':	
				if(current_user_can('FlAG Upload images')){
					include_once (dirname (__FILE__) . '/addmoreimages.php');
					flag_add_more_images($this->gid);	
				} else {
					die(__('Cheatin&#8217; uh?'));
				}
			break;
	  	case 'main':
			default:
				if(current_user_can('FlAG Upload images')){
					include_once (dirname (__FILE__) . '/addgallery.php');
					flag_admin_add_gallery();
				}
				include_once (dirname (__FILE__) . '/manage-galleries.php');
				flag_manage_gallery_main();
			break;
		}
	}

	function processor() {
	
		global $wpdb, $flag;
		
		if ($this->mode == 'delete') {
		// Delete a gallery
		
			check_admin_referer('flag_editgallery');
		
			// get the path to the gallery
			$gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->flaggallery WHERE gid = '$this->gid' ");
			if ($gallerypath){
		
				// delete pictures
				$imagelist = $wpdb->get_col("SELECT filename FROM $wpdb->flagpictures WHERE galleryid = '$this->gid' ");
				if ($flag->options['deleteImg']) {
					if (is_array($imagelist)) {
						foreach ($imagelist as $filename) {
							@unlink(WINABSPATH . $gallerypath . '/thumbs/thumbs_' . $filename);
							@unlink(WINABSPATH . $gallerypath .'/'. $filename);
						}
					}
					// delete folder
						@rmdir( WINABSPATH . $gallerypath . '/thumbs' );
						@rmdir( WINABSPATH . $gallerypath );
				}
			}
	
			$delete_pic = $wpdb->query("DELETE FROM $wpdb->flagpictures WHERE galleryid = $this->gid");
			$delete_galllery = $wpdb->query("DELETE FROM $wpdb->flaggallery WHERE gid = $this->gid");
			
			if($delete_galllery)
				flagGallery::show_message( __ngettext( 'Gallery', 'Galleries', 1, 'flag' ) . ' \''.$this->gid.'\' '.__('deleted successfully','flag'));
				
		 	$this->mode = 'main'; // show mainpage
		}
	
		if ($this->mode == 'delpic') {
		// Delete a picture
		//TODO:Remove also Tag reference
			check_admin_referer('flag_delpicture');
			$filename = $wpdb->get_var("SELECT filename FROM $wpdb->flagpictures WHERE pid = '$this->pid' ");
			if ($filename) {
				$gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->flaggallery WHERE gid = '$this->gid' ");
				if ($gallerypath){
					$thumb_folder = flagGallery::get_thumbnail_folder($gallerypath, FALSE);
					if ($flag->options['deleteImg']) {
						@unlink(WINABSPATH . $gallerypath . '/thumbs/thumbs_' .$filename);
						@unlink(WINABSPATH . $gallerypath . '/' . $filename);
					}
				}		
				$delete_pic = $wpdb->query("DELETE FROM $wpdb->flagpictures WHERE pid = $this->pid");
			}
			if($delete_pic)
				flagGallery::show_message( __('Picture','flag').' \''.$this->pid.'\' '.__('deleted successfully','flag') );
				
		 	$this->mode = 'edit'; // show pictures
	
		}
		
		if (isset ($_POST['bulkaction']) && isset ($_POST['doaction']))  {
			// do bulk update
			
			check_admin_referer('flag_updategallery');
			
			$gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->flaggallery WHERE gid = '$this->gid' ");
			$imageslist = array();
			
			if ( is_array($_POST['doaction']) ) {
				foreach ( $_POST['doaction'] as $imageID ) {
					$imageslist[] = $wpdb->get_var("SELECT filename FROM $wpdb->flagpictures WHERE pid = '$imageID' ");
				}
			}
			
			switch ($_POST['bulkaction']) {
				case 'no_action';
				// No action
					break;
				case 'new_thumbnail':
				// Create new thumbnails
					flagAdmin::do_ajax_operation( 'create_thumbnail' , $_POST['doaction'], __('Create new thumbnails','flag') );
					break;
				case 'resize_images':
				// Resample images
					flagAdmin::do_ajax_operation( 'resize_image' , $_POST['doaction'], __('Resize images','flag') );
					break;
				case 'delete_images':
				// Delete images
					if ( is_array($_POST['doaction']) ) {
					if ($gallerypath){
						$thumb_folder = flagGallery::get_thumbnail_folder($gallerypath, FALSE);
						foreach ( $_POST['doaction'] as $imageID ) {
							$filename = $wpdb->get_var("SELECT filename FROM $wpdb->flagpictures WHERE pid = '$imageID' ");
							if ($flag->options['deleteImg']) {
								@unlink(WINABSPATH.$gallerypath.'/'.$thumb_folder.'/'. "thumbs_" .$filename);
								@unlink(WINABSPATH.$gallerypath.'/'.$filename);	
							} 
							$delete_pic = $wpdb->query("DELETE FROM $wpdb->flagpictures WHERE pid = $imageID");
						}
					}		
					if($delete_pic)
						flagGallery::show_message(__('Pictures deleted successfully ',"flag"));
					}
					break;
			}
		}
		
		// will be called after a ajax operation
		if (isset ($_POST['ajax_callback']))  {
				if ($_POST['ajax_callback'] == 1)
					flagGallery::show_message(__('Operation successful. Please clear your browser cache.',"flag"));
			$this->mode = 'edit';		
		}
		
		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_SelectGallery']))  {
			
			check_admin_referer('flag_thickbox_form');
			
			$pic_ids  = explode(',', $_POST['TB_imagelist']);
			$dest_gid = (int) $_POST['dest_gid'];
			
			switch ($_POST['TB_bulkaction']) {
				case 'copy_to':
				// Copy images
					flagAdmin::copy_images( $pic_ids, $dest_gid );
					break;
				case 'move_to':
				// Move images
					flagAdmin::move_images( $pic_ids, $dest_gid );
					break;
			}
		}
		
		if (isset ($_POST['updatepictures']))  {
		// Update pictures	
		
			check_admin_referer('flag_updategallery');
		
			$gallery_title   = attribute_escape($_POST['title']);
			$gallery_path    = attribute_escape($_POST['path']);
			$gallery_desc    = attribute_escape($_POST['gallerydesc']);
			
			$wpdb->query("UPDATE $wpdb->flaggallery SET title= '$gallery_title', path= '$gallery_path', galdesc = '$gallery_desc' WHERE gid = '$this->gid'");
	
			if (isset ($_POST['author']))  {		
				$gallery_author  = (int) $_POST['author'];
				$wpdb->query("UPDATE $wpdb->flaggallery SET author = '$gallery_author' WHERE gid = '$this->gid'");
			}
	
			$this->update_pictures();
	
			//hook for other plugin to update the fields
			do_action('flag_update_gallery', $this->gid, $_POST);
	
			flagGallery::show_message(__('Update successful',"flag"));
		}
	
		if (isset ($_POST['scanfolder']))  {
		// Rescan folder
			check_admin_referer('flag_updategallery');
		
			$gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->flaggallery WHERE gid = '$this->gid' ");
			flagAdmin::import_gallery($gallerypath);
		}
	
		if ( isset ($_POST['backToGallery']) )
			$this->mode = 'edit';
		
		// show sort order
		if ( isset ($_POST['sortGallery']) )
			$this->mode = 'sort';

		// add more images
		if ( isset ($_POST['addImages']) )
			$this->mode = 'add';
		
	}
	
	function update_pictures() {
		global $wpdb;

		//TODO:Error message when update failed
		//TODO:Combine update in one query per image
		
		$description = 	$_POST['description'];
		$alttext = 		$_POST['alttext'];
		$pictures = 	$_POST['pid'];
		
		if ( is_array($description) ) {
			foreach( $description as $key => $value ) {
				$desc = $wpdb->escape($value);
				$wpdb->query( "UPDATE $wpdb->flagpictures SET description = '$desc' WHERE pid = $key");
			}
		}
		if ( is_array($alttext) ){
			foreach( $alttext as $key => $value ) {
				$alttext = $wpdb->escape($value);
				$wpdb->query( "UPDATE $wpdb->flagpictures SET alttext = '$alttext' WHERE pid = $key");
			}
		}
		return;
	}

	// Check if user can select a author
	function get_editable_user_ids( $user_id, $exclude_zeros = true ) {
		global $wpdb;
	
		$user = new WP_User( $user_id );
	
		if ( ! $user->has_cap('FlAG Manage others gallery') ) {
			if ( $user->has_cap('FlAG Manage gallery') || $exclude_zeros == false )
				return array($user->id);
			else
				return false;
		}
	
		$level_key = $wpdb->prefix . 'user_level';
		$query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$level_key'";
		if ( $exclude_zeros )
			$query .= " AND meta_value != '0'";
	
		return $wpdb->get_col( $query );
	}

}
?>
