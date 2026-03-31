<?php
require_once 'config/config.php';

$pageTitle = "À propos - " . SITE_NAME;
$pageDescription = "Découvrez notre histoire, notre mission et notre équipe";

include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>À propos de Genova</h1>
            <p>Notre histoire, notre mission, notre équipe</p>
        </div>
    </section>
    
    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-content">
                    <div class="mission-box">
                        <div class="icon-box">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h2>Notre mission</h2>
                        <p>Chez Genova, nous croyons que la technologie doit être au service de l'humain. Notre mission est d'accompagner les entreprises dans leur transformation digitale avec des solutions innovantes et sur mesure.</p>
                    </div>
                    
                    <div class="history-box">
                        <div class="icon-box">
                            <i class="fas fa-history"></i>
                        </div>
                        <h2>Notre histoire</h2>
                        <p>Fondée en 2020, Genova est née de la passion commune pour le digital et l'innovation. Aujourd'hui, nous sommes une équipe de passionnés qui mettent leur expertise au service de vos projets.</p>
                        <div class="timeline">
                            <div class="timeline-item">
                                <span class="year">2020</span>
                                <span class="event">Création de Genova</span>
                            </div>
                            <div class="timeline-item">
                                <span class="year">2022</span>
                                <span class="event">+50 projets réalisés</span>
                            </div>
                            <div class="timeline-item">
                                <span class="year">2024</span>
                                <span class="event">Équipe de 10 experts</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="values-box">
                        <div class="icon-box">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h2>Nos valeurs</h2>
                        <div class="values-grid">
                            <div class="value-card">
                                <i class="fas fa-lightbulb"></i>
                                <h3>Innovation</h3>
                                <p>Toujours à la pointe des technologies</p>
                            </div>
                            <div class="value-card">
                                <i class="fas fa-star"></i>
                                <h3>Qualité</h3>
                                <p>Des solutions robustes et durables</p>
                            </div>
                            <div class="value-card">
                                <i class="fas fa-comments"></i>
                                <h3>Transparence</h3>
                                <p>Une communication claire et honnête</p>
                            </div>
                            <div class="value-card">
                                <i class="fas fa-handshake"></i>
                                <h3>Engagement</h3>
                                <p>Accompagnement sur le long terme</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <h2>Notre équipe</h2>
                <p>Des experts passionnés à votre service</p>
            </div>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-image">
                        <img src="images/jo.jpeg" alt="Joseph Kalvin Gbilimou">
                    </div>
                    <div class="team-info">
                        <h3>Joseph Kalvin Gbilimou</h3>
                        
                        <p>Expert en developpement full-stack, mobile hybrid avec plus de 5 ans d'expérience</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <img src="images/alpha.jpeg" alt="Mamadou Alpha Diallo">
                    </div>
                    <div class="team-info">
                        <h3>Mamadou Alpha Diallo</h3>
                    
                        <p>Expert en développement full-stack , Analyste</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <img src="images/arfan.jpeg" alt="Arfan Mohamed Youla">
                    </div>
                    <div class="team-info">
                        <h3>Arfan Mohamed Youla</h3>
                        
                        <p>Spécialiste en UI/UX et design thinking</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-dribbble"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-card">
                    <div class="team-image">
                        <img src="images/sacko.jpeg" alt="Yacouba Sacko">
                    </div>
                    <div class="team-info">
                        <h3>Yacouba Sacko</h3>
                       
                        <p>Expert en maintenance réseau</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-dribbble"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-card">
                    <div class="team-image">
                        <img src="images/sory.jpeg" alt="Ibrahima Sory Fofana">
                    </div>
                    <div class="team-info">
                        <h3>Ibrahima Sory Fofana</h3>
                        
                        <p>Technicien</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-dribbble"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-card">
                    <div class="team-image">
                        <img src="images/bouba.jpeg" alt="Boubacar Diallo">
                    </div>
                    <div class="team-info">
                        <h3>Boubacar Diallo</h3>
                        
                        <p>Administrateur Sécurité</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-dribbble"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-card">
                    <div class="team-image">
                        <img src="images/saliou.jpeg" alt="Saliou Diallo">
                    </div>
                    <div class="team-info">
                        <h3>Saliou Diallo</h3>
                        
                        <p>Expert en developpement</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-dribbble"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-card">
                    <div class="team-image">
                        <img src="images/..........jpeg" alt="Saliou ..........">
                    </div>
                    <div class="team-info">
                        <h3>............</h3>
                        
                        <p>Graphiste</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-dribbble"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-card">
                    <div class="team-image">
                        <img src="images/.........jpeg" alt="Saliou ..........">
                    </div>
                    <div class="team-info">
                        <h3>............</h3>
                        
                        <p>Graphiste</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-dribbble"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">10+</div>
                    <div class="stat-label">Projets réalisés</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10+</div>
                    <div class="stat-label">Clients satisfaits</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10+</div>
                    <div class="stat-label">Experts passionnés</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support client</div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Rejoignez l'aventure Genova</h2>
                <p>Vous souhaitez travailler avec nous ou rejoindre notre équipe ?</p>
                <div class="cta-buttons">
                    <a href="contact.php" class="btn btn-primary">Nous contacter</a>
                    <a href="services.php" class="btn btn-outline">Découvrir nos services</a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
/* Page Header */
.page-header {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    color: white;
    padding: 120px 0 60px;
    text-align: center;
    margin-top: 70px;
}

.page-header h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.page-header p {
    font-size: 1.2rem;
    opacity: 0.9;
}

/* About Section */
.about-section {
    padding: 80px 0;
    background: #f9fafb;
}

.about-grid {
    max-width: 1000px;
    margin: 0 auto;
}

.mission-box,
.history-box,
.values-box {
    background: white;
    border-radius: 16px;
    padding: 40px;
    margin-bottom: 40px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.mission-box:hover,
.history-box:hover,
.values-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.icon-box {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
}

.icon-box i {
    font-size: 2rem;
    color: white;
}

.mission-box h2,
.history-box h2,
.values-box h2 {
    font-size: 1.8rem;
    margin-bottom: 20px;
    color: #1f2937;
}

.mission-box p,
.history-box p {
    color: #6b7280;
    line-height: 1.8;
    font-size: 1.1rem;
}

/* Timeline */
.timeline {
    margin-top: 30px;
}

.timeline-item {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 15px;
    padding: 10px;
    background: #f9fafb;
    border-radius: 8px;
}

.year {
    font-weight: 700;
    font-size: 1.2rem;
    color: #10b981;
    min-width: 70px;
}

.event {
    color: #374151;
}

/* Values Grid */
.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    margin-top: 20px;
}

.value-card {
    text-align: center;
    padding: 20px;
    background: #f9fafb;
    border-radius: 12px;
    transition: transform 0.3s;
}

.value-card:hover {
    transform: translateY(-5px);
}

.value-card i {
    font-size: 2.5rem;
    color: #10b981;
    margin-bottom: 15px;
}

.value-card h3 {
    font-size: 1.2rem;
    margin-bottom: 10px;
    color: #1f2937;
}

.value-card p {
    font-size: 0.9rem;
    color: #6b7280;
}

/* Team Section */
.team-section {
    padding: 80px 0;
    background: white;
}

.section-header {
    text-align: center;
    margin-bottom: 50px;
}

.section-header h2 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    color: #1f2937;
}

.section-header p {
    color: #6b7280;
    font-size: 1.1rem;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
}

.team-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    text-align: center;
}

.team-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 25px rgba(0,0,0,0.1);
}

.team-image {
    width: 100%;
    height: 300px;
    overflow: hidden;
}

.team-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.team-card:hover .team-image img {
    transform: scale(1.1);
}

.team-info {
    padding: 24px;
}

.team-info h3 {
    font-size: 1.3rem;
    margin-bottom: 5px;
    color: #1f2937;
}

.team-position {
    color: #10b981;
    font-weight: 500;
    margin-bottom: 15px;
}

.team-info p {
    color: #6b7280;
    margin-bottom: 20px;
    font-size: 0.9rem;
}

.team-social {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.team-social a {
    width: 36px;
    height: 36px;
    background: #f3f4f6;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #10b981;
    text-decoration: none;
    transition: all 0.3s;
}

.team-social a:hover {
    background: #10b981;
    color: white;
    transform: translateY(-3px);
}

/* Stats Section */
.stats-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
    text-align: center;
}

.stat-number {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 10px;
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
}

/* CTA Section */
.cta-section {
    padding: 80px 0;
    background: #f9fafb;
    text-align: center;
}

.cta-content h2 {
    font-size: 2rem;
    margin-bottom: 15px;
    color: #1f2937;
}

.cta-content p {
    color: #6b7280;
    margin-bottom: 30px;
    font-size: 1.1rem;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-outline {
    background: transparent;
    border: 2px solid #10b981;
    color: #10b981;
}

.btn-outline:hover {
    background: #10b981;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        padding: 100px 0 40px;
    }
    
    .page-header h1 {
        font-size: 2rem;
    }
    
    .mission-box,
    .history-box,
    .values-box {
        padding: 24px;
    }
    
    .mission-box h2,
    .history-box h2,
    .values-box h2 {
        font-size: 1.5rem;
    }
    
    .values-grid {
        grid-template-columns: 1fr;
    }
    
    .team-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-buttons .btn {
        width: 100%;
        max-width: 250px;
        justify-content: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>