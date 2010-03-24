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
function flagShowFlashAlbum($galleryID, $name, $width='', $height='', $skin='') {
	
	require_once ( dirname(__FILE__) . '/class.swfobject.php' );

	$flag_options = get_option('flag_options');

	if (empty($width) ) $width  = $flag_options['flashWidth'];
	if (empty($height)) $height = (int) $flag_options['flashHeight'];
	if($name == '') $name = 'Gallery';
	if($skin == '') $skin = $flag_options['flashSkin'];
	if($flag_options['flashBacktransparent'] == 'transparent') {
		$wmode = 'transparent';
	} else {
		$wmode = 'window';
	}
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	if(!is_dir($skinpath)) $skin = 'default';
	// init the flash output
	$swfobject = new swfobject( $flag_options['skinsDirURL'].$skin.'/gallery.swf' , 'so' . $galleryID, $width, $height, '10.0.0', FLAG_URLPATH .'skins/expressInstall.swf');
	global $swfCounter;

	$swfobject->message = '<p>'. __('The <a href="http://www.macromedia.com/go/getflashplayer">Flash Player</a> and a browser with Javascript support are needed.', 'flag').'</p>';
	$swfobject->add_params('wmode', $wmode);
	$swfobject->add_params('allowfullscreen', 'true');
	$swfobject->add_params('menu', 'false');
	$swfobject->add_params('bgcolor', $flag_options['flashBackcolor'] );
	$swfobject->add_attributes('styleclass', 'flashalbum');
	$swfobject->add_attributes('id', 'so' . $galleryID . '_f' . $swfCounter);
	$swfobject->add_attributes('name', 'so' . $galleryID . '_f' . $swfCounter);

	// adding the flash parameter	
	$swfobject->add_flashvars( 'path', $flag_options['skinsDirURL'].$skin.'/' );
	$swfobject->add_flashvars( 'gID', $galleryID );
	$swfobject->add_flashvars( 'galName', $name );
	$swfobject->add_flashvars( 'width', $width );
	$swfobject->add_flashvars( 'height', $height );	
	// add now the script code
   $out = "\n".'<script type="text/javascript" defer="defer">';
	$out .= $swfobject->javascript();
	$out .= "\n".'</script>';
	// create the output
	$out .= '<div class="flashalbum">' . $swfobject->output() . '</div>';

	$out = apply_filters('flag_show_flash_content', $out);
			
	return $out;	
}

?>