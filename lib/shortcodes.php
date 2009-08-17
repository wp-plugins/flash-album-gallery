<?php
/**
 * @description Use WordPress Shortcode API for more features
 * @Docs http://codex.wordpress.org/Shortcode_API
 */

class FlAG_shortcodes {
	
	// register the new shortcodes
	function FlAG_shortcodes() {
	
		// convert the old shortcode
		add_filter('the_content', array(&$this, 'convert_shortcode'));
		
		// do_shortcode on the_excerpt could causes several unwanted output. Uncomment it on your own risk
		// add_filter('the_excerpt', array(&$this, 'convert_shortcode'));
		// add_filter('the_excerpt', 'do_shortcode', 11);

		add_shortcode( 'flagallery', array(&$this, 'show_flashalbum' ) );
	}

	 /**
	   * FlAG_shortcodes::convert_shortcode()
	   * convert old shortcodes to the new WordPress core style
	   * [gallery=1]  ->> [flagallery gid=1]
	   *
	   * @param string $content Content to search for shortcodes
	   * @return string Content with new shortcodes.
	   */
	function convert_shortcode($content) {

		if ( stristr( $content, '[flagallery' )) {
			$search = "@(?:<p>)*\s*\[flagallery\s*=\s*(.*?)(\w+|^\+)(|,(\d+)|,)(|,(\d+))\]\s*(?:</p>)*@i";
			if (preg_match_all($search, $content, $matches, PREG_SET_ORDER)) {

				foreach ($matches as $match) {
					// remove the comma
					$match[2] = ltrim($match[2],',');
					$match[3] = ltrim($match[3],',');
					$match[3] = ltrim($match[4],',');
					$replace = "[flagallery gid=\"{$match[1]}\" name=\"{$match[2]}\" w=\"{$match[3]}\" h=\"{$match[4]}\"]";
					$content = str_replace ($match[0], $replace, $content);
				}
			}
		}

		return $content;
	}

	function show_flashalbum( $atts ) {

		global $wpdb, $flagdb;
	
		extract(shortcode_atts(array(
			'gid' 		=> '',
			'name'		=> '',
			'w'		 	=> '',
			'h'		 	=> ''
		), $atts ));
		
		// make an array out of the ids
        if($gid == "all") {
            $gallerylist = $flagdb->find_all_galleries('gid', 'ASC');
            if(is_array($gallerylist)) {
				foreach($gallerylist as $gallery) {
					$gids.='_'.$gallery->gid;
				}
                $gids = ltrim($gids,'_');
                $out = flagShowFlashAlbum($gids, $name, $w, $h);
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
    			$out = flagShowFlashAlbum($gids, $name, $w, $h);
    		else
    			$out = __('[Gallery not found]','flag');
    		}

        return $out;
	}
	
}

// let's use it
$flagShortcodes = new FlAG_Shortcodes;	

?>
