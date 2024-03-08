<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    // Assuming you're using filter_input to sanitize input
    $role1 = filter_input(INPUT_POST, "role1", FILTER_SANITIZE_STRING);
    $systemId = $_POST["systemId"];

    // Update the system role in the database
    $updateQuery = "UPDATE tbl_system_user SET role = :role WHERE user_id = :user_id";
    $stmtUpdate = $conn->prepare($updateQuery);
    $stmtUpdate->bindParam(':role', $role1);
    $stmtUpdate->bindParam(':user_id', $systemId);

    if ($stmtUpdate->execute()) {
        // Successful update
        echo "<script>
            alert('Access updated successfully');
            window.location.href='';
        </script>";
    } else {
        // Handle the error
        echo "Error updating role.";
    }
}