<?php

if (isset($_POST['add'])) {
    $errors = [];

    try {
        $startY  = htmlspecialchars(strip_tags($_POST['ystart']));
        $endY = $startY + 1;
    
        $query = "INSERT INTO tbl_school_year (school_yearstart, school_yearend, semester) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1,  $startY , PDO::PARAM_INT);
        $stmt->bindValue(2, $endY , PDO::PARAM_STR);
        $stmt->bindValue(3, '1st Semester', PDO::PARAM_STR);
        $stmt->execute();

        $query2 = "INSERT INTO tbl_school_year (school_yearstart, school_yearend, semester) VALUES (?, ?, ?)";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bindValue(1,  $startY , PDO::PARAM_INT);
        $stmt2->bindValue(2, $endY , PDO::PARAM_STR);
        $stmt2->bindValue(3, '2nd Semester', PDO::PARAM_STR);
        $stmt2->execute();

        $messageSuccessErr = 'Record inserted successfully!';
    } catch (Exception $e) {
        // If there is an error, add it to the errors array
        $errors[] = $e->getMessage();
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
                window.location.href = '';
             </script>";
    } else {
        $messageSuccessErr = htmlspecialchars($messageSuccessErr);
        echo "<script>
                alert('$messageSuccessErr');
                window.location.href = '';
              </script>";
    }
}