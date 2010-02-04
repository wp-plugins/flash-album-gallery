<?php  
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

function flag_admin_options()  {
	
	global $wpdb, $flag;
	
	$old_state = $flag->options['usePermalinks'];
	
	// same as $_SERVER['REQUEST_URI'], but should work under IIS 6.0
	$filepath    = admin_url() . 'admin.php?page='.$_GET['page'];

	if ( isset($_POST['updateoption']) ) {	
		check_admin_referer('flag_settings');
		// get the hidden option fields, taken from WP core
		if ( $_POST['page_options'] )	
			$options = explode(',', stripslashes($_POST['page_options']));
		if ($options) {
			foreach ($options as $option) {
				$option = trim($option);
				$value = trim($_POST[$option]);
				$flag->options[$option] = $value;
			}
		// the path should always end with a slash	
		$flag->options['galleryPath']    = trailingslashit($flag->options['galleryPath']);
		// the custom sortorder must be ascending
		$flag->options['galSortDir'] = ($flag->options['galSort'] == 'sortorder') ? 'ASC' : $flag->options['galSortDir'];
		}
		// Save options
		update_option('flag_options', $flag->options);

	 	flagGallery::show_message(__('Update Successfully','flag'));
	}		

	if ( isset($_POST['update_cap']) ) {	

		check_admin_referer('flag_addroles');

		// now set or remove the capability
		flag_set_capability($_POST['general'],"FlAG overview");
		flag_set_capability($_POST['tinymce'],"FlAG Use TinyMCE");
		flag_set_capability($_POST['add_gallery'],"FlAG Upload images");
		flag_set_capability($_POST['manage_gallery'],"FlAG Manage gallery");
		flag_set_capability($_POST['manage_others'],"FlAG Manage others gallery");
		flag_set_capability($_POST['change_skin'],"FlAG Change skin");
		flag_set_capability($_POST['add_skins'],"FlAG Add skins");
		flag_set_capability($_POST['delete_skins'],"FlAG Delete skins");
		flag_set_capability($_POST['change_options'],"FlAG Change options");
		
		flagGallery::show_message(__('Updated capabilities',"flag"));
	}
	
	// message windows
	if(!empty($messagetext)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$messagetext.'</p></div>'; }
	
	?>
	
	<div id="slider" class="wrap">
	
		<ul id="tabs" class="tabs">
			<li class="selected"><a href="#" rel="generaloptions"><?php _e('General Options', 'flag') ;?></a></li>
			<li><a href="#" rel="thumbnails"><?php _e('Thumbnails', 'flag') ;?></a></li>
			<li><a href="#" rel="images"><?php _e('Images', 'flag') ;?></a></li>
			<li><a href="#" rel="sorting"><?php _e( 'Sorting', 'flag' ) ;?></a></li>
			<li><a href="#" rel="colors"><?php _e('Colors', 'flag') ;?></a></li>
			<li><a href="#" rel="roles"><?php _e('Roles', 'flag') ;?></a></li>
		</ul>

		<!-- General Options -->

		<div id="generaloptions" class="cptab">
			<h2><?php _e('General Options','flag'); ?></h2>
			<form name="generaloptions" method="post">
			<?php wp_nonce_field('flag_settings') ?>
			<input type="hidden" name="page_options" value="galleryPath,flashWidth,flashHeight,deleteImg,useMediaRSS" />
				<table class="form-table flag-options">
					<tr valign="top">
						<th align="left"><?php _e('Gallery path','flag') ?></th>
						<td><input type="text" size="35" name="galleryPath" value="<?php echo $flag->options['galleryPath']; ?>" />
						<span class="setting-description"><?php _e('This is the default path for all galleries','flag') ?></span></td>
					</tr>
					<tr valign="top">
						<th><?php _e('Default flash size (W x H)','flag') ?>:</th>
						<td><input type="text" size="4" maxlength="4" name="flashWidth" value="<?php echo $flag->options['flashWidth'] ?>" /> x
						<input type="text" size="4" maxlength="4" name="flashHeight" value="<?php echo $flag->options['flashHeight'] ?>" /></td>
					</tr>					
					<tr valign="top">
						<th align="left"><?php _e('Delete image files','flag') ?></th>
						<td><input type="checkbox" name="deleteImg" value="1" <?php checked('1', $flag->options['deleteImg']); ?> />
						<?php _e('Delete files, when removing a gallery in the database','flag') ?></td>
					</tr>
					<tr>
						<th align="left"><?php _e('Activate Media RSS feed','flag') ?></th>
						<td><input type="checkbox" name="useMediaRSS" value="1" <?php checked('1', $flag->options['useMediaRSS']); ?> />
						<span class="setting-description"><?php _e('A RSS feed will be added to you blog header.','flag') ?></span></td>
					</tr>
				</table>
			<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes', 'flag') ;?>"/></div>
			</form>	
		</div>	
		
		<!-- Thumbnail settings -->
		
		<div id="thumbnails" class="cptab">
			<h2><?php _e('Thumbnail settings','flag'); ?></h2>
			<form name="thumbnailsettings" method="POST">
			<?php wp_nonce_field('flag_settings') ?>
			<input type="hidden" name="page_options" value="thumbWidth,thumbHeight,thumbFix,thumbCrop,thumbQuality" />
				<p><?php _e('Please note : If you change the settings, you need to recreate the thumbnails under -> Manage Gallery .', 'flag') ?></p>
				<table class="form-table flag-options">
					<tr valign="top">
						<th align="left" style="width:250px;"><?php _e('Width x Height (in pixel)','flag') ?></th>
						<td><input type="text" size="4" maxlength="4" name="thumbWidth" value="<?php echo $flag->options['thumbWidth']; ?>" /> x <input type="text" size="4" maxlength="4" name="thumbHeight" value="<?php echo $flag->options['thumbHeight']; ?>" />
						<span class="setting-description"><?php _e('These values are maximum values ','flag') ?></span></td>
					</tr>
					<tr valign="top">
						<th align="left"><?php _e('Set fix dimension','flag') ?></th>
						<td><input type="checkbox" name="thumbFix" value="1" <?php checked('1', $flag->options['thumbFix']); ?> />
						<?php _e('Ignore the aspect ratio, no portrait thumbnails','flag') ?></td>
					</tr>
					<tr valign="top">
						<th align="left"><?php _e('Crop square thumbnail from image','flag') ?></th>
						<td><input type="checkbox" name="thumbCrop" value="1" <?php checked('1', $flag->options['thumbCrop']); ?> />
						<?php _e('Create square thumbnails, use only the width setting :','flag') ?> <?php echo $flag->options['thumbWidth']; ?> x <?php echo $flag->options['thumbWidth']; ?></td>
					</tr>
					<tr valign="top">
						<th align="left"><?php _e('Thumbnail quality','flag') ?></th>
						<td><input type="text" size="3" maxlength="3" name="thumbQuality" value="<?php echo $flag->options['thumbQuality']; ?>" /> %</td>
					</tr>
				</table>
			<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes', 'flag') ;?>"/></div>
			</form>	
		</div>
		
		<!-- Image settings -->
		
		<div id="images" class="cptab">
			<h2><?php _e('Image settings','flag'); ?></h2>
			<form name="imagesettings" method="POST">
			<?php wp_nonce_field('flag_settings') ?>
			<input type="hidden" name="page_options" value="imgResize,imgWidth,imgHeight,imgQuality" />
				<table class="form-table flag-options">
					<tr valign="top">
						<th scope="row" style="width:250px;"><label for="fixratio"><?php _e('Resize Images','flag') ?></label><br /><small>(Manage Gallery -> 'Resize Images' action)</small></th>
						<td><input type="hidden" name="imgResize" value="1" <?php checked('1', $flag->options['imgResize']); ?> /> </td>
						<td><input type="text" size="5" name="imgWidth" value="<?php echo $flag->options['imgWidth']; ?>" /> x <input type="text" size="5" name="imgHeight" value="<?php echo $flag->options['imgHeight']; ?>" />
						<span class="setting-description"><?php _e('Width x Height (in pixel). Flash Album Gallery will keep ratio size','flag') ?></span></td>
					</tr>
					<tr valign="top">
						<th align="left"><?php _e('Image quality','flag') ?></th>
						<td></td>
						<td><input type="text" size="3" maxlength="3" name="imgQuality" value="<?php echo $flag->options['imgQuality']; ?>" /> %</td>
					</tr>
				</table>
			<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes', 'flag') ;?>"/></div>
			</form>	
		</div>
		
		<!-- Sorting settings -->
		
		<div id="sorting" class="cptab">
			<h2><?php _e('Sorting','flag'); ?></h2>
			<form name="gallerysort" method="POST">
			<?php wp_nonce_field('flag_settings') ?>
			<input type="hidden" name="page_options" value="galSort,galSortDir" />
			<h3><?php _e('Sort options','flag') ?></h3>
				<table class="form-table flag-options">
					<tr>
						<th valign="top"><?php _e('Sort thumbnails','flag') ?>:</th>
						<td>
						<label><input name="galSort" type="radio" value="sortorder" <?php checked('sortorder', $flag->options['galSort']); ?> /> <?php _e('Custom order', 'flag') ;?></label><br />
						<label><input name="galSort" type="radio" value="pid" <?php checked('pid', $flag->options['galSort']); ?> /> <?php _e('Image ID', 'flag') ;?></label><br />
						<label><input name="galSort" type="radio" value="filename" <?php checked('filename', $flag->options['galSort']); ?> /> <?php _e('File name', 'flag') ;?></label><br />
						<label><input name="galSort" type="radio" value="alttext" <?php checked('alttext', $flag->options['galSort']); ?> /> <?php _e('Alt / Title text', 'flag') ;?></label><br />
						<label><input name="galSort" type="radio" value="imagedate" <?php checked('imagedate', $flag->options['galSort']); ?> /> <?php _e('Date / Time', 'flag') ;?></label>
						</td>
					</tr>
					<tr>
						<th valign="top"><?php _e('Sort direction','flag') ?>:</th>
						<td><label><input name="galSortDir" type="radio" value="ASC" <?php checked('ASC', $flag->options['galSortDir']); ?> /> <?php _e('Ascending', 'flag') ;?></label><br />
						<label><input name="galSortDir" type="radio" value="DESC" <?php checked('DESC', $flag->options['galSortDir']); ?> /> <?php _e('Descending', 'flag') ;?></label>
						</td>
					</tr>
				</table>
			<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes', 'flag') ;?>"/></div>
			</form>	
		</div>
		
		<!-- Slideshow settings -->
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#colors .colorPick').each( function(){
		var inpID = jQuery(this).attr('name');
		jQuery('#cp_'+inpID).farbtastic('#'+inpID);
		jQuery('#'+inpID).focus( function(){
		    jQuery('#cp_'+inpID).show();
		});
		jQuery('#'+inpID).blur( function(){
		    jQuery('#cp_'+inpID).hide();
		});
	});
  function tChecked() {
		if( jQuery('#flashBacktransparent').attr('checked') ) {
	    var dclone=jQuery('#flashBackcolor').clone();
	    jQuery('#flashBackcolor').hide();
			dclone.removeAttr('style').removeAttr('id').removeAttr('name').addClass('flashBackcolor').attr('disabled','disabled').insertAfter('#flashBackcolor');
	  } else {
	    jQuery('.flashBackcolor').remove();
	    jQuery('#flashBackcolor').show();
	  }
  }
  tChecked();
  jQuery("#flashBacktransparent").click(tChecked);
});
</script>
	<div id="colors" class="cptab">
		<form name="color_options" method="POST">
		<?php wp_nonce_field('flag_settings') ?>
		<input type="hidden" name="page_options" value="flashBackcolor,buttonsBG,flashBacktransparent,buttonsMouseOver,buttonsMouseOut,catButtonsMouseOver,catButtonsMouseOut,catButtonsTextMouseOver,catButtonsTextMouseOut,thumbMouseOver,thumbMouseOut,mainTitle,categoryTitle,itemBG,itemTitle,itemDescription" />
		<h2><?php _e('Colors','flag'); ?></h2>
				<table class="form-table flag-options">
					<tr>
						<th style="width: 30%;"><?php _e('Background Color','flag') ?>:</th>
						<td><input class="colorPick" type="text" size="8" maxlength="7" id="flashBackcolor" name="flashBackcolor" value="<?php echo $flag->options['flashBackcolor'] ?>" /><div id="cp_flashBackcolor" style="background:#F9F9F9;position:absolute;display:none;"></div> <label><input type="checkbox" id="flashBacktransparent" name="flashBacktransparent" value="transparent" <?php if($flag->options['flashBacktransparent']) echo 'checked="checked"'; ?> /> transparent</label></td>
					</tr>
					<tr>					
						<th><?php _e('Buttons Background Color','flag') ?>:</th>
						<td><input class="colorPick" type="text" size="8" maxlength="7" id="buttonsBG" name="buttonsBG" value="<?php echo $flag->options['buttonsBG'] ?>" /><div id="cp_buttonsBG" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
					</tr>
					<tr>					
						<th><?php _e('Buttons Text Color','flag') ?>:</th>
						<td>
							<input class="colorPick" type="text" size="8" maxlength="7" id="buttonsMouseOver" name="buttonsMouseOver" value="<?php echo $flag->options['buttonsMouseOver'] ?>" /> mouseOver<br />
							<div id="cp_buttonsMouseOver" style="background:#F9F9F9;position:absolute;display:none;"></div>
							<input class="colorPick" type="text" size="8" maxlength="7" id="buttonsMouseOut" name="buttonsMouseOut" value="<?php echo $flag->options['buttonsMouseOut'] ?>" /> mouseOut<br />
							<div id="cp_buttonsMouseOut" style="background:#F9F9F9;position:absolute;display:none;"></div>
						</td>
					</tr>
					<tr>					
						<th><?php _e('Category Buttons Color','flag') ?>:</th>
						<td>
							<input class="colorPick" type="text" size="8" maxlength="7" id="catButtonsMouseOver" name="catButtonsMouseOver" value="<?php echo $flag->options['catButtonsMouseOver'] ?>" /> mouseOver<br />
							<div id="cp_catButtonsMouseOver" style="background:#F9F9F9;position:absolute;display:none;"></div>
							<input class="colorPick" type="text" size="8" maxlength="7" id="catButtonsMouseOut" name="catButtonsMouseOut" value="<?php echo $flag->options['catButtonsMouseOut'] ?>" /> mouseOut<br />
							<div id="cp_catButtonsMouseOut" style="background:#F9F9F9;position:absolute;display:none;"></div>
						</td>
					</tr>
					<tr>					
						<th><?php _e('Category Buttons Text Color','flag') ?>:</th>
						<td>
							<input class="colorPick" type="text" size="8" maxlength="7" id="catButtonsTextMouseOver" name="catButtonsTextMouseOver" value="<?php echo $flag->options['catButtonsTextMouseOver'] ?>" /> mouseOver<br />
							<div id="cp_catButtonsTextMouseOver" style="background:#F9F9F9;position:absolute;display:none;"></div>
							<input class="colorPick" type="text" size="8" maxlength="7" id="catButtonsTextMouseOut" name="catButtonsTextMouseOut" value="<?php echo $flag->options['catButtonsTextMouseOut'] ?>" /> mouseOut<br />
							<div id="cp_catButtonsTextMouseOut" style="background:#F9F9F9;position:absolute;display:none;"></div>
						</td>
					</tr>
					<tr>					
						<th><?php _e('Thumbs Rollover Color','flag') ?>:</th>
						<td>
							<input class="colorPick" type="text" size="8" maxlength="7" id="thumbMouseOver" name="thumbMouseOver" value="<?php echo $flag->options['thumbMouseOver'] ?>" /> mouseOver<br />
							<div id="cp_thumbMouseOver" style="background:#F9F9F9;position:absolute;display:none;"></div>
							<input class="colorPick" type="text" size="8" maxlength="7" id="thumbMouseOut" name="thumbMouseOut" value="<?php echo $flag->options['thumbMouseOut'] ?>" /> mouseOut<br />
							<div id="cp_thumbMouseOut" style="background:#F9F9F9;position:absolute;display:none;"></div>
						</td>
					</tr>
					<tr>					
						<th><?php _e('Main Title','flag') ?>:</th>
						<td><input class="colorPick" type="text" size="8" maxlength="7" id="mainTitle" name="mainTitle" value="<?php echo $flag->options['mainTitle'] ?>" /><div id="cp_mainTitle" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
					</tr>
					<tr>					
						<th><?php _e('Category Title','flag') ?>:</th>
						<td><input class="colorPick" type="text" size="8" maxlength="7" id="categoryTitle" name="categoryTitle" value="<?php echo $flag->options['categoryTitle'] ?>" /><div id="cp_categoryTitle" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
					</tr>
					<tr>					
						<th><?php _e('Item Background','flag') ?>:</th>
						<td><input class="colorPick" type="text" size="8" maxlength="7" id="itemBG" name="itemBG" value="<?php echo $flag->options['itemBG'] ?>" /><div id="cp_itemBG" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
					</tr>
					<tr>					
						<th><?php _e('Item Title','flag') ?>:</th>
						<td><input class="colorPick" type="text" size="8" maxlength="7" id="itemTitle" name="itemTitle" value="<?php echo $flag->options['itemTitle'] ?>" /><div id="cp_itemTitle" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
					</tr>
					<tr>					
						<th><?php _e('Item Description','flag') ?>:</th>
						<td><input class="colorPick" type="text" size="8" maxlength="7" id="itemDescription" name="itemDescription" value="<?php echo $flag->options['itemDescription'] ?>" /><div id="cp_itemDescription" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
					</tr>
				</table>
				<div class="clear"> &nbsp; </div>
				<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes', 'flag') ;?>"/></div>
		</form>
	</div>
	
	<div id="roles" class="cptab">
		<form method="POST" name="addroles" id="addroles" accept-charset="utf-8">
			<?php wp_nonce_field('flag_addroles') ?>
			<input type="hidden" name="page_options" value="flashBackcolor,buttonsMouseOver,buttonsMouseOut,catButtonsMouseOver,catButtonsMouseOut,catButtonsTextMouseOver,catButtonsTextMouseOut,thumbMouseOver,thumbMouseOut,mainTitle,categoryTitle,itemTitle,itemDescription" />
			<h2><?php _e('Roles / capabilities','flag'); ?></h2>
			<p><?php _e('Select the lowest role which should be able to access the follow capabilities. Flash Album Gallery supports the standard roles from WordPress.', 'flag') ?></p>
			<table class="form-table"> 
			<tr valign="top"> 
				<th scope="row"><?php _e('Main Flash Album Gallery overview', 'flag') ;?>:</th> 
				<td><label for="general"><select name="general" id="general"><?php wp_dropdown_roles( flag_get_role('FlAG overview') ); ?></select></label></td>
			</tr>
			<tr valign="top"> 
				<th scope="row"><?php _e('Use TinyMCE Button / Upload tab', 'flag') ;?>:</th> 
				<td><label for="tinymce"><select name="tinymce" id="tinymce"><?php wp_dropdown_roles( flag_get_role('FlAG Use TinyMCE') ); ?></select></label></td>
			</tr>
			<tr valign="top"> 
				<th scope="row"><?php _e('Add gallery / Upload images', 'flag') ;?>:</th> 
				<td><label for="add_gallery"><select name="add_gallery" id="add_gallery"><?php wp_dropdown_roles( flag_get_role('FlAG Upload images') ); ?></select></label></td>
			</tr>
			<tr valign="top"> 
				<th scope="row"><?php _e('Manage gallery', 'flag') ;?>:</th> 
				<td><label for="manage_gallery"><select name="manage_gallery" id="manage_gallery"><?php wp_dropdown_roles( flag_get_role('FlAG Manage gallery') ); ?></select></label></td>
			</tr>
			<tr valign="top"> 
				<th scope="row"><?php _e('Manage others gallery', 'flag') ;?>:</th> 
				<td><label for="manage_others"><select name="manage_others" id="manage_others"><?php wp_dropdown_roles( flag_get_role('FlAG Manage others gallery') ); ?></select></label></td>
			</tr>
			<tr valign="top"> 
				<th scope="row"><?php _e('Change skin', 'flag') ;?>:</th> 
				<td><label for="change_skin"><select name="change_skin" id="change_skin"><?php wp_dropdown_roles( flag_get_role('FlAG Change skin') ); ?></select></label></td>
			</tr>
			<tr valign="top"> 
				<th scope="row"><?php _e('Add skins', 'flag') ;?>:</th> 
				<td><label for="add_skins"><select name="add_skins" id="add_skins"><?php wp_dropdown_roles( flag_get_role('FlAG Add skins') ); ?></select></label></td>
			</tr>
			<tr valign="top"> 
				<th scope="row"><?php _e('Delete skins', 'flag') ;?>:</th> 
				<td><label for="delete_skins"><select name="delete_skins" id="delete_skins"><?php wp_dropdown_roles( flag_get_role('FlAG Delete skins') ); ?></select></label></td>
			</tr>
			<tr valign="top"> 
				<th scope="row"><?php _e('Change options', 'flag') ;?>:</th> 
				<td><label for="change_options"><select name="change_options" id="change_options"><?php wp_dropdown_roles( flag_get_role('FlAG Change options') ); ?></select></label></td>
			</tr>
			</table>
			<div class="submit"><input type="submit" class="button-primary" name= "update_cap" value="<?php _e('Update capabilities', 'flag') ;?>"/></div>
		</form>
	</div>
</div>
<script type="text/javascript">
	var cptabs=new ddtabcontent("tabs");
	cptabs.setpersist(true);
	cptabs.setselectedClassTarget("linkparent");
	cptabs.init();
</script>

	<?php
}

function flag_get_role($capability){
	// This function return the lowest roles which has the capabilities
	$check_order = array("subscriber", "contributor", "author", "editor", "administrator");

	$args = array_slice(func_get_args(), 1);
	$args = array_merge(array($capability), $args);

	foreach ($check_order as $role) {
		$check_role = get_role($role);
		
		if ( empty($check_role) )
			return false;
			
		if (call_user_func_array(array(&$check_role, 'has_cap'), $args))
			return $role;
	}
	return false;
}

function flag_set_capability($lowest_role, $capability){
	// This function set or remove the $capability
	$check_order = array("subscriber", "contributor", "author", "editor", "administrator");

	$add_capability = false;
	
	foreach ($check_order as $role) {
		if ($lowest_role == $role)
			$add_capability = true;
			
		$the_role = get_role($role);
		
		// If you rename the roles, the please use the role manager plugin
		
		if ( empty($the_role) )
			continue;
			
		$add_capability ? $the_role->add_cap($capability) : $the_role->remove_cap($capability) ;
	}
	
}

/**********************************************************/
// taken from WP Core

function flag_input_selected( $selected, $current) {
	if ( $selected == $current)
		return ' selected="selected"';
}
	
function flag_input_checked( $checked, $current) {
	if ( $checked == $current)
		return ' checked="checked"';
}
?>
