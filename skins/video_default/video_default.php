<?php
/*
vSkin Name: Default Mini VideoBlog
Skin URI:
Description:
Author: PGC
Author URI: http://PhotoGalleryCreator.com
Version: 1.0
*/

function flagShowSkin_video_default($args) {
	extract($args);
	$flag_options = get_option('flag_options');

	$skinID = 'id_'.mt_rand();
	// look up for the path
	$playlistpath = $flag_options['galleryPath'].'playlists/video/'.$playlist.'.xml';
	$data = file_get_contents($playlistpath);
	$flashBackcolor = flagGetBetween($data,'<property1>0x','</property1>');
	if(empty($width)) {
		$width = flagGetBetween($data,'<width><![CDATA[',']]></width>');
	}
	if(empty($height)) {
		$height = flagGetBetween($data,'<height><![CDATA[',']]></height>');
	}

	if(empty($wmode)) {
		$wmode = flagGetBetween($data,'<property0><![CDATA[',']]></property0>');
	}
	if(empty($flashBackcolor)) {
		$flashBackcolor = $flag_options['flashBackcolor'];
	}
	$alternate = '';
	// init the flash output
	$swfobject = new flag_swfobject( $flag_options['skinsDirURL'].$skin.'/gallery.swf' , $skinID, $width, $height, '10.1.52', FLAG_URLPATH .'skins/expressInstall.swf');
	global $swfCounter;

	$swfobject->message = '<p>'. __('The <a href="http://www.macromedia.com/go/getflashplayer">Flash Player</a> and a browser with Javascript support are needed.', 'flag').'</p>';
	$swfobject->add_params('wmode', $wmode);
	$swfobject->add_params('allowfullscreen', 'true');
	$swfobject->add_params('allowScriptAccess', 'always');
	$swfobject->add_params('saling', 'lt');
	$swfobject->add_params('scale', 'noScale');
	$swfobject->add_params('menu', 'false');
	$swfobject->add_params('bgcolor', '#'.$flashBackcolor );
	$swfobject->add_attributes('id', $skinID);
	$swfobject->add_attributes('name', $skinID);

	// adding the flash parameter
	$swfobject->add_flashvars( 'path', $flag_options['skinsDirURL'].$skin.'/' );
	$swfobject->add_flashvars( 'skinID', $skinID );
	$swfobject->add_flashvars('playlist', $playlist);
	// create the output
	$out = '<div class="grandvideo">' . $swfobject->output($alternate) . '</div>';
	// add now the script code
	$out .= "\n".'<script type="text/javascript" defer="defer">';
	$out .= $swfobject->javascript();
	$out .= "\n".'</script>';

	$out = apply_filters('flag_show_flash_v_content', $out);	
			
	return $out;	
}
remove_all_filters( 'flagShowVideoSkin' );
add_filter( 'flagShowVideoSkin', 'flagShowSkin_video_default' );
?>