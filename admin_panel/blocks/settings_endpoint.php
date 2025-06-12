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
        <div class="endpoint_container">
            <h3>Створіть ендпоінт</h3>
        
            <!-- Назва ендпоінта -->
            <div class="ec_name">
                <label for="ec_end_name">Назва ендпоінта:</label>
                <input type="text" id="ec_end_name">
            </div>
        
            <!-- Вибір шляху -->
            <div class="ec_way">
                <div class="way_directory">
                    <label for="choose_way_directory">Основний шлях:</label>
                    <select id="choose_way_directory">
                        <option value="application/v1">application/v1</option>
                    </select>
                </div>
                <div class="way_end_directory">
                    <label for="input_way_end_directory">Кінцевий шлях:</label>
                    <input type="text" id="input_way_end_directory">
                </div>
            </div>
        
            <!-- Поля ендпоінта -->
            <div class="ec_label">
                <h4>Додайте поля:</h4>
                <div class="ec_label_create">
                    <label for="ec_label_name_label">Назва поля:</label>
                    <input type="text" id="ec_label_name_label">
        
                    <label for="choose_ec_label_create">Тип поля:</label>
                    <select id="choose_ec_label_create">
                        <option value="text">Текст</option>
                        <option value="file">Файл</option>
                    </select>
        
                    <label for="ec_label_mandatory_label">Обов’язкове поле:</label>
                    <input type="checkbox" id="ec_label_mandatory_label">
        
                    <input type="button" id="ec_label_add" value="Додати">
                </div>
        
                <!-- Перегляд доданих полів -->
                <div class="ec_label_preview">
                    <ul>
        
                    </ul>
                </div>
            </div>
        
            <!-- Тип ендпоінта -->
            <div class="ec_type">
                <label for="choose_ec_label_type">Тип ендпоінта:</label>
                <select id="choose_ec_label_type">
                    <option value="find_nanny">Няня</option>
                    <option value="test">Тестовий</option>
                </select>
            </div>
        
            <!-- Кнопка створення -->
            <input type="button" id="ec_endpoint_add" value="Створити ендпоінт">
        </div>
        <div class="endpoints_review">
            <h3>Список ендпоінтів</h3>
            <ul class="endpoint_list">
                
            </ul>       
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