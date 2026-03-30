<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            // Détecter si on est sur Render
            $isProduction = getenv('APP_ENV') === 'production' || 
                           !empty(getenv('RENDER')) ||
                           !empty(getenv('DATABASE_URL'));
            
            if ($isProduction) {
                // Utiliser PostgreSQL sur Neon.tech
                $dbUrl = getenv('DATABASE_URL');
                
                if (!$dbUrl) {
                    die("Erreur: DATABASE_URL non définie sur Render");
                }
                
                // Connexion PostgreSQL avec SSL
                $this->connection = new PDO($dbUrl, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 30
                ]);
                
                // Forcer SSL
                $this->connection->exec("SET sslmode = 'require'");
                
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
        // ... (le reste du code de création des tables)
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