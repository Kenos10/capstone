<?php

function getStudentById($conn, $studentId) {
    $query = 'SELECT * FROM tbl_students WHERE student_id = :student_id OR first_name = :student_id OR last_name = :student_id';
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':student_id', $studentId);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return $stmt->fetch();
    } else {
        return null;
    }
}

if (isset($_POST['submit']) && !empty($_POST['studentid'])) {
    $studentId = htmlspecialchars(strip_tags($_POST['studentid']));
    $student = getStudentById($conn, $studentId);

    if ($student) {    
        header('Location: ../includes/search.php?id=' . $studentId);
        die();
    } else {          
        $_SESSION['message'] = 'Student not found';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        die();
    }    
}