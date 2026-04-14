<?php
require __DIR__ . '/config/app.php';
require __DIR__ . '/includes/functions.php';

$result = $conn->query(
    "SELECT b.id, b.title, b.short_description, b.image, b.created_at, u.name AS author_name
     FROM blogs b
     INNER JOIN users u ON u.id = b.user_id
     WHERE b.is_publish = 1
     ORDER BY b.id DESC"
);

require __DIR__ . '/includes/header.php';
?>

<div class="card">
    <h2>Published Blogs</h2>
    <p class="muted">See all public posts from users.</p>
</div>

<div class="grid">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($blog = $result->fetch_assoc()): ?>
            <div class="card">
                <h3><?= e($blog['title']) ?></h3>
                <p class="muted">By <?= e($blog['author_name']) ?> | <?= e($blog['created_at']) ?></p>
                <p><?= nl2br(e($blog['short_description'])) ?></p>
                <?php if (!empty($blog['image'])): ?>
                    <img class="blog-image" src="uploads/<?= e($blog['image']) ?>" alt="Blog image">
                <?php endif; ?>
                <p><a class="btn" href="blog_view.php?id=<?= (int) $blog['id'] ?>">View Details</a></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card">
            <p>No published blog found.</p>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
