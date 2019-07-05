<?php

namespace Connectome;

/**
 * Registers cpt with custom default args
 *
 * @param string $name
 * @param string $plural
 * @param array $args
 * @return void
 */
function register_cpt($name, $plural, $args = [])
{
    $singular = ucfirst($name);
    $plural = ucfirst($plural);

    $labels = [
        'name' => $plural,
        'singular_name' => $singular,
        'add_new' => 'Add New',
        'add_new_item' => 'Add New ' . $singular,
        'edit_item' => 'Edit ' . $singular,
        'new_item' => 'New ' . $singular,
        'all_items' => 'All ' . $plural,
        'view_item' => 'View ' . $singular,
        'search_items' => 'Search ' . $plural,
        'not_found' => 'No ' . $plural . ' found',
        'not_found_in_trash' => 'No ' . $plural . ' found in the Trash',
        'parent_item_colon' => '',
        'menu_name' => $singular,
    ];

    $default = [
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-art',
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'comments'],
        'menu_position' => 10,
        // adding map_meta_cap will map the meta correctly
        'map_meta_cap' => true,
    ];

    $args = wp_parse_args($args, $default);

    register_post_type($name, $args);
}

//Register all Custom Post Types
add_action('wp_loaded', function () {
    if (CONNECTOME_DEVELOP) {
        $bdts = [
            'my_cpt',
        ];
        foreach ($bdts as $bdt) {
            register_cpt($bdt, $bdt . 's');
        }
    }
    save_post_types();
}, 100);
