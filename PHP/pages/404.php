<?php
$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : "The page you are looking for doesn't exist or an error occurred.";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>404 Not Found</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <style>
        .not-found {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100vh;
            background-color: #f8f8f8;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .not-found h1 {
            font-size: 6em;
            color: #ff6f61;
            margin-bottom: 20px;
        }

        .not-found p {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 20px;
        }

        .not-found a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1.2em;
            color: white;
            background-color: #ff6f61;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .not-found a:hover {
            background-color: #ff4a3a;
        }
    </style>
</head>

<body>
    <div class="not-found">
        <h1>404 Not Found</h1>
        <p><?php echo $error_message; ?></p>
        <a href="../../index.php">Go Back to Home</a>
    </div>
</body>

</html>
