<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

function get_playlist_data( $playlist_file ) {
	global $wpdb;	
	$playlist_content = file_get_contents($playlist_file);

	$playlist_data['title'] = flagGallery::flagGetBetween($playlist_content,'<title><![CDATA[',']]></title>');
	$playlist_data['skin'] = flagGallery::flagGetBetween($playlist_content,'<skin><![CDATA[',']]></skin>');
	$playlist_data['width'] = flagGallery::flagGetBetween($playlist_content,'<width><![CDATA[',']]></width>');
	$playlist_data['height'] = flagGallery::flagGetBetween($playlist_content,'<height><![CDATA[',']]></height>');
	$playlist_data['description'] = flagGallery::flagGetBetween($playlist_content,'<description><![CDATA[',']]></description>');
	preg_match_all( '|<item id="(.*)">|', $playlist_content, $items );
	$playlist_data['items'] = $items[1];
	return $playlist_data;
}

/**
 * Check the playlists directory and retrieve all playlist files with playlist data.
 *
 */
function get_playlists($playlist_folder = '') {

	$flag_options = get_option('flag_options');
	$flag_playlists = array ();
	$playlist_root = ABSPATH.$flag_options['galleryPath'].'playlists';
	if( !empty($playlist_folder) )
		$playlist_root = $playlist_folder;

	// Files in flagallery/playlists directory
	$playlists_dir = @ opendir( $playlist_root);
	$playlist_files = array();
	if ( $playlists_dir ) {
		while (($file = readdir( $playlists_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' )
				continue;
			if ( substr($file, -4) == '.xml' )
				$playlist_files[] = $file;
		}
	}
	@closedir( $playlists_dir );

	if ( !$playlists_dir || empty($playlist_files) )
		return $flag_playlists;

	foreach ( $playlist_files as $playlist_file ) {
		if ( !is_readable( "$playlist_root/$playlist_file" ) )
			continue;

		$playlist_data = get_playlist_data( "$playlist_root/$playlist_file" );

		if ( empty ( $playlist_data['title'] ) )
			continue;

		$flag_playlists[basename( $playlist_file, ".xml" )] = $playlist_data;
	}
	uasort( $flag_playlists, create_function( '$a, $b', 'return strnatcasecmp( $a["title"], $b["title"] );' ));

	return $flag_playlists;
}

function flagSavePlaylist($title,$descr,$data,$file='') {
	global $wpdb;
	if(!trim($title)) {
		$title = 'default';
	}
	if (!$file) {
		$file = sanitize_title($title);
	}
	if(!is_array($data))
		$data = explode(',', $data);

	$flag_options = get_option('flag_options');
    $skin = isset($_POST['skinname'])? $_POST['skinname'] : 'music_default';
    $skinaction = isset($_POST['skinaction'])? $_POST['skinaction'] : 'update';
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	$playlistPath = ABSPATH.$flag_options['galleryPath'].'playlists/'.$file.'.xml';
	if( file_exists($playlistPath) && ($skin == $skinaction) ) {
		$settings = file_get_contents($playlistPath);
	} else {
		$settings = file_get_contents($skinpath . "/settings/settings.xml");
	}
	$properties = flagGallery::flagGetBetween($settings,'<properties>','</properties>');

	if(count($data)) {
		$content = '<gallery>
<properties>'.$properties.'</properties>
<category id="'.$file.'">
	<properties>
		<title><![CDATA['.$title.']]></title>
		<description><![CDATA['.$descr.']]></description>
		<skin><![CDATA['.$skin.']]></skin>
	</properties>
	<items>';

		foreach( (array) $data as $id) {
			$mp3 = get_post($id);
			if($mp3->post_mime_type == 'audio/mpeg') {
			    $thumb = get_post_meta($id, 'thumbnail', true);
				$content .= '
		<item id="'.$mp3->ID.'">
          <track>'.$mp3->guid.'</track>
          <title><![CDATA['.$mp3->post_title.']]></title>
          <description><![CDATA['.$mp3->post_content.']]></description>
          <thumbnail>'.$thumb.'</thumbnail>
        </item>';
			}
		}
		$content .= '
	</items>
</category>
</gallery>';
		// Save options
		$flag_options = get_option('flag_options');
		if(wp_mkdir_p(ABSPATH.$flag_options['galleryPath'].'playlists/')) {
			if( flagGallery::saveFile($playlistPath,$content,'w') ){
				flagGallery::show_message(__('Playlist Saved Successfully','flag'));
			}
		} else {
			flagGallery::show_message(__('Create directory please:','flag').'"/'.$flag_options['galleryPath'].'playlists/"');
		}
	}
}

function flagSavePlaylistSkin($file) {
	global $wpdb;
	$flag_options = get_option('flag_options');
    $skin = isset($_POST['skinname'])? $_POST['skinname'] : 'music_default';
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	$playlistPath = ABSPATH.$flag_options['galleryPath'].'playlists/'.$file.'.xml';
	$playlist = file_get_contents($playlistPath);
	$settings = file_get_contents($skinpath . "/settings/settings.xml");
	//$oldproperties = flagGallery::flagGetBetween($playlist,'<properties>','</properties>');
	$newproperties = flagGallery::flagGetBetween($settings,'<properties>','</properties>');
	$content = preg_replace("|<properties>.*?</properties>|si", '<properties>'.$newproperties.'</properties>', $playlist, 1);
	// Save options
	if( flagGallery::saveFile($playlistPath,$content,'w') ){
		flagGallery::show_message(__('Playlist Skin Options Saved Successfully','flag'));
	}
}

function flagSaveWpMedia() {
   	if ( !empty($_POST['item_a']) )
    //echo '<pre>';print_r($_POST['item_a']); echo '</pre>';
    foreach ( $_POST['item_a'] as $item_id => $item ) {
		$post = $_post = get_post($item_id, ARRAY_A);
		$postmeta = get_post_meta($item_id, 'thumbnail', true);
		if ( isset($item['post_content']) )
			$post['post_content'] = $item['post_content'];
		if ( isset($item['post_title']) )
			$post['post_title'] = $item['post_title'];

		$post = apply_filters('attachment_fields_to_save', $post, $item);

        if( isset($item['post_thumb']) && $item['post_thumb'] != $postmeta ) {
            /*$thumb = image_resize( $item['post_thumb'], $max_w=200, $max_h=200, $crop = true, $suffix = null, $dest_path = null, $jpeg_quality = 90 );
            if(is_string($thumb))
                update_post_meta($item_id, 'thumbnail', $thumb);
            else*/
                update_post_meta($item_id, 'thumbnail', $item['post_thumb']);
        } /*else {
          delete_post_meta($item_id, 'thumbnail');
        }*/

		if ( isset($post['errors']) ) {
			$errors[$item_id] = $post['errors'];
			unset($post['errors']);
		}

		if ( $post != $_post )
			wp_update_post($post);
	}
}

function flag_playlist_delete($playlist) {
	$flag_options = get_option('flag_options');
	$playlistXML = ABSPATH.$flag_options['galleryPath'].'playlists/'.$playlist.'.xml';
	if(file_exists($playlistXML)){
		if(unlink($playlistXML)) {
			flagGallery::show_message("'".$playlist.".xml' ".__('deleted','flag'));
		}
	}
}

?>