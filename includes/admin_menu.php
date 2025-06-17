<?php

function applications_plugin_admin_page_menu()
{
    add_menu_page(
        'Заявки',
        'Заяви',
        'manage_options',
        'applications_admin_page',
        'applications_plugin_admin_page',
        'dashicons-list-view',
        26
    );


}

add_action('admin_menu', 'applications_plugin_admin_page_menu');

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_applications_admin_page') return;

    wp_enqueue_style(
        'applications-admin-style',
        PLUGIN_MAIN_PATH . 'assets/css/admin-style.css'
    );

    wp_enqueue_script(
        'applications-admin-script', // Ім'я має бути тут таке саме
        PLUGIN_MAIN_PATH . 'assets/js/admin-script.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('applications-admin-script', 'applicationsData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('applications_nonce')
    ]);
});

function applications_plugin_admin_page()
{
    // Отримуємо типи заявок із бази через клас
    $types = \classes\ApplicationsManager::get_all_application_types();

    // Мапимо в назви
    $type_labels = [
        'nanny_match' => 'Підбір няні',
        'training_application' => 'Навчання',
        'nanny_signup'=> 'Заявка від няні',
        'nanny_review'=> 'Відгук на вакансію',
    ];

    // Отримати збережені email, наприклад з опцій
    $saved_emails = get_option('applications_notification_emails', '');

    ?>
    <div class="wrap">
        <h1>Заявки</h1>

        <div id="email-settings-block">
            <form method="post" action="options.php">
                <?php
                settings_fields('applications_settings_group'); // назва групи налаштувань
                do_settings_sections('applications_settings_page');
                ?>
                <h2>Налаштування email-сповіщень</h2>
                <label for="applications_notification_emails">Email-адреси для сповіщень (через кому)</label>
                <textarea id="applications_notification_emails" name="applications_notification_emails"
                          placeholder="example1@mail.com, example2@mail.com"
                          style="width:100%; height:80px;"><?php echo esc_textarea($saved_emails); ?></textarea>
                <p class="description">Введіть email-адреси, на які будуть надходити сповіщення про нові заявки.</p>
                <?php submit_button('Зберегти налаштування'); ?>
            </form>
        </div>
        <form id="applications-filter-form">
            <div class="filters">
                <input type="text" name="full_name" placeholder="ПІБ">
                <input type="text" name="phone" placeholder="Телефон">
                <input type="email" name="email" placeholder="Email">
                <input type="text" name="location" placeholder="Місто, країна">
                <select name="type">
                    <option value="">Тип заявки</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?php echo esc_attr($type); ?>">
                            <?php echo esc_html($type_labels[$type] ?? ucfirst(str_replace('_', ' ', $type))); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="is_viewed">
                    <option value="">Статус</option>
                    <option value="1">Переглянута</option>
                    <option value="0">Не переглянута</option>
                </select>
                <button type="submit" class="button button-primary">Застосувати</button>
                <button type="button" id="reset-filters" class="button">Скинути</button>
            </div>
        </form>


        <div id="applications-list" style="margin-top: 30px;">
            <!-- Тут будуть заявки -->
        </div>

        <div id="application-modal" class="hidden">
            <div class="modal-content">
                <button id="close-modal" class="close-button">&times;</button>
                <div id="application-details">
                    <!-- Деталі заявки -->
                </div>
                <button id="mark-viewed" class="button">Позначити як переглянуту</button>
            </div>
        </div>

    </div>
    <?php
}


// 1. Регіструємо налаштування
add_action('admin_init', function () {
    register_setting(
        'applications_settings_group', // назва групи налаштувань
        'applications_notification_emails', // ім'я опції у БД
        [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ]
    );

});

