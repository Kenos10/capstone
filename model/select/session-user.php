<?php

$userId = $_SESSION['user_id'];

$query = "SELECT * FROM tbl_students, tbl_sbo, tbl_system_user WHERE tbl_sbo.sbo_id = tbl_system_user.sbo_id and tbl_students.student_id = tbl_sbo.student_id and tbl_system_user.user_id = :user_id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $userId);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $profileImg = '../uploads/' . $row['profile_img'];
        $fullName = $row['first_name'] . " " . $row['last_name'];
        $role = $row['role'];
        $position = $row['position'];
        $year = $row['year_level'];
        $gmail = $row['gmail'];
        $studentId = $row['student_id'];
    }
}