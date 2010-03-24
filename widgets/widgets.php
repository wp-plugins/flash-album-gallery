<?php
/*
* GRAND FlAGallery Widget
*/

/**
 * flagWidget - The widget control for GRAND FlAGallery ( require WP2.5 or higher)
 *
 * @package GRAND FlAGallery
 * @version 1.0
 * @access public
 */
if (!class_exists('flagWidget')) { 
class flagWidget {
	
	function flagWidget() {
	
		// Run our code later in case this loads prior to any required plugins.
		add_action('widgets_init', array(&$this, 'flag_widget_register'));
		
	}
	
	function flag_widget_register() {

		if ( !$options = get_option('flag_widget') )
			$options = array();
		
		$widget_ops = array('classname' => 'flag_images', 'description' => __( 'Add recent or random images from the galleries','flag' ));
		$control_ops = array('width' => 250, 'height' => 200, 'id_base' => 'flag-images');
		$name = __('FlAGallery Widget','flag');
		$id = false;

		foreach ( array_keys($options) as $o ) {
			// Old widgets can have null values for some reason
			if ( !isset($options[$o]['title']) )
				continue;
				
			$id = "flag-images-$o"; // Never never never translate an id
			wp_register_sidebar_widget($id, $name, array(&$this, 'flag_widget_output'), $widget_ops, array( 'number' => $o ) );
			wp_register_widget_control($id, $name, array(&$this, 'flag_widget_control'), $control_ops, array( 'number' => $o ));
		}

		// If there are none, we register the widget's existance with a generic template
		if ( !$id ) {
			wp_register_sidebar_widget( 'flag-images-1', $name, array(&$this, 'flag_widget_output'), $widget_ops, array( 'number' => -1 ) );
			wp_register_widget_control( 'flag-images-1', $name, array(&$this, 'flag_widget_control'), $control_ops, array( 'number' => -1 ) );
		}

	 }

	function flag_widget_control($widget_args = 1) {
		
		global $wp_registered_widgets;
		static $updated = false;
		
		// Get the widget ID
		if (is_numeric($widget_args))
			$widget_args = array('number' => $widget_args);

		$widget_args = wp_parse_args($widget_args, array('number' => -1));
		extract($widget_args, EXTR_SKIP);

		$options = get_option('flag_widget');
		if ( !is_array($options) )
			$options = array();
	
		if (!$updated && !empty($_POST['sidebar'])) {
			$sidebar = (string) $_POST['sidebar'];

			$sidebars_widgets = wp_get_sidebars_widgets();
			if ( isset($sidebars_widgets[$sidebar]) )
				$this_sidebar = &$sidebars_widgets[$sidebar];
			else
				$this_sidebar = array();

			foreach ( $this_sidebar as $_widget_id ) {
				// Remove all widgets of this type from the sidebar.  We'll add the new data in a second.  This makes sure we don't get any duplicate data
				// since widget ids aren't necessarily persistent across multiple updates
				if ( 'flag_images' == $wp_registered_widgets[$_widget_id]['classname'] 
					&& 	isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
					
					$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
					if (!in_array( "flag-images-$widget_number", $_POST['widget-id'])) // the widget has been removed.
						unset($options[$widget_number]);
				}
			}

			foreach ( (array) $_POST['widget_flag_images'] as $widget_number => $widget_flag_images ) {
				if ( !isset($widget_flag_images['width']) && isset($options[$widget_number]) ) // user clicked cancel
					continue;
					
				$widget_flag_images = stripslashes_deep( $widget_flag_images );
				$options[$widget_number]['title']	= $widget_flag_images['title'];
				$options[$widget_number]['items']	= (int) $widget_flag_images['items'];
				$options[$widget_number]['type']	= $widget_flag_images['type'];
				$options[$widget_number]['show']	= $widget_flag_images['show'];
				$options[$widget_number]['width']	= (int) $widget_flag_images['width'];
				$options[$widget_number]['height']	= (int) $widget_flag_images['height'];
				$options[$widget_number]['exclude']	= $widget_flag_images['exclude'];
				$options[$widget_number]['list']	= $widget_flag_images['list'];
				$options[$widget_number]['webslice']= (bool) $widget_flag_images['webslice'];

			}

			update_option('flag_widget', $options);
			$updated = true;
		}
		
		if ( -1 == $number ) {
			// Init parameters check
			$title = 'Gallery';
			$items = 4;
			$type = 'random';
			$show= 'thumbnail';
			$width = 75;
			$height = 50;
			$exclude = 'all';
			$list = '';
			$number = '%i%';
			$webslice = true;
		} else {
			extract( (array) $options[$number] );
		}

		// The form has inputs with names like widget-many[$number][something] so that all data for that instance of
		// the widget are stored in one $_POST variable: $_POST['widget-many'][$number]
		?>

		<p>
			<label for="flag_images-title-<?php echo $number; ?>"><?php _e('Title :','flag'); ?>
			<input id="flag_images-title-<?php echo $number; ?>" name="widget_flag_images[<?php echo $number; ?>][title] ?>" type="text" class="widefat" value="<?php echo $title; ?>" />
			</label>
		</p>
			
		<p>
			<label for="flag_images-items-<?php echo $number; ?>"><?php _e('Show :','flag'); ?><br />
			<select id="flag_images-items-<?php echo $number; ?>" name="widget_flag_images[<?php echo $number; ?>][items]">
				<?php for ( $i = 1; $i <= 10; ++$i ) echo "<option value='$i' ".($items==$i ? "selected='selected'" : '').">$i</option>"; ?>
			</select>
			<select id="flag_images-show-<?php echo $number; ?>" name="widget_flag_images[<?php echo $number; ?>][show]" >
				<option <?php selected("thumbnail" , $show); ?> value="thumbnail"><?php _e('Thumbnails','flag'); ?></option>
				<option <?php selected("original" , $show); ?> value="original"><?php _e('Original images','flag'); ?></option>
			</select>
			</label>
		</p>

		<p>
			<label for="widget_flag_images<?php echo $number; ?>">&nbsp;
			<input name="widget_flag_images[<?php echo $number; ?>][type]" type="radio" value="random" <?php checked("random" , $type); ?> /> <?php _e('random','flag'); ?>
			<input name="widget_flag_images[<?php echo $number; ?>][type]" type="radio" value="recent" <?php checked("recent" , $type); ?> /> <?php _e('recent added ','flag'); ?>
			</label>
		</p>

		<p>
			<label for="flag_webslice<?php echo $number; ?>">&nbsp;
			<input id="flag_webslice<?php echo $number; ?>" name="widget_flag_images[<?php echo $number; ?>][webslice]" type="checkbox" value="1" <?php checked(true , $webslice); ?> /> <?php _e('Enable IE8 Web Slices','flag'); ?>
			</label>
		</p>

		<p>
			<label for="flag_images-width-<?php echo $number; ?>"><?php _e('Width x Height :','flag'); ?><br />
			<input style="width: 50px; padding:3px;" id="flag_images-width-<?php echo $number; ?>" name="widget_flag_images[<?php echo $number; ?>][width]" type="text" value="<?php echo $width; ?>" /> x
			<input style="width: 50px; padding:3px;" id="flag_images-height-<?php echo $number; ?>" name="widget_flag_images[<?php echo $number; ?>][height]" type="text" value="<?php echo $height; ?>" /> (px)
			</label>
		</p>

		<p>
			<label for="flag_images-exclude-<?php echo $number; ?>"><?php _e('Select :','flag'); ?>
			<select id="flag_images-exclude-<?php echo $number; ?>" name="widget_flag_images[<?php echo $number; ?>][exclude]" class="widefat">
				<option <?php selected("all" , $exclude); ?>  value="all" ><?php _e('All galleries','flag'); ?></option>
				<option <?php selected("denied" , $exclude); ?> value="denied" ><?php _e('Only which are not listed','flag'); ?></option>
				<option <?php selected("allow" , $exclude); ?>  value="allow" ><?php _e('Only which are listed','flag'); ?></option>
			</select>
			</label>
		</p>

		<p>
			<label for="flag_images-list-<?php echo $number; ?>"><?php _e('Gallery ID :','flag'); ?>
			<input id="flag_images-list-<?php echo $number; ?>" name="widget_flag_images[<?php echo $number; ?>][list]" type="text" class="widefat" value="<?php echo $list; ?>" />
			<br/><small><?php _e('Gallery IDs, separated by commas.','flag'); ?></small>
			</label>
		</p>

		<input type="hidden" id="flag_images-submit-<?php echo $number; ?>" name="widget_flag_images[<?php echo $number; ?>][submit]" value="1" />
		
	<?php
	
	}

	function flag_widget_output($args, $widget_args = 1 , $options = false) {

		global $wpdb;
				
		extract($args, EXTR_SKIP);
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );
		
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract($widget_args, EXTR_SKIP);

		// We could get this also as parameter
		if (!$options)				
			$options = get_option('flag_widget');
			
		$title = $options[$number]['title'];
		$items 	= $options[$number]['items'];
		$exclude = $options[$number]['exclude'];
		$list = $options[$number]['list'];
		$webslice = $options[$number]['webslice'];

		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->flagpictures WHERE exclude != 1 ");
		if ($count < $options[$number]['items']) 
			$options[$number]['items'] = $count;

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
		}
		
		if ( $options[$number]['type'] == 'random' ) 
			$imageList = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE tt.exclude != 1 $exclude_list ORDER by rand() limit {$items}");
		else
			$imageList = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE tt.exclude != 1 $exclude_list ORDER by pid DESC limit 0,$items");
		
		if ( $webslice ) {
			//TODO:  If you change the title, it will not show up in widget admin panel
			$before_title  = "\n" . '<div class="hslice" id="flag-webslice" >' . "\n";
			$before_title .= '<h2 class="widgettitle entry-title">';
			$after_title   = '</h2>';
			$after_widget  =  '</div>'."\n" . $after_widget;			
		}	
		                      
		echo $before_widget . $before_title . $title . $after_title;
		echo "\n" . '<div class="flag-widget entry-content">'. "\n";
	
		if (is_array($imageList)){
			foreach($imageList as $image) {
				// get the URL constructor
				$image = new flagImage($image);

				// enable i18n support for alttext and description
				$alttext      =  htmlspecialchars( stripslashes( flagGallery::i18n($image->alttext) ));
				$description  =  htmlspecialchars( stripslashes( flagGallery::i18n($image->description) ));
				
				//TODO:For mixed portrait/landscape it's better to use only the height setting, if widht is 0 or vice versa
				$out = '<a href="' . $image->imageURL . '" title="' . $description . '">';
				// Typo fix for the next updates (happend until 1.0.2)
				$options[$number]['show'] = ( $options[$number]['show'] == 'orginal' ) ? 'original' : $options[$number]['show'];
				
				if ( $options[$number]['show'] == 'original' )
					$out .= '<img src="'.FLAG_URLPATH.'flagshow.php?pid='.$image->pid.'&amp;width='.$options[$number]['width'].'&amp;height='.$options[$number]['height']. '" title="'.$alttext.'" alt="'.$alttext.'" />';
				else	
					$out .= '<img src="'.$image->thumbURL.'" width="'.$options[$number]['width'].'" height="'.$options[$number]['height'].'" title="'.$alttext.'" alt="'.$alttext.'" />';			
				
				echo $out . '</a>'."\n";
				
			}
		}
		
		echo '</div>'."\n";
		echo $after_widget;
		
	}

}// end widget class
}
// let's show it
$flagWidget = new flagWidget;	

/**
 * flagDisplayRandomImages($number,$width,$height,$exclude,$list,$show)
 * Function for templates without widget support
 *
 * @return echo the widget content
 */
function flagDisplayRandomImages($number, $width = '75', $height = '50', $exclude = 'all', $list = '', $show = 'thumbnail') {
	
	$options[1] = array('title'=>'', 
						'items'=>$number,
						'show'=>$show ,
						'type'=>'random',
						'width'=>$width, 
						'height'=>$height, 
						'exclude'=>$exclude,
						'list'=>$list   );
	
	flagWidget::flag_widget_output($args = array(), 1, $options);
}

/**
 * flagDisplayRecentImages($number,$width,$height,$exclude,$list,$show)
 * Function for templates without widget support
 *
 * @return echo the widget content
 */
function flagDisplayRecentImages($number, $width = '75', $height = '50', $exclude = 'all', $list = '', $show = 'thumbnail') {

	$options[1] = array('title'=>'', 
						'items'=>$number,
						'show'=>$show ,
						'type'=>'recent',
						'width'=>$width, 
						'height'=>$height, 
						'exclude'=>$exclude,
						'list'=>$list   );
	
	flagWidget::flag_widget_output($args = array(), 1, $options);
}

?>