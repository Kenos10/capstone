<?php
// get_event_details.php

// Assuming you have a database connection
require_once '../config/const-nologo.php';

if (isset($_POST['event_id'])) {
    $eventId = $_POST['event_id'];

    // Perform a query to retrieve detailed information for the specified event
    $query = "SELECT 
                tbl_events.*, 
                tbl_event_sched.*, 
                CONCAT(tbl_school_year.school_yearstart, '-', tbl_school_year.school_yearend) as school_year 
            FROM 
                tbl_events 
            JOIN 
                tbl_event_sched ON tbl_events.event_id = tbl_event_sched.event_id 
            JOIN 
                tbl_school_year ON tbl_events.school_year_id = tbl_school_year.school_year_id 
            WHERE 
                tbl_events.event_id = :event_id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':event_id', $eventId);
    $stmt->execute();

    // Fetch the result
    $eventDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Display the event details in HTML format
    echo "
        <h3>Event Details</h3>
        <p><strong>School Year:</strong> {$eventDetails['school_year']}</p>
        <p><strong>Name:</strong> {$eventDetails['event_name']}</p>
        <p><strong>Description:</strong> {$eventDetails['event_description']}</p>
        <p><strong>Venue:</strong> {$eventDetails['event_venue']}</p>
        <p><strong>Date:</strong> " . date("M. d, Y, l", strtotime($eventDetails['event_date'])) . "</p>
        <p><strong>Fines:</strong> {$eventDetails['fines']}</p>
    ";

    // You can add more details based on your database schema
} else {
    echo "Invalid request. Please provide an event ID.";
}
?>
