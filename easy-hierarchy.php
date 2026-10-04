<?php
/*
Plugin Name: Easy Hierarchy – Monitor Page Tree & Custom Post Types
Description: Makes WordPress page hierarchy management easy and intuitive with enhanced filtering and visual hierarchy indicators, for pages and any hierarchical post type
Version: 3.0.1
Author: Marco Milesi
Author URI: https://www.marcomilesi.com
Requires at least: 4.6
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: easy-hierarchy
*/

if (!defined('ABSPATH')) exit;

class Easy_Hierarchy_Plugin {
    // Statuses shown in the tree, in the parent filter and in the counts
    const PAGE_STATUSES = 'publish,draft,pending,private,future';
    const SETTINGS_SLUG = 'easy-hierarchy';

    // Supported post types and parent => children map per post type, loaded once per request
    private $post_types = null;
    private $children = [];
    private $descendants = [];
    private $tree_index = 0;

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_pages']);
        add_action('admin_init', [$this, 'register_column_hooks']);
        add_action('admin_notices', [$this, 'show_subpages_filter_notice']);
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_settings_link']);

        add_filter('parse_query', [$this, 'filter_parent_pages']);
        add_action('restrict_manage_posts', [$this, 'parent_pages_dropdown']);
        add_action('manage_pages_custom_column', [$this, 'render_hierarchy_columns'], 10, 2);
        add_action('admin_head-edit.php', [$this, 'hierarchy_columns_style']);
    }

    // === Post types ===

    /**
     * All hierarchical post types with an admin UI, keyed by name.
     */
    public function get_post_types() {
        if ($this->post_types === null) {
            $post_types = get_post_types(['hierarchical' => true, 'show_ui' => true], 'objects');
            $this->post_types = apply_filters('easy_hierarchy_post_types', $post_types);
        }
        return $this->post_types;
    }

    private function is_supported($post_type) {
        return is_string($post_type) && array_key_exists($post_type, $this->get_post_types());
    }

    private function tree_slug($post_type) {
        // Keep the original slug for pages so existing bookmarks keep working
        return $post_type === 'page' ? 'pages-hierarchy' : $post_type . '-hierarchy';
    }

    private function tree_url($post_type) {
        return menu_page_url($this->tree_slug($post_type), false);
    }

    // === Hierarchy data ===

    private function get_children($post_type, $parent_id) {
        if (!isset($this->children[$post_type])) {
            $this->children[$post_type] = [];
            $posts = get_pages([
                'post_type' => $post_type,
                'post_status' => self::PAGE_STATUSES,
                'sort_column' => 'menu_order,post_title',
                'hierarchical' => 0,
            ]);
            foreach ($posts as $post) {
                $this->children[$post_type][$post->post_parent][] = $post;
            }
        }
        return isset($this->children[$post_type][$parent_id]) ? $this->children[$post_type][$parent_id] : [];
    }

    private function count_descendants($post_type, $parent_id) {
        if (!isset($this->descendants[$post_type][$parent_id])) {
            $count = 0;
            foreach ($this->get_children($post_type, $parent_id) as $child) {
                $count += 1 + $this->count_descendants($post_type, $child->ID);
            }
            $this->descendants[$post_type][$parent_id] = $count;
        }
        return $this->descendants[$post_type][$parent_id];
    }

    /**
     * Number of items per status, for the tree status filter.
     */
    private function count_statuses($post_type) {
        $this->get_children($post_type, 0);
        $counts = [];
        foreach ($this->children[$post_type] as $posts) {
            foreach ($posts as $post) {
                $counts[$post->post_status] = isset($counts[$post->post_status]) ? $counts[$post->post_status] + 1 : 1;
            }
        }
        return $counts;
    }

    private function get_title($post) {
        return $post->post_title !== '' ? $post->post_title : __('(no title)', 'default');
    }

    // === Admin pages ===

    public function add_admin_pages() {
        add_options_page(
            __('Easy Hierarchy', 'easy-hierarchy'),
            __('Easy Hierarchy', 'easy-hierarchy'),
            'manage_options',
            self::SETTINGS_SLUG,
            [$this, 'settings_page']
        );

        foreach ($this->get_post_types() as $post_type => $object) {
            // Post types without their own top level menu get a hidden tree page, linked from the settings
            $parent = $object->show_in_menu === true ? 'edit.php?post_type=' . $post_type : 'options.php';
            $title = sprintf(__('%s Tree', 'easy-hierarchy'), $object->labels->singular_name);
            add_submenu_page(
                $parent,
                $title,
                $title,
                $object->cap->edit_posts,
                $this->tree_slug($post_type),
                function () use ($post_type) {
                    $this->tree_page($post_type);
                }
            );
        }
    }

    public function add_settings_link($links) {
        $url = admin_url('options-general.php?page=' . self::SETTINGS_SLUG);
        array_unshift($links, '<a href="' . esc_url($url) . '">' . esc_html__('Settings', 'default') . '</a>');
        return $links;
    }

    public function settings_page() {
        $post_types = $this->get_post_types();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Easy Hierarchy', 'easy-hierarchy'); ?></h1>
            <p><?php esc_html_e('The tree view, the Hierarchy column and the parent filter are available for all these hierarchical post types.', 'easy-hierarchy'); ?></p>
            <table class="widefat striped eh-settings-table">
                <thead>
                    <tr>
                        <th scope="col"><?php esc_html_e('Post type', 'easy-hierarchy'); ?></th>
                        <th scope="col"><?php esc_html_e('Items', 'easy-hierarchy'); ?></th>
                        <th scope="col"><?php esc_html_e('Links', 'easy-hierarchy'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($post_types as $post_type => $object) :
                    $counts = (array) wp_count_posts($post_type);
                    $total = 0;
                    foreach (explode(',', self::PAGE_STATUSES) as $status) {
                        $total += isset($counts[$status]) ? (int) $counts[$status] : 0;
                    }
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($object->labels->name); ?></strong>
                            <br><code><?php echo esc_html($post_type); ?></code>
                        </td>
                        <td><?php echo esc_html(number_format_i18n($total)); ?></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('edit.php?post_type=' . $post_type)); ?>" class="button button-small"><?php echo esc_html($object->labels->all_items); ?></a>
                            <?php if ($this->tree_url($post_type)) : ?>
                                <a href="<?php echo esc_url($this->tree_url($post_type)); ?>" class="button button-small"><?php echo esc_html(sprintf(__('%s Tree', 'easy-hierarchy'), $object->labels->singular_name)); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$post_types) : ?>
                    <tr><td colspan="3"><?php esc_html_e('No hierarchical post types found.', 'easy-hierarchy'); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <style>
            .eh-settings-table { max-width: 900px; }
            .eh-settings-table td, .eh-settings-table th { vertical-align: middle; }
            .eh-settings-table .button + .button { margin-left: 6px; }
        </style>
        <?php
    }

    public function tree_page($post_type) {
        $object = get_post_type_object($post_type);
        $top_posts = $this->get_children($post_type, 0);

        echo '<div class="wrap">';
        echo '<h1>' . esc_html(sprintf(__('%s Hierarchy Overview', 'easy-hierarchy'), $object->labels->name)) . '</h1>';
        ?>
        <?php if ($top_posts) : ?>
        <div class="eh-search-box">
            <label for="eh-page-search" class="screen-reader-text"><?php echo esc_html($object->labels->search_items); ?></label>
            <input type="text" id="eh-page-search" class="regular-text" placeholder="<?php echo esc_attr($object->labels->search_items); ?>">

            <label for="eh-status-filter" class="screen-reader-text"><?php esc_html_e('Filter by status', 'easy-hierarchy'); ?></label>
            <select id="eh-status-filter">
                <option value=""><?php esc_html_e('All statuses', 'easy-hierarchy'); ?></option>
                <?php foreach ($this->count_statuses($post_type) as $status => $count) :
                    $status_obj = get_post_status_object($status);
                    ?>
                    <option value="<?php echo esc_attr($status); ?>"><?php echo esc_html(($status_obj ? $status_obj->label : $status) . ' (' . number_format_i18n($count) . ')'); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="eh-sort"><?php esc_html_e('Sort by', 'easy-hierarchy'); ?></label>
            <select id="eh-sort">
                <option value="order"><?php esc_html_e('Default order', 'easy-hierarchy'); ?></option>
                <option value="title"><?php esc_html_e('Title (A-Z)', 'easy-hierarchy'); ?></option>
                <option value="date_desc"><?php esc_html_e('Newest first', 'easy-hierarchy'); ?></option>
                <option value="date_asc"><?php esc_html_e('Oldest first', 'easy-hierarchy'); ?></option>
                <option value="modified_desc"><?php esc_html_e('Recently modified', 'easy-hierarchy'); ?></option>
            </select>
        </div>
        <?php endif; ?>
        <?php
        echo '<div class="eh-hierarchy-overview">';
        foreach ($top_posts as $post) {
            $this->display_page_tree($post);
        }
        echo '</div>';
        echo '<p class="eh-no-results"' . ($top_posts ? ' hidden' : '') . '>' . esc_html($object->labels->not_found) . '</p>';
        ?>
        <style>
            .eh-search-box {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px;
                margin: 24px 0 16px 0;
                padding: 16px 18px;
                background: #f8fafc;
                border: 1px solid #e0e4ea;
                border-radius: 2px;
                box-shadow: 0 2px 8px rgba(30,40,90,0.04);
            }
            .eh-search-box label[for="eh-sort"] {
                margin-left: 8px;
                color: #646970;
            }
            .eh-search-box input[type="text"] {
                flex: 1 1 240px;
                max-width: 350px;
                padding: 8px 12px;
                border-radius: 2px;
                border: 1px solid #c3c4c7;
                font-size: 15px;
                transition: border-color 0.2s;
            }
            .eh-search-box input[type="text"]:focus {
                border-color: #2271b1;
                outline: none;
                background: #fff;
            }
            .eh-hierarchy-overview {
                margin: 24px 0;
            }
            .eh-page-tree {
                margin: 0 0 14px 0;
            }
            .eh-page-item-inline {
                display: flex;
                align-items: center;
                gap: 24px;
                padding: 10px 16px;
                background: #fff;
                border: 1px solid #e0e4ea;
                border-radius: 2px;
                box-shadow: 0 2px 8px rgba(30,40,90,0.06);
                margin-bottom: 8px;
                min-height: 44px;
            }
            .eh-page-title {
                margin: 5px 0;
            }
            .eh-page-title-inline {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 16px;
                font-weight: 500;
                min-width: 180px;
            }
            .title-count {
                background: #2271b1;
                color: #fff;
                border-radius: 10px;
                padding: 2px 8px;
                font-size: 12px;
                margin-left: 4px;
            }
            .eh-page-meta-inline {
                display: flex;
                align-items: center;
                gap: 16px;
                font-size: 13px;
                color: #646970;
                flex-wrap: wrap;
            }
            .eh-date-meta {
                display: flex;
                gap: 4px;
                align-items: center;
                background: #f6f8fa;
                border-radius: 5px;
                padding: 2px 8px;
            }
            .eh-date-meta strong {
                color: #2271b1;
                font-weight: 600;
            }
            .eh-date-meta span {
                color: #3c434a;
            }
            .eh-page-actions-inline {
                display: flex;
                gap: 10px;
                margin-left: auto;
            }
            .eh-page-actions-inline .button:hover {
                background: #2271b1;
                color: #fff;
                border-color: #2271b1;
            }
            .eh-page-children {
                margin-left: 24px;
                margin-top: 2px;
            }
            .eh-no-results {
                color: #646970;
                font-style: italic;
            }
            /* Parents shown only because a child matches the filters */
            .eh-dimmed {
                opacity: 0.55;
            }
        </style>
        <script>
        jQuery(document).ready(function($) {
            var $overview = $('.eh-hierarchy-overview');

            // Sort siblings inside each level, so the hierarchy is kept
            function sortTree() {
                var mode = $('#eh-sort').val();
                var compare = {
                    order: function(a, b) { return a.dataset.index - b.dataset.index; },
                    title: function(a, b) { return a.dataset.title.localeCompare(b.dataset.title); },
                    date_desc: function(a, b) { return b.dataset.date - a.dataset.date; },
                    date_asc: function(a, b) { return a.dataset.date - b.dataset.date; },
                    modified_desc: function(a, b) { return b.dataset.modified - a.dataset.modified; }
                }[mode];
                $overview.add($overview.find('.eh-page-children')).each(function() {
                    var $list = $(this);
                    $list.append($list.children('.eh-page-tree').get().sort(compare));
                });
            }

            // An item is shown if it matches, or if one of its children does (then it is dimmed)
            function filterTree() {
                var term = $('#eh-page-search').val().toLowerCase();
                var status = $('#eh-status-filter').val();
                function visit(item) {
                    var $item = $(item);
                    var matches = item.dataset.title.toLowerCase().includes(term) && (!status || item.dataset.status === status);
                    var childVisible = false;
                    $item.children('.eh-page-children').children('.eh-page-tree').each(function() {
                        childVisible = visit(this) || childVisible;
                    });
                    $item.toggle(matches || childVisible);
                    $item.children('.eh-page-item').toggleClass('eh-dimmed', !matches && childVisible);
                    return matches || childVisible;
                }
                var anyVisible = false;
                $overview.children('.eh-page-tree').each(function() {
                    anyVisible = visit(this) || anyVisible;
                });
                $('.eh-no-results').prop('hidden', anyVisible);
            }

            $('#eh-page-search').on('input', filterTree);
            $('#eh-status-filter').on('change', filterTree);
            $('#eh-sort').on('change', sortTree);
        });
        </script>
        <?php
        echo '</div>';
    }

    private function display_page_tree($post) {
        $children = $this->get_children($post->post_type, $post->ID);
        $child_count = count($children);

        $date_format = get_option('date_format');
        $publish_date = get_the_date($date_format, $post);
        $modified_date = get_the_modified_date($date_format, $post);

        printf(
            '<div class="eh-page-tree" data-index="%d" data-title="%s" data-status="%s" data-date="%d" data-modified="%d">',
            $this->tree_index++,
            esc_attr($this->get_title($post)),
            esc_attr($post->post_status),
            get_post_time('U', true, $post),
            get_post_modified_time('U', true, $post)
        );
        echo '<div class="eh-page-item eh-page-item-inline">';
        echo '<div class="eh-page-title-inline">';
        echo '<span class="eh-page-title">' . esc_html($this->get_title($post)) . '</span>';
        if ($child_count > 0) {
            echo '<span class="title-count">' . esc_html(number_format_i18n($child_count)) . '</span>';
        }
        echo '</div>';
        echo '<div class="eh-page-meta-inline">';
        echo '<span class="eh-date-meta"><strong>' . esc_html__('Published', 'default') . ':</strong> <span>' . esc_html($publish_date) . '</span></span>';
        echo '<span class="eh-date-meta"><strong>' . esc_html__('Revision', 'default') . ':</strong> <span>' . esc_html($modified_date) . '</span></span>';
        $status_obj = get_post_status_object($post->post_status);
        $status_label = $status_obj ? $status_obj->label : ucfirst($post->post_status);
        echo '<span class="eh-date-meta"><strong>' . esc_html__('Status:', 'default') . '</strong> <span>' . esc_html($status_label) . '</span></span>';
        echo '</div>';
        echo '<div class="eh-page-actions-inline">';
        $edit_link = get_edit_post_link($post->ID);
        if ($edit_link) {
            echo '<a href="' . esc_url($edit_link) . '" class="button button-small">' . esc_html__('Edit', 'default') . '</a>';
        }
        if (is_post_type_viewable($post->post_type)) {
            echo '<a href="' . esc_url(get_permalink($post->ID)) . '" target="_blank" rel="noopener" class="button button-small">' . esc_html__('View', 'default') . '</a>';
        }
        echo '</div>';
        echo '</div>'; // .eh-page-item

        if (!empty($children)) {
            echo '<div class="eh-page-children">';
            foreach ($children as $child) {
                $this->display_page_tree($child);
            }
            echo '</div>';
        }
        echo '</div>'; // .eh-page-tree
    }

    // === List screen: filter, notice and Hierarchy column ===

    public function filter_parent_pages($query) {
        global $pagenow;
        if (!is_admin() || $pagenow !== 'edit.php' || empty($_GET['eh_parent_pages'])) {
            return;
        }
        if (!$query->is_main_query() || !$this->is_supported($query->get('post_type'))) {
            return;
        }
        $query->set('post_parent', absint($_GET['eh_parent_pages']));
    }

    public function parent_pages_dropdown($post_type) {
        if (!$this->is_supported($post_type)) {
            return;
        }
        $current = isset($_GET['eh_parent_pages']) ? absint($_GET['eh_parent_pages']) : 0;
        $options = $this->parent_options($post_type, 0, 0, $current);
        if ($options === '') {
            return;
        }

        echo '<select name="eh_parent_pages">';
        echo '<option value="">' . esc_html(get_post_type_object($post_type)->labels->all_items) . '</option>';
        echo $options; // Escaped in parent_options()
        echo '</select>';
    }

    /**
     * Options for every item that has children, indented like the tree.
     */
    private function parent_options($post_type, $parent_id, $depth, $current) {
        $html = '';
        foreach ($this->get_children($post_type, $parent_id) as $post) {
            if (!$this->has_children($post_type, $post->ID)) {
                continue;
            }
            $html .= sprintf(
                '<option value="%d"%s>%s%s</option>',
                $post->ID,
                selected($current, $post->ID, false),
                str_repeat('&#160;', $depth * 3),
                esc_html($this->get_title($post) . ' (' . number_format_i18n($this->count_descendants($post_type, $post->ID)) . ')')
            );
            $html .= $this->parent_options($post_type, $post->ID, $depth + 1, $current);
        }
        return $html;
    }

    private function has_children($post_type, $parent_id) {
        return $this->get_children($post_type, $parent_id) !== [];
    }

    public function show_subpages_filter_notice() {
        global $pagenow;

        if (!is_admin() || $pagenow !== 'edit.php' || empty($_GET['eh_parent_pages'])) {
            return;
        }

        $post_type = isset($_GET['post_type']) ? sanitize_key($_GET['post_type']) : 'post';
        if (!$this->is_supported($post_type)) {
            return;
        }

        $parent_id = absint($_GET['eh_parent_pages']);
        $parent_title = $parent_id ? get_the_title($parent_id) : '';
        if (!$parent_title) {
            return;
        }

        $clear_url = remove_query_arg(['eh_parent_pages', 'paged']);
        $message = sprintf(
            __('You are viewing the children of "%s".', 'easy-hierarchy'),
            $parent_title
        );

        printf(
            '<div class="notice notice-info"><p><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true" style="vertical-align:text-bottom;margin-right:6px;"></span>%1$s <a href="%2$s">%3$s</a></p></div>',
            esc_html($message),
            esc_url($clear_url),
            esc_html(get_post_type_object($post_type)->labels->all_items)
        );
    }

    public function register_column_hooks() {
        foreach (array_keys($this->get_post_types()) as $post_type) {
            add_filter("manage_{$post_type}_posts_columns", [$this, 'add_hierarchy_columns']);
        }
    }

    public function hierarchy_columns_style() {
        $screen = get_current_screen();
        if (!$screen || !$this->is_supported($screen->post_type)) {
            return;
        }
        ?>
            <style>
                .column-page_parent {
                    position: relative;
                    width: 15% !important;
                }
                .wp-list-table .column-title {
                    width: 25% !important;
                }
                .wp-list-table .column-author,
                .wp-list-table .column-comments,
                .wp-list-table .column-date {
                    width: auto !important;
                }
                .eh-children-count {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    min-width: 20px;
                    height: 20px;
                    padding: 0 4px;
                    border-radius: 10px;
                    background: #2271b1;
                    color: #fff;
                    font-size: 12px;
                    font-weight: 500;
                    margin-left: 6px;
                    transition: all 0.2s ease;
                }
                .eh-children-count:hover {
                    color: #ffffff;
                }
                .eh-subpages-icon {
                    font-size: 14px;
                    line-height: 1;
                    width: 14px;
                    height: 14px;
                    margin-right: 4px;
                }
                .eh-hierarchy-path {
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                }
                .eh-hierarchy-item {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    font-size: 13px;
                }
                .eh-hierarchy-separator {
                    color: #8c8f94;
                    margin-left: 4px;
                }
                .eh-hierarchy-link {
                    color: #2271b1;
                    text-decoration: none;
                    padding: 2px 6px;
                    border-radius: 3px;
                    transition: all 0.2s ease;
                }
                .eh-hierarchy-link:hover {
                    background: #f0f6fc;
                }
            </style>
        <?php
    }

    public function add_hierarchy_columns($columns) {
        // Reorder columns to move hierarchy after title
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['page_parent'] = __('Hierarchy', 'easy-hierarchy');
            }
        }

        return $new_columns;
    }

    public function render_hierarchy_columns($column, $post_id) {
        if ($column !== 'page_parent') {
            return;
        }
        $post_type = get_post_type($post_id);
        if (!$this->is_supported($post_type)) {
            return;
        }

        // Filter links always start from the first page of results
        $base_url = remove_query_arg('paged');

        // Show parent hierarchy
        $parents = [];
        $pid = wp_get_post_parent_id($post_id);
        while ($pid) {
            array_unshift($parents, $pid);
            $pid = wp_get_post_parent_id($pid);
        }

        if (!empty($parents)) {
            echo '<div class="eh-hierarchy-path">';
            foreach ($parents as $index => $parent_id) {
                $parent_title = get_the_title($parent_id);
                echo '<div class="eh-hierarchy-item">';
                if ($index > 0) {
                    echo '<span class="eh-hierarchy-separator">└</span>';
                }
                printf(
                    '<a href="%s" class="eh-hierarchy-link" title="%s">%s</a>',
                    esc_url(add_query_arg(['eh_parent_pages' => $parent_id], $base_url)),
                    esc_attr(sprintf(__('Show children of "%s"', 'easy-hierarchy'), $parent_title)),
                    esc_html($parent_title)
                );
                echo '</div>';
            }
            echo '</div>';
        }

        // Show children count inline if there are children
        $count = $this->count_descendants($post_type, $post_id);
        if ($count) {
            $display_text = $post_type === 'page'
                ? sprintf(_n('%s subpage', '%s subpages', $count, 'easy-hierarchy'), number_format_i18n($count))
                : sprintf(_n('%s child', '%s children', $count, 'easy-hierarchy'), number_format_i18n($count));
            printf(
                '<a href="%s" class="eh-children-count" title="%s"><span class="dashicons dashicons-arrow-right-alt2 eh-subpages-icon" aria-hidden="true"></span>%s</a>',
                esc_url(add_query_arg(['eh_parent_pages' => $post_id], $base_url)),
                esc_attr($display_text),
                esc_html($display_text)
            );
        }
    }
}

new Easy_Hierarchy_Plugin();
