<?php
function sl_handle_get_applications() {

    $applications = get_applications();

    if (!$applications) {
        wp_send_json_error(['message' => "Заявки не отримано!"]);
    } else {
        wp_send_json_success(['message' => "Заявки  отримано!","data" => $applications]);
    }
}

add_action('wp_ajax_sl_get_applications', 'sl_handle_get_applications');
add_action('wp_ajax_nopriv_sl_get_applications', 'sl_handle_get_applications');