<?php
/*
Plugin Name: MessyMenu
Plugin URI: https://doc4design.com/plugins/messymenu/
Description: Create additional, custom, internal and external dashboard links
Version: 4.0
Requires at least: 2.7
Author: Doc4
Author URI: https://doc4design.com/
License: GPL v2.0 or later
License URL: https://www.gnu.org/licenses/gpl-2.0.html
*/

/******************************************************************************

Copyright 2008 - 2024  Doc4 : info@doc4design.com

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
The license is also available at https://www.gnu.org/licenses/gpl-2.0.html

*********************************************************************************/


// Prevent direct access
if (!defined('ABSPATH'))
    exit;


// Add MessyMenu Settings Page
function incorporate_messymenu_settings_page()
{
    add_options_page('MessyMenu Settings', 'MessyMenu', 'manage_options', 'messymenu', 'messymenu_settings_page');
}
add_action('admin_menu', 'incorporate_messymenu_settings_page');


// Enqueue Styles and Scripts
function enqueue_messymenu_styles_and_scripts()
{
    wp_enqueue_style('messymenu-styles', plugin_dir_url(__FILE__) . 'css/messy_styles.css', array(), '2.7');
}
add_action('admin_enqueue_scripts', 'enqueue_messymenu_styles_and_scripts');


// Handle Form Submissions (Save Links)
add_action('admin_post_messymenu_save', 'messymenu_save_links');
function messymenu_save_links()
{
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized user');
    }

    // Verify Nonce
    if (!isset($_POST['messymenu_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['messymenu_nonce'])), 'messymenu_action')) {
        wp_die('Nonce verification failed!');
    }

    // Validate the "Link Label" field
    if (empty($_POST['messymenu_text'])) {
        // Redirect back to the settings page with an error message
        wp_safe_redirect(admin_url('options-general.php?page=messymenu&error=empty_label'));
        exit;
    }

    // Save Links
    $links = get_option('messymenu_links', array());
    $link_id = isset($_POST['link_id']) && is_numeric($_POST['link_id']) ? absint($_POST['link_id']) : -1;

    // Find the highest existing position, default to 0 if no links
    $highest_position = count($links) > 0 ? max(array_column($links, 'position')) : 0;

    // Set the new link's position to the next available number if not provided
    $new_link = array(
        'text' => sanitize_text_field($_POST['messymenu_text']),
        'url' => sanitize_url($_POST['messymenu_url']),
        'icon' => sanitize_text_field($_POST['messymenu_icon']),
        'position' => isset($_POST['messymenu_position']) && $_POST['messymenu_position'] !== '' 
            ? absint($_POST['messymenu_position']) 
            : $highest_position + 1 // Auto-increment if no position is provided
    );

    if ($link_id >= 0 && $link_id < count($links)) {
        $links[$link_id] = $new_link;
    } else {
        $links[] = $new_link;
    }

    // Sort links by position
    usort($links, function ($a, $b) {
        return $a['position'] <=> $b['position'];
    });

    update_option('messymenu_links', $links);
    
    wp_safe_redirect(admin_url('options-general.php?page=messymenu'));
    exit;
}

// Handle Form Submissions (Delete Links)
add_action('admin_post_messymenu_delete', 'messymenu_delete_links');
function messymenu_delete_links()
{
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized user');
    }

    // Verify Nonce
    if (!isset($_POST['messymenu_delete_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['messymenu_delete_nonce'])), 'messymenu_delete_action')) {
        wp_die('Nonce verification failed!');
    }

    // Delete Links
    $links = get_option('messymenu_links', array());
    $delete_links = isset($_POST['link_ids']) && is_array($_POST['link_ids']) ? array_map('sanitize_text_field', $_POST['link_ids']) : array();

    if (!empty($delete_links)) {
        foreach ($delete_links as $link_id) {
            if (isset($links[$link_id])) {
                unset($links[$link_id]);
            }
        }

        $links = array_values($links);
        update_option('messymenu_links', $links);
    }

    wp_safe_redirect(admin_url('options-general.php?page=messymenu'));
    exit;
}

// Display Settings Page
function messymenu_settings_page()
{
    $links = get_option('messymenu_links', array());

    // Check if there's an error
    if (!empty($_GET['error']) && $_GET['error'] === 'empty_label') {
        echo '<div class="notice notice-error"><p>Please fill in the "Link Label" field.</p></div>';
    }

    // Dashicons Array
    require_once plugin_dir_path(__FILE__) . 'php/dashicons-array.php';

    $edit_link = array(
        'text' => '',
        'url' => '',
        'icon' => ''
    );

    if (!empty($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['link_id'])) {
        $link_id = absint($_GET['link_id']);
        if ($link_id >= 0 && $link_id < count($links)) {
            $edit_link = $links[$link_id];
        }
    }
    ?>

    <div class="wrap">
        <h1>MessyMenu Settings</h1>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <!-- Nonce field for the form -->
            <?php wp_nonce_field('messymenu_action', 'messymenu_nonce'); ?>
            <input type="hidden" name="action" value="messymenu_save">
            <?php if (!empty($edit_link['text'])) { ?>
                <input type="hidden" name="link_id" value="<?php echo esc_attr($link_id); ?>">
            <?php } ?>

            <h2><?php echo !empty($edit_link['text']) ? 'Edit' : 'Add'; ?> Link</h2>
            <div class="row">
                <div class="column">
                    <label for="messymenu_text">Link Label:</label>
                    <input type="text" name="messymenu_text" id="messymenu_text"
                        value="<?php echo esc_attr($edit_link['text']); ?>">
                </div>
                <div class="column">
                    <label for="messymenu_icon">Link Icon:</label>
                    <select name="messymenu_icon" id="messymenu_icon">
                        <?php foreach ($dashicons as $dashicon) { ?>
                            <option value="<?php echo esc_attr($dashicon); ?>" <?php selected($dashicon, $edit_link['icon']); ?>>
                                <?php echo esc_html($dashicon); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

            </div>

            <div class="row">
                <div class="column">
                    <label for="messymenu_position">Menu Location #:</label>
                    <input type="number" name="messymenu_position" id="messymenu_position"
                        value="<?php echo esc_attr($edit_link['position'] ?? ''); ?>" min="1">
                    <div class="note">
                        The location of your link in the Dashboard Menu. For example; type '0' to show the link at the top or type '136' to show the link at the bottom of the menu. Experiment to find the desired location number as WordPress and Plugins use these. Defaults to '0'.
                    </div>
                </div>
                <div class="column">
                    <label for="messymenu_url">Link URL:</label>
                    <input type="text" name="messymenu_url" id="messymenu_url"
                        value="<?php echo esc_attr($edit_link['url']); ?>">
                    <div class="note">
                        External link requires the full address (http://mylink.com) / Internal links only require the base
                        (themes.php)
                    </div>
                </div>
            </div>
            <p>
                <input type="submit" name="submit"
                    value="<?php echo !empty($edit_link['text']) ? 'Save' : 'Create'; ?> Your Link" class="button-primary">
                <?php if (!empty($edit_link['text'])) { ?>
                    <button type="button" class="button-secondary"
                        onclick="window.location.href='<?php echo esc_url(admin_url('options-general.php?page=messymenu')); ?>';">Cancel</button>
                <?php } ?>
            </p>

        </form>

        <h2 style="margin-top: 50px;">Menu Links</h2>

        <?php if (count($links) > 0) { ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <!-- Nonce field for link deletion -->
                <?php wp_nonce_field('messymenu_delete_action', 'messymenu_delete_nonce'); ?>
                <input type="hidden" name="action" value="messymenu_delete">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <td class="manage-column column-cb check-column">
                                <input id="cb-select-all-1" type="checkbox">
                            </td>
                            <th class="manage-column column-icon">Icon</th>
                            <th class="manage-column column-title">Text</th>
                            <th class="manage-column column-url">URL</th>
                            <th class="manage-column column-position">Position</th>
                        </tr>
                    </thead>
                    <tbody id="messymenu-links-sortable">
                        <?php foreach ($links as $link_id => $link) { ?>
                            <tr data-id="<?php echo esc_attr($link_id); ?>">
                                <th scope="row" class="check-column">
                                    <input id="cb-select-<?php echo esc_attr($link_id); ?>" type="checkbox" name="link_ids[]"
                                        value="<?php echo esc_attr($link_id); ?>">
                                </th>
                                <td class="messy-dashicons">
                                    <div class="wp-menu-image dashicons-before <?php echo esc_attr($link['icon']); ?>"
                                        aria-hidden="true"></div>
                                </td>
                                <td>
                                    <?php echo esc_html($link['text']); ?>
                                    <div class="row-actions">
                                        <span class="edit"><a
                                                href="<?php echo esc_url(admin_url('options-general.php?page=messymenu&action=edit&link_id=' . $link_id)); ?>">Edit</a></span>
                                    </div>
                                </td>
                                <td><?php echo esc_html($link['url']); ?></td>
                                <td class="column-position"><?php echo esc_html($link['position']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>

                </table>
                <p>
                    <input type="submit" name="delete_links" value="Delete Selected Links" class="button-primary">
                </p>
            </form>
        <?php } else { ?>
            <p>No links found.</p>
        <?php } ?>
    </div>

    <?php
}

// Add MessyMenu Links As Separate Menu Items In The Specified Order
function include_messymenu_links()
{
    $links = get_option('messymenu_links', array());

    usort($links, function ($a, $b) {
        return $a['position'] <=> $b['position'];
    });

    if (!empty($links)) {
        foreach ($links as $link_id => $link) {
            $url = esc_url($link['url']);
            $icon = esc_attr($link['icon']);
            $title = esc_html($link['text']);
            $position = isset($link['position']) ? absint($link['position']) : 100;
			
            add_menu_page($title, $title, 'manage_options', $url, '', $icon, $position);
        }
    }
}
add_action('admin_menu', 'include_messymenu_links');

// Open 'http' And 'https' Links New Browser Tab
function messymenu_open_links_in_new_tab() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Target all MessyMenu links in the dashboard
            $('#adminmenu a').each(function() {
                var linkUrl = $(this).attr('href');
                // Check if the URL starts with 'http' or 'https'
                if (linkUrl.startsWith('http://') || linkUrl.startsWith('https://')) {
                    $(this).attr('target', '_blank');
                }
            });
        });
    </script>
    <?php
}
add_action('admin_footer', 'messymenu_open_links_in_new_tab');