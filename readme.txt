=== Easy Hierarchy – Monitor Page Tree & Custom Post Types ===
Contributors: Milmor
Tags: hierarchy, page tree, parent-child, custom post types, page management
Donate link: https://www.paypal.me/milesimarco
Requires at least: 4.6
Tested up to: 7.2
Requires PHP: 7.4
Stable tag: 3.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Your whole site, mapped. A clear view of complex WordPress sites: structure, status and quick actions for pages and custom post types.

== Description ==

**Your whole site, mapped.**

Easy Hierarchy is made for people who manage large, complex WordPress sites: hundreds of pages, deep hierarchies, many editors and custom post types.

Over time, the structure gets hard to follow. Pages end up in the wrong place, drafts and scheduled content are forgotten, and finding things means scrolling through flat lists. Easy Hierarchy gives you a clear view of your content, so you can see how the site is organized, check what needs attention and reach any item in one click.

**Who is it for**

- Public administrations and schools
- Universities and large organizations
- Agencies managing client sites
- Documentation sites, knowledge bases and intranets

**See the structure**

- A tree view for Pages and every hierarchical post type (for example Pages > Page Tree).
- A Hierarchy column in the list screen, with the full parent path of each item.
- The number of children of each item, in the tree and in the list.
- Settings > Easy Hierarchy lists all hierarchical post types, with links to their list and tree.

**Monitor what needs attention**

- Drafts, pending, private and scheduled items are shown in the tree.
- Filter the tree by status, keeping the parents visible for context.
- Sort by title, newest, oldest or recently modified, without losing the hierarchy.
- Publish date, last revision and status for each item.

**Navigate and act**

- Search the tree by title.
- Filter the list by parent, with every item that has children at any level.
- Edit and View links for each item.
- Fast on large sites, fully integrated with the WordPress admin.

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

1. Page Tree: the whole hierarchy with status, dates and quick Edit / View links
2. Status filter and sorting in the tree, with parents kept visible for context
3. Hierarchy column and parent filter in the Pages list
4. Tree view for a custom post type (Docs)
5. Settings > Easy Hierarchy with all hierarchical post types

== Changelog ==

= 3.0.1 2026-10-04 =
* Minor changes

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
