<?php
//Database credentials
// Settings/db_cred.php

// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'dbforlab');


if (!defined("SERVER")) {
    // Prefer IPv4 localhost to avoid IPv6/hostname resolution issues
    define("SERVER", "localhost");
}

if (!defined("USERNAME")) {
    define("USERNAME", "elsie.lartey");
}

if (!defined("PASSWD")) {
    define("PASSWD", "YourNewPassword");
}

if (!defined("DATABASE")) {
    // Use the database name from the provided SQL dump
    define("DATABASE", "ecommerce_2025A_elsie_lartey");
}

// MySQL port - change if your server uses a non-standard port (default 3306)
// Determine port: prefer environment variable MYSQL_PORT, otherwise try common ports
if (!defined('PORT')) {
    $envPort = getenv('MYSQL_PORT');
    if ($envPort && is_numeric($envPort)) {
        define('PORT', (int)$envPort);
    } else {
        // common ports to try, pick the first one (you can override with env var)
        $common = [3306, 3307, 33060];
        // Prefer the MySQL TCP listener port observed on this machine (3307)
        define('PORT', 3307);
    }
}
?>
