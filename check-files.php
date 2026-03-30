<?php
echo "<h1>Vérification des fichiers</h1>";

$files = [
    'config/config.php',
    'config/database.php',
    'config/blog.php',
    'includes/header.php',
    'includes/footer.php',
    'includes/blog-functions.php',
    'blog/index.php',
    'admin/categories.php',
    'admin/includes/admin-header.php',
    'admin/includes/admin-footer.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "✅ $file - OK<br>";
    } else {
        echo "❌ $file - MANQUANT<br>";
    }
}
?>