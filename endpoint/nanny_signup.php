<?php
add_action('rest_api_init', function () {
    register_rest_route('applications/v1', '/nanny_signup', [
        'methods' => 'POST',
        'callback' => 'applications_handle_nanny_signup',
        'permission_callback' => 'check_basic_auth_permission_plugin',
    ]);
});

function applications_handle_nanny_signup(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    $required_fields = ['full_name', 'country', 'birth_date', 'phone', 'email', 'experience', 'format'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            return new WP_Error('missing_field', "Поле '$field' обов'язкове", ['status' => 400]);
        }
    }

    if (!is_email($data['email'])) {
        return new WP_Error('invalid_email', 'Невірний формат email', ['status' => 400]);
    }

    // Збереження заявки
    $saved = \classes\ApplicationsManager::create([
        'type' => 'nanny_signup',
        'is_viewed' => 0,
        'full_name' => sanitize_text_field($data['full_name']),
        'location' => sanitize_text_field($data['country']),
        'birth_date' => sanitize_text_field($data['birth_date']),
        'phone' => sanitize_text_field($data['phone']),
        'email' => sanitize_email($data['email']),
        'experience' => sanitize_textarea_field($data['experience']),
        'format' => maybe_serialize($data['format']),
    ]);

    if ($saved === false) {
        return new WP_Error('db_error', 'Помилка при збереженні заявки', ['status' => 500]);
    }

    // Відправка email
    $saved_emails_raw = get_option('applications_notification_emails', '');
    $saved_emails = array_filter(array_map('trim', explode(',', $saved_emails_raw)));
    $formats = is_array($data['format']) ? $data['format'] : maybe_unserialize($data['format']);
    $format_str = is_array($formats) ? implode(', ', $formats) : '';
    

    if (!empty($saved_emails)) {
        $subject_admin = 'Нова заявка: Хочу стати нянею';
        $message_admin = "Надійшла нова заявка від потенційної няні:\n\n" .
            "ПІБ: {$data['full_name']}\n" .
            "Країна: {$data['country']}\n" .
            "Дата народження: {$data['birth_date']}\n" .
            "Телефон: {$data['phone']}\n" .
            "Email: {$data['email']}\n" .
            "Досвід: {$data['experience']}\n" .
            "Формат зайнятості: {$format_str}\n\n" .
            "ID заявки: $saved";

        foreach ($saved_emails as $admin_email) {
            wp_mail($admin_email, $subject_admin, $message_admin);
        }
    }

    // Лист подяки
    $subject_client = 'Дякуємо за вашу заявку';
    $message_client = "Доброго дня, {$data['full_name']}!\n\n" .
        "Дякуємо, що зацікавились нашою агенцією. Ми розглянемо вашу заявку та зв'яжемося з вами найближчим часом.\n\n" .
        "З повагою,\nКоманда";

    wp_mail($data['email'], $subject_client, $message_client);

    return new WP_REST_Response([
        'message' => 'Заявку успішно створено',
        'id' => $saved,
    ], 201);
}
