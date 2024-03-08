<?php

session_set_cookie_params(1200);
session_start();
session_regenerate_id(true);

require_once('../config/constant.php');
require_once(CURRENT_USER);
require_once(COUNT_REQUEST);
require_once('search-student.php');

unset($_SESSION['id_student']);

if (!$_SESSION['logged_in'] || !$_SESSION['user_id']) {
    header('Location: '.LOGIN_URL);
    die();
}

$activePage = basename($_SERVER['PHP_SELF'], '.php');
if($activePage == 'dashboard'){
  include('../ams/home.php');
}

if (isset($_POST['school_year_id'])) {
  $_SESSION['sch_year'] = $_POST['school_year_id'];
  exit();
}

?>
    <link rel="stylesheet" href="../assets/css/menu.css?version=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <script src="../assets/js/index.js?version=<?php echo time(); ?>" defer></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js" defer></script>

    <header>
        <div class="logo">
          <div>
            <img src="../icons/logo-white.png" alt="logo" id="logo">
            <span id="menu-toggle"><img src="../icons/menu.png" alt="menu" id="menu"></span>
          </div>
        </div>

        <div class="profile">
          <div class="search">
            <div class="search-icon">
              <img src="../icons/search-interface-symbol.png" alt="search">
            </div>
            <span><img src="../icons/search-interface-symbol.png" alt="search"></span>
            <form action="" method="POST" autocomplete="off">
              <input type="search" name="search" id="search" placeholder="Search...">
            </form>
            <div id="result"></div>
          </div>
      
          <div class="card-profile" title="request">
          <div class="sy">
            <select name="school_year_id" id="sy">   
              <?php
              // Get the current year
              $currentYear = date('Y');

              // Set the default query to get all school years
              $queryDefault = "SELECT * FROM tbl_school_year ORDER BY school_yearstart ASC";

              // Set the default value to the current year
              $defaultOption = "<option value='' disabled>Select School Year</option>";
              echo $defaultOption;

              // Initialize the variable to store the school_year_id of the default value
              $defaultSchoolYearId = null;

              // Check if a specific school year is selected (from session)
              if (isset($_SESSION['sch_year'])) {
                  $selectedYear = $_SESSION['sch_year'];

                  $querySelected = "SELECT * FROM tbl_school_year WHERE school_year_id = :id";
                  $stmtSelected = $conn->prepare($querySelected);
                  $stmtSelected->bindParam(':id', $selectedYear);
                  $stmtSelected->execute();

                  $rowSelected = $stmtSelected->fetch(PDO::FETCH_ASSOC);

                  $sySelected = "S.Y. " . $rowSelected['school_yearstart'] . "-" . $rowSelected['school_yearend'] . " " . $rowSelected['semester'];

                  // Display the selected school year as selected
                  echo "<option value='" . $rowSelected['school_year_id'] . "' selected style='font-weight: bold;'>" . $sySelected . "</option>";

                  // Set the default school year ID to the selected year's ID
                  $defaultSchoolYearId = $rowSelected['school_year_id'];
              }

              // Execute the default query and display options for all school years
              $stmtDefault = $conn->prepare($queryDefault);
              $stmtDefault->execute();

              while ($rowDefault = $stmtDefault->fetch(PDO::FETCH_ASSOC)) {
                  $syDefault = "S.Y. " . $rowDefault['school_yearstart'] . "-" . $rowDefault['school_yearend'] . " " . $rowDefault['semester'];

                  // Check if this is the default value (current year)
                  if ($rowDefault['school_year_id'] == $defaultSchoolYearId) {
                      echo "<option value='" . $rowDefault['school_year_id'] . "' selected style='font-weight: bold;'>" . $syDefault . "</option>";
                  } else {
                      echo "<option value='" . $rowDefault['school_year_id'] . "'>" . $syDefault . "</option>";
                  }
              }
              ?>
          </select>

            </div>
            <?php if ($_SESSION['role'] == ACCOUNT_TYPE_A) {?>
              <a href="../ams/users.php" class="request">
                <img src="../icons/request.png" alt="request" >
                <?php
                    if($totalCountRequest !== 0){
                      echo '<p>' . $totalCountRequest . '</p>';
                    }
                ?>
              </a>
            <?php }?>

            <img src="<?php echo $profileImg;?>" alt="profile">

            <div class="menu-profile">
              <img src="../icons/dots.png" alt="menu" id="profile-menu">
                <ul id="toggle-acc">
                  <a href="../ams/users.php">
                    <li>
                        <img src="../icons/user.png" alt="">
                        <p>Profile</p>
                    </li>
                  </a>

                  <a href="../logout.php">
                    <li>
                        <img src="../icons/exit.png" alt="">
                        <p>Logout</p>
                    </li>
                  </a>
                </ul>
            </div>

            <div class="card-user">
              <p><?php echo $fullName;?></p>
              <p><?php echo $role;?></p>
            </div>
          </div>
        </div>
    </header>

    <section class="menu-section" id="container">
        <div class="menu-container">
            <nav class="main-container">
                  <div class="main dashboard">
                    <div class="title">
                      <img src="../icons/dashboard.png" alt="dashboard">
                      <h4>Dashboard</h4>
                    </div>
                    <div class="sub">
                      <a href="../includes/dashboard.php" class="<?= ($activePage == 'dashboard') ? 'active-page':''; ?>">Dashboard</a>
                    </div>
                  </div>
                  
                  <div class="main events">
                    <div class="title">
                      <img src="../icons/calendar.png" alt="calendar">
                      <h4>Events</h4>
                    </div>
                    <div class="sub">
                      <a href="../ams/events.php" class="<?= ($activePage == 'events') ? 'active-page':''; ?>">Manage Events</a>
                    </div>
                  </div>
                  
                  <?php
                    if ($_SESSION['role'] == ACCOUNT_TYPE_A || $_SESSION['role'] == ACCOUNT_TYPE_AM) {
                        echo "
                          <div class='main attendance'>
                            <div class='title'>
                              <img src='../icons/calendar (1).png' alt='attendance'>
                              <h4>Attendance</h4>
                            </div>
                            <div class='sub'>
                              <a href='../ams/attendance-events.php' class='".($activePage == 'attendance-events' ? 'active-page':''). "'>Event Attendance</a>
                            </div>
                          </div>
                      ";
                    }
                  ?>
                  
                  <div class='main report'>
                      <div class='title'>
                          <img src='../icons/file.png' alt='report'>
                          <h4>Reports</h4>
                      </div>
                      <div class='sub'>
                        <a href='../ams/report-events.php' class='<?=($activePage == 'report-events') ? 'active-page' :''; ?>'>Event Report</a>
                    </div>
                  </div>

                  
                  <div class="main setting">
                    <div class="title">
                      <img src="../icons/settings.png" alt="setting">
                      <h4>Settings</h4>
                    </div>
                    <div class="sub">
              
                      <a href="../ams/users.php" class="<?= ($activePage == 'users') ? 'active-page':''; ?>">Users</a>

                    <?php
                      if($_SESSION['role'] == ACCOUNT_TYPE_A){
                        echo"
                        <a href='../ams/students.php' class='" . ($activePage == 'students' ? 'active-page' : '') . "'>Students</a>
                        <a href='../ams/officers.php' class='" . ($activePage == 'officers' ? 'active-page' : '') . "'>Officers</a>
                        <a href='../ams/schoolyear.php' class='".($activePage == 'schoolyear' ? 'active-page':'') . "'>School Year</a>
                        ";
                      }
                    ?>

                    </div>
                  </div>          
            </nav>
        </div>
    </section>
    <div id="overlay"></div>
<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script defer>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    $(document).ready(function () {
        $('#search').on('input', function () {
            var input = $(this).val();
            var url = "search-result.php";

            // Check if the first URL is valid, if not, use the alternate URL
            if (!urlExists(url)) {
                url = "../includes/search-result.php";
            }

            if (input !== "") {
                $.ajax({
                    url: url,
                    method: "POST",
                    data: { input: input },
                    success: function (data) {
                        $("#result").html(data);
                        $("#result").css("display", "block");
                    }
                });
            } else {
                $("#result").css("display", "none");
            }
        });

        // Clear the results when the default "x" is clicked
        $('#search').on('search', function () {
            if ($(this).val() === "") {
                $("#result").css("display", "none");
            }
        });

        //datatable
        $('#myTable').DataTable({
            "pageLength": 5, 
            "lengthMenu": [5, 10, 25, 50], 
            "searching": true,
        });

        $('#myTable1').DataTable({
            "pageLength": 5, 
            "lengthMenu": [5, 10, 25, 50], 
            "searching": true,
        });

        $('#myTable2').DataTable({
            "pageLength": 5, 
            "lengthMenu": [5, 10, 25, 50], 
            "searching": true,
        });

        $('#myTable3').DataTable({
            "pageLength": 5, 
            "lengthMenu": [5, 10, 25, 50], 
            "searching": true,
        });

        $('#sy').on('change', function(){
          // Get the selected value
          var selectedSchoolYearID = $(this).val();

          // Log the selected value for debugging
          console.log('Selected School Year ID:', selectedSchoolYearID);

          // Store the selected value in a JavaScript variable (client-side session)
          sessionStorage.setItem('sch_year', selectedSchoolYearID);

          // Send the selected value to the server to update the session variable
          $.ajax({
              type: 'POST',
              url: '<?php echo $_SERVER['PHP_SELF']; ?>',
              data: { school_year_id: selectedSchoolYearID },
              success: function(response) {
                  console.log(response); // Output the server response (for debugging)
                  location.reload();
              }
          });
        });

    });

    function urlExists(url) {
        var http = new XMLHttpRequest();

        http.open('HEAD', url, false);
        http.send();

        return http.status !== 404;
    }
</script>

