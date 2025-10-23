<?php
//Database credentials
// Settings/db_cred.php

// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'dbforlab');


if (!defined("SERVER")) {
    define("SERVER", "localhost");
}

if (!defined("USERNAME")) {
    define("USERNAME", "root");
}

if (!defined("PASSWD")) {
    define("PASSWD", "");
}

if (!defined("DATABASE")) {
    // Use the database name from the provided SQL dump
    define("DATABASE", "shoppn");
}

// MySQL port - change if your server uses a non-standard port (default 3306)
if (!defined("PORT")) {
    define('PORT', 3306);
}
?>
