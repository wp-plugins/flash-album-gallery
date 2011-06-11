jQuery(document).ready(function() {
	jQuery('div.flashalbum').bind("mouseenter",function(){
	  var obj_id = jQuery('object, embed',this).attr('id');
	  var flash = flagFind(obj_id);
	  if(flash)
		  flash[obj_id]("false");
    }).bind("mouseleave",function(){
	  var obj_id = jQuery('object, embed',this).attr('id');
	  var flash = flagFind(obj_id);
	  if(flash)
		  flash[obj_id]("true");
    });
});

function flagFind(flagName){
	if (window.document[flagName]){
		return window.document[flagName];
	}
	if (navigator.appName.indexOf("Microsoft Internet")==-1){
		if (document.embeds && document.embeds[flagName])
			return document.embeds[flagName];
	}
	else{
		return document.getElementById(flagName);
	}
}
