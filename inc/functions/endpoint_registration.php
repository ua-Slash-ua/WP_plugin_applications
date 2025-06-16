<?php
add_action('rest_api_init', function () {
    $endpoints = sl_get_option('endpoints'); // Отримуємо список ендпоінтів з БД

    if (!is_array($endpoints)) {
        return;
    }
});

