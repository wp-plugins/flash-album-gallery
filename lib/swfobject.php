<?php
/**
 * Return a script for the flash slideshow. Can be used in any tmeplate with <?php echo flagShowFlashAlbum($galleryID, $name, $width, $height, $skin) ? >
 * Require the script swfobject.js in the header or footer
 *
 * @access public
 * @param integer $galleryID ID of the gallery
 * @param string $name
 * @param string $width
 * @param string $height
 * @param string $skin
 * @param string $playlist
 * @param string $wmode
 * @param string $linkto
 * @param bool $fullwindow
 * @param string $align
 * @return string the content
 */
function flagShowFlashAlbum($galleryID, $name='', $width='', $height='', $skin='', $playlist='', $wmode='', $linkto='', $fullwindow=false, $align='') {
 	global $post;
	require_once ( dirname(__FILE__) . '/class.swfobject.php' );

	if($linkto) {
		$post = get_post($linkto);
	}
	$flag_options = get_option('flag_options');
	$skinID = 'sid_'.mt_rand();
	if($skin == '') $skin = $flag_options['flashSkin'];
	$skinpath = ABSPATH.'wp-content/plugins/flagallery-skins/'.$skin;
	if(!is_dir($skinpath)) {
		$skin = 'minima_jn';
		$skinpath = ABSPATH.'wp-content/plugins/flagallery-skins/'.$skin;
	} 
	$swfmousewheel = '';
	$flashBacktransparent = '';
	$flashBackcolor = '';
	if (empty($width) ) $width  = $flag_options['flashWidth'];
	if (empty($height)) $height = (int) $flag_options['flashHeight'];
	$data = '';
	if(file_exists($skinpath . "/settings/settings.xml")) {
		if ($settings_xml = @simplexml_load_file($skinpath . "/settings/settings.xml", 'SimpleXMLElement', LIBXML_NOCDATA)){
			$data = $settings_xml->properties;
			$data->plug = plugins_url() . '/' . FLAGFOLDER . '/lib/';
			$data->siteurl = site_url();
			$data->key = $flag_options['license_key'];
			if(empty($wmode))
				$wmode = (string) $settings_xml->properties->property0;
			$flashBackcolor = (string) $settings_xml->properties->property1;
			$flashBackcolor = str_replace('0x','',$flashBackcolor);
			$swfmousewheel = (string) $settings_xml->properties->swfmousewheel;
		}
	} else if(file_exists($skinpath . "_settings.php")) {
		include( $skinpath . "_settings.php");
	} else if(file_exists($skinpath . "/settings.php")) {
		include( $skinpath . "/settings.php");
	}
	if(empty($wmode)) $wmode = $flashBacktransparent? 'transparent' : 'opaque';
	if(empty($flashBackcolor)) $flashBackcolor = $flag_options['flashBackcolor'];
	$isCrawler = flagGetUserNow($_SERVER['HTTP_USER_AGENT']);

	$altColors['wmode'] = $wmode;
	$altColors['Background'] = $flashBackcolor;
	$altColors['BarsBG'] = $flag_options['BarsBG'];
	$altColors['CatBGColor'] = $flag_options['CatBGColor'];
	$altColors['CatBGColorOver'] = $flag_options['CatBGColorOver'];
	$altColors['CatColor'] = $flag_options['CatColor'];
	$altColors['CatColorOver'] = $flag_options['CatColorOver'];
	$altColors['ThumbBG'] = $flag_options['ThumbBG'];
	$altColors['ThumbLoaderColor'] = $flag_options['ThumbLoaderColor'];
	$altColors['TitleColor'] = $flag_options['TitleColor'];
	$altColors['DescrColor'] = $flag_options['DescrColor'];
	$altColors['FullWindow'] = $fullwindow;

	/** @var $xml array */
	include(FLAG_ABSPATH."admin/jgallery.php");
	if($flag_options['jAlterGal']) {
		$alternate = $xml['alt'];
	} else {
		$alternate = '';
	}
	$width = strpos($width, '%')? $width : $width.'px';
	if(!$isCrawler)
		$height = strpos($height, '%')? $height : $height.'px';
	else
		$height = 'auto';
	// init the flash output
	$swfobject = new flag_swfobject( plugins_url('flagallery-skins/'.$skin.'/gallery.swf') , $skinID, '100%', '100%', '11.0.0', FLAG_URLPATH .'skins/expressInstall.swf');

	$swfobject->add_params('wmode', $wmode);
	$swfobject->add_params('allowfullscreen', 'true');
	$swfobject->add_params('allowScriptAccess', 'always');
	$swfobject->add_params('saling', 'lt');
	$swfobject->add_params('scale', 'noScale');
	$swfobject->add_params('menu', 'false');
	$swfobject->add_params('bgcolor', '#'.$flashBackcolor );
	$swfobject->add_attributes('styleclass', 'flashalbum');
	$swfobject->add_attributes('id', $skinID);

	// adding the flash parameter
	$swfobject->add_flashvars( 'path', plugins_url('flagallery-skins/'.$skin.'/') );
	$swfobject->add_flashvars( 'gID', $galleryID );
	$swfobject->add_flashvars( 'galName', $name );
	$swfobject->add_flashvars( 'skinID', $skinID );
	$swfobject->add_flashvars( 'postID', $post->ID);
	$swfobject->add_flashvars( 'postTitle', urlencode($post->post_title." "));
	$swfobject->add_flashvars( 'json', 'json_xml_'.$skinID);
	if($fullwindow){
		$flag_custom = get_post_custom($post->ID);
		$backlink = $flag_custom["mb_button_link"][0];
		if(!$backlink || $backlink == 'http://'){ $backlink = $_SERVER["HTTP_REFERER"]; }
		if($backlink){
			$swfobject->add_flashvars( 'butText', urlencode($flag_custom["mb_button"][0]));
			$swfobject->add_flashvars( 'butLink', $backlink);
		}
	}
	// create the output
	if($width != '100%' && in_array($align, array('left', 'center', 'right'))){
		$margin = '';
		switch($align){
			case 'left':
				$margin = 'margin-right: auto;';
			break;
			case 'center':
				$margin = 'margin:0 auto;';
			break;
			case 'right':
				$margin = 'margin-left: auto;';
			break;
		}
		$out = '<div class="flashalbumwraper" style="text-align:'.$align.';"><div class="flashalbum" style="width:'.$width.';height:'.$height.';'.$margin.'">' . $swfobject->output($alternate) . '</div></div>';
	} else {
		$out = '<div class="flashalbum" style="width:'.$width.';height:'.$height.';">' . $swfobject->output($alternate) . '</div>';
	}
	// add now the script code
	if(!flagGetUserNow($_SERVER['HTTP_USER_AGENT']) && !preg_match("/Android/i", $_SERVER['HTTP_USER_AGENT'])){
		$out .= "\n".'<script type="text/javascript" defer="defer">';
		$out .= "\n".'function json_xml_'.$skinID.'(e){ return '.$xml['json'].'; }';
		$out .= "\n".'flag_alt[\''.$skinID.'\'] = jQuery("div#'.$skinID.'_jq").clone().wrap(document.createElement(\'div\')).parent().html();';
		$out .= $swfobject->javascript();
		$out .= "\n".'</script>';
	}

	$out = apply_filters('flag_show_flash_content', $out);
			
	return $out;	
}

function flagShowMPlayer($playlist, $width, $height, $wmode='', $skin='', $isWidget=false) {
	
	require_once ( dirname(__FILE__) . '/class.swfobject.php' );
    require_once ( dirname(dirname(__FILE__)) . '/admin/playlist.functions.php');

	$flag_options = get_option('flag_options');
	$playlistPath = 'wp-content/flagallery/playlists/'.$playlist.'.xml';
	$playlist_data = get_playlist_data(ABSPATH.$playlistPath);
	if(!$skin){
		$skin = $playlist_data['skin'];
	}
	$skinpath = ABSPATH.'wp-content/plugins/flagallery-skins/'.$skin;
	include_once ( $skinpath.'/'.$skin.'.php' );
	$isCrawler = flagGetUserNow($_SERVER['HTTP_USER_AGENT']);
	$args = array(
		'playlist' 	=> $playlist, 
		'skin' 		=> $skin, 
		'width' 	=> $width, 
		'height' 	=> $height,
		'wmode' 	=> $wmode,
		'crawler' 	=> $isCrawler,
		'isWidget'	=> $isWidget
	);
	$out = apply_filters( 'flagShowMusicSkin', $args );
	return $out;
}

function flagShowVPlayer($playlist, $width, $height, $wmode='') {
	
	require_once ( dirname(__FILE__) . '/class.swfobject.php' );
    require_once ( dirname(dirname(__FILE__)) . '/admin/video.functions.php');

	$flag_options = get_option('flag_options');
	$playlistPath = 'wp-content/flagallery/playlists/video/'.$playlist.'.xml';
	$playlist_data = get_v_playlist_data(ABSPATH.$playlistPath);
	$skin = $playlist_data['skin'];
	$skinpath = ABSPATH.'wp-content/plugins/flagallery-skins/'.$skin;
	include_once ( $skinpath.'/'.$skin.'.php' );
	if(isset($flag_options['license_key'])){
		$lkey = $flag_options['license_key'];
	} else {
		$lkey = '';
	}
	$isCrawler = flagGetUserNow($_SERVER['HTTP_USER_AGENT']);
	$args = array(
		'playlist'	=> $playlist, 
		'skin' 		=> $skin, 
		'width' 	=> $width, 
		'height' 	=> $height,
		'wmode' 	=> $wmode,
		'lkey' 		=> $lkey,
		'crawler' 	=> $isCrawler
	);
	$out = apply_filters( 'flagShowVideoSkin', $args );
	return $out;
}

function flagShowVmPlayer($id, $w, $h, $autoplay) {

	require_once ( dirname(__FILE__) . '/class.swfobject.php' );
	$flag_options = get_option('flag_options');
	$vID = 'vid_'.mt_rand();
	if (empty($w)) $w = $flag_options['vWidth'];
	if (empty($h)) $h = $flag_options['vHeight'];
	if (empty($autoplay)) $autoplay = $flag_options['vAutoplay'];

	// init the flash output
	$swfobject = new flag_swfobject( FLAG_URLPATH.'lib/video_mini.swf' , $vID, $w, $h, '10.1.52', FLAG_URLPATH .'skins/expressInstall.swf');

	$videoObject = get_post($id);
	$url = wp_get_attachment_url($videoObject->ID);
	$thumb = get_post_meta($videoObject->ID, 'thumbnail', true);
	$aimg = $thumb? '<img src="'.$thumb.'" style="float:left;margin-right:10px;width:150px;height:auto;" alt="" />' : '';
	$atitle = $videoObject->post_title? '<strong>'.$videoObject->post_title.'</strong>' : '';
	$acontent = $videoObject->post_content? '<div style="padding:4px 0;">'.$videoObject->post_content.'</div>' : '';
	$alternative = '<div id="video_'.$videoObject->ID.'" style="overflow:hidden;padding:7px 0;">'.$aimg.$atitle.$acontent.'<div style="font-size:80%;">This browser does not support flash! You can <a href="'.$url.'">download the video</a> instead.</div></div>';

	$swfobject->add_params('wmode', 'transparent');
	$swfobject->add_params('allowfullscreen', 'true');
	$swfobject->add_params('allowScriptAccess', 'always');
	$swfobject->add_params('saling', 'lt');
	$swfobject->add_params('scale', 'noScale');
	$swfobject->add_params('menu', 'false');
	$swfobject->add_params('bgcolor', '#'.$flag_options['videoBG']);
	$swfobject->add_attributes('styleclass', 'grandflv');
	$swfobject->add_attributes('id', $vID);

	// adding the flash parameter	
	$swfobject->add_flashvars( 'path', FLAG_URLPATH.'lib/' );
	$swfobject->add_flashvars( 'vID', $id );
	$swfobject->add_flashvars( 'flashID', $vID );
	$swfobject->add_flashvars( 'autoplay', $autoplay );
	// create the output
	$out = '<div class="grandflv">' . $swfobject->output($alternative) . '</div>';
	if(!flagGetUserNow($_SERVER['HTTP_USER_AGENT'])){
		// add now the script code
		$out .= "\n".'<script type="text/javascript" defer="defer">';
		$out .= $swfobject->javascript();
		$out .= "\n".'</script>';
	}

	$out = apply_filters('flag_flv_mini', $out);
			
	return $out;
}

function flagShowBanner($xml, $width, $height, $wmode='') {

	require_once ( dirname(__FILE__) . '/class.swfobject.php' );
    require_once ( dirname(dirname(__FILE__)) . '/admin/banner.functions.php');

	$flag_options = get_option('flag_options');
	$playlistPath = 'wp-content/flagallery/playlists/banner/'.$xml.'.xml';
	$playlist_data = get_b_playlist_data(ABSPATH.$playlistPath);
	$skin = $playlist_data['skin'];
	$items = $playlist_data['items'];
	$skinpath = ABSPATH.'wp-content/plugins/flagallery-skins/'.$skin;
	include_once ( $skinpath.'/'.$skin.'.php' );
	if(isset($flag_options['license_key'])){
		$lkey = $flag_options['license_key'];
	} else {
		$lkey = '';
	}
	$isCrawler = flagGetUserNow($_SERVER['HTTP_USER_AGENT']);
	$args = array(
		'xml'			=> $xml,
		'skin' 		=> $skin,
		'items' 	=> $items,
		'width' 	=> $width,
		'height' 	=> $height,
		'wmode' 	=> $wmode,
		'lkey' 		=> $lkey,
		'crawler' => $isCrawler
	);
	$out = apply_filters( 'flagShowBannerSkin', $args );
	return $out;
}

function flagShowWidgetBanner($xml, $width, $height, $skin) {

	require_once ( dirname(__FILE__) . '/class.swfobject.php' );
    require_once ( dirname(dirname(__FILE__)) . '/admin/banner.functions.php');

	$flag_options = get_option('flag_options');
	$playlistPath = 'wp-content/flagallery/playlists/banner/'.$xml.'.xml';
	$playlist_data = get_b_playlist_data(ABSPATH.$playlistPath);
	if(!$skin) {
		$skin = $playlist_data['skin'];
	}
	$items = $playlist_data['items'];
	$skinpath = ABSPATH.'wp-content/plugins/flagallery-skins/'.$skin;
	include_once ( $skinpath.'/'.$skin.'.php' );
	if(isset($flag_options['license_key'])){
		$lkey = $flag_options['license_key'];
	} else {
		$lkey = '';
	}
	$args = array(
		'xml'		=> $xml,
		'skin' 		=> $skin,
		'lkey' 		=> $lkey,
		'items' 	=> $items,
		'width' 	=> $width,
		'height' 	=> $height
	);
	$out = apply_filters( 'flagShowWidgetBannerSkin', $args );
	return $out;
}

function flagGetBetween($content,$start,$end){
    $r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}

function flagGetUserNow($userAgent) {
    $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|FeedBurner|' .
    'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
    'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby|yandex';
    $isCrawler = (preg_match("/$crawlers/i", $userAgent) > 0);
    return $isCrawler;
}

function get_include_contents($filename, $galleryID, $skin, $skinID, $width, $height, $altColors) {
    if (is_file($filename)) {
        ob_start();
		extract($altColors);
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    return false;
}

?>