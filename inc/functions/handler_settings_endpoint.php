<?php


function create_dynamic_endpoint() {
    $received_data = json_decode(stripslashes($_POST['data']), true);

    $base = sanitize_text_field($received_data['base_route']);
    $route = sanitize_text_field($received_data['end_route']);

    $stored = get_option('dynamic_endpoints', []);

    $stored[] = [
        'base' => $base,
        'route' => $route
    ];

    update_option('dynamic_endpoints', $stored);

    wp_send_json_success(['message' => 'Ендпоінт збережено']);
}

add_action('wp_ajax_create_dynamic_endpoint', 'create_dynamic_endpoint');
add_action('wp_ajax_nopriv_create_dynamic_endpoint', 'create_dynamic_endpoint');
add_action('rest_api_init', function () {
    $endpoints = get_option('dynamic_endpoints', []);

    foreach ($endpoints as $endpoint) {
        register_rest_route($endpoint['base'], '/' . $endpoint['route'], array(
            'methods'  => 'GET',
            'callback' => function () use ($endpoint) {
                return new WP_REST_Response([
                    'message' => 'Ви викликали динамічний ендпоінт: ' . $endpoint['base'] . '/' . $endpoint['route']
                ]);
            },
            'permission_callback' => '__return_true',
        ));
    }
});



