<?php

namespace Connectome;

/**
 * Class to store global options
 */
class OptionStorage
{
    /**
     * This is the current way of storing the options
     *
     * @var array
     */
    private static $options = [
        'EXCLUDED_TYPES' => ['attachment', 'page', 'popup'],
        'OPTIONS_NAME' => 'connectome-options',
        'OPTIONS_STORAGE_NAME' => 'connectome-options-options-storage',
        'OPTIONS_GRAPH_NAME' => 'connectome-options-graph',
        'ID_DELIMITER' => '_connectome_',
        'COLOR_PALETTE' => [
            '#1f77b4',
            '#ff7f0e',
            '#2ca02c',
            '#d62728',
            '#9467bd',
            '#8c564b',
            '#e377c2',
            '#7f7f7f',
            '#bcbd22',
            '#17becf',
            '#aec7e8',
            '#ffbb78',
            '#98df8a',
            '#ff9896',
            '#c5b0d5',
            '#c49c94',
            '#f7b6d2',
            '#c7c7c7',
            '#dbdb8d',
            '#9edae5',
        ],
    ];

    /**
     * Gets the options by name
     *
     * @param string $name
     * @return mixed
     */
    public static function get_option(string $name)
    {
        // Try to get the option from the static array first
        if (isset(self::$options[$name])) {
            return self::$options[$name];
        } else {
            // If it's not in the static array, try to find it on the wp database
            $option = self::get_option_from_database($name);
            if ($option !== null) {
                return $option;
            }
            // Finally, if it's not in the DB either, echo an error
            // arm_dump('OptionStorage error: option name not found: ', $name);
            return null;
        }
    }

    /**
     * Save an option in a permanent storage
     *
     * @param string $name the name of the option
     * @param mixed $value value to be inserted
     * @return void
     */
    public static function save_option($name = '', $value = null)
    {
        // Get the options as an array
        $optionName = self::get_option('OPTIONS_STORAGE_NAME') ;
        $options = get_option($optionName);
        // Add or update the value under the name
        $options[$name] = $value;
        // Save the array back
        update_option($optionName, $options);
    }

    /**
     * Tries to retrieve an option from the wp database
     *
     * @param string $name
     * @return void
     */
    public static function get_option_from_database($name = '')
    {
        $optionName = self::get_option('OPTIONS_STORAGE_NAME') ;
        $options = get_option($optionName);
        if (isset($options[$name])) {
            return $options[$name];
        } else {
            return null;
        }
    }
}
