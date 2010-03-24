<?php
/**
 * flagAdminPanel - Admin Section for Flash Album Gallery
 * 
 */
class flagAdminPanel{
	
	// constructor
	function flagAdminPanel() {

		// Add the admin menu
		add_action( 'admin_menu', array (&$this, 'add_menu') );
		add_action('init',array (&$this, 'wp_flag_init_messages'),2);

		// Add the script and style files
		add_action('admin_print_scripts', array(&$this, 'load_scripts') );
		add_action('admin_print_styles', array(&$this, 'load_styles') );
		
		add_filter('contextual_help', array(&$this, 'show_help'), 10, 2);
		add_filter('screen_meta_screen', array(&$this, 'edit_screen_meta'));
	}

	function flag_activation_notice_upgrade(){
		
		// Borrowed notice code from all in one seo pack
		if(function_exists('admin_url')){
			echo '<div class="error fade"><p>GRAND FlAGallery v0.40 '.__('(and above) use a different database structure than in previous versions - this was to support new features. You must update your database in order for this version to work - <strong>backup your database first</strong> then', "flag").' <a href="' . admin_url( 'admin.php?page=flash-album-gallery' ) . '">'.__('click here', "flag").'</a> '.__('to run the update script', "flag").'.</strong></p></div>';
		} else {
			echo '<div class="error fade"><p>GRAND FlAGallery v0.40 '.__('(and above) use a different database structure than in previous versions - this was to support new features. You must update your database in order for this version to work - <strong>backup your database first</strong> then', "flag").' <a href="' . get_option('siteurl') . 'admin.php?page=flash-album-gallery' . '">'.__('click here', "flag").'</a> '.__('to run the update script', "flag").'.</strong></p></div>';
		}
		
	}

	function wp_flag_init_messages() {
		global $flag;
		if( get_option( 'flag_db_version' ) != FLAG_DBVERSION ) {
			if (!isset($_GET['action'])) {
				add_action( 'admin_notices', array(&$this, 'flag_activation_notice_upgrade'));
			}
		}
	}

	// integrate the menu	
	function add_menu()  {
		
		add_menu_page( __('GRAND Flash Album Gallery overview','flag'), __('FlAGallery'), 'FlAG overview', FLAGFOLDER, array (&$this, 'show_menu'), FLAG_URLPATH .'admin/images/flag.png' );
	    add_submenu_page( FLAGFOLDER , __('GRAND Flash Album Gallery overview', 'flag'), __('Overview', 'flag'), 'FlAG overview', FLAGFOLDER, array (&$this, 'show_menu'));
	    add_submenu_page( FLAGFOLDER , __('FlAG Manage gallery', 'flag'), __('Manage Galleries', 'flag'), 'FlAG Manage gallery', 'flag-manage-gallery', array (&$this, 'show_menu'));
	    add_submenu_page( FLAGFOLDER , __('FlAG Manage skins', 'flag'), __('Skins', 'flag'), 'FlAG Change skin', 'flag-skins', array (&$this, 'show_menu'));
	    add_submenu_page( FLAGFOLDER , __('FlAG Change options', 'flag'), __('Options', 'flag'), 'FlAG Change options', 'flag-options', array (&$this, 'show_menu'));
		if ( flag_wpmu_site_admin() )
			add_submenu_page( 'wpmu-admin.php' , __('GRAND Flash Album Gallery', 'flag'), __('GRAND FlAGallery', 'flag'), 'activate_plugins', 'flag-wpmu', array (&$this, 'show_menu'));

		//register the column fields
		$this->register_columns();	

	}

	// load the script for the defined page and load only this code	
	function show_menu() {
		
		global $flag;
		
		// check for upgrade
		if( get_option( 'flag_db_version' ) != FLAG_DBVERSION ) {
			include_once ( dirname (__FILE__) . '/functions.php' );
			include_once ( dirname (__FILE__) . '/upgrade.php' );
			flag_upgrade_page();
			return;			
		}
		
		// Set installation date
		if( empty($flag->options['installDate']) ) {
			$flag->options['installDate'] = time();
			update_option('flag_options', $flag->options);			
		}
		
  		switch ($_GET['page']){
			case "flag-manage-gallery" :
				include_once ( dirname (__FILE__) . '/functions.php' );	// admin functions
				include_once ( dirname (__FILE__) . '/manage.php' );		// flag_admin_manage_gallery
				// Initate the Manage Gallery page
				$flag->manage_page = new flagManageGallery ();
				// Render the output now, because you cannot access a object during the constructor is not finished
				$flag->manage_page->controller();
				
				break;
			case "flag-options" :
				include_once ( dirname (__FILE__) . '/settings.php' );		// flag_admin_options
				flag_admin_options();
				break;
			case "flag-skins" :
				include_once ( dirname (__FILE__) . '/skins.php' );		// flag_manage_skins
				break;
			case "flag-wpmu" :
				include_once ( dirname (__FILE__) . '/wpmu.php' );			// flag_wpmu_admin
				flag_wpmu_setup();
				break;
			default :
				include_once ( dirname (__FILE__) . '/overview.php' ); 	// flag_admin_overview
				flag_admin_overview();
				break;
		}
	}
	
	function load_scripts() {
		
		wp_register_script('flag-ajax', FLAG_URLPATH .'admin/js/flag.ajax.js', array('jquery'), '1.4.0');
		wp_localize_script('flag-ajax', 'flagAjaxSetup', array(
					'url' => admin_url('admin-ajax.php'),
					'action' => 'flag_ajax_operation',
					'operation' => '',
					'nonce' => wp_create_nonce( 'flag-ajax' ),
					'ids' => '',
					'permission' => __('You do not have the correct permission', 'flag'),
					'error' => __('Unexpected Error', 'flag'),
					'failure' => __('A failure occurred', 'flag')				
		) );
		wp_register_script('flag-progressbar', FLAG_URLPATH .'admin/js/flag.progressbar.js', array('jquery'), '1.0.0');
		wp_register_script('swfupload_f10', FLAG_URLPATH .'admin/js/swfupload.js', array('jquery'), '2.2.0');
				
		switch ($_GET['page']) {
			case FLAGFOLDER : 
				wp_enqueue_script( 'postbox' );
			case "flag-manage-gallery" :
				print "<script type='text/javascript' src='".FLAG_URLPATH."admin/js/tabs.js'></script>\n";
				wp_enqueue_script( 'multifile', FLAG_URLPATH .'admin/js/jquery.MultiFile.js', array('jquery'), '1.4.6' );
				wp_enqueue_script( 'flag-swfupload-handler', FLAG_URLPATH .'admin/js/swfupload.handler.js', array('swfupload_f10'), '2.2.0' );
				wp_enqueue_script( 'postbox' );
				wp_enqueue_script( 'flag-ajax' );
				wp_enqueue_script( 'flag-progressbar' );
				add_thickbox();
			break;
			case "flag-options" :
				print "<script type='text/javascript' src='".FLAG_URLPATH."admin/js/tabs.js'></script>\n";
			break;		
			case "flag-skins" :
				wp_enqueue_script('farbtastic-nosharp', FLAG_URLPATH.'admin/js/farbtastic-nosharp.js', array('jquery'), '1.2');
				//wp_enqueue_script( 'farbtastic' );
 				print "<script type='text/javascript' src='".FLAG_URLPATH."admin/js/tabs.js'></script>\n";
			break;		
		}
	}		
	
	function load_styles() {
		
		switch ($_GET['page']) {
			case FLAGFOLDER :
				wp_enqueue_style( 'flagadmin', FLAG_URLPATH .'admin/css/flagadmin.css', false, '2.8.1', 'screen' );
				wp_admin_css( 'css/dashboard' );
			break;
			case "flag-options" :
			case "flag-manage-gallery" :
				wp_enqueue_style( 'flagtabs', FLAG_URLPATH .'admin/css/tabs.css', false, '1.0.0', 'screen' );
				wp_enqueue_style( 'flagadmin', FLAG_URLPATH .'admin/css/flagadmin.css', false, '2.8.1', 'screen' );
				wp_enqueue_style( 'thickbox');
			break;
			case "flag-skins" :
				wp_enqueue_style( 'farbtastic' );
				wp_enqueue_style( 'flagtabs', FLAG_URLPATH .'admin/css/tabs.css', false, '1.0.0', 'screen' );
				wp_enqueue_style( 'flagadmin', FLAG_URLPATH .'admin/css/flagadmin.css', false, '2.8.1', 'screen' );
				wp_admin_css( 'css/dashboard' );
			break;
		}	
	}
	
	function show_help($help, $screen) {

		$link = '<a href="http://codeasily.com/wordpress-plugins/flag" target="_blank">CodEasily.com</a>';
		// menu title is localized...
		$i18n = strtolower  ( _n( 'Gallery', 'Galleries', 1, 'flag' ) );

		switch ($screen) {
			case 'toplevel_page_' . FLAGFOLDER :
				$link; 
			break;
			case "{$i18n}_page_flag-manage-gallery" :
			case "flag-manage-gallery":
			case "flag-manage-images":
				$link;
			break;
			case "{$i18n}_page_flag-skins" :
				$link .= ' | <a href="http://photogallerycreator.com/" target="_blank">' . __('PhotoGalleryCreator.com', 'flag') . '</a>';
			break;
			case "{$i18n}_page_flag-options" :
				$link;
			break;
		}
		
		if ( !empty($link) ) {
			$help  = '<h5>' . __('Get help with GRAND FlAGallery', 'flag') . '</h5>';
			$help .= '<div class="metabox-prefs">';
			$help .= $link;
			$help .= "</div>\n";
			$help .= '<h5>' . __('More Help & Info', 'flag') . '</h5>';
			$help .= '<div class="metabox-prefs">';
			$help .= '<a href="http://codeasily.com/wordpress-plugins/flash-album-gallery/grand-flash-album-gallery-wordpress-plugin-video-tutorial" target="_blank">' . __('GRAND FlAGallery Video Tutorial', 'flag') . '</a>';
			$help .= ' | <a href="http://codeasily.com/wordpress-plugins/flash-album-gallery/faq" target="_blank">' . __('FAQ', 'flag') . '</a>';
			$help .= ' | <a href="http://codeasily.com/wordpress-plugins/flash-album-gallery/flag-review" target="_blank">' . __('GRAND FlAGallery Review', 'flag') . '</a>';
			$help .= ' | <a href="http://codeasily.com/wordpress-plugins/flash-album-gallery/languages" target="_blank">' . __('Get your language pack', 'flag') . '</a>';
			$help .= ' | <a href="http://photogallerycreator.com/2009/07/skins-for-flash-album-gallery" target="_blank">' . __('Skins for GRAND FlAGallery', 'flag') . '</a>';
			$help .= "</div>\n";
		} 
		
		return $help;
	}
	
	function edit_screen_meta($screen) {

		// menu title is localized, so we need to change the toplevel name
		$i18n = strtolower  ( _n( 'Gallery', 'Galleries', 1, 'flag' ) );
		
		switch ($screen) {
			case "{$i18n}_page_flag-manage-gallery" :
				// we would like to have screen option only at the manage images / gallery page
				if ( isset ($_POST['sortGallery']) )
					$screen = $screen;
				else if ( ($_GET['mode'] == 'edit') || isset ($_POST['backToGallery']) )
					$screen = 'flag-manage-images';
				else if ( ($_GET['mode'] == 'sort') )
					$screen = $screen;
				else
					$screen = 'flag-manage-gallery';	
			break;
		}

		return $screen;
	}

	function register_column_headers($screen, $columns) {
		global $_wp_column_headers;
	
		if ( !isset($_wp_column_headers) )
			$_wp_column_headers = array();
	
		$_wp_column_headers[$screen] = $columns;
	}

	function register_columns() {
		include_once ( dirname (__FILE__) . '/manage-images.php' );
		
		$this->register_column_headers('flag-manage-images', flag_manage_gallery_columns() );	
	}

}

function flag_wpmu_site_admin() {
	// Check for site admin
	if ( function_exists('is_site_admin') )
		if ( is_site_admin() )
			return true;
			
	return false;
}

?>