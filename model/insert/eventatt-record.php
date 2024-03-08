<?php
require_once('../../config/constant.php');
$date = intval(date('j'));
$today = date('H:i:s');

try{
    if(isset($_POST['insertAtt']) && !empty($_POST['studentID']) && ($date == $_SESSION['event_date'])){
        $scheduleId = htmlspecialchars(strip_tags($_SESSION['schedule_id']));
        $studentID = htmlspecialchars(strip_tags($_POST['studentId']));
    
        $checkRecord = "SELECT * 
        FROM tbl_event_attendance, tbl_students
        WHERE tbl_event_attendance.student_id = :student_id and tbl_event_sched.schedule_id = :scheduleId"
        ;
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
    
                if(empty($rowVerify['morn_timeout']) && ($today >= $_SESSION['morn_timeout'])){
                    $queryUPDATE = "UPDATE tbl_event_attendance SET morn_timeout = CURTIME() WHERE event_att_id = ?";
                    $stmtUPDATE = $conn->prepare($queryUPDATE);
                    $stmtUPDATE->bindValue(1, $recordID, PDO::PARAM_INT);
                    $stmtUPDATE->execute();
                }
                if(empty($rowVerify['morn_timein']) && $today <= $_SESSION['morn_timein']){
                    $queryAttendance = "INSERT INTO tbl_event_attendance (schedule_id, student_id, morn_timein) VALUES (?, ?, CURTIME())";
                    $stmtAttendance = $conn->prepare($queryAttendance);
                    $stmtAttendance->bindValue(1, $scheduleId, PDO::PARAM_INT);
                    $stmtAttendance->bindValue(2, $studentID, PDO::PARAM_INT);
                    $stmtAttendance->execute();
                }            
            }
        }else{
            if($today <= $_SESSION['morn_timein'] ){
      
                $queryAttendance = "INSERT INTO tbl_event_attendance (schedule_id, student_id, morn_timein) VALUES (?, ?, CURTIME())";
                $stmtAttendance = $conn->prepare($queryAttendance);
                $stmtAttendance->bindValue(1, $scheduleId, PDO::PARAM_INT);
                $stmtAttendance->bindValue(2, $studentID, PDO::PARAM_INT);
                $stmtAttendance->execute();
        
            }else if($today >= $_SESSION['morn_timeout']){
             
                $queryAttendance = "INSERT INTO tbl_event_attendance (schedule_id, student_id, morn_timeout) VALUES (?, ?, CURTIME())";
                $stmtAttendance = $conn->prepare($queryAttendance);
                $stmtAttendance->bindValue(1, $scheduleId, PDO::PARAM_INT);
                $stmtAttendance->bindValue(2, $studentID, PDO::PARAM_INT); 
                $stmtAttendance->execute();
            }
        }
    }
}catch(PDOException $e){
    // echo '<script>alert("Error: ' . $e->getMessage() . '")</script>';
    echo '<script>alert("Error: Invalid Input or No student record!")</script>';
}

if(!empty($_SESSION['schedule_id'])){
    $scheduleId = htmlspecialchars(strip_tags($_SESSION['schedule_id']));

    $attendanceSelect = "SELECT * FROM tbl_event_attendance, tbl_students WHERE tbl_students.student_id = tbl_event_attendance.student_id and schedule_id = :schedule_id";
    $attendanceSelect = $conn->prepare($attendanceSelect);
    $attendanceSelect->bindValue(':schedule_id', $scheduleId, PDO::PARAM_INT);
    $attendanceSelect->execute();
}else{
    $scheduleId = null;

    $attendanceSelect = "SELECT * FROM tbl_event_attendance, tbl_students WHERE tbl_students.student_id = tbl_event_attendance.student_id and schedule_id = :schedule_id";
    $attendanceSelect = $conn->prepare($attendanceSelect);
    $attendanceSelect->bindValue(':schedule_id', $scheduleId, PDO::PARAM_INT);
    $attendanceSelect->execute();
}
