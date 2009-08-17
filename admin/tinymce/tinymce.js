function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function insertFLAGLink() {
	
	var tagtext;
	
	var galleryname = document.getElementById('galleryname').value;
	var gallerywidth = document.getElementById('gallerywidth').value;
	var galleryheight = document.getElementById('galleryheight').value;
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
		gallerysize = " w=" + gallerywidth + " h=" + galleryheight;
	else
		gallerysize="";

	if (galleryid != 0 )
		tagtext = '[flagallery gid=' + galleryid + ' name="' + galleryname + '"' + gallerysize + ']';
	else
		tinyMCEPopup.close();
	
	if(window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}
