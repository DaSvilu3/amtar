#!/bin/bash

###############################################################################
# AMTAR Engineering System - Backup Script
#
# This script creates backups of:
# - MySQL database
# - Uploaded files (storage/app/)
# - Environment configuration (.env)
#
# Usage: ./scripts/backup.sh
#
# Prerequisites:
# - MySQL client installed
# - Sufficient disk space in backup directory
# - Correct permissions (chmod +x backup.sh)
###############################################################################

set -e  # Exit on error

# Configuration
APP_DIR="/var/www/html/amtar"
BACKUP_DIR="/var/backups/amtar"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if script is run from correct directory
if [ ! -f "$APP_DIR/artisan" ]; then
    log_error "Application not found at $APP_DIR"
    log_error "Please update APP_DIR variable in this script"
    exit 1
fi

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

log_info "Starting backup process..."
log_info "Date: $(date)"
log_info "Backup directory: $BACKUP_DIR"

# Load environment variables
if [ -f "$APP_DIR/.env" ]; then
    source <(grep -v '^#' "$APP_DIR/.env" | sed 's/\r$//' | sed 's/^/export /')
else
    log_error ".env file not found at $APP_DIR/.env"
    exit 1
fi

# Check required environment variables
if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
    log_error "Database credentials not found in .env file"
    exit 1
fi

###############################################################################
# 1. DATABASE BACKUP
###############################################################################

log_info "Backing up database: $DB_DATABASE"

DB_BACKUP_FILE="$BACKUP_DIR/database_${DATE}.sql"
DB_BACKUP_GZ="$BACKUP_DIR/database_${DATE}.sql.gz"

# Create database dump
if mysqldump \
    --user="$DB_USERNAME" \
    --password="$DB_PASSWORD" \
    --host="${DB_HOST:-127.0.0.1}" \
    --port="${DB_PORT:-3306}" \
    --single-transaction \
    --routines \
    --triggers \
    --databases "$DB_DATABASE" \
    > "$DB_BACKUP_FILE" 2>/dev/null; then

    # Compress the dump
    gzip -f "$DB_BACKUP_FILE"

    DB_SIZE=$(du -h "$DB_BACKUP_GZ" | cut -f1)
    log_info "Database backup completed: $DB_BACKUP_GZ ($DB_SIZE)"
else
    log_error "Database backup failed"
    exit 1
fi

###############################################################################
# 2. FILE BACKUP (storage/app/)
###############################################################################

log_info "Backing up uploaded files..."

FILE_BACKUP="$BACKUP_DIR/files_${DATE}.tar.gz"

# Backup storage/app directory (contains uploads)
if tar -czf "$FILE_BACKUP" \
    -C "$APP_DIR" \
    storage/app \
    --exclude='storage/app/public/.gitignore' \
    2>/dev/null; then

    FILE_SIZE=$(du -h "$FILE_BACKUP" | cut -f1)
    log_info "File backup completed: $FILE_BACKUP ($FILE_SIZE)"
else
    log_error "File backup failed"
    exit 1
fi

###############################################################################
# 3. CONFIGURATION BACKUP (.env)
###############################################################################

log_info "Backing up configuration..."

ENV_BACKUP="$BACKUP_DIR/env_${DATE}.enc"

# Encrypt .env file (requires openssl)
if command -v openssl &> /dev/null; then
    # Generate random password for this backup
    BACKUP_PASSWORD=$(openssl rand -base64 32)

    # Encrypt .env
    openssl enc -aes-256-cbc \
        -salt \
        -in "$APP_DIR/.env" \
        -out "$ENV_BACKUP" \
        -k "$BACKUP_PASSWORD" 2>/dev/null

    # Save password to secure file
    echo "$BACKUP_PASSWORD" > "$BACKUP_DIR/env_${DATE}.key"
    chmod 600 "$BACKUP_DIR/env_${DATE}.key"

    log_info "Configuration backup completed (encrypted): $ENV_BACKUP"
    log_warn "Encryption key saved to: $BACKUP_DIR/env_${DATE}.key"
else
    # If openssl not available, copy without encryption
    cp "$APP_DIR/.env" "$BACKUP_DIR/env_${DATE}.txt"
    chmod 600 "$BACKUP_DIR/env_${DATE}.txt"
    log_warn "OpenSSL not found. Configuration backed up WITHOUT encryption"
fi

###############################################################################
# 4. CREATE BACKUP MANIFEST
###############################################################################

MANIFEST="$BACKUP_DIR/backup_${DATE}_manifest.txt"

cat > "$MANIFEST" << EOF
AMTAR Engineering System - Backup Manifest
===========================================

Backup Date: $(date)
Application: AMTAR Engineering System
Version: 1.0.0

Database Backup:
- File: $(basename "$DB_BACKUP_GZ")
- Size: $(du -h "$DB_BACKUP_GZ" | cut -f1)
- Database: $DB_DATABASE
- Host: ${DB_HOST:-127.0.0.1}

File Backup:
- File: $(basename "$FILE_BACKUP")
- Size: $(du -h "$FILE_BACKUP" | cut -f1)
- Contents: storage/app/ directory

Configuration Backup:
- File: $(basename "$ENV_BACKUP" 2>/dev/null || basename "$BACKUP_DIR/env_${DATE}.txt")
- Encrypted: $([ -f "$ENV_BACKUP" ] && echo "Yes" || echo "No")
- Key File: $([ -f "$BACKUP_DIR/env_${DATE}.key" ] && basename "$BACKUP_DIR/env_${DATE}.key" || echo "N/A")

Total Backup Size: $(du -sh "$BACKUP_DIR" | cut -f1)

Restoration Instructions:
========================

1. Database Restore:
   gunzip $DB_BACKUP_GZ
   mysql -u $DB_USERNAME -p $DB_DATABASE < database_${DATE}.sql

2. File Restore:
   cd $APP_DIR
   tar -xzf $FILE_BACKUP

3. Configuration Restore (if encrypted):
   openssl enc -aes-256-cbc -d -in $ENV_BACKUP -out .env -k \$(cat env_${DATE}.key)

EOF

log_info "Backup manifest created: $MANIFEST"

###############################################################################
# 5. CLEANUP OLD BACKUPS
###############################################################################

log_info "Cleaning up old backups (keeping last $RETENTION_DAYS days)..."

# Delete backups older than RETENTION_DAYS
find "$BACKUP_DIR" -type f -name "database_*.sql.gz" -mtime +$RETENTION_DAYS -delete
find "$BACKUP_DIR" -type f -name "files_*.tar.gz" -mtime +$RETENTION_DAYS -delete
find "$BACKUP_DIR" -type f -name "env_*" -mtime +$RETENTION_DAYS -delete
find "$BACKUP_DIR" -type f -name "backup_*_manifest.txt" -mtime +$RETENTION_DAYS -delete

BACKUP_COUNT=$(ls -1 "$BACKUP_DIR"/database_*.sql.gz 2>/dev/null | wc -l)
log_info "Current backup count: $BACKUP_COUNT"

###############################################################################
# 6. SUMMARY
###############################################################################

echo ""
echo "=========================================="
log_info "Backup completed successfully!"
echo "=========================================="
echo ""
echo "Backup location: $BACKUP_DIR"
echo "Database: $DB_BACKUP_GZ"
echo "Files: $FILE_BACKUP"
echo "Config: $([ -f "$ENV_BACKUP" ] && echo "$ENV_BACKUP" || echo "$BACKUP_DIR/env_${DATE}.txt")"
echo "Manifest: $MANIFEST"
echo ""
echo "Total size: $(du -sh "$BACKUP_DIR" | cut -f1)"
echo "Date: $(date)"
echo ""

# Optional: Send notification (uncomment if needed)
# if command -v mail &> /dev/null; then
#     echo "Backup completed successfully on $(hostname)" | mail -s "AMTAR Backup Success" admin@amtar.om
# fi

exit 0
