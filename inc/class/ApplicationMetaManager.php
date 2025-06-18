<?php

namespace sl_app;

class ApplicationMetaManager
{
    private $table;

    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix . 'sl_application_meta';
    }

    /**
     * Додає запис у таблицю wp_sl_application_meta
     *
     * @param int $main_id
     * @param string $meta_key
     * @param string $meta_value
     * @return int|false ID запису або false у разі помилки
     */
    public function add(int $main_id, string $meta_key, string $meta_value): bool|int
    {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table,
            [
                'main_id'     => $main_id,
                'meta_key'    => $meta_key,
                'meta_value'  => $meta_value,
            ],
            [
                '%d', // main_id
                '%s', // meta_key
                '%s', // meta_value
            ]
        );

        if ($result === false) {
            return false;
        }

        return $wpdb->insert_id;
    }
}
