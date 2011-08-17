<?php
/*
* GRAND FlAGallery Widget
*/

/**
 * flagSlideshowWidget - The slideshow widget control for GRAND FlAGallery ( require WP2.8 or higher)
 *
 * @package GRAND FlAGallery
 * @access public
 */
class flagSlideshowWidget extends WP_Widget {

	function flagSlideshowWidget() {
		$widget_ops = array('classname' => 'widget_slideshow', 'description' => __( 'Show a GRAND FlAGallery Slideshow', 'flag') );
		$this->WP_Widget('flag-slideshow', __('FLAGallery Slideshow', 'flag'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __('Slideshow', 'flag') : $instance['title'], $instance, $this->id_base);

		$out = $this->render_slideshow($instance['galleryid'] , $instance['width'] , $instance['height'] , $instance['skin']);

		if ( !empty( $out ) ) {
			echo $before_widget;
			if ( $title)
				echo $before_title . $title . $after_title;
		?>
		<div class="flag_slideshow widget">
			<?php echo $out; ?>
		</div>
		<?php
			echo $after_widget;
		}

	}

	function render_slideshow($gid, $w = '100%', $h = '200', $skin = 'default') {
        $out = do_shortcode('[flagallery gid='.$gid.' name=\' \' w='.$w.' h='.$h.' skin='.$skin.']');
		return $out;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['galleryid'] = (int) $new_instance['galleryid'];
		$instance['height'] = (int) $new_instance['height'];
		$instance['width'] = $new_instance['width'];
		$instance['skin'] = $new_instance['skin'];

		return $instance;
	}

	function form( $instance ) {

		global $wpdb;

		require_once (dirname( dirname(__FILE__) ) . '/admin/get_skin.php');

		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Slideshow', 'galleryid' => '0', 'height' => '200', 'width' => '100%', 'skin' => 'default') );
		$title  = esc_attr( $instance['title'] );
		$height = esc_attr( $instance['height'] );
		$width  = esc_attr( $instance['width'] );
		$skin  = esc_attr( $instance['skin'] );
		$tables = $wpdb->get_results("SELECT * FROM $wpdb->flaggallery ORDER BY 'name' ASC ");
		$all_skins = get_skins();
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('galleryid'); ?>"><?php _e('Select Gallery:', 'flag'); ?></label>
				<select size="1" name="<?php echo $this->get_field_name('galleryid'); ?>" id="<?php echo $this->get_field_id('galleryid'); ?>" class="widefat">
<?php
				if($tables) {
					foreach($tables as $table) {
					echo '<option value="'.$table->gid.'" ';
					if ($table->gid == $instance['galleryid']) echo "selected='selected' ";
					echo '>'.$table->gid.' - '.$table->name.'</option>'."\n\t";
					}
				}
?>
				</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', 'flag'); ?></label> <input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" style="padding: 3px; width: 45px;" value="<?php echo $height; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', 'flag'); ?></label> <input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" style="padding: 3px; width: 45px;" value="<?php echo $width; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('skin'); ?>"><?php _e('Select Skin:', 'flag'); ?></label>
				<select size="1" name="<?php echo $this->get_field_name('skin'); ?>" id="<?php echo $this->get_field_id('skin'); ?>" class="widefat">
					<option value="" <?php if (0 == $instance['skin']) echo "selected='selected' "; ?> ><?php _e('Choose Skin', 'flag'); ?></option>
<?php
				if($all_skins) {
					foreach ( (array)$all_skins as $skin_file => $skin_data) {
						echo '<option value="'.dirname($skin_file).'"';
						if (dirname($skin_file) == $instance['skin']) echo ' selected="selected"';
						echo '>'.$skin_data['Name'].'</option>'."\n";
					}
				}
?>
				</select>
		</p>
<?php
	}

}

// register it
//add_action('widgets_init', create_function('', 'return register_widget("flagSlideshowWidget");'));


class flagBannerWidget extends WP_Widget {

	function flagBannerWidget() {
		$widget_ops = array('classname' => 'widget_banner', 'description' => __( 'Show a GRAND FlAGallery Banner', 'flag') );
		$this->WP_Widget('flag-banner', __('FLAGallery Banner', 'flag'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __('Banner', 'flag') : $instance['title'], $instance, $this->id_base);

		$out = $this->render_slideshow($instance['xml'] , $instance['width'] , $instance['height'] , $instance['skin']);

		if ( !empty( $out ) ) {
			echo $before_widget;
			if ( $title)
				echo $before_title . $title . $after_title;
		?>
		<div class="flag_banner widget">
			<?php echo $out; ?>
		</div>
		<?php
			echo $after_widget;
		}

	}

	function render_slideshow($xml, $w = '100%', $h = '200', $skin = '') {
        $out = do_shortcode('[grandbannerwidget xml='.$xml.' w='.$w.' h='.$h.' skin='.$skin.']');
		return $out;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['xml'] = $new_instance['xml'];
		$instance['height'] = (int) $new_instance['height'];
		$instance['width'] = $new_instance['width'];
		$instance['skin'] = $new_instance['skin'];

		return $instance;
	}

	function form( $instance ) {

		global $wpdb;

		require_once (dirname( dirname(__FILE__) ) . '/admin/get_skin.php');
		require_once (dirname( dirname(__FILE__) ) . '/admin/banner.functions.php');

		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Banner', 'xml' => '', 'width' => '100%', 'height' => '200', 'skin' => 'banner_widget_default') );
		$title  = esc_attr( $instance['title'] );
		$width  = esc_attr( $instance['width'] );
		$height = esc_attr( $instance['height'] );
		$skin  = esc_attr( $instance['skin'] );
		$all_playlists = get_b_playlists();
		$all_skins = get_skins(false,'w');
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('xml'); ?>"><?php _e('Select XML:', 'flag'); ?></label>
				<select size="1" name="<?php echo $this->get_field_name('xml'); ?>" id="<?php echo $this->get_field_id('xml'); ?>" class="widefat">
<?php
	foreach((array)$all_playlists as $playlist_file => $playlist_data) {
		$playlist_name = basename($playlist_file, '.xml');
?>
					<option <?php selected($playlist_name , $instance['xml']); ?> value="<?php echo $playlist_name; ?>"><?php echo $playlist_data['title']; ?></option>
<?php
	}
?>
				</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', 'flag'); ?></label> <input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" style="padding: 3px; width: 45px;" value="<?php echo $height; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', 'flag'); ?></label> <input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" style="padding: 3px; width: 45px;" value="<?php echo $width; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('skin'); ?>"><?php _e('Select Skin:', 'flag'); ?></label>
				<select size="1" name="<?php echo $this->get_field_name('skin'); ?>" id="<?php echo $this->get_field_id('skin'); ?>" class="widefat">
<?php
				if($all_skins) {
					foreach ( (array)$all_skins as $skin_file => $skin_data) {
						echo '<option value="'.dirname($skin_file).'"';
						if (dirname($skin_file) == $instance['skin']) echo ' selected="selected"';
						echo '>'.$skin_data['Name'].'</option>'."\n";
					}
				}
?>
				</select>
		</p>
<?php
	}

}

// register it
add_action('widgets_init', create_function('', 'return register_widget("flagBannerWidget");'));


/**
 * flagWidget - The widget control for GRAND FlAGallery
 *
 * @package GRAND FlAGallery
 * @access public
 */
class flagWidget extends WP_Widget {
    
   	function flagWidget() {
		$widget_ops = array('classname' => 'flag_images', 'description' => __( 'Add recent or random images from the galleries', 'flag') );
		$this->WP_Widget('flag-images', __('FLAGallery Widget', 'flag'), $widget_ops);
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title']	= strip_tags($new_instance['title']);
		$instance['items']	= (int) $new_instance['items'];
		$instance['type']	= $new_instance['type'];
		$instance['width']	= (int) $new_instance['width'];
		$instance['height']	= (int) $new_instance['height'];
		$instance['exclude'] = $new_instance['exclude'];
		$instance['list']	 = $new_instance['list'];
		$instance['webslice']= (bool) $new_instance['webslice'];

		return $instance;
	}

	function form( $instance ) {
		
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 
            'title' => 'Gallery', 
            'items' => '4',
            'type'  => 'random',
            'height' => '50', 
            'width' => '75',
            'exclude' => 'all',
            'list'  =>  '',
            'webslice'  => true ) );
		$title  = esc_attr( $instance['title'] );
		$items  = intval  ( $instance['items'] );
        $height = esc_attr( $instance['height'] );
		$width  = esc_attr( $instance['width'] );

		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title :','flag'); ?>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title');?>" type="text" class="widefat" value="<?php echo $title; ?>" />
			</label>
		</p>
			
		<p>
			<?php _e('Show :','flag'); ?><br />
			<label for="<?php echo $this->get_field_id('items'); ?>">
			<input style="width: 50px;" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items');?>" type="text" value="<?php echo $items; ?>" />
			</label>
			<?php _e('Thumbnails','flag'); ?>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>_random">
			<input id="<?php echo $this->get_field_id('type'); ?>_random" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="random" <?php checked("random" , $instance['type']); ?> /> <?php _e('random','flag'); ?>
			</label>
            <label for="<?php echo $this->get_field_id('type'); ?>_recent">
            <input id="<?php echo $this->get_field_id('type'); ?>_recent" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="recent" <?php checked("recent" , $instance['type']); ?> /> <?php _e('recent added ','flag'); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('webslice'); ?>">
			<input id="<?php echo $this->get_field_id('webslice'); ?>" name="<?php echo $this->get_field_name('webslice'); ?>" type="checkbox" value="1" <?php checked(true , $instance['webslice']); ?> /> <?php _e('Enable IE8 Web Slices','flag'); ?>
			</label>
		</p>

		<p>
			<?php _e('Width x Height :','flag'); ?><br />
			<input style="width: 50px; padding:3px;" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" /> x
			<input style="width: 50px; padding:3px;" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" /> (px)
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Select :','flag'); ?>
			<select id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" class="widefat">
				<option <?php selected("all" , $instance['exclude']); ?>  value="all" ><?php _e('All galleries','flag'); ?></option>
				<option <?php selected("denied" , $instance['exclude']); ?> value="denied" ><?php _e('Only which are not listed','flag'); ?></option>
				<option <?php selected("allow" , $instance['exclude']); ?>  value="allow" ><?php _e('Only which are listed','flag'); ?></option>
			</select>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('list'); ?>"><?php _e('Gallery ID :','flag'); ?>
			<input id="<?php echo $this->get_field_id('list'); ?>" name="<?php echo $this->get_field_name('list'); ?>" type="text" class="widefat" value="<?php echo $instance['list']; ?>" />
			<br /><small><?php _e('Gallery IDs, separated by commas.','flag'); ?></small>
			</label>
		</p>
		
	<?php
	
	}

	function widget( $args, $instance ) {
		extract( $args );
        
        $title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title'], $instance, $this->id_base);

		global $wpdb;
				
		$items 	= $instance['items'];
		$exclude = $instance['exclude'];
		$list = $instance['list'];
		$webslice = $instance['webslice'];

		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->flagpictures WHERE exclude != 1 ");
		if ($count < $instance['items']) 
			$instance['items'] = $count;

		$exclude_list = '';

		// THX to Kay Germer for the idea & addon code
		if ( (!empty($list)) && ($exclude != 'all') ) {
			$list = explode(',',$list);
			// Prepare for SQL
			$list = "'" . implode("', '", $list ) . "'";
			
			if ($exclude == 'denied')	
				$exclude_list = "AND NOT (t.gid IN ($list))";

			if ($exclude == 'allow')	
				$exclude_list = "AND t.gid IN ($list)";
            
            // Limit the output to the current author, can be used on author template pages
            if ($exclude == 'user_id' )
                $exclude_list = "AND t.author IN ($list)";                
		}
		
		if ( $instance['type'] == 'random' ) 
			$imageList = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE tt.exclude != 1 $exclude_list ORDER by rand() limit {$items}");
		else
			$imageList = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE tt.exclude != 1 $exclude_list ORDER by pid DESC limit 0,$items");
		
        // IE8 webslice support if needed
		if ( $webslice ) {
			$before_widget .= "\n" . '<div class="hslice" id="flag-webslice" >' . "\n";
            //the headline needs to have the class enty-title
            $before_title  = str_replace( 'class="' , 'class="entry-title ', $before_title);
			$after_widget  =  '</div>'."\n" . $after_widget;			
		}	
		                      
		echo $before_widget . $before_title . $title . $after_title;
		echo "\n" . '<div class="flag-widget entry-content">'. "\n";
	
		if (is_array($imageList)){
			foreach($imageList as $image) {
				// get the URL constructor
				$image = new flagImage($image);

				// get the effect code
				$thumbcode = 'class="flag_fancybox" rel="flag_widget"';
				
				// enable i18n support for alttext and description
				$alttext      =  htmlspecialchars( stripslashes( flagGallery::i18n($image->alttext, 'pic_' . $image->pid . '_alttext') ));
				$description  =  htmlspecialchars( stripslashes( flagGallery::i18n($image->description, 'pic_' . $image->pid . '_description') ));
				
				//TODO:For mixed portrait/landscape it's better to use only the height setting, if widht is 0 or vice versa
				$out = '<a href="' . $image->imageURL . '" title="' . $description . '" ' . $thumbcode .'>';
				$out .= '<img src="'.$image->thumbURL.'" width="'.$instance['width'].'" height="'.$instance['height'].'" title="'.$alttext.'" alt="'.$alttext.'" />';			
				echo $out . '</a>'."\n";
				
			}
		}
		
		echo '</div>'."\n";
		echo $after_widget;
		
	}

}// end widget class

// register it
//add_action('widgets_init', create_function('', 'return register_widget("flagWidget");'));

/**
 * flagSlideshowWidget($galleryID, $width, $height)
 * Function for templates without widget support
 * 
 * @param integer $galleryID 
 * @param string $width
 * @param string $height
 * @return echo the widget content
 */
function flagSlideshowWidget($gid, $w = '100%', $h = '200', $skin = 'default') {

	echo flagSlideshowWidget::render_slideshow($gid, $w, $h, $skin);

}

function flagBannerWidget($xml, $w = '100%', $h = '200', $skin = 'default') {

	echo flagBannerWidget::render_slideshow($xml, $w, $h, $skin);

}

/**
 * flagDisplayRandomImages($number,$width,$height,$exclude,$list,$show)
 * Function for templates without widget support
 *
 * @return echo the widget content
 */
function flagDisplayRandomImages($number, $width = '75', $height = '50', $exclude = 'all', $list = '', $show = 'thumbnail') {
	
	$options = array(   'title'    => false, 
						'items'    => $number,
						'show'     => $show ,
						'type'     => 'random',
						'width'    => $width, 
						'height'   => $height, 
						'exclude'  => $exclude,
						'list'     => $list,
                        'webslice' => false );
                        
	$flag_widget = new flagWidget();
	$flag_widget->widget($args = array( 'widget_id'=> 'sidebar_1' ), $options);
}

/**
 * flagDisplayRecentImages($number,$width,$height,$exclude,$list,$show)
 * Function for templates without widget support
 *
 * @return echo the widget content
 */
function flagDisplayRecentImages($number, $width = '75', $height = '50', $exclude = 'all', $list = '', $show = 'thumbnail') {

	$options = array(   'title'    => false, 
						'items'    => $number,
						'show'     => $show ,
						'type'     => 'recent',
						'width'    => $width, 
						'height'   => $height, 
						'exclude'  => $exclude,
						'list'     => $list,
                        'webslice' => false );
                        
	$flag_widget = new flagWidget();
	$flag_widget->widget($args = array( 'widget_id'=> 'sidebar_1' ), $options);
}

?>