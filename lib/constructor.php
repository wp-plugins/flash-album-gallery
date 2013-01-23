<?php
if ( !defined('WP_LOAD_PATH') ) {
	/** classic root path if wp-content and plugins is below wp-config.php */
	preg_match('|^(.*?/)(wp-content)/|i', str_replace('\\', '/', __FILE__), $_m);
	$classic_root = $_m[1];
	if (file_exists( $classic_root . 'wp-load.php') )
		define( 'WP_LOAD_PATH', $classic_root);
	else
		exit("Could not find wp-load.php");
}

// let's load WordPress
require_once( WP_LOAD_PATH . 'wp-load.php');

if ( !is_user_logged_in() )
	die('-1');

// check for correct FlAG capability
if ( !current_user_can('FlAG Change skin') )
	die('-1');

$flashPost = file_get_contents("php://input");
// parse properties_skin
$arr = array();
parse_str($flashPost, $arr);
$settingsXML =  str_replace("\\","/", dirname(dirname(dirname(__FILE__))).'/flagallery-skins/'.$arr['skin_name'].'/settings/settings.xml');
if(isset($arr['properties_skin']) && !empty($arr['properties_skin'])) {
	$fp = fopen($settingsXML, "r");
	if(!$fp) {
		exit( "2");//Failure - not read;
	}
	$mainXML = '';
	while(!feof($fp)) {
		$mainXML .= fgetc($fp);
	}
	$fp = fopen($settingsXML, "w");
	if(!$fp)
		exit("0");//Failure
	$arr['properties_skin'] = str_replace( array( '=','?','"','$' ), '', $arr['properties_skin'] );
	$newProperties = preg_replace("|<properties>.*?</properties>|si", $arr['properties_skin'], $mainXML);
	if(fwrite($fp, $newProperties))
		echo "1";//Save
	else
		echo "0";
	fclose($fp);
} else {
	echo '0';
}

?>