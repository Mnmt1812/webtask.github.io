<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to your MySQL database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "docfinder";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO patients (patientname, patientphone, patientpassword, patientgender, patientdob, patientemail) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $patientname, $patientphone, $patientpassword, $patientgender, $patientdob, $patientemail);

    // Set parameters and execute
    $patientname = $_POST['Patient_Name'];
    $patientphone = $_POST['Number'];
    $patientpassword = password_hash($_POST['pwd'], PASSWORD_DEFAULT); // Hash the password for security
    $patientgender = $_POST['gender'];
    $patientdob = $_POST['newDob'];
    $patientemail = $_POST['email'];

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href = 'plogin.html';</script>";
        exit; // Stop further execution
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();
}
?>
