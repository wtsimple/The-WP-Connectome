<?php

namespace Connectome;

/**
 * Handles the list of nodes of the graph
 */
class NodeList
{
    /**
     * Graph Data
     * @var GraphData
     */
    private $graphData;
    /**
     * Remembers whether the posts data has been set
     * @var bool
     */
    private $arePostsBuilt;
    public $delimiter;

    public function __construct($graphData/* $users, $terms, $postTypes */)
    {
        $this->arePostsBuilt = false;
        $this->graphData = $graphData;
        $this->delimiter = OptionStorage::get_option('ID_DELIMITER');
    }

    /**
     * Sets the data inside of the node elements
     *
     * @return void
     */
    public function build_node_list()
    {
        $this->build_posts();
        $this->build_terms();
        $this->build_users();
    }

    /**
     * Sets the data for the posts elements
     *
     * @return void
     */
    private function build_posts()
    {
        // ----> Add properties needed for later rendering to all posts types
        // Loop through all types of posts
        foreach ($this->graphData->postTypes as $type => $postList) {
            // loop through the posts of that type
            $posts = $this->graphData->postTypes[$type]->get_objects();
            foreach ($posts as $post) {
                // Add label
                $this->graphData->postTypes[$type]->add_field_by_id($type . $this->delimiter . $post->ID, 'label', $post->post_title);
                // Add url
                $this->graphData->postTypes[$type]->add_field_by_id($type . $this->delimiter . $post->ID, 'url', get_permalink($post));
                // Add excerpt
                $this->graphData->postTypes[$type]->add_field_by_id($type . $this->delimiter . $post->ID, 'excerpt', wp_trim_words(get_the_excerpt($post)));
                // Add image
                $this->graphData->postTypes[$type]->add_field_by_id($type . $this->delimiter . $post->ID, 'image', get_the_post_thumbnail_url($post));
                // Add author
                $this->graphData->postTypes[$type]->add_field_by_id($type . $this->delimiter . $post->ID, 'author', 'user' . $this->delimiter . $post->post_author);
            }
        }

        $this->arePostsBuilt = true;
    }

    /**
     * Sets the data for the user elements
     *
     * can run it whenever you want :)
     * @return void
     */
    private function build_users()
    {
        // ----> Add properties needed for later rendering to all users
        // Loop through all users
        $users = $this->graphData->users->get_objects();
        foreach ($users as $user) {
            // Add label
            $this->graphData->users->add_field_by_id('user' . $this->delimiter . $user->ID, 'label', $user->display_name);
            // Add url
            $this->graphData->users->add_field_by_id('user' . $this->delimiter . $user->ID, 'url', get_author_posts_url($user->ID));
            // Add excerpt
            $this->graphData->users->add_field_by_id('user' . $this->delimiter . $user->ID, 'excerpt', wp_trim_words($user->description));
        }
    }

    /**
     * Sets the data for the terms elements
     *
     * Needs to be run after build_posts()
     * @return void
     */
    private function build_terms()
    {
        // Check if the posts are built
        if (!$this->arePostsBuilt) {
            $this->build_posts();
        }
        // ---> Add the terms to all the posts of different types to later build links

        // Loop through all types of posts
        foreach ($this->graphData->postTypes as $type => $postList) {
            // loop through the posts of that type
            $posts = $this->graphData->postTypes[$type]->get_objects();
            foreach ($posts as $post) {
                // Find the terms applying to the post
                // Get the taxonomies of the post and its terms
                $taxonomies = get_object_taxonomies(get_post($post->ID), 'names');
                $terms = wp_get_object_terms($post->ID, $taxonomies);
                $terms = array_map(function ($term) { return 'term' . $this->delimiter . $term->term_id; }, $terms);
                // Add the terms to the post element within its given ElementList object
                $this->graphData->postTypes[$type]->add_field_by_id($type . $this->delimiter . $post->ID, 'terms', $terms);
            }
        }

        // ----> Add properties needed for later rendering to all terms
        // Loop through the terms
        $terms = $this->graphData->terms->get_objects();
        foreach ($terms as $term) {
            // Add label
            $this->graphData->terms->add_field_by_id('term' . $this->delimiter . $term->term_id, 'label', $term->name);
            // Add url
            $this->graphData->terms->add_field_by_id('term' . $this->delimiter . $term->term_id, 'url', get_category_link($term->term_id));
            // Add excerpt
            $this->graphData->terms->add_field_by_id('term' . $this->delimiter . $term->term_id, 'excerpt', wp_trim_words(term_description($term->term_id)));
        }
    }

    public function get_graph_data()
    {
        return $this->graphData;
    }
}
