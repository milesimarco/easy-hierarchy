=== Easy Hierarchy ===
Contributors: Milmor
Tags: hierarchy, page tree, parent-child, custom post types, page management
Donate link: https://www.paypal.me/milesimarco
Requires at least: 4.6
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Hierarchies made easy, for pages and any hierarchical post type.

== Description ==

Managing complex hierarchies in WordPress can be a challenge. Easy Hierarchy adds a visual tree, a Hierarchy column and a parent filter to the admin, for Pages and for every hierarchical custom post type (docs, products, chapters and more).

**Features:**
- Adds a "Tree" submenu (for example Pages > Page Tree) with a full visual hierarchy overview.
- Shows drafts, pending, private and scheduled items too.
- Quick search box to filter items by title.
- Shows publish date, last revision date and status for each item, with direct Edit and View links.
- Highlights the number of children for each parent.
- **Adds a "Hierarchy" column to the list screen:**
  - See the full parent hierarchy for each item, with clickable links to filter by parent.
  - Instantly view the number of children, with quick filtering.
- Parent filter dropdown with only the items that have children.
- **Settings > Easy Hierarchy:** see all hierarchical post types, open their list or tree, and choose where Easy Hierarchy is active.
- Fully integrated with the WordPress admin interface.

== Installation ==

1. Install the plugin via the WordPress.org plugin directory or upload the files to your server.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Visit Pages > Page Tree, or Settings > Easy Hierarchy to see all supported post types.

== Frequently Asked Questions ==

= Where do I find the new features? =
Go to Pages > Page Tree for a visual tree of your pages, with search and quick actions. Every hierarchical post type gets its own Tree submenu. Settings > Easy Hierarchy lists them all.

= Does this plugin work with custom post types? =
Yes. Since version 3.0 Easy Hierarchy works with every hierarchical post type that has an admin screen. You can turn it off for single post types in Settings > Easy Hierarchy. Developers can also use the `easy_hierarchy_post_types` filter.

= Where is the tree of a post type that has no menu of its own? =
Some plugins place their post types inside another menu. In that case open the tree from Settings > Easy Hierarchy.

= Can I edit or view items from the tree? =
Yes! Each item in the tree has Edit and View buttons for quick access.

= What does the "Hierarchy" column do? =
The "Hierarchy" column in the list screen shows the full parent hierarchy for each item, with clickable links to filter by parent. It also displays the number of children for each item, making it easy to navigate and manage complex hierarchies.

== Screenshots ==

1. Page Tree screen and Hierarchy column in the Pages list
2. Settings > Easy Hierarchy with all hierarchical post types

== Changelog ==

= 3.0 2026-10-04 =
* Added: Support for all hierarchical post types (tree, Hierarchy column and parent filter)
* Added: Settings > Easy Hierarchy page to see all hierarchical post types, open their list or tree, and turn the plugin on or off for each one
* Added: Settings link in the Plugins list
* Tested with WP 7.1
* Improved: Page Tree now shows drafts, pending, private and scheduled pages
* Improved: Parent filter lists only pages with subpages, including non-published ones
* Improved: Search shows a message when no pages match
* Improved: Faster Page Tree, parent filter and Hierarchy column on sites with many pages
* Improved: Subpage counts include drafts, pending, private and scheduled pages
* Fixed: Hierarchy column styles no longer appear in Screen Options
* Fixed: Parent filter no longer affects other queries on the Pages screen
* Fixed: Filter links now reset pagination
* Security: Escaped page titles and labels in the admin

= 2.1 2026-05-13 =
* Tested with WP 7.0
* Improved: User interface and hierarchy overview

= 2.0.1 2025-05-29 =
* Added: Page Tree view for visualizing page hierarchy
* Added: Quick search/filter for pages in the tree
* Added: Edit and View actions for each page in the tree
* Improved: User interface and hierarchy overview
* Fixed: Minor bugs

= 1.2 20220901 =
* Compatibility check
* Minor changes

= 1.1 20201003 =
* Minor improvements
* Better description and assets
* Added translation system

= 1.0 20200921 =
* First release

== Upgrade Notice ==

= 3.0 =
Easy Hierarchy now works with every hierarchical post type, with a new settings page. Also includes non-published items in the tree, filter fixes and security hardening.
