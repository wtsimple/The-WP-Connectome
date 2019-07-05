<?php

namespace Connectome;

/**
 * Debugging function: echoes a value
 *
 * @param string $name a name to show before the value
 * @param mixed $var the value to print
 * @return void
 */
function arm_dump($name, $var)
{
    if (CONNECTOME_DEVELOP) {
        echo '<br>' . $name . ' --- ';
        var_dump($var);
        echo '----- <br>';
    }
}

/**
 * Debugging function: saves a value in a file
 *
 * @param string $filename name of the file where the value will be saved
 * @param mixed $var the value to be saved
 * @return void
 */
function arm_log($filename, $var)
{
    if (CONNECTOME_DEVELOP) {
        file_put_contents($filename, var_export($var, true));
    }
}

/**
 * Handles several array manipulations
 */
class ArrayTransform
{
    /**
     * Recursive function that eliminates duplicates in multidimensional arrays
     *
     * Copied from the php manual (see array_unique page)
     * @param array $array
     * @return void
     */
    public static function super_unique($array)
    {
        $result = array_map('unserialize', array_unique(array_map('serialize', $array)));
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::super_unique($value);
            }
        }
        return $result;
    }

    /**
     * Takes an array indexed anyhow and returns an array with
     * the same elements, but indexed continuously.
     * @param array $array
     * @return array the reindexed array
     */
    public static function reindex_array($array)
    {
        $reindexed = [];
        foreach ($array as $key => $value) {
            $reindexed[] = $value;
        }
        return $reindexed;
    }

    /**
     * Provides support for something like array_unique
     * but for multidimensional arrays.
     * @param array $array
     * @return array the array with unique fields
     */
    public static function unique($array)
    {
        $unique = self::super_unique($array);
        return self::reindex_array($unique);
    }

    /**
     * Returns the last key of an array
     *
     * note in php>= 7.3 you can use array_key_last() directly
     * @param array $array
     * @return mixed the last key
     */
    public static function last_key($array)
    {
        $key = null;
        if (is_array($array)) {
            end($array);
            $key = key($array);
        }
        return $key;
    }

    /**
     * Returns the keys of an array that match a regular expression
     *
     * @param array $array
     * @param string $regexp regular expression to filter the keys
     * @return array matching keys
     */
    public static function get_matching_keys($array, $regexp)
    {
        $keys = array_keys($array);
        return preg_grep($regexp, $keys);
    }
}
