<?php
/**
 * Plugin Name: SL Applications
 * Description: Плагін для залишення заявок.
 * Version: 1.0
 * Author: ua-Slash-ua
 */


// Захист від прямого доступу
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
define("SL_APPLICATIONS_PATH", plugin_dir_path( __FILE__ ));
define("SL_APPLICATIONS_URL", plugin_dir_url( __FILE__ ));

include_once SL_APPLICATIONS_PATH . "fincludes.php";
function my_applications_activate() {
    create_table();
    error_log('My Applications Plugin активовано!');
}
register_activation_hook( __FILE__, 'my_applications_activate' );