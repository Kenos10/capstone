<?php

session_start();
require_once('config/connection.php');

define("ACCOUNT_TYPE_A","Administrator");
define("ACCOUNT_TYPE_AM","Attendance Manager");
define("ACCOUNT_TYPE_EM","Event Manager");

if(isset($_POST['submit'])) {
    $username = strip_tags(trim($_POST['username']));
    $password = strip_tags(trim($_POST['password']));

    $query = "SELECT * FROM tbl_system_user WHERE username = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(":username", $username);
    $stmt->execute();

    $querySyear = "SELECT * FROM `tbl_school_year` WHERE YEAR(`school_yearstart`) = YEAR(CURDATE()) AND semester = '1st semester'";
    $stmtSyear = $conn->prepare($querySyear);
    $stmtSyear->execute();

    if($sy_id = $stmtSyear->fetch()){
        $_SESSION['sch_year'] = $sy_id['school_year_id'];
    }

    if ($user_data = $stmt->fetch()) {
        if (password_verify($password, $user_data['password'])) {
            if (ACCOUNT_TYPE_A == $user_data['role']) {
                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['role'] = ACCOUNT_TYPE_A;
                $_SESSION['logged_in'] = true;
                session_regenerate_id(true);
                header('Location: index.php');
                die();
            } else if (ACCOUNT_TYPE_AM == $user_data['role']) {
                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['role'] = ACCOUNT_TYPE_AM;
                $_SESSION['logged_in'] = true;
                session_regenerate_id(true);
                header('Location: index.php');
                die();
            } else if (ACCOUNT_TYPE_EM == $user_data['role']) {
                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['role'] = ACCOUNT_TYPE_EM;
                $_SESSION['logged_in'] = true;
                session_regenerate_id(true);
                header('Location: index.php');
                die();
            } else {
                $error = "Access Denied";
            }
        } else {
            $error = "Incorrect username or password";
        }
    } else {
        $error = "Incorrect username or password";
    }
}

$conn = null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/index.css?version=12">    
    <link href="icons/logo-black.png" rel="icon">
    <title>Login</title>
    <script src="assets/js/login.js?version=2" defer></script>
</head>
<body>
    <section>
        <div class="login-container">
            <div class="card-1">
                <form action="" method="POST">
                    <legend>Sign In</legend>

                    <div class="input-cont">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Type your username" required>                      
                    </div>
    
                    <div class="input-cont">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Type your password" required>
                        <div>
                            <span class="pass" id="invisible"><img src="icons/hide.png" alt="invisible"></span>
                            <span class="pass hidden" id="visible"><img src="icons/visible.png" alt="visible"></span>
                        </div>
                    </div>

                    <span class="error"><?php if (isset($error)){echo $error;} ?></span>
             
                    <div class="login">
                        <input type="submit" name="submit" value="LOGIN" id="login">
                        <a href="../MENU/menu.html">Forgot password?</a>
                    </div>
                </form>
            </div>
            
            <div class="card-2">
                <div>
                    <img src="icons/logo-cut.png" alt="icon">
                    <h3>Welcome</h3>
                </div>
            </div>
        </div>
    </section>
</body>
</html>