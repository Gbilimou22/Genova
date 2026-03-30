<?php
// Chemins CORRECTS - SANS ESPACE
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/blog-functions.php';

$pageTitle = "Blog - " . SITE_NAME;
$pageDescription = BLOG_DESCRIPTION;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * POSTS_PER_PAGE;
$posts = getPosts(POSTS_PER_PAGE, $offset);
$categories = getAllCategories();
$popularPosts = getPopularPosts(5);
$recentPosts = getRecentPosts(5);
$archives = getArchives();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Reset et variables */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #34d399;
            --secondary: #6366f1;
            --dark: #111827;
            --gray-dark: #1f2937;
            --gray: #6b7280;
            --gray-light: #9ca3af;
            --light: #f9fafb;
            --white: #ffffff;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background: var(--white);
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--white);
            z-index: 1000;
            box-shadow: var(--shadow);
        }

        .navbar {
            padding: 1rem 0;
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo a {
            text-decoration: none;
            font-size: 1.75rem;
            font-weight: 800;
        }

        .logo-text {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .logo-dot {
            color: var(--primary);
            font-size: 2rem;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: var(--primary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            flex-direction: column;
            cursor: pointer;
            background: none;
            border: none;
        }

        .mobile-menu-btn span {
            width: 25px;
            height: 3px;
            background: var(--dark);
            margin: 3px 0;
            transition: var(--transition);
        }

        /* Blog Styles */
        .blog-header {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 120px 0 60px;
            text-align: center;
        }

        .blog-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .blog-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .blog-section {
            padding: 60px 0;
        }

        .blog-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 40px;
        }

        .posts-grid {
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .post-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .post-image img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .post-content {
            padding: 24px;
        }

        .post-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            font-size: 0.85rem;
            color: var(--gray);
            flex-wrap: wrap;
        }

        .post-meta i {
            margin-right: 5px;
        }

        .post-meta a {
            color: var(--primary);
            text-decoration: none;
        }

        .post-content h2 {
            margin: 0 0 15px 0;
            font-size: 1.5rem;
        }

        .post-content h2 a {
            color: var(--dark);
            text-decoration: none;
            transition: var(--transition);
        }

        .post-content h2 a:hover {
            color: var(--primary);
        }

        .post-excerpt {
            color: var(--gray);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .read-more {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .read-more:hover {
            gap: 12px;
        }

        /* Sidebar */
        .sidebar-widget {
            background: var(--light);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
        }

        .sidebar-widget h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
            font-size: 1.2rem;
        }

        .search-form {
            display: flex;
            gap: 10px;
        }

        .search-form input {
            flex: 1;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-family: inherit;
        }

        .search-form button {
            padding: 10px 15px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-form button:hover {
            background: var(--primary-dark);
        }

        .categories-list,
        .popular-posts {
            list-style: none;
            padding: 0;
        }

        .categories-list li,
        .popular-posts li {
            margin-bottom: 12px;
        }

        .categories-list a,
        .popular-posts a {
            display: flex;
            justify-content: space-between;
            color: var(--gray-dark);
            text-decoration: none;
            transition: var(--transition);
        }

        .categories-list a:hover,
        .popular-posts a:hover {
            color: var(--primary);
        }

        .categories-list span {
            background: #e5e7eb;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .popular-posts span {
            font-size: 0.75rem;
            color: var(--gray-light);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .page-link {
            padding: 8px 16px;
            background: var(--light);
            color: var(--gray-dark);
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
        }

        .page-link:hover {
            background: var(--primary);
            color: white;
        }

        .page-current {
            padding: 8px 16px;
            background: var(--primary);
            color: white;
            border-radius: 8px;
        }

        .no-posts {
            text-align: center;
            padding: 60px;
            background: var(--light);
            border-radius: 12px;
        }

        .no-posts i {
            font-size: 3rem;
            color: var(--gray-light);
            margin-bottom: 15px;
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: var(--white);
            padding: 60px 0 20px;
            margin-top: 60px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-logo {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .footer-col p {
            color: var(--gray-light);
            margin-bottom: 1rem;
        }

        .footer-col h4 {
            margin-bottom: 1rem;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 0.5rem;
        }

        .footer-col ul li a {
            color: var(--gray-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-col ul li a:hover {
            color: var(--primary);
        }

        .contact-info li {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .contact-info li i {
            color: var(--primary);
            margin-top: 0.25rem;
        }

        .footer-bottom {
            padding: 1.5rem 0;
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: flex;
            }
            
            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--white);
                flex-direction: column;
                padding: 1rem;
                text-align: center;
                box-shadow: var(--shadow);
            }
            
            .nav-menu.active {
                display: flex;
            }
            
            .blog-layout {
                grid-template-columns: 1fr;
            }
            
            .blog-header h1 {
                font-size: 2rem;
            }
            
            .post-image img {
                height: 200px;
            }
            
            .post-meta {
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <a href="/Genova/index.php">
                        <span class="logo-text">Genova</span>
                        <span class="logo-dot">.</span>
                    </a>
                </div>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <ul class="nav-menu" id="navMenu">
                    <li><a href="/Genova/index.php">Accueil</a></li>
                    <li><a href="/Genova/services.php">Services</a></li>
                    <li><a href="/Genova/portfolio.php">Portfolio</a></li>
                    <li><a href="/Genova/blog/index.php" class="active">Blog</a></li>
                    <li><a href="/Genova/about.php">À propos</a></li>
                    <li><a href="/Genova/contact.php">Contact</a></li>
                </ul>
                
                <div class="nav-actions">
                    <a href="/Genova/contact.php" class="btn btn-primary btn-sm">Devis gratuit</a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="blog-header">
            <div class="container">
                <h1>Blog Genova</h1>
                <p><?php echo BLOG_DESCRIPTION; ?></p>
            </div>
        </section>
        
        <section class="blog-section">
            <div class="container">
                <div class="blog-layout">
                    <div class="blog-main">
                        <?php if (empty($posts)): ?>
                            <div class="no-posts">
                                <i class="fas fa-newspaper"></i>
                                <p>Aucun article pour le moment. Revenez bientôt !</p>
                                <a href="../admin/add-post.php" class="btn btn-primary">Créer un article</a>
                            </div>
                        <?php else: ?>
                            <div class="posts-grid">
                                <?php foreach($posts as $post): ?>
                                    <article class="post-card">
                                        <?php if($post['featured_image']): ?>
                                            <div class="post-image">
                                                <a href="article.php?slug=<?php echo $post['slug']; ?>">
                                                    <img src="<?php echo $post['featured_image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <div class="post-content">
                                            <div class="post-meta">
                                                <span class="post-category">
                                                    <i class="fas fa-folder"></i>
                                                    <a href="category.php?slug=<?php echo $post['category_slug']; ?>">
                                                        <?php echo htmlspecialchars($post['category_name']); ?>
                                                    </a>
                                                </span>
                                                <span class="post-date">
                                                    <i class="fas fa-calendar"></i>
                                                    <?php echo date('d/m/Y', strtotime($post['published_at'])); ?>
                                                </span>
                                                <span class="post-views">
                                                    <i class="fas fa-eye"></i>
                                                    <?php echo $post['views']; ?> vues
                                                </span>
                                                <span class="post-comments">
                                                    <i class="fas fa-comment"></i>
                                                    <?php echo $post['comment_count']; ?> commentaires
                                                </span>
                                            </div>
                                            <h2>
                                                <a href="article.php?slug=<?php echo $post['slug']; ?>">
                                                    <?php echo htmlspecialchars($post['title']); ?>
                                                </a>
                                            </h2>
                                            <p class="post-excerpt">
                                                <?php echo htmlspecialchars(substr($post['excerpt'] ?: strip_tags($post['content']), 0, 150)) . '...'; ?>
                                            </p>
                                            <a href="article.php?slug=<?php echo $post['slug']; ?>" class="read-more">
                                                Lire la suite <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="pagination">
                                <?php if($page > 1): ?>
                                    <a href="?page=<?php echo $page-1; ?>" class="page-link">
                                        <i class="fas fa-chevron-left"></i> Précédent
                                    </a>
                                <?php endif; ?>
                                <span class="page-current">Page <?php echo $page; ?></span>
                                <?php if(count($posts) == POSTS_PER_PAGE): ?>
                                    <a href="?page=<?php echo $page+1; ?>" class="page-link">
                                        Suivant <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <aside class="blog-sidebar">
                        <div class="sidebar-widget">
                            <h3>Rechercher</h3>
                            <form action="search.php" method="GET" class="search-form">
                                <input type="text" name="q" placeholder="Rechercher un article...">
                                <button type="submit"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                        
                        <div class="sidebar-widget">
                            <h3>Catégories</h3>
                            <ul class="categories-list">
                                <?php foreach($categories as $cat): ?>
                                    <li>
                                        <a href="category.php?slug=<?php echo $cat['slug']; ?>">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                            <span>(<?php echo $cat['post_count']; ?>)</span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div class="sidebar-widget">
                            <h3>Articles populaires</h3>
                            <ul class="popular-posts">
                                <?php foreach($popularPosts as $post): ?>
                                    <li>
                                        <a href="article.php?slug=<?php echo $post['slug']; ?>">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                            <span><?php echo $post['views']; ?> vues</span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </main>
    
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <span class="logo-text">Genova</span>
                        <span class="logo-dot">.</span>
                    </div>
                    <p><?php echo SITE_TAGLINE; ?></p>
                </div>
                <div class="footer-col">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="/Genova/index.php">Accueil</a></li>
                        <li><a href="/Genova/services.php">Services</a></li>
                        <li><a href="/Genova/portfolio.php">Portfolio</a></li>
                        <li><a href="/Genova/blog/index.php">Blog</a></li>
                        <li><a href="/Genova/about.php">À propos</a></li>
                        <li><a href="/Genova/contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact</h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> <?php echo $contact['address']; ?></li>
                        <li><i class="fas fa-phone"></i> <?php echo $contact['phone']; ?></li>
                        <li><i class="fas fa-envelope"></i> <?php echo $contact['email']; ?></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Genova. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    
    <script>
        // Menu mobile
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navMenu = document.getElementById('navMenu');
        
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                navMenu.classList.toggle('active');
                mobileMenuBtn.classList.toggle('active');
            });
        }
        
        // Fermer le menu au clic sur un lien
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                mobileMenuBtn.classList.remove('active');
            });
        });
    </script>
</body>
</html>