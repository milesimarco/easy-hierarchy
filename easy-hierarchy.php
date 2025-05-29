<?php
/*
Plugin Name: Easy Hierarchy
Description: Makes WordPress page hierarchy management easy and intuitive with enhanced filtering and visual hierarchy indicators
Version: 2.0.3
Author: Marco Milesi
Author URI: https://www.marcomilesi.com
License: GPL Attribution-ShareAlike
Text Domain: easy-hierarchy
*/

if (!defined('ABSPATH')) exit;

class Easy_Hierarchy_Plugin {
    public function __construct() {
        add_action('init', [$this, 'load_textdomain']);
        add_action('admin_menu', [$this, 'add_dashboard_page']);

        // Restore admin columns and filters
        add_filter('parse_query', [$this, 'filter_parent_pages']);
        add_action('restrict_manage_posts', [$this, 'parent_pages_dropdown']);
        add_filter('manage_pages_columns', [$this, 'add_hierarchy_columns']);
        add_action('manage_pages_custom_column', [$this, 'render_hierarchy_columns'], 10, 2);
    }

    public function load_textdomain() {
        load_plugin_textdomain('easy-hierarchy');
    }

    public function add_dashboard_page() {
        add_submenu_page(
            'edit.php?post_type=page',
            __('Page Tree', 'easy-hierarchy'),
            __('Page Tree', 'easy-hierarchy'),
            'edit_pages',
            'pages-hierarchy',
            [$this, 'dashboard_page']
        );
    }

    public function dashboard_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Pages Hierarchy Overview', 'easy-hierarchy') . '</h1>';
        ?>
        <div class="eh-search-box">
            <input type="text" id="eh-page-search" class="regular-text" placeholder="<?php esc_attr_e('Search pages...', 'easy-hierarchy'); ?>">
        </div>
        <?php
        $top_pages = get_pages(['parent' => 0, 'sort_column' => 'menu_order,post_title']);
        echo '<div class="eh-hierarchy-overview">';
        foreach ($top_pages as $page) {
            $this->display_page_tree($page);
        }
        echo '</div>';
        ?>
        <style>
            .eh-search-box {
                margin: 24px 0 16px 0;
                padding: 16px 18px;
                background: #f8fafc;
                border: 1px solid #e0e4ea;
                border-radius: 2px;
                box-shadow: 0 2px 8px rgba(30,40,90,0.04);
            }
            .eh-search-box input[type="text"] {
                width: 100%;
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
            .eh-page-item {
                padding: 18px 22px;
                background: #fff;
                border: 1px solid #e0e4ea;
                border-radius: 2px;
                box-shadow: 0 2px 8px rgba(30,40,90,0.06);
                transition: box-shadow 0.2s, border-color 0.2s;
                margin-bottom: 8px;
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
            .eh-page-meta {
                font-size: 13px;
                color: #646970;
                margin-top: 4px;
                display: flex;
                flex-wrap: wrap;
                gap: 18px;
                align-items: center;
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
            .eh-page-actions {
                display: flex;
                gap: 10px;
                margin-left: auto;
            }
            .eh-page-actions-inline {
                display: flex;
                gap: 10px;
                margin-left: auto;
            }
            .eh-page-actions .button:hover {
                background: #2271b1;
                color: #fff;
                border-color: #2271b1;
            }
            .eh-page-actions-inline .button:hover {
                background: #2271b1;
                color: #fff;
                border-color: #2271b1;
            }
            /* Indent child trees */
            .eh-page-tree > .eh-page-item > .eh-page-tree {
                margin-left: 24px;
                margin-top: 10px;
            }
        </style>
        <script>
        jQuery(document).ready(function($) {
            $('#eh-page-search').on('input', function() {
                var searchTerm = $(this).val().toLowerCase();
                $('.eh-page-tree').each(function() {
                    var pageTitle = $(this).find('.eh-page-title').text().toLowerCase();
                    $(this).toggle(pageTitle.includes(searchTerm));
                });
            });
        });
        </script>
        <?php
        echo '</div>';
    }

    private function display_page_tree($page, $depth = 0) {
        $view_link = get_permalink($page->ID);
        $children = get_pages(['parent' => $page->ID, 'sort_column' => 'menu_order,post_title']);
        $child_count = count($children);

        $date_format = get_option('date_format');
        $publish_date = get_the_date($date_format, $page);
        $modified_date = get_the_modified_date($date_format, $page);

        echo '<div class="eh-page-tree">';
        echo '<div class="eh-page-item eh-page-item-inline">'; // Add new class for inline style
        // Inline row
        echo '<div class="eh-page-title-inline">';
        echo '<span class="eh-page-title">' . esc_html($page->post_title) . '</span>';
        if ($child_count > 0) {
            echo '<span class="title-count">' . $child_count . '</span>';
        }
        echo '</div>';
        echo '<div class="eh-page-meta-inline">';
        echo '<span class="eh-date-meta"><strong>' . __('Published', 'default') . ':</strong> <span>' . esc_html($publish_date) . '</span></span>';
        echo '<span class="eh-date-meta"><strong>' . __('Revision', 'default') . ':</strong> <span>' . esc_html($modified_date) . '</span></span>';
        $status_obj = get_post_status_object($page->post_status);
        $status_label = $status_obj ? $status_obj->label : ucfirst($page->post_status);
        echo '<span class="eh-date-meta"><strong>' . __('Status:', 'default') . '</strong> <span>' . esc_html($status_label) . '</span></span>';
        echo '</div>';
        echo '<div class="eh-page-actions-inline">';
        echo '<a href="' . esc_url(get_edit_post_link($page->ID)) . '" class="button button-small">' . __('Edit', 'default') . '</a>';
        echo '<a href="' . esc_url($view_link) . '" target="_blank" class="button button-small">' . __('View', 'default') . '</a>';
        echo '</div>';
        echo '</div>'; // .eh-page-item

        // Children (indented, but still one-line per child)
        if (!empty($children)) {
            echo '<div style="margin-left: 24px; margin-top: 2px;">';
            foreach ($children as $child) {
                $this->display_page_tree($child, $depth + 1);
            }
            echo '</div>';
        }
        echo '</div>'; // .eh-page-tree
    }

    // === Restored admin columns and filters ===

    public function filter_parent_pages($query) {
        global $pagenow;
        if (is_admin() && $pagenow == 'edit.php' && !empty($_GET['eh_parent_pages'])) {
            $query->query_vars['post_parent'] = sanitize_text_field($_GET['eh_parent_pages']);
        }
    }

    public function parent_pages_dropdown() {
        global $wpdb;
        if (isset($_GET['post_type']) && is_post_type_hierarchical($_GET['post_type'])) {
            $sql = "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'page' AND post_parent = 0 AND post_status = 'publish' ORDER BY post_title";
            $parent_pages = $wpdb->get_results($sql, OBJECT_K);
            $select = '<select name="eh_parent_pages"><option value="">' . __('All first level pages', 'easy-hierarchy') . '</option>';
            $current = isset($_GET['eh_parent_pages']) ? sanitize_text_field($_GET['eh_parent_pages']) : '';
            foreach ($parent_pages as $page) {
                $select .= sprintf(
                    '<option value="%s"%s>%s</option>',
                    $page->ID,
                    $page->ID == $current ? ' selected="selected"' : '',
                    $page->post_title . ' (' . count(get_pages(['child_of' => $page->ID])) . ')'
                );
            }
            $select .= '</select>';
            echo $select;
        }
    }

    public function add_hierarchy_columns($columns) {
        $style = '
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
        ';

        // Reorder columns to move hierarchy after title
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['page_parent'] = __('Parent') . $style;
            }
        }

        return $new_columns;
    }

    public function render_hierarchy_columns($column, $post_id) {
        if ($column === 'page_parent') {
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
                        esc_url(add_query_arg(['eh_parent_pages' => $parent_id], $_SERVER['REQUEST_URI'])),
                        esc_attr(sprintf(__('Show children of "%s"', 'easy-hierarchy'), $parent_title)),
                        esc_html($parent_title)
                    );
                    echo '</div>';
                }
                echo '</div>';
            }

            // Show children count inline if there are children
            $children = get_pages(['child_of' => $post_id]);
            $count = count($children);
            if ($count) {
                $tooltip = sprintf(
                    _n('%s child page', '%s child pages', $count, 'easy-hierarchy'),
                    number_format_i18n($count)
                );
                $display_text = sprintf(
                    '%s %s',
                    number_format_i18n($count),
                    _n('child', 'children', $count, 'easy-hierarchy')
                );
                printf(
                    '<a href="%s" class="eh-children-count" title="%s">%s</a>',
                    esc_url(add_query_arg(['eh_parent_pages' => $post_id], $_SERVER['REQUEST_URI'])),
                    esc_attr($tooltip),
                    esc_html($display_text)
                );
            }
        }
    }
}

new Easy_Hierarchy_Plugin();
