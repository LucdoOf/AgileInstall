<?php

require 'src/boot.php';

return [
    "paths"        => [
        "migrations" => "data/database/migrations",
        "seeds"      => "data/database/seeds",
    ],
    "environments" => [
        "default_migration_table" => "migrations",
        "default_database"        => "main",
        "main"                    => [
            "adapter" => "mysql",
            "host"    => getenv('MYSQL_HOST'),
            "name"    => getenv('MYSQL_DB'),
            "user"    => getenv('MYSQL_USER'),
            "pass"    => getenv('MYSQL_PASS'),
            "port"    => 3306,
        ],
    ],
];
