<?php
function is_list(array $array): bool {
    return array_keys($array) === range(0, count($array) - 1);
}
function is_assoc(array $array): bool {
    return array_keys($array) !== range(0, count($array) - 1);
}


add_action('rest_api_init', function () {
    $endpoints = sl_get_option('endpoints'); // Отримуємо список ендпоінтів з БД

    if (!is_array($endpoints)) {
        return;
    }

    // Якщо лише один ендпоінт без вкладеного масиву
    if (is_assoc($endpoints)) {
        $endpoints = [$endpoints];
    }

    foreach ($endpoints as $endpoint) {
        $ed_name = $endpoint['name'];
        $ed_end_path = $endpoint['path_end'];
        $ed_method = strtoupper(str_replace('ep_add_method_', '', $endpoint['method']));
        $ed_type = $endpoint['type'][0]['slug'];
        $labels = $endpoint['labels'];

        register_rest_route('applications/v1', $ed_end_path, array(
            'methods'  => $ed_method, // Тут буде 'POST'
            'callback' => function (WP_REST_Request $request) use ($ed_name, $ed_type, $labels) {


                if (!is_array($labels)) {
                    return new WP_REST_Response(['error' => 'Невірний формат labels'], 400);
                }

                $body = $request->get_json_params(); // тіло POST-запиту
                $result_labels = [];

                foreach ($labels as $label) {
                    if (!isset($label['type']) || $label['type'] !== 'l_text') {
                        continue; // тільки тип l_text
                    }

                    $slug = $label['slug'] ?? null;
                    $mandate = $label['mandate'] ?? 'off';
                    $value = $body[$slug] ?? null;

                    if ($mandate === 'on' && (is_null($value) || $value === '')) {
                        return new WP_REST_Response([
                            'error' => "Поле '{$slug}' є обов’язковим, але не передано"
                        ], 422);
                    }

                    $result_labels[] = [$slug => $value];
                }
                add_application($ed_name, $ed_type, $result_labels);
                return new WP_REST_Response([
                    'message' => "Викликано $ed_name типу $ed_type",
                    'data' => $result_labels
                ]);
            },
            'permission_callback' => '__return_true',
        ));

    }
});


