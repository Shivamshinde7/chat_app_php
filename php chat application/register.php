<?php
include('db.php');

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);

    echo "Registration successful! <a href='login.php'>Login here</a>";
}
?>

<form method="POST" action="">
    <label>Username: </label><input type="text" name="username" required><br>
    <label>Password: </label><input type="password" name="password" required><br>
    <button type="submit" name="register">Register</button>
</form>
