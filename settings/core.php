<?php
// Settings/core.php

//for header redirection
ob_start();

//function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

//function to get user ID
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

//function to check for role (admin, customer, etc)
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
}

?>
