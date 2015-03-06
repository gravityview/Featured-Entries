=== GravityView - Featured Entries Extension ===
Tags: gravityview
Requires at least: 3.3
Tested up to: 3.9.1
Stable tag: trunk
Contributors: katzwebservices, ryanduff
License: GPL 3 or higher

Enable Featured Entries in GravityView.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. To feature an entry, "Star" it in Gravity Forms' Entries view

== Changelog ==

= 1.1 on March 5, 2015 =
* Fixed: Inaccurate counts on pages without featured entries
* Modified: Moved `GravityView_Featured_Entries` class to external file
* Updated: Hungarian translation. Thanks, [@dbalage](https://www.transifex.com/accounts/profile/dbalage/)!

= 1.0.6 on December 12 =
* Fixed: Not showing entries when all entries were featured
* Fixed: Flush GravityView cache when entry is starred or un-starred

= 1.0.5 =
* Add styling support for DataTables (Requires DataTables Extension Version 1.2+)
* Updated some functions to work better with latest versions of GravityView
* Added Dutch translation (thanks [@erikvanbeek](https://www.transifex.com/accounts/profile/erikvanbeek/)!)
* Added Turkish translation (thanks, [@suhakaralar](https://www.transifex.com/accounts/profile/suhakaralar/)!)

= 1.0.4 =
* Use different filter to modify pagination, changing just the numbers, not the text

= 1.0.3 =
* Support existing search filters
* Add `gravityview_featured_entries_always_show` filter, which allows override of default behavior, which is to respect search queries.

= 1.0.2 =
* Fixed entry pagination
* Code cleanup

= 1.0.1 =
* Added translations
* Added `gravityview_featured_entries_enable` filter in the `featured_class()` method
* Moved CSS to `/assets/css/`
* Namespaced CSS class
* Added tooltip content
* Modified required GravityView version
* Added readme.txt

= 1.0 =
* From Ryan