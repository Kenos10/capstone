<?php
include('../includes/dashboard.php');
$filesToInclude = [EVENT, SYEAR];
foreach ($filesToInclude as $file) {
    require_once($file);
}

if($_SESSION['role'] !== ACCOUNT_TYPE_A && $_SESSION['role'] !== ACCOUNT_TYPE_EM && $_SESSION['role'] !== ACCOUNT_TYPE_AM){
    echo "<script>
            window.location.href = '../logout.php';
        </script>";
}

if(isset($_POST['month'])){
    $_SESSION['month'] = $_POST['month'];
}

if(isset($_SESSION['sch_year'])){
    $syid = $_SESSION['sch_year'];

    $querysyid = "SELECT *
    FROM tbl_school_year WHERE school_year_id = :id";
    $stmtsyid = $conn->prepare($querysyid);
    $stmtsyid->bindValue(':id', $syid);
    $stmtsyid->execute();

    $sy_id = $stmtsyid->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/report-event.css?version=<?php echo time(); ?>">
    <script src="../assets/js/time.js" defer></script>
    <title>Events</title>
</head>
<body>
    <main class="home">
        <div class="title-dashboard">
            <div>
                <span><img src="../icons/dashboard (2).png" alt="dashboard"></span>
                <h2>Event Report</h2>
                <button id="myBtn" onclick="window.print()">Generate</button>
            </div>
            <div class="breadcrumb">
                <p>Home</p>
                <p>Report</p>
                <p class="active-page">Event Report</p>
            </div>
        </div>
        <section class="home-container">
            <div class="home-card date-card">
                <div class="item-date">
                    <div>
                        <p class="count" id="time">12:00 am</p>
                        <p class="count-title" id="date">Monday, January 01, 2024</p>
                    </div>
                </div>
            </div>

            <div class="home-card filter-card">
                <form action="" method="POST" class="monthDisplay" id="monthForm">
                    <div class="option-item item-1">
                        <select name="month" id="fMonth">
                            <?php 
                                $monthOptions = array(
                                    1 => 'January',
                                    2 => 'February',
                                    3 => 'March',
                                    4 => 'April',
                                    5 => 'May',
                                    6 => 'June',
                                    7 => 'July',
                                    8 => 'August',
                                    9 => 'September',
                                    10 => 'October',
                                    11 => 'November',
                                    12 => 'December'
                                );

                                if (isset($_SESSION['month']) && ($_SESSION['month'] !== '')) {
                                    $selectedMonth = $_SESSION['month'];
                                    echo '<option disabled selected value="' . $selectedMonth . '">-- ' . $monthOptions[$selectedMonth] . ' --</option>';
                                } else {
                                    echo '<option>-- Select Month --</option>';
                                }
                            ?>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                </form>

                <form action="" method="POST">
                        <div class="option-item item-1">
                                <?php
                                    if (!empty($_SESSION['month'])) {
                                        if ($_SESSION['month'] !== "All") {
                                            echo '<select name="event" id="fEvent" required>';
                                            echo '<option disabled selected value="">-- Event Name --</option>';

                                            if (!empty($_SESSION['sch_year'])) {
                                                $eventMonth = $_SESSION['month'];
                                                $eventSY = $_SESSION['sch_year'];

                                                $queryMonth = "SELECT * FROM tbl_events WHERE MONTH(event_date) = :month AND school_year_id = :syid";
                                                $stmtMonth = $conn->prepare($queryMonth);
                                                $stmtMonth->bindParam(":month", $eventMonth);
                                                $stmtMonth->bindParam(":syid", $eventSY);
                                                $stmtMonth->execute();

                                                while ($rowCount = $stmtMonth->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='" . $rowCount['event_id'] . "'>" . $rowCount['event_name'] . "</option>";
                                                }
                                            }

                                            echo '</select>';
                                        }
                                    }
                                ?>

                                <select name="sYear" id="sYear" required>
                                    <option disabled selected value="">-- Year Level--</option>
                                    <option value="1st">1st</option>
                                    <option value="2nd">2nd</option>
                                    <option value="3rd">3rd</option>
                                    <option value="4th">4th</option>
                                </select>
                        </div>

                        <div class="option-item item-3">
                            <input type="submit" value="Filter" name="fReport">
                        </div>

                    </div>
                </form>
            </div>

            <div class="home-card report-card" id="section-to-print">
                <div class="report-header">
                    <img src="../logo/com.png" alt="logo">
                    <div>
                        <h2>Capitol University</h2>
                        <h3>College of Computer Studies </h3>
                        <p>Event Attendance Report</p>
                    </div>
                    <img src="../logo/cu.png" alt="logo">
                </div>
                <div class="report-table">
                    <div class="report-info">
                    <?php
                        if (!empty($_SESSION['month'])) {
                            if (isset($_POST['event']) && isset($_POST['sYear'])) {
                                $id = $_POST['event'];
                                $year = $_POST['sYear'];

                                $queryMorningTime = "
                                SELECT 
                                    tbl_event_sched.schedule_id,
                                    tbl_event_sched.timein,
                                    tbl_event_sched.timeout
                                FROM tbl_event_sched
                                WHERE tbl_event_sched.event_id = :id
                                AND tbl_event_sched.phases = 'Morning'
                                ";
                                
                                $stmtMorningTime = $conn->prepare($queryMorningTime);
                                $stmtMorningTime->bindParam(":id", $id);
                                $stmtMorningTime->execute();
                                
                                $morningTotalSeconds = 0;
                                
                                while ($rowMorningTime = $stmtMorningTime->fetch()) {
                                    $morningTimeout = strtotime($rowMorningTime['timeout']);
                                    $morningTimein = strtotime($rowMorningTime['timein']);
                                    
                                    // Calculate the difference in seconds and accumulate
                                    $morningTotalSeconds += $morningTimeout - $morningTimein;
                                }
                                
                                // Convert total seconds to HH:MM:SS format
                                $_SESSION['morn_timetotal'] = gmdate("H:i:s", $morningTotalSeconds);
                            
                                // Afternoon Phase
                                $queryAfternoonTime = "
                                SELECT 
                                    tbl_event_sched.schedule_id,
                                    tbl_event_sched.timeout,
                                    tbl_event_sched.timein
                                FROM tbl_event_sched
                                WHERE tbl_event_sched.event_id = :id
                                AND tbl_event_sched.phases = 'Afternoon'
                                ";

                                $stmtAfternoonTime = $conn->prepare($queryAfternoonTime);
                                $stmtAfternoonTime->bindParam(":id", $id);
                                $stmtAfternoonTime->execute();

                                $afternoonTotalSeconds = 0;

                                while ($rowAfternoonTime = $stmtAfternoonTime->fetch()) {
                                $afternoonTimeout = strtotime($rowAfternoonTime['timeout']);
                                $afternoonTimein = strtotime($rowAfternoonTime['timein']);

                                // Calculate the difference in seconds and accumulate
                                $afternoonTotalSeconds += $afternoonTimeout - $afternoonTimein;
                                }

                                // Convert total seconds to HH:MM:SS format
                                $_SESSION['aft_timetotal'] = gmdate("H:i:s", $afternoonTotalSeconds);


                                $queryTotalTime = "
                                    SELECT 
                                        tbl_events.*, 
                                        SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, tbl_event_sched.timein, tbl_event_sched.timeout))) AS overall_total_time
                                    FROM tbl_events
                                    LEFT JOIN tbl_event_sched ON tbl_events.event_id = tbl_event_sched.event_id
                                    WHERE tbl_events.event_id = :id
                                    GROUP BY tbl_events.event_id
                                ";

                                $stmtTotalTime = $conn->prepare($queryTotalTime);
                                $stmtTotalTime->bindParam(":id", $id);
                                $stmtTotalTime->execute();

                                if ($row = $stmtTotalTime->fetch()) {
                                    echo "
                                        <p>School Year: <b>" . $sy_id['school_yearstart'] . "-" . $sy_id['school_yearend'] . "</b></p>
                                        <p>Course and Year: <b>BSIT - " . $year . " Year</b></p>
                                        <p>Event: <b>" . $row['event_name'] . "</b></p>
                                        <p>Date: <b>" . date('l, F j, Y', strtotime($row['event_date'])) . "</b></p>
                                        <p>Venue: <b>" . $row['event_venue'] . "</b></p>
                                        <p>Description: <b>" . $row['event_description'] . "</b></p>
                                        <p>Total Time: <b>" . $row['overall_total_time'] . "</b></p>
                                    ";
                                }
                            }
                        }
                    ?>

                    </div>
                    
                    <?php
                        if (!empty($_SESSION['month'])) {
                            if ($_SESSION['month'] !== "") {
                                echo '<table>';
                                echo '<thead>';
                                echo '<tr>';
                                echo '<th>Student ID</th>';
                                echo '<th>Name</th>';
                            
                                // Display morning status column header only if there is morning data
                                if (isset($_POST['event']) && isset($_POST['sYear']) && isset($_POST['fReport'])) {
                                    $eventId = $_POST['event'];
                                    $studentYear = $_POST['sYear'];
                            
                                    $queryMorningCheck = "
                                        SELECT 1
                                        FROM tbl_event_sched
                                        WHERE event_id = :event
                                        AND phases = 'Morning'
                                    ";
                            
                                    $stmtMorningCheck = $conn->prepare($queryMorningCheck);
                                    $stmtMorningCheck->bindParam(':event', $eventId);
                                    $stmtMorningCheck->execute();
                            
                                    $hasMorningData = $stmtMorningCheck->fetchColumn();
                            
                                    if ($hasMorningData) {
                                        echo '<th>Morning</th>';
                                        echo '<th>Remarks</th>';
                                        echo '<th>Fines</th>';
                                    }
                                }
                            
                                // Display afternoon status column header only if there is afternoon data
                                if (isset($_POST['event']) && isset($_POST['sYear']) && isset($_POST['fReport'])) {
                                    $eventId = $_POST['event'];
                                    $studentYear = $_POST['sYear'];
                            
                                    $queryAfternoonCheck = "
                                        SELECT 1
                                        FROM tbl_event_sched
                                        WHERE event_id = :event
                                        AND phases = 'Afternoon'
                                    ";
                            
                                    $stmtAfternoonCheck = $conn->prepare($queryAfternoonCheck);
                                    $stmtAfternoonCheck->bindParam(':event', $eventId);
                                    $stmtAfternoonCheck->execute();
                            
                                    $hasAfternoonData = $stmtAfternoonCheck->fetchColumn();
                            
                                    if ($hasAfternoonData) {
                                        echo '<th>Afternoon</th>';
                                        echo '<th>Remarks</th>';
                                        echo '<th>Fines</th>';
                                    }
                                }
                            
                                echo '<th>Total Time</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';
                            
                                if (isset($_POST['event']) && isset($_POST['sYear']) && isset($_POST['fReport'])) {
                                    $eventId = $_POST['event'];
                                    $studentYear = $_POST['sYear'];
                            
                                    $queryEventAtt = "SELECT 
                                    tbl_students.student_id, 
                                    CONCAT(tbl_students.first_name, ' ', tbl_students.last_name) AS fullname,
                                    CASE
                                        WHEN morning_att.time_in_out IS NOT NULL AND TIME_TO_SEC(morning_att.time_in_out) < 0 THEN 'TimeInOnly'
                                        ELSE COALESCE(morning_att.time_in_out, 'Absent')
                                    END AS morning_time_in_out,
                                    CASE
                                        WHEN afternoon_att.time_in_out IS NOT NULL AND TIME_TO_SEC(afternoon_att.time_in_out) < 0 THEN 'TimeInOnly'
                                        ELSE COALESCE(afternoon_att.time_in_out, 'Absent')
                                    END AS afternoon_time_in_out,                                
                                    CASE 
                                        WHEN morning_att.att_status IS NULL OR morning_att.att_status = 'TimeInOnly'AND afternoon_att.att_status IS NULL OR afternoon_att.att_status = 'TimeInOnly' THEN COALESCE(tbl_events.fines, '0.00')
                                        ELSE '0.00'
                                    END AS fines,
                                    morning_att.remarks AS morning_remarks, 
                                    afternoon_att.remarks AS afternoon_remarks
                                FROM tbl_students
                                LEFT JOIN (
                                    SELECT 
                                        student_id,
                                        att_status,
                                        remarks,
                                        CASE
                                            WHEN time_out = '00:00:00' THEN '00:00:00'
                                            ELSE SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, time_in, timeout)))
                                        END AS time_in_out
                                    FROM tbl_event_attendance
                                    JOIN tbl_event_sched ON tbl_event_attendance.schedule_id = tbl_event_sched.schedule_id
                                    WHERE tbl_event_sched.event_id = :event
                                    AND tbl_event_sched.phases = 'Morning'
                                    GROUP BY student_id
                                ) AS morning_att ON tbl_students.student_id = morning_att.student_id
                                LEFT JOIN (
                                    SELECT 
                                        student_id,
                                        att_status,
                                        remarks,
                                        CASE
                                            WHEN time_out = '00:00:00' THEN '00:00:00'
                                            ELSE SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, time_in, timeout)))
                                        END AS time_in_out
                                    FROM tbl_event_attendance
                                    JOIN tbl_event_sched ON tbl_event_attendance.schedule_id = tbl_event_sched.schedule_id
                                    WHERE tbl_event_sched.event_id = :event
                                    AND tbl_event_sched.phases = 'Afternoon'
                                    GROUP BY student_id
                                ) AS afternoon_att ON tbl_students.student_id = afternoon_att.student_id                                
                                LEFT JOIN tbl_events ON tbl_events.event_id = :event
                                WHERE tbl_students.year_level = :year AND tbl_students.status = 'Active'
                                ";
                            
                                    $stmtAtt = $conn->prepare($queryEventAtt);
                                    $stmtAtt->bindParam(':event', $eventId);
                                    $stmtAtt->bindParam(':year', $studentYear);
                                    $stmtAtt->execute();
                            
                                    while ($rowAtt = $stmtAtt->fetch()) {
                                        echo "<tr>
                                                <td>" . $rowAtt['student_id'] . "</td>
                                                <td>" . $rowAtt['fullname'] . "</td>";
                                        
                                        // Calculated time
                                        $calculatedTotal = 0;
                                    
                                        // Display morning time in/out only if there is morning data
                                        if ($hasMorningData) {
                                            if (isset($rowAtt['morning_time_in_out']) && strtotime($rowAtt['morning_time_in_out']) !== false) {
                                                $morningDiff = strtotime($_SESSION['morn_timetotal']) - strtotime($rowAtt['morning_time_in_out']);
                                                $morningDiffDisplay = ($morningDiff <= 0) ? "Present" : gmdate("H:i:s", $morningDiff);
                                                if(!empty($rowAtt['morning_remarks'])){
                                                    echo "<td>00:00:00</td>";
                                                    echo "<td>" . $rowAtt['morning_remarks']. "</td>";
                                                    echo "<td>" . $rowAtt['fines'] . "</td>";
                                                }else{
                                                    echo "<td>" . $morningDiffDisplay . "</td>";
                                                    echo "<td></td>";
                                                    echo "<td>" . $rowAtt['fines'] . "</td>";

                                                    if ($morningDiff > 0) {
                                                        $calculatedTotal += $morningDiff;
                                                    }
                                                }
                                            } else {
                                                echo "<td>".$_SESSION['morn_timetotal']."</td>";
                                                echo "<td>" . $rowAtt['morning_remarks'] . "</td>";
                                                echo "<td>" . $rowAtt['fines'] . "</td>";
                                                $calculatedTotal += strtotime("1970-01-01 " . $_SESSION['morn_timetotal'] . " UTC");
                                            }
                                        }
                                    
                                        // Display afternoon time in/out only if there is afternoon data
                                        if ($hasAfternoonData) {
                                            if (isset($rowAtt['afternoon_time_in_out']) && strtotime($rowAtt['afternoon_time_in_out']) !== false) {
                                                $afternoonDiff = strtotime($_SESSION['aft_timetotal']) - strtotime($rowAtt['afternoon_time_in_out']);
                                                $afternoonDiffDisplay = ($afternoonDiff <= 0) ? "Present" : gmdate("H:i:s", $afternoonDiff);
                                                echo "<td>" . $afternoonDiffDisplay . "</td>";
                                                echo "<td>" . $rowAtt['afternoon_remarks'] . "</td>";
                                                echo "<td>" . $rowAtt['fines'] . "</td>";
                                                if ($afternoonDiff > 0) {
                                                    $calculatedTotal += $afternoonDiff;
                                                }
                                            } else {
                                                echo "<td>".$_SESSION['aft_timetotal']."</td>";
                                                echo "<td>" . $rowAtt['afternoon_remarks'] . "</td>";
                                                echo "<td>" . $rowAtt['fines'] . "</td>";
                                                $calculatedTotal += strtotime("1970-01-01 " . $_SESSION['aft_timetotal'] . " UTC");
                                            }
                                        }
                                    
                                        $calculatedTotalFormatted = gmdate("H:i:s", $calculatedTotal);
                                    
                                        echo "<td>" . $calculatedTotalFormatted . "</td>";
                                    }                                                              
                                }                           
                                echo '</tbody>';
                                echo '</table>';
                            }                          
                        }
                    ?>
                </div>
            </div>
       </section>
    </main>

<script>
    const form = document.getElementById('monthForm');
    const select = document.getElementById('fMonth');

    select.addEventListener('change', function () {
        form.submit();
    });

    var deleteLinks = document.getElementsByClassName("delete");
    for (var i = 0; i < deleteLinks.length; i++) {
        deleteLinks[i].addEventListener("click", function(event){
            if(!confirm("Are you sure you want to delete this event?")){
                event.preventDefault();
            }
        });
    }

    const currentUrl = window.location.href;
    if (currentUrl.includes('deleteid')) {
        const updatedUrl = currentUrl.split('?')[0];
        window.location.href = updatedUrl;
    }
</script>

</body>
</html>