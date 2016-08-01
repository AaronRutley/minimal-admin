=== Minimal Admin ===
Contributors: AaronRutley
Tags: admin,minimal,dashboard,cms
Requires at least: 3.4.2
Tested up to: 4.6.0
Stable tag: 2.3.0
License: GPL2+

Very simple plugin to hide non essential wp-admin functionality

== Description ==
Very simple plugin to hide 'non essential' wp-admin functionality.
This plugin is intended for select client projects where the client is an editor.

= Features include: =
* Overwrites a range of default wp-admin CSS with minimal styles
* Hides the dashboard and directs admin's or editor users to the edit pages screen
* Hides non essential menu items & separators
* Hides the WordPress admin bar (only front end)
* Site link in the admin bar opens in new window
* Local Development - admin bar colour change
* Local Development - project quicklinks

= Options to: =
* Hide Posts from the WordPress menu
* Hide screen options tab and help tab

= Works well with the following plugins: =
* Yoast SEO: Hides SEO columns from the edit page / edit posts screens
* Gravity Forms: Grants editor (client) access to manage Gravity Forms
* Advanced Custom Fields Pro : Minimal field group listings
* All in One SEO Pack
* Simple Page Ordering
* Admin Collapse Sub Pages

== Installation ==

1. Upload `Minimal Admin` to `/wp-content/plugins/`
2. Activate the Plugin via the plugins menu


== FAQs ==

= How do I enable the quicklinks dropdown ? =

So your project links appear in the quicklinks dropdown under the W icon in the admin bar you need to:

First, define your local URL in wp-config.php or functions.php as this only works on local.

```
define('LOCAL_URL', 'http://minimaladmin.dev');
```

Secondly, add a function similar to the following to your theme's functions.php

```
function minimal_admin_project_links() {
	$minimal_admin_project_links = array(
		array("Local","http://localurl.com"),
		array("Staging","http://stagingurl.com"),
		array("Trello","http://trellourl.com"),
		array("Git Repo","http://gitrepourl.com")
	);
	return $minimal_admin_project_links;
}
```

== Screenshots ==
1. Before (when logged in as an editor / client)
2. After (when logged in an an editor / client)

== Changelog ==

= 2.3.0 =
* Local Development - Project quicklinks feature added
* Local Development - Admin bar colour change
* Minor CSS tweaks for Yoast SEO 3.4
* Minor CSS tweaks for ACF Pro
* Minor CSS tweaks for WP 4.6
* SVG icons for Admin Collapse Sub Pages

= 2.2.0 =
* Compatibility with WP Rocket
* Compatibility with All in One SEO Pack Pro
* Minor CSS tweaks for WP 4.3 compatibility
* Minor CSS tweaks for ACF 5.2.9 compatibility
* Minor CSS tweaks to keep Plugins list screen minimal
* Tested against wptest.io sample data

= 2.1.0 =
* Minor CSS tweaks for WordPress v4.0+ compatibility
* Minor CSS tweaks for ACF v5.0+ Pro compatibility
* Minor CSS bug fix for Firefox on lists of pages
* Minor CSS tweaks for WordPress multisite
* Remove howdy from admin menu bar
* Fix for redirection error for some user roles

= 2.0.1 =
* Bug fix for Gravity Forms / Backup Buddy menu conflict

= 2.0.0 =
* Major Update which includes compatibility with WordPress 3.7, MP6 & various other plugins.

= 1.0.1 =
* Original Commit
