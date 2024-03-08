<?php

require_once('../config/constant.php');

if(isset($_POST['input'])) {
    $studentSearch =  htmlspecialchars(strip_tags($_POST['input']));
    
    $querySearch = 'SELECT * FROM tbl_students WHERE student_id LIKE :studentSearch OR first_name LIKE :studentSearch OR last_name LIKE :studentSearch LIMIT 3';
    $stmtSearch = $conn->prepare($querySearch);
    $stmtSearch->bindValue(':studentSearch', '%'.$studentSearch.'%');
    $stmtSearch->execute();

    if ($stmtSearch->rowCount() > 0) {
        echo '<table rules="none">
                <tbody>';
        while($rowSearch = $stmtSearch->fetch()) {
            echo '<tr onclick="redirectToSearchRes('.$rowSearch['student_id'].')">
                    <td>'.$rowSearch['student_id'].'</td>
                    <td>'.$rowSearch['last_name'].', </td>
                    <td>'.$rowSearch['first_name'].'</td>
                  </tr>';
        }
        echo '</tbody>
              </table>';
    } else {
        echo "<p>No student found!</p>";
    }
}

?>

<script defer>
  function redirectToSearchRes(studentId) {
    var url = "search.php?id=" + studentId;

    // Check if the URL is valid, if not, use the alternate URL
    if (!urlExists(url)) {
      url = "../includes/search.php?id=" + studentId;
    }

    window.location.href = url;
  }

  function urlExists(url) {
    var http = new XMLHttpRequest();

    http.open('HEAD', url, false);
    http.send();

    return http.status !== 404;
  }
</script>

