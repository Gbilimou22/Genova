<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();

// Générer le hash du mot de passe
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Vérifier si l'utilisateur existe déjà
$stmt = $db->prepare("SELECT * FROM users WHERE username = 'admin'");
$stmt->execute();
$existingUser = $stmt->fetch();

if (!$existingUser) {
    // Créer l'utilisateur admin
    $sql = "INSERT INTO users (username, password, email, role) 
            VALUES (:username, :password, :email, :role)";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':username' => 'admin',
        ':password' => $hashedPassword,
        ':email' => 'admingenova@gmail.com',
        ':role' => 'admin'
    ]);
    
    echo "✅ Utilisateur admin créé avec succès !<br>";
    echo "Identifiants :<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
} else {
    echo "⚠️ L'utilisateur admin existe déjà.<br>";
}

echo "<br><a href='admin/login.php'>Accéder à l'administration</a>";
?>