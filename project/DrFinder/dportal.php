<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection and insertion code
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "docfinder";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check for duplicate email, license number, and phone number
    $doctoremail = $_POST['email'];
    $licenceNo = $_POST['licenceNo'];
    $doctorphone = $_POST['phone'];

    $checkEmailQuery = "SELECT * FROM doctors WHERE doctoremail = ?";
    $stmtCheckEmail = $conn->prepare($checkEmailQuery);
    $stmtCheckEmail->bind_param("s", $doctoremail);
    $stmtCheckEmail->execute();
    $resultEmail = $stmtCheckEmail->get_result();

    $checkLicenceNoQuery = "SELECT * FROM doctors WHERE licenceno = ?";
    $stmtCheckLicenceNo = $conn->prepare($checkLicenceNoQuery);
    $stmtCheckLicenceNo->bind_param("s", $licenceNo);
    $stmtCheckLicenceNo->execute();
    $resultLicenceNo = $stmtCheckLicenceNo->get_result();

    $checkphoneQuery = "SELECT * FROM doctors WHERE doctorphone = ?";
    $stmtCheckphone = $conn->prepare($checkphoneQuery);
    $stmtCheckphone->bind_param("s", $doctorphone);
    $stmtCheckphone->execute();
    $resultphone = $stmtCheckphone->get_result();

    if ($resultEmail->num_rows > 0 || $resultLicenceNo->num_rows > 0 || $resultphone->num_rows > 0) {
        echo "<script>alert('Email, license number, or phone number already exists! Please use different details.');window.location.href = 'dlogin.html';</script>";
        exit;
    }


    // Prepare and bind parameters for insertion
    $stmt = $conn->prepare("INSERT INTO doctors (doctorname, doctorgender, doctoremail, doctorspecialist, experience, weekdaysAvailability, lunchtime, doctorDob, doctorphone, doctorcity, degree, weekendsAvailability, licenceNo, doctorpassword, photo, clinicName) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssssssssssss", $doctorname, $doctorgender, $doctoremail, $doctorspecialist, $experience, $weekdaysAvailability, $lunchtime, $doctorDob, $doctorphone, $doctorcity, $degree, $weekendsAvailability, $licenceNo, $doctorpassword, $photo, $clinicName); // Bind clinicName parameter


    // Retrieve form data
    $doctorname = $_POST['doctorName'];
    $doctorgender = $_POST["gender"];
    $doctoremail = $_POST["email"];
    $doctorspecialist = ($_POST["specialist"] == "Other") ? $_POST["otherSpecialist"] : $_POST["specialist"];
    $experience = $_POST["experience"];
    $weekdaysAvailability = $_POST["weekdaysAvailability"];
    $lunchtime = $_POST["lunchtime"];
    $doctorDob = $_POST["Dob"];
    $doctorphone = $_POST["phone"];
    $doctorcity = ($_POST["city"] == "Other") ? $_POST["otherCity"] : $_POST["city"];
    $degree = $_POST["degree"];
    $weekendsAvailability = $_POST["weekendsAvailability"];
    $licenceNo = $_POST["licenceNo"];
    $doctorpassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $clinicName = $_POST["clinicName"];

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Check file type
        $allowedTypes = ['image/jpeg'];
        $fileType = $_FILES['photo']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            echo "<script>alert('Only JPEG files are allowed.');</script>";
            exit;
        }

        // Check file size
        $maxFileSize = 50 * 1024; // 50 KB in bytes
        $fileSize = $_FILES['photo']['size'];

        if ($fileSize > $maxFileSize) {
            echo "<script>alert('File size exceeds the maximum limit of 50 KB.');</script>";
            exit;
        }

        // Upload directory and file path
        $uploadDir = 'uploads/';
        $photoName = $_FILES['photo']['name'];
        $photoPath = $uploadDir . basename($photoName);

        // Move uploaded file to the uploads directory
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
            // Read file contents
            $photo = file_get_contents($photoPath);
        } else {
            echo "<script>alert('Failed to move uploaded file. Please try again.');</script>";
            exit;
        }
    } else {
        echo "<script>alert('Error uploading photo. Please try again.');</script>";
        exit;
    }

    // Execute insertion query
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href = 'dlogin.html';</script>";
        exit;
    } else {
        echo "<script>alert('Registration failed! Please try again.');</script>";
    }

    // Close statements and connection
    $stmt->close();
    $stmtCheckEmail->close();
    $stmtCheckLicenceNo->close();
    $stmtCheckphone->close();
    $conn->close();
}
