<?php
/**
 * @description Use WordPress Shortcode API for more features
 * @Docs http://codex.wordpress.org/Shortcode_API
 */

class FlAG_shortcodes {
	
	// register the new shortcodes
	function FlAG_shortcodes() {
	
		// do_shortcode on the_excerpt could causes several unwanted output. Uncomment it on your own risk
		// add_filter('the_excerpt', array(&$this, 'convert_shortcode'));
		// add_filter('the_excerpt', 'do_shortcode', 11);

		add_shortcode( 'flagallery', array(&$this, 'show_flashalbum' ) );
		add_shortcode( 'grandmp3', array(&$this, 'grandmp3' ) );
		add_shortcode( 'grandmusic', array(&$this, 'grandmusic' ) );
	}

	function show_flashalbum( $atts ) {

		global $wpdb, $flagdb;
	
		extract(shortcode_atts(array(
			'gid' 		=> '',
			'name'		=> '',
			'w'		 	=> '',
			'h'		 	=> '',
			'orderby' 	=> '',
			'order'	 	=> '',
			'exclude' 	=> '',
			'skin'	 	=> '',
			'play'	 	=> '',
			'wmode' 	=> ''
		), $atts ));
		
		// make an array out of the ids
        if($gid == "all") {
			if(!$orderby) $orderby='gid';
			if(!$order) $order='DESC';
            $gallerylist = $flagdb->find_all_galleries($orderby, $order);
            if(is_array($gallerylist)) {
				$excludelist = explode(',',$exclude);
				foreach($gallerylist as $gallery) {
					if (in_array($gallery->gid, $excludelist))
						continue;
					$gids.='_'.$gallery->gid;
				}
                $gids = ltrim($gids,'_');
                $out = flagShowFlashAlbum($gids, $name, $w, $h, $skin, $playlist, $wmode);
			} else {
            	$out = __('[Gallery not found]','flag');
			}
        } else {
            $ids = explode( ',', $gid );
    		$gids = str_replace(',','_',$gid);

    		foreach ($ids as $id) {
    			$galleryID = $wpdb->get_var("SELECT gid FROM $wpdb->flaggallery WHERE gid = '$id' ");
    			if(!$galleryID) $galleryID = $wpdb->get_var("SELECT gid FROM $wpdb->flaggallery WHERE name = '$id' ");
    			if(!$galleryID) return $out =  sprintf(__('[Gallery %s not found]','flag'),$id);
    		}

    		if( $galleryID )
    			$out = flagShowFlashAlbum($gids, $name, $w, $h, $skin, $playlist, $wmode);
    		else
    			$out = __('[Gallery not found]','flag');
    		}

        return $out;
	}

	function grandmusic( $atts ) {

		extract(shortcode_atts(array(
			'playlist'	=> '',
			'w'		 	=> '',
			'h'		 	=> '',
		), $atts ));
		
		if($playlist)
            $out = flagShowMPlayer($playlist, $w, $h);
        return $out;
	}

	function grandmp3( $atts ) {
		global $wpdb;
		extract(shortcode_atts(array(
			'id'	=> '',
		), $atts ));
		$flag_options = get_option('flag_options');
		if($id) {
			wp_enqueue_script( 'swfobject' );
			$mp3 = get_post(intval($id,10));
			$out = '<script type="text/javascript">swfobject.embedSWF("'.FLAG_URLPATH.'lib/mini.swf", "c-'.$id.'", "250", "20", "10.1.52", "expressInstall.swf", {path:"'.str_replace(array('http://','.mp3'), array('',''), $mp3->guid).'"}, {wmode:"transparent"}, {id:"f-'.$id.'",name:"f-'.$id.'"});</script>
<div id="c-'.$id.'"></div>';
		}
       	return $out;
	}
	
}

// let's use it
$flagShortcodes = new FlAG_Shortcodes;	

?>
