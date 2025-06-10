<?php

add_action('wp_ajax_add_log_and_pass', 'add_log_and_pass');
add_action('wp_ajax_nopriv_add_log_and_pass', 'add_log_and_pass');
function add_log_and_pass()
{
    error_log('-0--');
    error_log(print_r($_POST['data'], true));
    // Повернути результат для JavaScript
    wp_send_json_success([
        'message' => 'Data logged successfully',
        'logged_data' => $_POST['data']
    ]);
}