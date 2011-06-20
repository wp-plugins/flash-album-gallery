<?php
/**
 * @description Use WordPress Shortcode API for more features
 * @Docs http://codex.wordpress.org/Shortcode_API
 */

class FlAG_shortcodes {
	var $flag_shortcode;
	var $flag_add_script;
	var $flag_add_mousewheel;
	// register the new shortcodes
	function FlAG_shortcodes() {
	
		// do_shortcode on the_excerpt could causes several unwanted output. Uncomment it on your own risk
		// add_filter('the_excerpt', array(&$this, 'convert_shortcode'));
		// add_filter('the_excerpt', 'do_shortcode', 11);

		add_shortcode( 'flagallery', array(&$this, 'show_flashalbum' ) );
		add_shortcode( 'grandmp3', array(&$this, 'grandmp3' ) );
		add_shortcode( 'grandmusic', array(&$this, 'grandmusic' ) );
		add_shortcode( 'grandflv', array(&$this, 'grandflv' ) );
		add_shortcode( 'grandvideo', array(&$this, 'grandvideo' ) );
		add_action('wp_footer', array(&$this, 'add_script'));

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
		
		$out = '';
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
		$this->flag_shortcode = true;
		$this->flag_add_script = true;

		$flag_options = get_option('flag_options');
		if($skin == '') $skin = $flag_options['flashSkin'];
		$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
		if(!is_dir($skinpath)) {
			$skin = 'default';
			$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
		} 
		$swfmousewheel = false;
		if(file_exists($skinpath . "/settings/settings.xml")) {
			$data = file_get_contents($skinpath . "/settings/settings.xml");
			$swfmousewheel = flagGetBetween($data,'<swfmousewheel>','</swfmousewheel>');
		} 
		if($swfmousewheel == 'true') $this->flag_add_mousewheel = true;

        return $out;
	}

	function add_script() {
		if ( $this->flag_shortcode ) {
			wp_register_script('flagscroll', plugins_url('/admin/js/flagscroll.js', dirname(__FILE__)), array('jquery'), '1.0', true );
			wp_print_scripts('flagscroll');
		}
		if ( $this->flag_add_script ) {
			wp_register_style('fancybox', plugins_url('/admin/js/jquery.fancybox-1.3.4.css', dirname(__FILE__)) );
			wp_print_styles('fancybox');
			wp_register_script('fancybox', plugins_url('/admin/js/jquery.fancybox-1.3.4.pack.js', dirname(__FILE__)), array('jquery'), '1.3.4', true );
			wp_print_scripts('fancybox');
			wp_register_script('flagscript', plugins_url('/admin/js/script.js', dirname(__FILE__)), array('jquery'), '1.0', true );
			wp_print_scripts('flagscript');
		}
		if ( $this->flag_add_mousewheel ) {
			wp_register_script('swfmousewheel', plugins_url('/admin/js/swfmousewheel.js', dirname(__FILE__)), false, '2.0', true );
			wp_print_scripts('swfmousewheel');
		}
	}

	function grandmusic( $atts ) {

		extract(shortcode_atts(array(
			'playlist'	=> '',
			'w'		 	=> '',
			'h'		 	=> ''
		), $atts ));
		$out = '';
		if($playlist) {
			$this->flag_shortcode = true;
			$this->flag_add_mousewheel = true;
            $out = flagShowMPlayer($playlist, $w, $h);
		}
        return $out;
	}

	function grandmp3( $atts ) {
		global $wpdb;
		extract(shortcode_atts(array(
			'id'	=> ''
		), $atts ));
		$out = '';
		$flag_options = get_option('flag_options');
		if($id) {
			$mp3 = get_post(intval($id,10));
			$out = '<script type="text/javascript">swfobject.embedSWF("'.FLAG_URLPATH.'lib/mini.swf", "c-'.$id.'", "250", "20", "10.1.52", "expressInstall.swf", {path:"'.str_replace(array('http://','.mp3'), array('',''), $mp3->guid).'"}, {wmode:"transparent"}, {id:"f-'.$id.'",name:"f-'.$id.'"});</script>
<div id="c-'.$id.'"></div>';
		}
       	return $out;
	}

	function grandvideo( $atts ) {

		extract(shortcode_atts(array(
			'playlist'	=> '',
			'w'		 	=> '',
			'h'		 	=> ''
		), $atts ));
		$out = '';
		if($playlist) {
			$this->flag_shortcode = true;
            $out = flagShowVPlayer($playlist, $w, $h);
		}
        return $out;
	}

	function grandflv( $atts ) {
		global $wpdb;
		extract(shortcode_atts(array(
			'id'		=> '',
			'w'			=> '',
			'h'			=> '',
			'autoplay'	=> ''
		), $atts ));
		$out = '';
		if($id) {
			$this->flag_shortcode = true;
            $out = flagShowVmPlayer($id, $w, $h, $autoplay);
		}
       	return $out;
	}
	
}

// let's use it
$flagShortcodes = new FlAG_Shortcodes;	

?>
