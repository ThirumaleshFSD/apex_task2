<?php
session_start();
require_once 'db.php';

// Require login
//required for login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = getDB();
$success = $_GET['msg'] ?? '';

// Fetch all posts with author name
$posts = $db->query("
    SELECT p.id, p.title, p.content, p.created_at, u.username
    FROM posts p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
");
//the blog page for login
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts – Blog App</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">✍️ Blog App</div>
    <div class="nav-links">
        <span class="nav-user">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="post_form.php" class="btn btn-primary btn-sm">+ New Post</a>
        <a href="logout.php" class="btn btn-ghost btn-sm">Logout</a>
    </div>
</nav>

<main class="container">
    <div class="page-header">
        <h1>All Posts</h1>
        <p>Browse and manage blog posts</p>
    </div>

    <?php if ($success === 'created'): ?>
        <div class="alert alert-success">✅ Post created successfully!</div>
    <?php elseif ($success === 'updated'): ?>
        <div class="alert alert-success">✅ Post updated successfully!</div>
    <?php elseif ($success === 'deleted'): ?>
        <div class="alert alert-success">🗑️ Post deleted.</div>
    <?php endif; ?>

    <?php if ($posts->num_rows === 0): ?>
        <div class="empty-state">
            <div class="empty-icon">📝</div>
            <h3>No posts yet</h3>
            <p>Be the first to write something!</p>
            <a href="post_form.php" class="btn btn-primary">Create First Post</a>
        </div>
    <?php else: ?>
        <div class="posts-grid">
            <?php while ($post = $posts->fetch_assoc()): ?>
            <div class="post-card">
                <div class="post-meta">
                    <span class="post-author">👤 <?= htmlspecialchars($post['username']) ?></span>
                    <span class="post-date"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                </div>
                <h2 class="post-title"><?= htmlspecialchars($post['title']) ?></h2>
                <p class="post-excerpt"><?= nl2br(htmlspecialchars(substr($post['content'], 0, 160))) ?>...</p>
                <div class="post-actions">
                    <a href="view_post.php?id=<?= $post['id'] ?>" class="btn btn-ghost btn-sm">Read More</a>
                    <?php if ($_SESSION['user_id']): ?>
                        <a href="post_form.php?id=<?= $post['id'] ?>" class="btn btn-outline btn-sm">✏️ Edit</a>
                        <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this post?')">🗑️ Delete</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</main>

<footer class="footer">
    <p>© <?= date('Y') ?> <span>Blog App</span> — Apex Planet Project</p>
</footer>

</body>
</html>
<?php $db->close(); ?>
