<?php
/*
Plugin Name: GRAND Flash Album Gallery
Plugin URI: http://codeasily.com/wordpress-plugins/flash-album-gallery/flag/
Description: The GRAND FlAGallery plugin - provides a comprehensive interface for managing photos and images through a set of admin pages, and it displays photos in a way that makes your web site look very professional.
Version: 0.39
Author: Sergey Pasyuk
Author URI: http://codeasily.com/

-------------------

		Copyright 2009  Sergey Pasyuk  (email : pasyuk@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

//ini_set('display_errors', '1');
//ini_set('error_reporting', E_ALL);


if (!class_exists('flagLoad')) {
class flagLoad {
	
	var $version     = '0.39';
	var $dbversion   = '0.32';
	var $minium_WP   = '2.7';
	var $options     = '';
	var $manage_page;
	
	function flagLoad() {

		// Load the language file
		load_plugin_textdomain('flag', 'wp-content/plugins/' . dirname( plugin_basename(__FILE__) ) . '/lang', dirname( plugin_basename(__FILE__) ) . '/lang');
		
		// Stop the plugin if we missed the requirements
		if ( !$this->required_version() )
			return;
			
		// Get some constants first
		$this->load_options();
		$this->define_constant();
		$this->define_tables();
		$this->load_dependencies();
		
		// Init options & tables during activation & deregister init option
		register_activation_hook( dirname(__FILE__) . '/flag.php', array(&$this, 'activate') );
		register_deactivation_hook( dirname(__FILE__) . '/flag.php', array(&$this, 'deactivate') );	

		// Register a uninstall hook to atumatic remove all tables & option
		if ( function_exists('register_uninstall_hook') )
			register_uninstall_hook( dirname(__FILE__) . '/flag.php', array('flagLoad', 'uninstall') );

		// Start this plugin once all other plugins are fully loaded
		add_action( 'plugins_loaded', array(&$this, 'start_plugin') );
		
	}
	
	function start_plugin() {

		// Content Filters
		add_filter('flag_gallery_name', 'sanitize_title');

		// Load the admin panel or the frontend functions
		if ( is_admin() ) {	
			
		add_action( 'after_plugin_row', array(&$this, 'flag_check_message_version') );
			// Pass the init check or show a message
			if (get_option( "flag_init_check" ) != false )
				add_action( 'admin_notices', create_function('', 'echo \'<div id="message" class="error"><p><strong>' . get_option( "flag_init_check" ) . '</strong></p></div>\';') );
				
		} else {			
			
			// Add MRSS to wp_head
			if ( $this->options['useMediaRSS'] )
				add_action('wp_head', array('flagMediaRss', 'add_mrss_alternate_link'));
			
			// Add the script and style files
			add_action('wp_print_scripts', array(&$this, 'load_scripts') );
			add_action('wp_print_styles', array(&$this, 'load_styles') );

		}	
	}
	
	function required_version() {
		
		global $wp_version;
		
 		// Check for WP version installation
		$wp_ok  =  version_compare($wp_version, $this->minium_WP, '>=');
		
		if ($wp_ok == FALSE) {
			add_action('admin_notices', create_function('', 'global $flag; printf (\'<div id="message" class="error"><p><strong>\' . __(\'Sorry, Flash Album Gallery works only under WordPress %s or higher\', "flag" ) . \'</strong></p></div>\', $flag->minium_WP );'));
			return false;
		}
		return true;
		
	}
	
	function define_tables() {		
		global $wpdb;
		
		// add database pointer 
		$wpdb->flagpictures					= $wpdb->prefix . 'flag_pictures';
		$wpdb->flaggallery					= $wpdb->prefix . 'flag_gallery';
		
	}
	
	function define_constant() {
		
		define('FLAGVERSION', $this->version);
		// Minimum required database version
		define('FLAG_DBVERSION', $this->dbversion);

		// required for Windows & XAMPP
		define('WINABSPATH', str_replace("\\", "/", ABSPATH) );
			
		// define URL
		define('FLAGFOLDER', plugin_basename( dirname(__FILE__)) );
		
		define('FLAG_ABSPATH', str_replace("\\","/", WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ) . '/' ));
		define('FLAG_URLPATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
		
		// get value for safe mode
		if ( (gettype( ini_get('safe_mode') ) == 'string') ) {
			// if sever did in in a other way
			if ( ini_get('safe_mode') == 'off' ) define('SAFE_MODE', FALSE);
			else define( 'SAFE_MODE', ini_get('safe_mode') );
		} else
		define( 'SAFE_MODE', ini_get('safe_mode') );
		
	}
	
	function load_dependencies() {
		global $flagdb;
	
		// Load global libraries												
		require_once (dirname (__FILE__) . '/lib/core.php'); 
		require_once (dirname (__FILE__) . '/lib/flag-db.php');
		require_once (dirname (__FILE__) . '/lib/image.php');

		// We didn't need all stuff during a AJAX operation
		if ( defined('DOING_AJAX') )
			require_once (dirname (__FILE__) . '/admin/ajax.php');
		else {
			require_once (dirname (__FILE__) . '/lib/meta.php');
			require_once (dirname (__FILE__) . '/lib/media-rss.php');
			include_once (dirname (__FILE__) . '/admin/tinymce/tinymce.php');

			// Load backend libraries
			if ( is_admin() ) {	
				require_once (dirname (__FILE__) . '/admin/admin.php');
				require_once (dirname (__FILE__) . '/admin/media-upload.php');
				$this->flagAdminPanel = new flagAdminPanel();
				
			// Load frontend libraries							
			} else {
				require_once (dirname (__FILE__) . '/lib/swfobject.php');
				require_once (dirname (__FILE__) . '/lib/shortcodes.php');
			}	
		}
	}
	
	function load_scripts() {

		echo "<meta name='GRAND FlAGallery' content='" . $this->version . "' />\n";
		
		wp_enqueue_script('swfobject', FLAG_URLPATH .'admin/js/swfobject.js', FALSE, '2.1');

	}
	
	function load_styles() {
		
//		wp_enqueue_style('FlAG', FLAG_URLPATH.'css/'.$this->options['FlCSSfile'], false, '1.0.0', 'screen'); 
		
	}
	
	function load_options() {
		// Load the options
		$this->options = get_option('flag_options');
	}

	function activate() {
		include_once (dirname (__FILE__) . '/admin/flag_install.php');
		// check for tables
		flag_install();
		
	}
	
	function deactivate() {
		// remove & reset the init check option
		delete_option( 'flag_init_check' );
	}

	function uninstall() {
  	include_once (dirname (__FILE__) . '/admin/flag_install.php');
    flag_uninstall();
	}

	
	### PLUGIN MESSAGE ON PLUGINS PAGE
	function flag_check_message_version($file)
	{
		static $this_plugin;
		global $wp_version;
		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

		if ($file == $this_plugin ){
			$checkfile = "http://codeasily.com/flagallery.chk";

			$icheck = wp_remote_fopen($checkfile);

			if($icheck)
			{
				$icheck = explode('@', $icheck);
				$RemoteInfo = $icheck[1];
				$theMessage = $icheck[3];
				
				$columns = substr($wp_version, 0, 3) == "2.8" ? 3 : 5;

				if( strval($RemoteInfo) == 1 )
				{
					echo '<td colspan="'.$columns.'" class="plugin-update" style="line-height:1.2em; font-size:11px; padding:1px;"><div id="flag-update-msg" style="padding-bottom:1px;" >'.$theMessage.'</div></td>';
				} else {
					return;
				}
			}
		}
	}

}
	// Let's start the holy plugin
	global $flag;
	$flag = new flagLoad();

}


?>
