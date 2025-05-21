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
$treatmentType = $_POST['treatmentType'];
$description = $_POST['description'];
$treatmentDate = $_POST['treatmentDate'];

// Handle other treatment type if selected
if ($treatmentType == 'Other') {
    $otherTreatmentInput = $_POST['otherTreatmentInput'];
    // Use $otherTreatmentInput as needed
}
// Handle other treatment type if selected
if ($treatmentType == 'Other') {
    $otherTreatmentInput = $_POST['otherTreatmentInput'];
    // Use $otherTreatmentInput as the treatment type
    $treatmentType = $otherTreatmentInput;
}

// File upload handling
$treatmentFiles = $_FILES['treatmentFiles'];
$fileName = $treatmentFiles['name'];
$fileTmpName = $treatmentFiles['tmp_name'];
$fileError = $treatmentFiles['error'];

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

                    // Insert treatment data into the database
                    $sql = "INSERT INTO treatments (appointment_id, treatment_type, description, treatment_date, treatment_files) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("issss", $appointmentId, $treatmentType, $description, $treatmentDate, $fileDestination);

                    // Execute the prepared statement
                    if ($stmt->execute()) {
                        // Treatment submitted successfully
                        echo "<script>alert('Treatment submitted successfully.'); window.location.href = 'ddash.php';</script>";
                        exit; // Exit to prevent further execution
                    } else {
                        echo "Error submitting treatment: " . $conn->error;
                    }

                    // Close prepared statement for treatment insertion
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
