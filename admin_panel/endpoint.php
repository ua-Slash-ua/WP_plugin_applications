<?php
function register_sl_app_endpoint_submenu()
{
    add_submenu_page(
        'sl_app_main',           // Батьківський slug
        'Endpoint',                 // Назва сторінки
        'Endpoint',                 // Назва пункту в підменю
        'edit_posts',                     // Права доступу
        'sl_app_endpoint',           // Той самий slug!
        'render_sl_app_endpoint_page'     // Функція рендеру
    );
}

add_action('admin_menu', 'register_sl_app_endpoint_submenu');

function render_sl_app_endpoint_page()
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
                    <li class="mtab_header_item" id="endpoint_main">Ендпоінти</li>
                    <li class="mtab_header_item tab_active" id="endpoint_label">Поля</li>
                    <li class="mtab_header_item " id="endpoint_type">Типи</li>
                </ul>
                <div class="mtab_content">
                    <div class="mtab_content_item" id="content_endpoint_main">
                        111111111111
                    </div>
                    <div class="mtab_content_item content_active" id="content_endpoint_label">
                        <div class="ed_process_label">
                            <div class="add_labels">

                            </div>
                            <div class="preview_labels">

                            </div>
                        </div>
                    </div>
                    <div class="mtab_content_item" id="content_endpoint_type">

                        <div class="ed_process_type">
                            <div class="add_types">
                                <label for="ed_at_add_name">Назва *</label>
                                <input type="text" id="ed_at_add_name">
                                <label for="ed_at_add_slug">Слаг *</label>
                                <input type="text" id="ed_at_add_slug">
                                <input type="button" id="ed_at_add_type" value="Додати">
                            </div>
                            <div class="preview_types">
                                <ul class="preview_types_list">

                                </ul>
                            </div>
                        </div>

                        <div class="pop-up-edit-type">
                            <label for="ed_at_edit_name">Назва *</label>
                            <input type="text" id="ed_at_edit_name">
                            <label for="ed_at_edit_slug">Слаг *</label>
                            <input type="text" id="ed_at_edit_slug">
                            <input type="button" id="ed_at_edit_type" value="Змінити">
                        </div>

                    </div>
                </div>


            </div>

        </form>
    </div>

    <?php
}

function enqueue_sl_app_endpoint_style_and_script($hook)
{
    if ($hook === 'applications_page_sl_app_endpoint') {
        $endpoint_styles = array(
            ['sl_app_endpoint_types-style','assets/styles/ap_endpoint_types.css', [], '1.0.0'],
            ['sl_app_endpoint_labels-style','assets/styles/ap_endpoint_labels.css', [], '1.0.0'],
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
            ['sl_app_endpoint_types-script','assets/scripts/ap_endpoint_types.js', [], '1.0.0'],
            ['sl_app_endpoint_labels-style','assets/styles/ap_endpoint_labels.css', [], '1.0.0'],
            ['sl_app_settings-ajax-script','assets/scripts/ajax.js', ['jquery'], '1.0.0'],
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


        wp_localize_script('sl_app_settings-ajax-script', 'ajax_object', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));
    }
}
add_action('admin_enqueue_scripts', 'enqueue_sl_app_endpoint_style_and_script');

