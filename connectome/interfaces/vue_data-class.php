<?php

namespace Connectome;

//A class to return/handle all the configuration data the VUE app needs to
//get from WordPress
/**
 *
 */
class VueData
{
    /**
     * vue script handle defined in wp_register_script
     * @var string
     */
    public $script = 'vue_connectome_build';
    /**
     * url where the vue script will be available
     * @var string
     */
    public $url;

    public function __construct()
    {
        // $this->url = 'http://localhost:8080/dist/build.js';
        $this->url = plugins_url('enqueue/js/' . $this->script . '.js', dirname(__FILE__));
    }

    //This array is intended to be passed to the vue script via wp_localize_script()
    public function data_array()
    {
        $data = [
            'restUrl' => untrailingslashit(esc_url_raw(rest_url())), // URL to the REST endpoint.
            'graph' => $this->graph_data(),
            'typesData' => $this->types_data(),
        ];
        return $data;
    }

    public function is_vue_needed()
    {
        return true; //is_user_logged_in();
    }

    public function graph_data()
    {
        return graph_data();
    }

    public function types_data()
    {
        $optionName = OptionStorage::get_option('OPTIONS_NAME');
        $colorPalette = OptionStorage::get_option('COLOR_PALETTE');
        $max = get_options_max($optionName);
        $typesData = [];
        $index = 0;
        foreach ($max as $type => $value) {
            $typesData[$type] = [
                'color' => $colorPalette[$index],
                'plural' => ucfirst($type) . 's',
            ];

            $index++;
        }
        return $typesData;
    }
}
