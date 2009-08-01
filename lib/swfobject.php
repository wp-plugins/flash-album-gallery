<?php
/**
 * Return a script for the flash slideshow. Can be used in any tmeplate with <?php echo flagShowFlashAlbum($galleryID, $width, $height) ?>
 * Require the script swfobject.js in the header or footer
 * 
 * @access public 
 * @param integer $galleryID ID of the gallery
 * @param integer $flashWidth Width of the flash container
 * @param integer $flashHeight Height of the flash container
 * @return the content
 */
function flagShowFlashAlbum($galleryID, $name, $width, $height) {
	
	if ( !class_exists('swfobject') ) :
	/**
	 * swfobject - PHP class for creating dynamic content of SWFObject V2.1
	 */
	class swfobject {
		/* id of the HTML element */
	    var $id;
		/* specifies the width of your SWF */
	    var $width;
		/* specifies the height of your SWF */
	    var $height;
		/* the javascript output */
	    var $js;
		/* the replacemnt message */
	    var $message = 'The <a href="http://www.macromedia.com/go/getflashplayer">Flash Player</a> and <a href="http://www.mozilla.com/firefox/">a browser with Javascript support</a> are needed..';			
		/* the classname for the div element */
	    var $classname = 'swfobject';			
		/* array of flashvars */
	    var $flashvars;
	  /* array of nested object element params */
	    var $params;
	  /* array of object's attributest */
	    var $attributes;

		/**
		 * swfobject::swfobject()
		 * 
		 * @param string $swfUrl (required) specifies the URL of your SWF
		 * @param string $id (required) specifies the id of the HTML element (containing your alternative content) you would like to have replaced by your Flash content
		 * @param string $width (required) specifies the width of your SWF
		 * @param string $height (required) specifies the height of your SWF
		 * @param string $version (required) specifies the Flash player version your SWF is published for (format is: "major.minor.release")
		 * @param string $expressInstallSwfurl (optional) specifies the URL of your express install SWF and activates Adobe express install
		 * @param array $flashvars (optional) specifies your flashvars with name:value pairs
		 * @param array $params (optional) specifies your nested object element params with name:value pair
		 * @param array $attributes (optional) specifies your object's attributes with name:value pairs
		 * @return string the content
		 */
		function swfobject( $swfUrl, $id, $width, $height, $version, $expressInstallSwfurl = false, $flashvars = false, $params = false, $attributes = false ) {
		
			global $swfCounter;
			
			// look for a other swfobject instance
			if ( !isset($swfCounter) )
				$swfCounter = 1;
			
			$this->id = $id . '_c' . $swfCounter;
			$this->width = $width;
			$this->height = $height;		
			
			$this->flashvars  = ( is_array($flashvars) )  ? $flashvars : array();
			$this->params     = ( is_array($params) )     ? $params : array();
			$this->attributes = ( is_array($attributes) ) ? $attributes : array();

			$this->embedSWF = 'swfobject.embedSWF("'. $swfUrl .'", "'. $this->id .'", "'. $width .'", "'. $height .'", "'. $version .'", '. $expressInstallSwfurl .', this.flashvars, this.params , this.attr );' . "\n";
			$this->embedSWF .= 'swfobject.createCSS("#'. $id .'","outline:none");' . "\n";
		}
		
		function output () {
			
			global $swfCounter;
			
			// count up if we have more than one swfobject
			$swfCounter++;
			
			$out  = "\n" . '<div class="'. $this->classname .'" id="'. $this->id  .'" style="width:'.$this->width .'px; height:'. $this->height .'px;">';
			$out .= "\n" . $this->message;
			$out .= "\n" . '</div>';
			
			return $out;
		}
		
		function javascript () {

			//Build javascript
			$this->js  = "\nvar " . $this->id  . " = {\n";
			$this->js .= $this->add_js_parameters('params', $this->params) . ",\n";
			$this->js .= $this->add_js_parameters('flashvars', $this->flashvars) . ",\n";
			$this->js .= $this->add_js_parameters('attr', $this->attributes) . ",\n";
			$this->js .= "\tstart : function() {" . "\n\t\t";
			$this->js .= $this->embedSWF;
			$this->js .= "\t}\n}\n";
			$this->js .= $this->id  . '.start();';
		
			return $this->js;
		}
		
		function add_flashvars ( $key, $value ) {
			$this->flashvars[$key] = $value;
			return;
		}

		function add_params ( $key, $value ) {
			$this->params[$key] = $value;
			return;
		}

		function add_attributes ( $key, $value ) {
			$this->attributes[$key] = $value;
			return;
		}
		
		function add_js_parameters( $name, $params ) {
			$list = '';
			if ( is_array($params) ) {
				foreach ($params as $key => $value) {
					if  ( !empty($list) )
						$list .= ",";	
					$list .= "\n\t\t" . $key . ' : ' . '"' . $value .'"';
				}
			}
			$js = "\t" . $name . ' : {' . $list . '}';		
			return $js;		
		}
		
	}
	endif;

	$flag_options = get_option('flag_options');

	if (empty($width) ) $width  = $flag_options['flashWidth'];
	if (empty($height)) $height = (int) $flag_options['flashHeight'];

	// init the flash output
	$swfobject = new swfobject( FLAG_URLPATH.'skins/'.$flag_options['flashSkin'].'/gallery.swf' , 'so' . $galleryID, $width, $height, '9.0.45', 'false');

	$swfobject->message = '<p>'. __('The <a href="http://www.macromedia.com/go/getflashplayer">Flash Player</a> and <a href="http://www.mozilla.com/firefox/">a browser with Javascript support</a> are needed..', 'flag').'</p>';
	$swfobject->add_params('wmode', 'transparent');
	$swfobject->add_params('allowfullscreen', 'true');
	$swfobject->add_params('menu', 'false');
	$swfobject->add_params('bgcolor', $flag_options['flashBackcolor'] );
	$swfobject->add_attributes('styleclass', 'flashalbum');
	$swfobject->add_attributes('id', 'so' . $galleryID);
	$swfobject->add_attributes('name', 'so' . $galleryID);

	// adding the flash parameter	
	$swfobject->add_flashvars( 'path', FLAG_URLPATH.'skins/'.$flag_options['flashSkin'].'/' );
	$swfobject->add_flashvars( 'gID', $galleryID );
	$swfobject->add_flashvars( 'galName', $name );
	$swfobject->add_flashvars( 'width', $width );
	$swfobject->add_flashvars( 'height', $height );	
	// add now the script code
   $out = "\n".'<script type="text/javascript" defer="defer">';
	$out .= "\n".'// <![CDATA[';
	$out .= $swfobject->javascript();
	$out .= "\n".'// ]]>';
	$out .= "\n".'</script>';
	// create the output
	$out .= '<div class="flashalbum">' . $swfobject->output() . '</div>';

	$out = apply_filters('flag_show_flash_content', $out);
			
	return $out;	
}

?>