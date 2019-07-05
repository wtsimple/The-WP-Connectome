<?php

namespace Connectome;

/**
 * Get all the names of the post types except for the excluded
 * in an option
 *
 * @param bool $exclude if false none will be excluded
 * @return array $types
 */
function get_all_post_types()
{
    // Get the extra types recorded in option storage
    $storedTypes = OptionStorage::get_option('POST_TYPES');
    return $storedTypes;
}

function get_post_types_count()
{
    return OptionStorage::get_option('POST_TYPES_COUNTS');
}

/**
 * Storages all post types for later use
 *
 * @return void
 */
function save_post_types()
{
    $rawTypes = get_post_types(['public' => true], 'names');
    $types = [];
    $count = [];
    foreach ($rawTypes as $type) {
        $types[] = $type;
        $count[$type] = wp_count_posts($type)->publish;
    }
    // Exclude the types set in the OptionStorage to be excluded
    $types = array_filter($types, function ($type) {
        $excluded = OptionStorage::get_option('EXCLUDED_TYPES');
        return !in_array($type, $excluded);
    });

    OptionStorage::save_option('POST_TYPES', $types);
    OptionStorage::save_option('POST_TYPES_COUNTS', $count);
}

/**
 * Returns an array with the max amount per type of element
 *
 * @param array $options
 * @return array ['element_type' => amount]
 */
function get_options_max($optionName)
{
    $options = get_option($optionName);
    $max = [];
    $handler = new SettingsOptionHandler();
    $maxOps = $handler->get_matching_options('max$');

    if (!empty($maxOps)) {
        foreach ($maxOps as $maxOp) {
            $keyParts = explode($handler->separator, $maxOp);
            $size = sizeof($keyParts);
            $type = $keyParts[$size - 2];
            $max[$type] = (int) $options[$maxOp];
        }
    }
    return $max;
}
