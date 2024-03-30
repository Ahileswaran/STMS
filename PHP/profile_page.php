<?php
//session_start(); // Start the session
require_once 'login.php';


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>School Teacher Management System</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="../styles.css">
    <script src="javaScript.js"></script>
</head>

<body>
    <!-- Main container with glass effect -->
    <div class="glass-box-container">
        <!-- Banner glass container -->
        <div class="glass-container title-container">
            <img src="../imgs/logo-STMS.png" alt="Banner" class="banner-image-full">
        </div>

        <!-- Banner image taking up the entire screen -->
        <img src="../imgs/banner.png" alt="Banner" class="banner-image-full">

        <!-- Mini gap between the body and the second glass container -->
        <div class="mini-gap"></div>

        <!-- Body glass container with the navigation bar -->
        <div class="glass-container nav-container">
            <!-- Container for navigation -->
            <nav>
                <a class="active button" href="../index.html">Home</a>
                <a class="active button" href="../pages/registering_page.html">Register</a>
                <a class="active button" href="../pages/login_page.html">Login</a>
            </nav>

            <!-- Dropdown menu -->
            <div class="drop_menu">                
                <select name="menu" onchange="redirect(this)">
                    <option value="menu0" disabled selected>Downloads</option>
                    <option value="teachers_guide">Teachers Guides</option>
                    <option value="syllabi">Syllabi</option>
                    <option value="resource_page">Resource Books</option>
                </select>
            </div>

             <!-- Input Field -->
            <div class="Search_field">                               
                <input type="text" name="search" placeholder="Search...">
            </div>

            <!-- Search Button -->
            <div class="search_button">
                <button type="submit">Search</button>
            </div>

            <div class="content">
                <!-- main content goes here -->
            </div>


        </div>

        <!-- Profile container with glass effect -->
        <div class="glass-container background-glass">
            <div class="profile-pic-container">
                <img src="../imgs/profile-pic.png" alt="Profile Picture">
            </div>
            <h4>First Name: <?php echo $_SESSION['first_name']; ?></h4><br>
            <h4>Last Name: <?php echo $_SESSION['last_name']; ?></h4><br>
            <h4>Address: <?php echo $_SESSION['user_address']; ?></h4><br>
            <h4>Age: <?php echo $_SESSION['age']; ?></h4><br>
            <h4>Sex: <?php echo $_SESSION['sex']; ?></h4><br>
            <h4>Marital Status: <?php echo $_SESSION['marital_status']; ?></h4><br>
            <h4>Registration Id: <?php echo $_SESSION['registration_id']; ?></h4><br>
            <h4>Subject: <?php echo $_SESSION['subject_name']; ?></h4><br>
            <h4>User Name: <?php echo $_SESSION['username']; ?></h4><br>
            <h4>E-mail: <?php echo $_SESSION['email']; ?></h4><br>
        </div>
        

        
        <!-- Time Table For Teacher  -->
        <div class="master-table">
            <table>
                <caption>
                    <h3>Time Table</h3>
                    <h5>Subject: Science</h5>
                </caption>
                <tr>
                    <th></th> <!-- An empty cell for spacing -->
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednsday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                </tr>

 <!--               
                <tr>
                    <th>Time_1</th>
                    <td class="profile_circle"><span>Data_1</span></td>
                    <td class="profile_circle"><span>Data_2</span></td>
                    <td class="profile_circle"><span>Data_3</span></td>
                    <td class="profile_circle"><span>Data_4</span></td>
                    <td class="profile_circle"><span>Data_5</span></td>
                </tr>
                <tr>
                    <th>Time_2</th>
                    <td class="profile_circle"><span>Data_7</span></td>
                    <td class="profile_circle"><span>Data_8</span></td>
                    <td class="profile_circle"><span>Data_9</span></td>
                    <td class="profile_circle"><span>Data_10</span></td>
                    <td class="profile_circle"><span>Data_11</span></td>
                </tr>
                <tr>
                    <th>Time_3</th>
                    <td class="profile_circle"><span>Data_12</span></td>
                    <td class="profile_circle"><span>Data_13</span></td>
                    <td class="profile_circle"><span>Data_14</span></td>
                    <td class="profile_circle"><span>Data_15</span></td>
                    <td class="profile_circle"><span>Data_16</span></td>
                </tr>
                <tr>
                    <th>Time_4</th>
                    <td class="profile_circle"><span>Data_17</span></td>
                    <td class="profile_circle"><span>Data_18</span></td>
                    <td class="profile_circle"><span>Data_19</span></td>
                    <td class="profile_circle"><span>Data_20</span></td>
                    <td class="profile_circle"><span>Data_21</span></td>
                </tr>
                <tr>
                    <th>Time_5</th>
                    <td class="profile_circle"><span>Data_22</span></td>
                    <td class="profile_circle"><span>Data_22</span></td>
                    <td class="profile_circle"><span>Data_23</span></td>
                    <td class="profile_circle"><span>Data_24</span></td>
                    <td class="profile_circle"><span>Data_25</span></td>
                </tr>
                <tr>
                    <th>Time_6</th>
                    <td class="profile_circle"><span>Data_26</span></td>
                    <td class="profile_circle"><span>Data_27</span></td>
                    <td class="profile_circle"><span>Data_28</span></td>
                    <td class="profile_circle"><span>Data_29</span></td>
                    <td class="profile_circle"><span>Data_30</span></td>
                </tr>
                <tr>
                    <th>Time_7</th>
                    <td class="profile_circle"><span>Data_17</span></td>
                    <td class="profile_circle"><span>Data_18</span></td>
                    <td class="profile_circle"><span>Data_19</span></td>
                    <td class="profile_circle"><span>Data_20</span></td>
                    <td class="profile_circle"><span>Data_21</span></td>
                </tr>
                <tr>
                    <th>Time_8</th>
                    <td class="profile_circle"><span>Data_21</span></td>
                    <td class="profile_circle"><span>Data_22</span></td>
                    <td class="profile_circle"><span>Data_23</span></td>
                    <td class="profile_circle"><span>Data_24</span></td>
                    <td class="profile_circle"><span>Data_25</span></td>
                </tr>
--> 
                
                <?php
                // Define days of the week
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                
                // Define timeslots
                $timeslots = ['Time_1', 'Time_2', 'Time_3', 'Time_4', 'Time_5', 'Time_6', 'Time_7', 'Time_8'];
                
                // Query to fetch data from the database
                $query = "SELECT class_day, start_time, end_time, class_id FROM teacher_time_table_TNTEA1250 WHERE registration_id = '{$_SESSION['registration_id']}' AND subject_id = '{$_SESSION['subject_name']}'";
                $result = mysqli_query($connection, $query);
                
                // Initialize timetable array
                $timetable = array_fill_keys($timeslots, array_fill_keys($days, ''));

                // Populate timetable array only if there are rows in the result set
                if (mysqli_num_rows($result) > 0) {
                    // Initialize timetable array
                    $timetable = array_fill_keys($timeslots, array_fill_keys($days, ''));
                
                    // Populate timetable array
                    while ($row = mysqli_fetch_assoc($result)) {
                        $day_index = array_search($row['class_day'], $days);
                        $time_index = array_search($row['start_time'] . '-' . $row['end_time'], $timeslots);
                        $timetable[$timeslots[$time_index]][$days[$day_index]] = $row['class_id'];
                    }
                
                    // Output timetable
                    foreach ($timeslots as $time) {
                        echo "<tr>";
                        echo "<th>$time</th>";
                        foreach ($days as $day) {
                            $class_id = $timetable[$time][$day];
                            if (!empty($class_id)) {
                                $query_time = "SELECT start_time, end_time FROM teacher_time_table_TNTEA1250 WHERE registration_id = '{$_SESSION['registration_id']}' AND subject_id = '{$_SESSION['subject_name']}' AND class_id = '$class_id' AND class_day = '$day'";
                                $result_time = mysqli_query($connection, $query_time);
                                if ($result_time && mysqli_num_rows($result_time) > 0) {
                                    $time_row = mysqli_fetch_assoc($result_time);
                                    $time_display = $time_row['start_time'] . ' - ' . $time_row['end_time'];
                                    echo "<td><span>$time_display</span></td>";
                                } else {
                                    echo "<td><span>No data available</span></td>";
                                }
                            } else {
                                echo "<td><span>No class scheduled</span></td>";
                            }
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No timetable data available</td></tr>";
                }

                mysqli_close($connection);
                ?>
            </table>  
    </div>

    

    <!-- Footer with rich text -->
    <footer class="footer">
        <p>&copy; School Teachers Management System 2024. All rights reserved. Designed by Dragons.</p>
    </footer>

</body>

</html>
