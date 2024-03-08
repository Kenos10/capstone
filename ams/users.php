<?php
include('../includes/dashboard.php');
$filesToInclude = [INSERT_USER, DELETE_USER, UPDATE_USER, UPDATE_REQUEST_ABSENCE, UPDATE_REQUEST_TIME];
foreach ($filesToInclude as $file) {
    require_once($file);
}

if($_SESSION['role'] !== ACCOUNT_TYPE_A && $_SESSION['role'] !== ACCOUNT_TYPE_AM && $_SESSION['role'] !== ACCOUNT_TYPE_EM){
    echo "<script>
            window.location.href = '../logout.php';
        </script>";
}

$queryUsers = "SELECT
    tbl_system_user.*,
    tbl_sbo.*,
    tbl_students.*,
    CONCAT(tbl_students.first_name, ' ', tbl_students.last_name) AS fullname
FROM
    tbl_system_user
JOIN
    tbl_sbo ON tbl_system_user.sbo_id = tbl_sbo.sbo_id
JOIN
    tbl_students ON tbl_sbo.student_id = tbl_students.student_id";


$stmtUsers = $conn->prepare($queryUsers);
$stmtUsers->execute();

$request = "SELECT 
                tbl_request_time.*,
                tbl_events.event_name,
                tbl_system_user.role,
                tbl_event_sched.timein,
                tbl_students.first_name,
                tbl_students.last_name
            FROM 
                tbl_request_time
            JOIN tbl_event_sched ON tbl_request_time.schedule_id = tbl_event_sched.schedule_id
            JOIN tbl_events ON tbl_event_sched.event_id = tbl_events.event_id
            JOIN tbl_system_user ON tbl_request_time.user_id = tbl_system_user.user_id
            JOIN tbl_sbo ON tbl_system_user.sbo_id = tbl_sbo.sbo_id
            JOIN tbl_students ON tbl_sbo.student_id = tbl_students.student_id";
$smthRequest = $conn->prepare($request);
$smthRequest->execute();

$requestSt = "SELECT 
            tbl_request_absence.*,
            tbl_events.event_name,
            tbl_system_user.role,
            tbl_students.first_name,
            tbl_students.last_name
            FROM 
            tbl_request_absence
            JOIN tbl_events ON tbl_request_absence.event_id = tbl_events.event_id
            JOIN tbl_system_user ON tbl_request_absence.user_id = tbl_system_user.user_id
            JOIN tbl_sbo ON tbl_system_user.sbo_id = tbl_sbo.sbo_id
            JOIN tbl_students ON tbl_sbo.student_id = tbl_students.student_id";
$smthRequestSt = $conn->prepare($requestSt);
$smthRequestSt->execute();

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/users.css?version=<?php echo time(); ?>">
    <script src="../assets/js/modal.js?version=1" defer></script>
    <title>Settings | User</title>
</head>
<body>
<main class="home">
    <section class="home-container">
        <ul class="tabs">
            <li id="profile-tab" class="active-tab">Profile</li>
            <?php if($_SESSION['role'] == ACCOUNT_TYPE_A){?>
                <li id="users-tab">Users</li>
                    <?php if($totalCountRequest !== 0){?>
                        <li id="request-tab">Request: <?php echo $totalCountRequest?></li>
                    <?php }?>
            <?php }?>
        </ul>
        <div class="content">
            <div id="profile-content" class="tab-content tab-profile">
                <div class="profile-background">
                    <img  class="profile-background" src="<?php echo $profileImg;?>" alt="background">
                </div>
                <div class="profile-info">
                    <img src="<?php echo $profileImg;?>" alt="Profile">
                    <h2><?php echo $fullName;?></h2>
                </div>
                <div class="profile-detail">
                    <div class="detail01">
                        <h3>
                            INFORMATION
                        </h3>
                    </div>
                    <div class="detail02">
                        <div>
                            <span>Student ID</span>
                            <p><?php echo $studentId;?></p>
                        </div>
                        <div>
                            <span>Email</span>
                            <a href="mailto:<?php echo $gmail; ?>"><?php echo $gmail; ?></a>
                        </div>
                        <div>
                            <span>Year</span>
                            <p><?php echo $year.' Year';?></p>
                        </div>
                        <div>
                            <span>Position</span>
                            <p><?php echo $position;?></p>
                        </div>
                        <div>
                            <span>Role</span>
                            <p><?php echo $role;?></p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($_SESSION['role'] == ACCOUNT_TYPE_A){?>
                <div id="users-content" class="tab-content tab-user" style="display: none;">
                    <div class="button-card">
                        <button id="myBtn">Add New</button>
                    </div>

                    <div class="table-card">
                        <table id="" class="display">
                            <thead>
                                <tr class="table-title">
                                    <th>Profile</th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Year</th>
                                    <th>Position</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($rowUsers = $stmtUsers->fetch()){?>

                                <tr class="table-data">
                                    <td><img src="<?php echo '../uploads/'.$rowUsers['profile_img']?>" alt="Picture"></td>
                                    <td><?php echo $rowUsers['student_id']?></td>
                                    <td><?php echo $rowUsers['first_name'].' '.$rowUsers['last_name']?></td>
                                    <td><a href="mailto:<?php echo $rowUsers['gmail']?>"><?php echo $rowUsers['gmail']?></a></td>
                                    <td><?php echo $rowUsers['year_level']?></td>
                                    <td><?php echo $rowUsers['position']?></td>
                                    <td><?php echo $rowUsers['role']?></td>
                                    <td>
        
                                        <a class="edit" id="myBtn<?php echo $rowUsers['user_id']; ?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?userEditid=<?php echo $rowUsers['user_id']; ?>">
                                            <img class="table-icon" src="../icons/edit.png" alt="edit">
                                        </a>
                                        
                                        <a class="delete" href="<?php echo $_SERVER['PHP_SELF']; ?>?userid=<?php echo $rowUsers['user_id']; ?>">
                                            <img class="table-icon" src="../icons/delete.png" alt="delete">
                                        </a>
                                    </td>
                                </tr>
                                
                                <div id="myModal<?php echo $rowUsers['user_id']; ?>" class="modal1">
                                    <!-- Modal content -->
                                    <div class="modal-content1">
                                        <span class="close1 c<?php echo $rowUsers['user_id']; ?>">&times;</span>
                                        <h3>Edit Permission</h3>
                                        <form action="" method="POST">
                                            <div>
                                                <legend>Student Details</legend>
                                                <div class="form-group1">
                                                    <div>
                                                        <label for="name1">Name</label>
                                                        <input type="text" name="name1" placeholder="<?php echo $rowUsers['fullname']; ?>" readonly>
                                                    </div>

                                                    <div>
                                                        <label for="position1">Position</label>
                                                        <input type="text" name="position1" placeholder="<?php echo $rowUsers['position']; ?>" readonly>
                                                    </div>
                                                    
                                                    <div>
                                                        <label for="role1">Role</label>
                                                        <select name="role1" id="" required>
                                                            <?php
                                                            $roles = ['Administrator', 'Attendance Manager', 'Event Manager'];
                                                            
                                                            // Loop through the roles and create options
                                                            foreach ($roles as $role) {
                                                                echo '<option';
                                                                
                                                                // Check if the echoed value matches the current role
                                                                if ($rowUsers['role'] == $role) {
                                                                    echo ' selected';
                                                                }
                                                                
                                                                echo '>' . $role . '</option>';
                                                            }
                                                            ?>
                                                        </select>

                                                    </div>

                                                    <div>
                                                        <input type="number" name="systemId" value="<?php echo $rowUsers['user_id']; ?>" readonly hidden>
                                                    </div>

                                                </div>
                                            </div>
                                            <input type="submit" name="update" value="Update">
                                    </form>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                            var modal1<?php echo $rowUsers['user_id']; ?> = document.getElementById("myModal<?php echo $rowUsers['user_id']; ?>");
                                            var btn1<?php echo $rowUsers['user_id']; ?> = document.getElementById("myBtn<?php echo $rowUsers['user_id']; ?>");
                                            var span1<?php echo $rowUsers['user_id']; ?> = document.getElementsByClassName("c<?php echo $rowUsers['user_id']; ?>")[0];

                                            btn1<?php echo $rowUsers['user_id']; ?>.addEventListener("click", function(event) {
                                                event.preventDefault(); // Prevent the default form submission behavior
                                                modal1<?php echo $rowUsers['user_id']; ?>.style.display = "block";
                                            });

                                            span1<?php echo $rowUsers['user_id']; ?>.addEventListener("click", function() {
                                                modal1<?php echo $rowUsers['user_id']; ?>.style.display = "none";
                                            });

                                            window.onclick = function(event) {
                                                if (event.target == modal1<?php echo $rowUsers['user_id']; ?>) {
                                                    modal1<?php echo $rowUsers['user_id']; ?>.style.display = "none";
                                                }
                                            };
                                    });
                                </script>
                                <?php $conn = null; }?>
                            </tbody>
                        </table>
                    </div>
                    
                </div>

                <?php if($totalCountRequest !== 0){?>
                    <div id="request-content" class="tab-content tab-request" style="display: none;">          
                        <div class="table-card">
                            <?php if ($smthRequest->rowCount() > 0){?>
                                <table id="" class="display">
                                    <caption>Time Extension Request</caption>
                                    <thead>
                                        <tr class="table-title">
                                            <th>Requested By</th>
                                            <th>Event</th>
                                            <th>Time From</th>
                                            <th>Time To</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                            while ($rowRequest = $smthRequest->fetch()) {
                                                $role = htmlspecialchars($rowRequest['role']);
                                                $eventName = htmlspecialchars($rowRequest['event_name']);
                                                $morningTimeIn = htmlspecialchars($rowRequest['timein']);
                                                $time = htmlspecialchars($rowRequest['time']);
                                                $schedId = htmlspecialchars($rowRequest['schedule_id']);
                                                $reqId = htmlspecialchars($rowRequest['request_id']);

                                                echo "<tr>
                                                        <td>" . $role . "</td>
                                                        <td>" . $eventName . "</td>
                                                        <td>" . date("h:i A", strtotime($morningTimeIn)) . "</td>
                                                        <td>" . date("h:i A", strtotime($time)) . "</td>
                                                        <td class='requestAction'>
                                                            <form method='POST' action='" . $_SERVER['PHP_SELF'] . "'>
                                                                <input type='hidden' name='schedId' value='" . $schedId . "'>
                                                                <input type='hidden' name='time' value='" . $time . "'>
                                                                <input type='submit' value='Accept'>
                                                            </form>
                                                            |
                                                            <form method='POST' action='" . $_SERVER['PHP_SELF'] . "'>
                                                                <input type='hidden' name='reqId' value='" . $reqId . "'>
                                                                <input type='submit' name='rejectId' value='Reject'>
                                                            </form>                       
                                                        </td>
                                                    </tr>";
                                            }
                                    ?>
                                </tbody>
                                </table>
                            <?php }?>
                                            
                            <?php  if ($smthRequestSt->rowCount() > 0){?>
                                <table id="" class="display">
                                    <caption>Absence Request</caption>
                                    <thead>
                                        <tr class="table-title">
                                            <th>Requested By</th>
                                            <th>Event</th>
                                            <th>Student ID</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                            while ($rowRequest = $smthRequestSt->fetch()) {
                                                $role = htmlspecialchars($rowRequest['role']);
                                                $eventName = htmlspecialchars($rowRequest['event_name']);
                                                $studentId = htmlspecialchars($rowRequest['student_id']);
                                                $stat = htmlspecialchars($rowRequest['status']);
                                                $eventId = htmlspecialchars($rowRequest['event_id']);
                                                $reqIdst = htmlspecialchars($rowRequest['requestst_id']);

                                                echo "<tr>
                                                        <td>" . $role . "</td>
                                                        <td>" . $eventName . "</td>
                                                        <td>" . $studentId . "</td>
                                                        <td>" . $stat  . "</td>
                                                        <td class='requestAction'>
                                                            <form method='POST' action='" . $_SERVER['PHP_SELF'] . "'>
                                                                <input type='hidden' name='eventIdSt' value='" . $eventId . "'>
                                                                <input type='hidden' name='studentid' value='" . $studentId . "'>
                                                                <input type='hidden' name='stat' value='" . $stat . "'>
                                                                <input type='submit' value='Accept'>
                                                            </form>
                                                            |
                                                            <form method='POST' action='" . $_SERVER['PHP_SELF'] . "'>
                                                                <input type='hidden' name='reqstId' value='" . $reqIdst . "'>
                                                                <input type='submit' name='RejectSt' value='Reject'>
                                                            </form>                       
                                                        </td>
                                                    </tr>";
                                            }
                                    ?>
                                    </tbody>
                                </table>
                            <?php }?>
                        </div>
                    </div>
                <?php }?>
            <?php }?>
        </div>
    </section>
</main>

<?php include('modal/usersModal.php');?>
<script>
    /*Search*/
    $(document).ready(function() {
        $('table.display').DataTable();
    } );

    $(document).ready(function(){ 
            $('#searchuser').keyup(function(){
                var input = $(this).val();
                if(input !== ""){
                    $.ajax({
                        url: "../model/select/search-sbo-user.php",
                        method: "POST",
                        data: {input:input},
                        success:function(data){
                            $("#resultuser").html(data);
                            $("#resultuser").css("display", "block");
                        }
                    })
                }else{
                    $("#resultuser").css("display", "none");
                }
            });
    });

    $(document).on("click", ".student-row", function() {
        var fullName = $(this).data("full-name");
        var position = $(this).data("position");
        var sboID = $(this).data("sboid");
        $("#resultuser").css("display", "none");
        $.ajax({
            url: "users.php",
            method: "GET",
            data: {id:position, fname:fullName, userid:sboID},
            success:function(data){
                $("#fullname-input").val(fullName);
                $("#position-input").val(position);
                $("#sboid-input").val(sboID);
                }
            });
    });

    /***/
    // JavaScript to handle tab switching

    const tabs = document.querySelectorAll(".tabs li");
    const tabContents = document.querySelectorAll(".tab-content");

    tabs.forEach((tab) => {
        tab.addEventListener("click", () => {
            tabs.forEach((t) => t.classList.remove("active-tab"));
            tab.classList.add("active-tab");

            const tabId = tab.id.replace("-tab", "");
            tabContents.forEach((content) => {
                content.style.display = "none";
            });

            const contentToShow = document.getElementById(`${tabId}-content`);
            contentToShow.style.display = "block";
        });
    });

    /***/

    var deleteLinks = document.getElementsByClassName("delete");
    for (var i = 0; i < deleteLinks.length; i++) {
        deleteLinks[i].addEventListener("click", function(event){
            if(!confirm("Are you sure you want to delete this user?")){
                event.preventDefault();
            }
        });
    }

    const currentUrl = window.location.href;
    if (currentUrl.includes('userid')) {
        const updatedUrl = currentUrl.split('?')[0];
        window.location.href = updatedUrl;
    }
</script>
</body>
</html>