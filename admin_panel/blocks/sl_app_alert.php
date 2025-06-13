<?php

function display_sl_app_alert() {
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


function enqueue_sl_app_assets($hook) {
    // Масив сторінок, для яких потрібно підключити стилі та скрипти
    $allowed_pages = ['toplevel_page_sl_app_main', 'applications_page_sl_app_endpoint', 'applications_page_sl_app_settings'];

    // Перевіряємо, чи поточний `hook` є однією зі сторінок нашого меню
    if (in_array($hook, $allowed_pages)) {
        wp_enqueue_style('sl_app_alert-style', SL_APPLICATIONS_URL . '/assets/styles/alert_styles.css', [], '1.0.0');
        wp_enqueue_script('sl_app_alert-script', SL_APPLICATIONS_URL . '/assets/scripts/alert_scripts.js', ['jquery'], '1.0.0', true);
}
}
add_action('admin_enqueue_scripts', 'enqueue_sl_app_assets');