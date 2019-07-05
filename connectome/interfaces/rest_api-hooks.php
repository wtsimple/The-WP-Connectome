<?php namespace Connectome;
//Register all the rest api endpoints
add_action( 'rest_api_init', function () {
  register_rest_route( 'connectome/v1', '/graph', array(
    'methods' => 'GET',
    'callback' => '\Connectome\api_graph',
  ) );
} );
