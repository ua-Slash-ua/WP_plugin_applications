<?php
function register_sl_app_applications_submenu()
{
    add_submenu_page(
        'sl_app_main',           // Батьківський slug
        'Applications',                 // Назва сторінки
        'Applications',                 // Назва пункту в підменю
        'edit_posts',                     // Права доступу
        'sl_app_applications',           // Той самий slug!
        'render_sl_app_applications_page'     // Функція рендеру
    );
}

add_action('admin_menu', 'register_sl_app_applications_submenu');

function render_sl_app_applications_page()
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
            <div class="applications_container">
                <h2>Applications List</h2>
                <div class="filtered_container">
                    <div class="filtered_ed_type">
                        <select name="" id="filtered_ed_type" >
                            <option value="" disabled selected hidden>Виберіть тип заявки</option>
                        </select>
                    </div>
                    <div class="filtered_ed_label">
                        <select name="" id="filtered_ed_label">
                            <option value="" disabled selected hidden>Виберіть поле заявки</option>
                        </select>
                        <input type="text" id="filtered_ed_label_value" placeholder="Вкажіть значення поля">
                    </div>
                    <div class="filtered_action">
                        <input type="button" value="Filter" id="app_filter">
                    </div>
                </div>
                <div class="preview-container">

                </div>

            </div>

        </form>
    </div>

    <?php
}

function enqueue_sl_app_applications_style_and_script($hook)
{
    if ($hook === 'applications_page_sl_app_applications') {
        $endpoint_styles = array(
            ['sl_app_applications-style', 'assets/styles/ap_applications.css', [], '1.0.0'],
        );
        // CSS
        foreach ($endpoint_styles as $endpoint_style) {
            wp_enqueue_style(
                $endpoint_style[0],
                SL_APPLICATIONS_URL . $endpoint_style[1], // не додаємо ще один слеш!
                $endpoint_style[2],
                $endpoint_style[3]
            );
        }
        $endpoint_scripts = array(
            ['sl_app_applications-script', 'assets/scripts/ap_applications.js', [], '1.0.0'],
        );

        // JS
        foreach ($endpoint_scripts as $endpoint_script) {
            wp_enqueue_script(
                $endpoint_script[0],
                SL_APPLICATIONS_URL . $endpoint_script[1], // не додаємо ще один слеш!
                $endpoint_script[2],
                $endpoint_script[3]
            );
        }


    }
}

add_action('admin_enqueue_scripts', 'enqueue_sl_app_applications_style_and_script');