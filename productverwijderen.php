<?php
require 'db_connect.php';

// Controleren of er een ID is meegegeven
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Geen product-ID opgegeven.");
}
$id = intval($_GET['id']);

// Bevestiging voordat het product wordt verwijderd
if (isset($_POST['confirm']) && $_POST['confirm'] == 'Ja') {
    $sql = "DELETE FROM producten WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<p>Product succesvol verwijderd!</p>";
    } else {
        echo "<p>Fout bij verwijderen: " . $conn->error . "</p>";
    }
    
    // Doorsturen naar productbeheer na 2 seconden
    echo "<script>setTimeout(() => { window.location.href = 'productbeheer.php'; }, 2000);</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Verwijderen</title>
    <link rel="stylesheet" href="body.css">
</head>
<body>
    <h2>Weet je zeker dat je dit product wilt verwijderen?</h2>
    <form method="POST">
        <button type="submit" name="confirm" value="Ja">Ja, verwijderen</button>
        <a href="productbeheer.php">Annuleren</a>
    </form>
</body>
</html>