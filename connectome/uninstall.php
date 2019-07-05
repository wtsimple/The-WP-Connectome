<?php

// namespace Connectome;

/**
 * Fired when the plugin is uninstalled.
 *
 * @since      1.0.0
 *
 * @package    Connectome
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$optionNames = [
    'OPTIONS_NAME' => 'connectome-options',
    'OPTIONS_STORAGE_NAME' => 'connectome-options-options-storage',
    'OPTIONS_GRAPH_NAME' => 'connectome-options-graph',
];
foreach ($optionNames as  $option) {
    delete_option($option);
    delete_site_option($option);
}
