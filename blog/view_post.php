<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = getDB();
$id = intval($_GET['id'] ?? 0);

$stmt = $db->prepare("
    SELECT p.*, u.username 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    header("Location: posts.php");
    exit;
}
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> – Blog App</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">✍️ Blog App</div>
    <div class="nav-links">
        <a href="posts.php" class="btn btn-ghost btn-sm">← All Posts</a>
        <a href="post_form.php?id=<?= $post['id'] ?>" class="btn btn-outline btn-sm">✏️ Edit</a>
        <a href="logout.php" class="btn btn-ghost btn-sm">Logout</a>
    </div>
</nav>

<main class="container view-page">
    <article class="post-view">
        <div class="post-view-meta">
            <span>👤 <?= htmlspecialchars($post['username']) ?></span>
            <span>📅 <?= date('F d, Y · H:i', strtotime($post['created_at'])) ?></span>
        </div>
        <h1 class="post-view-title"><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-view-content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>

        <div class="post-view-actions">
            <a href="posts.php" class="btn btn-ghost">← Back to Posts</a>
            <a href="post_form.php?id=<?= $post['id'] ?>" class="btn btn-outline">✏️ Edit Post</a>
            <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-danger"
               onclick="return confirm('Delete this post?')">🗑️ Delete</a>
        </div>
    </article>
</main>

</body>
</html>
