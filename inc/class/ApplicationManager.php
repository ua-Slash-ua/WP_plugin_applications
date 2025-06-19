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
    public function get_all() {
        global $wpdb;

        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";

        return $wpdb->get_results($query);
    }
    public function setViewedStatus(int $applicationId, bool $viewed): bool
    {
        error_log('$applicationId = '.$applicationId);
        error_log('$viewed = '.$viewed);
        global $wpdb;

        $result = $wpdb->update(
            $this->table,
            ['viewed' => $viewed ? 1 : 0], // значення, яке оновлюємо
            ['id' => $applicationId],      // де саме
            ['%d'],                        // формат значення
            ['%d']                         // формат умови
        );

        return $result !== false;
    }
    /**
     * Видаляє заявку з таблиці wp_sl_application за ID
     *
     * @param int $id ID заявки
     * @return bool true у разі успіху, false — якщо помилка
     */
    public function delete(int $id): bool
    {
        global $wpdb;

        $result = $wpdb->delete(
            $this->table,
            [ 'id' => $id ],
            [ '%d' ]
        );

        return $result !== false;
    }

}