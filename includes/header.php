<?php
$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Raw SQL Blog</title>
    <style>
        body { margin: 0; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f4f6fb; color: #111827; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .nav { background: #111827; color: #fff; }
        .nav .container { display: flex; gap: 14px; align-items: center; justify-content: space-between; }
        .nav a { color: #fff; text-decoration: none; margin-right: 12px; }
        .nav .left, .nav .right { display: flex; align-items: center; }
        .card { background: #fff; border-radius: 10px; padding: 18px; margin-bottom: 16px; box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06); }
        h1, h2, h3 { margin-top: 0; }
        input[type="text"], input[type="email"], input[type="password"], textarea { width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; margin-top: 6px; margin-bottom: 12px; box-sizing: border-box; }
        textarea { min-height: 120px; resize: vertical; }
        .btn { background: #111827; color: #fff; border: none; border-radius: 8px; padding: 10px 16px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn.secondary { background: #4b5563; }
        .btn.success { background: #047857; }
        .btn.warn { background: #b45309; }
        .btn.danger { background: #b91c1c; }
        .alert { padding: 10px 12px; border-radius: 8px; margin-bottom: 12px; }
        .alert.error { background: #fee2e2; color: #991b1b; }
        .alert.success { background: #dcfce7; color: #166534; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); gap: 14px; }
        .muted { color: #6b7280; font-size: 14px; }
        .blog-image { max-width: 100%; border-radius: 8px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        th { background: #f9fafb; }
        .actions a { margin-right: 8px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="nav">
        <div class="container">
            <div class="left">
                <a href="index.php"><strong>Raw SQL Blog</strong></a>
                <?php if ($user): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="blog_create.php">Create Blog</a>
                <?php endif; ?>
            </div>
            <div class="right">
                <?php if ($user): ?>
                    <span style="margin-right:12px;">Hi, <?= e($user['name']) ?></span>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container">
