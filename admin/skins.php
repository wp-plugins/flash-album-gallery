<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
// look up for the path
require_once( dirname(__FILE__) . '/../flag-config.php');

// check for correct capability
if ( !is_user_logged_in() )
	die('-1');

// check for correct FlAG capability
if ( !current_user_can('FlAG Change skin') ) 
	die('-1');	

require_once (dirname (__FILE__) . '/get_skin.php');

add_action('install_skins_dashboard', 'install_skins_dashboard');
function install_skins_dashboard() {
	?>
	<ul id="tabs" class="tabs">
		<li class="selected"><a href="#" rel="addskin"><?php _e('Add new skin', 'flag') ;?></a></li>
		<li><a href="#" rel="wantmore"><?php _e('Want more skins?', 'flag') ;?></a></li>
	<?php if(isset($_GET['action']))
		echo '<li><a href="#" rel="uploadaction">'. __('Action', 'flag') . '</a></li>';
	?>
	</ul>

	<div id="addskin" class="cptab">
		<h2><?php _e('Add new skin', 'flag') ;?></h2>
	<h4><?php _e('Install a skin in .zip format', 'flag') ?></h4>
	<p><?php _e('If you have a skin in a .zip format, You may install it by uploading it here.', 'flag') ?></p>
	<form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin.php?page=flag-skins&action=upload&tabs=2') ?>">
		<?php wp_nonce_field( 'skin-upload') ?>
		<p><input type="file" name="skinzip" />
		<input type="submit" class="button" value="<?php _e('Install Now') ?>" /></p>
	</form>
	</div>
	
	<div id="wantmore" class="cptab">
		<h2><?php _e('More skins', 'flag') ;?></h2>
	<p><?php _e('If you want more skins, You may get it at.', 'flag') ?> <a target="_blank" href="http://photogallerycreator.com">PhotoGalleryCreator.com</a></p>
	</div>
<?php }
add_action('install_skins_upload', 'upload_skin');
function upload_skin() {

	echo '<div id="uploadaction" class="cptab">';
	echo '<h2>'.__('Install info', 'flag').'</h2>';

	if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) ) {
		echo "<p>".$uploads['error']."</p>\n";
	} else {
		if ( !empty($_FILES) ) {
			$filename = $_FILES['skinzip']['name'];
		}	else if ( isset($_GET['package']) ) {
			$filename = $_GET['package'];
		}
		if ( !$filename ) {
			echo "<p>".__('No skin Specified', 'flag')."</p>\n";
		} else {
			check_admin_referer('skin-upload');
			echo '<h4>', sprintf( __('Installing Skin from file: %s', 'flag'), basename($filename) ), '</h4>';

			//Handle a newly uploaded file, Else assume it was
			if ( !empty($_FILES) ) {
				$filename = wp_unique_filename( $uploads['basedir'], $filename );
				$local_file = $uploads['basedir'] . '/' . $filename;

				// Move the file to the uploads dir
				if ( false === @ move_uploaded_file( $_FILES['skinzip']['tmp_name'], $local_file) )
					echo "<p>".sprintf( __('The uploaded file could not be moved to %s.', 'flag'), $uploads['path'])."</p>\n";
			} else {
				$local_file = $uploads['basedir'] . '/' . $filename;
			}
			do_skin_install_local_package($local_file, $filename);
		}
	}
	echo '</div>';
}

/**
 * Install a skin from a local file.
 *
 */
function do_skin_install_local_package($package, $filename = '') {
	global $wp_filesystem;

	if ( empty($package) ) {
		show_message( __('No skin Specified', 'flag') );
		return;
	}

	if ( empty($filename) )
		$filename = basename($package);

	$url = 'admin.php?page=flag-skins&action=upload&tabs=2';
	$url = add_query_arg(array('package' => $filename), $url);

	$url = wp_nonce_url($url, 'skin-upload');
	if ( false === ($credentials = request_filesystem_credentials($url)) )
		return;

	if ( ! WP_Filesystem($credentials) ) {
		request_filesystem_credentials($url, '', true); //Failed to connect, Error and request again
		return;
	}

	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
			show_message($message);
		return;
	}

	$result = wp_install_skin_local_package( $package, 'show_message' );

	if ( is_wp_error($result) ) {
		show_message($result);
		show_message( __('Installation Failed', 'flag') );
	} else {
		show_message( __('The skin installed successfully.', 'flag') );
		$skin_file = $result;
		$install_actions = apply_filters('install_skin_complete_actions', array(
							'activate_skin' => '<a href="'.admin_url('admin.php?page=flag-skins&skin='.$skin_file).'" title="' . __('Activate this skin', 'flag') . '" target="_parent">' . __('Activate Skin', 'flag') . '</a>',
							'skins_page' => '<a href="#'.basename($skin_file, '.php').'" title="' . __('Goto skin overview', 'flag') . '" target="_parent">' . __('Skin overview', 'flag') . '</a>'
							), array(), $skin_file);
		if ( ! empty($install_actions) )
			show_message('<strong>' . __('Actions:', 'flag') . '</strong> ' . implode(' | ', (array)$install_actions));
	}
}

/**
 * Install skin from local package
 *
 */
function wp_install_skin_local_package($package, $feedback = '') {
	global $wp_filesystem;

	if ( !empty($feedback) )
		add_filter('install_feedback', $feedback);

	// Is a filesystem accessor setup?
	if ( ! $wp_filesystem || ! is_object($wp_filesystem) )
		WP_Filesystem();

	if ( ! is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', __('Could not access filesystem.', 'flag'));

	if ( $wp_filesystem->errors->get_error_code() )
		return new WP_Error('fs_error', __('Filesystem error', 'flag'), $wp_filesystem->errors);

	//Get the base skin folder
	$flag_options = get_option('flag_options');
	$skins_dir = $flag_options['skinsDirABS'];
	if ( empty($skins_dir) )
		return new WP_Error('fs_no_skins_dir', __('Unable to locate FlAGallery Skin directory.', 'flag'));

	//And the same for the Content directory.
	$content_dir = $wp_filesystem->wp_content_dir();
	if( empty($content_dir) )
		return new WP_Error('fs_no_content_dir', __('Unable to locate WordPress Content directory (wp-content).', 'flag'));

	$skins_dir = trailingslashit( $skins_dir );
	$content_dir = trailingslashit( $content_dir );

	if ( empty($package) )
		return new WP_Error('no_package', __('Install package not available.', 'flag'));

	$working_dir = $content_dir . 'upgrade/' . basename($package, '.zip');

	// Clean up working directory
	if ( $wp_filesystem->is_dir($working_dir) )
		$wp_filesystem->delete($working_dir, true);

	apply_filters('install_feedback', __('Unpacking the skin package', 'flag'));
	// Unzip package to working directory
	$result = unzip_file($package, $working_dir);

	// Once extracted, delete the package
	unlink($package);

	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($working_dir, true);
		return $result;
	}

	//Get a list of the directories in the working directory before we delete it, We need to know the new folder for the skin
	$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );

	if( $wp_filesystem->exists( $skins_dir . $filelist[0] ) ) {
		$wp_filesystem->delete($working_dir, true);
		return new WP_Error('install_folder_exists', __('Folder already exists.', 'flag'), $filelist[0] );
	}

	apply_filters('install_feedback', __('Installing the skin', 'flag'));
	// Copy new version of skin into place.
	$result = copy_dir($working_dir, $skins_dir);
	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($working_dir, true);
		return $result;
	}

	//Get a list of the directories in the working directory before we delete it, We need to know the new folder for the skin
	$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );

	// Remove working directory
	$wp_filesystem->delete($working_dir, true);

	if( empty($filelist) )
		return false; //We couldnt find any files in the working dir, therefor no skin installed? Failsafe backup.

	$folder = $filelist[0];
	$skin = get_skins('/' . $folder); //Ensure to pass with leading slash
	$skinfiles = array_keys($skin); //Assume the requested skin is the first in the list

	//Return the skin files name.
	return  $folder . '/' . $skinfiles[0];
}

if( isset($_GET['skin']) ) {
	global $blog_id, $flag;

	$set_skin = dirname($_GET['skin']);
	$flag_options = get_option('flag_options');
	// Flash settings
	$flag_options['flashSkin'] = $set_skin; 

	include_once ( $flag_options['skinsDirABS'].$_GET['skin'] );	// activate skin

	update_option('flag_options', $flag_options);
}

if( current_user_can('FlAG Add skins') ) {
	echo '<div id="slider" class="wrap">';

	do_action('install_skins_dashboard');
	if( isset($_GET['action']) ) {
		do_action('install_skins_upload');
	}

	echo '<script type="text/javascript">	
	/* <![CDATA[ */
	var cptabs=new ddtabcontent("tabs");
	cptabs.setpersist(false);
	cptabs.setselectedClassTarget("linkparent");
	cptabs.init();

	jQuery(document).ready(function(){
		jQuery("#tabs a[rel=uploadaction]").parent("li").siblings().click(function () {
			jQuery("#tabs a[rel=uploadaction]").hide("slow");
		});
	});
	/* ]]> */
	</script>';
	echo '</div>';
} 
?>


<div class="wrap">
<h2><?php _e('Skins', 'flag'); ?></h2>

<?php

$all_skins = get_skins();
$total_all_skins = count($all_skins);
$flag_options = get_option ('flag_options');
?>
<table class="widefat" cellspacing="0" id="skins-table">
	<thead>
	<tr>
		<th scope="col" class="manage-column"><?php _e('Skin', 'flag'); ?></th>
		<th scope="col" class="manage-column"><?php _e('Description', 'flag'); ?></th>
		<th scope="col" class="action-links"><?php _e('Action', 'flag'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column"><?php _e('Skin', 'flag'); ?></th>
		<th scope="col" class="manage-column"><?php _e('Description', 'flag'); ?></th>
		<th scope="col" class="action-links"><?php _e('Action', 'flag'); ?></th>
	</tr>
	</tfoot>

	<tbody class="skins">
<?php

	if ( empty($all_skins) ) {
		echo '<tr>
			<td colspan="3">' . __('No skins to show') . '</td>
		</tr>';
	}
	foreach ( (array)$all_skins as $skin_file => $skin_data) {
		$actions = array();
		$is_active = false /*is_skin_active($skin_file)*/;
		$class = ( dirname($skin_file) == $flag_options['flashSkin'] ) ? 'active' : 'inactive';
		echo "
	<tr id='".basename($skin_file, '.php')."' class='$class first'>
		<td class='skin-title'><strong>{$skin_data['Name']}</strong></td>
		<td class='desc'>";
		$skin_meta = array();
		if ( !empty($skin_data['Version']) )
			$skin_meta[] = sprintf(__('Version %s', 'flag'), $skin_data['Version']);
		if ( !empty($skin_data['Author']) ) {
			$author = $skin_data['Author'];
			if ( !empty($skin_data['AuthorURI']) )
				$author = '<a href="' . $skin_data['AuthorURI'] . '" title="' . __( 'Visit author homepage', 'flag' ) . '">' . $skin_data['Author'] . '</a>';
			$skin_meta[] = sprintf( __('By %s', 'flag'), $author );
		}
		if ( ! empty($skin_data['SkinURI']) )
			$skin_meta[] = '<a href="' . $skin_data['SkinURI'] . '" title="' . __( 'Visit skin site', 'flag' ) . '">' . __('Visit skin site', 'flag' ) . '</a>';

		echo implode(' | ', $skin_meta);
		echo "</td>";
		echo "<td class='skin-activate action-links'>";
		if ( dirname($skin_file) != $flag_options['flashSkin'] ) {
			echo '<strong><a href="'.admin_url('admin.php?page=flag-skins&skin='.$skin_file).'" title="' . __( 'Activate this skin', 'flag' ) . '">' . __('Activate', 'flag' ) . '</a></strong>';
 		} else {
 			echo "<strong>".__('Activated', 'flag' )."</strong>";
 		}
		echo "</td>";
/* // delete link
		echo "<td class='skin-delete action-links'>";
		if ( current_user_can('FlAG Delete skins') ) {
			echo '<a class="delete" href="'.wp_nonce_url( admin_url('admin.php?page=flag-skins&delete='.dirname($skin_file)), 'flag_deleteskin').'" title="' . __( 'Delete this skin', 'flag' ) . '">' . __('Delete', 'flag' ) . '</a>';
 		}
		echo "</td>";
*/
	echo "</tr>
	<tr class='$class second'>
		<td class='skin-title'><img src='".$flag_options['skinsDirURL'].dirname($skin_file)."/".basename($skin_file, '.php').".png' alt='{$skin_data['Name']}' title='{$skin_data['Name']}' /></td>
		<td class='desc' colspan='2'><p>{$skin_data['Description']}</p></td>";
	echo "</tr>\n";
	}
?>
	</tbody>
</table>
</div>
<?php ?>