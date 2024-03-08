<?php
    require_once('../config/constant.php');
    $filesToInclude = [CURRENT_USER, EVENT, OFFICERS, STUDENTS];
    foreach ($filesToInclude as $file) {
        require_once($file);
    }

    if (!$_SESSION['logged_in'] || !$_SESSION['user_id']) {
        header('Location: '.LOGIN_URL);
        die();
    }

    if($_SESSION['role'] !== ACCOUNT_TYPE_A && $_SESSION['role'] !== ACCOUNT_TYPE_AM && $_SESSION['role'] !== ACCOUNT_TYPE_EM){
        echo "<script>
                  window.location.href = '../logout.php';
              </script>";
    }
    

$query = "SELECT year_level, COUNT(*) as count FROM tbl_students  WHERE status = :active GROUP BY year_level";
$stmt = $conn->prepare($query);
$stmt->bindValue('active', 'Active');
$stmt->execute();

$yearLevelCounts = ['1st' => 0, '2nd' => 0, '3rd' => 0, '4th' => 0];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $yearLevel = $row['year_level'];
  $count = $row['count'];

  // Ensure that the year level is a valid key in the $yearLevelCounts array
  if (isset($yearLevelCounts[$yearLevel])) {
      $yearLevelCounts[$yearLevel] = $count;
  }
}

// Convert PHP array to JavaScript array using json_encode
$yearLevelCountsJson = json_encode(array_values($yearLevelCounts));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/home.css?v=<?php echo time(); ?>" media="all" type="text/css">
    <title>Dashboard</title>
</head>
<body>
    <main class="home">
        <div class="title-dashboard">
            <span><img src="../icons/dashboard (2).png" alt="dashboard"></span>
            <h2>Dashboard</h2>
        </div>
        <section class="home-container">
            <div class="home-card students-card">
                <span><img src="../icons/user-group.png" alt="students"></span>
                <div>
                    <p class="count"><?php echo $studentCount; ?></p>
                    <p class="count-title">Total students</p>
                </div>
            </div>
            <div class="home-card officers-card">
                <span><img src="../icons/people.png" alt="officers"></span>
                <div>
                    <p class="count"><?php echo $sboCount; ?></p>
                    <p class="count-title">Total SBO officers</p>
                </div>
            </div>
            <div class="home-card events-card">
                <span><img src="../icons/calendar (2).png" alt="events"></span>
                <div>
                    <p class="count"><?php echo $eventCount; ?></p>
                    <p class="count-title">Total events</p>
                </div>
            </div>
            <div class="home-card charts">
                <p class="count">Students</p>
                <div id="bar-chart"></div>
            </div>
            <div class="home-card upcoming-event-card">
                <p class="count">Upcoming Event</p>
                <div>
                  <?php
                    if($stmtUpcomingE->rowCount() > 0){
                      while($rowEvent = $stmtUpcomingE->fetch()){
                        echo "
                          <div class='item-event'>
                            <p>" . $rowEvent['event_name'] . "</p>
                            <p>" . $rowEvent['formatted_date'] . "</p>
                          </div>
                        ";
                      }
                    }else{
                      echo "
                        <div class='item-event'>
                          <p>No Event</p>
                          <p>No Event</p>
                        </div>
                      ";
                    }
                  ?>
            </div>
        </section>
    </main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.41.1/apexcharts.min.js"></script>
<script defer>
    var barChartOptions = {
        series: [{
            name: 'Students',
            data: <?php echo $yearLevelCountsJson; ?>
        }],
        chart: {
            height: 300,
            type: 'bar',
            events: {
                click: function(chart, w, e) {
                    // console.log(chart, w, e)
                }
            },
            toolbar: {
                show: false
            }
        },
        colors: [
            '#FF0000',
            '#FFA500',
            '#FFFF00',
            '#008000'
        ],
        plotOptions: {
            bar: {
                columnWidth: '25%',
                distributed: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            show: false
        },
        xaxis: {
            categories: [
                '1st Year',
                '2nd Year',
                '3rd Year',
                '4th Year',
            ],
            labels: {
                style: {
                    colors: [],
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            title: {
                text: "Count"
            }
        }
    };

    var barChart = new ApexCharts(document.querySelector("#bar-chart"), barChartOptions);
    barChart.render();
</script>
</body>
</html>

