<?php

session_start();
session_regenerate_id(true);

require_once('config/connection.php');
require_once('config/constant.php');
require_once('config/functions.php');

$user_data = check_login($conn);

header('Location: includes/dashboard.php');
die();

?>