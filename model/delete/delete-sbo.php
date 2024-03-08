<?php
if(!empty($_GET['sboid'])){
    $message = '';
    try {
        $delete = htmlspecialchars(strip_tags($_GET['sboid']));

        $query = "DELETE FROM tbl_sbo WHERE sbo_id = :delete";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':delete', $delete, PDO::PARAM_INT);
        $stmt->execute();

        $messageSuccess = "Deleted successfully";
    } catch (Exception $e) {
        // $message = $e->getMessage();
        $message = "Deleted unsuccessfully";
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