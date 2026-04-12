<?php
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
  $database = new mysqli('localhost', 'root', 'Aa**2003//', 'PHPCourse');

  if ($database->connect_error) {
    die('Connection failed: ' . $database->connect_error);
  }

  $database->query("DELETE FROM users WHERE id = {$id} LIMIT 1");
}

header('Location: users.php');
exit();
?>