<?php
add_action('rest_api_init', function () {
    $endpoints = sl_get_option('endpoint'); // Отримуємо список ендпоінтів з БД

    if (!is_array($endpoints)) {
        return;
    }

    foreach ($endpoints as $endpoint) {
        if (!isset($endpoint['base_path'], $endpoint['end_path'], $endpoint['type'])) {
            continue; // Пропускаємо некоректні
        }

        $route = '/' . trim($endpoint['base_path'], '/') . '/' . trim($endpoint['end_path'], '/');

        register_rest_route($endpoint['base_path'], '/'.$endpoint['end_path'], [
            'methods' => 'POST', // або GET, залежно від потреб
            'callback' => function (WP_REST_Request $request) use ($endpoint) {
                $data = $request->get_json_params();
                $errors = [];
                $validated = [];

                if (!isset($endpoint['labels']) || !is_array($endpoint['labels'])) {
                    return new WP_REST_Response(['error' => 'Некоректна структура labels'], 400);
                }

                foreach ($endpoint['labels'] as $label) {
                    $field_name = $label['name'] ?? null;
                    $is_required = ($label['isMandat'] ?? '') === 'on';
                    $type = $label['type'] ?? 'text';

                    if (!$field_name) {
                        continue; // пропускаємо некоректні
                    }

                    $value = $data[$field_name] ?? null;

                    if ($is_required && (is_null($value) || $value === '')) {
                        $errors[] = "Поле '{$field_name}' є обов’язковим і відсутнє або порожнє.";
                        continue;
                    }

                    // Проста перевірка для text — вважаємо, що string або масив строк
                    if ($type === 'text' && !is_null($value)) {
                        if (!is_string($value) && !is_array($value)) {
                            $errors[] = "Поле '{$field_name}' повинно бути рядком або масивом.";
                            continue;
                        }
                    }

                    // Зберігаємо успішно пройдені поля
                    $validated[$field_name] = $value;
                }

                if (!empty($errors)) {
                    return new WP_REST_Response([
                        'message' => 'Валідація не пройдена',
                        'errors' => $errors
                    ], 422);
                }

                // Логування отриманих і валідованих даних
                error_log("== 🟢 Отримано дані з ендпоінта {$endpoint['end_path']} ==");
                error_log(print_r($validated, true));

                return rest_ensure_response([
                    'message' => 'Ендпоінт викликано успішно!',
                    'received' => $validated,
                    'endpoint_info' => $endpoint,
                ]);
            },

            'permission_callback' => '__return_true', // У проді замінити на реальну перевірку!
        ]);
    }
});

