<?php
session_start();
require_once('../config/const-nologo.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from POST request
    $userId = $_POST['user_id'];
    $scheduleId = $_POST['schedule_id'];
    $timeObject = new DateTime($_POST['time']);
    $time = $timeObject->format('H:i:s');


    // Perform the insertion into tbl_request
    $query = "INSERT INTO tbl_request_time (user_id, schedule_id, time) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $scheduleId, PDO::PARAM_INT);
    $stmt->bindValue(3, $time, PDO::PARAM_STR);

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