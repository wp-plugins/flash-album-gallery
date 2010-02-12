<?php  
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
	
	// sometimes a error feedback is better than a white screen
	@ini_set('error_reporting', E_ALL ^ E_NOTICE);

function flag_add_more_images($galleryID = 0){

	global $wpdb, $flagdb, $flag;
	
	if ($galleryID == 0) return;

	$galleryID = (int) $galleryID;

	// Load the gallery metadata
	$gallery = $flagdb->find_gallery($galleryID);

	// same as $_SERVER['REQUEST_URI'], but should work under IIS 6.0
	$filepath    = admin_url() . 'admin.php?page=flag-manage-gallery&amp;mode=add&amp;gid=' . $galleryID;
	
	// check for the max image size
	$maxsize    = flagGallery::check_memory_limit();
	
	// link for the flash file
	$swf_upload_link = FLAG_URLPATH . 'admin/upload.php';
	$swf_upload_link = wp_nonce_url($swf_upload_link, 'flag_swfupload');
	//flash doesn't seem to like encoded ampersands, so convert them back here
	$swf_upload_link = str_replace('&#038;', '&', $swf_upload_link);

	if ($_POST['uploadimage']){
		check_admin_referer('flag_addgallery');
		if ($_FILES['MF__F_0_0']['error'] == 0) {
			$messagetext = flagAdmin::upload_images();
		}
		else
			flagGallery::show_error( __('Upload failed!','flag') );	
	}
	
	if (isset($_POST['swf_callback'])){
		if ($_POST['galleryselect'] == "0" )
			flagGallery::show_error(__('No gallery selected !','flag'));
		else {
			// get the path to the gallery
			$galleryID = (int) $_POST['galleryselect'];
			$gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->flaggallery WHERE gid = '$galleryID' ");
			flagAdmin::import_gallery($gallerypath);
		}	
	}

	if ( isset($_POST['disable_flash']) ){
		check_admin_referer('flag_addgallery');
		$flag->options['swfUpload'] = false;	
		update_option('flag_options', $flag->options);
	}

	if ( isset($_POST['enable_flash']) ){
		check_admin_referer('flag_addgallery');
		$flag->options['swfUpload'] = true;	
		update_option('flag_options', $flag->options);
	}

	//get all galleries (after we added new ones)
	$gallerylist = $flagdb->find_all_galleries('gid', 'DESC');

	?>
	
	<?php if($flag->options['swfUpload']) { ?>
	<!-- SWFUpload script -->
	<script type="text/javascript">
		var flag_swf_upload;
			
		window.onload = function () {
			flag_swf_upload = new SWFUpload({
				// Backend settings
				upload_url : "<?php echo $swf_upload_link; ?>",
				flash_url : "<?php echo FLAG_URLPATH; ?>admin/js/swfupload.swf",
				
				// Button Settings
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 300,
				button_height: 27,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
								
				// File Upload Settings
				file_size_limit : "<?php echo wp_max_upload_size(); ?>b",
				file_types : "*.jpg;*.gif;*.png",
				file_types_description : "<?php _e('Image Files', 'flag') ;?>",
				
				// Queue handler
				file_queued_handler : fileQueued,
				
				// Upload handler
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				
				post_params : {
					"auth_cookie" : "<?php echo $_COOKIE[AUTH_COOKIE]; ?>",
					"galleryselect" : "0"
				},
				
				// i18names
				custom_settings : {
					"remove" : "<?php _e('remove', 'flag') ;?>",
					"browse" : "<?php _e('Browse...', 'flag') ;?>",
					"upload" : "<?php _e('Upload images', 'flag') ;?>"
				},

				// Debug settings
				debug: false
				
			});
			
			// on load change the upload to swfupload
			initSWFUpload();
			
		};
	</script>
	<div class="wrap" id="progressbar-wrap" style="display:none;">
		<div class="progressborder">
			<div class="progressbar" id="progressbar">
				<span>0%</span>
			</div>
		</div>
	</div>
	<?php } else { ?>
	<!-- MultiFile script -->
	<script type="text/javascript">	
	/* <![CDATA[ */
		jQuery(document).ready(function(){
			jQuery('#imagefiles').MultiFile({
				STRING: {
			    	remove:'<?php _e('remove', 'flag') ;?>'
  				}
		 	});
		});
	/* ]]> */
	</script>
	<?php } ?>
		
<div class="wrap">
	<h2><?php printf(__('Upload More Images in "%s"', 'flag'), $gallery->title) ;?></h2>
		<!-- upload images -->
	<div id="poststuff">
		<div id="post-body"><div id="post-body-content"><div id="normal-sortables" style="position: relative;">
			<div id="addmoreimg" class="postbox" >
				<h3><span><?php _e('Add images to gallery', 'flag') ?></span></h3>
				<div class="inside">
					<form name="uploadimage" id="uploadimage_form" method="POST" enctype="multipart/form-data" action="<?php echo $filepath; ?>" accept-charset="utf-8" >
					<?php wp_nonce_field('flag_addmoreimages') ?>
						<table class="form-table"> 
						<tr valign="top"> 
							<th scope="row"><strong><?php _e('Upload image(s):', 'flag') ;?></strong></th>
							<td><span id='spanButtonPlaceholder'></span><input type="file" name="imagefiles[]" id="imagefiles" size="35" class="imagefiles"/></td>
							<td><div class="alignright actions"><input class="button-secondary action" type="submit" name="backToGallery" value="<?php _e('Back to gallery', 'flag') ?>" /></div>
								<input name="galleryselect" id="galleryselect" type="hidden" value="<?php echo $galleryID ?>" ></td>
						</tr> 
						</table>
						<div class="submit" style="float:none; padding: 12px;">
							<?php if ($flag->options['swfUpload']) { ?>
							<input type="submit" name="disable_flash" id="disable_flash" title="<?php _e('The batch upload requires Adobe Flash 10, disable it if you have problems','flag') ?>" value="<?php _e('Disable flash upload', 'flag') ;?>" />
							<?php } else { ?>
							<input type="submit" name="enable_flash" id="enable_flash" title="<?php _e('Upload multiple files at once by ctrl/shift-selecting in dialog','flag') ?>" value="<?php _e('Enable flash based upload', 'flag') ;?>" />
							<?php } ?>
							<input class="button-primary" type="submit" name="uploadimage" id="uploadimage_btn" value="<?php _e('Upload images', 'flag') ;?>" />
						</div>
					</form>
				</div>
			</div>
		</div></div></div>
	</div>
</div>
<?php
	}
?>