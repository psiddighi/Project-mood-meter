<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin page</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="search" placeholder="Zoek naar opleidingen en namen">
        <input type="submit" value="Zoeken">
    </form>

    <?php
    $servername = "localhost";
    $username = "Parsa";
    $password = "goedverhaal";
    $dbname = "moodmeter";

    // Maak verbinding met de database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Controleer de verbinding
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Voer een SQL-query uit om de gegevens op te halen
    $sql = "SELECT * FROM Informatica_MoodMeter";
    if (isset($_POST['search'])) {
        $search = $conn->real_escape_string($_POST['search']);
        $sql .= " WHERE `gebruikersnaam` LIKE '%$search%' OR `Huidige Opleiding` LIKE '%$search%' OR `Komende Opleiding` LIKE '%$search%'";
    }
    $result = $conn->query($sql);

    $mood_scores = array();
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Gebruikersnaam</th><th>Mood Score</th><th>Huidige Opleiding</th><th>Komende Opleiding</th><th>Timestamp</th><th>Acties</th></tr>";
        // Output de gegevens van elke rij
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"]. "</td><td>" . $row["gebruikersnaam"]. "</td><td>" . $row["mood_score"]. "</td><td>" . $row["Huidige Opleiding"]. "</td><td>" . $row["Komende Opleiding"]. "</td><td>" . $row["timestamp"]. "</td><td><form action='' method='post'><input type='hidden' name='id_to_delete' value='" . $row["id"] . "'><input type='submit' name='delete' value='Verwijder'></form></td></tr>";
            $mood_scores[] = $row["mood_score"];
        }
        echo "</table>";
    } else {
        echo "0 results";
    }

    if (isset($_POST['delete'])) {
        $id_to_delete = $conn->real_escape_string($_POST['id_to_delete']);
        $sql = "DELETE FROM Informatica_MoodMeter WHERE id=$id_to_delete";
        if ($conn->query($sql) === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
    
    $sql = "SELECT * FROM Informatica_MoodMeter";
    if (isset($_POST['search'])) {
        $search = $conn->real_escape_string($_POST['search']);
        $sql .= " WHERE `gebruikersnaam` LIKE '%$search%' OR `Huidige Opleiding` LIKE '%$search%' OR `Komende Opleiding` LIKE '%$search%'";
    }

    $conn->close();
    ?>

    <canvas id="myChart"></canvas>

    <script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($mood_scores)); ?>,
            datasets: [{
                label: 'Mood Score',
                data: <?php echo json_encode(array_values($mood_scores)); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>
</body>
</html>