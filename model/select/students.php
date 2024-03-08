<?php

$yearLevel = 'All';
define("ACTIVE","Active");

if(isset($_POST['yearlevel']) && !($_POST['yearlevel'] == 'All')){
    $yearLevel = intval(htmlspecialchars($_POST['yearlevel']));
    $query = 'SELECT * FROM tbl_students WHERE year_level = :year_level AND status = :active';
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':year_level', $yearLevel);
    $stmt->bindValue(':active', ACTIVE);
    $stmt->execute();
}else{
    $query = 'SELECT * FROM tbl_students WHERE status = :active';
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':active', ACTIVE);
    $stmt->execute();
}

$countQuery = 'SELECT COUNT(*) FROM tbl_students WHERE status = :active';

if(isset($_POST['yearlevel']) && !($_POST['yearlevel'] == 'All')){
    $countQuery .= ' WHERE year_level = :year_level AND status = :active';
}

$countStmt = $conn->prepare($countQuery);
$countStmt->bindValue(':active', ACTIVE);

if(isset($_POST['yearlevel']) && !($_POST['yearlevel'] == 'All')){
    $countStmt->bindValue(':year_level', $yearLevel);
    $countStmt->bindValue(':active', ACTIVE);
}

$countStmt->execute();
$studentCount = $countStmt->fetchColumn();