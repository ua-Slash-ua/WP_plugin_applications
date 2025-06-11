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
            <div class="ec_way">
            
            </div>
            <div class="ec_label">
            
            </div>
            <div class="ec_type">
            
            </div>
            <div class="ec_default_label">
            
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