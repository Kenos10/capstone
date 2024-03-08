<?php
    $countRequestQuery = "SELECT COUNT(*) FROM tbl_request_time";
    $stmtRequestCount = $conn->prepare($countRequestQuery);
    $stmtRequestCount->execute();
    $requestCount = $stmtRequestCount->fetchColumn();

    // Count rows from tbl_request_student
    $countStudentQuery = "SELECT COUNT(*) FROM tbl_request_absence";
    $stmtStudentCount = $conn->prepare($countStudentQuery);
    $stmtStudentCount->execute();
    $studentCount = $stmtStudentCount->fetchColumn();

    // Calculate the total count
    $totalCountRequest = $requestCount + $studentCount;
?>