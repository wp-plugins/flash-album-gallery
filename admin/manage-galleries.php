<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

// *** show main gallery list
function flag_manage_gallery_main() {

	global $flag, $flagdb, $wp_query;
	
	//Build the pagination for more than 25 galleries
	if ( ! isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
		$_GET['paged'] = 1;
	
	$start = ( $_GET['paged'] - 1 ) * 25;
	$gallerylist = $flagdb->find_all_galleries('gid', 'asc', TRUE, 25, $start, false);

	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%' ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $flagdb->paged['max_objects_per_page'],
		'current' => $_GET['paged']
	));
		
	?>
	<script type="text/javascript"> 
	<!--
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
	
		var numchecked = getNumChecked(document.getElementById('editgalleries'));
		 
		if(numchecked < 1) { 
			alert('<?php echo js_escape(__('No images selected', 'flag')); ?>');
			return false; 
		} 
		
		actionId = jQuery('#bulkaction').val();
		
		switch (actionId) {
			case "resize_images":
				showDialog('resize_images', 120);
				return false;
				break;
			case "new_thumbnail":
				showDialog('new_thumbnail', 160);
				return false;
				break;
		}
		
		return confirm('<?php echo sprintf(js_escape(__("You are about to start the bulk edit for %s galleries \n \n 'Cancel' to stop, 'OK' to proceed.",'flag')), "' + numchecked + '") ; ?>');
	}

	function showDialog( windowId, height ) {
		var form = document.getElementById('editgalleries');
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
		tb_show("", "#TB_inline?width=640&height=" + height + "&inlineId=" + windowId + "&modal=true", false);
	}
	
	//-->
	</script>
	<div class="wrap">
		<h2><?php _e('Gallery Overview', 'flag') ?></h2>
		<form class="search-form" action="" method="get">
		<p class="search-box">
			<label class="hidden" for="media-search-input"><?php _e( 'Search Images', 'flag' ); ?>:</label>
			<input type="hidden" id="page-name" name="page" value="flag-manage-gallery" />
			<input type="text" id="media-search-input" name="s" value="<?php the_search_query(); ?>" />
			<input type="submit" value="<?php _e( 'Search Images', 'flag' ); ?>" class="button" />
		</p>
		</form>
		<form id="editgalleries" class="flagform" method="POST" action="<?php echo $flag->manage_page->base_page . '&amp;paged=' . $_GET['paged']; ?>" accept-charset="utf-8">
		<?php wp_nonce_field('flag_bulkgallery') ?>
		<input type="hidden" name="page" value="manage-galleries" />
		
		<div class="tablenav">
			
			<div class="alignleft actions">
				<?php if ( function_exists('json_encode') ) : ?>
				<select name="bulkaction" id="bulkaction">
					<option value="no_action" ><?php _e("No action",'flag'); ?></option>
					<option value="new_thumbnail" ><?php _e("Create new thumbnails",'flag'); ?></option>
					<option value="resize_images" ><?php _e("Resize images",'flag'); ?></option>
					<option value="import_meta" ><?php _e("Import metadata",'flag'); ?></option>
				</select>
				<input name="showThickbox" class="button-secondary" type="submit" value="<?php _e('Apply','flag'); ?>" onclick="if ( !checkSelected() ) return false;" />
				<?php endif; ?>
			</div>
			
		<?php if ( $page_links ) : ?>
			<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
				number_format_i18n( ( $_GET['paged'] - 1 ) * $flagdb->paged['objects_per_page'] + 1 ),
				number_format_i18n( min( $_GET['paged'] * $flagdb->paged['objects_per_page'], $flagdb->paged['total_objects'] ) ),
				number_format_i18n( $flagdb->paged['total_objects'] ),
				$page_links
			); echo $page_links_text; ?></div>
			<br class="clear" />
		<?php endif; ?>
		
		</div>
		<table class="widefat" cellspacing="0">
			<thead>
			<tr>
				<th scope="col" class="cb column-cb" >
					<input type="checkbox" onclick="checkAll(document.getElementById('editgalleries'));" name="checkall"/>
				</th>
				<th scope="col" ><?php _e('ID') ?></th>
				<th scope="col" width="25%"><?php _e('Title', 'flag') ?></th>
				<th scope="col" width="55%"><?php _e('Description', 'flag') ?></th>
				<th scope="col" ><?php _e('Author', 'flag') ?></th>
				<th scope="col" ><?php _e('Quantity', 'flag') ?></th>
				<th scope="col" ><?php _e('Action', 'flag'); ?></th>
			</tr>
			</thead>
			<tbody>
<?php
if($gallerylist) {
	foreach($gallerylist as $gallery) {
		$class = ( !isset($class) || $class == 'class="alternate"' ) ? '' : 'class="alternate"';
		$gid = $gallery->gid;
		$name = (empty($gallery->title) ) ? $gallery->name : $gallery->title;
		$author_user = get_userdata( (int) $gallery->author );
		?>
		<tr id="gallery-<?php echo $gid ?>" <?php echo $class; ?> >
			<th scope="row" class="cb column-cb">
				<?php if (flagAdmin::can_manage_this_gallery($gallery->author)) { ?>
					<input name="doaction[]" type="checkbox" value="<?php echo $gid ?>" />
				<?php } ?>
			</th>
			<td scope="row"><?php echo $gid; ?></td>
			<td>
				<?php if (flagAdmin::can_manage_this_gallery($gallery->author)) { ?>
					<a href="<?php echo wp_nonce_url( $flag->manage_page->base_page . "&amp;mode=edit&amp;gid=" . $gid, 'flag_editgallery')?>" class='edit' title="<?php _e('Edit') ?>" >
						<?php echo flagGallery::i18n($name); ?>
					</a>
				<?php } else { ?>
					<?php echo flagGallery::i18n($gallery->title); ?>
				<?php } ?>
			</td>
			<td><?php echo flagGallery::i18n($gallery->galdesc); ?>&nbsp;</td>
			<td><?php echo $author_user->display_name; ?></td>
			<td><?php echo $gallery->counter; ?></td>
			<td>
				<?php if (flagAdmin::can_manage_this_gallery($gallery->author)) : ?>
					<a href="<?php echo wp_nonce_url( $flag->manage_page->base_page . "&amp;mode=delete&amp;gid=" . $gid, 'flag_editgallery')?>" class="delete" onclick="javascript:check=confirm( '<?php _e("Delete this gallery ?",'flag')?>');if(check==false) return false;"><?php _e('Delete','flag') ?></a>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}
} else {
	echo '<tr><td colspan="7" align="center"><strong>'.__('No entries found','flag').'</strong></td></tr>';
}
?>			
			</tbody>
		</table>
		</form>
	</div>

	<!-- #resize_images -->
	<div id="resize_images" style="display: none;" >
		<form id="form_resize_images" method="POST" accept-charset="utf-8">
		<?php wp_nonce_field('flag_thickbox_form') ?>
		<input type="hidden" id="resize_images_imagelist" name="TB_imagelist" value="" />
		<input type="hidden" id="resize_images_bulkaction" name="TB_bulkaction" value="" />
		<input type="hidden" name="page" value="manage-galleries" />
		<table width="100%" border="0" cellspacing="3" cellpadding="3" >
			<tr valign="top">
				<td>
					<strong><?php _e('Resize Images to', 'flag'); ?>:</strong> 
				</td>
				<td>
					<input type="text" size="5" name="imgWidth" value="<?php echo $flag->options['imgWidth']; ?>" /> x <input type="text" size="5" name="imgHeight" value="<?php echo $flag->options['imgHeight']; ?>" />
					<br /><small><?php _e('Width x height (in pixel). FlAGallery will keep ratio size','flag') ?></small>
				</td>
			</tr>
		  	<tr align="right">
		    	<td colspan="2" class="submit">
		    		<input class="button-primary" type="submit" name="TB_ResizeImages" value="<?php _e('OK', 'flag'); ?>" />
		    		&nbsp;
		    		<input class="button-secondary" type="reset" value="&nbsp;<?php _e('Cancel', 'flag'); ?>&nbsp;" onclick="tb_remove()"/>
		    	</td>
			</tr>
		</table>
		</form>
	</div>
	<!-- /#resize_images -->

	<!-- #new_thumbnail -->
	<div id="new_thumbnail" style="display: none;" >
		<form id="form_new_thumbnail" method="POST" accept-charset="utf-8">
		<?php wp_nonce_field('flag_thickbox_form') ?>
		<input type="hidden" id="new_thumbnail_imagelist" name="TB_imagelist" value="" />
		<input type="hidden" id="new_thumbnail_bulkaction" name="TB_bulkaction" value="" />
		<input type="hidden" name="page" value="manage-galleries" />
		<table width="100%" border="0" cellspacing="3" cellpadding="3" >
			<tr valign="top">
				<th align="left"><?php _e('Width x height (in pixel)','flag') ?></th>
				<td><input type="text" size="5" maxlength="5" name="thumbWidth" value="<?php echo $flag->options['thumbWidth']; ?>" /> x <input type="text" size="5" maxlength="5" name="thumbHeight" value="<?php echo $flag->options['thumbHeight']; ?>" />
				<br /><small><?php _e('These values are maximum values ','flag') ?></small></td>
			</tr>
			<tr valign="top">
				<th align="left"><?php _e('Set fix dimension','flag') ?></th>
				<td><input type="checkbox" name="thumbFix" value="1" <?php checked('1', $flag->options['thumbFix']); ?> />
				<br /><small><?php _e('Ignore the aspect ratio, no portrait thumbnails','flag') ?></small></td>
			</tr>
		  	<tr align="right">
		    	<td colspan="2" class="submit">
		    		<input class="button-primary" type="submit" name="TB_NewThumbnail" value="<?php _e('OK', 'flag');?>" />
		    		&nbsp;
		    		<input class="button-secondary" type="reset" value="&nbsp;<?php _e('Cancel', 'flag'); ?>&nbsp;" onclick="tb_remove()"/>
		    	</td>
			</tr>
		</table>
		</form>
	</div>
	<!-- /#new_thumbnail -->	

<?php
} 
?>