# Checklist de déploiement Genova

## Avant déploiement
- [ ] Tester toutes les fonctionnalités en local
- [ ] Vérifier les erreurs PHP (error_reporting)
- [ ] Optimiser les images (compression)
- [ ] Minifier CSS/JS
- [ ] Vérifier les liens brisés
- [ ] Tester le responsive

## Base de données
- [ ] Exporter la base de données
- [ ] Vérifier les indexes
- [ ] Optimiser les tables
- [ ] Configurer les backups automatiques

## Sécurité
- [ ] Configurer SSL/HTTPS
- [ ] Modifier les mots de passe par défaut
- [ ] Configurer le pare-feu
- [ ] Activer la protection CSRF
- [ ] Configurer les logs
- [ ] Vérifier les permissions des dossiers

## Performance
- [ ] Activer la compression GZIP
- [ ] Configurer le cache navigateur
- [ ] Mettre en place CDN (Cloudflare)
- [ ] Optimiser les requêtes SQL
- [ ] Configurer Redis/Memcached

## SEO
- [ ] Vérifier le sitemap.xml
- [ ] Configurer Google Analytics
- [ ] Configurer Google Search Console
- [ ] Vérifier les meta tags
- [ ] Vérifier les URLs canoniques

## Monitoring
- [ ] Configurer les alertes email
- [ ] Mettre en place Uptime Robot
- [ ] Configurer les logs d'erreurs
- [ ] Vérifier les sauvegardes

## Après déploiement
- [ ] Tester le site en production
- [ ] Vérifier les formulaires
- [ ] Tester le paiement (si activé)
- [ ] Vérifier les emails
- [ ] Monitorer les erreurs pendant 24h