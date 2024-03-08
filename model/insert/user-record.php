<?php

if (isset($_POST['add'])) {
    // Initialize an array to store all the error messages
    $errors = [];

    // Validate schoolid
    if (!ctype_digit($_POST['sboid'])) {
        $errors[] = "Error: Cannot add user";
    } else {
        $queryTry = "SELECT * FROM tbl_system_user WHERE sbo_id = :sbo_id";
        $stmtTry = $conn->prepare($queryTry);

        try {
            // Sanitize the input
            $sboid = htmlspecialchars(strip_tags($_POST['sboid']));

            // Bind the parameters and execute the query
            $stmtTry->bindValue(':sbo_id', $sboid, PDO::PARAM_INT);
            $stmtTry->execute();

            if ($stmtTry->rowCount() > 0) {
                $errors[] = "Error: User already exists.";
            }
        } catch (Exception $e) {
            // If there is an error, add it to the errors array
            $errors[] = $e->getMessage();
        }
    }

    // Validate Role
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $_POST['role']) || strlen($_POST['role']) > 30) {
        $errors[] = "Error: Invalid Role. Role field must contain only letters, numbers, and spaces.";
    }

    // Validate Username
    $queryTryUser = "SELECT * FROM tbl_system_user WHERE username = :username";
    $stmtTryUser = $conn->prepare($queryTryUser);

    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $_POST['username'])) {
        $errors[] = "Error: Invalid Username. Username field must contain only letters, numbers, and spaces.";
    } else {
        try {
            // Sanitize the input
            $user = htmlspecialchars(strip_tags($_POST['username']));

            // Bind the parameters and execute the query
            $stmtTryUser->bindValue(':username', $user, PDO::PARAM_STR);
            $stmtTryUser->execute();

            if ($stmtTryUser->rowCount() > 0) {
                $errors[] = "Username already exists.";
            }
        } catch (Exception $e) {
            // If there is an error, add it to the errors array
            $errors[] = $e->getMessage();
        }
    }

    // Validate Password
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{5,15}$/', $_POST['password'])) {
        $errors[] = "Error: Invalid Password. Password field must contain only letters, numbers and must be 5 to 15 characters.";
    }

    // Validate Confirm Password
    if ($_POST['password'] !== $_POST['confpass']) {
        $errors[] = "Error: Password and Confirm Password fields do not match.";
    }

    // If there are no errors, insert the records into the database
    if (empty($errors)) {
        try {
            // Prepare the query
            $query = "INSERT INTO tbl_system_user (sbo_id, role, username, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            // Sanitize the input
            $id = htmlspecialchars(strip_tags($_POST['sboid']));
            $role = htmlspecialchars(strip_tags($_POST['role']));
            $username = htmlspecialchars(strip_tags($_POST['username']));
            $password = htmlspecialchars(strip_tags($_POST['password']));
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the hashed password into the database
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            $stmt->bindValue(2, $role, PDO::PARAM_STR);
            $stmt->bindValue(3, $username, PDO::PARAM_STR);
            $stmt->bindValue(4, $hashed_password, PDO::PARAM_STR);
            $stmt->execute();

            // Print a message to indicate that the event was added successfully
            $messageSuccessErr = 'User added successfully.';
        } catch (PDOException $e) {
            // Print an error message if the query fails
            $errors[] = "Error: " . $e->getMessage();
        }
    }

    // If there are any errors, display them using a script alert
    if (!empty($errors)) {
        // Join the error messages into a single string
        $msg = implode("\\n", $errors);
        // Escape special characters in the message
        $msg = htmlspecialchars($msg);
        // Display the error message using a script alert
        echo "<script>
                alert('$msg');
            </script>";
    } else {
        $messageSuccessErr = htmlspecialchars($messageSuccessErr);
        echo "<script>
                alert('$messageSuccessErr');
            </script>";
    }
}
