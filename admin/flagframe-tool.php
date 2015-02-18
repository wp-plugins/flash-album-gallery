<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

// check for correct capability
if ( !is_user_logged_in() )
	die('-1');

// check for correct FlAG capability
if ( !current_user_can('FlAG iFrame page') )
	die('-1');	

if(isset($_POST['copy_file'])) {
	if(copy(FLAG_ABSPATH.'flagframe.php',ABSPATH.'flagframe.php')) {
		flagGallery::show_message(__('Success','flag'));
	} else {
		flagGallery::show_error(__('Failure','flag'));
	}
}
global $flag, $flagdb;
require_once (dirname(__FILE__) . '/get_skin.php');
require_once (dirname(__FILE__) . '/playlist.functions.php');
require_once (dirname(__FILE__) . '/video.functions.php');
require_once (dirname(__FILE__) . '/banner.functions.php');
$i_skins = get_skins();
$all_m_playlists = get_playlists();
$all_v_playlists = get_v_playlists();
$all_b_playlists = get_b_playlists();
$fb_url = plugins_url().'/flash-album-gallery/flagframe.php';
if(file_exists(ABSPATH.'flagframe.php')) {
	$fb_url = home_url().'/flagframe.php';
}
?>
<script type="text/javascript">/*<![CDATA[*/
var url = '<?php echo $fb_url; ?>';
jQuery(document).ready(function() {
	jQuery('#galleries input[value="all"]').attr('checked','checked').parent().siblings('.row').find('input').removeAttr('checked');
	jQuery('#items_array').val('all');
	var galleries = '?i='+jQuery('#items_array').val().split(',').join('_');
	var skin = jQuery('#skinname option:selected').val();
	if(skin) skin = '&f='+skin; else skin = '';
	var h = parseInt(jQuery('#galleryheight').val());
	if(h) h = '&h='+h; else h = '';
	var l = parseInt(jQuery('#postid').val());
	if(l) l = '&l='+l; else l = '';
	fb_url(galleries,skin,h,l);
	jQuery('#galleries :checkbox').click(function(){
		var cur, arr, del;
		if(jQuery(this).is(':checked')){
			cur = jQuery(this).val();
			if(cur == 'all') {
				jQuery(this).parent().siblings('.row').find('input').removeAttr('checked');
				jQuery('#items_array').val(cur);
			} else {
				jQuery('#galleries input[value="all"]').removeAttr('checked');
				arr = jQuery('#items_array').val();
				if(arr && arr != 'all') { del = ','; } else { arr = ''; del = ''; }
				jQuery('#items_array').val(arr+del+cur);
			}
		} else {
			cur = jQuery(this).val();
			arr = jQuery('#items_array').val().split(',');
			arr = jQuery.grep(arr, function(a){ return a != cur; }).join(',');
			if(arr) {
				jQuery('#items_array').val(arr);
			} else {
				jQuery('#galleries input[value="all"]').attr('checked','checked');
				jQuery('#items_array').val('all');
			}
		}
		galleries = '?i='+jQuery('#items_array').val().split(',').join('_');
		skin = jQuery('#skinname option:selected').val(); if(skin) skin = '&f='+skin; else skin = '';
		h = parseInt(jQuery('#galleryheight').val()); if(h) h = '&h='+h; else h = '';
		l = parseInt(jQuery('#postid').val()); if(l) l = '&l='+l; else l = '';
		fb_url(galleries,skin,h,l);
	});
	jQuery('#skinname').change(function(){
		var skin = jQuery(this).val();
		if(skin) {
			skin = '&f='+skin;
		} else {
			skin = '';
		}
		galleries = '?i='+jQuery('#items_array').val().split(',').join('_');
		h = parseInt(jQuery('#galleryheight').val()); if(h) h = '&h='+h; else h = '';
		l = parseInt(jQuery('#postid').val()); if(l) l = '&l='+l; else l = '';
		fb_url(galleries,skin,h,l);
	});
	jQuery('#galleryheight').bind('keyup',function(){
		var h = parseInt(jQuery(this).val());
		if(h) {
			h = '&h='+h;
		} else {
			h = '';
		}
		galleries = '?i='+jQuery('#items_array').val().split(',').join('_');
		skin = jQuery('#skinname option:selected').val(); if(skin) skin = '&f='+skin; else skin = '';
		l = parseInt(jQuery('#postid').val()); if(l) l = '&l='+l; else l = '';
		fb_url(galleries,skin,h,l);
	});
	jQuery('#postid').bind('keyup',function(){
		var l = parseInt(jQuery(this).val());
		if(l) {
			l = '&l='+l;
		} else {
			l = '';
		}
		galleries = '?i='+jQuery('#items_array').val().split(',').join('_');
		skin = jQuery('#skinname option:selected').val(); if(skin) skin = '&f='+skin; else skin = '';
		h = parseInt(jQuery('#galleryheight').val()); if(h) h = '&h='+h; else h = '';
		fb_url(galleries,skin,h,l);
	});
	jQuery('#m_playlist').change(function(){
		var playlist = jQuery(this).val();
		if(playlist) {
			playlist = '?m='+playlist;
		} else {
			playlist = '?m=';
		}
		jQuery('#fb2_url0').val(url+playlist);
	});
	jQuery('#v_playlist').change(function(){
		var playlist = jQuery(this).val();
		if(playlist) {
			playlist = '?v='+playlist;
		} else {
			playlist = '?v=';
		}
		jQuery('#fb3_url0').val(url+playlist);
	});
	jQuery('#b_playlist').change(function(){
		var playlist = jQuery(this).val();
		if(playlist) {
			playlist = '?b='+playlist;
		} else {
			playlist = '?b=';
		}
		jQuery('#fb4_url0').val(url+playlist);
	});
});
function fb_url(galleries,skin,h,l) {
	jQuery('#fb1_url0').val(url+galleries+skin+h+l);
}
/*]]>*/</script>
<div class="flag-wrap">
<h2><?php _e('Flagallery iFrame', 'flag'); ?></h2>
<form id="flagframe_copy" name="flagframe_copy" method="POST" class="alignright">
	<p>Optional: &nbsp; <input type="submit" name="copy_file" class="button-primary" value="<?php _e('Copy flagframe.php file to root directory', 'flag'); ?>" /><br />
	(makes iframe url shorter)</p>
</form>
<form id="generator1"><fieldset style="clear:both; margin:0 0 20px 0; padding: 20px; border: 1px solid #888888;"><legend style="font-size: 18px; padding: 0 5px;"><?php _e("Photo Gallery iFrame Generator", 'flag'); ?></legend>
	<table border="0" cellpadding="4" cellspacing="0">
        <tr>
           <td nowrap="nowrap" valign="top"><div><?php _e("Select galleries", 'flag'); ?>:<span style="color:red;"> *</span><br /><small><?php _e("(album categories)", 'flag'); ?></small></div></td>
           <td valign="top"><div id="galleries" style="width: 214px; height: 160px; overflow: auto; white-space: nowrap;">
                   <div class="row"><input type="checkbox" value="all" checked="checked" /> <strong><span style="display:inline-block; width:3em;">*</span> - <?php _e("all galleries", 'flag'); ?></strong></div>
			<?php
				$gallerylist = $flagdb->find_all_galleries($flag->options['albSort'], $flag->options['albSortDir']);
				if(is_array($gallerylist)) {
					foreach($gallerylist as $gallery) {
						$name = ( empty($gallery->title) ) ? $gallery->name : esc_html(stripslashes($gallery->title));
						echo '<div class="row"><input type="checkbox" value="' . $gallery->gid . '" /> <span><span style="display:inline-block; width:3em;">' . $gallery->gid . '</span> - ' . $name . '</span></div>' . "\n";
					}
				}
			?>
           </div></td>
        </tr>
        <tr>
           <td nowrap="nowrap" valign="top"><p style="padding-top:3px;"><?php _e("Galleries order", 'flag'); ?>: &nbsp; </p></td>
           <td valign="top"><p><input readonly="readonly" type="text" id="items_array" value="all" style="width: 214px;" /></p></td>
        </tr>
        <tr>
            <td nowrap="nowrap" valign="top"><p style="padding-top:3px;"><label for="skinname"><?php _e("Choose skin", 'flag'); ?>:</label></p></td>
            <td valign="top"><p><select id="skinname" name="skinname" style="width: 214px;">
                    <option value="" selected="selected"><?php _e("skin active by default", 'flag'); ?></option>
<?php
	foreach ( (array)$i_skins as $skin_file => $skin_data) {
		echo '<option value="'.dirname($skin_file).'">'.$skin_data['Name'].'</option>'."\n";
	}
?>
            </select></p></td>
        </tr>
		<tr>
			<td valign="top"><p style="padding-top:3px;"><?php _e("Skin size", 'flag'); ?>:<br /><span style="font-size:9px">(<?php _e("blank for default", 'flag'); ?>)</span></p></td>
            <td valign="top"><p><?php _e("width", 'flag'); ?>: <input id="gallerywidth" type="text" disabled="disabled" style="width: 50px" value="100%" /> &nbsp; <?php _e("height", 'flag'); ?>: <input id="galleryheight" type="text" style="width: 50px" /></p></td>
		</tr>
        <tr>
            <td nowrap="nowrap" valign="top"><p style="padding-top:3px;"><?php _e("Post ID", 'flag'); ?>:<br /><span style="font-size:9px">(<?php _e("optional", 'flag'); ?>)</span></p></td>
            <td valign="top"><p><input id="postid" type="text" /></p></td>
        </tr>
		<tr>
			<td valign="top"><div style="padding-top:3px;"><strong><?php _e("iFrame Url", 'flag'); ?>: &nbsp; </strong></div></td>
            <td valign="top"><input id="fb1_url0" type="text" style="width: 780px; font-size: 10px;" value="<?php echo $fb_url.'?i=all'; ?>" /></td>
		</tr>
    </table>
</fieldset></form>
<form id="generator2"><fieldset style="padding: 20px; margin:0 0 20px 0; border: 1px solid #888888;"><legend style="font-size: 18px; padding: 0 5px;"><?php _e("mp3 Gallery iFrame Generator", 'flag'); ?></legend>
	<table border="0" cellpadding="4" cellspacing="0">
        <tr>
            <td nowrap="nowrap" valign="top"><p style="padding-top:3px;"><label><?php _e("Choose playlist", 'flag'); ?>:</label></p></td>
            <td valign="top"><p><select id="m_playlist" style="width: 214px;">
					<option value="" selected="selected"><?php _e('Choose playlist', 'flag'); ?></option>
				<?php 
					foreach((array)$all_m_playlists as $playlist_file => $playlist_data) {
						$playlist_name = basename($playlist_file, '.xml');
				?>
					<option value="<?php echo $playlist_name; ?>"><?php echo esc_html(stripslashes($playlist_data['title'])); ?></option>
				<?php 
					}
				?>
            </select></p></td>
        </tr>
		<tr>
			<td valign="top"><div style="padding-top:3px;"><strong><?php _e("iFrame Url", 'flag'); ?>: &nbsp; </strong></div></td>
            <td valign="top"><input id="fb2_url0" type="text" style="width: 600px; font-size: 10px;" value="<?php echo $fb_url.'?m='; ?>" /></td>
		</tr>
    </table>
</fieldset></form>
<form id="generator3"><fieldset style="padding: 20px; margin:0 0 20px 0; border: 1px solid #888888;"><legend style="font-size: 18px; padding: 0 5px;"><?php _e("Video Blog Gallery iFrame Generator", 'flag'); ?></legend>
	<table border="0" cellpadding="4" cellspacing="0">
        <tr>
            <td nowrap="nowrap" valign="top"><p style="padding-top:3px;"><label><?php _e("Choose playlist", 'flag'); ?>:</label></p></td>
            <td valign="top"><p><select id="v_playlist" style="width: 214px;">
					<option value="" selected="selected"><?php _e('Choose playlist', 'flag'); ?></option>
				<?php 
					foreach((array)$all_v_playlists as $playlist_file => $playlist_data) {
						$playlist_name = basename($playlist_file, '.xml');
				?>
					<option value="<?php echo $playlist_name; ?>"><?php echo esc_html(stripslashes($playlist_data['title'])); ?></option>
				<?php 
					}
				?>
            </select></p></td>
        </tr>
		<tr>
			<td valign="top"><div style="padding-top:3px;"><strong><?php _e("iFrame Url", 'flag'); ?>: &nbsp; </strong></div></td>
            <td valign="top"><input id="fb3_url0" type="text" style="width: 600px; font-size: 10px;" value="<?php echo $fb_url.'?v='; ?>" /></td>
		</tr>
    </table>
</fieldset></form>
<form id="generator4"><fieldset style="padding: 20px; margin:0 0 20px 0; border: 1px solid #888888;"><legend style="font-size: 18px; padding: 0 5px;"><?php _e("Banner Box iFrame Generator", 'flag'); ?></legend>
	<table border="0" cellpadding="4" cellspacing="0">
        <tr>
            <td nowrap="nowrap" valign="top"><p style="padding-top:3px;"><label><?php _e("Choose xml", 'flag'); ?>:</label></p></td>
            <td valign="top"><p><select id="b_playlist" style="width: 214px;">
					<option value="" selected="selected"><?php _e('Choose XML', 'flag'); ?></option>
				<?php 
					foreach((array)$all_b_playlists as $playlist_file => $playlist_data) {
						$playlist_name = basename($playlist_file, '.xml');
				?>
					<option value="<?php echo $playlist_name; ?>"><?php echo esc_html(stripslashes($playlist_data['title'])); ?></option>
				<?php 
					}
				?>
            </select></p></td>
        </tr>
		<tr>
			<td valign="top"><div style="padding-top:3px;"><strong><?php _e("iFrame Url", 'flag'); ?>: &nbsp; </strong></div></td>
            <td valign="top"><input id="fb4_url0" type="text" style="width: 600px; font-size: 10px;" value="<?php echo $fb_url.'?b='; ?>" /></td>
		</tr>
    </table>
</fieldset></form>
</div>
