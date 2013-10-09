/*
 * Title                   : Wall/Grid Gallery
 * Version                 : 0.5
 * Copyright               : 2013 CodEasily.com
 * Website                 : http://www.codeasily.com
 */
if (typeof jQuery.fn.flagallery_phantom == 'undefined') {
	(function ($, window, document, undefined) {
		$.fn.flagallery_phantom = function (options) {
			var Container = this,
					ID = '',
					moduleID = '',
					Settings,
					Content,
					tempVar,
					opt,

					defaultSettings = {
						'width'            : 900, // Width (value in pixels). Default value: 900. Set the width of the gallery.
						'height'           : 0, // Height (value in pixels). Default value: 0. Set the height of the gallery. If you set the value to 0 all thumbnails are going to be displayed.
						'responsiveEnabled': 'true', // Responsive Enabled (true, false). Default value: true. Enable responsive layout.
						'thumbsNavigation' : 'mouse', // Thumbnails Navigation (mouse, scroll). Default value: mouse. Set how you navigate through the thumbnails.
						'thumbCols'        : 0, // Number of Columns (auto, number). Default value: 0. Set the number of columns for the grid.
						'thumbRows'        : 0, // Number of Lines (auto, number). Default value: 0. Set the number of lines for the grid.
						'bgColor'          : 'f1f1f1', // Background Color (color hex code). Default value: f1f1f1. Set gallery background color.
						'bgAlpha'          : 100, // Background Alpha (value from 0 to 100). Default value: 100. Set gallery background alpha.

						'thumbWidth'         : 100, // Thumbnail Width (the size in pixels). Default value: 200. Set the width of a thumbnail.
						'thumbHeight'        : 100, // Thumbnail Height (the size in pixels). Default value: 100. Set the height of a thumbnail.
						'thumbsSpacing'      : 15, // Thumbnails Spacing (value in pixels). Default value: 15. Set the space between thumbnails.
						'thumbsPaddingTop'   : 3, // Thumbnails Padding Top (value in pixels). Default value: 0. Set the top padding for the thumbnails.
						'thumbsPaddingRight' : 3, // Thumbnails Padding Right (value in pixels). Default value: 0. Set the right padding for the thumbnails.
						'thumbsPaddingBottom': 3, // Thumbnails Padding Bottom (value in pixels). Default value: 0. Set the bottom padding for the thumbnails.
						'thumbsPaddingLeft'  : 3, // Thumbnails Padding Left (value in pixels). Default value: 0. Set the left padding for the thumbnails.

						'thumbLoader'           : '../img/ThumbnailLoader.gif', // Thumbnail Loader (path to image). Set the loader for the thumbnails.
						'thumbAlpha'            : 80, // Thumbnail Alpha (value from 0 to 100). Default value: 80. Set the transparancy of a thumbnail.
						'thumbAlphaHover'       : 100, // Thumbnail Alpha Hover (value from 0 to 100). Default value: 100. Set the transparancy of a thumbnail when hover.
						'thumbBgColor'          : 'cccccc', // Thumbnail Background Color (color hex code). Default value: cccccc. Set the color of a thumbnail's background.
						'thumbBgColorHover'     : '000000', // Thumbnail Background Color Hover (color hex code). Default value: 000000. Set the color of a thumbnail's background when hover.
						'thumbBorderSize'       : 0, // Thumbnail Border Size (value in pixels). Default value: 0. Set the size of a thumbnail's border.
						'thumbBorderColor'      : 'cccccc', // Thumbnail Border Color (color hex code). Default value: cccccc. Set the color of a thumbnail's border.
						'thumbBorderColorHover' : '000000', // Thumbnail Border Color Hover (color hex code). Default value: 000000. Set the color of a thumbnail's border when hover.
						'thumbPaddingTop'       : 3, // Thumbnail Padding Top (value in pixels). Default value: 3. Set top padding value of a thumbnail.
						'thumbPaddingRight'     : 3, // Thumbnail Padding Right (value in pixels). Default value: 3. Set right padding value of a thumbnail.
						'thumbPaddingBottom'    : 3, // Thumbnail Padding Bottom (value in pixels). Default value: 3. Set bottom padding value of a thumbnail.
						'thumbPaddingLeft'      : 3,  // Thumbnail Padding Left (value in pixels). Default value: 3. Set left padding value of a thumbnail.
						'thumbsScrollScrubColor': '777777', // Thumbnails Scroll Scrub Color (color hex code). Default value: 777777. Set the scroll scrub color.
						'thumbsScrollBarColor'  : 'e0e0e0', // Thumbnails Scroll Bar Color (color hex code). Default value: e0e0e0. Set the scroll bar color.

						'thumbsInfo'        	: 'label', // Info Thumbnails Display (none, tooltip, label). Default value: tooltip. Display a small info text on the thumbnails, a tooltip or a label on bottom.

						'tooltipBgColor'    	: 'ffffff', // Tooltip Background Color (color hex code). Default value: ffffff. Set tooltip background color.
						'tooltipStrokeColor'	: '000000', // Tooltip Stroke Color (color hex code). Default value: 000000. Set tooltip stroke color.
						'tooltipTextColor'  	: '000000', //   Tooltip Text Color (color hex code). Default value: 000000. Set tooltip text color.

						'labelPosition'      	: 'bottom', // Label Position (bottom, top). Default value: bottom. Set label position.
						'labelTextColor'     	: '000000', // Label Text Color (color hex code). Default value: 000000. Set label text color.
						'labelTextColorHover'	: 'ffffff', // Label Text Color Hover (color hex code). Default value: ffffff. Set label text hover color.

						'lightboxPosition'     : 'document', // Lightbox Position (document, gallery). Default value: document. If the value is document the lightbox is displayed over the web page fitting in the browser's window, else the lightbox is displayed in the gallery's container.
						'lightboxWindowColor'  : '000000', // Lightbox Window Color (color hex code). Default value: 000000. Set the color for the lightbox window.
						'lightboxWindowAlpha'  : 80, // Lightbox Window Alpha (value from 0 to 100). Default value: 80. Set the transparancy for the lightbox window.
						'lightboxLoader'       : '../img/LightboxLoader.gif', // Lightbox Loader (path to image). Set the loader for the lightbox image.
						'lightboxBgColor'      : '000000', // Lightbox Background Color (color hex code). Default value: 000000. Set the color for the lightbox background.
						'lightboxBgAlpha'      : 100, // Lightbox Background Alpha (value from 0 to 100). Default value: 100. Set the transparancy for the lightbox background.
						'lightboxMarginTop'    : 20, // Lightbox Margin Top (value in pixels). Default value: 20. Set top margin value for the lightbox.
						'lightboxMarginRight'  : 20, // Lightbox Margin Right (value in pixels). Default value: 20. Set right margin value for the lightbox.
						'lightboxMarginBottom' : 20, // Lightbox Margin Bottom (value in pixels). Default value: 20. Set bottom margin value for the lightbox.
						'lightboxMarginLeft'   : 20, // Lightbox Margin Left (value in pixels). Default value: 20. Set top left value for the lightbox.
						'lightboxPaddingTop'   : 10, // Lightbox Padding Top (value in pixels). Default value: 10. Set top padding value for the lightbox.
						'lightboxPaddingRight' : 10, // Lightbox Padding Right (value in pixels). Default value: 10. Set right padding value for the lightbox.
						'lightboxPaddingBottom': 10, // Lightbox Padding Bottom (value in pixels). Default value: 10. Set bottom padding value for the lightbox.
						'lightboxPaddingLeft'  : 10, // Lightbox Padding Left (value in pixels). Default value: 10. Set left padding value for the lightbox.

						'lightboxNavPrev'      : '../img/LightboxPrev.png', // Lightbox Navigation Previous Button Image (path to image). Upload the image for lightbox navigation's previous button.
						'lightboxNavPrevHover' : '../img/LightboxPrevHover.png', // Lightbox Navigation Previous Button Hover Image (path to image). Upload the image for lightbox navigation's previous hover button.
						'lightboxNavNext'      : '../img/LightboxNext.png', // Lightbox Navigation Next Button Image (path to image). Upload the image for lightbox navigation's next button.
						'lightboxNavNextHover' : '../img/LightboxNextHover.png', // Lightbox Navigation Next Button Hover Image (path to image). Upload the image for lightbox navigation's next hover button.
						'lightboxNavClose'     : '../img/LightboxClose.png', // Lightbox Navigation Close Button Image (path to image). Upload the image for lightbox navigation's close button.
						'lightboxNavCloseHover': '../img/LightboxCloseHover.png', // Lightbox Navigation Close Button Hover Image (path to image). Upload the image for lightbox navigation's close hover button.

						'captionHeight'    				: 75, // Caption Height (value in pixels). Default value: 75. Set caption height.
						'captionTitleColor'				: 'eeeeee', // Caption Title Color (color hex code). Default value: eeeeee. Set caption title color.
						'captionTextColor' 				: 'dddddd', // Caption Text Color (color hex code). Default value: dddddd. Set caption text color.
						'captionScrollScrubColor' : '777777', // Caption Scroll Scrub Color (color hex code). Default value: 777777. Set the scroll scrub color.
						'captionScrollBarColor' 	: 'e0e0e0', // Caption Scroll Bar Color (color hex code). Default value: e0e0e0. Set the scroll bar color.

						'socialShareEnabled' : 'true', // Social Share Enabled (true, false). Default value: true. Enable AddThis Social Share.
						'socialShareLightbox': '../img/SocialShareLightbox.png' // Lightbox Social Share Button Image

					},

					Images = new Array(),
					Thumbs = new Array(),
					ThumbsWidth = new Array(),
					ThumbsHeight = new Array(),
					ThumbsLoaded = new Array(),
					ThumbsFirstPosX = new Array(),
					ThumbsFirstPosY = new Array(),
					CaptionTitle = new Array(),
					CaptionText = new Array(),
					Media = new Array(),
					Links = new Array(),
					LinksTarget = new Array(),
					noItems = 0,

					startGalleryID = 0,
					startWith = 0,

					currentItem = 0,
					itemLoaded = false,
					ImageWidth = 0,
					ImageHeight = 0,
					LightboxDisplayTime = 600,
					LightboxNavDisplayTime = 600,


					methods = {
						init         : function () {// Init Plugin.
							return this.each(function () {
								Settings = methods.parseSettings();
								opt = $.extend(defaultSettings, Settings);
								if (options) {
									opt = $.extend(opt, options);
								}
								if(opt.width.indexOf("%") !== -1){
									if(opt.responsiveEnabled == 'true'){
										opt.width = screen.width;
									} else {
										opt.width = $(Container).width();
									}
								}
								opt.initialWidth = opt.width;
								opt.initialHeight = opt.height;
								opt.thumbWidthDesktop = opt.thumbWidth;
								opt.thumbHeightDesktop = opt.thumbHeight;

								methods.parseContent();
								$(window).bind('resize.flagallery_phantom', methods.initRP);
							});
						},
						parseSettings: function () {// Parse Settings.
							ID = $(Container).attr('id').split('_ID');
							moduleID = ID[0];
							ID = ID[1];
							if (typeof(window[moduleID + '_ID' + ID + '_Settings']) === 'object')
								Settings = window[moduleID + '_ID' + ID + '_Settings'];
							else
								Settings = {};
							return Settings;
						},
						parseContent : function () {// Parse Content.
							if (typeof(window[moduleID + '_ID' + ID + '_Content']) === 'object')
								Content = window[moduleID + '_ID' + ID + '_Content'];
							else
								return;
							$.each(Content, function (index) {
								$.each(Content[index], function (key) {
									switch (key) {
										case 'image':
											Images.push(prototypes.acaoBuster(Content[index][key]));
											break;
										case 'thumb':
											Thumbs.push(prototypes.acaoBuster(Content[index][key]));
											break;
										case 'captionTitle':
											CaptionTitle.push(Content[index][key]);
											break;
										case 'captionText':
											CaptionText.push(Content[index][key]);
											break;
										case 'media':
											Media.push(Content[index][key]);
											break;
										case 'link':
											Links.push(Content[index][key]);
											break;
										case 'linkTarget':
											if (Content[index][key] == '') {
												LinksTarget.push('_blank');
											}
											else {
												LinksTarget.push(Content[index][key]);
											}
											break;
									}
								});
							});

							noItems = Thumbs.length;

							if (opt.responsiveEnabled == 'true') {
								methods.rpResponsive();
							}

							methods.initGallery();
						},
						initGallery  : function () {// Init the Gallery
							var LightboxHTML = new Array(),
									HTML = new Array();

							LightboxHTML.push('    <div class="' + moduleID + '_LightboxWrapper" id="' + moduleID + '_LightboxWrapper_' + ID + '">');
							LightboxHTML.push('        <div class="' + moduleID + '_LightboxWindow"></div>');
							LightboxHTML.push('        <div class="' + moduleID + '_LightboxLoader"><img src="' + opt.lightboxLoader + '" alt="" /></div>');
							LightboxHTML.push('        <div class="' + moduleID + '_LightboxContainer">');
							LightboxHTML.push('            <div class="' + moduleID + '_LightboxBg"></div>');
							LightboxHTML.push('            <div class="' + moduleID + '_Lightbox"></div>');
							LightboxHTML.push('            <div class="' + moduleID + '_LightboxNav">');
							LightboxHTML.push('                <div class="' + moduleID + '_LightboxNavExtraButtons">');
							LightboxHTML.push('                    <div class="' + moduleID + '_LightboxNav_CloseBtn">');
							LightboxHTML.push('                        <img src="' + opt.lightboxNavClose + '" class="normal" alt="" />');
							LightboxHTML.push('                        <img src="' + opt.lightboxNavCloseHover + '" class="hover" alt="" />');
							LightboxHTML.push('                    </div>');
							if (opt.socialShareEnabled == 'true') {
								LightboxHTML.push('                    <div class="' + moduleID + '_LightboxSocialShare"></div>');
							}
							LightboxHTML.push('                    <br class="' + moduleID + '_Clear" />');
							LightboxHTML.push('                </div>');
							LightboxHTML.push('                <div class="' + moduleID + '_LightboxNavButtons">');
							LightboxHTML.push('                    <div class="' + moduleID + '_LightboxNav_PrevBtn">');
							LightboxHTML.push('                        <img src="' + opt.lightboxNavPrev + '" class="normal" alt="" />');
							LightboxHTML.push('                        <img src="' + opt.lightboxNavPrevHover + '" class="hover" alt="" />');
							LightboxHTML.push('                    </div>');
							LightboxHTML.push('                    <div class="' + moduleID + '_LightboxNav_NextBtn">');
							LightboxHTML.push('                        <img src="' + opt.lightboxNavNext + '" class="normal" alt="" />');
							LightboxHTML.push('                        <img src="' + opt.lightboxNavNextHover + '" class="hover" alt="" />');
							LightboxHTML.push('                    </div>');
							LightboxHTML.push('                    <br class="' + moduleID + '_Clear" />');
							LightboxHTML.push('                </div>');
							LightboxHTML.push('            </div>');
							LightboxHTML.push('            <div class="' + moduleID + '_Caption">');
							LightboxHTML.push('                <div class="' + moduleID + '_CaptionTextWrapper">');
							LightboxHTML.push('                    <div class="' + moduleID + '_CaptionTitle">');
							LightboxHTML.push('                        <div class="title"></div>');
							LightboxHTML.push('                        <div class="count"><span id="' + moduleID + '_ItemCount_' + ID + '"></span> / ' + noItems + '</div>');
							LightboxHTML.push('                        <br style="clear:both;" />');
							LightboxHTML.push('                    </div>');
							LightboxHTML.push('                    <div class="' + moduleID + '_CaptionTextContainer">');
							LightboxHTML.push('                        <div class="' + moduleID + '_CaptionText"></div>');
							LightboxHTML.push('                    </div>');
							LightboxHTML.push('                </div>');
							LightboxHTML.push('            </div>');
							LightboxHTML.push('        </div>');
							LightboxHTML.push('    </div>');

							HTML.push('<div class="' + moduleID + '_Container">');
							HTML.push('   <div class="' + moduleID + '_Background"></div>');
							HTML.push('   <div class="' + moduleID + '_thumbsWrapper">');
							HTML.push('       <div class="' + moduleID + '_thumbs"></div>');
							HTML.push('   </div>');

							if (opt.thumbsInfo == 'tooltip' && !prototypes.isTouchDevice()) {
								HTML.push('<div class="' + moduleID + '_Tooltip"></div>');
							}

							if (opt.lightboxPosition != 'document') {
								HTML.push(LightboxHTML.join(''));
							}
							HTML.push('</div>');

							Container.html(HTML.join(''));

							if (opt.lightboxPosition == 'document') {
								$('body').append(LightboxHTML.join(''));
							}
							methods.initSettings();
						},
						initSettings : function () {// Init Settings
							methods.initContainer();
							methods.initThumbs();
							if (opt.thumbsInfo == 'tooltip' && !prototypes.isTouchDevice()) {
								methods.initTooltip();
							}
							methods.initLightbox();
							methods.initCaption();

						},
						initRP       : function () {// Init Resize & Positioning
							if (opt.responsiveEnabled == 'true') {
								methods.rpResponsive();
								methods.rpContainer();
								methods.rpThumbs();

								if (itemLoaded) {
									if (Media[currentItem - 1] == '') {
										methods.rpLightboxImage();
									}
									else {
										methods.rpLightboxMedia();
									}
								}
							}
						},
						rpResponsive : function () {
							var hiddenBustedItems = prototypes.doHideBuster($(Container));

							if ($(Container).width() < opt.initialWidth) {
								opt.width = $(Container).width();
							}
							else {
								opt.width = opt.initialWidth;
							}

							if ($(window).width() <= 640){
								opt.thumbWidth = opt.thumbWidthDesktop/2;
								opt.thumbHeight = opt.thumbHeightDesktop/2;
							}
							else{
								opt.thumbWidth = opt.thumbWidthDesktop;
								opt.thumbHeight = opt.thumbHeightDesktop;
							}

							prototypes.undoHideBuster(hiddenBustedItems);
						},

						initContainer: function () {// Init Container
							$('.' + moduleID + '_Container', Container).css('display', 'block');

							if (opt.height == 0) {
								$('.' + moduleID + '_Container', Container).css('overflow', 'visible');
							}
							$('.' + moduleID + '_Background', Container).css('background-color', '#' + opt.bgColor);
							$('.' + moduleID + '_Background', Container).css('opacity', parseInt(opt.bgAlpha) / 100);
							methods.rpContainer();
						},
						rpContainer  : function () {// Resize & Position Container
							$('.' + moduleID + '_Container', Container).width(opt.width);

							if (opt.height != 0) {
								$('.' + moduleID + '_Container', Container).height(opt.height);
							}
							else {
								$('.' + moduleID + '_Container', Container).css('height', 'auto');
								$('.' + moduleID + '_thumbsWrapper', Container).css('height', 'auto');
							}
						},

						initThumbs             : function () {//Init Thumbnails
							if (opt.height == 0) {
								$('.' + moduleID + '_thumbsWrapper', Container).css({'overflow': 'visible', 'position': 'relative'});
							}

							for (var i = 1; i <= noItems; i++) {
								methods.loadThumb(i);
							}

							if (opt.height != 0) {
								if (prototypes.isTouchDevice()) {
									prototypes.touchNavigation($('.' + moduleID + '_thumbsWrapper', Container), $('.' + moduleID + '_thumbs', Container));
								}
								else if (opt.thumbsNavigation == 'mouse') {
									$('.' + moduleID + '_thumbs', Container).css('position', 'absolute');
									methods.moveThumbs();
								}
								else if (opt.thumbsNavigation == 'scroll') {
									methods.initThumbsScroll();
								}
							}

							methods.rpThumbs();
						},
						loadThumb              : function (no) {// Load a thumbnail
							methods.initThumb(no);
							var img = new Image();

							$(img).load(function () {
								$('.' + moduleID + '_Thumb', '#' + moduleID + '_ThumbContainer_' + ID + '-' + no, Container).html(this);
								$('.' + moduleID + '_Thumb img', '#' + moduleID + '_ThumbContainer_' + ID + '-' + no, Container).attr('alt', CaptionTitle[no - 1]);

								var hiddenBustedItems = prototypes.doHideBuster($(Container));
								ThumbsWidth[no-1] = $(this).width();
								ThumbsHeight[no-1] = $(this).height();
								prototypes.undoHideBuster(hiddenBustedItems);

								methods.loadCompleteThumb(no);
							}).attr('src', Thumbs[no - 1]);
						},
						initThumb              : function (no) {// Init thumbnail before loading
							var ThumbHTML = new Array(),
									labelHeight = opt.thumbsInfo == 'label' ? $('.' + moduleID + '_ThumbLabel', Container).height()+parseFloat($('.' + moduleID + '_ThumbLabel', Container).css('padding-top'))+parseFloat($('.' + moduleID + '_ThumbLabel', Container).css('padding-bottom')):0;

							ThumbHTML.push('<div class="' + moduleID + '_ThumbContainer" id="' + moduleID + '_ThumbContainer_' + ID + '-' + no + '">');

							if(opt.labelPosition == 'bottom') {
								ThumbHTML.push('   <div class="' + moduleID + '_Thumb"></div>');
							}

							if (opt.thumbsInfo == 'label') {
								if (CaptionTitle[no - 1] == '') {
									CaptionTitle[no - 1] = '&nbsp;';
								}
								ThumbHTML.push('   <div class="' + moduleID + '_ThumbLabel">' + CaptionTitle[no - 1] + '</div>');
							}

							if(opt.labelPosition == 'top') {
								ThumbHTML.push('   <div class="' + moduleID + '_Thumb"></div>');
							}

							if (no == noItems) {
								ThumbHTML.push('</div><br style="clear:both;" />');
							}
							else {
								ThumbHTML.push('</div>');
							}

							$('.' + moduleID + '_thumbs', Container).append(ThumbHTML.join(""));

							if (!prototypes.isTouchDevice()) {
								$('#' + moduleID + '_ThumbContainer_' + ID + '-' + no).css('opacity', parseInt(opt.thumbAlpha) / 100);
							}

							if (opt.labelPosition == 'top' && opt.thumbsInfo == 'label') {
								$('.' + moduleID + '_Thumb', Container).css('margin-top', opt.thumbPaddingTop + labelHeight);
							}
							else {
								$('.' + moduleID + '_Thumb', '#' + moduleID + '_ThumbContainer_' + ID + '-' + no).css('margin-top', opt.thumbPaddingTop);
							}
							$('.' + moduleID + '_Thumb', '#' + moduleID + '_ThumbContainer_' + ID + '-' + no).css({'margin-left': opt.thumbPaddingLeft, 'margin-bottom': opt.thumbPaddingBottom, 'margin-right': opt.thumbPaddingRight});

							$('#' + moduleID + '_ThumbContainer_' + ID + '-' + no).css({'background-color': '#' + opt.thumbBgColor, 'border-width': opt.thumbBorderSize, 'border-color': '#' + opt.thumbBorderColor});

							$('#' + moduleID + '_ThumbContainer_' + ID + '-' + no).addClass(moduleID + '_ThumbLoader').css('background-image', 'url('+opt.thumbLoader+')');

							if (opt.thumbsInfo == 'label') {
								$('.' + moduleID + '_ThumbLabel', Container).css('color', '#' + opt.labelTextColor);
							}

							methods.rpThumbs();
						},
						loadCompleteThumb      : function (no) {// Resize, Position & Edit a thumbnmail after loading
							$('#' + moduleID + '_ThumbContainer_' + ID + '-' + no).removeClass(moduleID + '_ThumbLoader').css('background-image', 'none');

							ThumbsLoaded[no-1] = true;

							methods.rpThumbs();

							$('.' + moduleID + '_Thumb', '#' + moduleID + '_ThumbContainer_' + ID + '-' + no).css('opacity', 0);
							$('.' + moduleID + '_Thumb', '#' + moduleID + '_ThumbContainer_' + ID + '-' + no).stop(true, true).animate({'opacity': '1'}, 600);

							if (!prototypes.isTouchDevice()) {
								$('#' + moduleID + '_ThumbContainer_' + ID + '-' + no).hover(function () {
											$(this).stop(true, true).animate({'opacity': opt.thumbAlphaHover / 100}, 600);
											$(this).css({'background-color': '#' + opt.thumbBgColorHover, 'border-color': '#' + opt.thumbBorderColorHover});
											if (opt.thumbsInfo == 'tooltip' && !prototypes.isTouchDevice()) {
												methods.showTooltip(no - 1);
											}
											if (opt.thumbsInfo == 'label') {
												$('.' + moduleID + '_ThumbLabel', this).css('color', '#' + opt.labelTextColorHover);
											}
										},
										function () {
											$(this).stop(true, true).animate({'opacity': parseInt(opt.thumbAlpha) / 100}, 600);
											$(this).css({'background-color': '#' + opt.thumbBgColor, 'border-color': '#' + opt.thumbBorderColor});
											if (opt.thumbsInfo == 'tooltip' && !prototypes.isTouchDevice()) {
												$('.' + moduleID + '_Tooltip', Container).css('display', 'none');
											}
											if (opt.thumbsInfo == 'label') {
												$('.' + moduleID + '_ThumbLabel', this).css('color', '#' + opt.labelTextColor);
											}
										});
							}

							$('#' + moduleID + '_ThumbContainer_' + ID + '-' + no, Container).click(function () {
								if (Links[no - 1] != '') {
									prototypes.openLink(Links[no - 1], LinksTarget[no - 1]);
								}
								else {
									methods.showLightbox(no);
								}
							});
						},
						rpThumbs               : function () {// Resize & Position Thumbnails
							var labelHeight = opt.thumbsInfo == 'label' ? $('.' + moduleID + '_ThumbLabel', Container).height()+parseFloat($('.' + moduleID + '_ThumbLabel', Container).css('padding-top'))+parseFloat($('.' + moduleID + '_ThumbLabel', Container).css('padding-bottom')): 0,
									thumbW = opt.thumbWidth + opt.thumbPaddingRight + opt.thumbPaddingLeft + 2 * opt.thumbBorderSize,
									no = 0,
									hiddenBustedItems = prototypes.doHideBuster($(Container));
							if (opt.height == 0 || (opt.thumbCols == 0 && opt.thumbRows == 0)) {
								opt.thumbCols = parseInt((opt.width - opt.thumbsPaddingRight - opt.thumbsPaddingLeft + opt.thumbsSpacing) / (thumbW + opt.thumbsSpacing));
								opt.thumbRows = parseInt(noItems / opt.thumbCols);

								if (opt.thumbCols == 0){
									opt.thumbCols = 1;
								}

								if (opt.thumbRows * opt.thumbCols < noItems) {
									opt.thumbRows++;
								}
							} else {
								if ((opt.thumbRows * opt.thumbCols < noItems) && opt.thumbCols != 0) {
									if (noItems % opt.thumbCols != 0) {
										opt.thumbRows = parseInt(noItems / opt.thumbCols) + 1;
									}
									else {
										opt.thumbRows = noItems / opt.thumbCols;
									}
								} else {
									if (noItems % opt.thumbRows != 0) {
										opt.thumbCols = parseInt(noItems / opt.thumbRows) + 1;
									}
									else {
										opt.thumbCols = noItems / opt.thumbRows;
									}
								}
							}
							$('.' + moduleID + '_ThumbContainer', Container).css({'height': opt.thumbHeight + opt.thumbPaddingTop + opt.thumbPaddingBottom + labelHeight,
								'margin': 0,
								'width': opt.thumbWidth + opt.thumbPaddingRight + opt.thumbPaddingLeft});
							$('.' + moduleID + '_Thumb', Container).width(opt.thumbWidth);
							$('.' + moduleID + '_Thumb', Container).height(opt.thumbHeight);

							$('.' + moduleID + '_ThumbContainer', Container).each(function () {
								no++;

								if (no > opt.thumbCols) {
									$(this).css('margin-top', opt.thumbsSpacing);
								}
								if (no % opt.thumbCols != 1 && opt.thumbCols != 1) {
									$(this).css('margin-left', opt.thumbsSpacing);
								}
								if (no <= opt.thumbCols) {
									$(this).css('margin-top', opt.thumbsPaddingTop);
								}
								if (no % opt.thumbCols == 0 && opt.thumbCols != 1) {
									$(this).css('margin-right', opt.thumbsPaddingRight);
								}
								if (no > opt.thumbCols * (opt.thumbRows - 1)) {
									$(this).css('margin-bottom', opt.thumbsPaddingBottom);
								}
								if (no % opt.thumbCols == 1 && opt.thumbCols != 1) {
									$(this).css('margin-left', opt.thumbsPaddingLeft);
								}

								if (ThumbsLoaded[no-1]){
									if ($('img', this).width() == 0){
										prototypes.resizeItem2($('.' + moduleID + '_Thumb', this), $('img', this), opt.thumbWidth, opt.thumbHeight, $('.' + moduleID + '_Thumb', this).width(), $('.' + moduleID + '_Thumb', this).height(), 'center');
									}
									else{
										prototypes.resizeItem2($('.' + moduleID + '_Thumb', this), $('img', this), opt.thumbWidth, opt.thumbHeight, ThumbsWidth[no-1], ThumbsHeight[no-1], 'center');
									}

									if (ThumbsFirstPosX[no-1] == undefined){
										ThumbsFirstPosX[no-1] = parseInt($('img', this).css('margin-left'));
									}
									else{
										if (Math.abs(ThumbsFirstPosX[no-1]-parseInt($('img', this).css('margin-left'))) < 5){
											$('img', this).css('margin-left', ThumbsFirstPosX[no-1]);
										}
									}

									if (ThumbsFirstPosY[no-1] == undefined){
										ThumbsFirstPosY[no-1] = parseInt($('img', this).css('margin-top'));
									}
									else{
										if (Math.abs(ThumbsFirstPosY[no-1]-parseInt($('img', this).css('margin-top'))) < 5){
											$('img', this).css('margin-top', ThumbsFirstPosY[no-1]);
										}
									}
								}
							});

							var thumbs_el = $('.' + moduleID + '_thumbs', Container);
							thumbs_el.width(opt.thumbsPaddingRight + opt.thumbsPaddingLeft + thumbW * opt.thumbCols + (opt.thumbCols - 1) * opt.thumbsSpacing);

							var scrollbar_width = 0;
							if (thumbs_el.width() <= $('.' + moduleID + '_Container', Container).width()) {
								$('.' + moduleID + '_thumbsWrapper', Container).width(thumbs_el.width());
							}
							else {
								$('.' + moduleID + '_thumbsWrapper', Container).width($('.' + moduleID + '_Container', Container).width());
								scrollbar_width = methods.scrollbarWidth();
							}

							if ((thumbs_el.height() + scrollbar_width) <= $('.' + moduleID + '_Container', Container).height()) {
								$('.' + moduleID + '_thumbsWrapper', Container).height(thumbs_el.height() + scrollbar_width);
							}
							else {
								$('.' + moduleID + '_thumbsWrapper', Container).height($('.' + moduleID + '_Container', Container).height());
							}

							prototypes.centerItem($('.' + moduleID + '_Container', Container), $('.' + moduleID + '_thumbsWrapper', Container), $('.' + moduleID + '_Container', Container).width(), $('.' + moduleID + '_Container', Container).height());

							if (parseInt(thumbs_el.css('margin-left')) < (-1) * (thumbs_el.width() - $('.' + moduleID + '_thumbsWrapper', Container).width())) {
								thumbs_el.css('margin-left', (-1) * (thumbs_el.width() - $('.' + moduleID + '_thumbsWrapper', Container).width()));
							}
							if (parseInt(thumbs_el.css('margin-left')) > 0) {
								thumbs_el.css('margin-left', 0);
							}
							if (parseInt(thumbs_el.css('margin-top')) < (-1) * (thumbs_el.height() - $('.' + moduleID + '_thumbsWrapper', Container).height())) {
								$('.' + moduleID + '_thumbs', Container).css('margin-top', (-1) * (thumbs_el.height() - $('.' + moduleID + '_thumbsWrapper', Container).height()));
							}
							if (parseInt(thumbs_el.css('margin-top')) > 0) {
								thumbs_el.css('margin-top', 0);
							}

							if (opt.thumbsNavigation == 'scroll' && typeof(jQuery.fn.jScrollPane) != 'undefined') {
								$('.' + moduleID + '_thumbsWrapper .jspContainer', Container).width($('.' + moduleID + '_thumbsWrapper', Container).width());
								$('.jspDrag', '.' + moduleID + '_thumbsWrapper', Container).css('background', '#' + opt.thumbsScrollScrubColor);
								$('.jspTrack', '.' + moduleID + '_thumbsWrapper', Container).css('background', '#' + opt.thumbsScrollBarColor);
							}

							methods.rpContainer();

							prototypes.undoHideBuster(hiddenBustedItems);
						},
						moveThumbs             : function () {// Init thumbnails move
							var thumbs_el = $('.' + moduleID + '_thumbs', Container);
							$('.' + moduleID + '_thumbsWrapper', Container).mousemove(function (e) {
								var thumbW, thumbH, mousePosition, thumbsPosition;

								if (thumbs_el.width() > $('.' + moduleID + '_thumbsWrapper', Container).width()) {
									thumbW = opt.thumbWidth + opt.thumbPaddingRight + opt.thumbPaddingLeft + 2 * opt.thumbBorderSize;
									mousePosition = e.clientX - $(this).offset().left + parseInt($(this).css('margin-left')) + $(document).scrollLeft();
									thumbsPosition = 0 - (mousePosition - thumbW) * (thumbs_el.width() - $('.' + moduleID + '_thumbsWrapper', Container).width()) / ($('.' + moduleID + '_thumbsWrapper', Container).width() - 2 * thumbW);
									if (thumbsPosition < (-1) * (thumbs_el.width() - $('.' + moduleID + '_thumbsWrapper', Container).width())) {
										thumbsPosition = (-1) * (thumbs_el.width() - $('.' + moduleID + '_thumbsWrapper', Container).width());
									}
									if (thumbsPosition > 0) {
										thumbsPosition = 0;
									}
									thumbs_el.css('margin-left', thumbsPosition);
									//thumbs_el.animate({'margin-left': thumbsPosition}, { duration: 200, queue: false });
								}

								if (thumbs_el.height() > $('.' + moduleID + '_thumbsWrapper', Container).height()) {
									thumbH = opt.thumbHeight + opt.thumbPaddingTop + opt.thumbPaddingBottom + 2 * opt.thumbBorderSize;
									mousePosition = e.clientY - $(this).offset().top + parseInt($(this).css('margin-top')) + $(document).scrollTop();
									thumbsPosition = 0 - (mousePosition - thumbH) * (thumbs_el.height() - $('.' + moduleID + '_thumbsWrapper', Container).height()) / ($('.' + moduleID + '_thumbsWrapper', Container).height() - 2 * thumbH);
									if (thumbsPosition < (-1) * (thumbs_el.height() - $('.' + moduleID + '_thumbsWrapper', Container).height())) {
										thumbsPosition = (-1) * (thumbs_el.height() - $('.' + moduleID + '_thumbsWrapper', Container).height());
									}
									if (thumbsPosition > 0) {
										thumbsPosition = 0;
									}
									thumbs_el.css('margin-top', thumbsPosition);
									//thumbs_el.animate({'margin-top': thumbsPosition}, { duration: 200, queue: false });
								}
							});
						},
						initThumbsScroll       : function () {//Init Thumbnails Scroll
							if (typeof(jQuery.fn.jScrollPane) != 'undefined') {
								setTimeout(function () {
									$('.' + moduleID + '_thumbsWrapper', Container).jScrollPane({autoReinitialise: true});
									$('.jspDrag', '.' + moduleID + '_thumbsWrapper', Container).css('background', '#' + opt.thumbsScrollScrubColor);
									$('.jspTrack', '.' + moduleID + '_thumbsWrapper', Container).css('background', '#' + opt.thumbsScrollBarColor);
								}, 10);
							}
							else {
								$('.' + moduleID + '_thumbsWrapper', Container).css('overflow', 'auto');
							}
						},
						scrollbarWidth         : function () {
							var div = $('<div style="position:absolute;left:-200px;top:-200px;width:50px;height:50px;overflow:scroll"><div>&nbsp;</div></div>').appendTo('body'),
									width = 50 - div.children().innerWidth();
							div.remove();
							return width;
						},
						initLightbox           : function () {// Init Lightbox
							startGalleryID = prototypes.$_GET('gmedia_wall_grid_gallery_id') != undefined ? parseInt(prototypes.$_GET('gmedia_wall_grid_gallery_id')) : 0;
							startWith = prototypes.$_GET('gmedia_wall_grid_gallery_share') != undefined && startGalleryID == ID ? parseInt(prototypes.$_GET('gmedia_wall_grid_gallery_share')) : 0;

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxWindow').css({'background-color': '#' + opt.lightboxWindowColor,
								'opacity'                                                                                               : opt.lightboxWindowAlpha / 100});
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxBg').css({'background-color': '#' + opt.lightboxBgColor,
								'opacity'                                                                                           : opt.lightboxBgAlpha / 100});

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').hover(function () {
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav').stop(true, true).animate({'opacity': 1}, LightboxNavDisplayTime);
							}, function () {
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav').stop(true, true).animate({'opacity': 0}, LightboxNavDisplayTime);
							});

							if (!prototypes.isTouchDevice()) {
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav_PrevBtn').hover(function () {
									$('.normal', this).css('display', 'none');
									$('.hover', this).css('display', 'block');
								}, function () {
									$('.normal', this).css('display', 'block');
									$('.hover', this).css('display', 'none');
								});

								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav_NextBtn').hover(function () {
									$('.normal', this).css('display', 'none');
									$('.hover', this).css('display', 'block');
								}, function () {
									$('.normal', this).css('display', 'block');
									$('.hover', this).css('display', 'none');
								});

								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav_CloseBtn').hover(function () {
									$('.normal', this).css('display', 'none');
									$('.hover', this).css('display', 'block');
								}, function () {
									$('.normal', this).css('display', 'block');
									$('.hover', this).css('display', 'none');
								});
							} else {
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav').css('opacity', 1);
								methods.lightboxNavigationSwipe();
							}

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav_PrevBtn').click(function () {
								methods.previousLightbox();
							});

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav_NextBtn').click(function () {
								methods.nextLightbox();
							});

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxSocialShare').hover(function () {
								setTimeout(function () {
									$('#at15s').css('position', 'fixed');
									$('#at15s').one('mouseover', function () {
										$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav').stop(true, true).css('opacity', 1);
									});
								}, 10);
							}, function () {
									//$('#at15s').off('mouseover');
							});

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav_CloseBtn').click(function () {
								methods.hideLightbox();
							});

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxWindow').click(function () {
								methods.hideLightbox();
							});

							$(document).keydown(function (e) {
								if (itemLoaded) {
									switch (e.keyCode) {
										case 27:
											methods.hideLightbox();
											break;
										case 37:
											methods.previousLightbox();
											break;
										case 39:
											methods.nextLightbox();
											break;
									}
								}
							});

							if (startGalleryID == ID) {
								var href = window.location.href,
										variables = 'gmedia_wall_grid_gallery_id=' + startGalleryID + '&gmedia_wall_grid_gallery_share=' + startWith;

								if (href.indexOf('?' + variables) != -1) {
									variables = '?' + variables;
								}
								else {
									variables = '&' + variables;
								}

								window.location = '#' + moduleID + '_ID' + ID;

								try {
									window.history.pushState({'html': '', 'pageTitle': document.title}, '', href.split(variables)[0]);
								} catch (e) {
									//console.log(e);
								}
							}

							if (startWith != 0) {
								methods.showLightbox(startWith);
								startWith = 0;
							}
						},
						showLightbox           : function (no) {// Show Lightbox
							var documentW, documentH, windowW, windowH, maxWidth, maxHeight, currW, currH;

							if (opt.lightboxPosition == 'document') {
								documentW = $(document).width();
								documentH = $(document).height();
								windowW = $(window).width();
								windowH = $(window).height();
							}
							else {
								documentW = $(Container).width();
								documentH = $(Container).height();
								windowW = $(Container).width();
								windowH = $(Container).height();
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css('position', 'absolute');
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css('position', 'absolute');
							}

							$('#' + moduleID + '_LightboxWrapper_' + ID).width(documentW);
							$('#' + moduleID + '_LightboxWrapper_' + ID).height(documentH);
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxWindow').width(documentW);
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxWindow').height(documentH);

							$('#' + moduleID + '_LightboxWrapper_' + ID).css('display', 'block');
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css('display', 'block');
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css({'margin-top': (windowH - $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').height()) / 2, 'margin-left': (windowW - $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').width()) / 2});

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css('display', 'none');
							$('#' + moduleID + '_LightboxWrapper_' + ID).css('display', 'none');

							$('#' + moduleID + '_LightboxWrapper_' + ID).fadeIn(LightboxDisplayTime, function () {
								methods.loadLightboxImage(no);
							});
						},
						hideLightbox           : function () {// Hide Lightbox
							if (itemLoaded) {
								$('#' + moduleID + '_LightboxWrapper_' + ID).fadeOut(LightboxDisplayTime, function () {
									currentItem = 0;
									itemLoaded = false;
									$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css('opacity', 0);
									$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').html('');
								});
							}
						},
						loadLightboxImage      : function (no) {// Load Lightbox Image
							var img = new Image();

							currentItem = no;
							$('#' + moduleID + '_ItemCount_' + ID).html(currentItem);

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css('display', 'block');

							$(img).load(function () {
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css('display', 'none');
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').html(this);
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox img').attr('alt', CaptionTitle[no - 1]);
								if (opt.socialShareEnabled == 'true'){
									methods.initSocialShare();
								}
								$('#' + moduleID + '_LightboxWrapper_' + ID).css('display', 'block');
								ImageWidth = $(this).width();
								ImageHeight = $(this).height();
								$('#' + moduleID + '_LightboxWrapper_' + ID).css('display', 'none');

								itemLoaded = true;
								methods.showCaption(no);
								methods.rpLightboxImage();

								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').stop(true, true).animate({'opacity': 1}, LightboxDisplayTime, function () {
									if (prototypes.isIEBrowser() && CaptionText[no - 1] != '') {
										methods.rpLightboxImage();
									}
								});
							}).attr('src', Images[no - 1]);
						},
						previousLightbox       : function () {
							var previousItem = currentItem - 1;

							if (currentItem == 1) {
								previousItem = noItems;
							}

							if (Links[previousItem - 1] == '') {
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').stop(true, true).animate({'opacity': 0}, LightboxDisplayTime, function () {
									methods.loadLightboxImage(previousItem);
								});
							}
							else {
								currentItem = previousItem;
								methods.previousLightbox();
							}
						},
						nextLightbox           : function () {
							var nextItem = currentItem + 1;

							if (currentItem == noItems) {
								nextItem = 1;
							}

							if (Links[nextItem - 1] == '') {
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').stop(true, true).animate({'opacity': 0}, LightboxDisplayTime, function () {
									methods.loadLightboxImage(nextItem);
								});
							}
							else {
								currentItem = nextItem;
								methods.nextLightbox();
							}
						},
						rpLightboxImage        : function () {// Resize & Position Lightbox Image
							var documentW, documentH, windowW, windowH, maxWidth, maxHeight, currW, currH;

							$('#' + moduleID + '_LightboxWrapper_'+ID).css('display', 'none');

							if (opt.lightboxPosition == 'document') {
								documentW = $(document).width();
								documentH = $(document).height();
								windowW = $(window).width();
								windowH = $(window).height();
							}
							else {
								documentW = $(Container).width();
								documentH = $(Container).height();
								windowW = $(Container).width();
								windowH = $(Container).height();
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css('position', 'absolute');
							}

							$('#' + moduleID + '_LightboxWrapper_'+ID).css('display', 'block');
							maxWidth = windowW - (($(window).width() <= 640) ? 1 : opt.lightboxMarginRight) - (($(window).width() <= 640) ? 1 : opt.lightboxMarginLeft) - opt.lightboxPaddingRight - opt.lightboxPaddingLeft;
							maxHeight = windowH - (($(window).width() <= 640) ? 1 : opt.lightboxMarginTop) - (($(window).width() <= 640) ? 1 : opt.lightboxMarginBottom) - opt.lightboxPaddingTop - opt.lightboxPaddingBottom - opt.captionHeight;

							$('#' + moduleID + '_LightboxWrapper_' + ID).width(documentW);
							$('#' + moduleID + '_LightboxWrapper_' + ID).height(documentH);
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxWindow').width(documentW);
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxWindow').height(documentH);

							$('#' + moduleID + '_LightboxWrapper_' + ID).css('display', 'block');
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css('display', 'block');
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css({'margin-top': (windowH - $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').height()) / 2, 'margin-left': (windowW - $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').width()) / 2});

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css('display', 'none');

							if (itemLoaded) {
								if (ImageWidth <= maxWidth && ImageHeight <= maxHeight) {
									currW = ImageWidth;
									currH = ImageHeight;
								}
								else {
									currH = maxHeight;
									currW = (ImageWidth * maxHeight) / ImageHeight;

									if (currW > maxWidth) {
										currW = maxWidth;
										currH = (ImageHeight * maxWidth) / ImageWidth;
									}
								}

								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox img').width(currW);
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox img').height(currH);
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox img').css({'margin-top': opt.lightboxPaddingTop, 'margin-left': opt.lightboxPaddingLeft});
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').css({'height': $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').children().height(),
																																																	'width'	: $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').children().width()});
								methods.rpCaption();
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').width(currW + opt.lightboxPaddingRight + opt.lightboxPaddingLeft);
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').height(currH + opt.lightboxPaddingTop + opt.lightboxPaddingBottom + $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Caption').height());

								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css({'margin-top': (windowH - $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').height()) / 2, 'margin-left': (windowW - $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').width()) / 2});

								methods.rpLightboxNav();
							}
						},
						rpLightboxMedia        : function () {// Resize & Position Lightbox Media
							var documentW, documentH, windowW, windowH, maxWidth, maxHeight, currW, currH;

							$('#' + moduleID + '_LightboxWrapper_'+ID).css('display', 'none');

							if (opt.lightboxPosition == 'document') {
								documentW = $(document).width();
								documentH = $(document).height();
								windowW = $(window).width();
								windowH = $(window).height();
							}
							else {
								documentW = $(Container).width();
								documentH = $(Container).height();
								windowW = $(Container).width();
								windowH = $(Container).height();
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css('position', 'absolute');
							}

							$('#' + moduleID + '_LightboxWrapper_'+ID).css('display', 'block');
							maxWidth = windowW-($(window).width() <= 640 ? 1:opt.lightboxMarginRight)-($(window).width() <= 640 ? 1:opt.lightboxMarginLeft)-opt.lightboxPaddingRight-opt.lightboxPaddingLeft;
							maxHeight = windowH-($(window).width() <= 640 ? 1:opt.lightboxMarginTop)-($(window).width() <= 640 ? 1:opt.lightboxMarginBottom)-opt.lightboxPaddingTop-opt.lightboxPaddingBottom-opt.captionHeight;

							$('#' + moduleID + '_LightboxWrapper_' + ID).width(documentW);
							$('#' + moduleID + '_LightboxWrapper_' + ID).height(documentH);
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxWindow').width(documentW);
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxWindow').height(documentH);

							$('#' + moduleID + '_LightboxWrapper_' + ID).css('display', 'block');
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css('display', 'block');
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css({
								'margin-top'	: (windowH - $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').height()) / 2,
								'margin-left' : (windowW - $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').width()) / 2});

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxLoader').css('display', 'none');

							if (ImageWidth <= maxWidth && ImageHeight <= maxHeight){
								currW = ImageWidth;
								currH = ImageHeight;

								if (ImageWidth == 0 && ImageHeight == 0){
									currW = $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').children().width();
									currH = $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').children().height();
								}
							}
							else{
								currH = maxHeight;
								currW = (ImageWidth*maxHeight)/ImageHeight;

								if (currW > maxWidth){
									currW = maxWidth;
									currH = (ImageHeight*maxWidth)/ImageWidth;
								}
							}

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').children().width(currW);
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').children().height(currH);
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').children().css({'margin-top'	: opt.lightboxPaddingTop, 'margin-left' : opt.lightboxPaddingLeft});
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').css({'height': currH, 'width': currW});

							methods.rpCaption();
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').width(currW + opt.lightboxPaddingRight + opt.lightboxPaddingLeft);
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').height(currH + opt.lightboxPaddingTop + opt.lightboxPaddingBottom + $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Caption').height());

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css({
									'margin-top'	: (windowH - $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').height()) / 2,
									'margin-left' : (windowW - $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').width()) / 2});

							methods.rpLightboxNav();
						},
						rpLightboxNav          : function () {// Resize & Position Lightbox Navigation
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNavButtons').css({
								'margin-top'	: ($('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').height() - $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNavButtons').children().height()) / 2 + opt.lightboxPaddingTop,
								'margin-left' : opt.lightboxPaddingLeft,
								'width'       : $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').width()});
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNavExtraButtons').css({
								'margin-top'	: opt.lightboxPaddingTop,
								'margin-left' : opt.lightboxPaddingLeft,
								'width'       : $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Lightbox').width()});
						},
						lightboxNavigationSwipe: function () {
							var prev, curr, touch, initial, positionX;

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').bind('touchstart', function (e) {
								touch = e.originalEvent.touches[0];
								prev = touch.clientX;
								initial = parseFloat($('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css('margin-left'));
							});

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').bind('touchmove', function (e) {
								e.preventDefault();
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav').css('opacity', 0);

								touch = e.originalEvent.touches[0];
								curr = touch.clientX;
								positionX = curr > prev ? parseInt($('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css('margin-left')) + (curr - prev) : parseInt($('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css('margin-left')) - (prev - curr);

								prev = curr;
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css('margin-left', positionX);
							});

							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').bind('touchend', function (e) {
								e.preventDefault();
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxNav').css('opacity', 1);

								if (parseFloat($('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css('margin-left')) < 0) {
									$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css({'margin-left': initial, 'opacity': 0});
									methods.nextLightbox();
								}
								else if (parseFloat($('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css('margin-left')) + $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').width() > $(window).width()) {
									$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css({'margin-left': initial, 'opacity': 0});
									methods.previousLightbox();
								}
								else {
									$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxContainer').css('margin-left', initial);
								}
							});
						},

						initCaption: function () {// Init Caption
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_Caption').css({'margin-left': opt.lightboxPaddingLeft, 'margin-right': opt.lightboxPaddingRight, 'bottom': opt.lightboxPaddingBottom});
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_CaptionTitle').css('color', '#' + opt.captionTitleColor);
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_CaptionText').css('color', '#' + opt.captionTextColor);
							if (typeof(jQuery.fn.jScrollPane) != 'undefined') {
								$('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_CaptionTextContainer').jScrollPane();
							}
						},
						showCaption: function (no) {// Show Caption
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_CaptionTitle .title').html(CaptionTitle[no - 1]);
							$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_CaptionText').html($("<div />").html(CaptionText[no-1]).text());

							if (CaptionText[no - 1] == '') {
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_CaptionTextContainer').css('display', 'none');
							}
							else {
								$('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_CaptionTextContainer').css('display', 'block');
							}
						},
						rpCaption  : function () {// Resize & Position Caption
							$('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_CaptionTextContainer').height($('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_CaptionText').height()).css('overflow', 'hidden');
							$('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_Caption').width($('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_Lightbox').children().width());

							var textHeight = opt.captionHeight-$('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_CaptionTitle .title').height()-parseFloat($('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_CaptionTitle').css('margin-top'))-parseFloat($('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_CaptionTextContainer').css('margin-top'));

							if ($('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_CaptionTextContainer').height() > textHeight){
								$('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_CaptionTextContainer').height(textHeight).css('overflow', 'auto');
								if (typeof(jQuery.fn.jScrollPane) != 'undefined') {
									$('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_CaptionTextContainer').jScrollPane();
									$('#' + moduleID + '_LightboxWrapper_'+ID+' .jspDrag').css('background-color', '#'+opt.captionScrollScrubColor);
									$('#' + moduleID + '_LightboxWrapper_'+ID+' .jspTrack').css('background-color', '#'+opt.captionScrollBarColor);
								}
							}

							if (typeof(jQuery.fn.jScrollPane) != 'undefined') {
								setTimeout(function(){
									$('#' + moduleID + '_LightboxWrapper_'+ID+' .' + moduleID + '_CaptionTextContainer').jScrollPane();
									$('#' + moduleID + '_LightboxWrapper_'+ID+' .jspDrag').css('background-color', '#'+opt.captionScrollScrubColor);
									$('#' + moduleID + '_LightboxWrapper_'+ID+' .jspTrack').css('background-color', '#'+opt.captionScrollBarColor);
								}, 100);
							}
						},

						initSocialShare: function () {
							var HTML = new Array(),
									URL = window.location.href + (window.location.href.indexOf('?') != -1 ? '&' : '?') + 'gmedia_wall_grid_gallery_id=' + ID + '&gmedia_wall_grid_gallery_share=' + currentItem;

							HTML.push('       <div id="addthis_' + ID + '-' + currentItem + '">');
							HTML.push('            <a addthis:url="' + URL + '" addthis:title="' + CaptionTitle[currentItem - 1] + '">');
							HTML.push('                <img src="' + opt.socialShareLightbox + '" alt="" />');
							HTML.push('            </a>');
							HTML.push('       </div>');

							var addthis_wrapper = $('#' + moduleID + '_LightboxWrapper_' + ID + ' .' + moduleID + '_LightboxSocialShare'),
									addthis_cur,
									addthis_config = {
										ui_click        : false,
										ui_offset_left  : -78,
										ui_offset_top   : 0,
										services_exclude: 'print'
									},
									addthis_share = {
										url      : URL,
										title    : CaptionTitle[currentItem - 1],
										templates: {
											twitter: '{{title}} {{url}}'
										}
									};
							if (window.addthis != undefined) {
								addthis_wrapper.html(HTML.join(''));
								addthis_cur = document.getElementById('addthis_' + ID + '-' + currentItem);
								window.addthis.button(addthis_cur, addthis_config, addthis_share);
							} else {

								$.getScript('http://s7.addthis.com/js/300/addthis_widget.js')
										.done(function () {
											if (window.addthis) {
												addthis_wrapper.html(HTML.join(''));
												addthis_cur = document.getElementById('addthis_' + ID + '-' + currentItem);
												//window.addthis.ost = 0;
												//window.addthis.init();
												//window.addthis.ready();
												window.addthis.button(addthis_cur, addthis_config, addthis_share);
											}
										})
										.fail(function () {
											addthis_wrapper.empty();
										});

							}
						},

						initTooltip: function () {// Init Tooltip
							$('.' + moduleID + '_ThumbContainer', Container).on('mouseover mousemove', function (e) {
								var thumbs_wrapper = $('.' + moduleID + '_thumbsWrapper', Container),
										mousePositionX = e.clientX - $(thumbs_wrapper).offset().left + parseInt($(thumbs_wrapper).css('margin-left')) + $(document).scrollLeft(),
										mousePositionY = e.clientY - $(thumbs_wrapper).offset().top + parseInt($(thumbs_wrapper).css('margin-top')) + $(document).scrollTop();

								$('.' + moduleID + '_Tooltip', Container).css('left', mousePositionX - 10);
								$('.' + moduleID + '_Tooltip', Container).css('top', mousePositionY - $('.' + moduleID + '_Tooltip', Container).height() - 15);
							});
						},
						showTooltip: function (no) {// Resize, Position & Display the Tooltip
							var HTML = new Array();
							HTML.push(CaptionTitle[no]);
							HTML.push('<div class="' + moduleID + '_Tooltip_ArrowBorder"></div>');
							HTML.push('<div class="' + moduleID + '_Tooltip_Arrow"></div>');
							$('.' + moduleID + '_Tooltip', Container).html(HTML.join(""));

							if (opt.tooltipBgColor != 'css') {
								$('.' + moduleID + '_Tooltip', Container).css('background-color', '#' + opt.tooltipBgColor);
								$('.' + moduleID + '_Tooltip_Arrow', Container).css('border-top-color', '#' + opt.tooltipBgColor);
							}
							if (opt.tooltipStrokeColor != 'css') {
								$('.' + moduleID + '_Tooltip', Container).css('border-color', '#' + opt.tooltipStrokeColor);
								$('.' + moduleID + '_Tooltip_ArrowBorder', Container).css('border-top-color', '#' + opt.tooltipStrokeColor);
							}
							if (opt.tooltipTextColor != 'css') {
								$('.' + moduleID + '_Tooltip', Container).css('color', '#' + opt.tooltipTextColor);
							}
							if (CaptionTitle[no] != '') {
								$('.' + moduleID + '_Tooltip', Container).css('display', 'block');
							}
						}
					},

					prototypes = {
						resizeItem : function (parent, child, cw, ch, dw, dh, pos) {// Resize & Position an Item (the item is 100% visible)
							var currW = 0, currH = 0;

							if (dw <= cw && dh <= ch) {
								currW = dw;
								currH = dh;
							}
							else {
								currH = ch;
								currW = (dw * ch) / dh;

								if (currW > cw) {
									currW = cw;
									currH = (dh * cw) / dw;
								}
							}

							child.width(currW);
							child.height(currH);
							switch (pos.toLowerCase()) {
								case 'top':
									prototypes.topItem(parent, child, ch);
									break;
								case 'bottom':
									prototypes.bottomItem(parent, child, ch);
									break;
								case 'left':
									prototypes.leftItem(parent, child, cw);
									break;
								case 'right':
									prototypes.rightItem(parent, child, cw);
									break;
								case 'horizontal-center':
									prototypes.hCenterItem(parent, child, cw);
									break;
								case 'vertical-center':
									prototypes.vCenterItem(parent, child, ch);
									break;
								case 'center':
									prototypes.centerItem(parent, child, cw, ch);
									break;
								case 'top-left':
									prototypes.tlItem(parent, child, cw, ch);
									break;
								case 'top-center':
									prototypes.tcItem(parent, child, cw, ch);
									break;
								case 'top-right':
									prototypes.trItem(parent, child, cw, ch);
									break;
								case 'middle-left':
									prototypes.mlItem(parent, child, cw, ch);
									break;
								case 'middle-right':
									prototypes.mrItem(parent, child, cw, ch);
									break;
								case 'bottom-left':
									prototypes.blItem(parent, child, cw, ch);
									break;
								case 'bottom-center':
									prototypes.bcItem(parent, child, cw, ch);
									break;
								case 'bottom-right':
									prototypes.brItem(parent, child, cw, ch);
									break;
							}
						},
						resizeItem2: function (parent, child, cw, ch, dw, dh, pos) {// Resize & Position an Item (the item covers all the container)
							var currW = 0, currH = 0;

							currH = ch;
							currW = (dw * ch) / dh;

							if (currW < cw) {
								currW = cw;
								currH = (dh * cw) / dw;
							}

							child.width(currW);
							child.height(currH);

							switch (pos.toLowerCase()) {
								case 'top':
									prototypes.topItem(parent, child, ch);
									break;
								case 'bottom':
									prototypes.bottomItem(parent, child, ch);
									break;
								case 'left':
									prototypes.leftItem(parent, child, cw);
									break;
								case 'right':
									prototypes.rightItem(parent, child, cw);
									break;
								case 'horizontal-center':
									prototypes.hCenterItem(parent, child, cw);
									break;
								case 'vertical-center':
									prototypes.vCenterItem(parent, child, ch);
									break;
								case 'center':
									prototypes.centerItem(parent, child, cw, ch);
									break;
								case 'top-left':
									prototypes.tlItem(parent, child, cw, ch);
									break;
								case 'top-center':
									prototypes.tcItem(parent, child, cw, ch);
									break;
								case 'top-right':
									prototypes.trItem(parent, child, cw, ch);
									break;
								case 'middle-left':
									prototypes.mlItem(parent, child, cw, ch);
									break;
								case 'middle-right':
									prototypes.mrItem(parent, child, cw, ch);
									break;
								case 'bottom-left':
									prototypes.blItem(parent, child, cw, ch);
									break;
								case 'bottom-center':
									prototypes.bcItem(parent, child, cw, ch);
									break;
								case 'bottom-right':
									prototypes.brItem(parent, child, cw, ch);
									break;
							}
						},

						topItem        : function (parent, child, ch) {// Position Item on Top
							parent.height(ch);
							child.css('margin-top', 0);
						},
						bottomItem     : function (parent, child, ch) {// Position Item on Bottom
							parent.height(ch);
							child.css('margin-top', ch - child.height());
						},
						leftItem       : function (parent, child, cw) {// Position Item on Left
							parent.width(cw);
							child.css('margin-left', 0);
						},
						rightItem      : function (parent, child, cw) {// Position Item on Right
							parent.width(cw);
							child.css('margin-left', parent.width() - child.width());
						},
						hCenterItem    : function (parent, child, cw) {// Position Item on Horizontal Center
							parent.width(cw);
							child.css({'padding-left': (cw - child.width()) / 2, 'padding-right': (cw - child.width()) / 2});
						},
						vCenterItem    : function (parent, child, ch) {// Position Item on Vertical Center
							parent.height(ch);
							child.css('margin-top', (ch - child.height()) / 2);
						},
						centerItem     : function (parent, child, cw, ch) {// Position Item on Center
							prototypes.hCenterItem(parent, child, cw);
							prototypes.vCenterItem(parent, child, ch);
						},
						tlItem         : function (parent, child, cw, ch) {// Position Item on Top-Left
							prototypes.topItem(parent, child, ch);
							prototypes.leftItem(parent, child, cw);
						},
						tcItem         : function (parent, child, cw, ch) {// Position Item on Top-Center
							prototypes.topItem(parent, child, ch);
							prototypes.hCenterItem(parent, child, cw);
						},
						trItem         : function (parent, child, cw, ch) {// Position Item on Top-Right
							prototypes.topItem(parent, child, ch);
							prototypes.rightItem(parent, child, cw);
						},
						mlItem         : function (parent, child, cw, ch) {// Position Item on Middle-Left
							prototypes.vCenterItem(parent, child, ch);
							prototypes.leftItem(parent, child, cw);
						},
						mrItem         : function (parent, child, cw, ch) {// Position Item on Middle-Right
							prototypes.vCenterItem(parent, child, ch);
							prototypes.rightItem(parent, child, cw);
						},
						blItem         : function (parent, child, cw, ch) {// Position Item on Bottom-Left
							prototypes.bottomItem(parent, child, ch);
							prototypes.leftItem(parent, child, cw);
						},
						bcItem         : function (parent, child, cw, ch) {// Position Item on Bottom-Center
							prototypes.bottomItem(parent, child, ch);
							prototypes.hCenterItem(parent, child, cw);
						},
						brItem         : function (parent, child, cw, ch) {// Position Item on Bottom-Right
							prototypes.bottomItem(parent, child, ch);
							prototypes.rightItem(parent, child, cw);
						},
						isIEBrowser    : function () {// Detect the browser IE
							var isIE = false,
									agent = navigator.userAgent.toLowerCase();

							if (agent.indexOf('msie') != -1) {
								isIE = true;
							}
							return isIE;
						},
						isTouchDevice  : function () {// Detect Touchscreen devices
							return 'ontouchend' in document;
						},
						touchNavigation: function (parent, child) {// One finger Navigation for touchscreen devices
							var prevX, prevY, currX, currY, touch, moveTo, thumbsPositionX, thumbsPositionY,
									thumbW = opt.thumbWidth + opt.thumbPaddingRight + opt.thumbPaddingLeft + 2 * opt.thumbBorderSize,
									thumbH = opt.thumbHeight + opt.thumbPaddingTop + opt.thumbPaddingBottom + 2 * opt.thumbBorderSize;


							parent.bind('touchstart', function (e) {
								touch = e.originalEvent.touches[0];
								prevX = touch.clientX;
								prevY = touch.clientY;
							});

							parent.bind('touchmove', function (e) {
								touch = e.originalEvent.touches[0];
								currX = touch.clientX;
								currY = touch.clientY;
								thumbsPositionX = currX > prevX ? parseInt(child.css('margin-left')) + (currX - prevX) : parseInt(child.css('margin-left')) - (prevX - currX);
								thumbsPositionY = currY > prevY ? parseInt(child.css('margin-top')) + (currY - prevY) : parseInt(child.css('margin-top')) - (prevY - currY);

								if (thumbsPositionX < (-1) * (child.width() - parent.width())) {
									thumbsPositionX = (-1) * (child.width() - parent.width());
								}
								else if (thumbsPositionX > 0) {
									thumbsPositionX = 0;
								}
								else {
									e.preventDefault();
								}

								if (thumbsPositionY < (-1) * (child.height() - parent.height())) {
									thumbsPositionY = (-1) * (child.height() - parent.height());
								}
								else if (thumbsPositionY > 0) {
									thumbsPositionY = 0;
								}
								else {
									e.preventDefault();
								}

								prevX = currX;
								prevY = currY;

								if (parent.width() < child.width()){
									child.css('margin-left', thumbsPositionX);
								}
								if (parent.height() < child.height()){
									child.css('margin-top', thumbsPositionY);
								}
							});

							parent.bind('touchend', function (e) {
								e.preventDefault();

								if (thumbsPositionX % (opt.thumbWidth + opt.thumbsSpacing) != 0) {
									if ((thumbsPosition == 'horizontal') && $('.gMedia_thumbScroller_thumbs', Container).width() > $('.gMedia_thumbScroller_thumbsWrapper', Container).width()) {
										if (prevX > touch.clientX) {
											moveTo = parseInt(thumbsPositionX / (thumbW + opt.thumbsSpacing)) * (thumbW + opt.thumbsSpacing);
										}
										else {
											moveTo = (parseInt(thumbsPositionX / (thumbW + opt.thumbsSpacing)) - 1) * (thumbW + opt.thumbsSpacing);
										}
										arrowsClicked = true;

										$('.gMedia_thumbScroller_thumbs', Container).stop(true, true).animate({'margin-left': moveTo}, thumbsNavigationArrowsSpeed, function () {
											arrowsClicked = false;
										});
									}
								}

								if (thumbsPositionY % (opt.thumbHeight + opt.thumbsSpacing) != 0) {
									if ((thumbsPosition == 'vertical') && $('.gMedia_thumbScroller_thumbs', Container).height() > $('.gMedia_thumbScroller_thumbsWrapper', Container).height()) {
										if (prevY > touch.clientY) {
											moveTo = parseInt(thumbsPositionY / (thumbH + opt.thumbsSpacing)) * (thumbH + opt.thumbsSpacing);
										}
										else {
											moveTo = (parseInt(thumbsPositionY / (thumbH + opt.thumbsSpacing)) - 1) * (thumbH + opt.thumbsSpacing);
										}
										arrowsClicked = true;

										$('.gMedia_thumbScroller_thumbs', Container).stop(true, true).animate({'margin-top': moveTo}, thumbsNavigationArrowsSpeed, function () {
											arrowsClicked = false;
										});
									}
								}
							});
						},

						openLink    : function (url, target) {// Open a link.
							switch (target.toLowerCase()) {
								case '_blank':
									window.open(url);
									break;
								case '_top':
									top.location.href = url;
									break;
								case '_parent':
									parent.location.href = url;
									break;
								default:
									window.location = url;
							}
						},
						$_GET       : function (variable) {
							var url = window.location.href.split('?')[1],
									variables = url != undefined ? url.split('&') : [],
									i;

							for (i = 0; i < variables.length; i++) {
								if (variables[i].indexOf(variable) != -1) {
									return variables[i].split('=')[1];
								}
							}

							return undefined;
						},
						acaoBuster  : function (dataURL) {
							var topURL = window.location.href,
									pathPiece1 = '', pathPiece2 = '';

							if (dataURL.indexOf('https') != -1 || dataURL.indexOf('http') != -1) {
								if (topURL.indexOf('http://www.') != -1) {
									pathPiece1 = 'http://www.';
								}
								else if (topURL.indexOf('http://') != -1) {
									pathPiece1 = 'http://';
								}
								else if (topURL.indexOf('https://www.') != -1) {
									pathPiece1 = 'https://www.';
								}
								else if (topURL.indexOf('https://') != -1) {
									pathPiece1 = 'https://';
								}

								if (dataURL.indexOf('http://www.') != -1) {
									pathPiece2 = dataURL.split('http://www.')[1];
								}
								else if (dataURL.indexOf('http://') != -1) {
									pathPiece2 = dataURL.split('http://')[1];
								}
								else if (dataURL.indexOf('https://www.') != -1) {
									pathPiece2 = dataURL.split('https://www.')[1];
								}
								else if (dataURL.indexOf('https://') != -1) {
									pathPiece2 = dataURL.split('https://')[1];
								}

								return pathPiece1 + pathPiece2;
							}
							else {
								return dataURL;
							}
						},
						doHideBuster:function(item){// Make all parents & current item visible
							var parent = item.parent(),
									items = new Array();

							if (item.prop('tagName') != undefined && item.prop('tagName').toLowerCase() != 'body'){
								items = prototypes.doHideBuster(parent);
							}

							if (item.css('display') == 'none'){
								item.css('display', 'block');
								items.push(item);
							}

							return items;
						},
						undoHideBuster:function(items){// Hide items in the array
							var i;

							for (i=0; i<items.length; i++){
								items[i].css('display', 'none');
							}
						},
						setCookie   : function (c_name, value, expiredays) {
							var exdate = new Date();
							exdate.setDate(exdate.getDate() + expiredays);

							document.cookie = c_name + "=" + encodeURI(value) + ((expiredays == null) ? "" : ";expires=" + exdate.toUTCString()) + ";javahere=yes;path=/";
						},
						readCookie  : function (name) {
							var nameEQ = name + "=",
									ca = document.cookie.split(";");

							for (var i = 0; i < ca.length; i++) {
								var c = ca[i];

								while (c.charAt(0) == " ") {
									c = c.substring(1, c.length);
								}

								if (c.indexOf(nameEQ) == 0) {
									return c.substring(nameEQ.length, c.length);
								}
							}
							return null;
						},
						deleteCookie: function (c_name, path, domain) {
							if (readCookie(c_name)) {
								document.cookie = c_name + "=" + ((path) ? ";path=" + path : "") + ((domain) ? ";domain=" + domain : "") + ";expires=Thu, 01-Jan-1970 00:00:01 GMT";
							}
						}
					};

			return methods.init.apply(this);
		}
	})(jQuery, window, document);
}