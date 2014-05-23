<?php
if ( !class_exists('flag_swfobject') ) :
/**
 * flag_swfobject - PHP class for creating dynamic content of SWFObject V2.1
 */
class flag_swfobject {
	
	var $id;									// id of the HTML element
	var $width;									// specifies the width of your SWF 
	var $height; 								// specifies the height of your SWF 
	var $skin;									// specifies the skin of your SWF 
	var $js;									// the javascript output 
	var $classname = 'flagallery_swfobject';	// the classname for the div element 
	var $flashvars;								// array of flashvars 
	var $params;								// array of nested object element params 
	var $attributes;							// array of object's attributest 

	/**
	 * flag_swfobject::flag_swfobject()
	 *
	 * @param string      $swfUrl               (required) specifies the URL of your SWF
	 * @param string      $id                   (required) specifies the id of the HTML element (containing your alternative content) you would like to have replaced by your Flash content
	 * @param string      $width                (required) specifies the width of your SWF
	 * @param string      $height               (required) specifies the height of your SWF
	 * @param string      $version              (required) specifies the Flash player version your SWF is published for (format is: "major.minor.release")
	 * @param bool|string $expressInstallSwfurl (optional) specifies the URL of your express install SWF and activates Adobe express install
	 * @param array|bool  $flashvars            (optional) specifies your flashvars with name:value pairs
	 * @param array|bool  $params               (optional) specifies your nested object element params with name:value pair
	 * @param array|bool  $attributes           (optional) specifies your object's attributes with name:value pairs
	 * @param bool $gallery
	 *
	 * @return string the content
	 */
	function flag_swfobject($swfUrl, $id, $width, $height, $version, $expressInstallSwfurl = false, $flashvars = false, $params = false, $attributes = false, $gallery = false){

		global $swfCounter;
		
		// look for a other swfobject instance
		if ( !isset($swfCounter) )
			$swfCounter = 1;

		$this->id = $id . '_div';
		$this->width = $width;
		$this->height = $height;

		$this->flashvars  = ( is_array($flashvars) )  ? $flashvars : array();
		$this->params     = ( is_array($params) )     ? $params : array();
		$this->attributes = ( is_array($attributes) ) ? $attributes : array();

		$this->embedSWF = 'if(jQuery.isFunction(swfobject.switchOffAutoHideShow)){ swfobject.switchOffAutoHideShow(); }';
		$this->embedSWF .= 'swfobject.embedSWF("'. $swfUrl .'", "'. $this->id .'", "'. $width .'", "'. $height .'", "'. $version .'", "'. $expressInstallSwfurl .'", this.flashvars, this.params , this.attr );';
		if($gallery){
			$this->embedSWF .= 'swfobject.createCSS("#' . $id . '","outline:none;width:100%;height:100%;");';
		} else{
			$width_css = strpos($width, '%')? '' : 'width:' . $width . 'px;';
			$height_css = strpos($height, '%')? '' : 'height:' . $height . 'px;';
			$this->embedSWF .= 'swfobject.createCSS("#' . $id . '","outline:none;' . $height_css . $width_css . '");';
		}
	}
	
	function output ($alternate = '') {
		
		global $swfCounter;
		
		if(!$alternate) {
			$alternate = __('The <a href="http://www.macromedia.com/go/getflashplayer">Flash Player</a> and a browser with Javascript support are needed.', 'flag');
		}
		// count up if we have more than one swfobject
		$swfCounter++;
		$out  = '<div class="'. $this->classname .'" id="'. $this->id  .'">';
		$out .= $alternate;
		$out .= '</div>';
		
		return $out;
	}
	
	function javascript () {

		//Build javascript
		$this->js  = "var " . $this->id  . " = {";
		$this->js .= $this->add_js_parameters('params', $this->params) . ",";
		$this->js .= $this->add_js_parameters('flashvars', $this->flashvars) . ",";
		$this->js .= $this->add_js_parameters('attr', $this->attributes) . ",";
		$this->js .= "start : function() {";
		$this->js .= $this->embedSWF;
		$this->js .= "} };";

		$this->js .= 'jQuery(function(){';
		$this->js .= $this->id  . '.start();';
		$this->js .= "});";

		return $this->js;
	}
	
	function add_flashvars ( $key, $value, $default = '', $type = '', $prefix = '' ) {
		if ( is_bool( $value ) )
			$value = ( $value ) ? "true" : "false";
		if ( $type == "bool" )
			$value = ( $value == "1" ) ? "true" : "false";
		// do not add the variable if we hit the default setting 	
		if ( $value == $default )	
			return;
		$this->flashvars[$key] = $prefix . $value;
		return;
	}

	function add_params ( $key, $value, $default = '', $type = '', $prefix = '' ) {
		if ( is_bool( $value ) )
			$value = ( $value ) ? "true" : "false";
		if ( $type == "bool" )
			$value = ( $value == "1" ) ? "true" : "false";
		// do not add the variable if we hit the default setting 	
		if ( $value == $default )	
			return;
		$this->params[$key] = $prefix . $value;
		return;
	}

	function add_attributes ( $key, $value, $default = '', $type = '', $prefix = '' ) {
		if ( is_bool( $value ) )
			$value = ( $value ) ? "true" : "false";
		if ( $type == "bool" )
			$value = ( $value == "1" ) ? "true" : "false";
		// do not add the variable if we hit the default setting 	
		if ( $value == $default )	
			return;
		$this->attributes[$key] = $prefix . $value;
		return;
	}
	
	function add_js_parameters( $name, $params ) {
		$list = '';
		if ( is_array($params) ) {
			foreach ($params as $key => $value) {
				if  ( !empty($list) )
					$list .= ",";	
				$list .= $key . ": " . "'" . $value ."'";
			}
		}
		$js = $name . ': {' . $list . '}';
		return $js;		
	}
	
}
endif;
