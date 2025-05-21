<?php
session_start(); // Start the session to access session variables

// Assuming you have already established a database connection
$servername = "localhost"; // Change this if your MySQL server is hosted elsewhere
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password
$dbname = "docfinder"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the submitted email and password
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Perform SQL query to retrieve the hashed password for the provided email
    $sql = "SELECT * FROM patients WHERE patientemail = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Fetch the row from the result
        $row = $result->fetch_assoc();
        $stored_hashed_password = $row['patientpassword'];
        $patientName = $row['patientname']; // Fetch the patient's name
        $patientGender = $row['patientgender'];
        $patientID = $row['id']; // Fetch the patient's gender
        $dob = $row['patientdob']; // Fetch the patient's dob
        $phoneNo = $row['patientphone'];
        $email = $row['patientemail']; // Fetch the patient's phone number
        

        // Verify the provided password against the stored hashed password
        if (password_verify($password, $stored_hashed_password)) {
            // Password is correct, set session variables and redirect to the patient dashboard
            $_SESSION['email'] = $email;
            $_SESSION['patientName'] = $patientName; // Store patient's name in session
            $_SESSION['patientGender'] = $patientGender; 
            $_SESSION['patientID'] = $patientID;// Store patient's gender in session
            $_SESSION['dob'] = $dob; // Store patient's dob in session
            $_SESSION['phoneNo'] = $phoneNo; // Store patient's phone number in session
            header("Location: pdash.php"); // Redirect to patient dashboard
            exit();
        } else {
            // Password doesn't match
            echo "<script>alert('Incorrect password. Please try again.'); window.location.href = 'plogin.html';</script>";
        }
    } else {
        // Email doesn't exist
        echo "<script>alert('Email does not exist. Please register.'); window.location.href = 'pportal.html';</script>";
    }
}

// Close the connection
$conn->close();
?>
