<?php header("content-type:text/xml;charset=utf-8"); ?>
<!--<?php 
	require_once( str_replace("\\","/", dirname(__FILE__).'/settings.php') ); 
	$file_settings = str_replace("\\","/", dirname(dirname(__FILE__)).'/'.basename( dirname(__FILE__) ).'_settings.php');
	if ( file_exists( $file_settings ) ) {
		include_once( $file_settings ); 
	}
?>-->
<?php $background = $flashBacktransparent ? '' : "0x{$flashBackcolor}"; ?>
<color>
	<!--graphic elements-->
	<background color="<?=$background?>"/>
	<buttons_bg color="0x<?=$buttonsBG?>"/>
	<buttons mouseOver="0x<?=$buttonsMouseOver?>" mouseOut="0x<?=$buttonsMouseOut?>"/>
	<categoryButtons mouseOver="0x<?=$catButtonsMouseOver?>" mouseOut="0x<?=$catButtonsMouseOut?>"/>
	<categoryButtonsText mouseOver="0x<?=$catButtonsTextMouseOver?>" mouseOut="0x<?=$catButtonsTextMouseOut?>"/>
	<thumbnail mouseOver="0x<?=$thumbMouseOver?>" mouseOut="0x<?=$thumbMouseOut?>"/>
	<!--text elements-->
	<mainTitle textColor="0x<?=$mainTitle?>"/>
	<categoryTitle textColor="0x<?=$categoryTitle?>"/>
	<item_bg color="0x<?=$itemBG?>"/>
	<itemTitle textColor="0x<?=$itemTitle?>"/>
	<itemDescription textColor="0x<?=$itemDescription?>"/>
</color>
