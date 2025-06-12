<?php
function render_settings_endpoint()
{
     echo'

    <div class="mtab_content_item content_active" id="content_endpoint_data">
        <div class="short_description">
            <div class="show-visible">
                Приховати
            </div>
            <h3>
                Блок який описує ендпоінти
            </h3>
            <div class="short_description_content">
            <h4>Створення типу запису (ендпоінта)</h4>
            <p>
               У блоці потрібно створити тип запису(ендпоінта, заявки)<br>
               Слаг має містити лише (a-z)(A-z)(_-)
            </p>
            </div>
        
        </div>
        <div class="endpoint_created">
            <h3>Створіть ендпоінт</h3>
            <div class="ec_name">
                <label for="ec_end_name">Введіть назву ендпоінта</label>
                <input type="text" id="ec_end_name">
            </div>
            <div class="ec_way">
            
                <div class="way_directory">
                    <select  id="choose_way_directory">
                        <option value="application">application/v1</option>
                    </select>
                </div>
                <div class="way_end_directory">
                    <label for="input_way_end_directory">Введіть кінцевий шлях ендпоінта</label>
                    <input type="text" id="input_way_end_directory">
                </div>
            </div>
            <div class="ec_label">
                <div class="ec_label_create">
                    <label for="ec_label_name_label">Назва поля</label>
                    <input type="text" id="ec_label_name_label">
                    <select  id="choose_ec_label_create">
                        <option value="text">text</option>
                        <option value="file">file</option>
                    </select>
                    <label for="ec_label_mandatory_label">Обовязкове поле</label>
                    <input type="checkbox" id="ec_label_mandatory_label">
                    <input type="button" id="ec_label_add" value="додати">
                </div>
                <div class="ec_label_preview">
                    <ul>
                           
                    </ul>
                </div>
            </div>
            <div class="ec_type">
                <select  id="choose_ec_label_type">
                </select>
            </div>
        </div>
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

    ';
}