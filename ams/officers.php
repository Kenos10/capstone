<?php
include('../includes/dashboard.php');
$filesToInclude = [OFFICERS, DELETE_OFFICER, INSERT_OFFICER, UPDATE_OFFICER];
foreach ($filesToInclude as $file) {
    require_once($file);
}


if($_SESSION['role'] !== ACCOUNT_TYPE_A){
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
    <link rel="stylesheet" href="../assets/css/officers.css?version=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/modalEdit.css?version=<?php echo time(); ?>">
    <script src="../assets/js/modal.js" defer></script>
    <title>Settings | Officers</title>
</head>
<body>
    <main class="home">
        <div class="title-dashboard">
            <div>
                <span><img src="../icons/dashboard (2).png" alt="dashboard"></span>
                <h2>Officers</h2>
                <button id="myBtn">Add New</button>
            </div>
            <div class="breadcrumb">
                <p>Home</p>
                <p>Settings</p>
                <p class="active-page">Officers</p>
            </div>
        </div>


        <section class="home-container">
            <div class="home-card table-card">
                <table id="myTable" class="display">
                    <thead>
                        <tr class="table-title">
                            <th>Student ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Position</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch()){?>
                            <tr data-id="<?php echo $row['sbo_id'];?>">
                                <td><?php echo $row['student_id'];?></td>
                                <td><?php echo $row['first_name'];?></td>
                                <td><?php echo $row['last_name'];?></td>
                                <td><?php echo $row['position'];?></td>
                                <td>
                                    <a href="../includes/search.php?id=<?php echo $row['student_id'];?>">
                                        <img class="table-icon" src="../icons/view.png" alt="View">
                                    </a>
                                    <a class="edit" id="myBtnEdit<?php echo $row['sbo_id']; ?>" href="<?php echo $_SERVER['PHP_SELF']; ?>?officerEditid=<?php echo $row['sbo_id']; ?>">
                                        <img class="table-icon" src="../icons/edit.png" alt="Edit">
                                    </a>
                                    <a href="">
                                    <a class="delete" href="<?php echo $_SERVER['PHP_SELF']; ?>?sboid=<?php echo $row['sbo_id']; ?>">
                                        <img class="table-icon" src="../icons/delete.png" alt="Delete">
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <div id="myModalEdit" class="modalEdit">
        <div class="modal-contentEdit">
            <span class="closeEdit">&times;</span>
            <h3>Edit Officer</h3>
            <form id="editForm" action="" method="POST">
                <?php if ($stmt->rowCount() > 0) { ?>
                    <?php $row = $stmt->fetch(); ?>
                    <div>
                        <legend>Officer Details</legend>
                        <div class="form-group">
                            <input type="hidden" name="sbo_id" value="<?php echo $row['sbo_id']; ?>">
                            <div>
                                <label for="fname">Name:</label>
                                <input type="text" name="fname" value="<?php echo $row['first_name']; ?>">
                            </div>

                            <div>
                                <label for="lname">Last Name:</label>
                                <input type="text" name="lname" value="<?php echo $row['last_name']; ?>">
                            </div>

                            <div>
                                <label for="position">Position:</label>
                                <input type="text" name="position" value="<?php echo $row['position']; ?>">
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <input type="submit" name="editOfficer" value="Update">
            </form>
        </div>
    </div>

    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Officer Registration</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <div>
                    <legend>Student Details</legend>
                    <div class="form-group">
                        <div class="form-search">
                            <span><img src="../icons/search-interface-symbol.png" alt="search"></span>
                            <label for="search">Search Student</label>
                            <input type="search" name="search" id="searchsbo" placeholder="Search..." autocomplete="off">
                            <div id="resultsbo"></div>
                        </div>

                        <div>
                            <label for="schoolID">Student ID</label>
                            <input type="number" name="schoolID" id="school-id-input" readonly>
                        </div>

                        <div>
                            <label for="fname">Name</label>
                            <input type="text" name="fname" id="fullname-input" readonly>
                        </div>

                        <div>
                            <label name="position">Position</label>
                            <input type="text" name="position" required>
                        </div>

                        <div>
                            <label for="file">Profile Picture</label>
                            <input type="file" name="file" required>
                        </div>
                    </div>
                </div>
                <input type="submit" name="add" value="Submit">
        </form>
        </div>
    </div>

<script>
    //Form
    
    document.addEventListener("DOMContentLoaded", function() {
        var modalEdit = document.getElementById("myModalEdit");
        var closeEdit = document.querySelector(".closeEdit");
        var editForm = document.getElementById("editForm");

        Array.from(document.querySelectorAll(".edit")).forEach(function(element) {
            element.addEventListener("click", function(event) {
                event.preventDefault();

                // Get the ID of the record to be edited
                var id = this.parentElement.parentElement.getAttribute("data-id");

                editForm.sbo_id.value = id;
                editForm.fname.value = this.parentElement.parentElement.querySelector("td:nth-child(2)").textContent;
                editForm.lname.value = this.parentElement.parentElement.querySelector("td:nth-child(3)").textContent;
                editForm.position.value = this.parentElement.parentElement.querySelector("td:nth-child(4)").textContent;

                // Show the modal
                modalEdit.style.display = "block";
            });
        });

        closeEdit.addEventListener("click", function() {
            modalEdit.style.display = "none";
        });

        window.onclick = function(event) {
            if (event.target == modalEdit) {
                modalEdit.style.display = "none";
            }
        };
    });

    //Search

    $(document).ready(function(){ 
            $('#searchsbo').keyup(function(){
                var input = $(this).val();
                if(input !== ""){
                    $.ajax({
                        url: "../model/select/search-student-sbo.php",
                        method: "POST",
                        data: {input:input},
                        success:function(data){
                            $("#resultsbo").html(data);
                            $("#resultsbo").css("display", "block");
                        }
                    })
                }else{
                    $("#resultsbo").css("display", "none");
                }
            });
    });

    $(document).on("click", ".student-row", function() {
        var fullName = $(this).data("full-name");
        var schoolid = $(this).data("student-id");
        $("#resultsbo").css("display", "none");
        $.ajax({
            url: "officers.php",
            method: "GET",
            data: {id:schoolid, fname:fullName},
            success:function(data){
                $("#fullname-input").val(fullName);
                $("#school-id-input").val(schoolid);
            }
        });
    });

    //

    var deleteLinks = document.getElementsByClassName("delete");
    for (var i = 0; i < deleteLinks.length; i++) {
        deleteLinks[i].addEventListener("click", function(event){
            if(!confirm("Are you sure you want to delete this student?")){
                event.preventDefault();
            }
        });
    }

    const currentUrl = window.location.href;
    if (currentUrl.includes('sboid')) {
        const updatedUrl = currentUrl.split('?')[0];
        window.location.href = updatedUrl;
    }
</script>
</body>
</html>