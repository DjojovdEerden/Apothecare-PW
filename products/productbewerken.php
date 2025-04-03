<?php
require 'db_connect.php';

// Haal product-ID op uit de URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Geen product-ID opgegeven.");
}
$id = intval($_GET['id']);

// Productgegevens ophalen
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product niet gevonden.");
}
$product = $result->fetch_assoc();

// Verwerken van formulier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = $_POST['naam'];
    $prijs = $_POST['prijs'];
    $korting = $_POST['korting'];

    $update_sql = "UPDATE products SET NAME = ?, price = ?, korting = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssdi", $naam, $prijs, $korting, $id);
    
    if ($update_stmt->execute()) {
        echo "<p>Gegevens succesvol bijgewerkt!</p>";
    } else {
        echo "<p>Fout bij het bijwerken: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productgegevens bewerken</title>
    <link rel="stylesheet" href="body.css">
</head>
<body>
    <h2>Productgegevens bewerken</h2>
    <form method="POST">
        <label>Naam:</label>
        <input type="text" name="naam" value="<?php echo htmlspecialchars($product['NAME']); ?>" required><br>
        
        <label>Prijs:</label>
        <input type="text" name="prijs" value="<?php echo htmlspecialchars($product['price']); ?>" required><br>

        <label>Korting:</label>
        <input type="text" name="korting" value="<?php echo htmlspecialchars($product['korting']); ?>"><br>
        
        <button type="submit">Opslaan</button>
        <a href="products/productbeheer.php">Annuleren</a>
    </form>
</body>
</html>
