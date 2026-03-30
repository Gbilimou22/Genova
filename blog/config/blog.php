<?php
// Configuration du blog
define('BLOG_NAME', 'Blog Genova');
define('BLOG_DESCRIPTION', 'Actualités, conseils et tendances du digital');
define('POSTS_PER_PAGE', 9);
define('COMMENTS_PER_PAGE', 20);
define('COMMENTS_APPROVAL_REQUIRED', true);
define('UPLOAD_DIR', __DIR__ . '/../uploads/blog/');
define('MAX_IMAGE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// Créer le dossier d'upload si nécessaire
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
?>