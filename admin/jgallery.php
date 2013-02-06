<?php global $wpdb, $post;
$flag_options = get_option ('flag_options');
$siteurl = site_url();
$c = array();
$isCrawler = flagGetUserNow($_SERVER['HTTP_USER_AGENT']); // check if is a crowler
extract($altColors);
$bg = ($wmode == 'window')? '#'.$Background : 'transparent';
$xml['alt'] .= '<style type="text/css">'.PHP_EOL;
if(!$isCrawler) {
	$xml['alt'] .= '@import url("'.FLAG_URLPATH.'admin/css/flagallery_nocrawler.css");'.PHP_EOL;
}
$xml['alt'] .= '@import url("'.FLAG_URLPATH.'admin/css/flagallery_noflash.css");'.PHP_EOL;
if($isCrawler) {
	$xml['alt'] .= '.flag_alternate .flagCatMeta h4 { padding: 4px 10px; margin: 7px 0; border: none; font: 14px Tahoma; text-decoration: none; background:#292929 none; color: #ffffff; }
.flag_alternate .flagCatMeta p { font-size: 12px; }'.PHP_EOL;
}
if($BarsBG) {
	$bgBar = ($wmode == 'window')? '#'.$BarsBG : 'transparent';
	if(!$isCrawler){
		$xml['alt'] .= "#fancybox-title-over .title { color: #{$TitleColor}; }
#fancybox-title-over .descr { color: #{$DescrColor}; }
.flag_alternate .flagcatlinks { background-color: #{$BarsBG}; }
.flag_alternate .flagcatlinks a.flagcat, span.flag_pic_counters { color: #{$CatColor}; background-color: #{$CatBGColor}; }
.flag_alternate .flagcatlinks a.active, .flag_alternate .flagcatlinks a.flagcat:hover { color: #{$CatColorOver}; background-color: #{$CatBGColorOver}; }".PHP_EOL;
	}
	$xml['alt'] .= ".flag_alternate .flagcategory a.flag_pic_alt { background-color: #{$ThumbBG}; border: 2px solid #{$ThumbBG}; color: #{$ThumbBG}; }
.flag_alternate .flagcategory a.flag_pic_alt:hover { background-color: #{$ThumbBG}; border: 2px solid #{$ThumbLoaderColor}; color: #{$ThumbLoaderColor}; }
.flag_alternate .flagcategory a.flag_pic_alt.current, .flag_alternate .flagcategory a.flag_pic_alt.last { border-color: #{$ThumbLoaderColor}; }".PHP_EOL;
}
if($altColors['FullWindow'] && !$isCrawler){
	$xml['alt'] .= ".flagcatlinks a.backlink { color: #{$CatColor}; background-color: #{$CatBGColor}; }".PHP_EOL;
}
$xml['alt'] .= '</style>'.PHP_EOL;
if(!$isCrawler){
	if(!intval($flag_options['jAlterGalScript'])) {
		$xml['alt'] .= '<link href="'.plugins_url('/flash-album-gallery/admin/js/jquery.fancybox-1.3.4.css').'" rel="stylesheet" type="text/css" />'.PHP_EOL;
		$xml['alt'] .= "<script type='text/javascript' src='".plugins_url('/flash-album-gallery/admin/js/jquery.fancybox-1.3.4.pack.js')."'></script>".PHP_EOL;
		$xml['alt'] .= "<script type='text/javascript'>var ExtendVar='fancybox', hitajax = '".plugins_url('/lib/hitcounter.php', dirname(__FILE__))."';</script>".PHP_EOL;
	} else if(intval($flag_options['jAlterGalScript']) == 1) {
	$xml['alt'] .= "<style type='text/css'>@import url('".plugins_url('/admin/js/photoswipe/photoswipe.css', dirname(__FILE__))."');</style>
<script type='text/javascript' src='".plugins_url('/admin/js/photoswipe/klass.min.js', dirname(__FILE__))."'></script>
<script type='text/javascript' src='".plugins_url('/admin/js/photoswipe/code.photoswipe.jquery-3.0.5.min.js', dirname(__FILE__))."'></script>
<script type='text/javascript'>var ExtendVar='photoswipe', hitajax = '".plugins_url('/lib/hitcounter.php', dirname(__FILE__))."';</script>".PHP_EOL;
	}
 }

$xml['alt'] .= '<div id="'.$skinID.'_jq" class="flag_alternate">
		<div class="flagcatlinks">';
			if($altColors['FullWindow'] && !$isCrawler){
				$flag_custom = get_post_custom($post->ID);
				$backlink = $flag_custom["mb_button_link"][0];
				if(!$backlink || $backlink == 'http://'){ $backlink = $_SERVER["HTTP_REFERER"]; }
				if($backlink){
					$xml['alt'] .= '<a id="backlink" class="backlink" href="'.$backlink.'">'.$flag_custom["mb_button"][0].'</a>';
				}
			}
		$xml['alt'] .= '</div>'.PHP_EOL;

$gID = explode( '_', $galleryID ); // get the gallery id
if ( is_user_logged_in() ) $exclude_clause = '';
else $exclude_clause = ' AND exclude<>1 ';
$i = 0;
foreach ( $gID as $galID ) {
	$galID = (int) $galID;
	if ( $galID == 0) {
		$thegalleries = array();
		$thepictures = $wpdb->get_results("SELECT pid, galleryid, filename, description, alttext, imagedate, sortorder, hitcounter, total_value, total_votes FROM $wpdb->flagpictures WHERE 1=1 {$exclude_clause} ORDER BY {$flag_options['galSort']} {$flag_options['galSortDir']} ", ARRAY_A);
	} else {
		$thegalleries = $wpdb->get_row("SELECT gid, name, path, title, galdesc FROM $wpdb->flaggallery WHERE gid={$galID}", ARRAY_A);
		$thepictures = $wpdb->get_results("SELECT pid, filename, description, alttext, imagedate, hitcounter, total_value, total_votes FROM $wpdb->flagpictures WHERE galleryid = '{$galID}' {$exclude_clause} ORDER BY {$flag_options['galSort']} {$flag_options['galSortDir']} ", ARRAY_A);
	}
	$captions = '';


	if (is_array ($thepictures) && count($thegalleries) && count($thepictures)){
		$thegalleries = array_map('stripslashes', $thegalleries);
		$thegalleries['galdesc'] = htmlspecialchars_decode($thegalleries['galdesc']);
		$a = $thegalleries;

		$xml['alt'] .= '<div class="flagCatMeta">'.PHP_EOL;
		$xml['alt'] .= '	<h4>'.$thegalleries['title'].'</h4>'.PHP_EOL;
		$xml['alt'] .= '	<p>'.$thegalleries['galdesc'].'</p>'.PHP_EOL;
		$xml['alt'] .= '</div>'.PHP_EOL;
		$xml['alt'] .= '<div class="flagcategory" id="gid_'.$galID.'_'.$skinID.'">'.PHP_EOL;
			$n = count($thepictures);
			$var = floor($n/5);
			if($var==0 || $var > 4) $var=4;
			$split = ceil($n/$var);
			$j=0;
			$b = array();
		foreach ($thepictures as $picture) {
			$picture = array_map('stripslashes', $picture);
			$b['data'][] = $picture;

			$pid = intval($picture['pid']);

			if ($isCrawler){
				$xml['alt'] .= '<a style="display:block; overflow: hidden; height: 100px; width: 115px; margin-bottom: 10px; background-color: #eeeeee; background-position: 22px 44px; text-align: left;" class="i'. $j++ .' flag_pic_alt" href="'.$siteurl.'/'.$thegalleries['path'].'/'.$picture['filename'].'" id="flag_pic_'.$pid.'" rel="gid_'.$galID.'_'.$skinID.'"><img style="float:left; margin-right: 10px; width: auto; height: auto; min-height:100px; min-width:115px;" title="'.strip_tags($picture['alttext']).'" alt="'.strip_tags($picture['alttext']).'" src="'.$siteurl.'/'.$thegalleries['path'].'/thumbs/thumbs_'.$picture['filename'].'" /><span style="display: block; overflow: hidden; text-decoration: none; color: #000; font-weight: normal;" class="flag_pic_desc" id="flag_desc_'.$pid.'"><strong>'.strip_tags($picture['alttext']).'</strong><br />'.strip_tags($picture['description'],'<b><u><i><span>').'</span></a>'.PHP_EOL;
			} else {
				$views = (intval($picture['hitcounter']) < 10000) ? $picture['hitcounter'] : round($picture['hitcounter']/1000, 1).'k';
				$likes = (intval($picture['total_votes']) < 10000) ? $picture['total_votes'] : round($picture['total_votes']/1000, 1).'k';
				$xml['alt'] .= '<a class="i'. $j++ .' flag_pic_alt" href="'.$siteurl.'/'.$thegalleries['path'].'/'.$picture['filename'].'" id="flag_pic_'.$pid.'" rel="gid_'.$galID.'_'.$skinID.'" title="'.strip_tags($picture['alttext']).'">[img src='.$siteurl.'/'.$thegalleries['path'].'/thumbs/thumbs_'.$picture['filename'].']<span class="flag_pic_counters"><i>'.$views.'</i><b>'.$picture['total_votes'].'</b></span><span class="flag_pic_desc" id="flag_desc_'.$pid.'"><strong>'.htmlspecialchars($picture['alttext']).'</strong><br /><span>'.htmlspecialchars($picture['description']).'</span></span></a>'.PHP_EOL;
			}
		}
		$xml['alt'] .= '</div>'.PHP_EOL;
		$c['galleries'][] = $a + $b;
	}
}
$xml['alt'] .= '</div>'.PHP_EOL;
$d = array('properties'=>$data) + $c;
$xml['json'] = json_encode($d);
?>