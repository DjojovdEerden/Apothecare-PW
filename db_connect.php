<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "apothecare";

// Verbinding maken met de database
$conn = new mysqli($servername, $username, $password, $database);

// Controleren op fouten
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
?>
