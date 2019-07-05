<?php

namespace Connectome;

function graph_data()
{
    $graph = new SiteGraph();
    $graph->build_pruned_graph();
    return SiteGraph::get_graph();
}

function api_graph($data)
{
    return graph_data();
}
