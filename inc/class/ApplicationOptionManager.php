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

    public function editAppOption(string $key = null, string $old_data = null, string $new_data = null): bool
    {
        if (is_null($key) || is_null($old_data) || is_null($new_data)) {
            return false;
        }

        $table = $this->table;

        // Підготовка SQL запиту
        $sql = $this->db->prepare(
            "UPDATE `$table`
         SET options_value = %s
         WHERE options_key = %s AND options_value = %s",
            $new_data, $key, $old_data
        );

        // Виконання запиту
        $result = $this->db->query($sql);

        // Повертаємо true, якщо хоча б один рядок був змінений
        return $result !== false && $result > 0;
    }
    public function findAppOption($search, string $key = null)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'sl_app_options';

        // Нормалізуємо пошукові значення до масиву
        $searchValues = is_array($search) ? $search : [$search];

        // Отримуємо всі записи або лише з певним ключем
        $query = "SELECT * FROM {$table}";
        $params = [];

        if ($key !== null) {
            $query .= " WHERE options_key = %s";
            $params[] = $key;
        }

        $results = $wpdb->get_results($wpdb->prepare($query, ...$params));

        $matched = [];

        foreach ($results as $row) {
            $value = $row->options_value;

            // Можливо це JSON
            $decoded = json_decode($value, true);

            foreach ($searchValues as $term) {
                if (is_array($decoded)) {
                    // Пошук по масиву JSON
                    if ($this->deepSearchInArray($decoded, $term)) {
                        $matched[] = $row;
                        break;
                    }
                } else {
                    // Пошук у звичайному текстовому значенні
                    if (stripos($value, (string)$term) !== false) {
                        $matched[] = $row;
                        break;
                    }
                }
            }
        }

        return $matched;
    }
    private function deepSearchInArray(array $array, $needle): bool
    {
        foreach ($array as $value) {
            if (is_array($value)) {
                if ($this->deepSearchInArray($value, $needle)) {
                    return true;
                }
            } elseif (stripos((string)$value, (string)$needle) !== false) {
                return true;
            }
        }
        return false;
    }

}