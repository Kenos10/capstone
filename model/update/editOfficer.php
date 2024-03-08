<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editOfficer"])) {
    // Assuming you're using filter_input to sanitize
    $position = filter_input(INPUT_POST, "position", FILTER_SANITIZE_STRING);
    $sboId = filter_input(INPUT_POST, "sbo_id", FILTER_SANITIZE_NUMBER_INT);    

    // Update the system role in the database
    $updateQuery = "UPDATE tbl_sbo SET position = :position WHERE sbo_id = :sbo_id";
    $stmtUpdate = $conn->prepare($updateQuery);
    $stmtUpdate->bindParam(':position', $position);
    $stmtUpdate->bindParam(':sbo_id', $sboId);

    if ($stmtUpdate->execute()) {
        // Check if any rows were updated
        if ($stmtUpdate->rowCount() > 0) {
            // Successful update
            echo "<script>
                alert('Updated successfully.');
                window.location.href='';
            </script>";
        } else {
            // No rows were updated
            echo "<script>
                alert('No changes were made.');
                window.location.href='';
            </script>";
        }
    } else {
        // Handle the error
        echo "<script>
            alert('Update unsuccessful.');
            window.location.href='';
        </script>";
    }
    
}