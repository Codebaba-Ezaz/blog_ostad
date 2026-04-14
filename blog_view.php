<?php
require __DIR__ . '/config/app.php';
require __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    redirect('index.php');
}

$stmt = $conn->prepare(
    'SELECT b.*, u.name AS author_name
     FROM blogs b
     INNER JOIN users u ON u.id = b.user_id
     WHERE b.id = ?
     LIMIT 1'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if (!$blog) {
    redirect('index.php');
}

$uid = current_user_id();
$canView = (int) $blog['is_publish'] === 1 || ($uid !== null && $uid === (int) $blog['user_id']) || is_admin();

if (!$canView) {
    redirect('index.php');
}

$error = '';
$success = flash_get('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();

    $comment = trim($_POST['comment'] ?? '');
    $canComment = $uid !== null && $uid !== (int) $blog['user_id'];

    if (!$canComment) {
        $error = 'You cannot comment on your own blog.';
    } elseif ($comment === '') {
        $error = 'Comment is required.';
    } else {
        $insertStmt = $conn->prepare('INSERT INTO comments (blog_id, user_id, comment) VALUES (?, ?, ?)');
        $insertStmt->bind_param('iis', $id, $uid, $comment);

        if ($insertStmt->execute()) {
            flash_set('success', 'Comment added successfully.');
            redirect('blog_view.php?id=' . $id);
        } else {
            $error = 'Failed to add comment.';
        }
    }
}

$commentStmt = $conn->prepare(
    'SELECT c.comment, c.created_at, u.name AS commenter_name
     FROM comments c
     INNER JOIN users u ON u.id = c.user_id
     WHERE c.blog_id = ?
     ORDER BY c.id DESC'
);
$commentStmt->bind_param('i', $id);
$commentStmt->execute();
$comments = $commentStmt->get_result();

require __DIR__ . '/includes/header.php';
?>

<div class="card">
    <h2><?= e($blog['title']) ?></h2>
    <p class="muted">Author: <?= e($blog['author_name']) ?> | <?= e($blog['created_at']) ?> | <?= (int) $blog['is_publish'] === 1 ? 'Published' : 'Unpublished' ?></p>
    <p><strong>Short Description:</strong><br><?= nl2br(e($blog['short_description'])) ?></p>
    <p><strong>Long Description:</strong><br><?= nl2br(e($blog['long_description'])) ?></p>
    <?php if (!empty($blog['image'])): ?>
        <img class="blog-image" src="uploads/<?= e($blog['image']) ?>" alt="Blog image">
    <?php endif; ?>
</div>

<div class="card">
    <h3>Comments</h3>

    <?php if ($success !== null): ?>
        <div class="alert success"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (is_logged_in() && current_user_id() !== (int) $blog['user_id']): ?>
        <form method="post">
            <label>Write a comment</label>
            <textarea name="comment"><?= e($_POST['comment'] ?? '') ?></textarea>
            <button class="btn" type="submit">Submit Comment</button>
        </form>
    <?php elseif (is_logged_in()): ?>
        <p class="muted">You cannot comment on your own blog.</p>
    <?php else: ?>
        <p class="muted">Please <a href="login.php">login</a> to comment.</p>
    <?php endif; ?>

    <hr style="border:none; border-top:1px solid #e5e7eb; margin:18px 0;">

    <?php if ($comments && $comments->num_rows > 0): ?>
        <?php while ($commentRow = $comments->fetch_assoc()): ?>
            <div style="padding: 10px 0; border-bottom:1px solid #f1f5f9;">
                <p style="margin:0 0 6px;"><strong><?= e($commentRow['commenter_name']) ?></strong> <span class="muted">(<?= e($commentRow['created_at']) ?>)</span></p>
                <p style="margin:0;"><?= nl2br(e($commentRow['comment'])) ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No comments yet.</p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
