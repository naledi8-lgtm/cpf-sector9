<?php
// hash.php – Run once and delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (strlen($password) >= 6) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        echo "<strong>Your hash:</strong> <code>$hash</code>";
        echo "<br><br>Use this in your SQL: <br> <code>INSERT INTO users ... VALUES ('name', 'email', '$hash', 'admin', 'active');</code>";
    } else {
        echo "Password must be at least 6 characters.";
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Generate Password Hash</title>
</head>
<body>
    <h2>Generate Bcrypt Hash</h2>
    <form method="POST">
        <label>Enter Password: <input type="password" name="password" required minlength="6"></label>
        <button type="submit">Generate Hash</button>
    </form>
</body>
</html>