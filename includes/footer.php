    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <span class="logo-text">Genova</span>
                        <span class="logo-dot">.</span>
                    </div>
                    <p><?php echo SITE_TAGLINE; ?></p>
                    <div class="social-links">
                        <?php foreach($socials as $platform => $link): ?>
                        <a href="<?php echo $link; ?>" target="_blank" aria-label="<?php echo ucfirst($platform); ?>">
                            <i class="fab fa-<?php echo $platform; ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="about.php">À propos</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="services.php#web">Sites Web</a></li>
                        <li><a href="services.php#mobile">Apps Mobiles</a></li>
                        <li><a href="services.php#digital">Stratégie Digitale</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Contact</h4>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo $contact['address']; ?></span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span><?php echo $contact['phone']; ?></span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span><?php echo $contact['email']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo SITE_YEAR; ?> Genova. Tous droits réservés.</p>
                <div class="footer-links">
                    <a href="#">Mentions légales</a>
                    <a href="#">Politique de confidentialité</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to top -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Scripts -->
    <script src="js/main.js"></script>
</body>
</html>