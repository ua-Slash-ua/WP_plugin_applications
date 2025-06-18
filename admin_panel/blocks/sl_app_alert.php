<?php

function display_sl_app_alert()
{
    echo '
    <div class="message_alert">
        <h3>Application повідомляє</h3>
        <div class="message_alert_message">
            <div class="msg-icon"></div>
            <p>- Тестове повідомлення</p>
        </div>
        <div class="alert-progress-bar"></div>
    </div>
    ';
}


function enqueue_sl_app_assets($hook)
{
    // Масив сторінок, для яких потрібно підключити стилі та скрипти
    $allowed_pages = [
        'toplevel_page_sl_app_main',
        'applications_page_sl_app_endpoint',
        'applications_page_sl_app_settings',
        'applications_page_sl_app_applications',
    ];

    // Перевіряємо, чи поточний `hook` є однією зі сторінок нашого меню
    if (in_array($hook, $allowed_pages)) {
        wp_enqueue_style('sl_app_alert-style', SL_APPLICATIONS_URL . '/assets/styles/alert_styles.css', [], '1.0.0');
        wp_enqueue_script('sl_app_alert-script', SL_APPLICATIONS_URL . '/assets/scripts/alert_scripts.js', ['jquery'], '1.0.0', true);
        // Підключаємо JS скрипт
        wp_enqueue_script(
            'sl_app_settings-ajax-script', // Унікальний ID для скрипту
            SL_APPLICATIONS_URL . '/assets/scripts/ajax.js', // Шлях до файлу скрипту
            ['jquery'], // Залежність від jQuery
            '1.0.0', // Версія скрипту
            true // Підключаємо скрипт внизу сторінки (після контенту)
        );
        $script_handles = [
            'sl_app_endpoint_types-script',
            'sl_app_endpoint_labels-script',
            'sl_app_alert-script',
            'sl_app_settings_security-script',
            'ap_applications-script',
        ]; // усі зареєстровані хендли твоїх скриптів

        foreach ($script_handles as $handle) {
            wp_localize_script('sl_app_settings-ajax-script', 'ajax_object', array(
                'ajaxurl' => site_url('/wp-admin/admin-ajax.php'),
            ));

        }

    }
}

add_action('admin_enqueue_scripts', 'enqueue_sl_app_assets');