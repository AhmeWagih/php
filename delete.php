<?php
require('connection.php');
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {

  $database->query("DELETE FROM users WHERE id = {$id} LIMIT 1");
}

header('Location: users.php');
exit();
?>