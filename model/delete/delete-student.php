<?php
if(!empty($_GET['deleteid'])){
    $message = '';
    try {
        $delete = htmlspecialchars(strip_tags($_GET['deleteid']));

        $query = "DELETE FROM tbl_students WHERE student_id = :delete";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':delete', $delete, PDO::PARAM_INT);
        $stmt->execute();

        $messageSuccess = "Deleted successfully";
    } catch (Exception $e) {
        // $message = $e->getMessage();
        $message = "Delete unsuccessfull";
    }
        if (!empty($message)) {
            $message = htmlspecialchars($message);
            "<script>
                alert('$message');
            </script>";
        }else{
            $messageSuccess = htmlspecialchars($messageSuccess);
            "<script>
                alert('$messageSuccess');
            </script>";
        }
}