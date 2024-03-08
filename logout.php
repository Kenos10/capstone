<?php

session_start();
session_regenerate_id(true);

if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
}

header('Location: index.php');
die();

