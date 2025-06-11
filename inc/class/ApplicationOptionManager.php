<?php

namespace sl_app;

use wpdb;

class ApplicationOptionManager
{
    protected wpdb $db;
    protected string $table;
    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;
        $this->table = $wpdb->prefix . 'sl_application_options';
    }

    public function createAppOption($key, $value, $parent_id): int|false
    {
        // Якщо значення масив або об'єкт — конвертуємо в JSON
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        $data = [
            'options_key'   => $key,
            'options_value' => $value,
            'parent_id'     => $parent_id ?? 0,
        ];

        $result = $this->db->insert($this->table, $data);

        return $result ? (int) $this->db->insert_id : false;
    }

    public function getAppOption(string $key, int $id = null, int $parent_id = null): string|array|null
    {
        $where = ['options_key' => $key];

        if (!is_null($id)) {
            $where['id'] = $id;
        }

        if (!is_null($parent_id)) {
            $where['parent_id'] = $parent_id;
        }

        $conditions = [];
        $values = [];

        foreach ($where as $column => $val) {
            $conditions[] = "`$column` = " . (is_int($val) ? '%d' : '%s');
            $values[] = $val;
        }

        $sql = "SELECT options_value FROM {$this->table} WHERE " . implode(' AND ', $conditions);
        $prepared = $this->db->prepare($sql, $values);

        $results = $this->db->get_col($prepared);


        if (empty($results)) {
            return null;
        }

        return count($results) === 1 ? $results[0] : $results;
    }

    public function removeAppOption(string $key = null, int $id = null, string $value = null, string $parent_id = null): int
    {
        $where = [];
        $values = [];

        if (!is_null($key) && $key !== '') {
            $where[] = "`options_key` = %s";
            $values[] = $key;
        }
        if (!is_null($id)) {
            $where[] = "`id` = %d";
            $values[] = $id;
        }
        if (!is_null($value) && $value !== '') {
            $where[] = "`options_value` = %s";
            $values[] = $value;
        }
        if (!is_null($parent_id) && $parent_id !== '') {
            $where[] = "`parent_id` = %s";
            $values[] = $parent_id;
        }

        if (empty($where)) {
            // Нема параметрів — не видаляємо нічого, або можна видалити все (залежить від логіки)
            return 0;
        }

        $sql = "DELETE FROM {$this->table} WHERE " . implode(' AND ', $where);
        $prepared = $this->db->prepare($sql, $values);

        return $this->db->query($prepared);
    }


}