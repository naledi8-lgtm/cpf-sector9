<?php
session_start();
// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: dashboard.php');
    } elseif ($_SESSION['user_role'] === 'patroller') {
        header('Location: patroller_dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'member';
    $phone = trim($_POST['phone'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered. Please login.';
        } else {
            // Insert user
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hashed, $role, 'active']);

            // If patroller, also insert into patrollers table
            if ($role === 'patroller') {
                if (empty($phone)) {
                    $error = 'Phone number is required for patrollers.';
                    // Rollback: delete the user we just inserted
                    $pdo->prepare('DELETE FROM users WHERE email = ?')->execute([$email]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO patrollers (name, email, phone, status) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$name, $email, $phone, 'pending']);
                    $success = 'Patroller registration successful! Please wait for admin approval.';
                }
            } else {
                $success = 'Registration successful! You can now login.';
            }

            // If error occurred during patroller insertion, we already deleted user
            if (!empty($error)) {
                // The user was deleted, so we can show error
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – CPF Sector 9</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<header class="forum-header">
    <div class="container">
        <div class="logo-area">
            <img src="images/cpf-logo.jpeg" alt="CPF Sector 9 Logo" class="logo-img">
            <span class="logo-text">CPF <span>Sector 9</span></span>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn btn-outline"><i class="fas fa-home"></i> Home</a>
            <a href="login.php" class="btn btn-outline">Login</a>
        </div>
    </div>
</header>

<section class="join-section">
    <div class="container">
        <div class="join-card" style="max-width: 500px; margin: 0 auto;">
            <h2>Create <span>Account</span></h2>
            <p style="color: var(--gray-600); margin-bottom: 20px;">Join the CPF Sector 9 community.</p>

            <?php if ($error): ?>
                <div style="background:#f8d7da; color:#721c24; padding:12px; border-radius:var(--radius-sm); margin-bottom:16px;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="background:#d4edda; color:#155724; padding:12px; border-radius:var(--radius-sm); margin-bottom:16px;">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                    <p style="margin-top:8px;"><a href="login.php" style="color:var(--blue-primary); font-weight:600;">Click here to login</a></p>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" placeholder="e.g. Thabo Mokoena" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Password * (min 6 characters)</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <input type="password" name="confirm_password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" id="roleSelect" onchange="togglePhone()">
                            <option value="member" selected>Member</option>
                            <option value="patroller">Patroller</option>
                        </select>
                    </div>
                    <div class="form-group" id="phoneGroup" style="display:none;">
                        <label>Phone Number (required for Patrollers)</label>
                        <input type="tel" name="phone" placeholder="+27 82 123 4567">
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-user-plus"></i> Register</button>
                </form>
                <p style="margin-top:16px; text-align:center; font-size:0.9rem;">
                    Already have an account? <a href="login.php" style="color:var(--blue-primary); font-weight:600;">Login here</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    function togglePhone() {
        const role = document.getElementById('roleSelect').value;
        const phoneGroup = document.getElementById('phoneGroup');
        phoneGroup.style.display = (role === 'patroller') ? 'block' : 'none';
    }
    // Initial state
    togglePhone();
</script>

<footer class="forum-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-info">
                <p>&copy; <?= date('Y') ?> CPF Sector 9 – Community Patrol Force.</p>
            </div>
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </div>
        </div>
    </div>
</footer>
</body>
</html>