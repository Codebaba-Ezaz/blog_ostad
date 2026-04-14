<?php
require __DIR__ . '/config/app.php';
require __DIR__ . '/includes/functions.php';
require_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $shortDescription = trim($_POST['short_description'] ?? '');
    $longDescription = trim($_POST['long_description'] ?? '');
    $isPublish = isset($_POST['is_publish']) ? 1 : 0;
    $uid = current_user_id();

    if ($title === '' || $shortDescription === '' || $longDescription === '') {
        $error = 'Title, short description and long description are required.';
    } else {
        $imageName = save_image($_FILES['image'] ?? []);

        if (isset($_FILES['image']) && ($_FILES['image']['name'] ?? '') !== '' && $imageName === null) {
            $error = 'Invalid image file.';
        } else {
            $stmt = $conn->prepare('INSERT INTO blogs (user_id, title, short_description, long_description, image, is_publish) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('issssi', $uid, $title, $shortDescription, $longDescription, $imageName, $isPublish);

            if ($stmt->execute()) {
                flash_set('success', 'Blog created successfully.');
                redirect('dashboard.php');
            } else {
                $error = 'Failed to create blog.';
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="card" style="max-width:760px; margin: 24px auto;">
    <h2>Create Blog</h2>
    <?php if ($error !== ''): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Title</label>
        <input type="text" name="title" value="<?= e($_POST['title'] ?? '') ?>">

        <label>Short Description</label>
        <textarea name="short_description"><?= e($_POST['short_description'] ?? '') ?></textarea>

        <label>Long Description</label>
        <textarea name="long_description"><?= e($_POST['long_description'] ?? '') ?></textarea>

        <label>Image</label>
        <input type="file" name="image" accept="image/*" style="margin-top:6px; margin-bottom:14px;">

        <label style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
            <input type="checkbox" name="is_publish" value="1" <?= isset($_POST['is_publish']) ? 'checked' : '' ?>>
            Publish now
        </label>

        <button class="btn" type="submit">Save Blog</button>
        <a class="btn secondary" href="dashboard.php">Back</a>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
