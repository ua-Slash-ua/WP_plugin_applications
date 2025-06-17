<?php

namespace classes;

class ApplicationsManager

{
    private $table_name;
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'applications';
    }

    // 1. Створення таблиці
    public function create_table()
    {
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_name} (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    type VARCHAR(100) NOT NULL,
    is_viewed TINYINT(1) DEFAULT 0,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    email VARCHAR(100),
    location VARCHAR(255),
    format VARCHAR(100),
    birth_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) $charset_collate;";


        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    // 2. Створення заявки
    public function insert_application($data)
    {
        return $this->wpdb->insert($this->table_name, [
            'type' => $data['type'] ?? '',
            'is_viewed' => $data['is_viewed'] ?? 0,
            'full_name' => $data['full_name'] ?? '',
            'phone' => $data['phone'] ?? '',
            'email' => $data['email'] ?? '',
            'location' => $data['location'] ?? '',
            'format' => $data['format'] ?? '',
            'birth_date' => $data['birth_date'] ?? null,
        ]);
    }


    // 3. Отримання певного поля по id і назві поля
    public function get_field($id, $field)
    {
        $allowed = ['type', 'is_viewed', 'full_name', 'phone', 'email', 'location', 'format', 'birth_date', 'created_at'];
        if (!in_array($field, $allowed)) return null;

        return $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT $field FROM {$this->table_name} WHERE id = %d",
            $id
        ));
    }

    // 4. Заміна певного поля по id і назві поля
    public function update_field($id, $field, $value)
    {
        $allowed = ['type', 'is_viewed', 'full_name', 'phone', 'email', 'location', 'format', 'birth_date'];
        if (!in_array($field, $allowed)) return false;

        return $this->wpdb->update(
            $this->table_name,
            [$field => $value],
            ['id' => $id],
            null,
            ['%d']
        );
    }

    // 5. Отримання усіх записів з фільтром
    public function get_all_applications($filters = [])
    {
        $where = 'WHERE 1=1';
        $params = [];

        $exact_match_fields = ['id', 'type', 'is_viewed', 'birth_date'];
        $like_match_fields = ['full_name', 'phone', 'email', 'location', 'format'];

        foreach ($filters as $key => $value) {
            if (in_array($key, $exact_match_fields, true)) {
                $where .= " AND $key = %s";
                $params[] = $key === 'id' ? (int) $value : $value;
            } elseif (in_array($key, $like_match_fields, true)) {
                $where .= " AND $key LIKE %s";
                $params[] = '%' . $value . '%';
            }
        }

        $sql = "SELECT * FROM {$this->table_name} $where";

        $results = empty($params)
            ? $this->wpdb->get_results($sql, ARRAY_A)
            : $this->wpdb->get_results($this->wpdb->prepare($sql, ...$params), ARRAY_A);

        // 🔄 Обробляємо серіалізовані поля
        foreach ($results as &$row) {
            if (isset($row['format'])) {
                $row['format'] = maybe_unserialize($row['format']);
            }
        }

        return $results;
    }




    public static function create($data)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'applications';

        $result = $wpdb->insert($table, [
            'type' => $data['type'],
            'is_viewed' => (int)$data['is_viewed'],
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'location' => $data['location'] ?? '',
            'format' => $data['format'],
            'vacancy' => $data['vacancy'],
            'birth_date' => $data['birth_date'] ?? null,
        ]);

        if ($result === false) {
            return false;
        }

        return $wpdb->insert_id;
    }


    public static function get_all_application_types(): array {
        global $wpdb;
        $table = $wpdb->prefix . 'applications';

        $results = $wpdb->get_col("SELECT DISTINCT type FROM {$table} WHERE type IS NOT NULL AND type != ''");

        if (!is_array($results)) {
            return [];
        }

        return $results;
    }




}

