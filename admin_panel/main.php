
<?php
function register_sl_app_main_menu()
{
    add_menu_page(
        'Applications',          // Назва вкладки
        'Applications',          // Назва меню
        'edit_posts',            // Права доступу (без manage_options)
        'sl_app_main',     // Унікальний ідентифікатор
        'render_sl_app_main_page', // Функція, яка відображатиме контент
        'dashicons-email',       // Іконка
        6                         // Позиція у меню
    );
}

function register_sl_app_main_submenu()
{
    add_submenu_page(
        'sl_app_main',           // Батьківський slug
        'Налаштування теми',                 // Назва сторінки
        'Налаштування теми',                 // Назва пункту в підменю
        'edit_posts',                     // Права доступу
        'sl_app_main',           // Той самий slug!
        'render_sl_app_main_page'     // Функція рендеру
    );
}

add_action('admin_menu', 'register_sl_app_main_menu');
add_action('admin_menu', 'register_sl_app_main_submenu');



// Реєстрація групи налаштувань
function register_settings_sl_app_main_group()
{

    $meta_fields = [
        'save_data_text'
    ];
    foreach ($meta_fields as $key) {
        register_setting(
            'sl_app_main_group',  // Унікальна назва групи налаштувань
            $key   // Опція, яку ми будемо зберігати в цій групі
        );
    }
}

add_action('admin_init', 'register_settings_sl_app_main_group');

// Виведення сторінки налаштувань
function render_sl_app_main_page()
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


function enqueue_sl_app_main_style_and_script($hook)
{
    // Перевіряємо, чи це сторінка кастомного меню "sl_app_main_slug"
    if ($hook === 'toplevel_page_sl_app_main') {
        // Підключаємо CSS стилі
        wp_enqueue_style(
            'sl_app_main-style', // Унікальний ID для стилю
            get_template_directory_uri() . '/inc/admin_panel/ap_styles/ap_sl_app_main_styles.css', // Шлях до файлу стилю
            [], // Залежності
            '1.0.0' // Версія стилю
        );

        // Підключаємо JS скрипт
        wp_enqueue_script(
            'sl_app_main-script', // Унікальний ID для скрипту
            get_template_directory_uri() . '/inc/admin_panel/ap_scripts/ap_sl_app_main_scripts.js', // Шлях до файлу скрипту
            ['jquery'], // Залежність від jQuery
            '1.0.0', // Версія скрипту
            true // Підключаємо скрипт внизу сторінки (після контенту)
        );
    }
}

add_action('admin_enqueue_scripts', 'enqueue_sl_app_main_style_and_script');



