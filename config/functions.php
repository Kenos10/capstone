<?php

function check_login($conn) {
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];

        $query = "SELECT * FROM tbl_system_user WHERE user_id = :id LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt && $stmt->rowCount() > 0) {
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user_data;
        }
    }

    header('Location: '.LOGIN_URL);
    die();

    return;
}
