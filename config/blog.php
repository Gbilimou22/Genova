<?php
// Configuration du blog
if (!defined('BLOG_NAME')) {
    define('BLOG_NAME', 'Blog Genova');
}

if (!defined('BLOG_DESCRIPTION')) {
    define('BLOG_DESCRIPTION', 'Actualités, conseils et tendances du digital');
}

if (!defined('POSTS_PER_PAGE')) {
    define('POSTS_PER_PAGE', 9);
}

if (!defined('COMMENTS_PER_PAGE')) {
    define('COMMENTS_PER_PAGE', 20);
}

if (!defined('COMMENTS_APPROVAL_REQUIRED')) {
    define('COMMENTS_APPROVAL_REQUIRED', true);
}

if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/../uploads/blog/');
}

if (!defined('MAX_IMAGE_SIZE')) {
    define('MAX_IMAGE_SIZE', 5242880);
}

if (!defined('ALLOWED_IMAGE_TYPES')) {
    define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
}

// Créer le dossier d'upload si nécessaire
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
?>