<?php

namespace sl_app;



class CreateTable
{
    protected array $tables;

    public function __construct(array|object $input)
    {
        $this->tables = is_object($input) ? get_object_vars($input) : $input;
    }

    public function generateSQL(): array
    {
        $queries = [];

        foreach ($this->tables as $tableName => $columns) {
            if (!is_array($columns)) continue;

            $columnsSql = [];

            foreach ($columns as $columnName => $definition) {
                $columnsSql[] = "`$columnName` $definition";
            }

            // dbDelta вимагає наявності PRIMARY KEY або UNIQUE
            $hasPrimaryKey = preg_grep('/PRIMARY\s+KEY/i', $columnsSql);
            if (empty($hasPrimaryKey)) {
                continue; // пропускаємо таблиці без ключів, бо dbDelta не обробить їх
            }

            $columnsString = implode(",\n  ", $columnsSql);
            $queries[$tableName] = "CREATE TABLE `$tableName` (\n  $columnsString\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        }

        return $queries;
    }

    public function executeTables(): void
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $queries = $this->generateSQL();

        foreach ($queries as $query) {
            dbDelta($query); // автоматично оновлює структуру
        }
    }
}

