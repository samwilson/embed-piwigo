=== Embed Piwigo ===
Contributors: samwilson
Donate link: https://piwigo.org/donate
Tags: piwigo, photos, embed, embeds
Requires at least: 4.7
Tested up to: 4.9
Requires PHP: 5.6
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The **Embed Pwigo** plugin adds support for embedding photos from whitelisted Piwigo websites.

[![Total Downloads](https://img.shields.io/wordpress/plugin/dt/embed-piwigo.svg?style=flat-square)]()
[![WordPress rating](https://img.shields.io/wordpress/plugin/r/embed-piwigo.svg?style=flat-square)]()
[![Latest Stable Version](https://img.shields.io/wordpress/plugin/v/embed-piwigo.svg?style=flat-square)](https://wordpress.org/plugins/embed-piwigo)
[![WordPress version](https://img.shields.io/wordpress/v/embed-piwigo.svg?style=flat-square)]()
[![License](https://img.shields.io/github/license/samwilson/embed-piwigo.svg?style=flat-square)](https://github.com/samwilson/embed-piwigo/blob/master/LICENSE.txt)
[![Build Status](https://travis-ci.org/samwilson/embed-piwigo.svg?branch=master)](https://travis-ci.org/samwilson/embed-piwigo)

== Description ==

The Embed Piwigo plugin adds support for embedding photos from whitelisted [Piwigo](https://piwigo.org/) websites.

This means that you can add the URL (a.k.a. 'web address') of a photo in a Piwigo site to a WordPress post or page, and a medium-sized, centered image will be inserted in its place.

The standard WordPress captioning system will be used (as for the [Caption Shortcode](https://codex.wordpress.org/Caption_Shortcode)), with the photo's title, date, and description added if they're available for a given image.

For example, if you have `https://piwigo.org/demo` in your Piwigo site URLs list, and add the following URL on its own line in a post or page on your blog:

    https://piwigo.org/demo/picture.php?/1382/category/111

then a photo of a mountain in Kerzers will be inserted, with the caption *"Colline de Chiètres (May 6, 2016)"*. The date format will match whatever your WordPress installation is configured to use.

== Installation ==

Install this plugin in the normal way.

Then go to the "Settings" → "Writing" page in the WordPress admin area, and list your Piwigo site URLs in the new "Piwigo site URLs" field.

Note that trailing slashes on the URLs do not matter, and can be added or not as you prefer.

== Frequently Asked Questions ==

= Does this modify the database? =

A single option is added, called `embed-piwigo-urls`, and no modifications are made to the database structure.

== Changelog ==

= 1.0.0 =
* First stable release.
* Documentation improvements.

= 0.4.0 =
* Add more metadata into captions.

= 0.3.0 =
* Add caching.

= 0.1.0 =
* Initial beta release.

== Upgrade Notice ==

Nothing unusual need be done on upgrade.
