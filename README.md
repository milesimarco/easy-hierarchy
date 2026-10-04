# Easy Hierarchy

Your whole site, mapped. A clear view of complex WordPress sites: structure, status and quick actions for pages and custom post types.

[![WordPress plugin](https://img.shields.io/wordpress/plugin/v/easy-hierarchy.svg)](https://wordpress.org/plugins/easy-hierarchy/)
[![Active Installs](https://img.shields.io/wordpress/plugin/installs/easy-hierarchy.svg)](https://wordpress.org/plugins/easy-hierarchy/)
[![Downloads](https://img.shields.io/wordpress/plugin/dt/easy-hierarchy.svg)](https://wordpress.org/plugins/easy-hierarchy/)
[![Tested up to](https://img.shields.io/wordpress/plugin/tested/easy-hierarchy.svg)](https://wordpress.org/plugins/easy-hierarchy/)
[![Rating](https://img.shields.io/wordpress/plugin/rating/easy-hierarchy.svg)](https://wordpress.org/plugins/easy-hierarchy/#reviews)
[![License](https://img.shields.io/badge/license-GPLv2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

![Easy Hierarchy](.wordpress-org/banner-1544x500.png)

Easy Hierarchy is made for people who manage large, complex WordPress sites: hundreds of pages, deep hierarchies, many editors and custom post types.

Over time, the structure gets hard to follow. Pages end up in the wrong place, drafts and scheduled content are forgotten, and finding things means scrolling through flat lists. Easy Hierarchy gives you a clear view of your content, so you can see how the site is organized, check what needs attention and reach any item in one click.

**Made for** public administrations and schools, universities and large organizations, agencies managing client sites, documentation sites, knowledge bases and intranets.

## Features

### See the structure

- **Tree view** for Pages and every hierarchical post type (for example **Pages → Page Tree**)
- **Hierarchy column** in the list screen, with the full parent path of each item
- Number of children of each item, in the tree and in the list
- **Settings → Easy Hierarchy**: all hierarchical post types, with links to their list and tree

### Monitor what needs attention

- Drafts, pending, private and scheduled items in the tree
- Status filter that keeps the parents visible for context
- Sorting by title, newest, oldest or recently modified, without losing the hierarchy
- Publish date, last revision and status for each item

### Navigate and act

- Search the tree by title
- **Parent filter** in the list, with every item that has children at any level
- Edit and View links for each item
- Fast on large sites: each screen loads the hierarchy with a single query

## Requirements

- WordPress 4.6+
- PHP 7.4+

## Installation

From the WordPress dashboard: **Plugins → Add New → search for "Easy Hierarchy"**.

After activation, open **Pages → Page Tree**, or **Settings → Easy Hierarchy** to see all supported post types.

## For developers

| Hook | Type | Description |
| --- | --- | --- |
| `easy_hierarchy_post_types` | Filter | Array of post type objects where Easy Hierarchy is active. By default, all hierarchical post types with an admin screen |

```php
// Hide Easy Hierarchy for a post type
add_filter('easy_hierarchy_post_types', function ($post_types) {
    unset($post_types['my_post_type']);
    return $post_types;
});
```

## Contributing

Issues and pull requests are welcome.

## Links

- [Plugin page on WordPress.org](https://wordpress.org/plugins/easy-hierarchy/)
- [Support forum](https://wordpress.org/support/plugin/easy-hierarchy/)
- [Changelog](readme.txt)

## Credits

Copyright © 2020-2026 **Marco Milesi**
[www.marcomilesi.com](https://www.marcomilesi.com)
