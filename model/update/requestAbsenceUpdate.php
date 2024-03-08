<?php
if (isset($_POST['eventIdSt']) && isset($_POST['stat']) && isset($_POST['studentid'])) {
    $eventId = filter_input(INPUT_POST, 'eventIdSt', FILTER_SANITIZE_NUMBER_INT);
    $student = filter_input(INPUT_POST, 'studentid', FILTER_SANITIZE_NUMBER_INT);
    $stat = filter_input(INPUT_POST, 'stat', FILTER_SANITIZE_STRING);

    if ($eventId && $stat && $student) {
        $eventItem = "SELECT * FROM tbl_events, tbl_event_sched WHERE tbl_events.event_id = :event_id AND tbl_event_sched.event_id = :event_id";
        $stmtEventItem = $conn->prepare($eventItem);
        $stmtEventItem->bindValue(':event_id', $eventId);
        $stmtEventItem->execute();

        // Fetch all the results as an associative array
        $eventData = $stmtEventItem->fetchAll(PDO::FETCH_ASSOC);

        // Check if there are any results
        if (!empty($eventData)) {
            $scheduleIds = array();

            // Collect all schedule_ids from $eventData
            foreach ($eventData as $row) {
                $scheduleIds[] = $row['schedule_id'];
            }

            // Loop through each schedule_id
            foreach ($scheduleIds as $schedId) {
                // Example: Check if there is any existing data related to the request
                $checkExistingDataQuery = "SELECT COUNT(*) FROM tbl_event_attendance WHERE student_id = :student_id AND schedule_id = :schedule_id";

                $stmtCheckExistingData = $conn->prepare($checkExistingDataQuery);
                $stmtCheckExistingData->bindParam(':student_id', $student);
                $stmtCheckExistingData->bindParam(':schedule_id', $schedId);
                $stmtCheckExistingData->execute();
                $existingDataCount = $stmtCheckExistingData->fetchColumn();

                if ($existingDataCount == 0) {
                    // Continue with the insert operation
                    $insertQuery = "INSERT INTO tbl_event_attendance
                                    (student_id, remarks, schedule_id)
                                    VALUES (:stud, :stat, :sched)";
                    $smthInsert = $conn->prepare($insertQuery);

                    $smthInsert->bindParam(':stud', $student);
                    $smthInsert->bindParam(':stat', $stat);
                    $smthInsert->bindParam(':sched', $schedId);

                    if ($smthInsert->execute()) {
                        // Insert successful
                    } else {
                        echo "<script>
                                alert('Insert failed');
                            </script>";
                    }
                }
            }

            // Delete the request after successful insertions
            $deleteReq = "DELETE FROM tbl_request_absence WHERE event_id = :event_id";
            $smthDelete = $conn->prepare($deleteReq);
            $smthDelete->bindParam(':event_id', $eventId);

            if ($smthDelete->execute()) {
                echo "<script>
                        alert('Data inserted successfully');
                        window.location.href = '';
                    </script>";
            } else {
                echo "<script>
                        alert('Delete failed');
                    </script>";
            }
        } else {
            echo "<script>
                    alert('No results found for the given event ID');
                </script>";
        }
    } else {
        echo "<script>
                alert('Invalid input');
            </script>";
    }
}

if(isset($_POST['RejectSt'])){
    if(isset($_POST['reqstId'])){
        $reqId = filter_input(INPUT_POST, 'reqstId', FILTER_SANITIZE_NUMBER_INT);

        if($reqId === false){
            echo "<script>
                alert('reqId failed filter_input');
            </script>";
        }elseif($reqId === null){
            echo "<script>
                alert('reqId is not set');
            </script>";
        }elseif($reqId){
            $deleteReq = "DELETE FROM tbl_request_absence WHERE requestst_id = :req_id";
            $smthDelete = $conn->prepare($deleteReq);
            $smthDelete->bindParam(':req_id', $reqId);

            if($smthDelete->execute()){
                echo "<script>
                    alert('Request deleted successfully');
                    window.location.href = '';
                </script>";
            }else{
                echo "<script>
                    alert('delete failed');
                </script>";
            }
        }else{
            echo "<script>
                alert('Invalid reqId');
            </script>";
        }
    }else{
        echo "<script>
            alert('reqId is not set in POST');
        </script>";
    }
}


if (isset($_POST['event_id']) && isset($_POST['remarks']) && isset($_SESSION['id_student'])){
    $eventId = filter_input(INPUT_POST, 'event_id', FILTER_SANITIZE_NUMBER_INT);
    $student = $_SESSION['id_student'];
    $stat = filter_input(INPUT_POST, 'remarks', FILTER_SANITIZE_STRING);

    if ($eventId && $stat && $student) {
        $eventItem = "SELECT * FROM tbl_events, tbl_event_sched WHERE tbl_events.event_id = :event_id AND tbl_event_sched.event_id = :event_id";
        $stmtEventItem = $conn->prepare($eventItem);
        $stmtEventItem->bindValue(':event_id', $eventId);
        $stmtEventItem->execute();

        // Fetch all the results as an associative array
        $eventData = $stmtEventItem->fetchAll(PDO::FETCH_ASSOC);

        // Check if there are any results
        if (!empty($eventData)) {
            $scheduleIds = array();

            // Collect all schedule_ids from $eventData
            foreach ($eventData as $row) {
                $scheduleIds[] = $row['schedule_id'];
            }

            // Loop through each schedule_id
            foreach ($scheduleIds as $schedId) {
                // Example: Check if there is any existing data related to the request
                $checkExistingDataQuery = "SELECT COUNT(*) FROM tbl_event_attendance WHERE student_id = :student_id AND schedule_id = :schedule_id";

                $stmtCheckExistingData = $conn->prepare($checkExistingDataQuery);
                $stmtCheckExistingData->bindParam(':student_id', $student);
                $stmtCheckExistingData->bindParam(':schedule_id', $schedId);
                $stmtCheckExistingData->execute();
                $existingDataCount = $stmtCheckExistingData->fetchColumn();

                if ($existingDataCount == 0) {
                    // Continue with the insert operation
                    $insertQuery = "INSERT INTO tbl_event_attendance
                                    (student_id, remarks, schedule_id)
                                    VALUES (:stud, :stat, :sched)";
                    $smthInsert = $conn->prepare($insertQuery);

                    $smthInsert->bindParam(':stud', $student);
                    $smthInsert->bindParam(':stat', $stat);
                    $smthInsert->bindParam(':sched', $schedId);

                    if ($smthInsert->execute()) {
                        // Insert successful
                    } else {
                        echo "<script>
                                alert('Insert failed');
                            </script>";
                    }
                } else {
                                        // Continue with the insert operation
                        $insertQuery = "UPDATE tbl_event_attendance SET remarks = :remarks WHERE schedule_id = :schedule_id AND student_id = :student_id";
                        $smthInsert = $conn->prepare($insertQuery);
    
                        $smthInsert->bindParam(':student_id', $student);
                        $smthInsert->bindParam(':remarks', $stat);
                        $smthInsert->bindParam(':schedule_id', $schedId);
    
                        if ($smthInsert->execute()) {
                            // Insert successful
                        } else {
                            echo "<script>
                                    alert('Insert failed');
                                </script>";
                        }
                }
            }
        }
    }
}