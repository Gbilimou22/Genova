<?php
// Script d'installation pour la production
echo "🛠️ Installation de Genova en production\n\n";

// Vérifier PHP version
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    die("❌ PHP 8.0 ou supérieur requis\n");
}
echo "✅ PHP version: " . PHP_VERSION . "\n";

// Vérifier les extensions
$extensions = ['pdo_mysql', 'mysqli', 'gd', 'curl', 'json', 'openssl'];
foreach ($extensions as $ext) {
    if (!extension_loaded($ext)) {
        die("❌ Extension $ext manquante\n");
    }
}
echo "✅ Extensions PHP OK\n";

// Créer les dossiers
$dirs = ['logs', 'cache', 'backups', 'uploads'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Dossier créé: $dir\n";
    }
}

// Configurer les permissions
chmod('logs', 0755);
chmod('cache', 0755);
chmod('backups', 0755);
chmod('uploads', 0755);
echo "✅ Permissions configurées\n";

// Créer le fichier .env
if (!file_exists('.env')) {
    copy('.env.example', '.env');
    echo "✅ Fichier .env créé - MODIFIEZ-LE AVEC VOS INFORMATIONS\n";
}

echo "\n🎉 Installation terminée !\n";
echo "📝 Prochaines étapes:\n";
echo "1. Modifiez le fichier .env avec vos informations\n";
echo "2. Configurez SSL/HTTPS\n";
echo "3. Importez la base de données\n";
echo "4. Testez le site: " . ($_SERVER['HTTP_HOST'] ?? 'http://localhost') . "\n";
?>