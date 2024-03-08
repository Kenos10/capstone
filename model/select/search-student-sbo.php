<?php
require_once('../../config/constant.php');

if(isset($_POST['input'])) {
    $studentSearch =  htmlspecialchars(strip_tags($_POST['input']));
    
    $querySearch = "SELECT *, CONCAT(first_name, ' ', last_name) AS full_name FROM tbl_students WHERE student_id LIKE :studentSearch OR first_name LIKE :studentSearch OR last_name LIKE :studentSearch LIMIT 3";
    $stmtSearch = $conn->prepare($querySearch);
    $stmtSearch->bindValue(':studentSearch', '%'.$studentSearch.'%');
    $stmtSearch->execute();

    if ($stmtSearch->rowCount() > 0) {?>
        <table rules="none">
            <tbody>
                <?php while($rowSearch = $stmtSearch->fetch()) {?>
                    <tr class="student-row" data-student-id="<?php echo $rowSearch['student_id'];?>" data-full-name="<?php echo $rowSearch['full_name'];?>">
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