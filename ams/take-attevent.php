<?php
session_start();
require_once('../config/const-nologo.php');
require_once(CURRENT_USER);

$date = intval(date('j'));
$timein = null;
$timeout = null;
$today = date('H:i:s');

if (isset($_GET['eventId'])) {
    $_SESSION['eventId'] = filter_var($_GET['eventId'], FILTER_VALIDATE_INT);
}

if(isset($_POST['sched_id'])) {
    $selectedSchedID = filter_var($_POST['sched_id'], FILTER_SANITIZE_NUMBER_INT);
    $_SESSION['sched_id'] = $selectedSchedID;
}

if (empty($_SESSION['sched_id'])) {
    $eventId = $_SESSION['eventId'];

    $querySession = "SELECT schedule_id
                     FROM tbl_event_sched
                     WHERE event_id = :eventId LIMIT 1";
                     
    $stmtSession = $conn->prepare($querySession);
    $stmtSession->bindParam(':eventId', $eventId);
    $stmtSession->execute();

    $scheduleId = $stmtSession->fetchColumn();

    if ($scheduleId) {
        $_SESSION['sched_id'] = $scheduleId;
    }
}

if (isset($_SESSION['eventId'])) {
    $eventId = $_SESSION['eventId'];
    $schedId =  $_SESSION['sched_id'];

    $queryAttendance = "SELECT tbl_event_sched.*, tbl_event_attendance.*, tbl_students.* 
        FROM tbl_event_attendance 
        JOIN tbl_students ON tbl_event_attendance.student_id = tbl_students.student_id 
        JOIN tbl_event_sched ON tbl_event_attendance.schedule_id = tbl_event_sched.schedule_id 
        WHERE tbl_event_sched.event_id = :eventId AND tbl_event_sched.schedule_id = :sched_id";

    $stmtAttendance = $conn->prepare($queryAttendance);
    $stmtAttendance->bindParam(':eventId', $eventId);
    $stmtAttendance->bindParam(':sched_id', $schedId);
    $stmtAttendance->execute();

    $queryEventInfo = "SELECT *
    FROM tbl_events, tbl_event_sched
    WHERE tbl_events.event_id = :eventId and tbl_event_sched.event_id = :eventId and tbl_event_sched.schedule_id= :sched_id";

    $stmtEventInfo = $conn->prepare($queryEventInfo);
    $stmtEventInfo->bindParam(':eventId', $eventId);
    $stmtEventInfo->bindParam(':sched_id', $schedId);
    $stmtEventInfo->execute();

    if($rowEventInfo = $stmtEventInfo->fetch()){
        $_SESSION['event_date'] = intval(date("j", strtotime($rowEventInfo['event_date'])));
        $_SESSION['schedule_id'] = intval($rowEventInfo['schedule_id']);
        $_SESSION['timeout'] = date('H:i:s',strtotime($rowEventInfo['timeout']));
        $_SESSION['timein'] = date('H:i:s',strtotime($rowEventInfo['timein']));
        $timein = date('H:i:s',strtotime($rowEventInfo['timein']));
        $timeout =  date('H:i:s',strtotime($rowEventInfo['timeout']));
    }
    // while ($rowEventInfo = $stmtEventInfo->fetch()) {     
    //     $_SESSION['event_date'] = intval(date("j", strtotime($rowEventInfo['event_date'])));
    //     $_SESSION['schedule_id'] = intval($rowEventInfo['schedule_id']);
    //     $_SESSION['timeout'] = date('H:i:s', strtotime($rowEventInfo['timeout']));
    //     $_SESSION['timein'] = date('H:i:s', strtotime($rowEventInfo['timein']));
        
    //     $timein = date('H:i:s', strtotime($rowEventInfo['timein']));
    //     $timeout = date('H:i:s', strtotime($rowEventInfo['timeout']));
    // }
} else {
    header('Location: attendance-events.php');
    exit();
}

try{
    if(isset($_POST['insertAtt']) && !empty($_POST['studentId']) && ($date == $_SESSION['event_date'])){
        $scheduleId = htmlspecialchars(strip_tags($_SESSION['schedule_id']));
        $studentID = htmlspecialchars(strip_tags($_POST['studentId']));
        $clockInOnly = "TimeInOnly";
        $clockOut = "Present";

        $checkStudent = "SELECT * FROM tbl_students WHERE student_id = :student_id";
        $studentRecord = $conn->prepare($checkStudent);
        $studentRecord->bindValue(':student_id', $studentID, PDO::PARAM_INT);
        $studentRecord->execute();

        if ($studentRecord->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'Student ID does not exist']);
            exit();
        }
    
        $checkRecord = "SELECT * 
        FROM tbl_event_attendance, tbl_students 
        WHERE tbl_event_attendance.student_id = :student_id and tbl_event_attendance.schedule_id = :scheduleId";             
        
        $eventRecord = $conn->prepare($checkRecord);
        $eventRecord->bindValue(':scheduleId', $scheduleId, PDO::PARAM_INT);
        $eventRecord->bindValue(':student_id', $studentID, PDO::PARAM_INT);
        $eventRecord->execute();
    
        if($eventRecord->rowCount() > 0) {
            $record = $eventRecord->fetch();
            $recordID = $record['event_att_id'];
    
            $recordVerify = "SELECT * FROM tbl_event_attendance WHERE event_att_id = :event_att_id";
            $recordVerify = $conn->prepare($recordVerify);
            $recordVerify->bindValue(":event_att_id", $recordID, PDO::PARAM_INT);
            $recordVerify->execute();
    
            if($recordVerify->rowCount() > 0){
                $rowVerify = $recordVerify->fetch();
    
                try {
                    if (empty($rowVerify['timeout']) && ($today >= $_SESSION['timeout'])) {
                        $queryUPDATE = "UPDATE tbl_event_attendance SET time_out = CURTIME(), att_status = ? WHERE event_att_id = ?";
                        $stmtUPDATE = $conn->prepare($queryUPDATE);
                        $stmtUPDATE->bindValue(1, $clockOut, PDO::PARAM_STR);
                        $stmtUPDATE->bindValue(2, $recordID, PDO::PARAM_INT);
                        $stmtUPDATE->execute();
                
                        echo json_encode(['success' => true, 'message' => 'Clock out successfully']);
                        exit();
                    }
                } catch (PDOException $e) {
                    // Handle the exception here
                    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                    exit();
                }
                          
            }
        }else{
            if($today <= $_SESSION['timein'] ){
      
                $queryAttendance = "INSERT INTO tbl_event_attendance (schedule_id, student_id, att_status, time_in) VALUES (?, ?, ?, CURTIME())";
                $stmtAttendance = $conn->prepare($queryAttendance);
                $stmtAttendance->bindValue(1, $scheduleId, PDO::PARAM_INT);
                $stmtAttendance->bindValue(2, $studentID, PDO::PARAM_INT);
                $stmtAttendance->bindValue(3, $clockInOnly, PDO::PARAM_STR);
                $stmtAttendance->execute();

                echo json_encode(['success' => true, 'message' => 'Clock in successfully']);
                exit();
        
            }else if($today <= $_SESSION['timeout']){
                $scheduledTimeIn = new DateTime($_SESSION['timein']);
                $currentTime = new DateTime(date('H:i:s'));
                $interval = $currentTime->diff($scheduledTimeIn);
                $timeLate = $interval->format('%H:%I:%S');

                $queryAttendance = "INSERT INTO tbl_event_attendance (schedule_id, student_id, att_status, time_late, time_in) VALUES (?, ?, ?, ?, CURTIME())";
                $stmtAttendance = $conn->prepare($queryAttendance);
                $stmtAttendance->bindValue(1, $scheduleId, PDO::PARAM_INT);
                $stmtAttendance->bindValue(2, $studentID, PDO::PARAM_INT);
                $stmtAttendance->bindValue(3, $clockInOnly, PDO::PARAM_STR);
                $stmtAttendance->bindValue(4, $timeLate, PDO::PARAM_STR);
                $stmtAttendance->execute();

                echo json_encode(['success' => true, 'message' => 'Late']);
                exit();
            }
        }
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit(); 
}

if(!empty($_SESSION['schedule_id'])){
    $scheduleId = htmlspecialchars(strip_tags($_SESSION['schedule_id']));

    $attendanceSelect = "SELECT * FROM tbl_event_attendance, tbl_students WHERE tbl_students.student_id = tbl_event_attendance.student_id and schedule_id = :schedule_id";
    $attendanceSelect = $conn->prepare($attendanceSelect);
    $attendanceSelect->bindValue(':schedule_id', $scheduleId, PDO::PARAM_INT);
    $attendanceSelect->execute();
    $totalRowCount = $attendanceSelect->rowCount();

}else{
    $scheduleId = null;

    $attendanceSelect = "SELECT * FROM tbl_event_attendance, tbl_students WHERE tbl_students.student_id = tbl_event_attendance.student_id and schedule_id = :schedule_id";
    $attendanceSelect = $conn->prepare($attendanceSelect);
    $attendanceSelect->bindValue(':schedule_id', $scheduleId, PDO::PARAM_INT);
    $attendanceSelect->execute();
    $totalRowCount = $attendanceSelect->rowCount();
}

if (isset($_SESSION['eventId']) && isset($_SESSION['sched_id'])) {
    $schedID = $_SESSION['sched_id'];
    $eventID = $_SESSION['eventId'];

    $querySession = "SELECT * FROM tbl_event_sched, tbl_events WHERE tbl_event_sched.schedule_id = :sched_id AND tbl_events.event_id = :event_id AND tbl_event_sched.event_id = :event_id";
    $stmtSession = $conn->prepare($querySession);
    $stmtSession->bindValue(':sched_id', $schedID);
    $stmtSession->bindValue(':event_id', $eventID);
    $stmtSession->execute();

    if ($stmtSession->rowCount() == 0) {
        unset($_SESSION['sched_id']);
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="../assets/css/take-attendance.css?version=<?php echo time(); ?>">
    <script src="../assets/js/time.js" defer></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js" defer></script>
</head>

<body>
    <header>
        <div class="title">
            <h1 class="title">Mark Attendance</h1>
            <div class="switch">
                  <form>
                    <span>Scanner</span>
                    <label class="toggle-switch">
                        <input type="checkbox" id="scannerToggle">
                        <span class="slider"></span>
                    </label>
                  </form>
            </div>
            <a href="" id="addTime" class="addTime">Add Time</a>
            <a href="" id="addExcuse" class="addExcuse">Excuse</a>
        </div>

        <section>       
            <div class="event">
                <div>
                    <h2 id="time">12:00 am</h2>
                    <p id="date">January 1, 2024 </p>
                </div>
            </div>

            <div class="attendance-stats">
                <div class="stat-item">
                    <span class="stat-label">Total:</span>
                    <span class="stat-value blue"><?= $totalRowCount ?></span>
                </div>
            </div>
        </section>
    </header>

    <main>
        <div class="container">
            <div class="filters">
                <div>
                    <div class="item-1 item-info">
                        <img src="../icons/info.png" alt="info" id="info-toggle">
                        <div id="info-detail">
                        <?php
                            if ($_SESSION['eventId']) {
                                $e = $_SESSION['eventId'];
                                if (isset($_SESSION['sched_id'])) {
                                    $id = $_SESSION['sched_id'];

                                    $queryE = "SELECT *
                                            FROM tbl_events, tbl_event_sched
                                            WHERE tbl_events.event_id = :e and tbl_event_sched.event_id = :e and tbl_event_sched.schedule_id = :id";

                                    $stmtE = $conn->prepare($queryE);
                                    $stmtE->bindParam(':e', $e);
                                    $stmtE->bindParam(':id', $id);
                                    $stmtE->execute();

                                    if ($rowEInfo = $stmtE->fetch()) {
                                        $timeIn = strtotime($rowEInfo['timein']);
                                        $timeOut = strtotime($rowEInfo['timeout']);
                                        $totalTimeSeconds = $timeOut - $timeIn;
                                        $totalTimeFormatted = sprintf('%02d:%02d:%02d', ($totalTimeSeconds / 3600), ($totalTimeSeconds / 60 % 60), $totalTimeSeconds % 60);

                                        echo "
                                            <p>Name: <b>" . $rowEInfo['event_name'] . "</b></p>
                                            <p>Description: <b>" . $rowEInfo['event_description'] . "</b></p>
                                            <p>Venue: <b>" . $rowEInfo['event_venue'] . "</b></p>
                                            <p>Date: <b>" . date('M. d, Y, l', strtotime($rowEInfo['event_date'])) . "</b></p>
                                            <p>Venue: <b>" . $rowEInfo['phases'] . "</b></p>
                                            <p>Fines: <b>" . $rowEInfo['fines'] . "</b></p>
                                            <p>Time In: <b>" . date('h:i A', $timeIn) . "</b></p>
                                            <p>Time Out: <b>" . date('h:i A', $timeOut) . "</b></p>
                                            <p>Total Time: <b>" . $totalTimeFormatted . "</b></p>
                                        ";
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div>
                        <form action="" Method="POST" id="sched_event">
                        <select name="sched_id" id="sched_id">
                            <?php
                                if(!empty($_SESSION['sched_id'])){
                                    $id = $_SESSION['sched_id'];

                                    $queryId = "SELECT phases, schedule_id FROM tbl_event_sched WHERE schedule_id= :sched_id";
                                    $stmtId = $conn->prepare($queryId);
                                    $stmtId->bindParam(':sched_id', $id, PDO::PARAM_INT);
                                    $stmtId->execute();
                                    $rowId = $stmtId->fetch(PDO::FETCH_ASSOC);

                                    echo "<option selected disabled value=''>selected: ".$rowId['phases']."</option>";

                                }else{
                                    echo "<option disabled value=''>-- Select Phase --</option>";
                                }
                                
                                $event_id =  $_SESSION['eventId'];
                                $query = "SELECT phases, schedule_id FROM tbl_event_sched WHERE event_id = :event_id";
                                $stmt = $conn->prepare($query);
                                $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
                                $stmt->execute();

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $phases = explode(',', $row['phases']);
                                    $schedID = explode(',', $row['schedule_id']);
                                    foreach ($phases as $key => $phase) {
                                        echo "<option value='$schedID[$key]'>$phase</option>";
                                    }
                                }
                            ?>
                        </select>
                        </form>
                    </div>
                </div>

                <form action="" method="POST" class="student-input">
                    <input type="number" name="studentId" placeholder="Enter Student ID">
                    <input type="submit" name="insertAtt" value="Submit">
                </form>
            </div>

            <table id="myTable" class="display">           
                <thead>
                    <tr class="table-title">
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Year</th>
                        <?php
                            echo '<th>Time In | <b>'.date("g:i A", strtotime($timein)).'</b></th>';
                            echo '<th>Time Out | <b>'.date("g:i A", strtotime($timeout)).'</b></th>';                
                        ?>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    if($attendanceSelect->rowCount() > 0){
                        while($rowAttendance = $attendanceSelect->fetch()){
                            echo "<tr>
                                <td>".$rowAttendance['student_id']."</td>
                                <td>".$rowAttendance['first_name']. ' ' .$rowAttendance['last_name']."</td>
                                <td>".$rowAttendance['year_level']."</td>
                                <td>".date('g:i A', strtotime($rowAttendance['time_in']))."</td>
                                <td>".date('g:i A', strtotime($rowAttendance['time_out']))."</td>
                                <td>".$rowAttendance['remarks']."</td>
                                </tr>";
                        }
                    }
                ?>
                </tbody>
            </table>
        </div>
    </main>

<div id="successModal" class="modal">
  <div class="modal-content success">
    <p>Attendance marked successfully!</p>
  </div>
</div>

<div id="errorModal" class="modal">
  <div class="modal-content error">
    <p>Error marking attendance!</p>
  </div>
</div>

<div id="addTimeModal" class="modalTime">
  <div class="modal-contentTime">
    <span class="close">&times;</span>
    <h2>Extend Time-in Time</h2>
    <form id="addTimeForm">
      <label for="time_plus">Time:</label>
      <input type="time" id="time_plus" name="time" placeholder="Enter time">
      <input type="hidden" id="userId" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
      <input type="hidden" id="scheduleId" name="schedule_id" value="<?php echo $_SESSION['schedule_id']; ?>">
      <button type="button" id="submitTime">Add Time</button>
    </form>
  </div>
</div>

<div id="excuseModal" class="modalExcuse">
  <div class="modal-contentExcuse">
    <span class="close">&times;</span>
    <h2>Add Student Details</h2>
    <form id="addExcuseForm">
      <label for="student">Student Id:</label>
      <input type="number" id="student" name="student" placeholder="Enter Student ID">
      <label for="stat">Remark:</label>
      <input type="text" id="stat" name="stat" placeholder="Ex: Excuse">
      <input type="hidden" id="userId" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
      <input type="hidden" id="eventId" name="event_id" value="<?php echo $_SESSION['eventId']; ?>">
      <button type="button" id="submitExcuse">Request</button>
    </form>
  </div>
</div>

<script>
        const form = document.getElementById('sched_event');
        const select = document.getElementById('sched_id');

        select.addEventListener('change', function () {
            form.submit();
        });
</script>
<script>
        function focusOnStudentInput() {
            $('.student-input input[name="studentId"]').focus();
        }

        document.addEventListener('DOMContentLoaded', function () {
            var toggleSwitch = document.getElementById('scannerToggle');
            var savedState = localStorage.getItem('toggleState');

            if (savedState) {
            toggleSwitch.checked = savedState === 'true';
            if (toggleSwitch.checked) {
                focusOnStudentInput();
            }
            }

            toggleSwitch.addEventListener('change', function () {
            localStorage.setItem('toggleState', toggleSwitch.checked);
            if (toggleSwitch.checked) {
                focusOnStudentInput();
            }
            });
        });

        

        $(document).ready(function () {        
            var intervalId;

            // Retrieve the stored state from localStorage
            var savedState = localStorage.getItem('toggleState');
            var addTimeModal = $('.modalTime');
            var addTimeLink = $('#addTime');
            var closeBtn = $('.close');

            //Request Student

            var addExcuseModal = $('.modalExcuse');
            var addExcuseLink = $('#addExcuse');
            var closeBtn = $('.close');

            addExcuseLink.click(function (e) {
                e.preventDefault();
                addExcuseModal.show();
            });

            closeBtn.click(function () {
                addExcuseModal.hide();
            });

            $(window).click(function (e) {
            if (e.target === addExcuseModal[0]) {
                addExcuseModal.hide();
            }
            });

            $(document).keydown(function (e) {
            if (e.key === 'Escape') {
                addExcuseModal.hide();
            }
            });

            $('#submitExcuse').click(function () {
                var userId = $('#userId').val();
                var eventId = $('#eventId').val();
                var studentID = $('#student').val();
                var statSt = $('#stat').val();

            // AJAX request to insert data into tbl_request
            $.ajax({
                type: 'POST',
                url: 'insert_request_absence.php', // Change this to the PHP file that handles the insertion
                data: {
                user_id: userId,
                event_id: eventId,
                studentid: studentID,
                stat: statSt
                },
                dataType: 'json',
                success: function (response) {
                if (response.success) {
                    // Insertion successful, you can update the UI or perform additional actions
                    console.log('Data inserted successfully');
                } else {
                    // Handle insertion failure
                    console.error('Error inserting data:', response.message);
                }

                // Close the modal after submission
                addExcuseModal.hide();
                },
                error: function (xhr, textStatus, errorThrown) {
                // Handle AJAX error
                console.error('AJAX request failed:', textStatus, errorThrown);
                }
            });
            });


            //Request

            addTimeLink.click(function (e) {
                e.preventDefault();
                addTimeModal.show();
            });

            closeBtn.click(function () {
                addTimeModal.hide();
            });

            $(window).click(function (e) {
            if (e.target === addTimeModal[0]) {
                addTimeModal.hide();
            }
            });

            $(document).keydown(function (e) {
            if (e.key === 'Escape') {
                addTimeModal.hide();
            }
            });

            $('#submitTime').click(function () {
            var userId = $('#userId').val();
            var scheduleId = $('#scheduleId').val();
            var time = $('#time_plus').val();

            // AJAX request to insert data into tbl_request
            $.ajax({
                type: 'POST',
                url: 'insert_request_time.php', // Change this to the PHP file that handles the insertion
                data: {
                user_id: userId,
                schedule_id: scheduleId,
                time: time
                },
                dataType: 'json',
                success: function (response) {
                if (response.success) {
                    // Insertion successful, you can update the UI or perform additional actions
                    console.log('Data inserted successfully');
                } else {
                    // Handle insertion failure
                    console.error('Error inserting data:', response.message);
                }

                // Close the modal after submission
                addTimeModal.hide();
                },
                error: function (xhr, textStatus, errorThrown) {
                // Handle AJAX error
                console.error('AJAX request failed:', textStatus, errorThrown);
                }
            });
            });

            if (savedState === 'true') {
            $('#scannerToggle').prop('checked', true);
            // Start the interval if the switch is initially checked
            intervalId = setInterval(function () {
                var studentId = $('.student-input input[type="number"]').val();
                if (studentId !== "") {
                $('.student-input').submit();
                }
            }, 1000);
            }

            $('#scannerToggle').change(function () {
            if ($(this).is(':checked')) {
                intervalId = setInterval(function () {
                var studentId = $('.student-input input[type="number"]').val();
                if (studentId !== "") {
                    $('.student-input').submit();
                }
                    focusOnStudentInput(); // Focus on the student input field when the switch is on
                    }, 1000);
                } else {
                    clearInterval(intervalId);
                }
            });

            $('.student-input').submit(function (e) {
                e.preventDefault();

                var studentId = $(this).find('input[name="studentId"]').val();
                console.log('Student ID:', studentId);

                try {
                    var ajaxRequest = $.ajax({
                        type: 'POST',
                        url: 'take-attevent.php',
                        data: {
                            insertAtt: true,
                            studentId: studentId
                        },
                        dataType: 'json', // Expecting JSON response
                    });

                    ajaxRequest.done(function (response) {
                        var modalId;
                        var modalMessage;

                        if (response.success) {
                            // Show success modal
                            modalId = 'successModal';
                            modalMessage = response.message;
                            $('#' + modalId + ' .modal-content p').text(modalMessage);
                            showModal(modalId);

                            setTimeout(function () {
                                location.reload();
                            }, 1000);           
                        } else {
                            // Show error modal
                            modalId = 'errorModal';
                            modalMessage = response.message;
                            $('#' + modalId + ' .modal-content p').text(modalMessage);
                            showModal(modalId);
                        }     

                        // Hide modal after 700 milliseconds (0.7 seconds)
                        setTimeout(function () {
                            hideModal();
                        }, 700);

                        $('.student-input input[name="studentId"]').val('');
                    });

                    ajaxRequest.fail(function (jqXHR, textStatus, errorThrown) {
                        // Show error modal
                        console.error('AJAX request failed');
                        console.error('textStatus:', textStatus);
                        console.error('errorThrown:', errorThrown);
                        console.error('Server response:', jqXHR.responseText);
                        showModal('errorModal');

                        // Hide error modal after 700 milliseconds (0.7 seconds)
                        setTimeout(function () {
                            hideModal();
                        }, 700);
                    });
                } catch (error) {
                    console.error('An error occurred:', error);
                }
            });

            function showModal(modalId) {
                $('#' + modalId).show();
            }

            function hideModal() {
                $('.modal').hide();
            }

            //datatable
            $('#myTable').DataTable({
                "pageLength": 5,
                "lengthMenu": [5, 10, 25, 50],
                "searching": true,
            });

});
</script>
</body>
</html>