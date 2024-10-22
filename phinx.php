<?php

require '../share/src/boot.php';

return [
    "paths"        => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds",
    ],
    "environments" => [
        "default_migration_table" => "migrations",
        "default_database"        => "main",
        "main"                    => [
            "adapter" => "mysql",
            "host"    => getenv('MYSQL_HOST'),
            "name"    => getenv('MYSQL_DATABASE'),
            "user"    => getenv('MYSQL_USER'),
            "pass"    => getenv('MYSQL_PASSWORD'),
            "port"    => 3306,
        ],
    ],
];
