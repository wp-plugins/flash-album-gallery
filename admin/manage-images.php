<?php  

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {	die('You are not allowed to call this page directly.');}

function flag_picturelist() {
// *** show picture list
	global $wpdb, $flagdb, $user_ID, $flag;
	
	// GET variables
	$act_gid    = $flag->manage_page->gid;
	
	// Load the gallery metadata
	$gallery = $flagdb->find_gallery($act_gid);

	if (!$gallery) {
		flagGallery::show_error(__('Gallery not found.', 'flag'));
		return;
	}
	
	// Check if you have the correct capability
	if (!flagAdmin::can_manage_this_gallery($gallery->author)) {
		flagGallery::show_error(__('Sorry, you have no access here', 'flag'));
		return;
	}	
	
	// look for pagination	
	if ( ! isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
		$_GET['paged'] = 1;
	
	$start = ( $_GET['paged'] - 1 ) * 50;
	
	// get picture values
	$picturelist = $flagdb->get_gallery($act_gid, $flag->options['galSort'], $flag->options['galSortDir'], false, 50, $start );
	
	// build pagination
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%' ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $flagdb->paged['max_objects_per_page'],
		'current' => $_GET['paged']
	));
	
	// get the current author
	$act_author_user    = get_userdata( (int) $gallery->author );
	
	// list all galleries
	$gallerylist = $flagdb->find_all_galleries();
	
?>

<script type="text/javascript"> 
//<![CDATA[
function showDialog( windowId ) {
	var form = document.getElementById('updategallery');
	var elementlist = "";
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].name == "doaction[]")
				if(form.elements[i].checked == true)
					if (elementlist == "")
						elementlist = form.elements[i].value
					else
						elementlist += "," + form.elements[i].value ;
		}
	}
	jQuery("#" + windowId + "_bulkaction").val(jQuery("#bulkaction").val());
	jQuery("#" + windowId + "_imagelist").val(elementlist);
	// console.log (jQuery("#TB_imagelist").val());
	tb_show("", "#TB_inline?width=640&height=120&inlineId=" + windowId + "&modal=true", false);
}

function checkAll(form)
{
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].name == "doaction[]") {
				if(form.elements[i].checked == true)
					form.elements[i].checked = false;
				else
					form.elements[i].checked = true;
			}
		}
	}
}

function getNumChecked(form)
{
	var num = 0;
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].name == "doaction[]")
				if(form.elements[i].checked == true)
					num++;
		}
	}
	return num;
}

// this function check for a the number of selected images, sumbmit false when no one selected
function checkSelected() {

	var numchecked = getNumChecked(document.getElementById('updategallery'));
	 
	if(numchecked < 1) { 
		alert('<?php echo js_escape(__("No images selected", 'flag')); ?>');
		return false; 
	} 
	
	actionId = jQuery('#bulkaction').val();
	
	switch (actionId) {
		case "copy_to":
		case "move_to":
			showDialog('selectgallery');
			return false;
			break;
	}
	
	return confirm('<?php echo sprintf(js_escape(__("You are about to start the bulk edit for %s images \n \n 'Cancel' to stop, 'OK' to proceed.",'flag')), "' + numchecked + '") ; ?>');
}

jQuery(document).ready( function() {
	// close postboxes that should be closed
	jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');

	if (typeof postboxes != "undefined")
		postboxes.add_postbox_toggles('flag-manage-gallery'); // WP 2.7
	else
		add_postbox_toggles('flag-manage-gallery'); 	// WP 2.6

});
//]]>
</script>

<div class="wrap">

<h2><?php echo __ngettext( 'Gallery', 'Galleries', 1, 'flag' ); ?> : <?php echo flagGallery::i18n($gallery->title); ?></h2>
<select name="select_gid" style="width:180px; float: right; margin: -20px 3px 0 0;" onchange="window.location.href=this.options[this.selectedIndex].value">
	<option selected="selected"><?php _e('Choose another gallery', 'flag') ?></option>
<?php 
	foreach ($gallerylist as $gal) { 
		if ($gal->gid != $act_gid) { 
?>
	<option value="<?php echo wp_nonce_url( $flag->manage_page->base_page . "&amp;mode=edit&amp;gid=" . $gal->gid, 'flag_editgallery')?>" ><?php echo $gal->gid; ?> - <?php echo stripslashes($gal->title); ?></option>
<?php 
		} 
	}
?>
</select>

<form id="updategallery" class="flagform" method="POST" action="<?php echo $flag->manage_page->base_page . '&amp;mode=edit&amp;gid=' . $act_gid . '&amp;paged=' . $_GET['paged']; ?>" accept-charset="utf-8">
<?php wp_nonce_field('flag_updategallery') ?>

<div id="poststuff" class="metabox-holder">
	<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
<div id="post-body"><div id="post-body-content"><div id="normal-sortables" class="meta-box-sortables ui-sortable" style="position: relative;">
	<div id="flagalleryset" class="postbox <?php echo postbox_classes('flagalleryset', 'flag-manage-gallery'); ?>" >
		<div class="handlediv" title="Click to toggle"><br/></div>
		<h3 class="hndle"><span><?php _e('Gallery settings', 'flag') ?></span></h3>
		<div class="inside">
			<table class="form-table" >
				<tr>
					<th align="left" scope="row"><?php _e('Title') ?>:</th>
					<td align="left"><input type="text" size="50" name="title" value="<?php echo $gallery->title; ?>"  /></td>
				</tr>
				<tr>
					<th align="left" scope="row"><?php _e('Description') ?>:</th> 
					<td align="left"><textarea name="gallerydesc" cols="30" rows="3" style="width: 95%" ><?php echo $gallery->galdesc; ?></textarea></td>
				</tr>
				<tr>
					<th align="left" scope="row"><?php _e('Path', 'flag') ?>:</th> 
					<td align="left"><input type="text" size="50" name="path" value="<?php echo $gallery->path; ?>"  /></td>
				</tr>
				<tr>
					<th align="right" scope="row"><?php _e('Author', 'flag'); ?>:</th>
					<td align="left"> 
					<?php
						$editable_ids = $flag->manage_page->get_editable_user_ids( $user_ID );
						if ( $editable_ids && count( $editable_ids ) > 1 )
							wp_dropdown_users( array('include' => $editable_ids, 'name' => 'author', 'selected' => empty( $gallery->author ) ? 0 : $gallery->author ) ); 
						else
							echo $act_author_user->display_name;
					?>
					</td>
				</tr>
			</table>
			
			<div class="submit">
				<input type="submit" class="button-secondary" name="scanfolder" value="<?php _e("Scan Folder for new images",'flag')?> " />
				<input type="submit" class="button-primary action" name="updatepictures" value="<?php _e("Save Changes",'flag')?>" />
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div></div></div>
</div> <!-- poststuff -->

<div class="tablenav flag-tablenav">
	<?php if ( $page_links ) : ?>
	<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
		number_format_i18n( ( $_GET['paged'] - 1 ) * $flagdb->paged['objects_per_page'] + 1 ),
		number_format_i18n( min( $_GET['paged'] * $flagdb->paged['objects_per_page'], $flagdb->paged['total_objects'] ) ),
		number_format_i18n( $flagdb->paged['total_objects'] ),
		$page_links
	); echo $page_links_text; ?></div>
	<?php endif; ?>
	<div class="alignleft actions">
	<select id="bulkaction" name="bulkaction">
		<option value="no_action" ><?php _e("No action",'flag')?></option>
		<option value="new_thumbnail" ><?php _e("Create new thumbnails",'flag')?></option>
		<option value="resize_images" ><?php _e("Resize images",'flag')?></option>
		<option value="delete_images" ><?php _e("Delete images",'flag')?></option>
		<option value="copy_to" ><?php _e("Copy to...",'flag')?></option>
		<option value="move_to"><?php _e("Move to...",'flag')?></option>
	</select>
	<input class="button-secondary" type="submit" name="showThickbox" value="<?php _e("OK",'flag')?>" onclick="if ( !checkSelected() ) return false;" />
	
	<input class="button-secondary" type="submit" name="sortGallery" value="<?php _e("Sort gallery",'flag')?>" />
<?php if(current_user_can('FlAG Upload images')){ ?>
	<input class="button-secondary" type="submit" name="addImages" value="<?php _e("Add Images",'flag')?>" />
<?php } ?>
	<input type="submit" name="updatepictures" class="button-primary action"  value="<?php _e("Save Changes",'flag')?>" />
	</div>
</div>

<table id="flag-listimages" class="widefat fixed" cellspacing="0" >

	<thead>
	<tr>
<th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('updategallery'));" name="checkall"/></th>
<th id="id" class="manage-column column-id" style="" scope="col"><?php _e("ID",'flag')?></th>
<th id="thumbnail" class="manage-column column-thumbnail" scope="col"><?php _e("Thumbnail",'flag')?></th>
<th id="filename" class="manage-column column-filename" scope="col"><?php _e("Filename / Date",'flag')?></th>
<th id="alt_title_desc" class="manage-column column-alt_title_desc" scope="col"><?php _e("Alt & Title Text / Description",'flag')?></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
<th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('updategallery'));" name="checkall"/></th>
<th id="id" class="manage-column column-id" style="" scope="col"><?php _e("ID",'flag')?></th>
<th id="thumbnail" class="manage-column column-thumbnail" scope="col"><?php _e("Thumbnail",'flag')?></th>
<th id="filename" class="manage-column column-filename" scope="col"><?php _e("Filename / Date",'flag')?></th>
<th id="alt_title_desc" class="manage-column column-alt_title_desc" scope="col"><?php _e("Alt & Title Text / Description",'flag')?></th>
	</tr>
	</tfoot>
	<tbody>
<?php
if($picturelist) {
	
	$thumbsize = '';
	if ($flag->options['thumbFix']) {
		$thumbsize = 'width="'.$flag->options['thumbWidth'].'" height="'.$flag->options['thumbHeight'].'"';
	}
	
	if ($flag->options['thumbCrop']) {
		$thumbsize = 'width="'.$flag->options['thumbWidth'].'" height="'.$flag->options['thumbWidth'].'"';
	}
	
		$alternate = '';
	foreach($picturelist as $picture) {

		$pid       = (int) $picture->pid;
		$alternate = ( $alternate == 'alternate' ) ? '' : 'alternate';	
		$date = mysql2date(get_option('date_format'), $picture->imagedate);
		$time = mysql2date(get_option('time_format'), $picture->imagedate);

		?>
		<tr id="picture-<?php echo $pid ?>" class="<?php echo $alternate ?> iedit"  valign="top">
						<th class="column-cb" scope="row"><input name="doaction[]" type="checkbox" value="<?php echo $pid ?>" /></th>
						<td class="column-id"><?php echo $pid; ?>
							<input type="hidden" name="pid[]" value="<?php echo $pid ?>" />
						</td>
						<td class="column-thumbnail"><a href="<?php echo $picture->imageURL; ?>" class="thickbox" title="<?php echo $picture->filename ?>">
								<img class="thumb" src="<?php echo $picture->thumbURL; ?>" <?php echo $thumbsize ?> id="thumb-<?php echo $pid ?>" />
							</a>
						</td>
						<td class="column-filename">
							<strong><a href="<?php echo $picture->imageURL; ?>" class="thickbox" title="<?php echo $picture->filename ?>">
								<?php echo ( empty($picture->alttext) ) ? $picture->filename : stripslashes(flagGallery::i18n($picture->alttext)); ?>
							</a></strong>
							<br /><?php echo $date ?>
							<?php  $imgpath = WINABSPATH.$picture->path."/".$picture->filename; 
								$img = @getimagesize($imgpath); if($img) echo '<br />Size: '.$img[0].'x'.$img[1].' px'; 
							?>
							<p>
							<?php
							$actions = array();
							$actions['view']   = '<a class="thickbox" href="' . $picture->imageURL . '" title="' . attribute_escape(sprintf(__('View "%s"'), $picture->filename)) . '">' . __('View', 'flag') . '</a>';
							$actions['custom_thumb']   = '<a class="thickbox" href="' . FLAG_URLPATH . 'admin/manage_thumbnail.php?id=' . $pid . '" title="' . __('Customize thumbnail','flag') . '">' . __('Edit thumb', 'flag') . '</a>';
							$actions['delete'] = '<a class="submitdelete" href="' . wp_nonce_url("admin.php?page=flag-manage-gallery&amp;mode=delpic&amp;gid=".$act_gid."&amp;pid=".$pid, 'flag_delpicture'). '" class="delete column-delete" onclick="javascript:check=confirm( \'' . attribute_escape(sprintf(__('Delete "%s"' , 'flag'), $picture->filename)). '\');if(check==false) return false;">' . __('Delete') . '</a>';
							$action_count = count($actions);
							$i = 0;
							echo '<div class="row-actions">';
							foreach ( $actions as $action => $link ) {
								++$i;
								( $i == $action_count ) ? $sep = '' : $sep = ' | ';
								echo "<span class='$action'>$link$sep</span>";
							}
							echo '</div>';
							?></p>
						</td>
						<td class="column-alt_title_desc">
							<input name="alttext[<?php echo $pid ?>]" type="text" style="width:95%; margin-bottom: 2px;" value="<?php echo stripslashes($picture->alttext) ?>" /><br/>
							<textarea name="description[<?php echo $pid ?>]" style="width:95%; margin-top: 2px;" rows="2" ><?php echo stripslashes($picture->description) ?></textarea>
						</td>
		</tr>
		<?php
	}
} else {
	echo '<tr><td colspan="5" align="center"><strong>'.__('No entries found','flag').'</strong></td></tr>';
}
?>
	
		</tbody>
	</table>
	<p class="submit"><input type="submit" class="button-primary action" name="updatepictures" value="<?php _e("Save Changes",'flag')?>" /></p>
	</form>	
	<br class="clear"/>
	</div><!-- /#wrap -->

	<!-- #selectgallery -->
	<div id="selectgallery" style="display: none;" >
		<form id="form-select-gallery" method="POST" accept-charset="utf-8">
		<?php wp_nonce_field('flag_thickbox_form') ?>
		<input type="hidden" id="selectgallery_imagelist" name="TB_imagelist" value="" />
		<input type="hidden" id="selectgallery_bulkaction" name="TB_bulkaction" value="" />
		<table width="100%" border="0" cellspacing="3" cellpadding="3" >
		  	<tr>
		    	<th>
		    		<?php _e('Select the destination gallery:', 'flag'); ?>&nbsp;
		    		<select name="dest_gid" style="width:90%" >
		    			<?php 
		    				foreach ($gallerylist as $gallery) { 
		    					if ($gallery->gid != $act_gid) { 
		    			?>
						<option value="<?php echo $gallery->gid; ?>" ><?php echo $gallery->gid; ?> - <?php echo stripslashes($gallery->title); ?></option>
						<?php 
		    					} 
		    				}
		    			?>
		    		</select>
		    	</th>
		  	</tr>
		  	<tr align="right">
		    	<td class="submit">
		    		<input type="submit" class="button-primary" name="TB_SelectGallery" value="<?php _e("OK",'flag')?>" />
		    		&nbsp;
		    		<input class="button-secondary" type="reset" value="<?php _e("Cancel",'flag')?>" onclick="tb_remove()"/>
		    	</td>
			</tr>
		</table>
		</form>
	</div>
	<!-- /#selectgallery -->

<?php
}
?>
