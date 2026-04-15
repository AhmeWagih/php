<?php
require('connection.php');
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
  (new User($database))->deleteById($id);
}

header('Location: users.php');
exit();
?>