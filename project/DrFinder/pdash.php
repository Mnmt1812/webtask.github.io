<?php
// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if the 'patientName' session variable is set
  if (isset($_SESSION['patientName']) && isset($_SESSION['patientGender']) && isset($_SESSION['patientID'])) {
    // Fetch the patient's data from the session variables
    $patientID = $_SESSION['patientID'];
    $patientName = $_SESSION['patientName'];
    $patientGender = $_SESSION['patientGender'];

    // Database connection settings
    $servername = "localhost"; // Change this to your MySQL server hostname
    $username = "root"; // Change this to your MySQL username
    $password = ""; // Change this to your MySQL password
    $database = "docfinder"; // Change this to your MySQL database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    // Check if the form fields for selected doctors are set and not empty
    if (isset($_POST["selectedDoctorName"]) && isset($_POST["selectedDoctorID"]) && isset($_POST["selectedClinicName"]) && !empty($_POST["selectedDoctorName"]) && !empty($_POST["selectedDoctorID"]) && !empty($_POST["selectedClinicName"])) {
      // Get the selected doctor names, IDs, and clinic names
      $selectedDoctorNames = $_POST["selectedDoctorName"];
      $selectedDoctorIDs = $_POST["selectedDoctorID"];
      $selectedClinicNames = $_POST["selectedClinicName"];

      // Loop through selected doctors
      for ($i = 0; $i < count($selectedDoctorNames); $i++) {
        // Validate and sanitize the input fields for each doctor and clinic
        $address = htmlspecialchars($_POST["address"]);
        $city = htmlspecialchars($_POST["city"]);
        $symptoms = htmlspecialchars($_POST["symptoms"]);
        $disease = htmlspecialchars($_POST["disease"]);
        $selectedDoctorName = htmlspecialchars($selectedDoctorNames[$i]);
        $selectedDoctorID = htmlspecialchars($selectedDoctorIDs[$i]);
        $selectedClinicName = htmlspecialchars($selectedClinicNames[$i]); // Retrieve clinic name
        $appointmentDate = htmlspecialchars($_POST["appointmentDate"]);
        $appointmentTime = htmlspecialchars($_POST["appointmentTime"]);

        // Check if the selected doctor ID exists in the doctors table
        $checkDoctorQuery = "SELECT id FROM doctors WHERE id = '$selectedDoctorID'";
        $checkDoctorResult = $conn->query($checkDoctorQuery);

        if ($checkDoctorResult->num_rows > 0) {
          // Insert patient appointment data into the database for each doctor and clinic
          $sql = "INSERT INTO appointments (patient_id, patient_name, patient_gender, address, city, symptoms, disease, doctor_name, doctor_id, clinic_name, appointment_date, appointment_time) 
              VALUES ('$patientID', '$patientName', '$patientGender', '$address', '$city', '$symptoms', '$disease', '$selectedDoctorName', '$selectedDoctorID', '$selectedClinicName', '$appointmentDate', '$appointmentTime')";

          if ($conn->query($sql) === TRUE) {
            // Appointment successfully booked
            echo "<script>alert('Appointment booked successfully.');</script>";
            echo "<script>window.location.href = window.location.href;</script>"; // Prevent form resubmission
            exit();
          } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
          }
        } else {
          echo "Error: Selected doctor ID doesn't exist.";
        }
      }
    } else {
      echo "Error: Selected doctor or clinic information not found or empty.";
    }

    $conn->close();
  } else {
    echo "Patient information not found. Please login again.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Page</title>
  <!-- Bootstrap CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="css/pdash.css">
  <style>
    .search-container {
      float: right;

    }

    .search-container input[type=text] {
      padding: 10px;
      width: 400px;
      border: none;
      border-radius: 5px;
      background-color: #f2f2f2;
      font-size: 16px;
    }

    .search-container input[type=text]:focus {
      outline: none;
      background-color: #ddd;
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
          if (isset($_SESSION['patientName'])) {

            echo '<a class="nav-link" href="#">Welcome! ' . $_SESSION['patientName'] . '</a>';
          } else {
            echo '<a class="nav-link" href="#">Welcome! Guest</a>';
          }
          ?>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="plogin.html">(Logout)</a>
        </li>
      </ul>
    </div>
  </nav>
  <aside class="sidebar">
    <div>
      <a href="#" class="list-group-item list-group-item-action" id="dashboard-link"><i class="fas fa-tachometer-alt icon"></i> Dashboard</a>
      <a href="#" class="list-group-item list-group-item-action" id="edit-profile-link"><i class="fas fa-user-edit icon"></i> Edit Profile</a>
      <a href="#" class="list-group-item list-group-item-action" id="book-appointment-link"><i class="fas fa-calendar-plus icon"></i> Book Appointment</a>
      <a href="#" class="list-group-item list-group-item-action" id="appointment-status-link"><i class="fas fa-calendar-check icon"></i> Appointment Status</a>
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
                <span>Edit Profile</span>
              </a>
            </td>
          </tr>
          <tr>
            <td>
              <a href="#" class="link">
                <i class="fas fa-calendar-check icon"></i>
                <span>Book appointment</span>
              </a>
            </td>
            <!-- Add more columns for additional links -->
          </tr>
          <tr>
            <td>
              <a href="#" class="link">
                <i class="fas fa-calendar-check icon"></i>
                <span>Appointment Status</span>
              </a>
            </td>
            <!-- Add more columns for additional links -->
          </tr>
        </tbody>
      </table>
    </section>
    <section id="edit-profile-section" class="edit-profile-form">
      <form action="pdash.php" id="editProfileForm">
        <table class="profile-table">
          <tr>
            <td colspan="3" class="section-header">
              <h2><b>Edit Profile</b></h2>
            </td>
          </tr>
          <tr>
            <td><label for="patientName">Patient Name:</label></td>
            <td>
              <input type="text" class="form-control" id="patientName" name="patientName" required disabled value="<?php echo isset($_SESSION['patientName']) ? $_SESSION['patientName'] : ''; ?>">
            </td>
            <td>
              <input type="text" class="form-control" id="newPatientName" name="newPatientName" placeholder="Patient Name">
            </td>
          </tr>
          <tr>
            <td><label for="phoneNo">Phone No:</label></td>
            <td>
              <input type="tel" class="form-control" id="phoneNo" name="phoneNo" pattern="[0-9]{10}" required disabled value="<?php echo isset($_SESSION['phoneNo']) ? $_SESSION['phoneNo'] : ''; ?>">
            </td>
            <td>
              <input type="tel" class="form-control" id="newPhoneNo" name="newPhoneNo" pattern="[0-9]{10}" placeholder="Phone No">
            </td>
          </tr>
          <tr>
            <td><label for="email">Email:</label></td>
            <td>
              <input type="email" class="form-control" id="email" name="email" required disabled value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>">
            </td>
            <td>
              <input type="email" class="form-control" id="newEmail" name="newEmail" placeholder="Email">
            </td>
          </tr>
          <tr>
            <td><label for="gender">Gender:</label></td>
            <td>
              <input type="text" class="form-control" id="gender" name="gender" required disabled value="<?php echo isset($_SESSION['patientGender']) ? ucfirst($_SESSION['patientGender']) : ''; ?>">
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
              <input type="date" class="form-control" id="dob" name="dob" required disabled value="<?php echo isset($_SESSION['dob']) ? $_SESSION['dob'] : ''; ?>">
            </td>
            <td>
              <input type="date" class="form-control" id="newDob" name="newDob" placeholder="Date of Birth">
            </td>
          </tr>
          <tr>
            <td><label for="password">New Password:</label></td>
            <td><input type="password" class="form-control" id="password" name="password"></td>
            <td><input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password"></td>
          </tr>
          <tr>
            <td colspan="2"><span id="passwordMatchError" class="error-message"></span></td>
          </tr>
          <tr>
            <td colspan="3" style="text-align: center;"><button type="submit" class="btn btn-primary">Update</button></td>
          </tr>
        </table>
      </form>
    </section>


    <section id="book-appointment-section" class="book-appointment-form">
      <div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <table class="appointment-table">
            <tr>
              <td colspan="2" class="section-header">
                <h2><b style="margin-left: 400px;">Book Appointment</b>
                  <div class="search-container">
                    <input type="text" id="searchInput" onkeyup="searchClinics()" placeholder="Search for clinic">
                  </div>
                </h2>

              </td>
            </tr>
            <tr>
              <td>
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address" required>
              </td>
              <td>
                <label for="city">City:</label>
                <select class="form-control" id="city" name="city" onchange="toggleCityInput(); filterDoctorsByCity();" required>
                  <option value="">Select your city</option>
                  <option value="Sasaram">Sasaram</option>
                  <option value="Karahgarh">Karahgarh</option>
                  <option value="Konar">Konar</option>
                  <option value="Takiya Bazar">Takiya Bazar</option>
                  <option value="Other">Other</option> 
                </select>
                <input type="text" class="form-control" id="otherCityInput" name="otherCity" placeholder="Enter Other City" style="display: none;">
              </td>

            </tr>
            <tr>
              <td>
                <label for="symptoms">Symptoms:</label>
                <textarea class="form-control" id="symptoms" name="symptoms" rows="4" placeholder="Enter your symptoms" maxlength="100" required></textarea>
              </td>
              <td>
                <label for="disease">Disease:</label>
                <input type="text" class="form-control" id="disease" name="disease" placeholder="Enter your disease" required>
              </td>
            </tr>

          </table>
          <div id="appointmentModal" class="modal">
            <div class="modal-content">
              <span class="close" onclick="closeAppointmentPopup()">&times;</span>

              <table>
                <tr>
                  <td colspan="2" class="section-header">
                    <h2><b>Book an Appointment Slot</b></h2>
                  </td>
                </tr>
                <tr>
                  <td><label for="appointmentDate">Select Date:</label></td>
                  <td><input type="date" id="appointmentDate" name="appointmentDate" required></td>
                </tr>
                <tr>
                  <td><label for="appointmentTime">Select Time:</label></td>
                  <td><input type="time" id="appointmentTime" name="appointmentTime" required></td>
                </tr>
                <tr>
                  <td><label for="amount">Amount (Rs):</label></td>
                  <td><input type="text" id="amount" name="amount" value="500" readonly disabled></td>
                </tr>
                <tr>
                  <td colspan="2" style="text-align: center;"><button type="submit" name="bookAppointment" onclick="submitForm(this.form)" class="btn btn-primary">Book Appointment</button>

                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div>
            <table id="clinicTable" class="profile-table">
              <tr>
                <td colspan="3" class="section-header">
                  <h2><b>Doctor Profile</b></h2>
                </td>
              </tr>
              <?php
              $servername = "localhost";
              $username = "root";
              $password = "";
              $database = "docfinder";
              $conn = new mysqli($servername, $username, $password, $database);
              if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
              }
              if (isset($_POST['city']) && !empty($_POST['city'])) {
                $selectedCity = $_POST['city'];
                $sql = "SELECT * FROM doctors WHERE doctorcity = '$selectedCity'";
              } else {
                $sql = "SELECT * FROM doctors";
              }

              $result = $conn->query($sql);

              $doctorLicenseNumbers = [];
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $doctorLicenseNumbers[] = $row['licenceNo'];
                  echo '<tr class="doctor-profile" data-city="' . $row['doctorcity'] . '">';
                  echo '<td>';

                  $imageData = base64_encode($row['photo']);
                  echo '<img src="data:image/jpeg;base64,' . $imageData . '" class="profile-img" alt="Doctor Image">';
                  echo '</td>';
                  echo '<td>';
                  echo '<h4 class="card-title"><b>' . $row['clinicName'] . ',' . $row['doctorcity'] .  '</b></h4>';
                  echo '<p class="card-title"><i class="fas fa-user icon"></i>Name:</b> ' . $row['doctorname'] . '</b></p>';
                  echo '<p class="card-text"><i class="fas fa-venus-mars icon"></i>Gender: ' . $row['doctorgender'] . '</p>';
                  echo '<p class="card-text"><i class="fas fa-graduation-cap icon"></i>Degree: ' . $row['degree'] . '</p>';
                  echo '<p class="card-text"><i class="fas fa-user-md icon"></i>Specialization: ' . $row['doctorspecialist'] . '</p>';
                  echo '<p class="card-text"><i class="fas fa-clock icon"></i>Experience: ' . $row['experience'] . ' years</p>';
                  echo '<p class="card-text"><i class="far fa-envelope icon"></i>Email: ' . $row['doctoremail'] . '</p>';
                  echo '<p class="card-text"><i class="fas fa-id-card icon"></i>Registration Number: ' . $row['licenceNo'] . '</p>';
                  echo '<p class="card-text"><i class="fas fa-map-marker-alt icon"></i>Location: ' . $row['doctorcity'] . '</p>';
                  echo '<p class="card-text"><i class="far fa-calendar-alt icon"></i>Availability: ' . $row['weekdaysAvailability'] . '-' . $row['weekendsAvailability'] . '</p>';
                  echo '<p class="card-text"><i class="fas fa-clock icon"></i>Lunch Time: ' . $row['lunchtime'] . '</p>';

                  echo '<input type="hidden" name="selectedDoctorName[]" value="' . $row['doctorname'] . '">';
                  echo '<input type="hidden" name="selectedDoctorID[]" value="' . $row['id'] . '">';
                  echo '<input type="hidden" name="selectedClinicName[]" value="' . $row['clinicName'] . '">';
                  echo '<button onclick="openAppointmentPopup()" class="btn btn-primary btn-book-appointment">Select</button>';

                  echo '</td>';
                  echo '</tr>';
                }

                $found = true;
              }

              if (!$found) {

                if (!isset($_POST['city']) || empty($_POST['city'])) {
                  echo '<tr><td colspan="2"></td></tr>';
                } else {

                  echo '<tr><td colspan="2">No clinics found</td></tr>';
                }
              }
              $conn->close();
              ?>

            </table>
          </div>
        </form>
      </div>
    </section>


    <section id="appointment-status-section" class="appointment-status content">
      <!-- Appointment Status Content Goes Here -->

      <table class="appointment-table">
        <thead>
          <tr>
            <th colspan="9" style="text-align: center;">
              <h2><b>Appointment Status</b></h2>
            </th>
          </tr>
          <tr>
            <th>Sl. No</th>
            <th>Doctor Name</th>
            <th>Clinic Name</th>
            <th>Appointment Date</th>
            <th>Time</th>
            <th>Location</th>
            <th>Symptoms</th>
            <th>Disease</th>
            <th>Status</th>
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
        $sql = "SELECT appointments.*, doctors.doctorname, doctors.clinicName
                FROM appointments
                INNER JOIN doctors ON appointments.doctor_id = doctors.id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          $count = 1;
          // Output data of each row
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $count . "</td>";
            echo "<td>" . $row['doctorname'] . "</td>";
            echo "<td>" . $row['clinicName'] . "</td>";
            echo "<td>" . $row['appointment_date'] . "</td>";
            echo "<td>" . $row['appointment_time'] . "</td>";
            echo "<td>" . $row['address'] . "</td>";
            echo "<td>" . $row['symptoms'] . "</td>";
            echo "<td>" . $row['disease'] . "</td>";
            echo "<td>";
            if ($row['status'] == 'Approved') {
              echo "Confirmed";
            } elseif ($row['status'] == 'Rejected') {
              echo "Rejected";
            } else {
              echo "Pending";
            }
            echo "</td>";
            echo "</tr>";
            $count++;
          }
        } else {
          echo "<tr><td colspan='9'>No appointments found</td></tr>";
        }
        $conn->close();
        ?>
      </table>
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

  <script src="js/pdash.js"></script>
  <script>
    function toggleCityInput() {
      var cityDropdown = document.getElementById("city");
      var otherCityInput = document.getElementById("otherCityInput");

      if (cityDropdown.value === "Other") {
        otherCityInput.style.display = "block";
      } else {
        otherCityInput.style.display = "none";
      }
    }
  </script>
  <script>
    // Function to submit form when a doctor is selected
    function submitForm(doctorForm) {
      doctorForm.submit();
    }
  </script>
  <script>
    function searchClinics() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("searchInput");
      filter = input.value.toUpperCase();
      table = document.getElementById("clinicTable");
      tr = table.getElementsByTagName("tr");

      var found = false; // Flag to track if any clinics are found

      // Check if the message row already exists and remove it if found
      var messageRow = document.getElementById("noClinicsMessage");
      if (messageRow) {
        messageRow.remove();
      }

      // Loop through all table rows, and hide those that don't match the search query
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1]; // Assuming the clinic name is in the second column
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
            found = true; // Set found flag to true
          } else {
            tr[i].style.display = "none";
          }
        }
      }

      // If no clinics are found, display a message
      if (!found) {
        var messageRow = document.createElement("tr");
        messageRow.setAttribute("id", "noClinicsMessage");
        var messageCell = document.createElement("td");
        messageCell.setAttribute("colspan", "2");
        messageCell.textContent = "No clinics found matching the search criteria.";
        messageRow.appendChild(messageCell);
        table.appendChild(messageRow);
      }
    }
  </script>
  <script>
    function toggleCityInput() {
      var citySelect = document.getElementById("city");
      var otherCityInput = document.getElementById("otherCityInput");

      if (citySelect.value === "Other") {
        otherCityInput.style.display = "block";
      } else {
        otherCityInput.style.display = "none";
      }
    }

    function filterDoctorsByCity() {
      var citySelect = document.getElementById("city");
      var selectedCity = citySelect.value;
      var doctorProfiles = document.getElementsByClassName("doctor-profile");
      var otherCityInput = document.getElementById("otherCityInput");
      var inputCity = otherCityInput.value;

      for (var i = 0; i < doctorProfiles.length; i++) {
        var profileCity = doctorProfiles[i].getAttribute("data-city");

        // Check if the selected city is "Other" and inputCity is not empty
        if (selectedCity === "Other" && inputCity.trim() !== "") {
          // Compare the input city with the doctor's city
          if (profileCity.toLowerCase() === inputCity.toLowerCase()) {
            doctorProfiles[i].style.display = "table-row";
          } else {
            doctorProfiles[i].style.display = "none";
          }
        } else {
          // Compare the selected city with the doctor's city
          if (selectedCity === profileCity) {
            doctorProfiles[i].style.display = "table-row";
          } else {
            doctorProfiles[i].style.display = "none";
          }
        }
      }
    }

    function openAppointmentPopup() {
      // Get the appointment modal element
      var modal = document.getElementById("appointmentModal");

      // Display the modal by changing its style display property to "block"
      modal.style.display = "block";
    }

    function closeAppointmentPopup() {
      // Hide the appointment modal
      var appointmentModal = document.getElementById("appointmentModal");
      appointmentModal.style.display = "none";
    }
  </script>
</body>

</html>