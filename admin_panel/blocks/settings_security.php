<?php
function render_settings_security(){
    echo '
    <div class="mtab_content_item" id="content_security_data">
                        <div class="base_auth_container">
                            <div class="base_auth_add">
                                <div class="base_auth_context">
                                    <h4>
                                        Для чого додають BASE_AUTH у REST API:
                                    </h4>
                                    <p>
✅ Ідентифікація користувача – дозволяє API зрозуміти, хто робить запит.<br>
🔐 Захист доступу – дає можливість обмежити доступ лише авторизованим користувачам.<br>
⚙️ Простий механізм – легко реалізується без складних токенів чи сесій.
                                    </p>
                                    <h4>
                                        Формат login & password ?
                                    </h4>
                                    <p>
                                        LOGIN = приставка sl_l + 28 символів(великі, малі букви і цифри)<br>
                                        PASSWORD = приставка sl_p + 28 символів(великі, малі букви і цифри)
                                    </p>


                                </div>
                                <div class="base_auth_label">
                                    <label for="ba_add_login">Login</label>
                                    <input type="text" id="ba_add_login" placeholder="Auth Login">
                                    <label for="ba_add_password">Password</label>
                                    <input type="text" id="ba_add_password" placeholder="Auth Password">
                                    <div class="ba_add_action">
                                        <input type="button" id="ba_add_generate" value="Згенерувати">
                                        <input type="button" id="ba_add_add" value="Додати">
                                    </div>
                                </div>


                            </div>
                            <div class="base_auth_preview">
                                <h3>
                                    Перегляд усіх даних
                                </h3>
                                <ul></ul>
                            </div>
                        </div>
                    </div>
    ';
}