<?php
session_start();
require_once 'config/db_connect.php';
require_once 'users.php';

// Check if the user is already logged in
if (isset($_SESSION['user_role'])) {
    // Redirect to the respective page based on the user's role
    if ($_SESSION['user_role'] === 'professor') {
        header('Location: professor.php');
        exit();
    } elseif ($_SESSION['user_role'] === 'student') {
        header('Location: student.php');
        exit();
    }
}

// Check the user credentials in the login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = authenticateUser($username, $password);

    if ($user) {
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['student_no'] = ($user['role'] === 'student') ? $username : null;
        $_SESSION['username'] = $username;

        if ($user['role'] === 'professor') {
            header('Location: professor.php');
            exit();
        } elseif ($user['role'] === 'student') {
            header('Location: student.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Invalid username or password.";
        header('Location: login.php');
        exit();
    }
}

// Check if there is an error message stored in the session
$errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;

// Clear the error message from the session
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
</head>
<body>
  <h1>Login</h1>
  <form action="login.php" method="post">
    <?php if ($errorMessage) { ?>
      <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php } ?>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" value="Login">
  </form>
</body>
</html>
