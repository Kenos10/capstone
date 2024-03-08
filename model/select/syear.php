<?php

$querySyear = "SELECT *
FROM tbl_school_year
ORDER BY school_yearstart ASC;
";
$stmtSyear = $conn->prepare($querySyear);
$stmtSyear->execute();
