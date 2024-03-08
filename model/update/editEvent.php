<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editEvent"])) {
    // Assuming you're using filter_input to sanitize
    $event_id = filter_input(INPUT_POST, "event_id", FILTER_SANITIZE_NUMBER_INT); 
    $eventName = filter_input(INPUT_POST, "eventName", FILTER_SANITIZE_STRING);
    $eventDesc = filter_input(INPUT_POST, "eventDesc", FILTER_SANITIZE_STRING);
    $eventVenue = filter_input(INPUT_POST, "eventVenue", FILTER_SANITIZE_STRING);


    // Update the system role in the database
    $updateQuery = "UPDATE tbl_events SET event_name = :event_name, event_description = :event_desc, event_venue = :event_venue WHERE event_id = :event_id";
    $stmtUpdate = $conn->prepare($updateQuery);
    $stmtUpdate->bindParam(':event_name', $eventName);
    $stmtUpdate->bindParam(':event_desc', $eventDesc);
    $stmtUpdate->bindParam(':event_venue', $eventVenue);
    $stmtUpdate->bindParam(':event_id', $event_id);

    if ($stmtUpdate->execute()) {
        // Check if any rows were updated
        if ($stmtUpdate->rowCount() > 0) {
            // Successful update
            echo "<script>
                alert('Updated successfully.');
                window.location.href='';
            </script>";
        } else {
            // No rows were updated
            echo "<script>
                alert('No changes were made.');
                window.location.href='';
            </script>";
        }
    } else {
        // Handle the error
        echo "<script>
            alert('Update unsuccessful.');
            window.location.href='';
        </script>";
    }
    
}