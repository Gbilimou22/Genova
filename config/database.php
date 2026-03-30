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
                // Utiliser PostgreSQL sur Neon.tech
                $dbUrl = getenv('DATABASE_URL');
                
                if (!$dbUrl) {
                    die("Erreur: DATABASE_URL non définie sur Render");
                }
                
                $this->connection = new PDO($dbUrl);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // CRÉER LES TABLES AUTOMATIQUEMENT
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
        try {
            // 1. Table contacts
            $this->connection->exec("CREATE TABLE IF NOT EXISTS contacts (
                id SERIAL PRIMARY KEY,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                subject TEXT NOT NULL,
                message TEXT NOT NULL,
                status TEXT DEFAULT 'non lu',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // 2. Table blog_categories
            $this->connection->exec("CREATE TABLE IF NOT EXISTS blog_categories (
                id SERIAL PRIMARY KEY,
                name TEXT NOT NULL UNIQUE,
                slug TEXT NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // 3. Table blog_posts
            $this->connection->exec("CREATE TABLE IF NOT EXISTS blog_posts (
                id SERIAL PRIMARY KEY,
                title TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                excerpt TEXT,
                content TEXT NOT NULL,
                featured_image TEXT,
                category_id INTEGER,
                author TEXT,
                views INTEGER DEFAULT 0,
                status TEXT DEFAULT 'draft',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                published_at TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
            )");
            
            // 4. Table blog_comments
            $this->connection->exec("CREATE TABLE IF NOT EXISTS blog_comments (
                id SERIAL PRIMARY KEY,
                post_id INTEGER NOT NULL,
                author TEXT NOT NULL,
                email TEXT NOT NULL,
                website TEXT,
                content TEXT NOT NULL,
                status TEXT DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
            )");
            
            // 5. Table users
            $this->connection->exec("CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                role TEXT DEFAULT 'editor',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // 6. Table newsletter
            $this->connection->exec("CREATE TABLE IF NOT EXISTS newsletter (
                id SERIAL PRIMARY KEY,
                email TEXT UNIQUE NOT NULL,
                token TEXT,
                confirmed BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // 7. Table bookings (réservations)
            $this->connection->exec("CREATE TABLE IF NOT EXISTS bookings (
                id SERIAL PRIMARY KEY,
                service TEXT NOT NULL,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                date DATE NOT NULL,
                time TIME NOT NULL,
                message TEXT,
                status TEXT DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // 8. INSÉRER L'UTILISATEUR ADMIN PAR DÉFAUT
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $this->connection->prepare("
                INSERT INTO users (username, password, email, role) 
                VALUES ('admin', :password, 'admin@genova.com', 'admin')
                ON CONFLICT (username) DO NOTHING
            ");
            $stmt->execute([':password' => $password]);
            
            // 9. INSÉRER UNE CATÉGORIE PAR DÉFAUT
            $stmt = $this->connection->prepare("
                INSERT INTO blog_categories (name, slug) 
                VALUES ('Actualités', 'actualites')
                ON CONFLICT (slug) DO NOTHING
            ");
            $stmt->execute();
            
            echo "✅ Tables créées avec succès !";
            
        } catch(PDOException $e) {
            // Afficher l'erreur mais ne pas bloquer
            error_log("Erreur création tables: " . $e->getMessage());
        }
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