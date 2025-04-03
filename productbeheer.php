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
    <title>Productbeheer</title>
    <link rel="stylesheet" href="body.css">
</head>
<body>
    <h2>Productbeheer</h2>
    
    <h3>Voeg een nieuw product toe</h3>
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
    </form>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Naam</th>
                <th>Artikelnummer</th>
                <th>Prijs</th>
                <th>Korting</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM producten";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["id"] . "</td>
                            <td>" . $row["naam"] . "</td>
                            <td>" . $row["artikelnummer"] . "</td>
                            <td>" . $row["prijs"] . "</td>
                            <td>" . $row["korting"] . "</td>
                            <td><a href='productbewerken.php?id=" . $row["id"] . "'>Bewerken</a> | 
                            <a href='productverwijderen.php?id=" . $row["id"] . "' onclick=\"return confirm('Weet je zeker dat je dit product wilt verwijderen?')\">Verwijderen</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Geen producten gevonden</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
