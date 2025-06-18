<?php

namespace sl_app;

class ApplicationManager {

    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'sl_application';
    }

    /**
     * Додає нову заявку
     *
     * @param string $name
     * @param string $type
     * @return int|false ID нового запису або false у разі помилки
     */
    public function add($name, $type) {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table,
            [
                'name'       => $name,
                'type'       => $type,
                'created_at' => current_time('mysql')
            ],
            [ '%s', '%s', '%s' ]
        );

        if ($result === false) {
            return false;
        }

        return $wpdb->insert_id;
    }
}