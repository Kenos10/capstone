<?php
if(!empty($_GET['syearid'])){
    $message = '';
    try {
        $delete = htmlspecialchars(strip_tags($_GET['syearid']));

        $query = "DELETE FROM tbl_school_year WHERE school_year_id = :delete";
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