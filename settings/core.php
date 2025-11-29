<?php
// Settings/core.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


//for header redirection
ob_start();

//function to check for login
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id'])) {
    header("Location: ../login/login.php");
    exit;
}

function checkLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

//function to get user ID
function getUserId() {
    return checkLoggedIn() ? $_SESSION['id'] : null;
}

//function to check for role (admin, customer, etc)

function checkAdminRole() {
    if (!checkLoggedIn()) {
        return false;
    }
    return isset($_SESSION['role']) && $_SESSION['role'] == 1; //admin role=1 
}


?>

