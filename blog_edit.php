<?php
require __DIR__ . '/config/app.php';
require __DIR__ . '/includes/functions.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$uid = current_user_id();
$error = '';

if ($id <= 0) {
    redirect('dashboard.php');
}

$stmt = $conn->prepare('SELECT * FROM blogs WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->bind_param('ii', $id, $uid);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if (!$blog) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $shortDescription = trim($_POST['short_description'] ?? '');
    $longDescription = trim($_POST['long_description'] ?? '');
    $isPublish = isset($_POST['is_publish']) ? 1 : 0;

    if ($title === '' || $shortDescription === '' || $longDescription === '') {
        $error = 'Title, short description and long description are required.';
    } else {
        $newImage = save_image($_FILES['image'] ?? []);

        if (isset($_FILES['image']) && ($_FILES['image']['name'] ?? '') !== '' && $newImage === null) {
            $error = 'Invalid image file.';
        } else {
            $imageName = $newImage !== null ? $newImage : $blog['image'];
            $updateStmt = $conn->prepare('UPDATE blogs SET title = ?, short_description = ?, long_description = ?, image = ?, is_publish = ? WHERE id = ? AND user_id = ?');
            $updateStmt->bind_param('ssssiii', $title, $shortDescription, $longDescription, $imageName, $isPublish, $id, $uid);

            if ($updateStmt->execute()) {
                flash_set('success', 'Blog updated successfully.');
                redirect('dashboard.php');
            } else {
                $error = 'Failed to update blog.';
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="card" style="max-width:760px; margin: 24px auto;">
    <h2>Edit Blog</h2>
    <?php if ($error !== ''): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Title</label>
        <input type="text" name="title" value="<?= e($_POST['title'] ?? $blog['title']) ?>">

        <label>Short Description</label>
        <textarea name="short_description"><?= e($_POST['short_description'] ?? $blog['short_description']) ?></textarea>

        <label>Long Description</label>
        <textarea name="long_description"><?= e($_POST['long_description'] ?? $blog['long_description']) ?></textarea>

        <label>Image</label>
        <input type="file" name="image" accept="image/*" style="margin-top:6px; margin-bottom:14px;">
        <?php if (!empty($blog['image'])): ?>
            <img class="blog-image" src="uploads/<?= e($blog['image']) ?>" alt="Current image" style="max-width:220px; display:block;">
        <?php endif; ?>

        <label style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
            <input type="checkbox" name="is_publish" value="1" <?= (isset($_POST['is_publish']) || (!isset($_POST['is_publish']) && (int) $blog['is_publish'] === 1)) ? 'checked' : '' ?>>
            Publish this blog
        </label>

        <button class="btn" type="submit">Update Blog</button>
        <a class="btn secondary" href="dashboard.php">Back</a>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
