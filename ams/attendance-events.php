<?php
include('../includes/dashboard.php');
require_once(EVENT);

if($_SESSION['role'] !== ACCOUNT_TYPE_A && $_SESSION['role'] !== ACCOUNT_TYPE_AM){
    echo "<script>
            window.location.href = '../logout.php';
        </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/attendance-events.css?version=<?php echo time(); ?>">
    <script src="../assets/js/time.js" defer></script>
    <title>Attendance | Event</title>
</head>
<body>
<main class="home">
        <div class="title-dashboard">
            <div>
                <span><img src="../icons/dashboard (2).png" alt="dashboard"></span>
                <h2>Event Attendance</h2>
                <button onclick="location.href='events.php'">View Events</button>
            </div>
            <div class="breadcrumb">
                <p>Home</p>
                <p>Attendance</p>
                <p class="active-page">Event Attendance</p>
            </div>
        </div>

        <section class="home-container">
            <div class="home-card students-card">
                <span><img src="../icons/calendar (4).png" alt="time"></span>
                <div>
                    <p class="count" id="time">12:00 am</p>
                    <p class="count-title" id="date">Monday, January 01, 2024</p>
                </div>
            </div>

            <div class="home-card officers-card">
                <span><img src="../icons/people.png" alt="officers"></span>
                <div>
                    <p class="count"><?php echo $eventCountMonth; ?></p>
                    <p class="count-title">This month events</p>
                </div>
            </div>

            <div class="home-card events-card">
                <span><img src="../icons/calendar (2).png" alt="events"></span>
                <div>
                    <p class="count"><?php echo $eventCount; ?></p>
                    <p class="count-title">Total events</p>
                </div>
            </div>

            <div class="home-card table-card">
            <div class="home-card options-card">
                    <div class="option-item">
                      <div class="item-1 item-info">
                        <img src="../icons/info.png" alt="info" id="info-toggle">
                        <div id="info-detail">
                        <?php
                            if(isset($_POST['eventName'])){
                                $currentMonth = date('m');
                                $currentYear = date('Y');
                                $eId = $_POST['eventName'];
    
                                $queryEvent = "SELECT * 
                                FROM tbl_events, tbl_event_sched 
                                WHERE MONTH(event_date) = :currentMonth 
                                AND YEAR(event_date) = :currentYear 
                                AND tbl_events.event_id = :event_id 
                                AND tbl_event_sched.event_id = :event_id";
    
                                $stmtEvent = $conn->prepare($queryEvent);
                                $stmtEvent->bindParam(':currentMonth', $currentMonth);
                                $stmtEvent->bindParam(':currentYear', $currentYear);
                                $stmtEvent->bindParam(':event_id', $eId );
                                $stmtEvent->execute();
    
                                $resultEvents = $stmtEvent->fetch(PDO::FETCH_ASSOC);
    
                                if ($resultEvents) {
                                    echo "<p> Event Name: ".$resultEvents['event_name']."</p>";
                                    echo "<p> Event Description: ".$resultEvents['event_description']."</p>";
                                } else {
                                    echo "<p>No events found for the current month and year.</p>";
                                }
                            }
                        ?>

                        </div>
                      </div>
                      
                      <form action="" method="POST">
                        <div class="item-1">
                            <select name="eventName" id="eventNameSelect">
                            <?php 
                                $currentMonth = date('m');
                                $currentYear = date('Y');
                                $schoolYear = $_SESSION['sch_year'];

                                $queryEvent = "SELECT * FROM tbl_events WHERE MONTH(event_date) = :currentMonth AND YEAR(event_date) = :currentYear AND school_year_id = :schoolYear";

                                $stmtEvent = $conn->prepare($queryEvent);
                                $stmtEvent->bindParam(':currentMonth', $currentMonth);
                                $stmtEvent->bindParam(':currentYear', $currentYear);
                                $stmtEvent->bindParam(':schoolYear', $schoolYear);
                                $stmtEvent->execute();

                                $resultEvents = $stmtEvent->fetchAll(PDO::FETCH_ASSOC);

                                echo "<option>--Select ".date('M')." Event--</option>";
                                foreach ($resultEvents as $event) {
                                    echo "<option value='".$event['event_id']."'>".$event['event_name']."</option>";
                                }
                            ?>
                            </select>
                        </div>

                        <div class="item-1">
                            <input type="submit" value="Filter" name="filter">
                        </div>
                      </form>
                    </div>

                    <div class="option-item">
                        <a href="#" id="takeAttendanceBtn" class="button-link">Attendance →</a>
                    </div>
              </div>
              
              <div class="table-container">
                <table id="myTable" class="display">
                    <thead>
                        <tr class="table-title">
                            <th>Student Id</th>
                            <th>Full Name</th>
                            <th>Year</th>
                            <th>Present</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                            if(isset($_POST['eventName'])){
                                $eventId = $_POST['eventName'];

                                $queryEventAtt = "SELECT tbl_event_attendance.*, tbl_students.*, tbl_event_sched.*
                                    FROM tbl_event_attendance 
                                    JOIN tbl_students ON tbl_event_attendance.student_id = tbl_students.student_id 
                                    JOIN tbl_event_sched ON tbl_event_attendance.schedule_id = tbl_event_sched.schedule_id 
                                    WHERE tbl_event_sched.event_id = :eventId";
                                $stmtEventAtt = $conn->prepare($queryEventAtt);
                                $stmtEventAtt->bindParam(':eventId', $eventId);

                                if($stmtEventAtt->execute()){
                                    while($rowEventAtt = $stmtEventAtt->fetch()){
                                        echo"
                                        <tr>
                                            <td>".$rowEventAtt['student_id']."</td>
                                            <td>".$rowEventAtt['first_name'].' '.$rowEventAtt['last_name']."</td>
                                            <td>".$rowEventAtt['year_level']."</td>
                                            <td>".$rowEventAtt['phases']."</td>
                                        </tr>
                                        ";
                                    }
                                }


                            }
                        ?>
                    </tbody>
                </table>
              </div>
            </div>
        </section>
    </main>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get references to the select and button elements
        var eventNameSelect = document.getElementById("eventNameSelect");
        var takeAttendanceBtn = document.getElementById("takeAttendanceBtn");

        // Attach a click event listener to the "Take Attendance" button
        takeAttendanceBtn.addEventListener("click", function() {
            // Get the selected event_id from the select element
            var selectedEventId = eventNameSelect.value;

            // Check if an event is selected
            if (!isNaN(selectedEventId)) {
                // Construct the URL with the selected event_id
                var takeAttendanceUrl = "take-attevent.php?eventId=" + selectedEventId;

                // Open the URL in a new window
                window.open(takeAttendanceUrl, "_blank");
            } else {
                // Show an alert if no event is selected
                alert("Please select an event before taking attendance.");
            }
        });
    });
</script>
</body>
</html>