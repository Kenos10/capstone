<?php

function validate_input($input, $field_name, $length = 60) {
    $error = '';
    if(!ctype_alpha(str_replace(' ', '', $input)) || strlen($input) > $length){
        $error = "Error: Invalid $field_name. $field_name must contain only letters.";
    }
    return $error;
}

if(isset($_POST['addStudent'])){
    $data = [
        'schoolID' => array_map('htmlspecialchars', array_map('strip_tags', $_POST['schoolID'])),
        'yearStudent' => array_map('htmlspecialchars', array_map('strip_tags', $_POST['yearStudent'])),
        'firstName' => array_map('htmlspecialchars', array_map('strip_tags', $_POST['firstName'])),
        'lastName' => array_map('htmlspecialchars', array_map('strip_tags', $_POST['lastName'])),
        'errors' => []
    ];

    $allowedYearLevels = ['1st', '2nd', '3rd', '4th'];

    for ($i = 0; $i < count($data['schoolID']); $i++) {
        if (!ctype_digit($data['schoolID'][$i]) || strlen($data['schoolID'][$i]) != 7) {
            $data['errors'][] = "Error: Invalid school id. School id must be a 7-digit number.";
        }

        if (!in_array($data['yearStudent'][$i], $allowedYearLevels)) {
            $data['errors'][] = "Error: Invalid year level. Year level must be one of the following: 1st, 2nd, 3rd, or 4th.";
        }

        foreach (['firstName', 'lastName'] as $field) {
            $error = validate_input($data[$field][$i], $field);
            if (!empty($error)) {
                $data['errors'][] = $error;
            }
        }
    }

    if(empty($data['errors'])){
        try {
            $query = "INSERT INTO tbl_students (student_id, year_level, first_name, last_name, gmail, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            for ($i = 0; $i < count($data['schoolID']); $i++) {
                // Sanitize the input
                $gmail = htmlspecialchars(strip_tags($data['schoolID'][$i]).'@g.cu.edu.ph');

                // Bind the parameters and execute the query
                $stmt->bindValue(1, $data['schoolID'][$i], PDO::PARAM_INT);
                $stmt->bindValue(2, $data['yearStudent'][$i], PDO::PARAM_STR);
                $stmt->bindValue(3, $data['firstName'][$i], PDO::PARAM_STR);
                $stmt->bindValue(4, $data['lastName'][$i], PDO::PARAM_STR);
                $stmt->bindValue(5, $gmail, PDO::PARAM_STR);
                $stmt->bindValue(6, 'Active', PDO::PARAM_STR);

                $stmt->execute();
            }

            echo "<script>
                    alert('Record inserted successfully!');
                    window.location.href = '';
                  </script>";
        } catch (PDOException $e) {
            echo "<script>
                    alert('{$e->getMessage()}');
                    window.location.href = '';
                  </script>";
        }
    } else {
        // Join the error messages into a single string
        echo "<script>
                alert('" . implode("\\n", array_unique($data['errors'])) . "');
                window.location.href = '';
              </script>";
    }
}