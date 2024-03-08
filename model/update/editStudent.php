<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editStudent"])) {
    // Assuming you're using filter_input to sanitize
    $fname = filter_input(INPUT_POST, "fname", FILTER_SANITIZE_STRING);
    $lname = filter_input(INPUT_POST, "lname", FILTER_SANITIZE_STRING);
    $year = filter_input(INPUT_POST, "year", FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, "status", FILTER_SANITIZE_STRING);
    $studentId = filter_input(INPUT_POST, "student_id", FILTER_SANITIZE_NUMBER_INT);    

    // Update the system role in the database
    $updateQuery = "UPDATE tbl_students SET first_name = :fname, last_name = :lname, year_level = :year, status = :status WHERE student_id = :student_id";
    $stmtUpdate = $conn->prepare($updateQuery);
    $stmtUpdate->bindParam(':fname', $fname);
    $stmtUpdate->bindParam(':lname', $lname);
    $stmtUpdate->bindParam(':year', $year);
    $stmtUpdate->bindParam(':status', $status);
    $stmtUpdate->bindParam(':student_id', $studentId);    

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