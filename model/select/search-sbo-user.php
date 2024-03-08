<?php
require_once('../../config/constant.php');

if(isset($_POST['input'])) {
    $studentSearch =  htmlspecialchars(strip_tags($_POST['input']));
    
    $querySearch = "SELECT CONCAT(tbl_students.first_name, ' ', tbl_students.last_name) AS full_name, tbl_students.student_id, tbl_sbo.position, tbl_sbo.sbo_id
    FROM tbl_students 
    JOIN tbl_sbo ON tbl_students.student_id = tbl_sbo.student_id WHERE tbl_sbo.student_id LIKE :studentSearch OR tbl_students.first_name LIKE :studentSearch OR tbl_students.last_name LIKE :studentSearch LIMIT 3";
    $stmtSearch = $conn->prepare($querySearch);
    $stmtSearch->bindValue(':studentSearch', '%'.$studentSearch.'%');
    $stmtSearch->execute();

    if ($stmtSearch->rowCount() > 0) {?>
        <table rules="none">
            <tbody>
                <?php while($rowSearch = $stmtSearch->fetch()) {?>
                    <tr class="student-row" data-position="<?php echo $rowSearch['position'];?>" data-full-name="<?php echo $rowSearch['full_name'];?>" data-sboid="<?php echo $rowSearch['sbo_id'];?>">
                        <td><?php echo $rowSearch['student_id'];?></td>
                        <td><?php echo $rowSearch['full_name'];?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    <?php } else {
        echo "<p>No student found!</p>";
    }
}