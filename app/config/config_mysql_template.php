<?php

define('DB_USER', 'root');
define('DB_PASSWORD', '1234');
define('DB_NAME', 'test');

return [
    // Set up details on how to connect to the database


    'dsn'     => "mysql:host=localhost;dbname=".DB_NAME.";",
    'username'        => DB_USER,
    'password'        => DB_PASSWORD,
    'driver_options'  => [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
    'table_prefix'    => "test_",

    // Display details on what happens
    'verbose' => false,

    // Throw a more verbose exception when failing to connect
    'debug_connect' => false,
];
