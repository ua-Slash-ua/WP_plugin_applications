<?php

/*
Plugin Name: Applications Manager
Description: Плагін для збереження і перегляду заявок в адмінці.
Version: 1.0
Author: Slash
*/

use classes\ApplicationsManager;

if (!defined('ABSPATH')) {
    exit; // Запобігаємо прямому доступу
}
define('PLUGIN_MAIN_PATH',plugin_dir_url(__FILE__) );
// Підключення файлів
require_once plugin_dir_path(__FILE__) . 'includes/custom_func.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-ajax.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin_menu.php';
require_once plugin_dir_path(__FILE__) . 'classes/ApplicationsManager.php';
require_once plugin_dir_path(__FILE__) . 'endpoint/nanny_match.php';
require_once plugin_dir_path(__FILE__) . 'endpoint/training_application.php';
require_once plugin_dir_path(__FILE__) . 'endpoint/nanny_signup.php';
require_once plugin_dir_path(__FILE__) . 'endpoint/review_nanny.php';

// Створення таблиці при активації
register_activation_hook(__FILE__, 'applications_plugin_install');

function applications_plugin_install()
{

    $appManager = new ApplicationsManager();
    $appManager->create_table();
}
