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

    // Отримуємо поточне значення з wp_options
    $stored_data = get_option('auth_data_list', []);

    // Якщо даних немає або вони не масив — створюємо пустий масив
    if (!is_array($stored_data)) {
        $stored_data = [];
    }

    // Додаємо новий запис
    $stored_data[] = $new_entry;

    // Оновлюємо `wp_options`
    update_option('auth_data_list', $stored_data);

    // Повертаємо JSON-відповідь
    wp_send_json_success([
        'message' => 'Дані успішно додані!',
        'updated_data' => $stored_data
    ]);
}


add_action('wp_ajax_get_log_and_pass', 'get_log_and_pass');
add_action('wp_ajax_nopriv_get_log_and_pass', 'get_log_and_pass');
function get_log_and_pass() {
    // Отримуємо поточне значення з wp_options
    $stored_data = get_option('auth_data_list', []);


    // Повертаємо JSON-відповідь
    wp_send_json_success([
        'message' => 'Дані успішно додані!',
        'data' => $stored_data
    ]);
}

function remove_log_and_pass() {

    // Декодуємо JSON-рядок у масив
    $received_data = json_decode(stripslashes($_POST['data']), true);

    $removed_data = [
        'login' => sanitize_text_field($received_data['login']),
        'pass'  => sanitize_text_field($received_data['pass'])
    ];

    // Отримуємо список логінів та паролів
    $stored_data = get_option('auth_data_list', []);

    // Перевіряємо, чи це масив
    if (!is_array($stored_data)) {
        wp_send_json_error(['message' => 'Дані в базі некоректні']);
    }

    // Фільтруємо масив, видаляючи відповідний запис
    $updated_data = array_filter($stored_data, function ($item) use ($removed_data) {
        return !($item['login'] === $removed_data['login'] && $item['pass'] === $removed_data['pass']);
    });

    // Оновлюємо wp_options
    update_option('auth_data_list', array_values($updated_data));

    // Повертаємо JSON-відповідь
    wp_send_json_success([
        'message' => 'Запис успішно видалено!',
        'updated_data' => $updated_data
    ]);
}

// Реєструємо AJAX-хук
add_action('wp_ajax_remove_log_and_pass', 'remove_log_and_pass');
add_action('wp_ajax_nopriv_remove_log_and_pass', 'remove_log_and_pass');
