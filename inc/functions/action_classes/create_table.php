<?php

use sl_app\CreateTable;

function create_table()
{
    $data = [
        "sl_application" => [
            "id" => "INT UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "name" => "VARCHAR(255) NOT NULL",
            "type" => "VARCHAR(255) NOT NULL",
            "created_at" => "DATETIME DEFAULT CURRENT_TIMESTAMP",
            "viewed" => "TINYINT(1) NOT NULL DEFAULT 0" // 0 = не переглянуто, 1 = переглянуто
        ],
        "sl_application_meta" => [
            "meta_id" => "INT UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "main_id" => "INT UNSIGNED NOT NULL",
            "meta_key" => "VARCHAR(255) NOT NULL",
            "meta_value" => "TEXT"
        ],
        "sl_application_options" => [
            "id" => "INT UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "options_key" => "VARCHAR(255) NOT NULL",
            "options_value" => "TEXT DEFAULT NULL",
            "parent_id" => "INT UNSIGNED DEFAULT NULL"
        ]
    ];


    $creator = new CreateTable($data);
    $creator->executeTables();
}
