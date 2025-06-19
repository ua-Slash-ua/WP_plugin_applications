<?php
function sl_handle_get_applications(): void
{

    $applications = get_applications();

    if (!$applications) {
        wp_send_json_error(['message' => "Заявки не отримано!"]);
    } else {
        wp_send_json_success(['message' => "Заявки  отримано!","data" => $applications]);
    }
}

add_action('wp_ajax_sl_get_applications', 'sl_handle_get_applications');
add_action('wp_ajax_nopriv_sl_get_applications', 'sl_handle_get_applications');

function sl_handle_set_view(): void
{
    $data = json_decode(stripslashes($_POST['data']), true);
    $idApp = (int) $data['id'];
    $statusView = (int) $data['view'];

    $statusView = $statusView == 1;
    $setView = set_view($idApp,$statusView);
    if (!$setView) {
        wp_send_json_error(['message' => "Статус не змінено"]);
    } else {
        wp_send_json_success(['message' => "Статус змінено"]);
    }
}

add_action('wp_ajax_sl_set_view', 'sl_handle_set_view');
add_action('wp_ajax_nopriv_sl_set_view', 'sl_handle_set_view');
function sl_handle_remove_application(): void
{
    $data = json_decode(stripslashes($_POST['data']), true);
    $idApp = (int) $data['id'];

    $status = remove_application($idApp);
    if (!$status) {
        wp_send_json_error(['message' => "Заявку ${$idApp} не видалено"]);
    } else {
        wp_send_json_success(['message' => "Заявку ${$idApp} видалено!"]);
    }
}

add_action('wp_ajax_sl_remove_application', 'sl_handle_remove_application');
add_action('wp_ajax_nopriv_sl_remove_application', 'sl_handle_remove_application');