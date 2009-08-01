<?php  
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

/**
 * flag_admin_overview()
 *
 * Add the admin overview in wp2.7 style 
 * @return mixed content
 */
function flag_admin_overview()  {	
?>
<div class="wrap flag-wrap">
	<h2><?php _e('FlAG Gallery Overview', 'flag') ?></h2>
	<div id="flag-overview" class="metabox-holder">
		<div id="side-info-column" class="inner-sidebar" style="display:block;">
				<?php do_meta_boxes(FLAGFOLDER, 'side', null); ?>
		</div>
		<div id="post-body" class="has-sidebar">
			<div id="post-body-content" class="has-sidebar-content">
					<?php do_meta_boxes(FLAGFOLDER, 'normal', null); ?>
			</div>
		</div>
	</div>
</div>

<?php
wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
?>
<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function() {
		jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	// postboxes
	<?php
	global $wp_version;
	if(version_compare($wp_version,"2.7-alpha", "<")){
		echo "add_postbox_toggles('".FLAGFOLDER."');"; //For WP2.6 and below
	}
	else{
		echo "postboxes.add_postbox_toggles('".FLAGFOLDER."');"; //For WP2.7 and above
	}
	?>
		jQuery('#side-info-column #major-publishing-actions').appendTo('#dashboard_primary');
	});
	//]]>
</script>

<?php
}

/**
 * Show the server settings
 * 
 * @return void
 */
function flag_overview_server() {
?>
<div id="dashboard_server_settings" class="dashboard-widget-holder wp_dashboard_empty">
	<div class="flag-dashboard-widget">
		<div class="dashboard-widget-content">
     	<ul class="settings">
     		<?php get_serverinfo(); ?>
	  	</ul>
		</div>
  </div>
</div>
<?php	
}

/**
 * Show the GD ibfos
 * 
 * @return void
 */
function flag_overview_graphic_lib() {
?>
<div id="dashboard_server_settings" class="dashboard-widget-holder">
	<div class="flag-dashboard-widget">
	  	<div class="dashboard-widget-content">
	  		<ul class="settings">
			<?php flag_GD_info(); ?>
			</ul>
		</div>
    </div>
</div>
<?php	
}

/**
 * Show the Setup Box and some info for Flash Album Gallery
 * 
 * @return void
 */
function flag_overview_setup(){ 
	global $wpdb, $flag;
			
	if (isset($_POST['resetdefault'])) {	
		check_admin_referer('flag_uninstall');
					
		include_once ( dirname (__FILE__).  '/flag_install.php');
		
		flag_default_options();
		$flag->load_options();
		
		flagGallery::show_message(__('Reset all settings to default parameter','flag'));
	}

	if (isset($_POST['uninstall'])) {	
		
		check_admin_referer('flag_uninstall');
		
		include_once ( dirname (__FILE__).  '/flag_install.php');

		flag_uninstall();
			 	
	 	flagGallery::show_message(__('Uninstall sucessful ! Now delete the plugin and enjoy your life ! Good luck !','flag'));
	}
?>
		<div class="submitbox" id="submitpost">
			<div id="minor-publishing">
				<div id="misc-publishing-actions">
					<div class="misc-pub-section">
						<span id="plugin-home" class="icon">
							<strong><a href="http://codeasily.com/wordpress-plugins/flash-album-gallery/flag" style="text-decoration: none;"><?php _e('Plugin Home','flag'); ?></a></strong>
						</span>
					</div>
					<div class="misc-pub-section">
						<span id="plugin-comments" class="icon">
							<a href="http://codeasily.com/wordpress-plugins/flash-album-gallery/flag#comments" style="text-decoration: none;"><?php _e('Plugin Comments','flag'); ?></a>
						</span>
					</div>
					<!-- <div class="misc-pub-section">
						<span id="rate-plugin" class="icon">
							<a href="#" style="text-decoration: none;"><?php _e('Rate Plugin','flag'); ?></a>
						</span>
					</div>
					<div class="misc-pub-section">
						<span id="my-plugins" class="icon">
							<a href="http://codeasily.com/category/wordpress-plugins" style="text-decoration: none;"><?php _e('My Plugins','flag'); ?></a>
						</span>
					</div> -->
					<div class="misc-pub-section curtime misc-pub-section-last">
						<span id="contact-me" class="icon">
							<a href="http://codeasily.com/about" style="text-decoration: none;"><?php _e('Contact Me','flag'); ?></a>
						</span>
					</div>
				</div>
			</div>
		</div>
	<div id="major-publishing-actions">
	<form id="resetsettings" name="resetsettings" method="post">
		<?php wp_nonce_field('flag_uninstall') ?>
			<div id="save-action" class="alignleft">
				<input class="button" id="save-post" type="submit" name="resetdefault" value="<?php _e('Reset settings', 'flag') ;?>" onclick="javascript:check=confirm('<?php _e('Reset all options to default settings ?\n\nChoose [Cancel] to Stop, [OK] to proceed.\n','flag'); ?>');if(check==false) return false;" />
			</div>
			<div id="preview-action" class="alignright">
				<input type="submit" name="uninstall" class="button delete" value="<?php _e('Uninstall plugin', 'flag') ?>" onclick="javascript:check=confirm('<?php _e('You are about to Uninstall this plugin from WordPress.\nThis action is not reversible.\n\nChoose [Cancel] to Stop, [OK] to Uninstall.\n','flag'); ?>');if(check==false) return false;" />
			</div>
			<br class="clear" />
	</form>
	</div>

<?php
}
/**
 * Show the News Box
 * 
 * @return void
 */
function flag_news_box(){ 
?>
<script type="text/javascript">
/*<![CDATA[*/
jQuery(document).ready(function(){
jQuery("#photogallerycreator").load("<?php echo FLAG_URLPATH ?>admin/news.php #flag-skin-gallery td:lt(10) > div", {want2Read:'http://photogallerycreator.com/2009/07/skins-for-flash-album-gallery/'},function(){
//Write your additional jQuery script below. Use as many functions as you like, for instance:
jQuery("#photogallerycreator div").css({border:"1px solid #dedede", margin:"5px", padding:"10px 0"});
jQuery("#photogallerycreator img").css({display:"block", margin:"0 auto 5px auto"});
});
});
/*]]>*/
</script>
		<p><?php _e("What's new at PhotoGalleryCreator.com","flag"); ?></p>
		<div id="photogallerycreator" style="text-align:center; overflow:auto; max-height:480px;"></div>
<?php
}

/**
 * Show a summary of the used images
 * 
 * @return void
 */
function flag_overview_right_now() {
	global $wpdb;
	$images    = intval( $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->flagpictures") );
	$galleries = intval( $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->flaggallery") );
?>

<p class="sub"><?php _e('At a Glance', 'flag'); ?></p>
<div class="table">
	<table>
		<tbody>
			<tr class="first">
				<td class="first b"><a href="admin.php?page=flag-manage-gallery&tabs=1"><?php echo $images; ?></a></td>
				<td class="t"><?php echo __ngettext( 'Image', 'Images', $images, 'flag' ); ?></td>
				<td class="b"></td>
				<td class="last"></td>
			</tr>
			<tr>
				<td class="first b"><a href="admin.php?page=flag-manage-gallery&tabs=0"><?php echo $galleries; ?></a></td>
				<td class="t"><?php echo __ngettext( 'Gallery', 'Galleries', $galleries, 'flag' ); ?></td>
				<td class="b"></td>
				<td class="last"></td>
			</tr>
		</tbody>
	</table>
</div>
<div class="versions">
    <p>
			<?php if(current_user_can('FlAG Upload images')): ?><a class="button rbutton" href="admin.php?page=manage-gallery&tabs=1"><strong><?php _e('Upload pictures', 'flag') ?></strong></a><?php endif; ?>
			<?php _e('Here you can control your images and galleries.', 'flag') ?></p>
		<span><?php
			$userlevel = '<span class="b">' . (current_user_can('manage_options') ? __('Gallery Administrator', 'flag') : __('Gallery Editor', 'flag')) . '</span>';
        printf(__('You currently have %s rights.', 'flag'), $userlevel);
    ?></span>
</div>
<?php
}

add_meta_box('dashboard_primary', __('Setup Box', 'flag'), 'flag_overview_setup', FLAGFOLDER, 'side', 'core');
add_meta_box('dashboard_news', __('News Box', 'flag'), 'flag_news_box', FLAGFOLDER, 'side', 'core');
add_meta_box('dashboard_right_now', __('Welcome to FlAG Gallery !', 'flag'), 'flag_overview_right_now', FLAGFOLDER, 'normal', 'core');
add_meta_box('flag_server', __('Server Settings', 'flag'), 'flag_overview_server', FLAGFOLDER, 'normal', 'core');
add_meta_box('flag_gd_lib', __('Graphic Library', 'flag'), 'flag_overview_graphic_lib', FLAGFOLDER, 'normal', 'core');

// ***************************************************************
function flag_GD_info() {
	
	if(function_exists("gd_info")){
		$info = gd_info();
		$keys = array_keys($info);
		for($i=0; $i<count($keys); $i++) {
			if(is_bool($info[$keys[$i]]))
				echo "<li> " . $keys[$i] ." : <span>" . flag_GD_Support($info[$keys[$i]]) . "</span></li>\n";
			else
				echo "<li> " . $keys[$i] ." : <span>" . $info[$keys[$i]] . "</span></li>\n";
		}
	}
	else {
		echo '<h4>'.__('No GD support', 'flag').'!</h4>';
	}
}

// ***************************************************************		
function flag_GD_Support($bool){
	if($bool) 
		return __('Yes', 'flag');
	else 
		return __('No', 'flag');
}

// ***************************************************************
function get_serverinfo() {
	global $wpdb;
	// Get MYSQL Version
	$sqlversion = $wpdb->get_var("SELECT VERSION() AS version");
	// GET SQL Mode
	$mysqlinfo = $wpdb->get_results("SHOW VARIABLES LIKE 'sql_mode'");
	if (is_array($mysqlinfo)) $sql_mode = $mysqlinfo[0]->Value;
	if (empty($sql_mode)) $sql_mode = __('Not set', 'flag');
	// Get PHP Safe Mode
	$safe_mode = ini_get('safe_mode') ? __('On', 'flag') : __('Off', 'flag');
	// Get PHP allow_url_fopen
	$allow_url_fopen = ini_get('allow_url_fopen') ? __('On', 'flag') : __('Off', 'flag'); 
	// Get PHP max Upload Size
	$upload_max = ini_get('upload_max_filesize') ? ini_get('upload_max_filesize') : __('N/A', 'flag');
	// Get PHP max Post Size
	$post_max = ini_get('post_max_size') ? ini_get('post_max_size') : __('N/A', 'flag');
	// Get PHP max execution time
	$max_execute = ini_get('max_execution_time') ? ini_get('max_execution_time') : __('N/A', 'flag');
	// Get PHP Memory Limit 
	$memory_limit = ini_get('memory_limit') ? ini_get('memory_limit') : __('N/A', 'flag');
	// Get actual memory_get_usage
	$memory_usage = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 2) . __(' MByte', 'flag') : __('N/A', 'flag');
	// required for EXIF read
	$exif = is_callable('exif_read_data') ? __('Yes', 'flag'). " ( V" . substr(phpversion('exif'),0,4) . ")" : __('No', 'flag');
	// required for meta data
	$iptc = is_callable('iptcparse') ? __('Yes', 'flag') : __('No', 'flag');
	// required for meta data
	$xml = is_callable('xml_parser_create') ? __('Yes', 'flag') : __('No', 'flag');
	
?>
	<li><?php _e('Operating System', 'flag'); ?> : <span><?php echo PHP_OS; ?></span></li>
	<li><?php _e('Server', 'flag'); ?> : <span><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></span></li>
	<li><?php _e('Memory usage', 'flag'); ?> : <span><?php echo $memory_usage; ?></span></li>
	<li><?php _e('MYSQL Version', 'flag'); ?> : <span><?php echo $sqlversion; ?></span></li>
	<li><?php _e('SQL Mode', 'flag'); ?> : <span><?php echo $sql_mode; ?></span></li>
	<li><?php _e('PHP Version', 'flag'); ?> : <span><?php echo PHP_VERSION; ?></span></li>
	<li><?php _e('PHP Safe Mode', 'flag'); ?> : <span><?php echo $safe_mode; ?></span></li>
	<li><?php _e('PHP Allow URL fopen', 'flag'); ?> : <span><?php echo $allow_url_fopen; ?></span></li>
	<li><?php _e('PHP Memory Limit', 'flag'); ?> : <span><?php echo $memory_limit; ?></span></li>
	<li><?php _e('PHP Max Upload Size', 'flag'); ?> : <span><?php echo $upload_max; ?></span></li>
	<li><?php _e('PHP Max Post Size', 'flag'); ?> : <span><?php echo $post_max; ?></span></li>
	<li><?php _e('PHP Max Script Execute Time', 'flag'); ?> : <span><?php echo $max_execute; ?>s</span></li>
	<li><?php _e('PHP Exif support', 'flag'); ?> : <span><?php echo $exif; ?></span></li>
	<li><?php _e('PHP IPTC support', 'flag'); ?> : <span><?php echo $iptc; ?></span></li>
	<li><?php _e('PHP XML support', 'flag'); ?> : <span><?php echo $xml; ?></span></li>
<?php
}


/**
 * get_phpinfo() - Extract all of the data from phpinfo into a nested array
 * 
 * @author jon@sitewizard.ca
 * @return array
 */
function get_phpinfo() {

	ob_start();
	phpinfo();
	$phpinfo = array('phpinfo' => array());
	
	if ( preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER) )
	    foreach($matches as $match) {
	        if(strlen($match[1]))
	            $phpinfo[$match[1]] = array();
	        elseif(isset($match[3]))
	            $phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
	        else
	            $phpinfo[end(array_keys($phpinfo))][] = $match[2];
	    }
	    
	return $phpinfo;
}
?>