=== Plugin Name ===
Contributors: clinton@insightdesigns.com
Donate link: 
Tags: 
Requires at least: 3.0.1, Node 6.4
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Compile javasript files using Webpack

== Description ==

Recursively sorts files from the back end into a dependency profile before compiling qualified files from the WP scripts object.  The plugin will not compile core files, files without a src supplied or improperly registered files.  Requires Node ^6.4.

== Installation ==

Upload the pluging to your plugins directory and activate.
Go to Settings > Network Nanny, click "Compile".
This will begin generating a dependency profile and may take a few minutes depending on the amount of scripts your site requires.
Once the profile is complete, the display will print out the list.  Hit "Save Profile".  Your site will now dynamically compile JS scripts into a Webpack bundle.js file.  You can monitor output in Network-Nanny/logs/runtime.log.  A list of files compiled will be available along with any errors.

== Frequently Asked Questions ==

= It's not compiling all of my scripts? =

This plugin only handles non-core files.  Many files are skipped over due to issues with compilation.  It also will only compile files properly registered and including a src attribute in the WP Scripts Object.  If files do not contain the proper data or are improperly registered, they are skipped due to inaccurate dependency negotiation.

= It's not writing the bundle file? =

Check the error log first, details might be there: Network-Nanny/logs/runtime.log.  If the log file is empty or doesn't exist, check your file permissions.  Network-Nanny/logs/runtime.log, Network-Nanny/public/js/dist and Network-Nanny/public/js/app.js all must be writable to the server for the plugin to work correctly.

== Changelog ==

= 1.0.0 =
* Initial Version.