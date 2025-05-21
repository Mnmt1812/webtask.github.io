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

// Retrieve form data
$prescriptionDate = $_POST['prescriptionDate'];
$drug = $_POST['drug'];
$dose = $_POST['dose'];
$amount = $_POST['amount'];

// File upload handling
$prescriptionFiles = $_FILES['prescriptionFiles'];
$fileName = $prescriptionFiles['name'];
$fileTmpName = $prescriptionFiles['tmp_name'];
$fileError = $prescriptionFiles['error'];

// File size limit (200KB)
$maxFileSize = 200 * 1024; // 200KB in bytes

// Allowed file types
$allowedTypes = ['pdf'];

if ($fileError === 0) {
    $fileSize = filesize($fileTmpName);

    // Check file size
    if ($fileSize <= $maxFileSize) {
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

        // Check file type
        if (in_array(strtolower($fileExt), $allowedTypes)) {
            $fileDestination = 'uploads/' . $fileName;

            // Move the file to the destination
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                // Get the appointment ID from the appointments table
                $sqlAppointment = "SELECT appointment_id FROM appointments WHERE status = 'approved'";
                $resultAppointment = $conn->query($sqlAppointment);

                if ($resultAppointment->num_rows > 0) {
                    $row = $resultAppointment->fetch_assoc();
                    $appointmentId = $row['appointment_id'];

                    // Insert prescription data into the database
                    $sql = "INSERT INTO prescriptions (appointment_id, prescription_date, drug, dose, amount, prescription_files) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isssss", $appointmentId, $prescriptionDate, $drug, $dose, $amount, $fileDestination);

                    // Execute the prepared statement
                    if ($stmt->execute()) {
                        // Prescription data inserted successfully
                        echo "<script>alert('Prescription submitted successfully.'); window.location.href = 'ddash.php';</script>";
                    } else {
                        echo "Error submitting prescription: " . $conn->error;
                    }

                    // Close prepared statement
                    $stmt->close();
                } else {
                    echo "No appointments found.";
                }
            } else {
                echo "Error moving file to destination.";
            }
        } else {
            echo "Only PDF files are allowed.";
        }
    } else {
        echo "File size exceeds the limit (200KB).";
    }
} else {
    echo "Error uploading file.";
}

// Close database connection
$conn->close();
?>
