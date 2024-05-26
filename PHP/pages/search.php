<?php
session_start();

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if (isset($_GET['search'])) {
    $search = $connection->real_escape_string($_GET['search']);
    
    // Adjust the SQL query to match the actual column names in the `teacher` table
    $sql = "SELECT * FROM teacher WHERE first_name LIKE ? OR last_name LIKE ? OR user_address LIKE ? OR subject_name LIKE ? OR username LIKE ? OR email LIKE ?";
    $stmt = $connection->prepare($sql);
    $search_term = "%" . $search . "%";
    $stmt->bind_param("ssssss", $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $search_results = [];
    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }
    
    $stmt->close();
    $connection->close();
    
    $_SESSION['search_results'] = $search_results;
    header("Location: search_result.php");
    exit();
} else {
    header("Location: ../../index.php");
    exit();
}
?>
