<?php
require 'connection/db_config.php';

// Controleren of er een ID is meegegeven
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Geen klant-ID opgegeven.");
}
$id = intval($_GET['id']);

// Klant verwijderen
$sql = "DELETE FROM klanten WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<p>Klant succesvol verwijderd!</p>";
} else {
    echo "<p>Fout bij verwijderen: " . $conn->error . "</p>";
}

// Doorsturen naar klantbeheer na 2 seconden
echo "<script>setTimeout(() => { window.location.href = 'klant/klantbeheer.php'; }, 2000);</script>";
?>

<a href="klant/klantbeheer.php">Terug naar Klantbeheer</a>