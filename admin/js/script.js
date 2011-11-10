var fv = swfobject.getFlashPlayerVersion();
function FlAGClass(ExtendVar, skin_id, pic_id, slideshow) {
  this.ExtendVar = ExtendVar;
  if (typeof(jQuery) == 'undefined') {
	var JQ = document.createElement('script');
	JQ.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js';
	JQ.type = 'text/javascript';
	document.getElementsByTagName('head')[0].appendChild(JQ);
  }

  waitJQ(skin_id, pic_id, slideshow);
}

function waitJQ(skin_id, pic_id, slideshow) {
	if (typeof(jQuery) == 'undefined') {
	  window.setTimeout(waitJQ, 100);
	}
	else {
	  if (typeof(jQuery.fn.fancybox) == 'undefined') {
		jQuery("head").append("<script type='text/javascript' src='"+this.ExtendVar+"admin/js/jquery.fancybox-1.3.4.pack.js'></script><link rel='stylesheet' href='"+this.ExtendVar+"admin/js/jquery.fancybox-1.3.4.css' type='text/css' media='screen' />");
	  }

	  waitFB(skin_id, pic_id, slideshow);
	}
}

function waitFB(skin_id, pic_id, slideshow) {
	if (typeof(jQuery.fn.fancybox) == 'undefined') {
	  window.setTimeout(waitFB, 100);
	}
	else {
	  jQuery(document).ready(function() {
		if(pic_id !== false){
			var skin_function = flagFind(skin_id);
			if(pic_id !== 0 ) {
				jQuery.fancybox(
				{
					'showNavArrows'	: false,
					'overlayShow'	: true,
					'overlayOpacity': '0.9',
					'overlayColor'	: '#000',
					'transitionIn'	: 'elastic',
					'transitionOut'	: 'elastic',
					'titlePosition'	: 'over',
					'titleFormat'	: function(title, currentArray, currentIndex, currentOpts) {
						var descr = jQuery('<div />').html(jQuery("#flag_pic_"+pic_id, flag_alt[skin_id]).find('.flag_pic_desc > span').html()).text();
						title = jQuery('<div />').html(jQuery("#flag_pic_"+pic_id, flag_alt[skin_id]).find('.flag_pic_desc > strong').html()).text();
						if(title.length || descr.length)
							return '<div class="grand_controls" rel="'+skin_id+'"><span rel="prev" class="g_prev">prev</span><span rel="show" class="g_slideshow '+slideshow+'">play/pause</span><span rel="next" class="g_next">next</span></div><div id="fancybox-title-over">'+(title.length? '<strong class="title">'+title+'</strong>' : '')+(descr.length? '<div class="descr">'+descr+'</div>' : '')+'</div>';
						else
							return '<div class="grand_controls" rel="'+skin_id+'"><span rel="prev" class="g_prev">prev</span><span rel="show" class="g_slideshow '+slideshow+'">play/pause</span><span rel="next" class="g_next">next</span></div>';
					},
					'href'			: jQuery("#flag_pic_"+pic_id, flag_alt[skin_id]).attr('href'),
					'onStart' 		: function(){
						if(skin_function && jQuery.isFunction(skin_function[skin_id+'_fb'])) {
							skin_function[skin_id+'_fb']('active');
						}
						jQuery('#fancybox-wrap').addClass('grand');
					},
					'onClosed' 		: function(currentArray, currentIndex){
						if(skin_function && jQuery.isFunction(skin_function[skin_id+'_fb'])) {
							skin_function[skin_id+'_fb']('close');
						}
						jQuery('#fancybox-wrap').removeClass('grand');
						//jQuery(currentArray[currentIndex]).removeClass('current').addClass('last');
					},
					'onComplete'	: function(currentArray, currentIndex) {
						//jQuery(currentArray).removeClass('current last');
						//jQuery(currentArray[currentIndex]).addClass('current');
					}
				});
			}
			jQuery('#fancybox-wrap').undelegate('.grand_controls span','click').delegate('.grand_controls span','click', function(){
				if(skin_function && jQuery.isFunction(skin_function[skin_id+'_fb'])) {
					skin_function[skin_id+'_fb'](jQuery(this).attr('rel'));
					if(jQuery(this).hasClass('g_slideshow')){
						jQuery(this).toggleClass('play stop');
					}
				}
			});
		} else {
			jQuery('.flag_alternate').each(function(i){
				jQuery(this).show();
				var catMeta = jQuery('.flagCatMeta',this).hide().get();
				for(j=0; j<catMeta.length; j++) {
					var catName = jQuery(catMeta[j]).find('h4').text();
					var catDescr = jQuery(catMeta[j]).find('p').text();
					var catId = jQuery(catMeta[j]).next('.flagcategory').attr('id');
					var act = '';
					if(j==0) act = ' active';
					jQuery('.flagcatlinks',this).append('<a class="flagcat'+act+'" href="#'+catId+'" title="'+catDescr+'">'+catName+'</a>');
				}
			});
			jQuery('.flag_alternate .flagcat').click(function(){
				if(!jQuery(this).hasClass('active')) {
					var catId = jQuery(this).attr('href');
					jQuery(this).addClass('active').siblings().removeClass('active');
					jQuery('.flag_alternate '+catId).show().siblings('.flagcategory').hide();
					alternate_flag_e(catId);
				}
				return false;
			});
			alternate_flag_e('.flagcategory:first');
		}
	  });
	}
}

function alternate_flag_e(t){
	jQuery('.flag_alternate').find(t).not('.loaded').each(function(){
		var d = jQuery(this).html();
		if(d) {
			d = d.replace(/\[/g, '<');
			d = d.replace(/\]/g, ' />');
			jQuery(this).addClass('loaded').html(d);
		}
		jQuery(this).show();
		jQuery('a.flag_pic_alt',this).fancybox({
			'overlayShow'	: true,
			'overlayOpacity': '0.5',
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic',
			'titlePosition'	: 'over',
			'titleFormat'	: function(title, currentArray, currentIndex, currentOpts) {
				var descr = jQuery('<div />').html(jQuery('.flag_pic_desc > span', currentArray[currentIndex]).html()).text();
				title = jQuery('<div />').html(jQuery('.flag_pic_desc > strong', currentArray[currentIndex]).html()).text();
				return '<div id="fancybox-title-over"><em>'+(currentIndex + 1)+' / '+currentArray.length+' &nbsp; </em>'+(title.length? '<strong class="title">'+title+'</strong>' : '')+(descr.length? '<div class="descr">'+descr+'</div>' : '')+'</div>';
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
if(fv.major<10 || (navigator.userAgent.toLowerCase().indexOf("android") > -1)) {
	new FlAGClass(ExtendVar, false, false, false);
}
function thumb_cl(skin_id, pic_id, slideshow){
  	pic_id = parseInt(pic_id);
	new FlAGClass(ExtendVar, skin_id, pic_id, slideshow);
}