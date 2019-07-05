<?php

namespace Connectome;

add_action('wp_enqueue_scripts', '\Connectome\adding_scripts');
add_action('admin_enqueue_scripts', '\Connectome\adding_scripts');
function adding_scripts()
{
    $vueData = new VueData(); //This object returns all the data the vue app needs
    if ($vueData->is_vue_needed()) { //Only enqueue it if it's needed
        wp_register_script($vueData->script, $vueData->url, null, null, true);
        wp_localize_script(
            $vueData->script, // vue script handle defined in wp_register_script.
          'vueData', // name of the javascript object that will made available to Vue.
          $vueData->data_array() // actual array that will become the object
        );

        wp_enqueue_script($vueData->script);
    }
}
