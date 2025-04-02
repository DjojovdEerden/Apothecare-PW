<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klantbeheer</title>
    <link rel="stylesheet" href="body.css">
</head>
<body>
    <h2>Klantbeheer</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Naam</th>
                <th>Email</th>
                <th>Telefoon</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Databaseverbinding inladen
            require 'db_connect.php';

            // Query om klantgegevens op te halen
            $sql = "SELECT * FROM klanten";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["id"] . "</td>
                            <td>" . $row["naam"] . "</td>
                            <td>" . $row["email"] . "</td>
                            <td>" . $row["telefoon"] . "</td>
                            <td><a href='klantbewerken.php?id=" . $row["id"] . "'>Bewerken</a> | 
                                <a href='klantverwijderen.php?id=" . $row["id"] . "' onclick='return confirm('Weet je zeker dat je deze klant wilt verwijderen?')'>Verwijderen</a>
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
