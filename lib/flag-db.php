<?php
if ( !class_exists('flagdb') ) :
/**
 * FlAG Gallery Database Class
 * 
 */
class flagdb {
	
	/**
	 * Holds the list of all galleries
	 *
     * @access public
     * @var object|array
	 */
	var $galleries = false;
	
    /**
     * Holds the list of all images
     *
     * @access public
     * @var object|array
     */
    var $images = false;
	
	/**
	 * PHP4 compatibility layer for calling the PHP5 constructor.
	 * 
	 */
	function flagdb() {
		return $this->__construct();
	}

	/**
	 * Init the Database Abstraction layer for FlAG Gallery
	 * 
	 */	
	function __construct() {
		global $wpdb;
		
		$this->galleries = array();
        $this->images    = array();
		
		register_shutdown_function(array(&$this, "__destruct"));
		
	}
	
	/**
	 * PHP5 style destructor and will run when database object is destroyed.
	 *
	 * @return bool Always true
	 */
	function __destruct() {
		return true;
	}	

	/**
	 * Get all the galleries
	 * 
	 * @param string $order_by
	 * @param string $order_dir
	 * @param bool $counter (optional) Select true  when you need to count the images
	 * @param int $limit number of paged galleries, 0 shows all galleries
	 * @param int $start the start index for paged galleries
	 * @return array $galleries
	 */
	function find_all_galleries($order_by = 'gid', $order_dir = 'ASC', $counter = false, $limit = 0, $start = 0) {		
		global $wpdb; 
		
		$order_dir = ( $order_dir == 'DESC') ? 'DESC' : 'ASC';
		if( $order_by == 'rand') $order_by = 'RAND()';
		$limit_by  = ( $limit > 0 ) ? 'LIMIT ' . intval($start) . ',' . intval($limit) : '';
		$this->galleries = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->flaggallery ORDER BY {$order_by} {$order_dir} {$limit_by}", OBJECT_K );
		
		// Count the number of galleries and calculate the pagination
		if ($limit > 0) {
			$this->paged['total_objects'] = intval ( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );
			$this->paged['objects_per_page'] = count( $this->galleries );
			$this->paged['max_objects_per_page'] = ( $limit > 0 ) ? ceil( $this->paged['total_objects'] / intval($limit)) : 1;
		}
		
		if ( !$this->galleries )
			return array();
		
        // get the galleries information    
        foreach ($this->galleries as $key => $value) {
            $galleriesID[] = $key;
            // init the counter values
            $this->galleries[$key]->counter = 0;
            wp_cache_add($key, $this->galleries[$key], 'flag_gallery');      
        }

		// if we didn't need to count the images then stop here
		if ( !$counter )
			return $this->galleries;
		
		// get the counter values 	
		$picturesCounter = $wpdb->get_results('SELECT galleryid, COUNT(*) as counter FROM '.$wpdb->flagpictures.' WHERE galleryid IN (\''.implode('\',\'', $galleriesID).'\') GROUP BY galleryid', OBJECT_K);

		if ( !$picturesCounter )
			return $this->galleries;
		
		// add the counter to the gallery objekt	
 		foreach ($picturesCounter as $key => $value) {
			$this->galleries[$value->galleryid]->counter = $value->counter;
            wp_cache_add($value->galleryid, $this->galleries[$value->galleryid], 'flag_gallery');
		}
		
		return $this->galleries;
	}
	
	/**
	 * Get a gallery given its ID
	 * 
	 * @param int|string $id or $name
	 * @return A flagGallery object (null if not found)
	 */
	function find_gallery( $id ) {		
		global $wpdb;
		
        if( is_numeric($id) ) {
            
            if ( $gallery = wp_cache_get($id, 'flag_gallery') )
                return $gallery;
            
			$gallery = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->flaggallery WHERE gid = %d", $id ) );

        } else
			$gallery = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->flaggallery WHERE name = %s", $id ) );
		
		// Build the object from the query result
        if ($gallery) {
            $gallery->abspath = WINABSPATH . $gallery->path;
            wp_cache_add($id, $gallery, 'flag_gallery');
            
            return $gallery;            
        } else 
			return false;
	}
	
	/**
	 * This function return all information about the gallery and the images inside
	 * 
	 * @param int|string $id or $name
	 * @param string $order_by 
	 * @param string $order_dir (ASC |DESC)
	 * @param int $limit number of paged galleries, 0 shows all galleries
	 * @param int $start the start index for paged galleries
	 * @return An array containing the flagImage objects representing the images in the gallery.
	 */
	function get_gallery($id, $order_by = 'sortorder', $order_dir = 'ASC', $limit = 0, $start = 0) {

		global $wpdb;

		// init the gallery as empty array
		$gallery = array();
		
		// Say no to any other value
		$order_dir = ( $order_dir == 'DESC') ? 'DESC' : 'ASC';
		$order_by  = ( empty($order_by) ) ? 'sortorder' : $order_by;
		
		// Should we limit this query ?
		$limit_by  = ( $limit > 0 ) ? 'LIMIT ' . intval($start) . ',' . intval($limit) : '';
		
		// Query database
		if( is_numeric($id) )
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS tt.*, t.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE t.gid = %d ORDER BY tt.{$order_by} {$order_dir} {$limit_by}", $id ), OBJECT_K );
		else
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS tt.*, t.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE t.name = %s ORDER BY tt.{$order_by} {$order_dir} {$limit_by}", $id ), OBJECT_K );

		// Build the object
		if ($result) {
				
			// Now added all image data
			foreach ($result as $key => $value)
				$gallery[$key] = new flagImage( $value );
		}
		
        // Could not add to cache, the structure is different to find_gallery() cache_add, need rework
        //wp_cache_add($id, $gallery, 'ngg_gallery');

		return $gallery;		
	}
	
	/**
	 * This function return all information about the gallery and the images inside
	 * 
	 * @param int|string $id or $name
	 * @param string $orderby 
	 * @param string $order (ASC |DESC)
	 * @return An array containing the flagImage objects representing the images in the gallery.
	 */
	function get_ids_from_gallery($id, $order_by = 'sortorder', $order_dir = 'ASC') {

		global $wpdb;
		
		// Say no to any other value
		$order_dir = ( $order_dir == 'DESC') ? 'DESC' : 'ASC';		
		$order_by  = ( empty($order_by) ) ? 'sortorder' : $order_by;
				
		// Query database
		if( is_numeric($id) )
			$result = $wpdb->get_col( $wpdb->prepare( "SELECT tt.pid FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE t.gid = %d ORDER BY tt.{$order_by} $order_dir", $id ) );
		else
			$result = $wpdb->get_col( $wpdb->prepare( "SELECT tt.pid FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE t.name = %s ORDER BY tt.{$order_by} $order_dir", $id ) );

		return $result;		
	}	
	/**
	 * Delete a gallery AND all the pictures associated to this gallery!
	 * 
	 * @gid The gallery ID
	 */
	function delete_gallery($gid) {		
		global $wpdb;
				
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->flagpictures WHERE galleryid = %d", $gid) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->flaggallery WHERE gid = %d", $gid) );
        
        wp_cache_delete($id, 'flag_gallery');

		//TODO:Remove all tag relationship
		return true;
	}

	/**
	 * Insert an image in the database
	 * 
	 * @return the ID of the inserted image
	 */
	function insert_image($gid, $filename, $alttext, $desc) {
		global $wpdb;
		
		$alttext = attribute_escape($alttext);
		$desc = attribute_escape($desc);
		
		$result = $wpdb->query(
			  "INSERT INTO $wpdb->flagpictures (galleryid, filename, description, alttext) VALUES "
			. "('$gid', '$filename', '$desc', '$alttext');");
		$pid = (int) $wpdb->insert_id;
        wp_cache_delete($gid, 'flag_gallery');
		
		return $pid;
	}

	/**
	 * flagdb::update_image() - Insert an image in the database
	 * 
	 * @param int $pid   id of the image
	 * @param (optional) string | int $galleryid
	 * @param (optional) string $filename
	 * @param (optional) string $description
	 * @param (optional) string $alttext
	 * @param (optional) int $sortorder
	 * @return bool result of the ID of the inserted image
	 */
	function update_image($pid, $galleryid = false, $filename = false, $description = false, $alttext = false, $sortorder = false) {

		global $wpdb;
		
		$sql = array();
		$pid = (int) $pid;
		
		$update = array(
		    'galleryid'   => $galleryid,
		    'filename' 	  => $filename,
		    'description' => $description,
		    'alttext' 	  => $alttext,
			'sortorder'   => $sortorder);
		
		// create the sql parameter "name = value"
		foreach ($update as $key => $value)
			if ($value)
				$sql[] = $key . " = '" . attribute_escape($value) . "'";
		
		// create the final string
		$sql = implode(', ', $sql);
		
		if ( !empty($sql) && $pid != 0)
			$result = $wpdb->query( "UPDATE $wpdb->flagpictures SET $sql WHERE pid = $pid" );

        wp_cache_delete($pid, 'flag_image'); 

		return $result;
	}
	
	/**
	 * Get an image given its ID
	 * 
	 * @param int $id The image ID
	 * @return object A flagImage object representing the image (false if not found)
	 */
	function find_image( $id ) {
		global $wpdb;
		
        if ( $image = wp_cache_get($id, 'flag_image') )
            return $image;
        
		// Query database
		$result = $wpdb->get_row( $wpdb->prepare( "SELECT tt.*, t.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE tt.pid = %d ", $id ) );
		
		// Build the object from the query result
		if ($result) {
			$image = new flagImage($result);
			return $image;
		} 
		
		return false;
	}
	
	/**
	 * Get images given a list of IDs 
	 * 
	 * @param $pids array of picture_ids
	 * @return An array of flagImage objects representing the images
	 */
	function find_images_in_list( $pids, $order = 'ASC' ) {
		global $wpdb;
	
		$result = array();
		
		// Check for the order setting
		$order_clause = ($order == 'RAND') ? 'ORDER BY rand() ' : ' ORDER BY t.pid ASC' ;
		
		if ( is_array($pids) ) {
			$id_list = "'" . implode("', '", $pids) . "'";
			
			// Save Query database
			$images = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flagpictures AS t INNER JOIN $wpdb->flaggallery AS tt ON t.galleryid = tt.gid WHERE t.pid IN ($id_list) $order_clause", OBJECT_K);
	
			// Build the image objects from the query result
			if ($images) {	
				foreach ($images as $key => $image)
					$result[$key] = new flagImage( $image );
			} 
		}
		return $result;
	}
	
	/**
	* Delete an image entry from the database
	*/
	function delete_image($pid) {
		global $wpdb;
		
		// Delete the image row
		$result = $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->flagpictures WHERE pid = %d", $pid) );
		
        // Remove from cache
        wp_cache_delete( $id, 'flag_image'); 
        
        return $result;
	}
	
	/**
	 * Get the last images registered in the database with a maximum number of $limit results
	 * 
	 * @param integer $page
	 * @param integer $limit
	 * @return
	 */
	function find_last_images($page = 0, $limit = 30, $galleryId = 0, $orderby = "id") {
		global $wpdb;
		
		$offset = (int) $page * $limit;
		
        $galleryId = (int) $galleryId;
        $gallery_clause = ($galleryId === 0) ? '' : ' AND galleryid = ' . $galleryId . ' ';

        // default order by pid
        $order = 'pid DESC';
        switch ($orderby) {
            case 'date':
                $order = 'imagedate DESC';
                break;
            case 'sort':
                $order = 'sortorder ASC';
                break;
        }

		$result = array();
		$gallery_cache = array();
		
		// Query database
		$images = $wpdb->get_results("SELECT * FROM $wpdb->flagpictures WHERE 1=1 $gallery_clause ORDER BY $order LIMIT $offset, $limit");
		
		// Build the object from the query result
		if ($images) {	
			foreach ($images as $key => $image) {
				
				// cache a gallery , so we didn't need to lookup twice
				if (!array_key_exists($image->galleryid, $gallery_cache))
					$gallery_cache[$image->galleryid] = flagdb::find_gallery($image->galleryid);
				
				// Join gallery information with picture information	
				foreach ($gallery_cache[$image->galleryid] as $index => $value)
					$image->$index = $value;
				
				// Now get the complete image data
				$result[$key] = new flagImage( $image );
			}
		}
		
		return $result;
	}
	
	/**
	 * flagdb::get_random_images() - Get an random image from one ore more gally
	 * 
	 * @param integer $number of images
	 * @param integer $galleryID optional a Gallery
	 * @return A flagImage object representing the image (null if not found)
	 */
	function get_random_images($number = 1, $galleryID = 0) {
		global $wpdb;
		
		$number = (int) $number;
		$galleryID = (int) $galleryID;
		$images = array();
		
		// Query database
		if ($galleryID == 0)
			$result = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid ORDER by rand() limit $number");
		else
			$result = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE t.gid = $galleryID ORDER by rand() limit {$number}");
		
		// Return the object from the query result
		if ($result) {
			foreach ($result as $image) {
				$images[] = new flagImage( $image );
			}
			return $images;
		} 
			
		return null;
	}

}
endif;

if ( ! isset($GLOBALS['flagdb']) ) {
    /**
     * Initate the FlAGallery Database Object, for later cache reasons
     * @global object $flagdb Creates a new flagdb object
     */
    unset($GLOBALS['flagdb']);
    $GLOBALS['flagdb'] =& new flagdb();
}
?>