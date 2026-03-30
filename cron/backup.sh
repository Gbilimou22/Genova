#!/bin/bash
# Sauvegarde quotidienne

BACKUP_DIR="/var/backups/genova"
DB_NAME="genova_db"
DB_USER="root"
DB_PASS=""

# Créer le dossier si nécessaire
mkdir -p $BACKUP_DIR

# Sauvegarde de la base de données
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$(date +%Y%m%d).sql

# Sauvegarde des fichiers
tar -czf $BACKUP_DIR/files_$(date +%Y%m%d).tar.gz /var/www/genova/uploads /var/www/genova/images

# Supprimer les sauvegardes de plus de 30 jours
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete

echo "Sauvegarde effectuée le $(date)" >> $BACKUP_DIR/backup.log