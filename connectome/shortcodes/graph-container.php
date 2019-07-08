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
    // ob_start();

    return '<div id="connectome-graph-container"><span style="text-decoration:underline;color:red;">[The widget should be here,otherwise it is probably a problem with javascript or the widget scripts]</span></div>';

    // $temp_content = ob_get_contents();
  //   ob_end_clean();
  //   return $temp_content;
}
add_shortcode('connectome-graph', '\Connectome\graph_container_shortcode');
