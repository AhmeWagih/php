<?php
require('./classes/Database.php');

$databaseConnection = new Database('localhost', 'root', 'Aa**2003//', 'PHPCourse');
$database = $databaseConnection->getConnection();
?>