<?php
// Vérifier la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Récupérer les informations de l'utilisateur
$username = $_SESSION['username'] ?? 'Admin';
$role = $_SESSION['role'] ?? 'editor';

// Déterminer la page active
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Genova</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
        }
        
        /* Admin Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .admin-sidebar {
            width: 280px;
            background: #1f2937;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 100;
        }
        
        .admin-logo {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-logo h2 {
            margin: 0;
            font-size: 1.5rem;
            background: linear-gradient(135deg, #10b981, #34d399);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .admin-menu {
            list-style: none;
            padding: 1rem 0;
        }
        
        .admin-menu li a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .admin-menu li a:hover,
        .admin-menu li.active a {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .admin-menu li a i {
            width: 20px;
        }
        
        /* Main Content */
        .admin-content {
            flex: 1;
            margin-left: 280px;
        }
        
        /* Header */
        .admin-header {
            background: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .admin-header h1 {
            font-size: 1.5rem;
            color: #1f2937;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .logout-btn {
            color: #ef4444;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #fee2e2;
        }
        
        /* Mobile Menu Button */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #1f2937;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.active {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
        }
        
        /* Main Content Area */
        .admin-main {
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-logo">
                <h2>Genova Admin</h2>
            </div>
            <ul class="admin-menu">
                <li class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                    <a href="index.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="<?php echo $current_page == 'messages.php' ? 'active' : ''; ?>">
                    <a href="messages.php">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </a>
                </li>
                <li class="<?php echo $current_page == 'projects.php' ? 'active' : ''; ?>">
                    <a href="projects.php">
                        <i class="fas fa-folder-open"></i>
                        <span>Projets</span>
                    </a>
                </li>
                <li class="<?php echo $current_page == 'newsletter.php' ? 'active' : ''; ?>">
                    <a href="newsletter.php">
                        <i class="fas fa-newspaper"></i>
                        <span>Newsletter</span>
                    </a>
                </li>
                <li class="<?php echo $current_page == 'posts.php' || $current_page == 'add-post.php' || $current_page == 'edit-post.php' ? 'active' : ''; ?>">
                <a href="posts.php">
                    <i class="fas fa-newspaper"></i>
                    <span>Articles</span>
                </a>
                
                </li>
                <li class="<?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">
                    <a href="categories.php">
                        <i class="fas fa-tags"></i>
                        <span>Catégories</span>
                    </a>
                </li>
                <li class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                    <a href="settings.php">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>

                <li class="<?php echo $current_page == 'seo-settings.php' ? 'active' : ''; ?>">
                    <a href="seo-settings.php">
                        <i class="fas fa-chart-line"></i>
                        <span>SEO & Performance</span>
                    </a>
                </li>
                <li class="<?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                <a href="users.php">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </a>
                </li>
                <li class="<?php echo $current_page == 'export-data.php' ? 'active' : ''; ?>">
                    <a href="export-data.php">
                        <i class="fas fa-file-export"></i>
                        <span>Export</span>
                    </a>
                </li>
                <li class="<?php echo $current_page == 'backup.php' ? 'active' : ''; ?>">
                    <a href="backup.php">
                        <i class="fas fa-database"></i>
                        <span>Sauvegarde</span>
                    </a>
                </li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <div class="admin-content">
            <header class="admin-header">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Tableau de bord</h1>
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($username, 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($username); ?></span>
                    </div>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            </header>
            
            <main class="admin-main">