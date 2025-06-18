<?php

add_action('wp_ajax_add_log_and_pass', 'add_log_and_pass');
add_action('wp_ajax_nopriv_add_log_and_pass', 'add_log_and_pass');
function add_log_and_pass() {
    // Переконуємось, що дані передані
    if (!isset($_POST['data']) || empty($_POST['data'])) {
        wp_send_json_error(['message' => 'Немає даних']);
    }

    // Декодуємо JSON-рядок у масив
    $received_data = json_decode(stripslashes($_POST['data']), true);

    // Перевіряємо, чи вдалося розпарсити JSON
    if (!is_array($received_data)) {
        wp_send_json_error(['message' => 'Невірний формат JSON']);
    }

    $new_entry = [
        'login' => sanitize_text_field($received_data['login']),
        'pass'  => sanitize_text_field($received_data['pass'])
    ];

    $result = sl_add_option('base_auth',$new_entry);
    $stored_data = sl_get_option('base_auth');
    // Якщо даних немає або вони не масив — створюємо пустий масив
    if (!$result) {
        // Повертаємо JSON-відповідь
        wp_send_json_error([
            'message' => 'Дані не додано!',
        ]);
    }

    // Повертаємо JSON-відповідь
    wp_send_json_success([
        'message' => 'Дані успішно додані!',
        'id' => $stored_data
    ]);
}


add_action('wp_ajax_get_log_and_pass', 'get_log_and_pass');
add_action('wp_ajax_nopriv_get_log_and_pass', 'get_log_and_pass');
function get_log_and_pass() {
    $stored_data = sl_get_option('base_auth');

    // Якщо $stored_data — рядок, намагаємось розпарсити JSON
    if (is_string($stored_data)) {
        $decoded = json_decode($stored_data, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $stored_data = [$decoded];
        }
    }elseif (is_array($stored_data)) {
        $stored_data = $stored_data;
    }

    wp_send_json_success([
        'message' => 'Дані отримано',
        'data' => $stored_data
    ]);
}


function remove_log_and_pass() {

    $received_data = stripslashes($_POST['data']);



    $status = sl_remove_option('base_auth',$received_data);

    // Перевіряємо, чи це масив
    if (!$status) {
        wp_send_json_error(['message' => 'Дані в базі некоректні']);
    }

    // Повертаємо JSON-відповідь
    wp_send_json_success([
        'message' => 'Запис успішно видалено!',
    ]);
}

// Реєструємо AJAX-хук
add_action('wp_ajax_remove_log_and_pass', 'remove_log_and_pass');
add_action('wp_ajax_nopriv_remove_log_and_pass', 'remove_log_and_pass');
