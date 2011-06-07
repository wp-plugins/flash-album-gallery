<?php global $wpdb, $post;
if(!function_exists('flagGetUserNow')) {
	function flagGetUserNow($userAgent) {
	    $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
	    'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
	    'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby|yandex';
	    $isCrawler = (preg_match("/$crawlers/i", $userAgent) > 0);
	    return $isCrawler;
	}
}
$flag_options = get_option ('flag_options'); 
$siteurl = get_option ('siteurl');
$isCrawler = flagGetUserNow($_SERVER['HTTP_USER_AGENT']); // check if is a crowler
?>
<link rel="stylesheet" type="text/css" href="<?php echo $flag_options['skinsDirURL'].$skin; ?>/alternate/scripts/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript" src="<?php echo $flag_options['skinsDirURL'].$skin; ?>/alternate/scripts/jquery.fancybox-1.3.4.pack.js"></script>
<?php $bg = ($wmode == 'window')? '#'.$Background : 'transparent'; 
$bgBar = ($wmode == 'window')? '#'.$BarsBG : 'transparent'; ?>
<style type="text/css">
.flashalbum { clear: both; }

.afflux .flagcatlinks { padding: 7px 3px; margin:0 0 3px; background: #000000; }
.afflux .flagcatlinks a.flagcat { padding: 4px 10px; margin: 0; border: none; border-left: 1px dotted #75c30f; font: 14px Tahoma; text-decoration: none; background: none; color: #75c30f; }
.afflux .flagcatlinks a.flagcat:hover { text-decoration: none; background: none; border: none; border-left: 1px dotted #75c30f; color: #ffffff; }
.afflux .flagcatlinks a.flagcat:first-child { border: none; }
.afflux .flagcategory { width: 100%; height: auto; position: relative; text-align: center; }
.afflux .flagcategory a { display: inline-block; margin: 1px 0 0 1px; padding: 0; height: 100px; width: 115px; line-height: 96px; position:relative; overflow: hidden; text-align: center; z-index:99; cursor:pointer; background: #ffffff; border: 2px solid #ffffff; text-decoration: none; }
.afflux .flagcategory a:hover { background: #ffffff; border: 2px solid #ffffff; text-decoration: none; }
.afflux .flagcategory a img { vertical-align: middle; display:inline-block; position: static; margin: 0 auto; padding: 0; border: none; height: 100px !important; width: 115px !important; }

.afflux { background-color: <?php echo $bg; ?>; visibility: hidden; }
.afflux .flagcatlinks { background-color: #<?php echo $BarsBG; ?>; }
.afflux .flagcatlinks a.flagcat { border-color: #<?php echo $CatColor; ?>; color: #<?php echo $CatColor; ?>; background-color: #<?php echo $CatBGColor; ?>; }
.afflux .flagcatlinks a.flagcat:hover { border-color: #<?php echo $CatColor; ?>; }
.afflux .flagcatlinks a.active, .afflux .flagcatlinks a.flagcat:hover { color: #<?php echo $CatColorOver; ?>; background-color: #<?php echo $CatBGColorOver; ?>; outline: none; }
.afflux .jspContainer { background-color: <?php echo $bgBar; ?>; }
.afflux .flagcategory a { background-color: #<?php echo $ThumbBG; ?>; border: 2px solid #<?php echo $ThumbBG; ?>; background-image: url(<?php echo $flag_options['skinsDirURL'].$skin; ?>/alternate/scripts/images/loadingAnimation.gif); background-repeat: no-repeat; background-position: 50% 50%; font-size: 8px; color: #<?php echo $ThumbBG; ?>; }
.afflux .flagcategory a:hover { background-color: #<?php echo $ThumbBG; ?>; border: 2px solid #<?php echo $ThumbBG; ?>; color: #<?php echo $ThumbLoaderColor; ?>; }
.afflux .flagcategory a.current, .afflux .flagcategory a.last { border-color: #<?php echo $ThumbLoaderColor; ?>; }
#fancybox-title-over .title { color: #<?php echo $TitleColor; ?>; }
#fancybox-title-over .descr { color: #<?php echo $DescrColor; ?>; }
</style>
<script type="text/javascript">
//<![CDATA[
if (typeof jQuery == 'undefined') { 
   var head = document.getElementsByTagName("head")[0];
   script = document.createElement('script');
   script.id = 'jQuery';
   script.type = 'text/javascript';
   script.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js';
   head.appendChild(script);
}
function <?php echo $skinID; ?>_flag_e(t){
	jQuery(t).not('.loaded').each(function(){
		var d = jQuery(this).html();
		d = d.replace(/\[/g, '<');
		d = d.replace(/\]/g, ' />');
		jQuery(this).addClass('loaded').html(d);
		jQuery(t+" a").fancybox({
			'overlayShow'	: true,
			'overlayOpacity': '0.5',
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic',
			'titlePosition'	: 'over',
			'titleFormat'	: function(title, currentArray, currentIndex, currentOpts) {
				var descr = jQuery('img', currentArray[currentIndex]).attr("alt");
				title = jQuery('img', currentArray[currentIndex]).attr("title");
				return '<span id="fancybox-title-over"><em><?php _e("Image", "flag"); ?> '+(currentIndex + 1)+' / '+currentArray.length+' &nbsp; </em>'+(title.length? '<strong class="title">'+title+'</strong>' : '')+(descr.length? '<span class="descr">'+descr+'</span>' : '')+'</span>';
			},
			'onClosed' 		: function(currentArray, currentIndex){
				jQuery(currentArray[currentIndex]).removeClass('current').addClass('last');
			},
			'onComplete'	: function(currentArray, currentIndex) {
				jQuery(currentArray).removeClass('current last');
				jQuery(currentArray[currentIndex]).addClass('current');
			}
		});
	});
}

jQuery(document).ready(function() {
	jQuery('#<?php echo $skinID; ?>_jq').css('visibility','visible').find('.flagCatMeta').hide();
	jQuery('#<?php echo $skinID; ?>_jq .flagcategory:first').siblings('.flagcategory').hide();
	jQuery('#<?php echo $skinID; ?>_jq .flagCatMeta').each(function(i){
		var catName = jQuery('h4',this).text();
		var catDescr = jQuery('p',this).text();
		var catId = jQuery(this).next('.flagcategory').attr('id');
		var act = '';
		if(i==0) act = ' active'; 
		jQuery('#<?php echo $skinID; ?>_jq .flagcatlinks').append('<a class="flagcat'+act+'" href="#'+catId+'" title="'+catDescr+'">'+catName+'</a>')
	});
	jQuery('#<?php echo $skinID; ?>_jq .flagcat').click(function(){
		if(!jQuery(this).hasClass('active')) {
			var catId = jQuery(this).attr('href');
			jQuery(this).addClass('active').siblings().removeClass('active');
			jQuery('#<?php echo $skinID; ?>_jq '+catId).show().siblings('.flagcategory').hide();
			<?php echo $skinID; ?>_flag_e('#<?php echo $skinID; ?>_jq '+catId);
		}
		return false;
	});

	var fv = swfobject.getFlashPlayerVersion();
	if(fv.major<9){	
		<?php echo $skinID; ?>_flag_e('#<?php echo $skinID; ?>_jq .flagcategory:first');
	}

});

//]]>	
</script>
<div id="<?php echo $skinID; ?>_jq" class="afflux">
		<div class="flagcatlinks"></div>
<?php 
$gID = explode( '_', $galleryID ); // get the gallery id
if ( is_user_logged_in() ) $exclude_clause = '';
else $exclude_clause = ' AND exclude<>1 ';
foreach ( $gID as $galID ) {
	$galID = (int) $galID;
	if ( $galID == 0) {
		$thepictures = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE 1=1 {$exclude_clause} ORDER BY tt.{$flag_options['galSort']} {$flag_options['galSortDir']} ");
	} else {
		$thepictures = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE t.gid = '{$galID}' {$exclude_clause} ORDER BY tt.{$flag_options['galSort']} {$flag_options['galSortDir']} ");
	}
	$captions = '';
?>
	<?php if (is_array ($thepictures) && count($thepictures)){ ?>
		<div class="flagCatMeta">
			<h4><?php echo esc_attr(stripslashes($thepictures[0]->title));?></h4>
			<p><?php echo esc_attr(stripslashes($thepictures[0]->galdesc));?></p>
		</div>
		<div class="flagcategory" id="gid_<?php echo $galID; ?>">
			<?php $n = count($thepictures); 
				$var = floor($n/5);
				if($var==0 || $var > 4) $var=4;
				$split = ceil($n/$var);
				$j=0;
		if ($isCrawler){
			foreach ($thepictures as $picture) { ?><a class="i<?php echo $j++; ?>" href="<?php echo $siteurl.'/'.$picture->path.'/'.$picture->filename; ?>" rel="gid_<?php echo $galID; ?>"><img title="<?php echo esc_attr(stripslashes($picture->alttext)); ?>" alt="<?php echo esc_attr(stripslashes($picture->description)); ?>" src="<?php echo $siteurl.'/'.$picture->path.'/thumbs/thumbs_'.$picture->filename; ?>" width="115" height="100" /></a><?php 
			}
		} else {
			foreach ($thepictures as $picture) { ?><a class="i<?php echo $j++; ?>" href="<?php echo $siteurl.'/'.$picture->path.'/'.$picture->filename; ?>" data-id="<?php echo $picture->pid.'p'.$post->ID; ?>" rel="gid_<?php echo $galID; ?>">[img title="<?php echo esc_attr(stripslashes($picture->alttext)); ?>" alt="<?php echo esc_attr(stripslashes($picture->description)); ?>" src="<?php echo $siteurl.'/'.$picture->path.'/thumbs/thumbs_'.$picture->filename; ?>"]</a><?php 
			}
		} ?>
		</div> <!--flagcategory-->
	<?php } ?>
<?php } ?>
<!--<p style="text-align: right">powered by &copy; <a href="http://codeasily.com" target="_blank">CodEasily.com</a></p>-->
</div> 
