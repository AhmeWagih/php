<?php
$database = new mysqli('localhost', 'root', 'Aa**2003//', 'PHPCourse');

if ($database->connect_error) {
  die("Connection failed: " . $database->connect_error);
}
?>