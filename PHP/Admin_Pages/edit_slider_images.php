<?php
session_start(); // Start the session

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['slider_image'])) {
    $image_id = $_POST['image_id'];
    $caption = $_POST['caption'];
    $image = $_FILES['slider_image']['tmp_name'];
    $imgContent = addslashes(file_get_contents($image));

    // Check if the image ID already exists
    $check_sql = "SELECT * FROM slider_picture WHERE image_id = '$image_id'";
    $check_result = $connection->query($check_sql);

    if ($check_result->num_rows > 0) {
        // If image ID exists, update the existing record
        $update_sql = "UPDATE slider_picture SET caption = '$caption', slider_pic = '$imgContent' WHERE image_id = '$image_id'";
        if ($connection->query($update_sql) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error: " . $update_sql . "<br>" . $connection->error;
        }
    } else {
        // If image ID does not exist, insert a new record
        $insert_sql = "INSERT INTO slider_picture (image_id, caption, slider_pic) VALUES ('$image_id', '$caption', '$imgContent')";
        if ($connection->query($insert_sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $insert_sql . "<br>" . $connection->error;
        }
    }
}

// Fetch images from the database
$sql = "SELECT image_id, caption, slider_pic FROM slider_picture";
$result = $connection->query($sql);
$sliderItems = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sliderItems[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Image Upload and Preview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .edit-all-container {
            width: 90%;
            max-width: 800px;
            padding: 20px;
            background: transparent;
            border-radius: 10px;
            margin: 20px auto;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 58px;
            margin-left: 650px;
        }

        .select-container {
            background: #333;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            text-align: center;
        }

        .select-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .select-container label,
        .select-container select,
        .select-container input,
        .select-container button {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
        }

        .select-container input[type="text"],
        .select-container select {
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .select-container button {
            background-color: #5cb85c;
            color: white;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }

        .select-container button:hover {
            background-color: #4cae4c;
        }

        .slider-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            overflow: hidden;
        }

        .slider {
            position: relative;
            width: 100%;
        }

        .slider-item {
            display: none;
            width: 100%;
        }

        .slider-item.active {
            display: block;
        }

        .slider img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 10px;
        }

        .slider-caption {
            position: absolute;
            bottom: 20px;
            left: 20px;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        @media (max-width: 768px) {

            .edit-all-container,
            .select-container {
                width: 100%;
            }

            .slider-caption {
                font-size: 18px;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const items = document.querySelectorAll('.slider-item');
            let currentItem = 0;

            function showNextItem() {
                items[currentItem].classList.remove('active');
                currentItem = (currentItem + 1) % items.length;
                items[currentItem].classList.add('active');
            }

            setInterval(showNextItem, 3000);

            const previewButton = document.getElementById('previewButton');
            const imageInput = document.getElementById('slider_image');
            const previewImage = document.getElementById('previewImage');
            const previewCaption = document.getElementById('previewCaption');
            const captionInput = document.getElementById('caption');

            previewButton.addEventListener('click', (e) => {
                e.preventDefault();
                const file = imageInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewImage.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
                previewCaption.textContent = captionInput.value;
                document.querySelector('.preview-container').style.display = 'block';
            });
        });
    </script>
</head>

<body>
    <div class="edit-all-container">
        <div class="slider-container">
            <div class="slider">
                <?php foreach ($sliderItems as $index => $item) : ?>
                    <div class="slider-item <?php echo $index === 0 ? 'active' : ''; ?>" id="<?php echo $item['image_id']; ?>">
                        <img class="slider-img" src="data:image/jpeg;base64,<?php echo base64_encode($item['slider_pic']); ?>">
                        <div class="slider-caption"><?php echo $item['caption']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="select-container">
            <form action="" method="post" enctype="multipart/form-data">
                <label for="image_id">Select Image ID:</label>
                <select name="image_id" id="image_id">
                    <option value="image_1">Image 1</option>
                    <option value="image_2">Image 2</option>
                    <option value="image_3">Image 3</option>
                </select>

                <label for="caption">Caption:</label>
                <input type="text" name="caption" id="caption" required>

                <label for="slider_image">Upload Image:</label>
                <input type="file" name="slider_image" id="slider_image" accept="image/*" required>

                <button id="previewButton">Preview</button>
                <button type="submit">Upload</button>
            </form>
        </div>

        <div class="preview-container" style="display: none;">
            <div class="slider">
                <div class="slider-item active" id="preview_image">
                    <img class="slider-img" id="previewImage" src="">
                    <div class="slider-caption" id="previewCaption"></div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

<?php
$connection->close();
?>
