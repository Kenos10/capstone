<?php

$currentMonth = date('n');
$currentYear = date('Y');

$schooolYear = $_SESSION['sch_year'];

//Upcoming Event
$queryUpcomingE = "SELECT event_name, DATE_FORMAT(event_date, '%a, %b %e, %Y') AS formatted_date
FROM tbl_events 
WHERE MONTH(event_date) = :currentMonth AND YEAR(event_date) = :currentYear AND school_year_id = :schoolYear";

$stmtUpcomingE = $conn->prepare($queryUpcomingE);
$stmtUpcomingE->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
$stmtUpcomingE->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmtUpcomingE->bindParam(':schoolYear', $schooolYear, PDO::PARAM_INT);
$stmtUpcomingE->execute();

//This Month Event Count
$queryCountMonth = "SELECT COUNT(*)
FROM tbl_events 
WHERE MONTH(event_date) = :currentMonth AND YEAR(event_date) = :currentYear AND school_year_id = :schoolYear";

$stmtCountMonth = $conn->prepare($queryCountMonth);
$stmtCountMonth->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
$stmtCountMonth->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$stmtCountMonth->bindParam(':schoolYear', $schooolYear, PDO::PARAM_INT);
$stmtCountMonth->execute();
$eventCountMonth = $stmtCountMonth->fetchColumn();

//This Sem Total Events
$countEQuery = 'SELECT COUNT(*) FROM tbl_events WHERE YEAR(event_date) = :currentYear AND school_year_id = :schoolYear';
$countEStmt = $conn->prepare($countEQuery);
$countEStmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$countEStmt->bindParam(':schoolYear', $schooolYear, PDO::PARAM_INT);
$countEStmt->execute();
$eventCount = $countEStmt->fetchColumn();

if(isset($_POST['yearlevel'])){
    if($_POST['yearlevel'] != 'All'){
        $yearLevel = intval(htmlspecialchars($_POST['yearlevel']));
        $queryEvents  = 'SELECT * FROM tbl_events WHERE YEAR(event_date) = YEAR(CURRENT_DATE) AND MONTH(event_date) = :year_level AND school_year_id = :schoolYear';
        $stmtEvents  = $conn->prepare($queryEvents);
        $stmtEvents->bindValue(':year_level', $yearLevel);
        $stmtEvents->bindValue(':schoolYear', $schooolYear);
        $stmtEvents->execute();
    }else{
        $queryEvents = 'SELECT * FROM tbl_events WHERE YEAR(event_date) = YEAR(CURRENT_DATE) AND school_year_id = :schoolYear';
        $stmtEvents  = $conn->prepare($queryEvents);
        $stmtEvents->bindValue(':schoolYear', $schooolYear);
        $stmtEvents->execute();
    }
}else{
    $queryEvents = 'SELECT * FROM tbl_events WHERE YEAR(event_date) = YEAR(CURRENT_DATE) AND MONTH(event_date) = MONTH(CURRENT_DATE) AND school_year_id = :schoolYear';
    $stmtEvents = $conn->prepare($queryEvents);
    $stmtEvents->bindValue(':schoolYear', $schooolYear);
    $stmtEvents->execute();
}