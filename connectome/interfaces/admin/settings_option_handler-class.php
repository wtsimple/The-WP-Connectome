<?php

namespace Connectome;

/**
 * Handles the options available and stored via the Settings page
 */
class SettingsOptionHandler
{
    /**
     * The name of the settings option (the one you need for get_option )
     * @var string
     */
    private $optionGroup;
    /**
     * The separator for the "directions" in the options names
     * @var string
     */
    public $separator;

    public function __construct()
    {
        $this->optionGroup = OptionStorage::get_option('OPTIONS_NAME');
        $this->separator = '-->';
    }

    /**
     * Returns the right name for an option stored in the settings options
     *
     * @param array $directions a list of direction names to find the option
     * @return string the name of the option
     */
    public function generate_name(array $directions)
    {
        $directions = array_filter($directions, function ($direction) { return !empty($direction);});
        $name = implode($this->separator, $directions);
        return $name;
    }

    /**
     * Returns an option stored in the settings options
     *
     * @param array $args a list of direction names to find the option
     * @return mixed the settings option
     */
    public function get_option(array $args)
    {
        $name = $this->generate_name($args);
        $fullOption = get_option($this->optionGroup);
        $option = $fullOption[$name];
        return $option;
    }

    /**
     * Finds all the keys for the options that match certain name
     *
     * @param string $name
     * @return array options keys that match the name
     */
    public function get_matching_options($name)
    {
        $fullOption = get_option($this->optionGroup);
        $regexp = '/' . $this->separator . $name . '/';
        $keys = [];
        if (!empty($fullOption) and is_array($fullOption)) {
            $keys = ArrayTransform::get_matching_keys($fullOption, $regexp);
        }
        return $keys;
    }
}
