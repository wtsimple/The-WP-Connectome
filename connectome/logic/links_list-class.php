<?php

namespace Connectome;

/**
 *
 */
class LinkList
{
    /**
     * Array of links data
     *
     * @var array
     */
    private $linksArray;
    /**
     * Graph Data
     *
     * @var GraphData
     */
    private $graphData;

    public function __construct($graphData)
    {
        $this->linksArray = [];
        $this->graphData = $graphData;
    }

    /**
     * Creates a new list of links
     *
     * @return void
     */
    public function build_link_list()
    {
        $this->build_posts_links_to_target('author');
        $this->build_posts_links_to_target('terms');
    }

    /**
     * Adds links to link array coming from a post and going to
     * a field in the post given by target. This field should contain
     * an ID or a list of IDs for the targets.
     *
     * @param string $target name of the field used to get the target IDs
     * @return void
     */
    private function build_posts_links_to_target($target = '')
    {
        $delimiter = OptionStorage::get_option('ID_DELIMITER');
        // Loop through all post types
        foreach ($this->graphData->postTypes as $type => $postList) {
            // loop through the posts of that type
            $posts = $this->graphData->postTypes[$type]->get_objects();
            foreach ($posts as $post) {
                $postID = $type . $delimiter . $post->ID;
                $targetIDs = $this->graphData->postTypes[$type]->get_field_by_id($postID, $target);
                // Turn the IDs into an array if its a single string
                if (!is_array($targetIDs)) {
                    $targetIDs = [$targetIDs];
                }
                foreach ($targetIDs as $targetID) {
                    // Check wether the target exists
                    $targetType = explode($delimiter, $targetID)[0];
                    $targetElement = $this->graphData->get_elements_by_id($targetType, $targetID);
                    if (!empty($targetElement)) {
                        // Add the target IDs to the links array
                        $this->linksArray[] = [
                            'source' => $postID,
                            'target' => $targetID,
                            'value' => 5,
                        ];
                    }
                }
            }
        }
    }

    public function get_links_data()
    {
        return $this->linksArray;
    }
}
