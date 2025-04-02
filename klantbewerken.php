<?php
require 'db_connect.php';

// Haal klant-ID op uit de URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Geen klant-ID opgegeven.");
}
$id = intval($_GET['id']);

// Klantgegevens ophalen
$sql = "SELECT * FROM klanten WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Klant niet gevonden.");
}
$klant = $result->fetch_assoc();

// Verwerken van formulier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = $_POST['naam'];
    $email = $_POST['email'];
    $telefoon = $_POST['telefoon'];

    $update_sql = "UPDATE klanten SET naam = ?, email = ?, telefoon = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $naam, $email, $telefoon, $id);
    
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
    <title>Klant bewerken</title>
    <link rel="stylesheet" href="klantbeheer.css">
</head>
<body>
    <h2>Klantgegevens bewerken</h2>
    <form method="POST">
        <label>Naam:</label>
        <input type="text" name="naam" value="<?php echo htmlspecialchars($klant['naam']); ?>" required><br>
        
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($klant['email']); ?>" required><br>
        
        <label>Telefoon:</label>
        <input type="text" name="telefoon" value="<?php echo htmlspecialchars($klant['telefoon']); ?>"><br>
        
        <button type="submit">Opslaan</button>
        <a href="klantbeheer.php">Annuleren</a>
        <a href="klantbeheer.php">Terug naar Klantbeheer</a>
    </form>
</body>
</html>
