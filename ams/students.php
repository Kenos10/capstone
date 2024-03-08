<?php
include('../includes/dashboard.php');

$filesToInclude = [STUDENTS, INSERT_STUDENT, DELETE_STUDENT, UPDATE_STUDENT];
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
    <link rel="stylesheet" href="../assets/css/students.css?version=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/modalEdit.css?version=<?php echo time(); ?>">
    <script src="../assets/js/modal.js" defer></script>
    <title>Settings | Students</title>
</head>
<body>
    <main class="home">
        <div class="title-dashboard">
            <div>
                <span><img src="../icons/dashboard (2).png" alt="dashboard"></span>
                <h2>Students</h2>
                <button id="myBtn">Add New</button>
            </div>
            <div class="breadcrumb">
                <p>Home</p>
                <p>Settings</p>
                <p class="active-page">Students</p>
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
                            <th>Year</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch()){?>
                            <tr>
                                <td><?php echo $row['student_id'];?></td>
                                <td><?php echo $row['first_name'];?></td>
                                <td><?php echo $row['last_name'];?></td>
                                <td><?php echo $row['year_level'];?></td>
                                <td><?php echo $row['status'];?></td>
                                <td>
                                    <a href="../includes/search.php?id=<?php echo $row['student_id'];?>">
                                        <img class="table-icon" src="../icons/view.png" alt="View">
                                    </a>
                                    <a class="edit" data-id="<?php echo $row['student_id']; ?>" href="#">
                                        <img class="table-icon" src="../icons/edit.png" alt="Edit">
                                    </a>
                                    <a class="delete" href="<?php echo $_SERVER['PHP_SELF']; ?>?deleteid=<?php echo $row['student_id']; ?>">
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
            <h3>Edit Student</h3>
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
                <input type="submit" name="editStudent" value="Update">
            </div>
            </form>
        </div>
    </div>

    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-title">
                <h3>Student Registration</h3>
                <button id="add-student">Add Row</button>
                <p id="row-count">Row(s): 1</p>
            </div>
            <form action="" method="POST">
                <table id="student" class="form" rules="none">
                    <tr>
                        <th>Student ID</th>
                        <th>Year Level</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                    </tr>
                    <tr id="form-inputs">
                        <td><input type="number" name="schoolID[]" required></td>
                        <td>
                            <select name="yearStudent[]" id="" required>
                                <option>-- Year --</option>
                                <option value="1st">1st</option>
                                <option value="2nd">2nd</option>
                                <option value="3rd">3rd</option>
                                <option value="4th">4th</option>
                            </select>
                        </td>
                        <td><input type="text" name="firstName[]" required></td>
                        <td><input type="text" name="lastName[]" required></td>
                    </tr>
                </table>
                <input type="submit" name="addStudent" value="Add Student">
            </form>
        </div>
    </div>
<script>
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
                editForm.fname.value = this.parentElement.parentElement.querySelector("td:nth-child(2)").textContent;
                editForm.lname.value = this.parentElement.parentElement.querySelector("td:nth-child(3)").textContent;
                editForm.year.value = this.parentElement.parentElement.querySelector("td:nth-child(4)").textContent;
                editForm.status.value = this.parentElement.parentElement.querySelector("td:nth-child(5)").textContent;

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

    let count = 1;
    document.getElementById('add-student').addEventListener('click', function() {
        var rowCount = document.getElementById('row-count');
        var originalTr = document.getElementById('form-inputs');
        var newTr = document.createElement('tr');
        newTr.innerHTML = originalTr.innerHTML + '<td><button class="remove-button">Remove</button></td>';
        var tbody = document.querySelector('#student tbody');

        tbody.appendChild(newTr);
        ++count;
        rowCount.innerHTML = 'Row(s): ' + count;
        newTr.querySelector('.remove-button').addEventListener('click', function() {
            newTr.remove();
            --count;
            rowCount.innerHTML = 'Row(s): ' + count;
        });
    });

    var deleteLinks = document.getElementsByClassName("delete");
    for (var i = 0; i < deleteLinks.length; i++) {
        deleteLinks[i].addEventListener("click", function(event){
            if(!confirm("Are you sure you want to delete this student?")){
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