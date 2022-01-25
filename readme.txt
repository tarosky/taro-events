=== Taro Events ===

Tags: events, posts
Contributors: tarosky, ko31
Tested up to: 5.8  
Requires at least: 5.4  
Requires PHP: 5.6  
Stable Tag: 
License: GPLv3 or later  
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

A WordPress plugin for creating events.

== Description ==

This plugin serves the following features.

- Add a custom post type and custom taxonomies for event
- Filtering form for event archive
- Add stuructured data for event single pages

= Customization =

 Filtering form 

To implement a filtering form of an event archive, you can use the shortcode `[taro-event-filter-form]`.

Also, you can call function `taro_events_get_filter_form` from anywhere in the theme's template files.

To override the filtering form, put the template file `taro_event_filter_form.php` in your themes directory.

 Hooks 

Many hooks are also available. Search your plugin direcoty with `'taro_events_'` and you can find them easily :)

 Functions 

See `inludes/functions.php` and you can find useful template tags and functions.

== Installation ==

= From Plugin Repository =

WIP

= From Github =

See [releases](https://github.com/tarosky/taro-events/releases).

== FAQ ==

= Where can I get supported? =

Please create new ticket on support forum.

= How can I contribute? =

Create a new [issue](https://github.com/tarosky/taro-events/issues) or send [pull requests](https://github.com/tarosky/taro-events/pulls).

== Changelog ==

= 1.0.0 =

* First release.
