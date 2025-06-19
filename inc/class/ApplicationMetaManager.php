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

    /**
     * Отримати всі записи з таблиці sl_application_meta за переданими параметрами
     *
     * @param int|null $main_id
     * @param string|null $meta_key
     * @param string|null $meta_value
     * @return array
     */
    public function getAll(?int $main_id = null, ?string $meta_key = null, ?string $meta_value = null): array
    {
        global $wpdb;

        $where = '1=1';
        $params = [];

        if ($main_id !== null) {
            $where .= ' AND main_id = %d';
            $params[] = $main_id;
        }

        if ($meta_key !== null) {
            $where .= ' AND meta_key = %s';
            $params[] = $meta_key;
        }

        if ($meta_value !== null) {
            $where .= ' AND meta_value = %s';
            $params[] = $meta_value;
        }

        $query = $wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE $where",
            ...$params
        );

        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * Видаляє всі мета-записи для переданого main_id
     *
     * @param int $main_id ID основної заявки
     * @return bool true у разі успіху, false — якщо помилка
     */
    public function deleteByMainId(int $main_id): bool
    {
        global $wpdb;

        $result = $wpdb->delete(
            $this->table,
            [ 'main_id' => $main_id ],
            [ '%d' ]
        );

        return $result !== false;
    }

}
