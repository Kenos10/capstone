<?php
// Insert
function validate_input($input, $field_name) {
    if (!preg_match('/^[a-zA-Z0-9\s.,-]+$/', $input) || empty($input)) {
        return "Error: Invalid $field_name. $field_name field must contain only letters, numbers, spaces, commas, periods, and dashes.";
    }
    return '';
}

if (isset($_POST['addevent'])) {
    $data = [
        'eventname' => htmlspecialchars(strip_tags($_POST['eventname'])),
        'eventdesc' => htmlspecialchars(strip_tags($_POST['eventdesc'])),
        'eventvenue' => htmlspecialchars(strip_tags($_POST['eventvenue'])),
        'eventdate' => htmlspecialchars(strip_tags($_POST['eventdate'])),
        'fines' => floatval($_POST['fines']),
        'syear' => intval($_POST['syear']), // Add fines field
        'errors' => []
    ];

    foreach ($data as $key => $value) {
        if ($key != 'errors' && $key != 'eventdate' && $key != 'fines') {
            $error = validate_input($value, $key);
            if (!empty($error)) {
                $data['errors'][] = $error;
            }
        }
    }

    if (!DateTime::createFromFormat('Y-m-d', $data['eventdate']) || empty($data['eventdate'])) {
        $data['errors'][] = "Error: Invalid date. Please enter a valid date format.";
    }

    if (empty($data['errors'])) {
        try {
            $query_event = "INSERT INTO tbl_events (event_name, event_description, event_venue, event_date, fines, school_year_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_event = $conn->prepare($query_event);

            $stmt_event->bindValue(1, $data['eventname'], PDO::PARAM_STR);
            $stmt_event->bindValue(2, $data['eventdesc'], PDO::PARAM_STR);
            $stmt_event->bindValue(3, $data['eventvenue'], PDO::PARAM_STR);
            $stmt_event->bindValue(4, $data['eventdate'], PDO::PARAM_STR);
            $stmt_event->bindValue(5, $data['fines'], PDO::PARAM_STR);
            $stmt_event->bindValue(6, $data['syear'], PDO::PARAM_INT);
            $stmt_event->execute();

            $event_id = $conn->lastInsertId();

            if (isset($_POST['schedule'])) {
                $schedule = $_POST['schedule'];
                $timeIn = htmlspecialchars(strip_tags($_POST['timeIn']));
                $timeOut = htmlspecialchars(strip_tags($_POST['timeOut']));
                $timeInA = htmlspecialchars(strip_tags($_POST['timeInA']));
                $timeOutA = htmlspecialchars(strip_tags($_POST['timeOutA']));
                $phase = htmlspecialchars(strip_tags($_POST['phase']));

                switch ($schedule) {
                    case 'timeInOut':
                        if ($phase == 'both') {
                            $query_sched = 'INSERT INTO tbl_event_sched (event_id, timein, timeout, phases) VALUES (?, ?, ?, ?)';
                            $stmt_sched = $conn->prepare($query_sched);
                            $stmt_sched->bindValue(1, $event_id, PDO::PARAM_INT);
                            $stmt_sched->bindValue(2, $timeIn, PDO::PARAM_STR);
                            $stmt_sched->bindValue(3, $timeOut, PDO::PARAM_STR);
                            $stmt_sched->bindValue(4, 'morning', PDO::PARAM_STR);
                            $stmt_sched->execute();
                            
                            $query_schedA = 'INSERT INTO tbl_event_sched (event_id, timein, timeout, phases) VALUES (?, ?, ?, ?)';
                            $stmt_schedA = $conn->prepare($query_schedA);
                            $stmt_schedA->bindValue(1, $event_id, PDO::PARAM_INT);
                            $stmt_schedA->bindValue(2, $timeInA, PDO::PARAM_STR);
                            $stmt_schedA->bindValue(3, $timeOutA, PDO::PARAM_STR);
                            $stmt_schedA->bindValue(4, 'afternoon', PDO::PARAM_STR);
                            $stmt_schedA->execute();
                        } else {
                            $query_sched = 'INSERT INTO tbl_event_sched (event_id, timein, timeout, phases) VALUES (?, ?, ?, ?)';
                            $stmt_sched = $conn->prepare($query_sched);
                            $stmt_sched->bindValue(1, $event_id, PDO::PARAM_INT);
                            $stmt_sched->bindValue(2, $timeIn, PDO::PARAM_STR);
                            $stmt_sched->bindValue(3, $timeOut, PDO::PARAM_STR);
                            $stmt_sched->bindValue(4, $phase, PDO::PARAM_STR);
                            $stmt_sched->execute();
                        }
                        break;
                    case 'disable':
                    default:
                        $query_sched = 'INSERT INTO tbl_event_sched (event_id) VALUES (?)';
                        $stmt_sched = $conn->prepare($query_sched);
                        $stmt_sched->bindValue(1, $event_id, PDO::PARAM_INT);
                        $stmt_sched->execute();
                        break;
                }
            }

            echo "<script>
                    alert('Record inserted successfully!');
                    window.location.href = '';
                  </script>";
        } catch (PDOException $e) {
            echo "<script>
                    alert('{$e->getMessage()}');
                    window.location.href = '';
                  </script>";
        }
    } else {
        $msg = implode("\\n", $data['errors']);
        echo "<script>
                alert('$msg');
                window.location.href = '';
              </script>";
    }
}
?>
