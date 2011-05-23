<?php
/**
 * Return a script for the flash slideshow. Can be used in any tmeplate with <?php echo flagShowFlashAlbum($galleryID, $name, $width, $height, $skin) ? >
 * Require the script swfobject.js in the header or footer
 * 
 * @access public 
 * @param integer $galleryID ID of the gallery
 * @param integer $flashWidth Width of the flash container
 * @param integer $flashHeight Height of the flash container
 * @return the content
 */
function flagShowFlashAlbum($galleryID, $name, $width='', $height='', $skin='', $playlist='', $wmode='') {
	
	require_once ( dirname(__FILE__) . '/class.swfobject.php' );

	$flag_options = get_option('flag_options');

	if (empty($width) ) $width  = $flag_options['flashWidth'];
	if (empty($height)) $height = (int) $flag_options['flashHeight'];
	if($name == '') $name = '';
	if($skin == '') $skin = $flag_options['flashSkin'];
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	if(!is_dir($skinpath)) {
		$skin = 'default';
		$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	} 
	include_once ( $skinpath.'/'.$skin.'.php' );
	if(function_exists('flagShowSkin')) {
		$out = flagShowSkin($galleryID, $name, $width, $height, $skin, $playlist, $wmode);
	} else {
		$flashBacktransparent = '';
		$flashBackcolor = '';
		// look up for the path
		if(file_exists($skinpath . "_settings.php")) {
			include_once( $skinpath . "_settings.php");
		} else if(file_exists($skinpath . "/settings.php")) {
			include_once( $skinpath . "/settings.php");
		}

		if(empty($wmode)) {
			$wmode = $flashBacktransparent? 'transparent' : 'window';
		}
		if(empty($flashBackcolor)) {
			$flashBackcolor = $flag_options['flashBackcolor'];
		}
		// init the flash output
		$swfobject = new flag_swfobject( $flag_options['skinsDirURL'].$skin.'/gallery.swf' , 'so' . $galleryID, $width, $height, '10.0.0', FLAG_URLPATH .'skins/expressInstall.swf');
		global $swfCounter;

		$swfobject->message = '<p>'. __('The <a href="http://www.macromedia.com/go/getflashplayer">Flash Player</a> and a browser with Javascript support are needed.', 'flag').'</p>';
		$swfobject->add_params('wmode', $wmode);
		$swfobject->add_params('allowfullscreen', 'true');
		$swfobject->add_params('allowScriptAccess', 'always');
		$swfobject->add_params('saling', 'lt');
		$swfobject->add_params('scale', 'noScale');
		$swfobject->add_params('menu', 'false');
		$swfobject->add_params('bgcolor', '#'.$flashBackcolor );
		$swfobject->add_attributes('styleclass', 'flashalbum');
		$swfobject->add_attributes('id', 'so' . $galleryID . '_f' . $swfCounter);
		$swfobject->add_attributes('name', 'so' . $galleryID . '_f' . $swfCounter);

		// adding the flash parameter	
		$swfobject->add_flashvars( 'path', $flag_options['skinsDirURL'].$skin.'/' );
		$swfobject->add_flashvars( 'gID', $galleryID );
		$swfobject->add_flashvars( 'galName', $name );
		$swfobject->add_flashvars( 'skinID', 'so' . $galleryID . '_f' . $swfCounter );
		$swfobject->add_flashvars( 'width', $width );
		$swfobject->add_flashvars( 'height', $height );	
		// create the output
		$out = '<div class="flashalbum">' . $swfobject->output() . '</div>';
		// add now the script code
		$out .= "\n".'<script type="text/javascript" defer="defer">';
		$out .= "\nvar swfdiv=document.getElementById('so".$galleryID."_div');swfdiv.style.display='none';setTimeout(function(){swfdiv.style.display='block';},3000);";
		$out .= $swfobject->javascript();
		$out .= "\n".'</script>';

		$out = apply_filters('flag_show_flash_content', $out);
	}
				
		return $out;	
}

function flagShowMPlayer($playlist, $width, $height) {
	
	require_once ( dirname(__FILE__) . '/class.swfobject.php' );
    require_once ( dirname(dirname(__FILE__)) . '/admin/playlist.functions.php');

	$flag_options = get_option('flag_options');
	$playlistPath = $flag_options['galleryPath'].'playlists/'.$playlist.'.xml';
	$playlist_data = get_playlist_data(ABSPATH.$playlistPath);
	$skin = $playlist_data['skin'];
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	include_once ( $skinpath.'/'.$skin.'.php' );
	$out = flagShowMusicSkin(array('playlist'=>$playlist, 'skin'=>$skin, 'width'=>$width, 'height'=>$height));
	return $out;	
}

function flagShowMusicSkin($args) {
	return apply_filters( 'flagShowMusicSkin', $args );
}

function flagGetBetween($content,$start,$end){
    $r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}
?>