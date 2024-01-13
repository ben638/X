<?php
    $dir = "./";
    session_start();
    $_SESSION = array();
    session_destroy();
    header("Location: " . $dir . "login.php");
    exit(0);
?>