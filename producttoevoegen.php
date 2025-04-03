<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = $_POST['naam'];
    $artikelnummer = $_POST['artikelnummer'];
    $prijs = $_POST['prijs'];
    $korting = $_POST['korting'];

    $sql = "INSERT INTO producten (naam, artikelnummer, prijs, korting) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdd", $naam, $artikelnummer, $prijs, $korting);
    
    if ($stmt->execute()) {
        echo "<p>Product succesvol toegevoegd!</p>";
    } else {
        echo "<p>Fout bij toevoegen: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Toevoegen</title>
    <link rel="stylesheet" href="body.css">
</head>
<body>
    <h2>Product Toevoegen</h2>
    <form method="POST">
        <label>Naam:</label>
        <input type="text" name="naam" required><br>
        
        <label>Artikelnummer:</label>
        <input type="text" name="artikelnummer" required><br>
        
        <label>Prijs:</label>
        <input type="number" step="0.01" name="prijs" required><br>
        
        <label>Korting:</label>
        <input type="number" step="0.01" name="korting"><br>
        
        <button type="submit">Toevoegen</button>
        <a href="productbeheer.php">Terug naar productbeheer</a>
    </form>
</body>
</html>
