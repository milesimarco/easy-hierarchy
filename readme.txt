=== Easy Hierarchy – Tree View for Pages & Custom Post Types ===
Contributors: Milmor
Tags: hierarchy, page tree, parent-child, custom post types, page management
Donate link: https://www.paypal.me/milesimarco
Requires at least: 4.6
Tested up to: 7.2
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
- Quick search, status filter and sorting (title, newest, oldest, recently modified) in the tree, keeping the hierarchy.
- Shows publish date, last revision date and status for each item, with direct Edit and View links.
- Highlights the number of children for each parent.
- **Adds a "Hierarchy" column to the list screen:**
  - See the full parent hierarchy for each item, with clickable links to filter by parent.
  - Instantly view the number of children, with quick filtering.
- Parent filter dropdown with only the items that have children.
- **Settings > Easy Hierarchy:** see all hierarchical post types and open their list or tree.
- Fully integrated with the WordPress admin interface.

== Installation ==

1. Install the plugin via the WordPress.org plugin directory or upload the files to your server.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Visit Pages > Page Tree, or Settings > Easy Hierarchy to see all supported post types.

== Frequently Asked Questions ==

= Where do I find the new features? =
Go to Pages > Page Tree for a visual tree of your pages, with search and quick actions. Every hierarchical post type gets its own Tree submenu. Settings > Easy Hierarchy lists them all.

= Does this plugin work with custom post types? =
Yes. Since version 3.0 Easy Hierarchy works with every hierarchical post type that has an admin screen. Developers can change the list with the `easy_hierarchy_post_types` filter.

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
* Added: Support for all hierarchical post types
* Added: Settings page listing hierarchical post types, with links to list and tree
* Added: Status filter and sorting in the tree
* Improved: Tree, counts and parent filter include non-published items
* Improved: Parent filter shows every item with children, at any level
* Improved: Better search and faster loading on large sites
* Fixed: Filter, pagination and Screen Options issues
* Security: Escaped output in the admin
* Tested with WP 7.2

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
Easy Hierarchy now works with every hierarchical post type and adds status filter and sorting in the tree. Also includes filter fixes and security hardening.
