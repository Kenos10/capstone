<?php
include('../includes/dashboard.php');
require_once(SYEAR);
require_once(DELETE_SYEAR);
require_once(INSERT_SYEAR);

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
    <link rel="stylesheet" href="../assets/css/schoolyear.css?version=<?php echo time(); ?>">
    <script src="../assets/js/modal.js" defer></script>
    <title>Settings | SchoolYear</title>
</head>
<body>
    <main class="home">
        <div class="title-dashboard">
            <div>
                <span><img src="../icons/dashboard (2).png" alt="dashboard"></span>
                <h2>School Year</h2>
                <button id="myBtn">Add New</button>
            </div>
            <div class="breadcrumb">
                <p>Home</p>
                <p>Settings</p>
                <p class="active-page">School Year</p>
            </div>
        </div>
        <section class="home-container">
        <div class="home-card table-card">
              <table id="myTable" class="display">
                <thead>
                    <tr class="table-title">
                        <th>S.Y. start</th>
                        <th>S.Y. end</th>
                        <th>Semester</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmtSyear->fetch()){?>
                        <tr>
                            <td><?php echo $row['school_yearstart'];?></td>
                            <td><?php echo $row['school_yearend'];?></td>
                            <td><?php echo $row['semester'];?></td>
                            <td>
                                <a class="delete" href="<?php echo $_SERVER['PHP_SELF']; ?>?syearid=<?php echo $row['school_year_id']; ?>">
                                    <img class="table-icon" src="../icons/delete.png" alt="Delete">
                                </a>
                            </td>
                        </tr>
                    <?php $conn = null; }?>
                </tbody>
              </table>
            </div>
        </section>
    </main>

    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>School Year</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <div>
                <div class="form-group">
                        <div>
                            <?php
                            echo '<label for="ystart">Year Start</label>
                                <select name="ystart" data-component="date">';
                            $startYear = date('Y');
                            $currentYear = date('Y');
                            for ($year = $startYear; $year <= $currentYear + 3; $year++) {
                                echo '<option value="' . $year . '">' . $year . '</option>';
                            }
                            echo "</select>";
                            ?>
                        </div>
                    </div>
                </div>
                <input type="submit" name="add" value="Submit">
        </form>
        </div>
    </div>
<script>
    var deleteLinks = document.getElementsByClassName("delete");
    for (var i = 0; i < deleteLinks.length; i++) {
        deleteLinks[i].addEventListener("click", function(event){
            if(!confirm("Are you sure you want to delete this SY?")){
                event.preventDefault();
            }
        });
    }

    const currentUrl = window.location.href;
    if (currentUrl.includes('syearid')) {
        const updatedUrl = currentUrl.split('?')[0];
        window.location.href = updatedUrl;
    }
</script>
</body>
</html>