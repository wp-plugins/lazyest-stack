=== Lazyest Stack ===
Contributors: macbrink
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1257529
Tags: image,gallery,stacked,css3,jquery,lazyest
Requires at least: 2.9
Tested up to: 3.5
Stable tag: 1.1.2

This plugin adds a beautiful photo stack gallery with jQuery and CSS3 to Lazyest Gallery.

== Description ==

Based on [Beautiful Photo Stack Gallery with jQuery and CSS3](http://tympanus.net/codrops/2010/06/27/beautiful-photo-stack-gallery-with-jquery-and-css3/) 

This plugin requires [Lazyest Gallery](http://wordpress.org/extend/plugins/lazyest-gallery/) version 1.0 or higher.

The plugin shows the albums as a slider, and when an album is chosen, it shows the images of that album as a photo stack. In the photo stack view, you can browse through the images by putting the top most image behind all the stack with a slick animation.

The plugin uses jQuery and CSS3 properties for the rotated image effect. It also uses the webkit-box-reflect property in order to mirror the boxes in the album view – check out the [demo](http://brimosoft.nl/stacked/) in Google Chrome or Apple Safari to see this wonderful effect.

== Installation ==

0. Install and configure [Lazyest Gallery](http://wordpress.org/extend/plugins/lazyest-gallery/)
1. Upload the contents of the zip file to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the `[lg_stack]` shortcode to a page to show your Lazyest Gallery as a Photo Stack

== Frequently Asked Questions ==

= What version of Lazyest Gallery is required? =

You need at least Lazyest Gallery version 1.0

== Changelog ==

= 1.1.1 =
* Bug Fix: Do not deactivate on update cycle

= 1.1 =
* Bug fix: jQuery reference
* Changed: Compresssed javascript
* Added: Plugin deactivates itself in Lazyest Gallery is not activated

= 1.0 = 
* First release of lazyest-stack plugin

== Upgrade Notice ==

= 1.1.1 =
* Fixed a bug where Lazyest Stack gets deactivated on an update of Lazyest Gallery

== Screenshots ==

1. The stacked photo album

== License ==
* Copyright (c) 2011 - Marcel Brinkkemper 
* Lazyest Stack is released under the GPLv2. See license.txt 