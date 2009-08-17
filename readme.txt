=== GRAND Flash Album Gallery ===
Contributors: Sergey Pasyuk
Donate link: 
Tags: photos, flash, slideshow, images, gallery, media, admin, post, photo-albums, pictures, photo, picture, image, multi-categories gallery, skinable gallery, skin
Requires at least: 2.7
Tested up to: 2.8.*
Stable tag: trunk

GRAND Flash Album Gallery is a full integrated (flash skin based) Image Gallery plugin with a powerfull administration back end.

== Description ==

Are you looking for a better way to manage and display photos on your blogs??? Then you must try this fantastic GRAND Flash Album Gallery plugin.  It provides a comprehensive interface for managing photos and images through a set of admin pages, and it displays photos in a way that makes your web site look very professional. You can display galleries with a beautiful flash skins integrated with GRAND Flash Album Gallery.

=IMPORTANT!!!=
Before upgrade from version 0.24 BACKUP your skins folder, please!!!
If FlAGallery get an error after upgrade, go to FlAGallery Overview page and press 'Reset settings' 

Important Links:

* <a href="http://codeasily.com/wordpress-plugins/flash-album-gallery/flag/" title="Demonstration page">Demonstration</a>
* <a href="http://codeasily.com/wordpress-plugins/flash-album-gallery/languages/" title="Translation and Language files">Language files</a>
* <a href="http://photogallerycreator.com/2009/07/skins-for-flash-album-gallery/" title="Additional skins">Additional skins</a>
* <a href="http://codeasily.com/wordpress-plugins/flash-album-gallery/faq/" title="GRAND Flash Album Gallery FAQ">GRAND FlAGallery FAQ</a>
* <a href="http://wordpress.org/tags/flash-album-gallery" title="Wordpress Support Forum">Support Forum</a>


= Features =

* Flash skins: You can add and change flash skins for displaying galleries.
* Media RSS feed 
* Role settings: Each gallery has a author
* AJAX based thumbnail generator: No more server limitation during the batch process
* Copy/Move: Copy or move images between galleries 
* TinyMCE: Button integration for easy adding the gallery tag with options
* HTML Editor: Button integration for easy adding the gallery tag with options
* Language support
* Upload tab integration: You have access to all pictures via the upload tab
* Import folder with images tab integration: You have access to all server directories via the import tab
* Sort images feature


== Changelog ==

=V0.2.9 - 17.08.2009=
* NEW : Import images from folder
* Added : Button "FlAGallery" on HTML Editor panel, even if Visual Editor is disabled
* Added : International Skin to display any language in the flash
* Added : Loader for News Box on Overview page
* Added : To display all galleries in the album, added parameter value "all", e.g.: gid=all
* Changed : Shortcode 'album' replaced with 'flagallery', becouse of conflict with NextGEN Gallery 
* Bugfix : Fix for Upload Images button on Overview page
* Bugfix : Fixed conflict with NextGEN Gallery (creating thumbnails cause error)


== Credits ==

Copyright 2009 by Sergey Pasyuk & CodEasily.com DevTeam

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


== Installation ==

1.	Upload the files to wp-content/plugins/flash-album-gallery.
2.	Activate the plugin.
3.	Add a gallery and upload some images (the main gallery folder must have write permission).
4.	Go to your post/page an enter the tag '[flagallery gid=X name="ALBUM TITLE"]', where X - gallery IDs separated by comma;  ALBUM TITLE - title of your album (default 'Gallery'). Easy way is click FlAGallery button on the Editor panel.
5.	If you would like to use additional Flash Skins (only a option), go to <a href="http://photogallerycreator.com/2009/07/skins-for-flash-album-gallery/" title="Flash Skins">Flash Skins</a>, download the skin and upload the file through Skins page in Wordpress admin panel.

	See more tags in the FAQ section

That's it ... Have fun


== Screenshots ==

1. Screenshot Manage Gallery 
2. Screenshot Sort Gallery
3. Screenshot Add / Change Skins
4. Screenshot Options Page
5. Screenshot Flash Album Gallery


== Frequently Asked Questions ==

1. The slideshow didn't work. I only see the message "The Flash Player and a browser with Javascript  needed..", but everything is installed and activated.
Make sure you have the following in your template. (It's in the original WP index.php template, but if you're creating your own, you may have forgotten to include it) :
<?php wp_head(); ?>
That line would go in between your <HEAD> </HEAD> tags

2. When I try to activate the plugin I get the message : "Plugin could not be activated because it triggered a fatal error."
This problem could happened if you have a low memory_limit in your php environment and a lot of plugins installed. For a simple test deactivate all other plugins and try then to activate GRAND Flash Album Gallery again. Please check also if you have a minimum memory_limit of 16Mbyte (as much as possible).

3. I get the message "Fatal error: Allowed memory size of xxx bytes exhausted" or get the "Error: Exceed Memory limit.". What does this means ?
This problem could happened if you have a low memory_limit in your php environment or you have a very large image (resolution, not size). The memory limit sets the maximum amount of memory in bytes that a script is allowed to allocate. You can either lower the resolution of your images or increase the PHP Memory limit (via ini_set, php.ini or htaccess). If you didn't know how to do that, please contact your web hoster.


**Read as startup :** http://codeasily.com/wordpress-plugins/flash-album-gallery/flag-review/

When writing a page/post, you can use the follow tag:

[flagallery gid=x name="ALBUM TITLE" w=width h=height]    -   (e.g.: [flagallery gid=1,3,5,6 name="New Year 2009" w=100% h=400] )
Use 'gid=all' for including all galleries in the album   -   (e.g.: [flagallery gid=all name="ALBUM TITLE"] )

Live Demo : http://codeasily.com/wordpress-plugins/flash-album-gallery/flag/

**A further FAQ you can found here :** http://codeasily.com/wordpress-plugins/flash-album-gallery/faq/
