<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
</head>
<body>
  <h1>Login</h1>
  <?php
  session_start();
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
  ?>
  <form action="login.php" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" value="Login">
  </form>
</body>
</html>
