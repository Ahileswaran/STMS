
<?php
$username = ""; 
$password = ""; 
$server = "";   
$database = "teachersDatabase"; 

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

echo "Server connected successfully";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
}

$connection->close();
?>
