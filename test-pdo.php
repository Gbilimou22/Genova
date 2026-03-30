<?php
echo "<h1>Test PDO Drivers</h1>";

$drivers = PDO::getAvailableDrivers();
echo "<h2>Drivers disponibles :</h2>";
echo "<ul>";
foreach ($drivers as $driver) {
    echo "<li>" . $driver . "</li>";
}
echo "</ul>";

echo "<h2>Test PostgreSQL :</h2>";

if (in_array('pgsql', $drivers)) {
    echo "✅ Driver pgsql disponible<br>";
} else {
    echo "❌ Driver pgsql NON disponible<br>";
}

if (in_array('pgsql', $drivers)) {
    $dbUrl = getenv('DATABASE_URL');
    if ($dbUrl) {
        try {
            $pdo = new PDO($dbUrl);
            echo "✅ Connexion PostgreSQL réussie !<br>";
        } catch(Exception $e) {
            echo "❌ Erreur connexion : " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ DATABASE_URL non définie<br>";
    }
}
?>