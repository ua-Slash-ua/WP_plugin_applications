<?php

function register_sl_app_settings_submenu()
{
    add_submenu_page(
        'sl_app_main',           // Батьківський slug
        'Settings',                 // Назва сторінки
        'Settings',                 // Назва пункту в підменю
        'edit_posts',                     // Права доступу
        'sl_app_settings',           // Той самий slug!
        'render_sl_app_settings_page'     // Функція рендеру
    );
}

add_action('admin_menu', 'register_sl_app_settings_submenu');

function render_sl_app_settings_page()
{
    display_sl_app_alert();
    ?>
    <div class="wrap">
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php
            settings_fields('sl_app_settings_group'); // Виводимо nonce та інші безпечні дані для групи налаштувань
            do_settings_sections('sl_app_settings_slug'); // Виводимо секції та поля налаштувань
            //++
            ?>
            <div class="mtab_hero">
                <ul class="mtab_header">
                    <li class="mtab_header_item tab_active" id="main_data">Основні</li>
                    <li class="mtab_header_item " id="security_data">Безпека</li>
                </ul>
                <div class="mtab_content">
                    <div class="mtab_content_item content_active" id="content_main_data">
                        Основні налаштування
                    </div>
                    <?php
                     render_settings_security();

                    ?>
                </div>
            </div>

        </form>
    </div>

    <?php
}

function enqueue_sl_app_settings_style_and_script($hook)
{
    // Перевіряємо, чи це сторінка кастомного меню "sl_app_settings_slug"
    if ($hook === 'applications_page_sl_app_settings') {
        // Підключаємо CSS стилі
        wp_enqueue_style(
            'sl_app_settings-style', // Унікальний ID для стилю
            SL_APPLICATIONS_URL . '/assets/styles/ap_settings_security.css', // Шлях до файлу стилю
            [], // Залежності
            '1.0.0' // Версія стилю
        );

        // Підключаємо JS скрипт
        wp_enqueue_script(
            'sl_app_settings_security-script', // Унікальний ID для скрипту
            SL_APPLICATIONS_URL . '/assets/scripts/ap_settings_security.js', // Шлях до файлу скрипту
            ['jquery'], // Залежність від jQuery
            '1.0.0', // Версія скрипту
            true // Підключаємо скрипт внизу сторінки (після контенту)
        );


    }
}

add_action('admin_enqueue_scripts', 'enqueue_sl_app_settings_style_and_script');