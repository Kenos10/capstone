<?php

if(isset($_POST['add'])){
    // Initialize an array to store all the error messages
    $errors = [];

    // File upload path
    $targetDir = "../uploads/";

    // Validate schoolid
    if(!ctype_digit($_POST['schoolID']) || strlen($_POST['schoolID']) != 7){
        $errors[] = "Error: Invalid school id. School id must be a 7-digit number.";
    } else {
        $queryTry = "SELECT * FROM tbl_sbo where student_id = :student_id";
        $stmtTry = $conn->prepare($queryTry);

        try {
            // Sanitize the input
            $sboid = htmlspecialchars(strip_tags($_POST['schoolID']));

            // Bind the parameters and execute the query
            $stmtTry->bindValue(':student_id', $sboid, PDO::PARAM_INT);
            $stmtTry->execute();

            if($stmtTry->rowCount() > 0){
                $errors[] = "Officer already exists.";
            }
        } catch (Exception $e) {
            // If there is an error, add it to the errors array
            $errors[] = $e->getMessage();
        }
    }

    // Validate Position
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $_POST['position']) || strlen($_POST['position']) > 60) {
        $errors[] = "Error: Invalid position. Position field must contain only letters, numbers, and spaces.";
    } else {
        $queryTryPosition = "SELECT * FROM tbl_sbo where position = :position";
        $stmtTryPosition = $conn->prepare($queryTryPosition);

        try {
            // Sanitize the input
            $role = htmlspecialchars(strip_tags($_POST['position']));

            // Bind the parameters and execute the query
            $stmtTryPosition->bindValue(':position', $role, PDO::PARAM_STR);
            $stmtTryPosition->execute();

            if($stmtTryPosition->rowCount() > 0){
                $errors[] = "Position already exists.";
            }
        } catch (Exception $e) {
            // If there is an error, add it to the errors array
            $errors[] = $e->getMessage();
        }
    }

    if(!empty($_FILES["file"]["name"]) && empty($errors)){
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

        if(in_array($fileType, $allowTypes)){
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){
                $query = "INSERT INTO tbl_sbo (student_id, position, profile_img) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);

                try {
                    // Sanitize the input
                    $schoolid = htmlspecialchars(strip_tags($_POST['schoolID']));
                    $position = htmlspecialchars(strip_tags($_POST['position']));

                    // Bind the parameters and execute the query
                    $stmt->bindValue(1, $schoolid, PDO::PARAM_INT);
                    $stmt->bindValue(2, $position, PDO::PARAM_STR);
                    $stmt->bindValue(3, $fileName);

                    $stmt->execute();
                    $messageSuccessErr = 'Record inserted successfully!';
                } catch (Exception $e) {
                    // If there is an error, add it to the errors array
                    $errors[] = $e->getMessage();
                }
            } else {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        } else {
            $errors[] = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
        }
    } else {
        $errors[] = 'Please select a file to upload.';
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
?>
