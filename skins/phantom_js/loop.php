<?php
$skinID = mt_rand();
$siteurl = site_url();
$skinurl = plugins_url('/flagallery-skins/'.$skin);
$js = "var flagallery_phantom_ID{$skinID}_Settings = {";
$a = array();
$a[] = "'width': '" . $size[0] . "'";
$a[] = "'height': '" . $size[1] . "'";
$a[] = "'responsiveEnabled': '{$data->responsiveEnabled}'";
$a[] = "'thumbsNavigation': '{$data->thumbsNavigation}'";
$a[] = "'thumbCols': " . intval( $data->thumbCols );
$a[] = "'thumbRows': " . intval( $data->thumbRows );
$a[] = "'bgColor': '".substr($data->bgColor, 2)."'";
$a[] = "'bgAlpha': " . intval( $data->bgAlpha );

$a[] = "'thumbWidth': " . intval( $data->thumbWidth );
$a[] = "'thumbHeight': " . intval( $data->thumbHeight );
$a[] = "'thumbsSpacing': " . intval( $data->thumbsSpacing );
$a[] = "'thumbsPaddingTop': " . intval( $data->thumbsPaddingTop );
$a[] = "'thumbsPaddingRight': " . intval( $data->thumbsPaddingRight );
$a[] = "'thumbsPaddingBottom': " . intval( $data->thumbsPaddingBottom );
$a[] = "'thumbsPaddingLeft': " . intval( $data->thumbsPaddingLeft );

$a[] = "'thumbLoader': '{$skinurl}/img/ThumbnailLoader.gif'";
$a[] = "'thumbAlpha': " . intval( $data->thumbAlpha );
$a[] = "'thumbAlphaHover': " . intval( $data->thumbAlphaHover );
$a[] = "'thumbBgColor': '".substr($data->thumbBgColor, 2)."'";
$a[] = "'thumbBgColorHover': '".substr($data->thumbBgColorHover, 2)."'";
$a[] = "'thumbBorderSize': " . intval( $data->thumbBorderSize );
$a[] = "'thumbBorderColor': '".substr($data->thumbBorderColor, 2)."'";
$a[] = "'thumbBorderColorHover': '".substr($data->thumbBorderColorHover, 2)."'";
$a[] = "'thumbPaddingTop': " . intval( $data->thumbPaddingTop );
$a[] = "'thumbPaddingRight': " . intval( $data->thumbPaddingRight );
$a[] = "'thumbPaddingBottom': " . intval( $data->thumbPaddingBottom );
$a[] = "'thumbPaddingLeft': " . intval( $data->thumbPaddingLeft );

$a[] = "'thumbsInfo': '{$data->thumbsInfo}'";

$a[] = "'tooltipBgColor': '".substr($data->tooltipBgColor, 2)."'";
$a[] = "'tooltipStrokeColor': '".substr($data->tooltipStrokeColor, 2)."'";
$a[] = "'tooltipTextColor': '".substr($data->tooltipTextColor, 2)."'";

$a[] = "'labelPosition': '{$data->labelPosition}'";
$a[] = "'labelTextColor': '".substr($data->labelTextColor, 2)."'";
$a[] = "'labelTextColorHover': '".substr($data->labelTextColorHover, 2)."'";

$a[] = "'lightboxPosition': '{$data->lightboxPosition}'";
$a[] = "'lightboxWindowColor': '".substr($data->lightboxWindowColor, 2)."'";
$a[] = "'lightboxWindowAlpha': " . intval( $data->lightboxWindowAlpha );
$a[] = "'lightboxLoader': '{$skinurl}/img/LightboxLoader.gif'";
$a[] = "'lightboxBgColor': '".substr($data->lightboxBgColor, 2)."'";
$a[] = "'lightboxBgAlpha': " . intval( $data->lightboxBgAlpha );
$a[] = "'lightboxMarginTop': " . intval( $data->lightboxMarginTop );
$a[] = "'lightboxMarginRight': " . intval( $data->lightboxMarginRight );
$a[] = "'lightboxMarginBottom': " . intval( $data->lightboxMarginBottom );
$a[] = "'lightboxMarginLeft': " . intval( $data->lightboxMarginLeft );
$a[] = "'lightboxPaddingTop': " . intval( $data->lightboxPaddingTop );
$a[] = "'lightboxPaddingRight': " . intval( $data->lightboxPaddingRight );
$a[] = "'lightboxPaddingBottom': " . intval( $data->lightboxPaddingBottom );
$a[] = "'lightboxPaddingLeft': " . intval( $data->lightboxPaddingLeft );

$a[] = "'lightboxNavPrev': '{$skinurl}/img/LightboxPrev.png'";
$a[] = "'lightboxNavPrevHover': '{$skinurl}/img/lightboxPrevHover.png'";
$a[] = "'lightboxNavNext': '{$skinurl}/img/lightboxNext.png'";
$a[] = "'lightboxNavNextHover': '{$skinurl}/img/lightboxNextHover.png'";
$a[] = "'lightboxNavClose': '{$skinurl}/img/lightboxClose.png'";
$a[] = "'lightboxNavCloseHover': '{$skinurl}/img/lightboxCloseHover.png'";

$a[] = "'captionHeight': " . intval( $data->captionHeight );
$a[] = "'captionTitleColor': '".substr($data->captionTitleColor, 2)."'";
$a[] = "'captionTextColor': '".substr($data->captionTextColor, 2)."'";

$a[] = "'socialShareEnabled': '{$data->socialShareEnabled}'";
$a[] = "'socialShareLightbox': '{$skinurl}/img/socialShareLightbox.png'";

$js .= implode( ",\n", $a );
$js .= "},";

$js .= "flagallery_phantom_ID{$skinID}_Content = [";
$a = array();
foreach ( $thepictures as $picture ) {
	$picture = array_map( 'stripslashes', $picture );
	$a[] = "{'image': '{$siteurl}/{$thegalleries['path']}/{$picture['filename']}','thumb': '{$siteurl}/{$thegalleries['path']}/thumbs/thumbs_{$picture['filename']}','captionTitle': " . json_encode( strip_tags( $picture['alttext'] ) ) . ",'captionText': " . json_encode(wpautop( $picture['description']  )) . ",'link': '{$picture['link']}','linkTarget': ''}";
}
$js .= implode( ",\n", $a );
$js .= "];";

$js .= "jQuery('#flagallery_phantom_ID{$skinID}').flagallery_phantom();";

$out = '<div id="flagallery_phantom_ID'.$skinID.'" class="flagallery_phantom"></div><script type="text/javascript">'.$js.'</script>';
