<?php
/*
bSkin Name: Default Banner Player
Skin URI:
Description: New version! Responsive layout. Themes available.
Author: PGC
Author URI: http://PhotoGalleryCreator.com
Version: 2.0
*/

function flagShowSkin_banner_default($args) {
	extract($args);
	$flag_options = get_option('flag_options');

	$skinID = mt_rand();
	// look up for the path
	$playlistpath = ABSPATH.$flag_options['galleryPath'].'playlists/banner/'.$xml.'.xml';
	$data = file_get_contents($playlistpath);
	if(empty($width)) {
		$width = flagGetBetween($data,'<width><![CDATA[',']]></width>');
	}
	if(empty($height)) {
		$height = flagGetBetween($data,'<height><![CDATA[',']]></height>');
	}

	$theme = flagGetBetween($data,'<theme><![CDATA[',']]></theme>');
	if(empty($theme))
		$theme = 'default';
	$effect = flagGetBetween($data,'<effect><![CDATA[',']]></effect>');
	$slices = flagGetBetween($data,'<slices><![CDATA[',']]></slices>');
	$animSpeed = flagGetBetween($data,'<animSpeed><![CDATA[',']]></animSpeed>');
	$pauseTime = flagGetBetween($data,'<pauseTime><![CDATA[',']]></pauseTime>');
	$startSlide = flagGetBetween($data,'<startSlide><![CDATA[',']]></startSlide>');
	$pauseOnHover = flagGetBetween($data,'<pauseOnHover>','</pauseOnHover>');
	$directionNav = flagGetBetween($data,'<directionNav>','</directionNav>');
	$controlNav = flagGetBetween($data,'<controlNav>','</controlNav>');
	$randomStart = flagGetBetween($data,'<randomStart>','</randomStart>');
	$linkTarget = (flagGetBetween($data,'<linkTarget>','</linkTarget>') == 'false')? '' : ' target="_blank"';
	$out = '';

	if(count($items)) {
		$out .= '<link rel="stylesheet" href="'.FLAG_URLPATH.'admin/js/themes/'.$theme.'/styles.css" type="text/css" media="screen" />
<link rel="stylesheet" href="'.FLAG_URLPATH.'admin/js/nivo-slider.css" type="text/css" media="screen" />
<script src="'.FLAG_URLPATH.'admin/js/jquery.nivo.slider.pack.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(window).load(function() {
    jQuery("#slider_'.$skinID.'").nivoSlider({
        effect:"'.$effect.'", // Specify sets like: fold,fade,sliceDown
        slices:'.$slices.', // For slice animations
        boxCols:8, // For box animations
        boxRows:4, // For box animations
        animSpeed:'.$animSpeed.', // Slide transition speed
        pauseTime:'.$pauseTime.', // How long each slide will show
        startSlide:'.$startSlide.', // Set starting Slide (0 index)
        directionNav:'.$directionNav.', // Next & Prev navigation
        controlNav:'.$controlNav.', // 1,2,3... navigation
        controlNavThumbs:false, // Use thumbnails for Control Nav
        pauseOnHover:'.$pauseOnHover.', // Stop animation while hovering
        prevText:"Prev", // Prev directionNav text
        nextText:"Next", // Next directionNav text
		randomStart: '.$randomStart.'
    });
});
</script>';
		$marginBot = $keyboardNav? '55px' : '0';
		$out .= '
<div class="slider-wrapper theme-'.$theme.'" style="position: relative; margin-bottom:'.$marginBot.'">
    <div class="ribbon"></div>
    <div id="slider_'.$skinID.'" class="nivoSlider">';
		$suffix = $width.'x'.$height;
		foreach( $items as $id ) {
			$ban = get_post($id);
		    //$thumb = get_post_meta($id, 'thumbnail', true);
			$url = wp_get_attachment_url($ban->ID);
			$path = get_attached_file($ban->ID);
			$info = pathinfo($path);
			$dir = $info['dirname'];
			$ext = $info['extension'];
			$name = urldecode( basename( str_replace( '%2F', '/', urlencode( $path ) ), ".$ext" ) );
			$img_file = "{$dir}/{$name}-{$suffix}.{$ext}";
			if(!file_exists($img_file)){
			    $track = $url;
			} else {
				$track = dirname($url)."/{$name}-{$suffix}.{$ext}";
			}
		    $link = get_post_meta($id, 'link', true);
		    $title = ($ban->post_title)? ' title="'.strip_tags($ban->post_title).'"' : '';
			if($link) {
				$out .= '
		<a href="'.$link.'"'.$linkTarget.'><img src="'.$track.'" alt=""'.$title.' /></a>';
			} else {
				$out .= '
	    <img src="'.$track.'" alt=""'.$title.' />';
			}
		}
	}
	$out .= '
    </div>
</div>';

	$out = apply_filters('flag_show_flash_b_content', $out);	
			
	return $out;	
}
remove_all_filters( 'flagShowBannerSkin' );
add_filter( 'flagShowBannerSkin', 'flagShowSkin_banner_default' );
?>