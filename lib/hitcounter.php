<?php
// include the flag function
@ require_once (dirname(dirname(__FILE__)). '/flag-config.php');

if( isset($_GET['pid']) ) {
	$pictureID = $_GET['pid'];
	flag_update_hitcounter($pictureID);
}
if ( isset($_POST['pid']) ) {
	$pictureID = $_POST['pid'];
	flag_update_hitcounter($pictureID);
}

/**
 * Update image hitcounter in the database
 * 
 * @param int $pid   id of the image
 * @param string | int $galleryid
 */
function flag_update_hitcounter($pid, $sethits = false) {
	global $wpdb;

	if( $sethits === FALSE ) 
		$sethits = "`hitcounter`+1";
	
	if ( $pid )
		$result = $wpdb->query( "UPDATE $wpdb->flagpictures SET `hitcounter` = $sethits WHERE pid = $pid" );

}
?>