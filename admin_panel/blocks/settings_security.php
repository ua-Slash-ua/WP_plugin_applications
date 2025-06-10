<?php
function render_settings_security(){
    echo '
    <div class="mtab_content_item" id="content_security_data">
                        <div class="base_auth_container">
                            <div class="base_auth_add">
                                <div class="base_auth_context">
                                    <h4>
                                        Для чого потрібен BASE_AUTH ?
                                    </h4>
                                    <p>
                                        Бо так треба
                                    </p>
                                    <h4>
                                        Формат login & password ?
                                    </h4>
                                    <p>
                                        login = admin <br>
                                        password = 1234
                                    </p>


                                </div>
                                <div class="base_auth_label">
                                    <label for="ba_add_login">Login</label>
                                    <input type="text" id="ba_add_login" placeholder="Auth Login">
                                    <label for="ba_add_password">Password</label>
                                    <input type="text" id="ba_add_password" placeholder="Auth Password">
                                    <div class="ba_add_action">
                                        <input type="button" id="ba_add_generate" value="Generate">
                                        <input type="button" id="ba_add_add" value="Add">
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