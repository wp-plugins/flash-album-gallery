<?php
$meta = new flagMeta($image->pid);
$dbdata = $meta->get_saved_meta();
$exifdata = $meta->get_EXIF();
$iptcdata = $meta->get_IPTC();
$xmpdata = $meta->get_XMP();
$alttext = trim ( $meta->get_META('title') );		
$description = trim ( $meta->get_META('caption') );	
$timestamp = $meta->get_date_time();

$makedescription = '<strong>'.__('Meta Data','flag')."</strong>\n";
if ($dbdata) { 
			foreach ($dbdata as $key => $value){
				if ( is_array($value) ) continue;
					$makedescription .= '<strong>'.$meta->i8n_name($key)."</strong> ".$value."\n";
			}
} else {
			$makedescription .= __('No meta data saved','flag')."\n";
}
if ($exifdata) { 
			$makedescription .= "\n<strong>".__('EXIF Data','flag')."</strong>\n"; 
			foreach ($exifdata as $key => $value){
				$makedescription .= '<strong>'.$meta->i8n_name($key)."</strong> ".$value."\n";
			}
		}
if ($iptcdata) { 
			$makedescription .= "\n<strong>".__('IPTC Data','flag')."</strong>\n"; 
			foreach ($iptcdata as $key => $value){
				$makedescription .= '<strong>'.$meta->i8n_name($key)."</strong> ".$value."\n";
			}
}
if ($xmpdata) {  
			$makedescription .= "\n<strong>".__('XMP Data','flag')."</strong>\n"; 
			foreach ($xmpdata as $key => $value){
				$makedescription .= '<strong>'.$meta->i8n_name($key)."</strong> ".$value."\n";
			}
}
?>