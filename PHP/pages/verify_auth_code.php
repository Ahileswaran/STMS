<?php
session_start();

$server = "localhost";
$username = "root";
$password = "";
$database = "stms_database";

if (isset($_POST['auth_code'])) {
    $input_code = $_POST['auth_code'];
    if ($input_code === $_SESSION['auth_code']) {
        $registration_data = $_SESSION['pending_registration'];

        $connection = new mysqli($server, $username, $password, $database);
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        $sql = "INSERT INTO principal (first_name, last_name, user_address, age, sex, marital_status, registration_id, username, email, user_password, admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssssssss", $registration_data['first_name'], $registration_data['last_name'], $registration_data['user_address'], $registration_data['age'], $registration_data['sex'], $registration_data['marital_status'], $registration_data['registration_id'], $registration_data['username'], $registration_data['mail_id'], $registration_data['password']);

        if ($stmt->execute()) {
            $sql_login = "INSERT INTO login (username, email, user_password) VALUES (?, ?, ?)";
            $stmt_login = $connection->prepare($sql_login);
            $stmt_login->bind_param("sss", $registration_data['username'], $registration_data['mail_id'], $registration_data['password']);
            if ($stmt_login->execute()) {
                echo "<script>alert('Registration successful.'); window.location.href = '../index.php';</script>";
            } else {
                echo "ERROR: Could not execute $sql_login. " . $stmt_login->error;
            }
            $stmt_login->close();
        } else {
            echo "ERROR: Could not execute $sql. " . $stmt->error;
        }
        $stmt->close();
        $connection->close();
        unset($_SESSION['auth_code']);
        unset($_SESSION['pending_registration']);
    } else {
        echo "<script>alert('Incorrect authentication code.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify Authentication Code</title>
    <link rel="stylesheet" href="../../styles.css">
</head>
<body>
    <div class="content">
        <div class="form-container">
            <form action="verify_auth_code.php" method="post">
                <label for="auth_code">Enter Authentication Code:</label>
                <input id="auth_code" name="auth_code" type="text" required>
                <button type="submit" value="submit">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
