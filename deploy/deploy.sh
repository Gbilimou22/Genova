#!/bin/bash
# Script de déploiement automatique

echo "🚀 Déploiement de Genova en production..."

# Variables
REMOTE_USER="user"
REMOTE_HOST="example.com"
REMOTE_PATH="/var/www/genova"
BACKUP_PATH="/var/backups/genova"

# 1. Sauvegarde
echo "📦 Création de la sauvegarde..."
ssh $REMOTE_USER@$REMOTE_HOST "mkdir -p $BACKUP_PATH && cp -r $REMOTE_PATH $BACKUP_PATH/backup_$(date +%Y%m%d_%H%M%S)"

# 2. Upload des fichiers
echo "📤 Upload des fichiers..."
rsync -avz --exclude 'logs/' --exclude 'uploads/' --exclude 'backups/' ./ $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/

# 3. Mise à jour des permissions
echo "🔒 Mise à jour des permissions..."
ssh $REMOTE_USER@$REMOTE_HOST "chmod -R 755 $REMOTE_PATH && chmod -R 777 $REMOTE_PATH/logs $REMOTE_PATH/uploads $REMOTE_PATH/backups"

# 4. Optimisation de la base de données
echo "🗄️ Optimisation de la base de données..."
ssh $REMOTE_USER@$REMOTE_HOST "mysql -u root -p genova_db < $REMOTE_PATH/sql/optimize.sql"

# 5. Vider le cache
echo "🧹 Vidage du cache..."
ssh $REMOTE_USER@$REMOTE_HOST "rm -rf $REMOTE_PATH/cache/*"

echo "✅ Déploiement terminé !"