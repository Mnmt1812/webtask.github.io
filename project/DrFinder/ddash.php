<?php
session_start();

// Check if 'doctorId' is set in the session and is not empty
if (isset($_SESSION['doctorId']) && !empty($_SESSION['doctorId'])) {
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

  if (isset($_POST['approve']) && isset($_POST['appointment_id'])) {
    $appointmentId = $_POST['appointment_id'];

    // Update appointment status to 'Approved' in the database
    $sql = "UPDATE appointments SET status = 'Approved' WHERE appointment_id = $appointmentId";

    if ($conn->query($sql) === TRUE) {
      echo "Appointment approved successfully.";
    } else {
      echo "Error updating appointment status: " . $conn->error;
    }
  } elseif (isset($_POST['deny']) && isset($_POST['appointment_id'])) {
    $appointmentId = $_POST['appointment_id'];

    // Update appointment status to 'Rejected' in the database
    $sql = "UPDATE appointments SET status = 'Rejected' WHERE appointment_id = $appointmentId";

    if ($conn->query($sql) === TRUE) {
      echo "Appointment denied and status updated successfully.";
    } else {
      echo "Error updating appointment status: " . $conn->error;
    }
  }

  // Fetch doctor's details from the database
  $sql = "SELECT * FROM doctors WHERE id = " . $_SESSION['doctorId'];
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // Fetching doctor's details
    $row = $result->fetch_assoc();

    // Set session variables for doctor details
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
  } else {
    echo "Doctor details not found!";
  }

  $conn->close();
} else {
  echo "Doctor ID not set in the session.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Page</title>
  <!-- Bootstrap CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="css/ddash.css">
  <style>
    /* Style for the approve button */
    .btn-success {
      background-color: #28a745;
      /* Green color */
      border-color: #28a745;
      margin-right: 10px;
    }

    /* Style for the deny button */
    .btn-danger {
      background-color: #dc3545;
      /* Red color */
      border-color: #dc3545;
    }

    /* Initially hide treatment and prescription forms */
    .treatment,
    .prescription1 {
      display: none;
    }

    .search-container {
      float: right;

    }

    .search-container input[type=text] {
      padding: 10px;
      width: 400px;
      border: none;
      border-radius: 5px;
      background-color: white;
      font-size: 16px;
    }

    .search-container input[type=text]:focus {
      outline: none;
      background-color: #ddd;
    }

    .highlight {
      background-color: blue;
      color: white;
    }
  </style>

</head>

<body>

  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-info">
    <a class="navbar-brand logo" href="#"> DocFinder Plus <p>Health Access</p></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <?php
          if (isset($_SESSION['doctorName'])) {
           
            echo '<a class="nav-link" href="#">Welcome! ' . $_SESSION['doctorName'] . '</a>';
          } else {
           
            echo '<a class="nav-link" href="#">Welcome! Guest</a>';
          }
          ?>

        </li>
        <li class="nav-item">
          <a class="nav-link" href="dlogin.html">(Logout)</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div>
      <a href="#" class="list-group-item list-group-item-action" id="dashboard-link"><i class="fas fa-tachometer-alt icon"></i> Dashboard</a>
      <a href="#" class="list-group-item list-group-item-action" id="manage-profile-link"><i class="fas fa-user-edit icon"></i> Manage Profile</a>
      <a href="#" class="list-group-item list-group-item-action" id="appointment-link"><i class="fas fa-calendar-plus icon"></i> Appointment</a>
      <a href="#" class="list-group-item list-group-item-action" id="patient-profile-link"><i class="fas fa-calendar-check icon"></i> Patient Profile</a>
      <a href="#" class="list-group-item list-group-item-action" id="treatment-record-link"><i class="fas fa-notes-medical icon"></i> Treatment Record</a>
    </div>

  </aside>

  <div class="content">
    <section id="dashboard-section" class="dashboard-section">
      <table class="dashboard-table">
        <thead>
          <tr>
            <th colspan="2" class="section-header">
              <h2><b>Dashboard</b></h2>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <a href="#" class="link">
                <i class="fas fa-user icon"></i>
                <span>Manage Profile</span>
              </a>
            </td>
          </tr>
          <tr>
            <td>
              <a href="#" class="link">
                <i class="fas fa-calendar-check icon"></i>
                <span> Appointment</span>
              </a>
            </td>
            <!-- Add more columns for additional links -->
          </tr>
          <tr>
            <td>
              <a href="#" class="link">
                <i class="fas fa-calendar-check icon"></i>
                <span>Patient Profile</span>
              </a>
            </td>
            <!-- Add more columns for additional links -->
          </tr>
          <tr>
            <td>
              <a href="#" class="link">
                <i class="fas fa-notes-medical icon"></i>
                <span>Treatment Record</span>
              </a>
            </td>
          </tr>
          <tr>
        </tbody>
      </table>
    </section>
    <section id="manage-profile-section" class="manage-profile-form">
      <form id="manageprofileform">
        <table class="profile-table">
          <tr>
            <td colspan="4" class="section-header">
              <h2><b>Manage Profile</b></h2>
            </td>
          </tr>
          <tr>
            <td rowspan="12" class="profile-photo">
              <?php
              $imageData = base64_encode($_SESSION['doctorPhoto']);
              echo '<img src="data:image/jpeg;base64,' . $imageData . '" class="profile-img" alt="Profile Photo">';
              ?>
            </td>
          </tr>
          <tr>
            <td><label for="clinicName">Clinic Name:</label></td>
            <td>
              <input type="text" class="form-control" id="clinicName" name="clinicName" required disabled value="<?php echo $_SESSION['clinicName']; ?>">
            </td>
            <td>
              <input type="text" class="form-control" id="newdoctorName" name="newdoctorName" placeholder="Doctor Name">
            </td>
          </tr>
          <tr>
            <td><label for="doctorName">Doctor Name:</label></td>
            <td>
              <input type="text" class="form-control" id="doctorName" name="doctorName" required disabled value="<?php echo $_SESSION['doctorName']; ?>">
            </td>
            <td>
              <input type="text" class="form-control" id="newdoctorName" name="newdoctorName" placeholder="Doctor Name">
            </td>
          </tr>

          <tr>
            <td><label for="phoneNo">Phone No:</label></td>
            <td>
              <input type="tel" class="form-control" id="phoneNo" name="phoneNo" pattern="[0-9]{10}" required disabled value="<?php echo $_SESSION['doctorPhone']; ?>">
            </td>
            <td>
              <input type="tel" class="form-control" id="newPhoneNo" name="newPhoneNo" pattern="[0-9]{10}" placeholder="Phone No">
            </td>
          </tr>
          <tr>
            <td><label for="email">Email:</label></td>
            <td>
              <input type="email" class="form-control" id="email" name="email" required disabled value="<?php echo $_SESSION['doctorEmail']; ?>">
            </td>
            <td>
              <input type="email" class="form-control" id="newEmail" name="newEmail" placeholder="Email">
            </td>
          </tr>
          <tr>
            <td><label for="gender">Gender:</label></td>
            <td>
              <select class="form-control" id="gender" name="gender" required disabled>
                <option value="">Select Gender</option>
                <option value="male" <?php if ($_SESSION['doctorGender'] === 'male') echo 'selected'; ?>>Male</option>
                <option value="female" <?php if ($_SESSION['doctorGender'] === 'female') echo 'selected'; ?>>Female</option>
                <option value="other" <?php if ($_SESSION['doctorGender'] === 'other') echo 'selected'; ?>>Other</option>
              </select>
            </td>
            <td>
              <select class="form-control" id="newGender" name="newGender" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </td>
          </tr>
          <tr>
            <td><label for="dob">Date of Birth:</label></td>
            <td>
              <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $_SESSION['doctorDob']; ?>" required disabled>
            </td>
            <td>
              <input type="date" class="form-control" id="newDob" name="newDob" placeholder="Date of Birth">
            </td>
          </tr>

          <tr>
            <td><label for="city">City:</label></td>
            <td>
              <input type="option" class="form-control" id="city" name="city" value="<?php echo $_SESSION['doctorCity']; ?>" required disabled>
            </td>
            <td>
              <select class="form-control" id="city" name="city" required>
                <option value="">Select City</option>
                <option value="New York">New York</option>
                <option value="Los Angeles">Los Angeles</option>
                <option value="Chicago">Chicago</option>
                <option value="other">Other</option>
              </select>
            </td>
          </tr>
          <tr>
            <td><label for="specialist">Specialist:</label></td>
            <td>
              <input type="option" class="form-control" id="specialist" name="specialist " value="<?php echo $_SESSION['doctorSpecialist']; ?>" required disabled>
            </td>
            <td>
              <select class="form-control" id="specialist" name="specialist" required>
                <option value="">Select Specialist</option>
                <option value="Cardiologist">Cardiologist</option>
                <option value="Dermatologist">Dermatologist</option>
                <option value="Neurologist">Neurologist</option>
                <option value="other">Other</option>
              </select>
            </td>
          </tr>
          <tr>
            <td><label for="degree">Degree:</label></td>

            <td>
              <input type="text" class="form-control" id="degree" name="degree" value="<?php echo $_SESSION['degree']; ?>" required disabled>
            </td>

            <td>
              <input type="text" class="form-control" id="newDegree" name="newDegree" placeholder="Degree">
            </td>
          </tr>
          <tr>
            <td><label for="registrationNo">Registration No:</label></td>
            <td>
              <input type="text" class="form-control" id="registrationNO" name="registrationNo" value="<?php echo $_SESSION['licenceNo']; ?>" required disabled>
            </td>
            <td>
              <input type="text" class="form-control" id="newRegistrationNo" name="newRegistrationNo" placeholder="Registration No">
            </td>
          </tr>
          <tr>
            <td><label for="photo">Upload Photo:</label></td>
            <td colspan="2">
              <input type="file" class="form-control" id="photo" name="photo">
            </td>
          </tr>
          <tr>
            <td></td>
            <td><label for="password">Password:</label></td>
            <td><input maxlength="8" type="password" class="form-control" id="password" name="password" placeholder="Abcd@123" required></td>
            <td><input maxlength="8" type="password" class="form-control" id="confirmPassword" name="confirmPassword" required placeholder="Confirm Password"></td>
          </tr>

          <tr>
            <td colspan="4" style="text-align: center;"><button type="submit" class="btn btn-primary">Update</button>
            </td>
          </tr>
        </table>
      </form>

    </section>


    <section id="appointment-section" class="appointment-form">
      <table id="appointment-table" class="appointment-table">
        <table class="table">
          <thead>
            <tr>
              <th colspan="8" style="text-align: center;">
                <h2><b>Appointment List</b></h2>
              </th>
            </tr>
            <tr>
              <th>Sl. No.</th>
              <th>Name</th>
              <th>City</th>
              <th>Gender</th>
              <th>Appointment Date</th>
              <th>Appointment Time</th>
              <th>Reason</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "docfinder";
            $conn = new mysqli($servername, $username, $password, $database);
            if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
            }
            $sql = "SELECT * FROM appointments WHERE status IS NULL OR status != 'Approved' AND status != 'Rejected'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
              $count = 1;

              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $count . "</td>";
                echo "<td>" . $row['patient_name'] . "</td>";
                echo "<td>" . $row['city'] . "</td>";
                echo "<td>" . $row['patient_gender'] . "</td>";
                echo "<td>" . $row['appointment_date'] . "</td>";
                echo "<td>" . $row['appointment_time'] . "</td>";
                echo "<td>" . $row['disease'] . "</td>";
                echo "<td>";
                echo '<button class="btn btn-success approve-btn" data-appointment-id="' . $row['appointment_id'] . '">Approve</button>';
                echo '<button class="btn btn-danger deny-btn" data-appointment-id="' . $row['appointment_id'] . '">Deny</button>';
                echo "</td>";
                echo "</tr>";
                $count++;
              }
            } else {
              echo "<tr><td colspan='8'>No pending appointments found</td></tr>";
            }

            $conn->close();
            ?>
          </tbody>
        </table>
      </table>
    </section>


    <section id="patient-profile-section" class="patient-profile">

      <table id="patient-profile-table" class="patient-profile-table">
        <thead>
          <tr>
            <th colspan="8" style="text-align: center;">
              <h2><b>Patient Profile</b></h2>
            </th>
          </tr>
          <tr>
            <th>Sl. No.</th>
            <th>Name</th>
            <th>City</th>
            <th>Gender</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Reason</th>
            <th>Action</th>
          </tr>
        </thead>
        <?php

        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "docfinder";

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM appointments WHERE status = 'approved'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          $count = 1;

          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $count . "</td>";
            echo "<td>" . $row['patient_name'] . "</td>";
            echo "<td>" . $row['city'] . "</td>";
            echo "<td>" . $row['patient_gender'] . "</td>";
            echo "<td>" . $row['appointment_date'] . "</td>";
            echo "<td>" . $row['appointment_time'] . "</td>";
            echo "<td>" . $row['disease'] . "</td>";
            echo "<td>";
            echo '<button class="btn btn-success">Treatment</button>';
            echo '<button class="btn btn-danger">Prescription</button>';
            echo "</td>";
            echo "</tr>";
            $count++;
          }
        } else {
          echo "<tr><td colspan='8'>No approved appointments found</td></tr>";
        }
        $conn->close();
        ?>

      </table>
      <div class="treatment">
        <h2><b>Treatment</b></h2>
        <form action="treatment.php" method="POST" enctype="multipart/form-data">
          <table class="table">
            <tbody>
              <tr>
                <td><label for="treatmentType">Treatment Type</label></td>
                <td>
                  <select class="form-control" name="treatmentType" id="treatmentType" required onchange="showInputField()">
                    <option value="">Select Treatment Type</option>
                    <option value="Physical Therapy">Physical Therapy</option>
                    <option value="Medication">Medication</option>
                    <option value="Surgery">Surgery</option>
                    <option value="Other">Other</option>
                    <!-- Add more treatment types as needed -->
                  </select>
                  <div id="otherTreatment" style="display: none;">
                    <input type="text" class="form-control" id="otherTreatmentInput" name="otherTreatmentInput" placeholder="Enter Other Treatment Type">
                  </div>
                </td>

              </tr>
              <tr>
                <td><label for="description">Description</label></td>
                <td><textarea class="form-control" name="description" id="description" rows="3" required></textarea></td>
              </tr>
              <tr>
                <td><label for="treatmentDate">Date</label></td>
                <td><input type="date" class="form-control" name="treatmentDate" id="treatmentDate" required></td>
              </tr>
              <tr>
                <td><label for="treatmentFiles">Upload Treatment Files</label></td>
                <td><input type="file" class="form-control-file" name="treatmentFiles" id="treatmentFiles"></td>
              </tr>
              <tr>
                <td colspan="3" style="text-align: center;"><button type="submit" class="btn btn-primary">Submit</button>
                </td>
              </tr>
            </tbody>
          </table>

        </form>
      </div>

      <div class="prescription">
        <h2><b>Prescriptions</b></h2>
        <form action="prescription.php" method="POST" enctype="multipart/form-data">
          <table class="table">
            <tbody>
              <tr>
                <td><label for="prescriptionDate">Date</label></td>
                <td><input type="date" class="form-control" name="prescriptionDate" id="prescriptionDate" required></td>
              </tr>
              <tr>
                <td><label for="drug">Drug</label></td>
                <td><input type="text" class="form-control" name="drug" id="drug" required></td>
              </tr>

              <tr>
                <td><label for="description">Dose</label></td>
                <td><textarea class="form-control" name="dose" id="dose" required></textarea></td>
              </tr>
              <tr>
                <td><label for="prescriptionFiles">Upload Prescription Files</label></td>
                <td><input type="file" class="form-control-file" name="prescriptionFiles" id="prescriptionFiles"></td>
              </tr>
              <tr>
                <td><label for="amount">Amount</label></td>
                <td><input type="number" class="form-control" name="amount" id="amount" required></td>
              </tr>
              <tr>
                <td colspan="2" style="text-align: center;">
                  <button type="submit" id="prescriptionSubmitBtn" class="btn btn-primary">Submit</button>
                </td>
              </tr>
            </tbody>
          </table>
        </form>
      </div>

    </section>
    <section id="Treatment-section" class="treatment-form">

      <table id="treatment-table" class="table">
        <thead>
          <tr>
            <th colspan="9" style="text-align: center;">
              <h2><b style="margin-left: 400px;">Treatment Record</b>
                <div class="search-container">
                  <input type="text" id="searchInput" onkeyup="searchPatients()" placeholder="Search for Patient">
                </div>
              </h2>
            </th>
          </tr>
          <tr>
            <th>Sl. No.</th>
            <th>Name</th>
            <th>City</th>
            <th>Gender</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Treatment</th>
            <th>Prescription</th>
          </tr>
        </thead>
        <tbody>
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

          // Fetch appointments from the database
          $sql = "SELECT * FROM appointments WHERE status = 'approved'";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $count . "</td>";
              echo "<td>" . $row["patient_name"] . "</td>";
              echo "<td>" . $row["city"] . "</td>";
              echo "<td>" . $row["patient_gender"] . "</td>";
              echo "<td>" . $row["appointment_date"] . "</td>";
              echo "<td>" . $row["appointment_time"] . "</td>";
              echo "<td><button class='btn btn-info treatment-open-btn' >Open</button></td>";
              echo "<td><button class='btn btn-info prescription-open-btn'>Open</button></td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='8'>No appointments found</td></tr>";
          }
          $conn->close();
          ?>
        </tbody>
      </table>
      <div class="treatment1">
        <h2><b>Treatment</b></h2>
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

        // Query to retrieve treatment data
        $sql = "SELECT * FROM treatments";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          $row = $result->fetch_assoc();
        ?>
          <form>
            <table class="table">
              <tbody>
                <tr>
                  <td><label for="treatmentType">Treatment Type</label></td>
                  <td>
                    <select class="form-control" id="treatmentType" required disabled>
                      <option value=""><?php echo $row['treatment_type']; ?></option>
                      <!-- Add more treatment types as needed -->
                    </select>
                  </td>
                </tr>
                <tr>
                  <td><label for="description">Description</label></td>
                  <td><textarea class="form-control" id="description" rows="3" required disabled><?php echo $row['description']; ?></textarea></td>
                </tr>
                <tr>
                  <td><label for="treatmentDate">Date</label></td>
                  <td><input type="date" class="form-control" id="treatmentDate" required value="<?php echo $row['treatment_date']; ?>" disabled></td>
                </tr>
                <tr>
                  <td><label for="treatmentFiles">Download Treatment Files</label></td>
                  <td><a href="<?php echo $row['treatment_files']; ?>" download>Download Files</a></td>
                </tr>
                <tr>
                  <td colspan="3" style="text-align: center;"><button type="submit" class="btn btn-primary">Back</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
        <?php
        } else {
          echo "No treatment records found.";
        }
        // Close database connection
        $conn->close();
        ?>
      </div>

      <div class="prescription1">
        <h2><b>Prescriptions</b></h2>
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

        // Query to retrieve prescription data from the database
        $sql = "SELECT * FROM prescriptions";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          // Fetch the prescription data
          $row = $result->fetch_assoc();
        ?>
          <form>
            <table class="table">
              <tbody>
                <tr>
                  <td><label for="prescriptionDate">Date</label></td>
                  <td><input type="date" class="form-control" id="prescriptionDate" value="<?php echo $row['prescription_date']; ?>" required disabled></td>
                </tr>
                <tr>
                  <td><label for="drug">Drug</label></td>
                  <td><input type="text" class="form-control" id="drug" value="<?php echo $row['drug']; ?>" required disabled> </td>
                </tr>
                <tr>
                  <td><label for="description">Dose</label></td>
                  <td><textarea class="form-control" id="dose" required disabled><?php echo $row['dose']; ?></textarea></td>
                </tr>
                <tr>
                  <td><label for="prescriptionFiles">Download Prescription Files</label></td>
                  <td><a href="<?php echo $row['prescription_files']; ?>" download>Download Files</a></td>
                </tr>
                <tr>
                  <td colspan="2" style="text-align: center;">
                    <button type="submit" class="btn btn-primary">Back</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
      </div>
    <?php
        } else {
          echo "No prescription records found.";
        }

        // Close database connection
        $conn->close();
    ?>
    </section>
  </div>


  <footer class="footer">
    <p>&copy; 2024 DocFinder Plus. All rights reserved.</p>
  </footer>

  <!-- Bootstrap JS and jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Font Awesome JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

  <script src="js/ddash.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Add event listeners to approve buttons
      var approveButtons = document.querySelectorAll('.approve-btn');
      approveButtons.forEach(function(button) {
        button.addEventListener('click', function() {
          var appointmentId = this.getAttribute('data-appointment-id');
          // Send AJAX request to update status to 'Approved'
          var xhr = new XMLHttpRequest();
          xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
              if (xhr.status === 200) {
                // Appointment approved successfully
                alert(xhr.responseText);
                // Remove the corresponding row from the table
                var row = button.closest('tr');
                row.parentNode.removeChild(row);
              } else {
                // Error approving appointment
                alert('Error: ' + xhr.responseText);
              }
            }
          };
          xhr.open('POST', 'update_status.php');
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          xhr.send('status=Approved&appointment_id=' + encodeURIComponent(appointmentId));
        });
      });

      // Add event listeners to deny buttons
      var denyButtons = document.querySelectorAll('.deny-btn');
      denyButtons.forEach(function(button) {
        button.addEventListener('click', function() {
          var appointmentId = this.getAttribute('data-appointment-id');
          // Send AJAX request to update status to 'Rejected'
          var xhr = new XMLHttpRequest();
          xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
              if (xhr.status === 200) {
                // Appointment denied and status updated successfully
                alert(xhr.responseText);
                // Remove the corresponding row from the table
                var row = button.closest('tr');
                row.parentNode.removeChild(row);
              } else {
                // Error denying appointment
                alert('Error: ' + xhr.responseText);
              }
            }
          };
          xhr.open('POST', 'update_status.php');
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          xhr.send('status=Rejected&appointment_id=' + encodeURIComponent(appointmentId));
        });
      });
    });
  </script>
  <script>
    function showInputField() {
      var selectBox = document.getElementById("treatmentType");
      var selectedValue = selectBox.options[selectBox.selectedIndex].value;
      if (selectedValue === "Other") {
        document.getElementById("otherTreatment").style.display = "block";
      } else {
        document.getElementById("otherTreatment").style.display = "none";
      }
    }
    document.addEventListener('DOMContentLoaded', function() {
      // Add event listeners to the treatment open buttons
      var treatmentOpenBtns = document.querySelectorAll('.treatment-open-btn');
      treatmentOpenBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
          // Show treatment form and hide prescription form
          document.querySelector('.treatment1').style.display = 'block';
          document.querySelector('.prescription1').style.display = 'none';
          // Scroll to the treatment section
          document.getElementById('Treatment-section').scrollIntoView();
        });
      });

      // Add event listeners to the prescription open buttons
      var prescriptionOpenBtns = document.querySelectorAll('.prescription-open-btn');
      prescriptionOpenBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
          // Show prescription form and hide treatment form
          document.querySelector('.prescription1').style.display = 'block';
          document.querySelector('.treatment1').style.display = 'none';
          // Scroll to the treatment section
          document.getElementById('Treatment-section').scrollIntoView();
        });
      });
    });

    function searchPatients() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("searchInput");
      filter = input.value.toUpperCase();
      table = document.getElementById("treatment-table"); // Assuming the table id is 'treatment-table'
      tr = table.getElementsByTagName("tr");

      var found = false; // Flag to track if any patients are found

      // Check if the message row already exists and remove it if found
      var messageRow = document.getElementById("noPatientsMessage");
      if (messageRow) {
        messageRow.remove();
      }

      // Loop through all table rows, and hide those that don't match the search query
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1]; // Assuming the patient name is in the second column
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            var startIndex = txtValue.toUpperCase().indexOf(filter);
            var endIndex = startIndex + filter.length;
            var newText = txtValue.substring(0, startIndex) +
              "<span class='highlight'>" + txtValue.substring(startIndex, endIndex) + "</span>" +
              txtValue.substring(endIndex);
            td.innerHTML = newText;
            tr[i].style.display = "";
            found = true; // Set found flag to true
          } else {
            tr[i].style.display = "none";
          }
        }
      }

      // If no patients are found, display a message
      if (!found) {
        var messageRow = document.createElement("tr");
        messageRow.setAttribute("id", "noPatientsMessage");
        var messageCell = document.createElement("td");
        messageCell.setAttribute("colspan", "8"); // Adjust colspan based on the number of columns in your table
        messageCell.textContent = "No patients found matching the search criteria.";
        messageRow.appendChild(messageCell);
        table.appendChild(messageRow);
      }
    }
  </script>
</body>

</html>