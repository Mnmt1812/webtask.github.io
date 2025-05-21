<?php
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

// Get status and appointment ID from the AJAX request
$status = $_POST['status'];
$appointmentId = $_POST['appointment_id'];

// Update appointment status in the database
$sql = "UPDATE appointments SET status = '$status' WHERE appointment_id = $appointmentId";

if ($conn->query($sql) === TRUE) {
    echo "Appointment $status successfully.";
} else {
    echo "Error updating appointment status: " . $conn->error;
}

$conn->close();
?>
