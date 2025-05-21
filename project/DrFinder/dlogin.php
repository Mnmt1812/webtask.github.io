<?php
// Start the session
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection settings
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "docfinder";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve email and password from form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if email exists in the database
    $checkEmailQuery = "SELECT * FROM doctors WHERE doctoremail = ?";
    $stmtCheckEmail = $conn->prepare($checkEmailQuery);
    $stmtCheckEmail->bind_param("s", $email);
    $stmtCheckEmail->execute();
    $result = $stmtCheckEmail->get_result();

    if ($result->num_rows > 0) {
        // Email exists, fetch doctor's information
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['doctorpassword'])) {
            // Password matches, set session variables for doctor's information
            $_SESSION['doctorId'] = $row['id'];
            $_SESSION['doctorName'] = $row['doctorname'];
            $_SESSION['doctorGender'] = $row['doctorgender'];
            $_SESSION['doctorEmail'] = $row['doctoremail'];
            $_SESSION['doctorSpecialist'] = $row['doctorspecialist'];
            $_SESSION['doctorExperience'] = $row['experience'];
            $_SESSION['weekdaysAvailability'] = $row['weekdaysAvailability'];
            $_SESSION['lunchtime'] = $row['lunchtime'];
            $_SESSION['doctorDob'] = $row['doctorDob'];
            $_SESSION['doctorPhone'] = $row['doctorphone'];
            $_SESSION['doctorCity'] = $row['doctorcity'];
            $_SESSION['degree'] = $row['degree'];
            $_SESSION['weekendsAvailability'] = $row['weekendsAvailability'];
            $_SESSION['clinicName'] = $row['clinicName'];
            $_SESSION['licenceNo'] = $row['licenceNo'];
            $_SESSION['doctorPhoto'] = $row['photo'];

            // Redirect to dashboard page
            header("Location: ddash.php");
            exit;
        } else {
            // Password does not match, show alert and stay on login page
            echo "<script>alert('Incorrect password. Please try again.'); window.location.href = 'dlogin.html';</script>";
            exit;
        }
    } else {
        // Email does not exist, redirect to registration page
        echo "<script>alert('Email not found. Please register.'); window.location.href = 'dportal.html';</script>";
        exit;
    }

    // Close statements and connection
    $stmtCheckEmail->close();
    $conn->close();
}
