<?php
include('../includes/dashboard.php');
$filesToInclude = [INSERT_EVENT, EVENT, DELETE_EVENT, SYEAR, UPDATE_EVENT];
foreach ($filesToInclude as $file) {
    require_once($file);
}

if($_SESSION['role'] !== ACCOUNT_TYPE_A && $_SESSION['role'] !== ACCOUNT_TYPE_EM && $_SESSION['role'] !== ACCOUNT_TYPE_AM){
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
    <link rel="stylesheet" href="../assets/css/events.css?version=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/modalEdit.css?version=<?php echo time(); ?>">
    <script src="../assets/js/modal.js" defer></script>
    <script src="../assets/js/time.js" defer></script>
    <title>Events</title>
</head>
<body>
    <main class="home">
        <div class="title-dashboard">
            <div>
                <span><img src="../icons/dashboard (2).png" alt="dashboard"></span>
                <h2>Events</h2>
                <?php 
                    if($_SESSION['role'] == ACCOUNT_TYPE_A || $_SESSION['role'] == ACCOUNT_TYPE_EM){
                        echo "<button id='myBtn'>Add New</button>";
                    }
                ?>
            </div>
            <div class="breadcrumb">
                <p>Home</p>
                <p>Events</p>
                <p class="active-page">Manage Events</p>
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
                    <form action="" method="POST">
                        <div class="option-item item-1">
                            <select name="yearlevel" id="months">
                                <option>-- Month --</option>
                                <option value="All">All</option>
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
                        <!-- <div class="option-item item-2">
                            <select name="status" id="status">
                                <option>-- Status --</option>
                                <option value="done">Done</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div> -->
                        <div class="option-item item-3">
                            <input type="submit" value="Filter" name="filter">
                        </div>
                    </form>
              </div>

              <div class="table-item">
                <table id="myTable" class="display">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Venue</th>
                            <th>Date</th>
                            <th>Status</th>
                            <?php 
                                if($_SESSION['role'] == ACCOUNT_TYPE_A || $_SESSION['role'] == ACCOUNT_TYPE_EM){
                                    echo "<th>Action</th>";
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php

                        if ($stmtEvents->execute()) {
                            while ($row = $stmtEvents->fetch()) {
                                $eventStatus = "";
                                $monthStart = date('n', strtotime($row['event_date']));
                                $dayStart = date('j', strtotime($row['event_date']));
                                $yearStart = date('y', strtotime($row['event_date']));

                                if (date('n') > $monthStart && date('y') >= $yearStart) {
                                    $eventStatus = 'Passed';
                                } else if (date('n') == $monthStart) {
                                    if (date('j') < date('j', strtotime($row['event_date']))) {
                                        $eventStatus = 'Upcoming';
                                    } else if (date('j') == date('j', strtotime($row['event_date']))) {
                                        $eventStatus = 'Ongoing';
                                    } else {
                                        $eventStatus = 'Passed';
                                    }
                                } else if (date('n') < $monthStart) {
                                    $eventStatus = 'Upcoming';
                                }
                                ?>
                                <tr data-id="<?php echo $row['event_id'];?>">
                                    <td><?= $row['event_name'] ?></td>
                                    <td><?= $row['event_description'] ?></td>
                                    <td><?= $row['event_venue'] ?></td>
                                    <td><?= date('M. d, Y, D', strtotime($row['event_date'])) ?></td>
                                    <td><?= $eventStatus ?></td>
                                    <?php 
                                    if($_SESSION['role'] == ACCOUNT_TYPE_A || $_SESSION['role'] == ACCOUNT_TYPE_EM){?>
                                        <td>
                                            <a href='#' class='view-link' data-toggle='modal' data-target='#eventModal' data-id='<?= $row['event_id'] ?>'>
                                                <img class='table-icon' src='../icons/view.png' alt='View'>
                                            </a>
                                            <a class='edit' data-id='<?= $row['event_id'] ?>' href='#'>
                                                <img class='table-icon' src='../icons/edit.png' alt='Edit'>
                                            </a>
                                            <a class='delete' href='<?= $_SERVER['PHP_SELF'] ?>?deleteid=<?= $row['event_id'] ?>'>
                                                <img class='table-icon' src='../icons/delete.png' alt='Delete'>
                                            </a> 
                                        </td>
                                    <?php }?>
                                    </tr>
                                <?php
                            }
                        } 
                        ?>
                    </tbody>
                </table>
              </div>
        </section>
    </main>

    <div id="myModalEdit" class="modalEdit">
        <div class="modal-contentEdit">
            <span class="closeEdit">&times;</span>
            <h3>Edit Event</h3>
            <form id="editForm" action="" method="POST">
            <legend>Event Details</legend>
            <div class="form-group">
                <input type="hidden" name="event_id" value="<?php echo $row['event_id']; ?>">
                <div>
                    <label for="eventName">Name:</label>
                    <input type="text" name="eventName" value="<?php echo $row['event_name']; ?>">
                </div>
                
                <div>
                    <label for="eventDesc">Description:</label>
                    <textarea name="eventDesc" value="<?php echo $row['event_description']; ?>"></textarea>
                </div>

                <div>
                    <label for="eventVenue">Venue:</label>
                    <input type="text" name="eventVenue" value="<?php echo $row['event_venue']; ?>">
                </div>
            </div>
                <input type="submit" name="editEvent" value="Update">
            </form>
        </div>
    </div>

<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Event Registration</h3>
        <form action="" method="POST">
            <div>
                <legend>Event Information</legend>
                <div class="form-group">
                    <div>
                        <label for="eventname">Event Name</label>
                        <input type="text" name="eventname" id="eventname" required>
                    </div>

                    <div>
                        <label for="eventdesc">Description</label>
                        <textarea name="eventdesc" id="eventdesc" required></textarea>
                    </div>

                    <div>
                        <label for="eventvenue">Venue</label>
                        <input type="text" name="eventvenue" id="eventvenue" required>
                    </div>

                    <div>
                        <label for="syear">School Year</label>
                        <select name="syear" id="syear" required>
                        <option selected disabled value="">-- Select School Year --</option>
                        <?php
                            $results = $stmtSyear->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($results as $row) {
                                $startYear = $row['school_yearstart'];
                                $endYear = $row['school_yearend'];
                                $sem = $row['semester'];
                                $schoolYear = $startYear . '-' . $endYear . ' ' . $sem;
                                echo "<option value='" . $row['school_year_id'] . "'>$schoolYear</option>";
                            }
                        ?>
                        </select>
                    </div>

                    <div>
                        <label for="fines">Fines <i style="color: grey;">for each phase(Morning/Afternoon)</i>
                        </label>
                        <input id="input-1" type="number" name="fines" id="eventfines" min="0.00" max="100.00" step="0.05" oninput="onInput(this)" value="0.00" />
                    </div>

                </div>
            </div>
            
            <div>
                <legend>Event Date</legend>
                <div class="form-group">
                    <div>
                        <label for="eventdate">Date</label>
                        <input type="date" name="eventdate" id="eventdate" required>
                    </div>
                </div>
            </div>
            
            <div>
                <legend>Schedule</legend>
                <div class="form-group checkbox">
                    <div>
                        <input type="radio" name="schedule" id="timeInOut" value="timeInOut" required>
                        <label for="timeInOut">Time-in/Out</label>
                    </div>
                </div>
            </div>

            <div class="schedule time">
                <legend>Time-in/Out</legend>
                <div class="form-group">
                    <div>
                        <label for="phase">Phases</label>
                        <select name="phase" id="phase">
                            <option value="morning">Morning</option>
                            <option value="afternoon">Afternoon</option>
                            <option value="both">Both</option>
                        </select>
                    </div>

                    <div class="time-phase">
                        <label for="timeIn">Time In</label>
                        <input type="time" name="timeIn">
                    </div>
                        
                    <div class="time-phase">
                        <label for="timeOut">Time Out</label>
                        <input type="time" name="timeOut">
                    </div>

                    <div class="time-afternoon" style="display: none;">
                        <label for="timeInA">Time In - Afternoon</label>
                        <input type="time" name="timeInA">
                    </div>
                        
                    <div class="time-afternoon" style="display: none;">
                        <label for="timeOutA">Time Out - Afternoon</label>
                        <input type="time" name="timeOutA">
                    </div>
                </div>
            </div>

            <input type="submit" name="addevent" value="Submit">
        </form>
    </div>
</div>

<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="closeModal" id="closeModal" onclick="closeModal()">&times;</span>
        <div id="eventDetails">
        </div>
    </div>
</div>

<script>
    //Form  
        document.addEventListener("DOMContentLoaded", function () {
        var modalEdit = document.getElementById("myModalEdit");
        var closeEdit = document.querySelector(".closeEdit");
        var editForm = document.getElementById("editForm");

        Array.from(document.querySelectorAll(".edit")).forEach(function (element) {
            element.addEventListener("click", function (event) {
                event.preventDefault();

                // Get the ID of the record to be edited
                var id = this.parentElement.parentElement.getAttribute("data-id");

                editForm.event_id.value = id;
                editForm.eventName.value = this.parentElement.parentElement.querySelector("td:nth-child(1)").textContent;
                editForm.eventDesc.value = this.parentElement.parentElement.querySelector("td:nth-child(2)").textContent;
                editForm.eventVenue.value = this.parentElement.parentElement.querySelector("td:nth-child(3)").textContent; 

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


    document.addEventListener('DOMContentLoaded', function () {
        const radioButtons = document.querySelectorAll('input[type=radio][name="schedule"]');
        const timeSchedule = document.querySelector('.schedule.time');

        // Hide morning and afternoon schedules initially
        timeSchedule.style.display = 'none';

        // Add event listener to radio buttons
        radioButtons.forEach(function (radio) {
            radio.addEventListener('change', function () {
                // Hide both schedules
                timeSchedule.style.display = 'none';

                if (this.id === 'timeInOut') {
                    timeSchedule.style.display = 'block';
                }
            });
        });
    });

    $(document).ready(function () {
        $('.view-link').click(function () {
            var eventId = $(this).data('id');

            // Make an AJAX request to get event details based on eventId
            $.ajax({
                type: 'POST',
                url: 'get_event_details.php',
                data: { event_id: eventId },
                success: function (response) {
                    $('#eventDetails').html(response);
                    openModal();
                }
            });
        });

        function openModal() {
            $('#eventModal').show();
        }

        function closeModal() {
            $('#eventModal').hide();
        }

        // Close the modal if the user clicks outside of it
        $(window).click(function (event) {
            var modal = $('#closeModal');
            if (event.target === modal[0]) {
                closeModal();
            }
        });

        toggleTimeAfternoon();

        // Event listener for the phase select element
        $('#phase').on('change', function() {
            toggleTimeAfternoon();
        });

        // Function to toggle the visibility of the time-afternoon div based on the selected option
        function toggleTimeAfternoon() {
            var selectedPhase = $('#phase').val();
            if (selectedPhase === 'both') {
                $('.time-afternoon').show();
                    $('.time-phase label').each(function() {
                        var originalLabel = $(this).text();
                        $(this).text(originalLabel + ' - Morning');
                    });
            } else {
                $('.time-afternoon').hide();
                $('.time-phase label').each(function() {
                    var originalLabel = $(this).text();
                    // Remove the postfix if it exists
                    $(this).text(originalLabel.replace(' - Morning', ''));
                });
            }
        }
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

    function onInput(event) {
        let value = parseFloat(event.value);
        if (Number.isNaN(value)) {
            document.getElementById('input-1').value = "0.00";
        } else {
            document.getElementById('input-1').value = value.toFixed(2);
        }              
    }
</script>

</body>
</html>