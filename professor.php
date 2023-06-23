<?php
session_start();

// Check if the user is logged in as a professor
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'professor') {
  header('Location: index.php');
  exit();
}

// Connect to the database
include('config/db_connect.php');

// Check the database connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if the form for updating the account is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateAccount'])) {
  // Retrieve the form data
  $username = $_SESSION['username'];
  $newUsername = $_POST['newUsername'];
  $oldPassword = $_POST['oldPassword'];
  $newPassword = $_POST['newPassword'];
  $confirmNewPassword = $_POST['confirmNewPassword'];

  // Verify if the old password is correct
  $verifySql = "SELECT password FROM users WHERE username = ? AND role = 'professor'";
  $verifyStmt = $conn->prepare($verifySql);

  if (!$verifyStmt) {
    // Error preparing statement
    $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update professor account.');
    header('Location: professor.php');
    exit();
  }

  $verifyStmt->bind_param("s", $username);
  $verifyStmt->execute();
  $verifyResult = $verifyStmt->get_result();

  if ($verifyResult && $verifyResult->num_rows > 0) {
    $row = $verifyResult->fetch_assoc();
    $hashedPassword = $row['password'];

    if (password_verify($oldPassword, $hashedPassword)) {
      // Old password is correct
      if ($newPassword === $confirmNewPassword) {
        // Hash the new password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Check if a new username is provided
        if (!empty($newUsername) && $newUsername !== $username) {
          // Check if the new username already exists in the database
          $checkUsernameSql = "SELECT * FROM users WHERE username = ? AND role = 'professor'";
          $checkUsernameStmt = $conn->prepare($checkUsernameSql);

          if (!$checkUsernameStmt) {
            // Error preparing statement
            $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update professor account.');
            header('Location: professor.php');
            exit();
          }

          $checkUsernameStmt->bind_param("s", $newUsername);
          $checkUsernameStmt->execute();
          $checkUsernameResult = $checkUsernameStmt->get_result();

          if ($checkUsernameResult && $checkUsernameResult->num_rows > 0) {
            // Username already exists
            $_SESSION['notification'] = array('type' => 'error', 'message' => 'The new username is already taken. Please choose a different username.');
            header('Location: professor.php');
            exit();
          }
        }

        // Update the professor account with the new username and/or password
        $updateSql = "UPDATE users SET ";

        if (!empty($newUsername)) {
          $updateSql .= "username = ?, ";
        }

        $updateSql .= "password = ? WHERE username = ? AND role = 'professor'";
        $updateStmt = $conn->prepare($updateSql);

        if (!$updateStmt) {
          // Error preparing statement
          $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update your account.');
          header('Location: professor.php');
          exit();
        }

        if (!empty($newUsername)) {
          $updateStmt->bind_param("sss", $newUsername, $hashedNewPassword, $username);
        } else {
          $updateStmt->bind_param("ss", $hashedNewPassword, $username);
        }

        if ($updateStmt->execute()) {
          // Account updated successfully
          $_SESSION['notification'] = array('type' => 'success', 'message' => 'Successfully updated your account.');
          header('Location: professor.php');
          exit();
        } else {
          // Error updating account
          $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update your account.');
          header('Location: professor.php');
          exit();
        }
      } else {
        // New password and confirm password do not match
        $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update your account: New passwords do not match.');
        header('Location: professor.php');
        exit();
      }
    } else {
      // Old password is incorrect
      $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update your account: Incorrect old password.');
      header('Location: professor.php');
      exit();
    }
  }
}


// Check if the form for adding a student account is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addAccount'])) {
  // Retrieve the form data
  $studentNo = str_pad($_POST['studentNo'], 8, '0', STR_PAD_LEFT);
  $username = $studentNo; // Use the student number as the username
  $password = $_POST['password'];
  $role = 'student'; // Assuming 'role' is hardcoded as 'student'

  // Hash the password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Check if the username already exists in the database
  $checkAccountSql = "SELECT * FROM users WHERE username = ?";
  $checkAccountStmt = $conn->prepare($checkAccountSql);

  if (!$checkAccountStmt) {
    // Error preparing statement
    $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to add student account.');
    header('Location: professor.php');
    exit();
  }

  $checkAccountStmt->bind_param("s", $username);
  $checkAccountStmt->execute();
  $checkAccountResult = $checkAccountStmt->get_result();

  if ($checkAccountResult && $checkAccountResult->num_rows > 0) {
    // Username already exists
    $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to add student account: Username already exists.');
    header('Location: professor.php');
    exit();
  } else {
    // Insert the new account into the database
    $insertAccountSql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)"; // Use parameterized query
    $insertAccountStmt = $conn->prepare($insertAccountSql);

    if (!$insertAccountStmt) {
      // Error preparing statement
      $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to add student account.');
      header('Location: professor.php');
      exit();
    }

    $insertAccountStmt->bind_param("sss", $username, $hashedPassword, $role);

    if ($insertAccountStmt->execute()) {
      // Account added successfully
      $_SESSION['notification'] = array('type' => 'success', 'message' => 'Successfully added student account.');
      header('Location: professor.php');
      exit();
    } else {
      // Error adding account
      $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to add student account.');
      header('Location: professor.php');
      exit();
    }

    $insertAccountStmt->close();
  }

  $checkAccountStmt->close();
}



// Check if the form for editing a student account is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editAccount'])) {
  // Retrieve the form data
  $studentNo = str_pad($_POST['studentNo'], 8, '0', STR_PAD_LEFT);
  $username = $studentNo; // Use the student number as the username
  $oldPassword = $_POST['oldPassword'];
  $newPassword = $_POST['newPassword'];
  $confirmNewPassword = $_POST['confirmNewPassword'];

  // Verify if the old password is correct
  $verifySql = "SELECT password FROM users WHERE username = ?";
  $verifyStmt = $conn->prepare($verifySql);

  if (!$verifyStmt) {
    // Error preparing statement
    $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update student account.');
    header('Location: professor.php');
    exit();
  }

  $verifyStmt->bind_param("s", $username);
  $verifyStmt->execute();
  $verifyResult = $verifyStmt->get_result();

  if ($verifyResult && $verifyResult->num_rows > 0) {
    $row = $verifyResult->fetch_assoc();
    $hashedPassword = $row['password'];

    if (password_verify($oldPassword, $hashedPassword)) {
      // Old password is correct
      if ($newPassword === $confirmNewPassword) {
        // Hash the new password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the student account with the new password
        $updateSql = "UPDATE users SET password = ? WHERE username = ?";
        $updateStmt = $conn->prepare($updateSql);

        if (!$updateStmt) {
          // Error preparing statement
          $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update student account.');
          header('Location: professor.php');
          exit();
        }

        $updateStmt->bind_param("ss", $hashedNewPassword, $username);
        if ($updateStmt->execute()) {
          // Account updated successfully
          $_SESSION['notification'] = array('type' => 'success', 'message' => 'Successfully updated student account.');
          header('Location: professor.php');
          exit();
        } else {
          // Error updating account
          $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update student account.');
          header('Location: professor.php');
          exit();
        }

        $updateStmt->close();
      } else {
        // New passwords do not match
        $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update student account: New passwords do not match.');
        header('Location: professor.php');
        exit();
      }
    } else {
      // Invalid old password
      $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update student account: Incorrect old password.');
      header('Location: professor.php');
      exit();
    }
  } else {
    // Student account not found
    $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update student account: Student account not found.');
    header('Location: professor.php');
    exit();
  }

  $verifyStmt->close();
}


// Fetch all records from the Records table
$sql = "SELECT StudentNo, ReferenceNo, Type, Score, Grade, DateTaken FROM Records ORDER BY StudentNo ASC";

$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
  die("Error executing query: " . $conn->error);
}

// Check if the form for adding a record is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addRecord'])) {
  // Retrieve the form data
  $studentNo = $_POST['studentNo'];
  $type = $_POST['type'];
  $score = $_POST['score'];
  $grade = $_POST['grade'];
  $dateTaken = $_POST['dateTaken'];

  // Generate the next reference number with leading zeros
  $nextReference = generateReferenceNumber($conn);

  // Insert the new record into the database
  $insertSql = "INSERT INTO Records (StudentNo, ReferenceNo, Type, Score, Grade, DateTaken) VALUES ('$studentNo', '$nextReference', '$type', $score, '$grade', '$dateTaken')";
  if ($conn->query($insertSql) === TRUE) {
    // Record added successfully
    $_SESSION['notification'] = array('type' => 'success', 'message' => 'Successfully added student record.');
    header('Location: professor.php');
    exit();
  } else {
    // Error adding record
    $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to add student record.');
    header('Location: professor.php');
    exit();
  }
}

// Function to generate the next reference number with leading zeros
function generateReferenceNumber($conn) {
  $sql = "SELECT MAX(ReferenceNo) AS MaxReference FROM Records";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $maxReference = $row['MaxReference'];

    if (!empty($maxReference)) {
      $nextReference = intval($maxReference) + 1;
    } else {
      $nextReference = 1;
    }

    return str_pad($nextReference, 4, '0', STR_PAD_LEFT);
  }

  return null;
}

// Check if the form for updating a record is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['referenceNo'])) {
  // Retrieve the form data
  $referenceNo = $_POST['referenceNo'];
  $type = $_POST['type'];
  $score = $_POST['score'];
  $grade = $_POST['grade'];
  $dateTaken = $_POST['dateTaken'];

  // Update the record in the database
  $updateSql = "UPDATE Records SET Type = '$type', Score = $score, Grade = '$grade', DateTaken = '$dateTaken' WHERE ReferenceNo = '$referenceNo'";

  if ($conn->query($updateSql) === TRUE) {
    // Record updated successfully
    $_SESSION['notification'] = array('type' => 'success', 'message' => 'Successfully updated student record.');
    header('Location: professor.php');
    exit();
  } else {
    // Error updating record
    $_SESSION['notification'] = array('type' => 'error', 'message' => 'Failed to update student record.');
    header('Location: professor.php');
    exit();
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Records (Professor)</title>
 <link rel="stylesheet" href="css/aniera.css">
</head>
<body>

<a href="logout.php" onclick="clearTextField()">Logout <?php echo $_SESSION['username']; ?></a>

<div id="notification" class="<?php echo isset($_SESSION['notification']) ? $_SESSION['notification']['type'] : ''; ?>">
  <?php echo isset($_SESSION['notification']) ? $_SESSION['notification']['message'] : ''; ?>
</div>


  <h1>Welcome, Professor!</h1>

  <!-- Add Student Account Form -->
  <h2>Add Student Account</h2>
  <form method="POST" action="">
    <label for="studentNo">Student Number:</label>
    <input type="text" id="studentNo" name="studentNo" pattern="\d{8}" title="Student number must be 8 digits long" required><br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" name="addAccount" value="Add Account">
  </form>

  <!-- Edit Student Account Form -->
  <h2>Edit Student Account</h2>
  <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <label for="studentNo">Student Number:</label>
    <input type="text" id="studentNo" name="studentNo" pattern="\d{8}" title="Student number must be 8 digits long" required><br><br>
    <label for="oldPassword">Old Password:</label>
    <input type="password" id="oldPassword" name="oldPassword" required><br><br>
    <label for="newPassword">New Password:</label>
    <input type="password" id="newPassword" name="newPassword" required><br><br>
    <label for="confirmNewPassword">Confirm New Password:</label>
    <input type="password" id="confirmNewPassword" name="confirmNewPassword" required><br><br>
    <input type="submit" name="editAccount" value="Edit Account">
  </form>

  <!-- Edit Prof Account Form -->
  <h2>Edit Your Account</h2>
  <form action="professor.php" method="POST">

  <label for="newUsername">Username:</label>
  <input type="text" id="newUsername" name="newUsername" value="<?php echo $_SESSION['username']; ?>"><br><br>

  <label for="oldPassword">Old Password:</label>
  <input type="password" id="oldPassword" name="oldPassword" required><br><br>

  <label for="newPassword">New Password:</label>
  <input type="password" id="newPassword" name="newPassword" required><br><br>

  <label for="confirmNewPassword">Confirm New Password:</label>
  <input type="password" id="confirmNewPassword" name="confirmNewPassword" required><br><br>

  <input type="submit" name="updateAccount" value="Update Account">
</form>


  <!-- Add Record Form -->
  <h2>Add Record</h2>
  <form action="" method="post">
    <input type="hidden" name="addRecord" value="true">
    <div>
      <label for="addStudentNo">Student No:</label>
      <input type="text" id="addStudentNo" name="studentNo" required><br><br>
    </div>
    <div>
      <label for="addType">Type:</label>
      <input type="text" id="addType" name="type"required><br><br>
    </div>
    <div>
      <label for="addScore">Score:</label>
      <input type="number" id="addScore" name="score" required><br><br>
    </div>
    <div>
      <label for="addGrade">Grade:</label>
      <select id="addGrade" name="grade" required><br><br>
        <option value="">Select Grade</option>
        <option value="1.00">1.00</option>
        <option value="1.25">1.25</option>
        <option value="1.50">1.50</option>
        <option value="1.75">1.75</option>
        <option value="2.00">2.00</option>
        <option value="2.25">2.25</option>
        <option value="2.50">2.50</option>
        <option value="2.75">2.75</option>
        <option value="3.00">3.00</option>
        <option value="3.25">3.25</option>
        <option value="3.50">3.50</option>
        <option value="3.75">3.75</option>
        <option value="4.00">4.00</option>
        <option value="4.25">4.25</option>
        <option value="4.50">4.50</option>
        <option value="4.75">4.75</option>
        <option value="5.00">5.00</option>
        <option value="INC">INC</option>
        <option value="UD">UD</option>
      </select><br><br>
      </select>
    </div>
    <div>
      <label for="addDateTaken">Date Taken:</label>
      <input type="date" id="addDateTaken" name="dateTaken" required><br><br>
    </div>
    <div>
      <input type="submit" value="Add Record">
    </div>
  </form>

  <!-- All Records Form -->
  <h2>All Records:</h2>

  <?php
  // Group records by student number
  $currentStudentNo = null;
  $studentTableOpen = false;

  // Check if there are any records
  if ($result->num_rows > 0) {
    // Loop through the result set and display the records
    while ($row = $result->fetch_assoc()) {
      // Check if the student number has changed
      if ($row['StudentNo'] !== $currentStudentNo) {
        // Close the previous student table if it's open
        if ($studentTableOpen) {
          echo "</table>";
          echo "</div>";
        }

        // Open a new student table
        echo "<div class='student-table'>";
        echo "<h3>Student No: " . $row['StudentNo'] . "</h3>";
        echo "<table>";
        echo "<tr>";
        echo "<th>Reference No.</th>";
        echo "<th>Type</th>";
        echo "<th>Score</th>";
        echo "<th>Grade</th>";
        echo "<th>Date Taken</th>";
        echo "<th>Action</th>";
        echo "</tr>";

        $currentStudentNo = $row['StudentNo'];
        $studentTableOpen = true;
      }

      echo "<tr>";
      echo "<td>" . $row['ReferenceNo'] . "</td>";
      echo "<td>" . $row['Type'] . "</td>";
      echo "<td>" . $row['Score'] . "</td>";
      echo "<td>" . $row['Grade'] . "</td>";
      echo "<td>" . $row['DateTaken'] . "</td>";
      echo "<td><button onclick=\"openModal('" . $row['ReferenceNo'] . "', '" . $row['StudentNo'] . "', '" . $row['Type'] . "', '" . $row['Score'] . "', '" . $row['Grade'] . "', '" . $row['DateTaken'] . "')\">Edit</button></td>";
      echo "</tr>";
    }

    // Close the last student table
    echo "</table>";
    echo "</div>";
  } else {
    echo "<p>No records found.</p>";
  }
  ?>

  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Edit Record</h2>
      <div id="editStudentNoContainer">
        <label><strong>Student No:</strong></label>
        <span id="editStudentNo"></span>
      </div>
      <div id="editReferenceNoContainer">
        <label><strong>Reference No:</strong></label>
        <span id="editReferenceNo"></span>
      </div>

      <form action="professor.php" method="post">
        <input type="hidden" id="editReferenceNoInput" name="referenceNo">
        <label for="editType">Type:</label>
        <input type="text" id="editType" name="type" required><br><br>
        <label for="editScore">Score:</label>
        <input type="number" id="editScore" name="score" required><br><br>
        <label for="editGrade">Grade:</label>
        <select id="editGrade" name="grade" required>
          <option value="">Select Grade</option>
          <option value="1.00">1.00</option>
          <option value="1.25">1.25</option>
          <option value="1.50">1.50</option>
          <option value="1.75">1.75</option>
          <option value="2.00">2.00</option>
          <option value="2.25">2.25</option>
          <option value="2.50">2.50</option>
          <option value="2.75">2.75</option>
          <option value="3.00">3.00</option>
          <option value="3.25">3.25</option>
          <option value="3.50">3.50</option>
          <option value="3.75">3.75</option>
          <option value="4.00">4.00</option>
          <option value="4.25">4.25</option>
          <option value="4.50">4.50</option>
          <option value="4.75">4.75</option>
          <option value="5.00">5.00</option>
          <option value="INC">INC</option>
          <option value="UD">UD</option>
        </select><br><br>
        <label for="editDateTaken">Date Taken:</label>
        <input type="date" id="editDateTaken" name="dateTaken" required><br><br>
        <input type="submit" value="Update">
      </form>
    </div>
  </div>

  <?php
  unset($_SESSION['notification']);
  ?>


  </body>
  </html>

  <script>
    // Get the modal element
    var modal = document.getElementById("editModal");

    // Get the close button element
    var closeBtn = modal.getElementsByClassName("close")[0];

    // Function to open the modal and populate the fields with existing data
    function openModal(referenceNo, studentNo, type, score, grade, dateTaken) {
      document.getElementById("editReferenceNoInput").value = referenceNo;
      document.getElementById("editReferenceNo").textContent = referenceNo;
      document.getElementById("editStudentNo").textContent = studentNo;
      document.getElementById("editType").value = type;
      document.getElementById("editScore").value = score;
      document.getElementById("editGrade").value = grade;
      document.getElementById("editDateTaken").value = dateTaken;
      modal.style.display = "block";
    }


    // Function to close the modal
    function closeModal() {
      modal.style.display = "none";
    }

    // Event listener for close button
    closeBtn.addEventListener("click", closeModal);

  // Function to clear the text field value
  function clearTextField() {
    document.getElementById("addStudentNo").value = "";
  }
</script>
