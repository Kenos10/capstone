<?php 

session_set_cookie_params(1200);
session_start();
session_regenerate_id(true);

require_once('../config/constant.php');
require_once(UPDATE_STUDENT);
require_once(UPDATE_REQUEST_ABSENCE);

$schoolYear =  $_SESSION['sch_year'];

if (!$_SESSION['logged_in'] || !$_SESSION['user_id']) {
    header('Location: '.LOGIN_URL);
    die();
}

if (isset($_GET['id'])) {
    $student_id = htmlspecialchars(strip_tags($_GET['id']));
    $_SESSION['id_student'] = $student_id;
    $status = 'TimeInOnly';
  
    $query = 'SELECT *
    FROM tbl_students
    WHERE (student_id = :student_id OR first_name = :student_id OR last_name = :student_id);
    ';
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':student_id', $student_id);
    $stmt->execute();

    // Check if the "All Time" button is clicked
    $allTime = isset($_GET['all_time']) && $_GET['all_time'] == 1;

    // Modify the WHERE clause based on the "All Time" button
    $whereClause = $allTime ? '' : 'AND (
                            YEAR(tbl_events.event_date) < YEAR(CURDATE())
                            OR (
                                YEAR(tbl_events.event_date) = YEAR(CURDATE())
                                AND DATE_FORMAT(tbl_events.event_date, "%m%d") <= DATE_FORMAT(CURDATE(), "%m%d")
                            )
                        )';

    $queryInfo = "SELECT
                    tbl_events.school_year_id,
                    tbl_events.event_id,
                    tbl_events.event_name,
                    tbl_events.event_date,
                    tbl_event_sched.phases,
                    COALESCE(tbl_event_attendance.att_status, 'Absent') AS Attendance_Status,
                    CASE
                        WHEN tbl_event_attendance.att_status IS NOT NULL AND tbl_event_attendance.att_status <> :stat AND tbl_event_attendance.time_late = '00:00:00' THEN '00:00:00'
                        WHEN tbl_event_attendance.time_late > '00:00:00' AND tbl_event_attendance.att_status <> :stat THEN tbl_event_attendance.time_late
                        ELSE COALESCE(
                            SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, tbl_event_sched.timein, tbl_event_sched.timeout)))
                        )
                    END AS time_late,
                    COALESCE(tbl_event_attendance.remarks, '') AS remarks,
                    CASE
                        WHEN tbl_event_attendance.att_status IS NULL OR tbl_event_attendance.att_status = :stat THEN tbl_events.fines
                        ELSE '0.00'  -- Exclude fines when att_status has a value
                    END AS Fines
                FROM
                    tbl_events
                LEFT JOIN
                    tbl_event_sched ON tbl_events.event_id = tbl_event_sched.event_id
                LEFT JOIN
                    tbl_event_attendance ON tbl_event_sched.schedule_id = tbl_event_attendance.schedule_id
                        AND tbl_event_attendance.student_id = :studentID
                WHERE
                    tbl_events.school_year_id = :schoolYear
                    $whereClause
                GROUP BY
                    tbl_events.event_id,
                    tbl_events.school_year_id,
                    tbl_events.event_name,
                    tbl_events.event_date,
                    tbl_event_sched.phases,
                    tbl_event_attendance.att_status,
                    tbl_event_attendance.time_late,
                    tbl_event_attendance.remarks,
                    tbl_events.fines
                ORDER BY
                    tbl_events.event_date ASC;
                ";

    $stmtInfo = $conn->prepare($queryInfo);
    $stmtInfo->bindValue(':studentID', $student_id);
    $stmtInfo->bindValue(':schoolYear', $schoolYear);
    $stmtInfo->bindValue(':stat', $status);
    $stmtInfo->execute();
  
    if ($stmt->rowCount() == 0) {
      $previous_page_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : INDEX;
      header("Location: $previous_page_url");
      die();
    }
  } else {
    $previous_page_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : INDEX;
    header("Location: $previous_page_url");
    die();
  }
  

$conn = null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Info</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        *{
            --colorGreen: #36c574;
        }
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        img{
            width: 1.5rem;
            height: 1.5rem;
        }
        #myModalEdit {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-contentEdit {
            position: fixed;
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px 50px;
            width: 30%;
            border-radius: 1rem;
            top: -5%;
            left: 35%;
        }
        .closeEdit, .closeEditEvent {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .closeEditEvent:hover,
        .closeEditEvent:focus,
        .closeEdit:hover,
        .closeEdit:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
        #myModalEdit h3 {
            text-align: center;
        }
        .modalEdit-btn {
            background-color: #17a2b8; /* Use the custom variable or replace with the color you want */
            color: #fff;
            padding: 10px 20px;
            border: none;
            width: 95%;
            border-radius: 5px;
            cursor: pointer;
            display: block;
        }
        .modalEdit-btn:hover{
            background-color: #007BFF;
        }
        .form-group{
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        label{
            display: block;
            font-size: 14px;
        }
        h3{
            text-align: left;
            width: fit-content;
            font-size: 20px;
        }
        .contentEdit-title{
            position: relative;
            margin-bottom: 1.5rem;
        }
        .contentEdit-title:after{
            position: fixed;
            content: '';
            border-bottom: 3px solid var(--colorGreen);
            height: 2px;
            width: 3%;
        }
        legend{
            text-align: left;
            font-size: 14px;
            color: grey;
        }
        input, select{
            width: 95%;
            padding: .5rem;
        }
        /***/
        #myModalEditEvent{
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="javascript:history.back()"> ← Back</a>
        <div class="d-flex justify-content-start align-items-center m-4" style="gap: 1rem;">
            <h5>Student Information</h5>
            <a href="javascript:void(0);" id="allTimeButton" class="btn-sm btn-secondary">All Time</a>
        </div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Year Level</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gmail</th>
                    <th>Status</th>
                    <?php if($_SESSION['role'] == ACCOUNT_TYPE_A){?>
                        <th>Action</th>
                    <?php }?>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $stmt->fetch()) {?>
                <tr>
                    <td><?php echo $row['student_id'];?></td>
                    <td><?php echo $row['year_level'];?></td>
                    <td><?php echo $row['first_name'];?></td>
                    <td><?php echo $row['last_name'];?></td>
                    <td><?php echo $row['gmail'];?></td>
                        <?php if($row['status'] == 'Active'){?>
                            <td class="text-success"><?php echo $row['status'];?></td>
                        <?php }else{?>
                            <td class="text-danger"><?php echo $row['status'];?></td>
                        <?php }?>
                        <?php if($_SESSION['role'] == ACCOUNT_TYPE_A){?>
                            <td>
                                <a class="edit" data-id="<?php echo $row['student_id']; ?>" href="#">
                                    <img class="table-icon" src="../icons/edit.png" alt="edit">
                                </a>
                            </td>
                        <?php }?>
                </tr>
                <?php
                    echo $stmt->rowCount() ? "" : "<p>No results found</p>";
                }?>
            </tbody>
        </table>
        <hr>
        <table class="table table-bordered table-hover text-capitalize">
            <thead class="text-success">
                <tr class=table-success>
                    <th style="display: none;"></th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Phase</th>
                    <th>Attendance Status</th>
                    <th>Total Hours</th>
                    <th>Fines</th>
                    <th>Other Remarks</th>
                    <?php if($_SESSION['role'] == ACCOUNT_TYPE_A){?>
                        <th>Action</th>
                    <?php }?>
                </tr>
            </thead>
            <tbody>
            <?php
                $totalFines = 0;
                $totalTimeLate = 0;

                while ($row = $stmtInfo->fetch()) {
            ?>
                    <tr>
                        <td style="display: none;"><?php echo $row['event_id']; ?></td>
                        <td><?php echo $row['event_name']; ?></td>
                        <td><?php echo date('M. j, Y', strtotime($row['event_date'])); ?></td>
                        <td><?php echo $row['phases']; ?></td>
                        <td><?php echo $row['Attendance_Status']; ?></td>
                        <td><?php echo date('H:i:s', strtotime($row['time_late'])); ?></td>
                        <td><?php echo '₱' . $row['Fines']; ?></td>
                        <td><?php echo $row['remarks']; ?></td>
                        <?php if($_SESSION['role'] == ACCOUNT_TYPE_A){?>
                            <td>
                                <a class="editEvent" data-id="<?php echo $row['event_id']; ?>" href="#">
                                    <img class="table-icon" src="../icons/edit.png" alt="edit">
                                </a>
                            </td>
                        <?php }?>
                    </tr>
                    <?php
                        $totalFines += floatval($row['Fines']);

                        // Calculate total time late
                        if ($row['time_late'] !== '00:00:00') {
                            $timeLateParts = explode(':', $row['time_late']);
                            $totalTimeLate += ($timeLateParts[0] * 3600) + ($timeLateParts[1] * 60) + $timeLateParts[2];
                        } 
                            echo $stmt->rowCount() ? "" : "<p>No results found</p>";
                        }
                    ?>
 
                <tr class="table-secondary font-weight-bold">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?php echo gmdate('H:i:s', $totalTimeLate); ?></td>
                    <td><?php echo '₱' . $totalFines; ?></td>
                    <td></td>
                    <?php if($_SESSION['role'] == ACCOUNT_TYPE_A){?>
                        <td></td>
                    <?php }?>
                </tr>
            </tbody>
        </table>
    </div>
  
    
    <div id="myModalEdit" class="modalEdit">
        <div class="modal-contentEdit">
            <span class="closeEdit">&times;</span>
            <div class="contentEdit-title">
                <h3>Edit Student</h3>
            </div>
            <form id="editForm" action="" method="POST">
            <legend>Student Details</legend>
            <div class="form-group">
                <div>
                    <label for="student_id">Student ID:</label>
                    <input type="number" name="student_id" value="<?php echo $row['student_id']; ?>">
                </div>
                
                <div>
                    <label for="fname">First Name:</label>
                    <input type="text" name="fname" value="<?php echo $row['first_name']; ?>">
                </div>

                <div>
                    <label for="lname">Last Name:</label>
                    <input type="text" name="lname" value="<?php echo $row['last_name']; ?>">
                </div>

                <div>
                    <label for="year">Year:</label>
                    <select name="year" id="year">
                        <option value="" disabled <?php echo !isset($row['year_level']) ? 'selected' : ''; ?>>-- Select Year --</option>
                        <option value="1st">1st</option>
                        <option value="2nd">2nd</option>
                        <option value="3rd">3rd</option>
                        <option value="4th">4th</option>
                    </select>
                </div>

                <div>
                    <label for="status">Status:</label>
                    <select name="status" id="status">
                        <option value="" disabled <?php echo !isset($row['status']) ? 'selected' : ''; ?>>-- Select Status --</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    </div>
                </div>
                <button class="modalEdit-btn" id="myModalEditBtn" name="editStudent">Update</button>
            </div>
            </form>
        </div>
    </div>

    <div id="myModalEditEvent" class="modalEditEvent">
        <div class="modal-contentEdit">
            <span class="closeEditEvent">&times;</span>
            <div class="contentEdit-title">
                <h3>Edit Remarks</h3>
            </div>
            <form id="editEventForm" action="" method="POST">
                <div class="form-group">
                    <div>
                        <label for="remarks">Remarks</label>
                        <input type="text" name="remarks" value="<?php echo $row['remarks']; ?>">
                        <input type="hidden" name="event_id" value="<?php echo $row['event_id']; ?>">
                    </div>
                </div>
                <button class="modalEdit-btn" id="myModalEditEventBtn" name="editEvent">Update</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
        //FORM EDIT STATUS
        document.addEventListener("DOMContentLoaded", function () {
            var modalEditEvent = document.getElementById("myModalEditEvent");
            var closeEditEvent = document.querySelector(".closeEditEvent");
            var editEventForm = document.getElementById("editEventForm");

            Array.from(document.querySelectorAll(".editEvent")).forEach(function (element) {
                element.addEventListener("click", function (event) {
                    event.preventDefault();

                    // Get the ID of the event to be edited
                    var eventId = this.getAttribute("data-event-id");
                    editEventForm.remarks.value = this.parentElement.parentElement.querySelector("td:nth-child(8)").textContent;
                    editEventForm.event_id.value = this.parentElement.parentElement.querySelector("td:nth-child(1)").textContent;

                    modalEditEvent.style.display = "block";
                });
            });

            closeEditEvent.addEventListener("click", function () {
                modalEditEvent.style.display = "none";
            });

            window.onclick = function (event) {
                if (event.target == modalEditEvent) {
                    modalEditEvent.style.display = "none";
                }
            };
        });
        
        // Form EDIT
        document.addEventListener("DOMContentLoaded", function () {
            var modalEdit = document.getElementById("myModalEdit");
            var closeEdit = document.querySelector(".closeEdit");
            var editForm = document.getElementById("editForm");

            Array.from(document.querySelectorAll(".edit")).forEach(function (element) {
                element.addEventListener("click", function (event) {
                    event.preventDefault();

                    // Get the ID of the record to be edited
                    var id = this.getAttribute("data-id");

                    editForm.student_id.value = id;
                    editForm.fname.value = this.parentElement.parentElement.querySelector("td:nth-child(3)").textContent;
                    editForm.lname.value = this.parentElement.parentElement.querySelector("td:nth-child(4)").textContent;
                    editForm.year.value = this.parentElement.parentElement.querySelector("td:nth-child(2)").textContent;
                    editForm.status.value = this.parentElement.parentElement.querySelector("td:nth-child(6)").textContent;

                    // Show the modal
                    modalEdit.style.display = "block";
                });
            });

            closeEdit.addEventListener("click", function () {
                modalEdit.style.display = "none";
            });

            window.onclick = function (event) {
                if (event.target == modalEdit) {
                    modalEdit.style.display = "none";
                }
            };
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        document.addEventListener("DOMContentLoaded", function() {
        // Get the current URL
        var currentUrl = window.location.href;

        // Get the "All Time" button element
        var allTimeButton = document.getElementById("allTimeButton");

        // Check if the "all_time" parameter is already present in the URL
        var isAllTime = currentUrl.includes("all_time=1");

        // Update the button text based on the current state
        allTimeButton.innerText = isAllTime ? "Current" : "All Time";

        // Add a click event listener to toggle the "all_time" parameter
        allTimeButton.addEventListener("click", function() {
            // Toggle the "all_time" parameter
            isAllTime = !isAllTime;

            // Update the button text based on the new state
            allTimeButton.innerText = isAllTime ? "Current" : "All Time";

            // Update the URL by appending or removing the "all_time" parameter
            var newUrl = isAllTime ? currentUrl + "&all_time=1" : currentUrl.replace("&all_time=1", "").replace("?all_time=1", "");
            
            // Navigate to the new URL
            window.location.href = newUrl;
        });
    });
    </script>
</body>
</html>

