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

$flag_options = get_option('flag_options');

// Create XML output
header("content-type:text/xml;charset=utf-8");
$background = $flag_options['flashBacktransparent'] ? '' : str_replace('#','0x',$flag_options['flashBackcolor']);
$buttonsBG = str_replace('#','0x',$flag_options['buttonsBG']);
$buttonsOver = str_replace('#','0x',$flag_options['buttonsMouseOver']);
$buttonsOut = str_replace('#','0x',$flag_options['buttonsMouseOut']);
$catButtonsOver = str_replace('#','0x',$flag_options['catButtonsMouseOver']);
$catButtonsOut = str_replace('#','0x',$flag_options['catButtonsMouseOut']);
$catButtonsTextOver = str_replace('#','0x',$flag_options['catButtonsTextMouseOver']);
$catButtonsTextOut = str_replace('#','0x',$flag_options['catButtonsTextMouseOut']);
$thumbOver = str_replace('#','0x',$flag_options['thumbMouseOver']);
$thumbOut = str_replace('#','0x',$flag_options['thumbMouseOut']);
$mainTitle = str_replace('#','0x',$flag_options['mainTitle']);
$categoryTitle = str_replace('#','0x',$flag_options['categoryTitle']);
$itemBG = str_replace('#','0x',$flag_options['itemBG']);
$itemTitle = str_replace('#','0x',$flag_options['itemTitle']);
$itemDescription = str_replace('#','0x',$flag_options['itemDescription']);
?>
<color>
	<!--graphic elements-->
	<background color="<?php echo $background; ?>"/>
	<buttons_bg color="<?php echo $buttonsBG; ?>"/>
	<buttons mouseOver="<?php echo $buttonsOver; ?>" mouseOut="<?php echo $buttonsOut; ?>"/>
	<categoryButtons mouseOver="<?php echo $catButtonsOver; ?>" mouseOut="<?php echo $catButtonsOut; ?>"/>
	<categoryButtonsText mouseOver="<?php echo $catButtonsTextOver; ?>" mouseOut="<?php echo $catButtonsTextOut; ?>"/>
	<thumbnail mouseOver="<?php echo $thumbOver; ?>" mouseOut="<?php echo $thumbOut; ?>"/>
	<!--text elements-->
	<mainTitle textColor="<?php echo $mainTitle; ?>"/>
	<categoryTitle textColor="<?php echo $categoryTitle; ?>"/>
	<item_bg color="<?php echo $itemBG; ?>"/>
	<itemTitle textColor="<?php echo $itemTitle; ?>"/>
	<itemDescription textColor="<?php echo $itemDescription; ?>"/>
</color>
