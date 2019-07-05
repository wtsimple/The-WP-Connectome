<?php

namespace Connectome;

/**
 * The connectome bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Connectome
 *
 * @wordpress-plugin
 * Plugin Name:       The Connectome
 * Plugin URI:        https://bikerentalhavana.com/armando/plugin/connectome
 * Description:       The WP Connectome shows you all of your site in a single visualization. It allows you to see the connections among the elements of your site like users, posts (either regular or custom post types) and taxonomy terms.
 * Version:           1.0.0
 * Author:            Armando Rivero
 * Author URI:        https://bikerentalhavana.com/armando
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       connectome
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('CONNECTOME_VERSION', '1.0.0');

require 'includes/helper-functions.php';

require 'data/data-loader.php';
require 'logic/logic-loader.php';
require 'interfaces/interfaces-loader.php';

require 'enqueue/enqueue.php';
require 'shortcodes/graph-container.php';

/**
 * Add plugin action links.
 *
 * Add a link to the settings page on the plugins.php page.
 * @link https://jeroensormani.com/adding-links-to-your-plugin-on-the-plugins-page/
 * @since 1.0.0
 *
 * @param  array  $links List of existing plugin action links.
 * @return array         List of modified plugin action links.
 */
function add_action_links($links)
{
    $optionsName = OptionStorage::get_option('OPTIONS_NAME');
    $links = array_merge([
        '<a href="' . esc_url(admin_url('/options-general.php?page=' . $optionsName)) . '">Settings </a>',
    ], $links);
    return $links;
}
add_action('plugin_action_links_' . plugin_basename(__FILE__), 'Connectome\add_action_links');

/**
 * To be run on activation
 *
 * @return void
 */
function run_activation()
{
    save_post_types();
}
register_activation_hook(__FILE__, 'Connectome\run_activation');
