<?php
function add_endpoint_type() {
    $received_data = stripslashes($_POST['data']);


    $status = sl_add_option('endpoint_type', $received_data);

    if (!$status){
        wp_send_json_error(['message' => 'Тип збережено']);
    }else{
        wp_send_json_success(['message' => 'Тип НЕ збережено', 'data' => $status]);
    }

}

add_action('wp_ajax_add_endpoint_type', 'add_endpoint_type');
add_action('wp_ajax_nopriv_add_endpoint_type', 'add_endpoint_type');

function get_endpoint_type() {

    $status = sl_get_option('endpoint_type');

    if (!$status){
        wp_send_json_error(['message' => 'Типи отримано']);
    }else{
        wp_send_json_success(['message' => 'Типи НЕ отримано', 'data' => $status]);
    }

}

add_action('wp_ajax_get_endpoint_type', 'get_endpoint_type');
add_action('wp_ajax_nopriv_get_endpoint_type', 'get_endpoint_type');

function remove_endpoint_type() {
    if (!isset($_POST['data'])) {
        error_log('remove_endpoint_type: data not set');
        wp_send_json_error(['message' => 'Дані не передані']);
    }
    $raw_data = $_POST['data'];

    $received_data = stripslashes($raw_data);

    $status = sl_remove_option('endpoint_type', $received_data);

    if (!$status){
        wp_send_json_error(['message' => 'Типи НЕ заявки видалено!']);
    } else {
        wp_send_json_success(['message' => 'Тип заявки видалено!', 'data' => $status]);
    }
}


add_action('wp_ajax_remove_endpoint_type', 'remove_endpoint_type');
add_action('wp_ajax_nopriv_remove_endpoint_type', 'remove_endpoint_type');

function edit_endpoint_type() {
    if (!isset($_POST['data'])) {
        error_log('remove_endpoint_type: data not set');
        wp_send_json_error(['message' => 'Дані не передані']);
    }
    $raw_data = $_POST['data'];

    $received_data = json_decode(stripslashes($raw_data),true);
    $old_data = json_encode($received_data[0]);
    $new_data = json_encode($received_data[1]);

    $status = sl_edit_options('endpoint_type', $old_data, $new_data);
    if (!$status){
        wp_send_json_error(['message' => 'Тип заявки НЕ змінено!']);
    } else {
        wp_send_json_success(['message' => 'Тип заявки змінено!']);
    }
}


add_action('wp_ajax_edit_endpoint_type', 'edit_endpoint_type');
add_action('wp_ajax_nopriv_edit_endpoint_type', 'edit_endpoint_type');

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



