<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/blog-functions.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

$post = getPostBySlug($slug);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    include dirname(__DIR__) . '/404.php';
    exit;
}

// Incrémenter les vues
incrementViews($post['id']);

$pageTitle = $post['title'] . ' - Blog ' . SITE_NAME;
$pageDescription = $post['excerpt'] ?: substr(strip_tags($post['content']), 0, 160);

// Traitement du commentaire
$commentSuccess = false;
$commentError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $author = trim($_POST['author']);
    $email = trim($_POST['email']);
    $content = trim($_POST['content']);
    $website = trim($_POST['website']);
    
    if (empty($author)) {
        $commentError = 'Veuillez saisir votre nom';
    } elseif (empty($email)) {
        $commentError = 'Veuillez saisir votre email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $commentError = 'Email invalide';
    } elseif (empty($content)) {
        $commentError = 'Veuillez saisir votre commentaire';
    } elseif (strlen($content) < 10) {
        $commentError = 'Le commentaire doit contenir au moins 10 caractères';
    } else {
        if (addComment($post['id'], $author, $email, $content, $website)) {
            $commentSuccess = true;
            header('Location: article.php?slug=' . $slug . '&success=1');
            exit;
        } else {
            $commentError = 'Erreur lors de l\'envoi du commentaire';
        }
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $commentSuccess = true;
}

$comments = getComments($post['id']);
$categories = getAllCategories();
$popularPosts = getPopularPosts(5);
$recentPosts = getRecentPosts(5);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Blog Genova</title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
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

        /* Article Styles */
        .blog-article {
            padding: 120px 0 60px;
        }

        .article-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 40px;
        }

        .breadcrumb {
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: var(--gray);
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb i {
            margin: 0 10px;
            font-size: 0.7rem;
        }

        .article-image img {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .article-header {
            margin-bottom: 30px;
        }

        .article-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: var(--gray);
            flex-wrap: wrap;
        }

        .article-meta i {
            margin-right: 5px;
        }

        .article-meta a {
            color: var(--primary);
            text-decoration: none;
        }

        .article-header h1 {
            font-size: 2.5rem;
            margin: 0;
            color: var(--dark);
        }

        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--gray-dark);
            margin-bottom: 40px;
        }

        .article-content p {
            margin-bottom: 1.5em;
        }

        .article-content h2 {
            margin: 1.5em 0 0.5em;
            font-size: 1.8rem;
            color: var(--dark);
        }

        .article-content h3 {
            margin: 1.2em 0 0.5em;
            font-size: 1.4rem;
            color: var(--dark);
        }

        /* Share Section */
        .article-share {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px 0;
            margin-bottom: 30px;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }

        .article-share span {
            font-weight: 500;
            color: var(--dark);
        }

        .article-share a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: transform 0.3s;
        }

        .article-share a:hover {
            transform: translateY(-3px);
        }

        .share-facebook { background: #1877f2; }
        .share-twitter { background: #1da1f2; }
        .share-linkedin { background: #0077b5; }

        /* Comments Section */
        .article-comments-section {
            border-top: 2px solid #e5e7eb;
            padding-top: 40px;
            margin-top: 20px;
        }

        .article-comments-section h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .comments-list {
            margin-bottom: 40px;
        }

        .comment {
            background: var(--light);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .comment-header strong {
            color: var(--primary);
        }

        .comment-header span {
            font-size: 0.85rem;
            color: var(--gray);
        }

        .no-comments {
            color: var(--gray);
            margin-bottom: 30px;
        }

        .comment-form {
            background: var(--light);
            border-radius: 12px;
            padding: 30px;
        }

        .comment-form h4 {
            margin: 0 0 20px 0;
            font-size: 1.2rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-family: inherit;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid var(--primary);
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
        }

        /* Sidebar */
        .sidebar-widget {
            background: var(--light);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
        }

        .sidebar-widget h3 {
            margin: 0 0 20px 0;
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
        .popular-posts,
        .recent-posts {
            list-style: none;
            padding: 0;
        }

        .categories-list li,
        .popular-posts li,
        .recent-posts li {
            margin-bottom: 12px;
        }

        .categories-list a,
        .popular-posts a,
        .recent-posts a {
            display: flex;
            justify-content: space-between;
            color: var(--gray-dark);
            text-decoration: none;
            transition: var(--transition);
        }

        .categories-list a:hover,
        .popular-posts a:hover,
        .recent-posts a:hover {
            color: var(--primary);
        }

        .categories-list span {
            background: #e5e7eb;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .popular-posts span,
        .recent-posts span {
            font-size: 0.75rem;
            color: var(--gray-light);
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
            
            .article-layout {
                grid-template-columns: 1fr;
            }
            
            .article-header h1 {
                font-size: 1.8rem;
            }
            
            .article-meta {
                gap: 10px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
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
        <article class="blog-article">
            <div class="container">
                <div class="article-layout">
                    <div class="article-main">
                        <div class="breadcrumb">
                            <a href="index.php">Blog</a>
                            <i class="fas fa-chevron-right"></i>
                            <span><?php echo htmlspecialchars($post['title']); ?></span>
                        </div>
                        
                        <?php if($post['featured_image']): ?>
                            <div class="article-image">
                                <img src="<?php echo $post['featured_image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="article-header">
                            <div class="article-meta">
                                <span class="article-category">
                                    <i class="fas fa-folder"></i>
                                    <a href="category.php?slug=<?php echo $post['category_slug']; ?>">
                                        <?php echo htmlspecialchars($post['category_name']); ?>
                                    </a>
                                </span>
                                <span class="article-date">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('d/m/Y', strtotime($post['published_at'])); ?>
                                </span>
                                <span class="article-author">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($post['author']); ?>
                                </span>
                                <span class="article-views">
                                    <i class="fas fa-eye"></i>
                                    <?php echo $post['views'] + 1; ?> vues
                                </span>
                                <span class="article-comments">
                                    <i class="fas fa-comment"></i>
                                    <?php echo count($comments); ?> commentaires
                                </span>
                            </div>
                            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                        </div>
                        
                        <div class="article-content">
                            <?php echo nl2br(htmlspecialchars_decode($post['content'])); ?>
                        </div>
                        
                        <div class="article-share">
                            <span>Partager :</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://localhost/Genova/blog/article.php?slug=' . $post['slug']); ?>" target="_blank" class="share-facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://localhost/Genova/blog/article.php?slug=' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="share-twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://localhost/Genova/blog/article.php?slug=' . $post['slug']); ?>&title=<?php echo urlencode($post['title']); ?>" target="_blank" class="share-linkedin">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                        
                        <div class="article-comments-section">
                            <h3>Commentaires (<?php echo count($comments); ?>)</h3>
                            
                            <?php if($commentSuccess): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    Votre commentaire a été envoyé et sera publié après validation.
                                </div>
                            <?php endif; ?>
                            
                            <?php if($commentError): ?>
                                <div class="alert alert-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo $commentError; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($comments)): ?>
                                <div class="comments-list">
                                    <?php foreach($comments as $comment): ?>
                                        <div class="comment">
                                            <div class="comment-header">
                                                <strong><?php echo htmlspecialchars($comment['author']); ?></strong>
                                                <span><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></span>
                                            </div>
                                            <div class="comment-content">
                                                <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-comments">Soyez le premier à commenter cet article !</p>
                            <?php endif; ?>
                            
                            <div class="comment-form">
                                <h4>Laisser un commentaire</h4>
                                <form method="POST" action="">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Nom *</label>
                                            <input type="text" name="author" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input type="email" name="email" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Site web</label>
                                        <input type="url" name="website">
                                    </div>
                                    <div class="form-group">
                                        <label>Commentaire *</label>
                                        <textarea name="content" rows="5" required></textarea>
                                    </div>
                                    <button type="submit" name="submit_comment" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Envoyer le commentaire
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <aside class="article-sidebar">
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
                                <?php foreach($popularPosts as $p): ?>
                                    <li>
                                        <a href="article.php?slug=<?php echo $p['slug']; ?>">
                                            <?php echo htmlspecialchars($p['title']); ?>
                                            <span><?php echo $p['views']; ?> vues</span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div class="sidebar-widget">
                            <h3>Articles récents</h3>
                            <ul class="recent-posts">
                                <?php foreach($recentPosts as $p): ?>
                                    <li>
                                        <a href="article.php?slug=<?php echo $p['slug']; ?>">
                                            <?php echo htmlspecialchars($p['title']); ?>
                                            <span><?php echo date('d/m/Y', strtotime($p['published_at'])); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </article>
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
        
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                mobileMenuBtn.classList.remove('active');
            });
        });
    </script>
</body>
</html>