<?php

namespace Connectome;

/**
 * Represents a list of graph elements like users, terms or posts
 *
 * Facilitates the retrieving by ID, sorting by a field, etc.
 */
class ElementList
{
    /**
     * The array to store the core data of the list
     *
     * Fields:
     * "object" => the actual WP object representing the element.
     * "id" => a unique identifier <name>_<number> like post_33.
     * 'Meta' Fields.
     * "degree" => the most elementary centrality measure.
     * @var array
     */
    private $data;
    /**
     * Name of the type represented by the list, something like "post" or "term"
     * @var string
     */
    private $type;
    private $delimiter;

    public function __construct($type)
    {
        $this->type = $type;
        $this->delimiter = OptionStorage::get_option('ID_DELIMITER');
    }

    // ------ ADDING DATA ---------------

    /**
     * Creates data elements from a list of objects.
     *
     * Transformations it does:
     * Adds an ID per each element.
     * @param array $objects
     * @return void
     */
    public function build_data_from_objects($objects)
    {
        $this->data = array_map(function ($object) {
            $datum = [];
            $datum['object'] = $object;
            if ($this->type === 'term') {
                $datum['id'] = 'term' . $this->delimiter . $object->term_id;
            } else {
                $datum['id'] = $this->type . $this->delimiter . $object->ID;
            }
            return $datum;
        }, $objects);
    }

    /**
     * Sets the value of a field for the element of given id
     *
     * @param string $id the element id
     * @param string $field the name of the field
     * @param mixed $value the value to be written on the field
     * @return void
     */
    public function add_field_by_id($id, $field, $value)
    {
        $key = $this->get_element_key_from_id($id);
        $this->data[$key][$field] = $value;
    }

    // ------ SORTING AND PRUNING DATA ----------------

    /**
     * Sorts the data array by a field value in descending order
     *
     * @param string $field name of the field used to do the ranking
     * @return void
     */
    public function sort_by_field($field = 'centrality')
    {
        usort($this->data, function ($datumA, $datumB) use ($field) {
            return $datumA[$field] > $datumB[$field] ? -1 : 1;
        });
    }

    /**
     * Ranks the elements by a field value an removes all with lower value, keeping
     * only certain amount
     *
     * @param integer $amount how many elements to keep
     * @param string $field name of the field to rank the elements
     * @return void
     */
    public function prune_by_field($amount = 0, $field = 'centrality')
    {
        $this->sort_by_field($field);
        if ($amount === -1) {
            $amount = sizeof($this->data);
        }
        $this->data = array_slice($this->data, 0, $amount);
    }

    /**
     * Deletes an element from the data
     *
     * @param string $id
     * @return void
     */
    public function remove_element_by_id($id)
    {
        $key = $this->get_element_key_from_id($id);
        unset($this->data[$key]);
    }

    // ---- GETTERS ---------------

    /**
     * Gets an element key (its location on the data array)
     * from its id
     *
     * @param string $id the element id, should be a type and a number, like 'post_33'
     * @return int the element key
     */
    private function get_element_key_from_id($id = '')
    {
        $elements = array_filter($this->data, function ($datum) use ($id) {
            return $datum['id'] === $id;
        });
        $firstKey = array_key_first($elements);
        $element = $elements[$firstKey];
        $key = array_search($element, $this->data);
        return $key;
    }

    /**
     * Returns the value of a field for the element of given id
     *
     * @param string $id the element id
     * @param string $field the name of the field
     * @return mixed the value of the field
     */
    public function get_field_by_id($id = '', $field = '')
    {
        $key = $this->get_element_key_from_id($id);
        return $this->data[$key][$field];
    }

    /**
     * Returns an array of all the objects contained in the list
     * @param bool $wholeElement decides if to return only the object or the whole element
     * @return array all the objects contained in the list
     */
    public function get_objects($wholeElement = false)
    {
        if ($wholeElement) {
            return $this->data;
        }
        return array_map(function ($datum) {
            return $datum['object'];
        }, $this->data);
    }

    /**
     * Returns the elements with matching IDs
     *
     * @param string|array $idList the IDs to perform the matching
     * @param bool $wholeElement decides if to return only the object or the whole element
     * @return array|object the element(s) selected by the IDs, could be the wp objects or the whole elements
     */
    public function get_objects_by_id($idList = null, $wholeElement = false)
    {
        $single = false;
        if (!is_array($idList)) {
            $idList = [$idList];
            $single = true;
        }
        // Filter the elements to find the matchings IDs
        $selectedElements = array_filter($this->data, function ($datum) use ($idList) {
            return in_array($datum['id'], $idList);
        });
        // Obtain the objects from the selected elements
        if ($wholeElement) {
            $selectedObjects = $selectedElements;
        } else {
            $selectedObjects = array_map(function ($element) use ($wholeElement) {
                return $element['object'];
            }, $selectedElements);
        }

        if ($single) {
            $firstKey = array_key_first($selectedObjects);
            $object = isset($selectedObjects[$firstKey]) ? $selectedObjects[$firstKey] : null;
            return $object;
        }

        return $selectedObjects;
    }

    /**
     * Returns the type of element the list contains
     *
     * Examples: 'post', 'user', 'term', etc.
     * @return string the type of elements contained in the list
     */
    public function get_type()
    {
        return $this->type;
    }
}
