<?php
add_action('wp_ajax_get_applications', function () {
    check_ajax_referer('applications_nonce', 'nonce');

    $filters = $_POST['filters'] ?? [];

    $manager = new \classes\ApplicationsManager();
    $results = $manager->get_all_applications($filters);

    // Сортуємо від нових до старих
    usort($results, function ($a, $b) {
        return strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });

    $page = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    $paged_results = array_slice($results, $offset, $per_page);

    wp_send_json_success([
        'items' => $paged_results,
        'total' => count($results),
        'page' => $page,
        'per_page' => $per_page
    ]);
});

add_action('wp_ajax_get_application', function () {
    check_ajax_referer('applications_nonce', 'nonce');

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if (!$id) wp_send_json_error('Не вказано ID');
    error_log('AJAX get_application, ID отриманий: ' . print_r($_POST['id'], true));

    $manager = new \classes\ApplicationsManager();

    // Отримаємо всі поля одразу (обхідним шляхом, бо немає get_by_id)
    $all = $manager->get_all_applications(['id' => $id]);

    if (empty($all)) {
        wp_send_json_error('Заявку не знайдено');
    }
    error_log(print_r($all[0], true));
    wp_send_json_success($all[0]);
});
add_action('wp_ajax_mark_application_viewed', function () {
    check_ajax_referer('applications_nonce', 'nonce');

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if (!$id) wp_send_json_error('Не вказано ID');

    $manager = new \classes\ApplicationsManager();
    $result = $manager->update_field($id, 'is_viewed', 1);

    if (!$result) {
        wp_send_json_error('Не вдалося оновити статус');
    }

    wp_send_json_success('Позначено як переглянуту');
});
add_action('wp_ajax_get_application_types', function () {
    check_ajax_referer('applications_nonce', 'nonce');

    $types = \classes\ApplicationsManager::get_all_application_types();

    // Мапінг типів у назви
    $labels = [
        'nanny_match' => 'Підбір няні',
        'training_application' => 'Навчання',
        'nanny_signup'=> 'Заявка від няні',
        'nanny_review'=> 'Відгук на вакансію',
    ];

    $result = [];
    foreach ($types as $type) {
        $result[$type] = $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    wp_send_json_success($result);
});
