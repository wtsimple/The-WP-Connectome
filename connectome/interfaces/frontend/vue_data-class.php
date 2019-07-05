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
    public $script = 'vue_build';
    /**
     * url where the vue script will be available
     * @var string
     */
    public $url;
    /**
     * WP base url to add new posts
     * @var  string
     */
    public $createPostBaseURL;

    public function __construct()
    {
        $this->createPostBaseURL = get_home_url() . '/wp-admin/post-new.php?post_type=';
        if (defined('CONNECTOME_DEVELOP') and CONNECTOME_DEVELOP) {
            $this->url = 'http://localhost:8080/dist/build.js';
        } else {
            $this->url = plugins_url() . '/connectome/enqueue/js/' . $this->script . '.js';
        }
    }

    //This array is intended to be passed to the vue script via wp_localize_script()
    public function data_array()
    {
        $data = [
            'restUrl' => untrailingslashit(esc_url_raw(rest_url())), // URL to the REST endpoint.
            'graph' => $this->graph_data(),
            'typesData' => $this->types_data(),
        ];
        // $data = array_merge($data, cpts_the_current_user_can_edit());
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
