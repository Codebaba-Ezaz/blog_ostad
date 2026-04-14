<?php
require __DIR__ . '/config/app.php';
require __DIR__ . '/includes/functions.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$uid = current_user_id();

if ($id <= 0) {
    redirect('dashboard.php');
}

$stmt = $conn->prepare('SELECT is_publish FROM blogs WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->bind_param('ii', $id, $uid);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if (!$blog) {
    redirect('dashboard.php');
}

$nextStatus = (int) $blog['is_publish'] === 1 ? 0 : 1;

$updateStmt = $conn->prepare('UPDATE blogs SET is_publish = ? WHERE id = ? AND user_id = ?');
$updateStmt->bind_param('iii', $nextStatus, $id, $uid);
$updateStmt->execute();

flash_set('success', $nextStatus === 1 ? 'Blog published.' : 'Blog unpublished.');
redirect('dashboard.php');
