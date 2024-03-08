<?php
session_start();
require_once('../config/const-nologo.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from POST request
    $userId = $_POST['user_id'];
    $eventId = $_POST['event_id'];
    $studId = $_POST['studentid'];
    $stat = $_POST['stat'];


    // Perform the insertion into tbl_request
    $query = "INSERT INTO tbl_request_absence (user_id, event_id, student_id, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $eventId, PDO::PARAM_INT);
    $stmt->bindValue(3, $studId, PDO::PARAM_INT);
    $stmt->bindValue(4, $stat, PDO::PARAM_STR);

    try {
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Data inserted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error inserting data: ' . $e->getMessage()]);
    }
} else {
    // Invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>