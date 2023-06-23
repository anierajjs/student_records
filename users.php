<?php
require_once 'config/db_connect.php';

function authenticateUser($username, $password)
{
    global $conn;

    // Prepare the SQL statement to fetch the user from the database
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);

    // Execute the SQL statement
    $stmt->execute();

    // Fetch the user record
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify the password
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return null;
}

?>
