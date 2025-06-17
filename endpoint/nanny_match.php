<?php
add_action('rest_api_init', function () {
    register_rest_route('applications/v1', '/nanny_match', [
        'methods' => 'POST',
        'callback' => 'applications_handle_create_request',
        'permission_callback' => 'check_basic_auth_permission_plugin',
    ]);
});

function applications_handle_create_request(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    $required_fields = ['full_name', 'phone', 'email', 'location', 'format'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            return new WP_Error('missing_field', "Поле '$field' обов'язкове", ['status' => 400]);
        }
    }

    if (!is_email($data['email'])) {
        return new WP_Error('invalid_email', 'Невірний формат email', ['status' => 400]);
    }

    if (!is_array($data['format'])) {
        return new WP_Error('invalid_format', 'Поле format повинно бути списком', ['status' => 400]);
    }

    // Збереження заявки
    $saved = \classes\ApplicationsManager::create([
        'type' => 'nanny_match',
        'is_viewed' => 0,
        'full_name' => sanitize_text_field($data['full_name']),
        'phone' => sanitize_text_field($data['phone']),
        'email' => sanitize_email($data['email']),
        'location' => sanitize_text_field($data['location']),
        'format' => maybe_serialize($data['format']),
    ]);

    if ($saved === false) {
        return new WP_Error('db_error', 'Помилка при збереженні заявки', ['status' => 500]);
    }

    // --- Відправка email ---

    // 1) Пошта адміністраторів
    $saved_emails_raw = get_option('applications_notification_emails', '');
    $saved_emails = array_filter(array_map('trim', explode(',', $saved_emails_raw)));

    if (!empty($saved_emails)) {
        $subject_admin = 'Нова заявка: Підбір няні';
        $message_admin = "Надійшла нова заявка з такими даними:\n\n" .
            "ПІБ: {$data['full_name']}\n" .
            "Телефон: {$data['phone']}\n" .
            "Email: {$data['email']}\n" .
            "Місто/Країна: {$data['location']}\n" .
            "Формат зайнятості: " . implode(', ', $data['format']) . "\n\n" .
            "ID заявки: $saved";

        foreach ($saved_emails as $admin_email) {
            wp_mail($admin_email, $subject_admin, $message_admin);
        }
    }

    // 2) Лист подяки клієнту
    $subject_client = 'Дякуємо за вашу заявку';
    $message_client = "Доброго дня, {$data['full_name']}!\n\n" .
        "Дякуємо за звернення. Наша команда найближчим часом зв'яжеться з вами для уточнення деталей.\n\n" .
        "Якщо у вас є додаткові питання, будь ласка, відповідайте на цей лист.\n\n" .
        "З повагою,\nКоманда";

    wp_mail($data['email'], $subject_client, $message_client);

    // --- Кінець відправки email ---

    return new WP_REST_Response([
        'message' => 'Заявку успішно створено',
        'id' => $saved,
    ], 201);
}

