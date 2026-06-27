<?php
session_start();
require_once 'db.php';
//this php of the delate the post of code 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = getDB();
$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$db->close();
header("Location: posts.php?msg=deleted");
exit;
