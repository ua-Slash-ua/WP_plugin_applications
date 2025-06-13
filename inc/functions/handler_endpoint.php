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


function add_endpoint() {
    if (!isset($_POST['data'])) {
        wp_send_json_error(['message' => 'Дані не передані']);
    }
    $raw_data = $_POST['data'];
    $received_data = stripslashes($_POST['data']);


    $status = sl_add_option('endpoint', $received_data);
    error_log($raw_data);
    if (!$status){
        wp_send_json_error(['message' => 'Тип заявки НЕ змінено!']);
    } else {
        wp_send_json_success(['message' => 'Тип заявки змінено!']);
    }
}


add_action('wp_ajax_add_endpoint', 'add_endpoint');
add_action('wp_ajax_nopriv_add_endpoint', 'add_endpoint');

function get_endpoint() {

    $status = sl_get_option('endpoint');
    if (!$status){
        wp_send_json_error(['message' => 'Едпоінти НЕ отримано!']);
    }else{
        wp_send_json_success(['message' => 'Типи отримано', 'data' => $status]);
    }

}

add_action('wp_ajax_get_endpoint', 'get_endpoint');
add_action('wp_ajax_nopriv_get_endpoint', 'get_endpoint');


function finder_options() {
    $data = json_decode(stripslashes($_POST['data']));
    $key = $data['key'] ?? null;
    $value = $data['value'];

    $status = sl_find_option($value,$key);
    if (!$status){
        wp_send_json_success(['message' => 'Даних не знайдено!']);
    }else{
        wp_send_json_error(['message' => 'Такі дані уже існують!', 'data' => $status]);
    }

}

add_action('wp_ajax_finder_options', 'finder_options');
add_action('wp_ajax_nopriv_finder_options', 'finder_options');


