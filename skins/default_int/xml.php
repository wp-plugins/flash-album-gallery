<?php

// look up for the path
if(file_exists(dirname(__FILE__) . "/../../flash-album-gallery/flag-config.php")) {
	require_once(dirname(__FILE__) . "/../../flash-album-gallery/flag-config.php");
} else if(file_exists(dirname(__FILE__) . "/../../../flash-album-gallery/flag-config.php")) {
	require_once(dirname(__FILE__) . "/../../../flash-album-gallery/flag-config.php");
} else if(file_exists(dirname(__FILE__) . "/../flash-album-gallery/flag-config.php")) {
	require_once(dirname(__FILE__) . "/../flash-album-gallery/flag-config.php");
} else if(file_exists(dirname(__FILE__) . "/../../flag-config.php")) {
	require_once(dirname(__FILE__) . "/../../flag-config.php");
} else if(file_exists(dirname(__FILE__) . "/../../../flag-config.php")) {
	require_once(dirname(__FILE__) . "/../../../flag-config.php");
} else if(file_exists(dirname(__FILE__) . "/../flag-config.php")) {
	require_once(dirname(__FILE__) . "/../flag-config.php");
}

global $wpdb;

$flag_options = get_option ('flag_options');
$siteurl	 = get_option ('siteurl');

// get the gallery id
$gID = explode( '_', $_GET['gid'] );

// Create XML output
header("content-type:text/xml;charset=utf-8");

echo "<gallery title='".attribute_escape(stripslashes($_GET['albumname']))."'>\n";
// get the pictures
foreach ( $gID as $galleryID ) {
	$galleryID = (int) $galleryID;
	if ( $galleryID == 0) {
		$thepictures = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid ORDER BY tt.{$flag_options['galSort']} {$flag_options['galSortDir']} ");
	} else {
		$thepictures = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE t.gid = '$galleryID' ORDER BY tt.{$flag_options['galSort']} {$flag_options['galSortDir']} ");
	}

	echo "  <category title='".attribute_escape(flagGallery::i18n(strip_tags(stripslashes($thepictures[0]->title))))."'>\n";
	echo "    <items>\n";

	if (is_array ($thepictures)){
		foreach ($thepictures as $picture) {
			echo "      <item image_icon='".$siteurl."/".$picture->path."/thumbs/thumbs_".$picture->filename."' image='".$siteurl."/".$picture->path."/".$picture->filename."' title ='".attribute_escape(flagGallery::i18n(strip_tags(stripslashes($picture->alttext))))."'><![CDATA[".htmlspecialchars_decode(attribute_escape(flagGallery::i18n(stripslashes($picture->description))))."]]></item>\n";
		}
	}
	 
	echo "    </items>\n";
	echo "  </category>\n";
}
echo "</gallery>\n";
?>