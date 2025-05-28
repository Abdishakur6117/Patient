<?php
$host = "localhost";
$user = "root"; // ama isticmaal magaca user kaaga
$password = ""; // ama password hadduu jiro
$dbname = "job"; // magaca database kaaga

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
