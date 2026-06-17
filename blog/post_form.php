<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = getDB();
$id = intval($_GET['id'] ?? 0);
$post = null;
$error = '';
$isEdit = false;

// Load post for editing
if ($id > 0) {
    $stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    if ($post) $isEdit = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $postId  = intval($_POST['post_id'] ?? 0);

    if (!$title || !$content) {
        $error = "Title and content are required.";
    } else {
        if ($postId > 0) {
            // UPDATE
            $stmt = $db->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
            $stmt->bind_param("ssi", $title, $content, $postId);
            $stmt->execute();
            header("Location: posts.php?msg=updated");
        } else {
            // CREATE
            $uid = $_SESSION['user_id'];
            $stmt = $db->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $title, $content, $uid);
            $stmt->execute();
            header("Location: posts.php?msg=created");
        }
        exit;
    }
}

$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit Post' : 'New Post' ?> – Blog App</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">✍️ Blog App</div>
    <div class="nav-links">
        <a href="posts.php" class="btn btn-ghost btn-sm">← Back to Posts</a>
        <a href="logout.php" class="btn btn-ghost btn-sm">Logout</a>
    </div>
</nav>

<main class="container form-page">
    <div class="page-header">
        <h1><?= $isEdit ? '✏️ Edit Post' : '📝 New Post' ?></h1>
        <p><?= $isEdit ? 'Update your blog post below' : 'Share something with the world' ?></p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" id="post-form">
            <input type="hidden" name="post_id" value="<?= $isEdit ? $post['id'] : 0 ?>">

            <div class="form-group">
                <label for="title">Post Title</label>
                <input type="text" id="title" name="title"
                       placeholder="Enter an interesting title..."
                       value="<?= $isEdit ? htmlspecialchars($post['title']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" rows="10"
                          placeholder="Write your post content here..."
                          required><?= $isEdit ? htmlspecialchars($post['content']) : '' ?></textarea>
            </div>

            <div class="form-actions">
                <a href="posts.php" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Update Post' : 'Publish Post' ?>
                </button>
            </div>
        </form>
    </div>
</main>

</body>
</html>
