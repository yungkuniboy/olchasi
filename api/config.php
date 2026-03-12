<?php
$configFile = __DIR__ . '/config.local.php';
if (file_exists($configFile)) {
    return require $configFile;
}
return [
    'db_host' => 'localhost',
    'db_name' => 'a1178155_game',
    'db_user' => 'your_user',
    'db_pass' => 'your_password',
    'db_charset' => 'utf8mb4',
];
