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
    public function findAppOption($value, ?string $key = null, array $jsonKeys = null): bool|array
    {
        global $wpdb;
        $table = $this->table;
//        error_log("LOG TEST: AJAX function called");


        $values = is_array($value) ? $value : [$value];
//        error_log("findAppOption called with values: " . print_r($values, true) . ", key: " . var_export($key, true) . ", jsonKeys: " . print_r($jsonKeys, true));

        $sql = "SELECT * FROM {$table}";
        $where = [];

        if ($key !== null) {
            $where[] = $wpdb->prepare("options_key = %s", $key);
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

//        error_log("SQL Query: " . $sql);

        $results = $wpdb->get_results($sql);
//        error_log("Number of rows fetched: " . count($results));

        $matched = [];

        foreach ($results as $index => $row) {
//            error_log("Processing row #{$index}: options_value = " . $row->options_value);

            $decoded = json_decode($row->options_value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
//                error_log("JSON decoded successfully: " . print_r($decoded, true));

                $searchArea = [];

                if ($jsonKeys !== null && is_array($jsonKeys)) {
                    foreach ($jsonKeys as $k) {
                        if (array_key_exists($k, $decoded)) {
                            $searchArea[] = $decoded[$k];
//                            error_log("Adding to searchArea from key '{$k}': " . print_r($decoded[$k], true));
                        }
                    }
                } else {
                    $searchArea = array_values($decoded);
//                    error_log("Using all values from decoded JSON as searchArea: " . print_r($searchArea, true));
                }

                foreach ($values as $val) {
                    $valNorm = ltrim(strtolower($val), '/');
//                    error_log("Normalized search value: '{$val}' -> '{$valNorm}'");

                    foreach ($searchArea as $searchItem) {
                        if (is_string($searchItem)) {
                            $searchNorm = ltrim(strtolower($searchItem), '/');
//                            error_log("Comparing with searchItem: '{$searchItem}' -> '{$searchNorm}'");

                            if ($valNorm === $searchNorm) {
//                                error_log("Match found! Adding row to matched results.");
                                $matched[] = $row;
                                break 2;
                            }
                        } else {
//                            error_log("Skipping non-string searchItem: " . print_r($searchItem, true));
                        }
                    }
                }
            } else {
//                error_log("Not a JSON or failed decoding, doing direct comparison.");

                foreach ($values as $val) {
                    if (is_string($row->options_value) && is_string($val)) {
                        $rowValNorm = ltrim(strtolower($row->options_value), '/');
                        $valNorm = ltrim(strtolower($val), '/');
//                        error_log("Normalized row value: '{$row->options_value}' -> '{$rowValNorm}', search value: '{$val}' -> '{$valNorm}'");

                        if ($rowValNorm === $valNorm) {
//                            error_log("Match found in direct comparison! Adding row to matched results.");
                            $matched[] = $row;
                            break;
                        }
                    } else {
//                        error_log("Direct string comparison: '{$row->options_value}' vs '{$val}'");
                        if (strval($row->options_value) === strval($val)) {
//                            error_log("Match found in direct string comparison! Adding row to matched results.");
                            $matched[] = $row;
                            break;
                        }
                    }
                }
            }
        }

//        error_log("Total matched rows: " . count($matched));
        return !empty($matched) ? $matched : false;
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