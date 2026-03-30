<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            // Détecter si on est sur Render
            $isProduction = getenv('APP_ENV') === 'production' || 
                           !empty(getenv('RENDER'));
            
            if ($isProduction) {
                // Utiliser SQLite en production
                $dbFile = __DIR__ . '/../database.sqlite';
                
                // Créer le dossier si nécessaire
                $dbDir = dirname($dbFile);
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0777, true);
                }
                
                // Connexion SQLite
                $this->connection = new PDO("sqlite:$dbFile");
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->exec("PRAGMA foreign_keys = ON");
                
                // Créer les tables
                $this->createTables();
            } else {
                // MySQL en local (XAMPP)
                $this->connection = new PDO(
                    "mysql:host=localhost;dbname=genova_db;charset=utf8mb4",
                    "root",
                    "",
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            }
        } catch(PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
    
    private function createTables() {
        // 1. Table contacts
        $this->connection->exec("CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            status TEXT DEFAULT 'non lu',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // 2. Table blog_categories
        $this->connection->exec("CREATE TABLE IF NOT EXISTS blog_categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // 3. Table blog_posts
        $this->connection->exec("CREATE TABLE IF NOT EXISTS blog_posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            excerpt TEXT,
            content TEXT NOT NULL,
            featured_image TEXT,
            category_id INTEGER,
            author TEXT,
            views INTEGER DEFAULT 0,
            status TEXT DEFAULT 'draft',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            published_at DATETIME,
            FOREIGN KEY (category_id) REFERENCES blog_categories(id)
        )");
        
        // 4. Table blog_comments
        $this->connection->exec("CREATE TABLE IF NOT EXISTS blog_comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            post_id INTEGER NOT NULL,
            author TEXT NOT NULL,
            email TEXT NOT NULL,
            website TEXT,
            content TEXT NOT NULL,
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
        )");
        
        // 5. Table users
        $this->connection->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            role TEXT DEFAULT 'editor',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // 6. Table newsletter
        $this->connection->exec("CREATE TABLE IF NOT EXISTS newsletter (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            token TEXT,
            confirmed BOOLEAN DEFAULT FALSE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // 7. Table bookings
        $this->connection->exec("CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            service TEXT NOT NULL,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT,
            date TEXT NOT NULL,
            time TEXT NOT NULL,
            message TEXT,
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // 8. Insérer l'utilisateur admin
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $this->connection->prepare("INSERT OR IGNORE INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', $password, 'admin@genova.com', 'admin']);
        
        // 9. Insérer une catégorie par défaut
        $stmt = $this->connection->prepare("INSERT OR IGNORE INTO blog_categories (name, slug) VALUES (?, ?)");
        $stmt->execute(['Actualités', 'actualites']);
        
        echo "✅ Tables SQLite créées avec succès !";
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}
?>