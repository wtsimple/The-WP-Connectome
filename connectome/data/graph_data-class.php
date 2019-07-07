<?php

namespace Connectome;

/**
 * Represents all the data contained in a graph
 */
class GraphData
{
    /**
     * The users object
     * @var ElementList
     */
    public $users;
    /**
     * Terms object
     * @var ElementList
     */
    public $terms;
    /**
     * Array of different post types objects
     * @var array
     */
    public $postTypes;

    public function __construct($users, $terms, $postTypes)
    {
        $this->users = $users;
        $this->terms = $terms;
        $this->postTypes = $postTypes;
    }

    /**
     * Executes a method over all instances of ElementList objects
     * with given arguments and returns the result
     *
     * @param string $type the type of element
     * @param string $method the name of the method
     * @param array $args the arguments to the method
     * @return mixed
     */
    public function execute_by_type($type = '', $method = '', $args = [])
    {
        switch ($type) {
            case 'user':
                return $this->users->$method(...$args);
                break;
            case 'term':
                return $this->terms->$method(...$args);
                break;
            default:
                return $this->postTypes[$type]->$method(...$args);
                break;
        }
    }

    /**
     * Returns all the elements of a given type that match an ID list
     *
     * @param array $type the type of element requested
     * @param array|string $idList
     * @param bool $getAll decides whether to return all elements, if true the $idList is ignored
     * @return array list of elements matching the IDs
     */
    public function get_elements_by_id($type = '', $idList = null, $getAll = false)
    {
        $returnWholeElement = true;
        if ($getAll) {
            return $this->execute_by_type($type, 'get_objects', [$returnWholeElement]);
        }
        return $this->execute_by_type($type, 'get_objects_by_id', [$idList, $returnWholeElement]);
    }

    /**
     * Write an element field value
     *
     * @param string $type the type of the element to write
     * @param string $id the id of the element
     * @param string $field the name of the field to be written
     * @param mixed $value the value to write on the element's field
     * @return void
     */
    public function write_element_field_by_id($type = '', $id = '', $field = '', $value = null)
    {
        $this->execute_by_type($type, 'add_field_by_id', [$id, $field, $value]);
    }

    /**
     * Deletes an element of given type with the ID
     *
     * @param string $type type of element to be destroyed
     * @param string $id id of the element to delete
     * @return void
     */
    public function remove_element_by_id($type = '', $id = '')
    {
        $this->execute_by_type($type, 'remove_element_by_id', [$id]);
    }

    /**
     * Rank all elements of a given type by a field and keep only a given amount
     *
     * @param string $type the type of element to be ranked and pruned
     * @param int $amount how many elements to keep
     * @param string $field name of the field to do the ranking
     * @return void
     */
    public function prune_by_type($type = '', $amount = 0, $field = 'degree')
    {
        $this->execute_by_type($type, 'prune_by_field', [$amount, $field]);
    }

    /**
     * Deletes all elements that are disabled in the options
     * @return void
     */
    public function remove_disabled_elements()
    {
        // Delete terms
        $terms = $this->terms->get_objects(true);
        $this->remove_type_disabled($terms, 'term');
        // Delete users
        $users = $this->users->get_objects(true);
        $this->remove_type_disabled($users, 'user');
        // Loop through post types
        $supraPost = OptionStorage::get_option('POST_TYPE_SUPRA');
        foreach ($this->postTypes as $type => $postObj) {
            $elements = $postObj->get_objects(true);
            $this->remove_type_disabled($elements, $type, $supraPost);
        }
    }

    /**
     * Loop through a type of element eliminating the disabled
     *
     * @param array $elements list of the elements
     * @param string $type name of the element type
     * @param string $supraType supra type like 'postTypes' if exists
     * @return void
     */
    public function remove_type_disabled($elements = [], $type = '', $supraType = '')
    {
        $handler = new SettingsOptionHandler();
        $options = get_option(OptionStorage::get_option('OPTIONS_NAME'));
        // Loop through elements
        foreach ($elements as $key => $element) {
            // check whether the isActive option exists
            $name = $handler->generate_name(['types', $supraType, $type, 'elements', $element['id'], 'isActive']);
            if (!isset($options[$name])) {
                $this->remove_element_by_id($type, $element['id']);
            }
        }
    }
}
