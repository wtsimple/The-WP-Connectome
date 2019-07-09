<?php

namespace Connectome;

// shortcode [connectome-graph]
function graph_container_shortcode()
{
    $optionsName = OptionStorage::get_option('OPTIONS_NAME');
    $max = get_options_max($optionsName);
    if (empty($max)) {
        $settingsPage = esc_url(admin_url('/options-general.php?page=' . $optionsName));
        return '<a href="' . $settingsPage . '"><h1 style="color:red;">Graph Not Ready. Check the settings and rebuild it</h1></a>';
    }

    return '<div id="connectome-graph-container"><span style="text-decoration:underline;color:red;">[The widget should be here. This could be a problem with javascript or the widget scripts. Also, you could save the options and rebuild the graph a couple of times to fix it]</span></div>';
}
add_shortcode('connectome-graph', '\Connectome\graph_container_shortcode');
