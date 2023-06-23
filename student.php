<?php
session_start();

// Check if the user is logged in as a student
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
  header('Location: index.php');
  exit();
}

// Get the student number from the session (assuming the student number is stored in the $_SESSION['student_no'] variable)
$studentNo = $_SESSION['student_no'];

// Connect to the database
include('config/db_connect.php');

// Check the database connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch the student's records from the Records table
$sql = "SELECT * FROM Records WHERE StudentNo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $studentNo);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Records (Student)</title>
  <link rel="stylesheet" href="css/aniera.css">
</head>
<body>

<a href="logout.php" onclick="clearTextField()">Logout <?php echo $_SESSION['username']; ?></a>

  <h1>Welcome, Student!</h1>
  <h2>Your Records:</h2>
  <table>
    <tr>
      <?php if ($_SESSION['user_role'] === 'professor') { ?>
      <th>Reference No.</th>
      <?php } ?>

      <th>Type</th>

      <?php if ($_SESSION['user_role'] === 'professor') { ?>
      <th>Score</th>
      <?php } ?>

      <th>Grade</th>
      <th>Date Taken</th>
    </tr>
    <?php
    if ($result) {
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          if ($_SESSION['user_role'] === 'professor') {
            echo "<td>" . $row['ReferenceNo'] . "</td>";
          }
          echo "<td>" . $row['Type'] . "</td>";
          if ($_SESSION['user_role'] === 'professor') {
          echo "<td>" . $row['Score'] . "</td>";
          }
          echo "<td>" . $row['Grade'] . "</td>";
          echo "<td>" . $row['DateTaken'] . "</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='5'>No records found.</td></tr>";
      }
    }
    ?>

  </table>
</body>
</html>


<script>
  // Function to clear the text field value
  function clearTextField() {
    document.getElementById("addStudentNo").value = "";
  }
</script>
