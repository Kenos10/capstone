<?php

$query = 'SELECT * FROM tbl_sbo, tbl_students WHERE tbl_sbo.student_id = tbl_students.student_id';
$stmt = $conn->prepare($query);
$stmt->execute();

$countQuery = 'SELECT COUNT(*) FROM tbl_sbo';
$countStmt = $conn->prepare($countQuery);
$countStmt->execute();
$sboCount = $countStmt->fetchColumn();