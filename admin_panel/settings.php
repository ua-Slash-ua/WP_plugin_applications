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
    ?>
    <div class="wrap">
        <h1>Налаштування теми</h1>
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php
            settings_fields('sl_app_main_group'); // Виводимо nonce та інші безпечні дані для групи налаштувань
            do_settings_sections('sl_app_main_slug'); // Виводимо секції та поля налаштувань
            //++
            ?>
            <div class="mtab_hero">
                <ul class="mtab_header">
                    <li class="mtab_header_item tab_active" id="main">Головна</li>
                    <li class="mtab_header_item" id="settings">Налаштування</li>
                </ul>
                <div class="mtab_content">
                    <div class="mtab_content_item content_active" id="content_main">

                    </div>
                    <div class="mtab_content_item" id="content_settings">

                    </div>
                </div>
            </div>


            <input type="submit" value="Save Changes" class="button button-primary">
        </form>
    </div>
    <?php
}