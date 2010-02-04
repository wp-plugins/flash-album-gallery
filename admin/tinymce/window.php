<?php

// look up for the path
require_once( dirname( dirname( dirname(__FILE__) ) ) . '/flag-config.php');
require_once (dirname( dirname(__FILE__) ) . '/get_skin.php');

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));

global $wpdb;

$all_skins = get_skins();

if($_REQUEST['riched'] == "false") {
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e("Insert Flash Album with one or more galleries", 'flag'); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo FLAG_URLPATH ?>admin/tinymce/popup.css" />
<base target="_self" />
</head>
<body id="link">
<?php } else { ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e("Insert Flash Album with one or more galleries", 'flag'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo FLAG_URLPATH ?>admin/tinymce/tinymce.js"></script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('galleries').focus();" style="display: none">
<?php } ?>
<form name="FlAG" action="#">
	<div class="tabs">
		<ul>
			<li id="gallery_tab" class="current"><span><a href="javascript:mcTabs.displayTab('gallery_tab','gallery_panel');" onmousedown="return false;"><?php _e( 'Galleries', 'flag' ) ?></a></span></li>
			<li id="skin_tab"><span><a href="javascript:mcTabs.displayTab('skin_tab','skin_panel');" onmousedown="return false;"><?php _e( 'Skin', 'flag' ) ?></a></span></li>
			<li id="sort_tab"><span><a href="javascript:mcTabs.displayTab('sort_tab','sort_panel');" onmousedown="return false;"><?php _e('Sort', 'nggallery'); ?></a></span></li>
		</ul>
	</div>
	
	<div class="panel_wrapper" style="border:1px solid #919B9C; height:130px;">
		<!-- gallery panel -->
		<div id="gallery_panel" class="panel current">
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap" valign="middle"><label for="galleryname"><?php _e("Album Name", 'flag'); ?>:<span style="color:red;"> *</span></label></td>
            <td valign="middle"><input id="galleryname" name="galleryname" value="Gallery" type="text" style="width: 200px" /></td>
         </tr>
         <tr>
            <td nowrap="nowrap" valign="top"><label for="gallerytag"><?php _e("Select galleries", 'flag'); ?>:<span style="color:red;"> *</span></label><br /><small><?php _e("(album categories)", 'flag'); ?></small></td>
            <td><select id="galleries" name="galleries" style="width: 200px" size="6" multiple="multiple">
                    <option value="all" selected="selected" onclick="javascript:document.getElementById('sort_tab').style.display='block'" style="font-weight:bold">* - all galleries</option>
				<?php
					$gallerylist = $flagdb->find_all_galleries('gid', 'ASC');
					if(is_array($gallerylist)) {
						foreach($gallerylist as $gallery) {
							$name = ( empty($gallery->title) ) ? $gallery->name : $gallery->title;
							echo '<option value="' . $gallery->gid . '" >' . $gallery->gid . ' - ' . $name . '</option>' . "\n";
						}
					}
				?>
            </select></td>
         </tr>
         <tr>
            <td nowrap="nowrap" valign="middle">
<script type="text/javascript">
/* <![CDATA[ */
function setVisibility(){
  document.getElementById('gallerysize').style.visibility = (document.getElementById('gallerycustomsize').checked) ? 'visible':'hidden';
}
jQuery('#galleries').change(function(){
	jQuery('#sort_tab').hide();
    if(jQuery('#galleries option[value=all]:selected')) {
        jQuery('#galleries option[value=all]:selected').siblings().removeAttr('selected');
    }
});
/* ]]> */
</script>
				<input id="gallerycustomsize" name="gallerycustomsize" type="checkbox" style="vertical-align:middle;" onclick="setVisibility()" /> <label for="gallerycustomsize" onclick="setVisibility()"><?php _e("custom size", 'flag'); ?></label></td>
            <td valign="middle"><div id="gallerysize" style="visibility:hidden;">width: <input id="gallerywidth" type="text" name="galleryheight" style="width: 50px" /> &nbsp; height: <input id="galleryheight" type="text" name="galleryheight" style="width: 50px" /></div></td>
         </tr>
        </table>
		</div>
		<!-- /gallery panel -->
		<!-- skin panel -->
		<div id="skin_panel" class="panel">
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap" valign="middle"><label for="skinname"><?php _e("Choose skin", 'flag'); ?>:</label></td>
            <td valign="middle"><select id="skinname" name="skinname" style="width: 200px">
                    <option value="" selected="selected">choose custom skin</option>
<?php
	foreach ( (array)$all_skins as $skin_file => $skin_data) {
		echo '<option value="'.dirname($skin_file).'">'.$skin_data['Name'].'</option>'."\n";
	}
?>
            </select></td>
         </tr>
        </table>
		</div>
		<!-- /skin panel -->
		<!-- sort panel -->
		<div id="sort_panel" class="panel">
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap" valign="middle"><label for="galorderby"><?php _e("Order by", 'flag'); ?>:</label></td>
            <td valign="middle"><select id="galorderby" name="galorderby" style="width: 200px">
                    <option value="" selected="selected">Gallery IDs (default)</option>
                    <option value="title">Gallery Title</option>
                    <!-- <option value="sortorder">User Defined</option> -->
                    <option value="rand">Randomly</option>
            </select></td>
         </tr>
         <tr>
            <td nowrap="nowrap" valign="middle"><label for="galorder"><?php _e("Order", 'flag'); ?>:</label></td>
            <td valign="middle"><select id="galorder" name="galorder" style="width: 200px">
                    <option value="" selected="selected">DESC (default)</option>
                    <option value="ASC">ASC</option>
            </select></td>
         </tr>
         <tr>
            <td nowrap="nowrap" valign="middle"><label for="galexclude"><?php _e("Exclude Gallery", 'flag'); ?>:</label></td>
            <td valign="middle"><input id="galexclude" name="galexclude" type="text" style="width: 200px" /></td>
         </tr>
       </table>
		</div>
		<!-- /sort panel -->
	</div>

<?php if($_REQUEST['riched'] == "false") { ?>
	<div class="mceActionPanel">
		<div style="float: right">
			<input type="button" id="insert" name="insert" value="<?php _e("Insert", 'flag'); ?>" />
		</div>
	</div>
	<script type="text/javascript">
		/* <![CDATA[ */
		var win = window.dialogArguments || opener || parent || top;
		jQuery('#insert').click(function(){
			var tagtext;
			var galleryname = document.getElementById('galleryname').value;
			var gallerywidth = document.getElementById('gallerywidth').value;
			var galleryheight = document.getElementById('galleryheight').value;
			var galorderby = document.getElementById('galorderby').value;
			var galorder = document.getElementById('galorder').value;
			var galexclude = document.getElementById('galexclude').value;
			var skinname = document.getElementById('skinname').value;
			var gallery = document.getElementById('galleries');
			var len = gallery.length;
			var galleryid="";
			for(i=0;i<len;i++)
			{
				if(gallery.options[i].selected) {
					if(galleryid=="") {
						galleryid = galleryid + gallery.options[i].value;
					} else {
						galleryid = galleryid + "," + gallery.options[i].value;
					}
				}
			}
			if (gallerywidth && galleryheight)
				var gallerysize = " w=" + gallerywidth + " h=" + galleryheight;
			else
				var gallerysize="";
			
			if (galleryid == 'all') {
				if (galorderby) {
					var galorderby = " orderby=" + galorderby;
				} 
				if (galorder) {
					var galorder = " order=" + galorder;
				}
				if (galexclude) {
					var galexclude = " exclude=" + galexclude;
				} 
			} else {
				var galorderby = '';
				var galorder = '';
				var galexclude = '';
			}
			if (skinname) {
				var skinname = " skin=" + skinname;
			} else var skinname = '';

			if (galleryid != 0 ) {
				tagtext = '[flagallery gid=' + galleryid + ' name="' + galleryname + '"' + gallerysize + galorderby + galorder + galexclude + skinname + ']';
				win.send_to_editor(tagtext);
				win.bind_resize();
			} else alert('Choose at least one gallery!');
		});
		jQuery(window).unload(function(){
			win.bind_resize();
		});
		/* ]]> */
	</script>
<?php } else { ?>
	<div class="mceActionPanel">
		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'flag'); ?>" onclick="insertFLAGLink();" />
		</div>
	</div>
<?php } ?>
</form>
</body>
</html>
