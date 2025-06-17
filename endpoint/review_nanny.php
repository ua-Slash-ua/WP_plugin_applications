<?php
add_action('rest_api_init', function () {
    register_rest_route('applications/v1', '/nanny_review', [
        'methods' => 'POST',
        'callback' => 'applications_handle_nanny_review_request',
        'permission_callback' => 'check_basic_auth_permission_plugin',
    ]);
});

function applications_handle_nanny_review_request(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    $required_fields = ['full_name', 'phone', 'email'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            return new WP_Error('missing_field', "Поле '$field' обов'язкове", ['status' => 400]);
        }
    }

    if (!is_email($data['email'])) {
        return new WP_Error('invalid_email', 'Невірний формат email', ['status' => 400]);
    }

    // Отримуємо тип тарифу, якщо переданий
    $tariff_type = isset($data['tariff_type']) ? sanitize_text_field($data['tariff_type']) : '';
    $vacancy_name = isset($data['vacancy_name']) ? sanitize_text_field($data['vacancy_name']) : '';

    // Зберігаємо заявку
    $saved = \classes\ApplicationsManager::create([
        'type' => 'nanny_review',
        'is_viewed' => 0,
        'full_name' => sanitize_text_field($data['full_name']),
        'phone' => sanitize_text_field($data['phone']),
        'email' => sanitize_email($data['email']),
        'location' => '',
        'format' => $tariff_type, // Зберігаємо як "формат", якщо немає окремого поля,
        'vacancy' => $vacancy_name, // Зберігаємо як "формат", якщо немає окремого поля,
    ]);

    if ($saved === false) {
        return new WP_Error('db_error', 'Помилка при збереженні заявки', ['status' => 500]);
    }

    // Повідомлення адміністратору
    $saved_emails_raw = get_option('applications_notification_emails', '');
    $saved_emails = array_filter(array_map('trim', explode(',', $saved_emails_raw)));

    if (!empty($saved_emails)) {
        $subject_admin = 'Нова заявка: Відгук на вакансію';
        $message_admin = "Надійшла нова заявка на Відгук на вакансію з такими даними:\n\n" .
            "ПІБ: {$data['full_name']}\n" .
            "Телефон: {$data['phone']}\n" .
            "Email: {$data['email']}\n".
            "Vacancy: {$data['vacancy_name']}\n";

        if ($tariff_type !== '') {
            $message_admin .= "Тип тарифу: $tariff_type\n";
        }

        $message_admin .= "\nID заявки: $saved";

        foreach ($saved_emails as $admin_email) {
            wp_mail($admin_email, $subject_admin, $message_admin);
        }
    }

    // Повідомлення клієнту
    $subject_client = 'Дякуємо за відгук на вакансію';
    $message_client = "Доброго дня, {$data['full_name']}!\n\n" .
        "Дякуємо за звернення. Наш менеджер скоро зв'яжеться з вами для уточнення деталей.\n\n" .
        "Якщо у вас є додаткові питання, будь ласка, відповідайте на цей лист.\n\n" .
        "З повагою,\nКоманда";

    wp_mail($data['email'], $subject_client, $message_client);

    return new WP_REST_Response([
        'message' => 'Заявку на навчання успішно створено',
        'id' => $saved,
    ], 201);
}