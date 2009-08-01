<?php

// look up for the path
require_once( dirname( dirname( dirname(__FILE__) ) ) . '/flag-config.php');

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));

global $wpdb;

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e("Insert Flash Album with one or more galleries", 'flag'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo FLAG_URLPATH ?>admin/tinymce/tinymce.js"></script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('galleries').focus();" style="display: none">
<form name="FlAG" action="#">
	<div class="panel_wrapper" style="border:1px solid #919B9C; height:150px;">
		<!-- gallery panel -->
		<div id="gallery_panel" class="panel current">
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap" valign="middle"><label for="galleryname"><?php _e("Album Name", 'flag'); ?>:<span style="color:red;"> *</span></label></td>
            <td valign="middle"><input id="galleryname" name="galleryname" value="Gallery" type="text" style="width: 200px" /></td>
         </tr>
         <tr>
            <td nowrap="nowrap" valign="top"><label for="gallerytag"><?php _e("Select galleries", 'flag'); ?>:<span style="color:red;"> *</span></label><br /><small><?php _e("(album categories)", 'flag'); ?></small></td>
            <td><select id="galleries" name="galleries" style="width: 200px" size="7" multiple="multiple">
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
/* ]]> */
</script>
				<input id="gallerycustomsize" name="gallerycustomsize" type="checkbox" style="vertical-align:middle;" onclick="setVisibility()" /> <label for="gallerycustomsize" onclick="setVisibility()"><?php _e("custom size", 'flag'); ?></label></td>
            <td valign="middle"><div id="gallerysize" style="visibility:hidden;">width: <input id="gallerywidth" type="text" name="galleryheight" style="width: 50px" /> &nbsp; height: <input id="galleryheight" type="text" name="galleryheight" style="width: 50px" /></div></td>
         </tr>
        </table>
		</div>
		<!-- gallery panel -->
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'flag'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'flag'); ?>" onclick="insertFLAGLink();" />
		</div>
	</div>
</form>
</body>
</html>
