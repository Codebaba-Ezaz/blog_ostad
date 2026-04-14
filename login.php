<?php
require __DIR__ . '/config/app.php';
require __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
$success = flash_get('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $stmt = $conn->prepare('SELECT id, name, email, password, user_type FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => (int) $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'user_type' => $user['user_type']
                ];
                redirect('dashboard.php');
            }
        }

        $error = 'Invalid email or password.';
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="card" style="max-width:520px; margin: 24px auto;">
    <h2>Login</h2>
    <?php if ($success !== null): ?>
        <div class="alert success"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Email</label>
        <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>">

        <label>Password</label>
        <input type="password" name="password">

        <button class="btn" type="submit">Login</button>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
