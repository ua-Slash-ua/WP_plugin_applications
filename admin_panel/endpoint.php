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
                                <div class="choose_label_type_container">
                                    <label for="choose_label_type">Тип мітки:</label>
                                    <select id="choose_label_type">
                                        <option value="l_text">Текст</option>
                                        <option value="l_file">Файл</option>
                                    </select>
                                </div>

                                <div class="l_text_container">
                                    <label for="l_text_name">Введіть назву</label>
                                    <input type="text" id="l_text_name" placeholder="Назва текстової мітки">
                                    <label for="l_text_slug">Введіть слаг</label>
                                    <input type="text" id="l_text_slug" placeholder="slug-текст">
                                </div>

                                <div class="l_file_container" style="display: none">
                                    <label for="l_file_name">Введіть назву</label>
                                    <input type="text" id="l_file_name" placeholder="Назва файлу">
                                    <label for="l_file_slug">Введіть слаг</label>
                                    <input type="text" id="l_file_slug" placeholder="slug-файлу">

                                    <label for="choose_file_type">Тип файлу:</label>
                                    <select id="choose_file_type" multiple>
                                        <option value="l_file_extend_png">PNG</option>
                                        <option value="l_file_extend_jpg">JPG</option>
                                        <option value="l_file_extend_pdf">PDF</option>
                                        <option value="l_file_extend_docx">DOCX</option>
                                        <option value="l_file_extend_txt">TXT</option>
                                    </select>

                                    <label for="file_size_range">Максимальний розмір (до 10 МБ):</label>
                                    <input type="range" id="file_size_range" min="0" max="10" value="1" step="0.1" oninput="document.getElementById('file_size_value').textContent = this.value + ' МБ'">
                                    <span id="file_size_value">1 МБ</span>
                                </div>
                                <div class="label_add_action">
                                    <input type="button" value="Add" id="l_action_add">
                                </div>
                            </div>

                            <div class="preview_labels">
                                <ul class="labels_container">

                                </ul>
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
            ['sl_app_endpoint_labels-script','assets/scripts/ap_endpoint_labels.js', [], '1.0.0'],
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
add_action('admin_enqueue_scripts', 'enqueue_sl_app_endpoint_style_and_script');

