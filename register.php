<?php
require __DIR__ . '/config/app.php';
require __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $phone === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email.';
    } else {
        $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $error = 'Email already exists.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conn->prepare('INSERT INTO users (name, email, phone, password, user_type) VALUES (?, ?, ?, ?, "user")');
            $insertStmt->bind_param('ssss', $name, $email, $phone, $hashedPassword);

            if ($insertStmt->execute()) {
                flash_set('success', 'Registration completed. Please login.');
                redirect('login.php');
            } else {
                $error = 'Registration failed. Try again.';
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="card" style="max-width:520px; margin: 24px auto;">
    <h2>Register</h2>
    <?php if ($error !== ''): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Name</label>
        <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>">

        <label>Phone</label>
        <input type="text" name="phone" value="<?= e($_POST['phone'] ?? '') ?>">

        <label>Password</label>
        <input type="password" name="password">

        <button class="btn" type="submit">Register</button>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
