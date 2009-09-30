<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

// *** show main gallery list
function flag_manage_gallery_main() {

	global $wpdb, $flag, $flagdb, $wp_query;
	
	if ( ! isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
		$_GET['paged'] = 1;
	
	$start = ( $_GET['paged'] - 1 ) * 25;
	$gallerylist = $flagdb->find_all_galleries('gid', 'asc', TRUE, 25, $start);

	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%' ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $flagdb->paged['max_objects_per_page'],
		'current' => $_GET['paged']
	));
		
	?>
	<div class="wrap">
		<h2><?php _e('Gallery Overview', 'flag') ?></h2>
		<?php if ( $page_links ) : ?>
		<div class="tablenav">
			<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
				number_format_i18n( ( $_GET['paged'] - 1 ) * $flagdb->paged['objects_per_page'] + 1 ),
				number_format_i18n( min( $_GET['paged'] * $flagdb->paged['objects_per_page'], $flagdb->paged['total_objects'] ) ),
				number_format_i18n( $flagdb->paged['total_objects'] ),
				$page_links
			); echo $page_links_text; ?></div>
		<br class="clear" />
		</div>
		<?php endif; ?>
		<table class="widefat" cellspacing="0">
			<thead>
			<tr>
				<th scope="col" ><?php _e('ID') ?></th>
				<th scope="col" ><?php _e('Title', 'flag') ?></th>
				<th scope="col" ><?php _e('Description', 'flag') ?></th>
				<th scope="col" ><?php _e('Author', 'flag') ?></th>
				<th scope="col" ><?php _e('Quantity', 'flag') ?></th>
				<th scope="col" ><?php _e('Action'); ?></th>
			</tr>
			</thead>
			<tbody>
<?php
		$alt = '';
if($gallerylist) {
	foreach($gallerylist as $gallery) {
		$alt = ( $alt == 'class="alternate"' ) ? '' : 'class="alternate"';
		$gid = $gallery->gid;
		if( empty($gallery->title) ) {
			$name = $gallery->name;
		} else {
			$name = attribute_escape(stripslashes($gallery->title));
		}
		$author_user = get_userdata( (int) $gallery->author );
		?>
		<tr id="gallery-<?php echo $gid ?>" <?php echo $alt; ?> >
			<th scope="row"><?php echo $gid; ?></th>
			<td>
					<a href="<?php echo wp_nonce_url( $flag->manage_page->base_page . "&amp;mode=edit&amp;gid=" . $gid, 'flag_editgallery')?>" class='edit' title="<?php _e('Edit') ?>" >
						<?php echo flagGallery::i18n($name); ?>
					</a>
			</td>
			<td><?php echo flagGallery::i18n($gallery->galdesc); ?>&nbsp;</td>
			<td><?php echo $author_user->display_name; ?></td>
			<td><?php echo $gallery->counter; ?></td>
			<td>
				<?php if (flagAdmin::can_manage_this_gallery($gallery->author)) : ?>
					<a href="<?php echo wp_nonce_url( $flag->manage_page->base_page . "&amp;mode=delete&amp;gid=" . $gid, 'flag_editgallery')?>" class="delete" onclick="javascript:check=confirm( '<?php _e("Delete this gallery ?",'flag')?>');if(check==false) return false;"><?php _e('Delete') ?></a>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}
} else {
	echo '<tr><td colspan="6" align="center"><strong>'.__('No entries found','flag').'</strong></td></tr>';
}
?>			
			</tbody>
		</table>
	</div>
<?php
} 
?>