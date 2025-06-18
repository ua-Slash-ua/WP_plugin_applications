<?php
function sl_handle_add_option() {
    $data = json_decode(stripslashes($_POST['data']), true);
    $key = $data['key'] ?? null;
    $value = $data['value'] ?? null;

    if (!$key || !$value) {
        wp_send_json_error(['message' => 'Некоректні вхідні дані']);
    }

    $status = sl_add_option($key, $value);

    if (!$status) {
        wp_send_json_error(['message' => "Опцію для '{$key}' не збережено"]);
    } else {
        wp_send_json_success(['message' => "Опцію для '{$key}' збережено"]);
    }
}

add_action('wp_ajax_sl_add_option', 'sl_handle_add_option');
add_action('wp_ajax_nopriv_sl_add_option', 'sl_handle_add_option');



function sl_handle_get_option() {
    $data = json_decode(stripslashes($_POST['data']), true);
    $key = $data['key'] ?? null;

    if (!$key) {
        wp_send_json_error(['message' => 'Ключ не переданий']);
    }

    $status = sl_get_option($key);
//    error_log(print_r($_POST['data'], true));
//    error_log(print_r($status, true));

    if (!$status) {
        wp_send_json_error(['message' => "Опції для '{$key}' не знайдено"]);
    } else {
        wp_send_json_success(['message' => "Опції для '{$key}' отримано", 'data' => $status]);
    }
}

add_action('wp_ajax_sl_get_option', 'sl_handle_get_option');
add_action('wp_ajax_nopriv_sl_get_option', 'sl_handle_get_option');



function sl_handle_remove_option() {
    $data = json_decode(stripslashes($_POST['data']), true);
    $key = $data['key'] ?? null;
    $value = $data['value'] ?? null;
    $opId = $data['opId'] ?? null;

    if (!$key ) {
        wp_send_json_error(['message' => 'Ключ або значення не передано']);
    }
//    error_log('$opId'.$opId);

    // Якщо $value масив, кодуємо в JSON (щоб передати рядок у sl_remove_option)
    if (is_array($value)) {
        $value = json_encode($value,JSON_UNESCAPED_UNICODE);
    }
//    error_log('$value'.$value);

    $status = sl_remove_option($key, $value, optionId: $opId);

    if (!$status) {
        wp_send_json_error(['message' => "Не вдалося видалити опцію для '{$key}'"]);
    } else {
        wp_send_json_success(['message' => "Опцію для '{$key}' видалено", 'data' => $status]);
    }
}



add_action('wp_ajax_sl_remove_option', 'sl_handle_remove_option');
add_action('wp_ajax_nopriv_sl_remove_option', 'sl_handle_remove_option');



function sl_handle_edit_option() {
    $data = json_decode(stripslashes($_POST['data']), true);
    $key = $data['key'] ?? null;
    $old = json_encode($data['value'][0] ?? null,JSON_UNESCAPED_UNICODE);
    $new = json_encode($data['value'][1] ?? null,JSON_UNESCAPED_UNICODE);
    if (!$key || !$old || !$new) {
        wp_send_json_error(['message' => 'Ключ або дані для редагування не передані']);
    }

    $status = sl_edit_options($key, $old, $new);

    if (!$status) {
        wp_send_json_error(['message' => "Опцію для '{$key}' не оновлено"]);
    } else {
        wp_send_json_success(['message' => "Опцію для '{$key}' оновлено"]);
    }
}

add_action('wp_ajax_sl_edit_option', 'sl_handle_edit_option');
add_action('wp_ajax_nopriv_sl_edit_option', 'sl_handle_edit_option');





function finder_options() {
    $data = json_decode(stripslashes($_POST['data']));
    $key = $data->key ?? null;
    $value = $data->value;

    $status = sl_find_option($value,$key);

    if (!$status){
        wp_send_json_success(['message' => 'Даних не знайдено!']);
    }else{
        wp_send_json_error(['message' => 'Такі дані уже існують!', 'data' => $status]);
    }

}

add_action('wp_ajax_finder_options', 'finder_options');
add_action('wp_ajax_nopriv_finder_options', 'finder_options');


