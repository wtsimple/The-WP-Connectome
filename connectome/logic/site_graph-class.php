<?php

namespace Connectome;

/**
 * Handles the whole graph representing the site
 *
 * It's based in other classes such as NodeList, LinkList, and GraphData,
 * that provide more low-level access to data
 */
class SiteGraph
{
    private $nodeList;
    private $linkList;
    /**
     * Graph Data object that encapsulates all the data
     *
     * @var GraphData
     */
    public $graphData;
    /**
     * The graph as it's sent to the front end
     * @var array
     */
    public $plainData;
    public static $baseFolder = WP_PLUGIN_DIR . '/connectome/';

    public function __construct()
    {
        $this->graphData = new GraphData(new ElementList('user'), new ElementList('term'), []);
    }

    /**
     * Returns the already stored graph, without building it
     * @return array
     */
    public static function get_graph()
    {
        $optionName = OptionStorage::get_option('OPTIONS_GRAPH_NAME');
        $graphRaw = get_option($optionName, '');
        $graphData = json_decode($graphRaw);

        if (empty($graphData)) {
            $graph = new SiteGraph();
            $graph->build_pruned_graph();
            self::get_graph();//Recursive
        }

        return $graphData;
    }

    /**
     * Builds a pruned graph, ready to use
     * @return void
     */
    public function build_pruned_graph()
    {
        $this->prepare_all_elements();
        $this->build_graph();
        $this->prune_graph();
        $this->save_graph();
    }

    /**
     * Builds the whole graph again, wipes out the old one and returns the new built graph
     *
     * @return void
     */
    public function build_graph($keepOldCentrality = false)
    {
        // Build the graph elements
        $this->nodeList = new NodeList($this->graphData);
        $this->linkList = new LinkList($this->graphData);

        $this->nodeList->build_node_list();
        $this->linkList->build_link_list();

        // Collect the transformed users, terms and postTypes
        $this->graphData = $this->nodeList->get_graph_data();

        // build the plain data needed to show the graph in the front end
        $this->build_plain_graph_data();

        $this->build_degree_centrality($keepOldCentrality);
    }

    /**
     * Adds the degree centrality to each node
     *
     * The degree centrality for a node is simply the amount of
     * links connecting with the node.
     * @param boolean $keepOldCentrality if true the original centrality is kept
     * @return void
     */
    private function build_degree_centrality($keepOldCentrality = false)
    {
        // If the old degree is to be kept, then there's nothing to do
        if (!$keepOldCentrality) {
            // We just need to count how many links connect to the node
            // Loop through all nodes
            foreach ($this->plainData['nodes'] as $node) {
                $degree = 0;
                $id = $node['id'];
                // For each node search all links
                foreach ($this->plainData['links'] as $link) {
                    // Increase degree if the node is the link's source or target
                    if ($link['source'] === $id or $link['target'] === $id) {
                        $degree++;
                    }
                }
                $this->graphData->write_element_field_by_id($node['type'], $id, 'degree', $degree);
            }
        }
    }

    /**
     * Removes the excess of elements by cutting down the less central.
     *
     * Rebuilds the graph afterwards.
     * @return void
     */
    public function prune_graph()
    {
        // Get max amount of each type of element
        $optionsName = OptionStorage::get_option('OPTIONS_NAME');
        $max = $this->get_options_max($optionsName);

        // Sort and prune the element lists by the importance measure
        foreach ($max as $type => $maxValue) {
            $this->graphData->prune_by_type($type, $maxValue, 'degree');
        }
        // Rebuilt the graph with the pruned element lists
        $this->build_graph(true);
    }

    /**
     * Sets users, terms and post types data to do the first graph built
     * @return void
     */
    public function prepare_all_elements()
    {
        $this->graphData->users->build_data_from_objects(get_users());
        $this->graphData->terms->build_data_from_objects($this->get_all_terms());
        $types = $this->get_post_types();
        foreach ($types as $type) {
            $this->graphData->postTypes[$type] = new ElementList($type);
            $this->graphData->postTypes[$type]->build_data_from_objects(get_posts(
                [
                    'posts_per_page' => -1,
                    'post_type' => $type,
                ]
            ));
        }
    }

    /**
     * Creates the data the front end needs to render the graph
     *
     * @return void
     */
    public function build_plain_graph_data()
    {
        $wholeElement = true;
        $elementTypes = [];
        $elementTypes['user'] = $this->graphData->users->get_objects($wholeElement);
        $elementTypes['term'] = $this->graphData->terms->get_objects($wholeElement);
        foreach ($this->graphData->postTypes as $type => $elementList) {
            $elementTypes[$type] = $elementList->get_objects($wholeElement);
        }
        $plainData = ['nodes' => [], 'links' => []];
        foreach ($elementTypes as $type => $elements) {
            foreach ($elements as $key => $element) {
                $plainData['nodes'][] = [
                    'id' => $element['id'],
                    'label' => $element['label'],
                    'type' => $type,
                    'url' => $element['url'],
                    'excerpt' => $element['excerpt'],
                    'image' => isset($element['image']) ? $element['image'] : '',
                    'degree' => isset($element['degree']) ? $element['degree'] : '',
                ];
            }
        }

        $plainData['links'] = $this->linkList->get_links_data();
        $this->plainData = $plainData;
    }

    /**
     * Saves the graph in the format that the front end needs to render it
     *
     * @return void
     */
    private function save_graph()
    {
        // Save on a file for inspection only under development
        if (CONNECTOME_DEVELOP) {
            $json = json_encode($this->plainData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            file_put_contents(self::$baseFolder . 'data/graph.json', $json);
        }
        $optionName = OptionStorage::get_option('OPTIONS_GRAPH_NAME');
        $json = json_encode($this->plainData, JSON_UNESCAPED_SLASHES);
        update_option($optionName, $json);
    }

    /**
     * Gets all the terms in all the taxonomies
     *
     * @return array the terms
     */
    public function get_all_terms()
    {
        $taxonomies = get_taxonomies();
        $terms = [];
        foreach ($taxonomies as $tax) {
            $terms = array_merge($terms, get_terms(['taxonomy' => $tax]));
        }
        return $terms;
    }

    /**
     * Returns an array with the max amount per type of element
     *
     * @param array $options
     * @return array
     */
    public function get_options_max($optionName)
    {
        return get_options_max($optionName);
    }

    /**
     * Returns all the post types that we'll use as nodes
     *
     * @return array the post types
     */
    public function get_post_types()
    {
        $types = get_all_post_types();

        return $types;
    }
}
