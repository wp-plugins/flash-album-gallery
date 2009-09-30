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
		
		// Add the script and style files
		add_action('admin_print_scripts', array(&$this, 'load_scripts') );
		add_action('admin_print_styles', array(&$this, 'load_styles') );
		
	}

	// integrate the menu	
	function add_menu()  {
		
		add_menu_page( __('Flash Album Gallery overview','flag'), __('FlAGallery'), 'FlAG overview', FLAGFOLDER, array (&$this, 'show_menu'), FLAG_URLPATH .'admin/images/flag.png' );
	    add_submenu_page( FLAGFOLDER , __('Flash Album Gallery overview', 'flag'), __('Overview', 'flag'), 'FlAG overview', FLAGFOLDER, array (&$this, 'show_menu'));
	    add_submenu_page( FLAGFOLDER , __('FlAG Manage gallery', 'flag'), __('Manage Galleries', 'flag'), 'FlAG Manage gallery', 'flag-manage-gallery', array (&$this, 'show_menu'));
	    add_submenu_page( FLAGFOLDER , __('FlAG Manage skins', 'flag'), __('Skins', 'flag'), 'FlAG Change skin', 'flag-skins', array (&$this, 'show_menu'));
	    add_submenu_page( FLAGFOLDER , __('FlAG Change options', 'flag'), __('Options', 'flag'), 'FlAG Change options', 'flag-options', array (&$this, 'show_menu'));

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
			default :
				include_once ( dirname (__FILE__) . '/overview.php' ); 	// flag_admin_overview
				flag_admin_overview();
				break;
		}
	}
	
	function load_scripts() {
		
		wp_register_script('flag-ajax', FLAG_URLPATH .'admin/js/flag.ajax.js', array('jquery'), '1.0.0');
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
				wp_enqueue_script( 'multifile', FLAG_URLPATH .'admin/js/jquery.MultiFile.js', array('jquery'), '1.1.1' );
				wp_enqueue_script( 'flag-swfupload-handler', FLAG_URLPATH .'admin/js/swfupload.handler.js', array('swfupload_f10'), '1.0.0' );
				wp_enqueue_script( 'postbox' );
				wp_enqueue_script( 'flag-ajax' );
				wp_enqueue_script( 'flag-progressbar' );
				add_thickbox();
			break;
			case "flag-options" :
				wp_enqueue_script( 'farbtastic' );
				print "<script type='text/javascript' src='".FLAG_URLPATH."admin/js/tabs.js'></script>\n";
			break;		
			case "flag-skins" :
 				print "<script type='text/javascript' src='".FLAG_URLPATH."admin/js/tabs.js'></script>\n";
			break;		
		}
	}		
	
	function load_styles() {
		
		switch ($_GET['page']) {
			case FLAGFOLDER :
				wp_enqueue_style( 'flagadmin', FLAG_URLPATH .'admin/css/flagadmin.css', false, '1.0.0', 'screen' );
				wp_admin_css( 'css/dashboard' );
			break;
			case "flag-options" :
				wp_enqueue_style( 'farbtastic' );
			case "flag-manage-gallery" :
				wp_enqueue_style( 'flagtabs', FLAG_URLPATH .'admin/css/tabs.css', false, '1.0.0', 'screen' );
				wp_enqueue_style( 'flagadmin', FLAG_URLPATH .'admin/css/flagadmin.css', false, '1.1.0', 'screen' );
				wp_enqueue_style( 'thickbox');
			break;
			case "flag-skins" :
				wp_enqueue_style( 'flagtabs', FLAG_URLPATH .'admin/css/tabs.css', false, '1.0.0', 'screen' );
				wp_enqueue_style( 'flagadmin', FLAG_URLPATH .'admin/css/flagadmin.css', false, '1.0.0', 'screen' );
				wp_admin_css( 'css/dashboard' );
			break;
		}	
	}
	
}

?>