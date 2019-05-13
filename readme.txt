=== redirection.io ===
Contributors: redirectionio
Donate link: https://redirection.io/
Tags: redirection, http, seo, redirect, search engine, meta tags, redirection.io
Requires at least: 4.0
Tested up to: 5.2
Stable tag: trunk
Requires PHP: 7.1
License: MIT
License URI: https://opensource.org/licenses/MIT

This plugin integrates redirection.io with your Wordpress website.

== Description ==

The redirection.io plugin works in combination with [redirection.io](https://redirection.io), a pwerful URL redirection manager (and a lot more).

With redirection.io, you can:

 * manage HTTP redirections ;
 * log your traffic and check HTTP errors ;
 * create "Page not found" responses ;
 * override SEO properties ;
 * inject rich data into pages ;
 * etc.

== Installation ==

= Automatic installation =

*The easy way*

1. Go to your WP admin area in `Plugins > Add new`
2. Type `redirection.io` in `Search Plugins` box
3. Click `Install Now` button
4. Click `Activate` button

You can then find the plugin configuration page in your WP admin area under `Settings > redirection.io`

= zip archive install =

We provide a zip archive of the plugin [in our "releases" page](https://github.com/redirectionio/proxy-wordpress/releases).

1. Download the latest release
2. In your WordPress admin area, head to `Plugins > Add New`
3. Click the "Upload Plugin" button
4. Choose the zip archive and click the "Install Now" button

You can then find the plugin configuration page in your WP admin area under `Settings > redirection.io`

= Manual installation =

This install procedure allows to install the plugin from this repository's code.

1. Donwload the zip archive of this repository's code: [https://github.com/redirectionio/proxy-wordpress/archive/master.zip](https://github.com/redirectionio/proxy-wordpress/archive/master.zip)
2. Extract this archive, and navigate in the extracted folder with a shell
3. Install [Composer](https://getcomposer.org/) dependencies:
```sh
COMPOSER_MIRROR_PATH_REPOS=1 composer install
```
4. Move this `wordpress` directory in `wp-content/plugins/` and rename it `redirectionio`
5. Go to your WordPress admin area in `Plugins > Installed Plugins`
6. Click `Activate` link in redirection.io row

You can then find the plugin configuration page in your WP admin area under `Settings > redirection.io`


== Frequently Asked Questions ==

= Do I need an external account? =

Yes, a [redirection.io](https://redirection.io/) account is required. You can create one for free here:

= How to setup redirection rules? =

Creating redirection rules (or SEO overrides, or meta tags injection, etc.) can be done using our "manager", a dedicated tool. You can find all the details in our documentation: [https://redirection.io/documentation/user-documentation/create-a-rule#how-to-create-a-rule](https://redirection.io/documentation/user-documentation/create-a-rule#how-to-create-a-rule).

== Screenshots ==

1. Website dashboard
2. Rule creation form

== Changelog ==

= 0.2 =

This is the initial version.
