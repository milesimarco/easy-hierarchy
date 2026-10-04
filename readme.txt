=== Easy Hierarchy ===
Contributors: Milmor
Tags: hierarchy, page-tree, admin, parent-child, page-management
Donate link: https://www.paypal.me/milesimarco
Requires at least: 4.6
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Hierarchies made easy!

== Description ==

Managing complex page hierarchies in WordPress can be a challenge. Easy Hierarchy streamlines your workflow by adding powerful tools to the admin area, making it simple to filter top-level pages and visualize parent-child relationships for every item.

**Features:**
- Adds a "Page Tree" submenu under Pages for a full visual hierarchy overview.
- Displays parent and child relationships in a nested tree, including drafts, pending, private and scheduled pages.
- Quick search box to filter pages by title.
- Shows publish date, last revision date, and status for each page.
- Direct Edit and View links for every page in the tree.
- Highlights number of child pages for each parent.
- **Adds a "Hierarchy" column to the Pages list:**  
  - See the full parent hierarchy for each page, with clickable links to filter by parent.
  - Instantly view the number of child pages for any page, with quick filtering.
- Fully integrated with the WordPress admin interface.

== Installation ==

1. Install the plugin via the WordPress.org plugin directory or upload the files to your server.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Visit the Pages > Page Tree screen to see the new hierarchy management features.

== Frequently Asked Questions ==

= Where do I find the new features? =
Go to the Pages section in your WordPress admin and click on "Page Tree" in the submenu. You’ll see a visual tree of your pages, with search and quick actions.

= Does this plugin work with custom post types? =
Currently, Easy Hierarchy is designed for the built-in "Pages" post type. Support for custom post types may be added in future versions.

= Can I edit or view pages from the tree? =
Yes! Each page in the tree has Edit and View buttons for quick access.

= What does the "Hierarchy" column do? =
The "Hierarchy" column in the Pages list shows the full parent hierarchy for each page, with clickable links to filter by parent. It also displays the number of child pages for each page, making it easy to navigate and manage complex hierarchies.

== Screenshots ==

1. Page Tree screen and Hierarchy column in the Pages list

== Changelog ==

= 2.2 2026-10-04 =
* Tested with WP 7.1
* Improved: Page Tree now shows drafts, pending, private and scheduled pages
* Improved: Parent filter includes non-published parent pages
* Improved: Search shows a message when no pages match
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

= 2.2 =
Page Tree now includes non-published pages, plus filter fixes and security hardening.
