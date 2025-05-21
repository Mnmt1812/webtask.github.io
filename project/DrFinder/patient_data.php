<?php
session_start(); 
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "docfinder";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['patientID'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Fetch patient's details from the database
$patientID = $_SESSION['patientID'];
$sql = "SELECT * FROM patients WHERE id = $patientID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Patient found, fetch and store the details
    $row = $result->fetch_assoc();
    $patientName = $row['patientname']; // Fetch the patient's name
    $phoneNo = $row['patientphone'];
    $email = $row['patientemail'];
    $gender = $row['patientgender'];
    $dob = $row['patientdob'];
} else {
    // Patient not found
    echo "Error: Patient not found.";
}

// Close the connection
$conn->close();
?>