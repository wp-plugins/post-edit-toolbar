=== Post Edit Toolbar ===
Contributors: WebYourBusiness
Donate link: http://webyourbusiness.com/post-edit-toolbar
Tags: edit post, toolbar, admin bar, list posts, list drafts 
Requires at least: 3.0.1
Tested up to: 3.9
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a dropdown to the WordPress toolbar to add a new post and quickly access your most recent drafts + posts. 

== Description ==

Adds a dropdown 'Post List' to the WordPress toolbar of the most recently edited drafts + posts. This gives you quick access to a work in progress, or a post that was recently published.

This plugin is designed for publishers who make extensive use of posts and of particular use to those of us who get interrupted while composing posts!

Currently it only displays an add-new post list, your 5 most recent drafts and 5 most recent published posts

== Installation ===

1. Upload `post-edit-toolbar.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. A dropdown will appear in the WordPress toolbar titled 'Post List'.

== Screenshots ==

1. The dropdown shows the add new post, 2 most recently edited drafter, and the 5 most recently published posts.

== Frequently Asked Questions ==

For FAQs - see the post-edit-toolbar page at: http://webyourbusiness.com/post-edit-toolbar

== Upgrade Notice ==

= 1.3.2 =
rushed out 1.3.2 because installation onto a system with page-edit-toolbar caused a duplicate function name which could break your site - changed our function names to fix
= 1.3.1 =
fixed broken page-edit-toolbar functionality where hierarchical caused less than 5 pages to be returned if there were not 5 top-level pages, but there were sub-pages

= 1.3.0 =
Rolled in the functionality of Page-Edit-Toolbar - so I could add and "Add Page" link at the top of that menu, as per Post-Edit-Toolbar (and drafts)

= 1.2 =
Added an 'Add drafts' - if you get interrupted while composing a post, you'll love access to drafts that I added in 1.2

== Changelog ==

= 0.1 =
* Initial plugin submission
= 1.0 =
* initial release - based on Page Edit Toolbar by Jeremy Green
= 1.1 =
* bug fix + add new post - added 'Add New Post' to top of list and fixed incorrect variables passed to get_posts() - they differ from get_pages()
= 1.1.1 =
* updated image included in the assets folder to be post-edit-tool-bar installed, not the page-edit-toolbar used as initial source
= 1.2 = 
* added drafts + separators
= 1.2.2 =
* added truncation of long post + draft titles so menu does not get too long
= 1.3.0 =
Rolled in page-edit-toolbar functionality.
= 1.3.1 =
fixed broken page-edit-toolbar functionality where - hierarchical caused less than 5 pages to be returned
= 1.3.2 =
1.3.2 - found a problem with this new version if you had page-edit-toolbar installed - changed function names to resolve
