<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klantbeheer</title>
    <link rel="stylesheet" href="body.css">
</head>
<body>
    <h2>productbeheer</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Naam</th>
                <th>Artikelnummer</th>
                <th>Prijs</th>
                <th>Korting</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Databaseverbinding inladen
            require 'db_connect.php';

            // Query om klantgegevens op te halen
            $sql = "SELECT * FROM producten";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["id"] . "</td>
                            <td>" . $row["naam"] . "</td>
                            <td>" . $row["artikelnummer"] . "</td>
                            <td>" . $row["Prijs"] . "</td>
                            <td>" . $row["korting"] . "</td>
                            <td><a href='productbewerken.php?id=" . $row["id"] . "'>Bewerken</a> | 
                                <a href='productverwijderen.php?id=" . $row["id"] . "' onclick='return confirm('Weet je zeker dat je deze klant wilt verwijderen?')'>Verwijderen</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Geen klanten gevonden</td></tr>";
            }

            // Verbinding sluiten
            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
