<?php
require __DIR__ . '/config/app.php';
require __DIR__ . '/includes/functions.php';
require_login();

$uid = current_user_id();
$success = flash_get('success');

$stmt = $conn->prepare(
    'SELECT id, title, short_description, is_publish, created_at FROM blogs WHERE user_id = ? ORDER BY id DESC'
);
$stmt->bind_param('i', $uid);
$stmt->execute();
$blogs = $stmt->get_result();

require __DIR__ . '/includes/header.php';
?>

<div class="card">
    <h2>My Dashboard</h2>
    <p><a class="btn success" href="blog_create.php">Create New Blog</a></p>
    <?php if ($success !== null): ?>
        <div class="alert success"><?= e($success) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Short Description</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($blogs && $blogs->num_rows > 0): ?>
                <?php while ($blog = $blogs->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($blog['title']) ?></td>
                        <td><?= e($blog['short_description']) ?></td>
                        <td><?= (int) $blog['is_publish'] === 1 ? 'Published' : 'Unpublished' ?></td>
                        <td><?= e($blog['created_at']) ?></td>
                        <td class="actions">
                            <a href="blog_view.php?id=<?= (int) $blog['id'] ?>">View</a>
                            <a href="blog_edit.php?id=<?= (int) $blog['id'] ?>">Edit</a>
                            <a href="blog_toggle.php?id=<?= (int) $blog['id'] ?>">
                                <?= (int) $blog['is_publish'] === 1 ? 'Unpublish' : 'Publish' ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">You have no blogs yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
