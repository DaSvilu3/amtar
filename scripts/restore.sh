#!/bin/bash

###############################################################################
# AMTAR Engineering System - Restore Script
#
# This script restores backups created by backup.sh
#
# Usage: ./scripts/restore.sh BACKUP_DATE
# Example: ./scripts/restore.sh 20260125_143000
#
# Prerequisites:
# - MySQL client installed
# - Backup files exist in BACKUP_DIR
# - Application stopped (php artisan down)
###############################################################################

set -e  # Exit on error

# Configuration
APP_DIR="/var/www/html/amtar"
BACKUP_DIR="/var/backups/amtar"

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

# Check if backup date provided
if [ -z "$1" ]; then
    log_error "Usage: $0 BACKUP_DATE"
    log_error "Example: $0 20260125_143000"
    echo ""
    log_info "Available backups:"
    ls -1 "$BACKUP_DIR"/database_*.sql.gz 2>/dev/null | sed 's/.*database_//' | sed 's/.sql.gz//' | head -10
    exit 1
fi

BACKUP_DATE=$1

# Check if backup files exist
DB_BACKUP="$BACKUP_DIR/database_${BACKUP_DATE}.sql.gz"
FILE_BACKUP="$BACKUP_DIR/files_${BACKUP_DATE}.tar.gz"
ENV_BACKUP="$BACKUP_DIR/env_${BACKUP_DATE}.enc"
ENV_BACKUP_TXT="$BACKUP_DIR/env_${BACKUP_DATE}.txt"

if [ ! -f "$DB_BACKUP" ]; then
    log_error "Database backup not found: $DB_BACKUP"
    exit 1
fi

if [ ! -f "$FILE_BACKUP" ]; then
    log_error "File backup not found: $FILE_BACKUP"
    exit 1
fi

log_warn "=========================================="
log_warn "WARNING: This will OVERWRITE current data!"
log_warn "=========================================="
echo ""
echo "Backup to restore: $BACKUP_DATE"
echo "Database backup: $DB_BACKUP"
echo "File backup: $FILE_BACKUP"
echo ""

read -p "Are you sure you want to continue? (yes/no): " -r
if [[ ! $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
    log_info "Restore cancelled"
    exit 0
fi

# Load environment variables
if [ -f "$APP_DIR/.env" ]; then
    source <(grep -v '^#' "$APP_DIR/.env" | sed 's/\r$//' | sed 's/^/export /')
else
    log_error ".env file not found at $APP_DIR/.env"
    exit 1
fi

###############################################################################
# 1. PUT APPLICATION IN MAINTENANCE MODE
###############################################################################

log_info "Putting application in maintenance mode..."
cd "$APP_DIR"
php artisan down --message="System restore in progress. Please wait..." || true

###############################################################################
# 2. BACKUP CURRENT STATE (just in case)
###############################################################################

log_info "Creating safety backup of current state..."
SAFETY_BACKUP_DIR="$BACKUP_DIR/pre_restore_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$SAFETY_BACKUP_DIR"

# Backup current database
mysqldump \
    --user="$DB_USERNAME" \
    --password="$DB_PASSWORD" \
    --host="${DB_HOST:-127.0.0.1}" \
    --port="${DB_PORT:-3306}" \
    --single-transaction \
    --databases "$DB_DATABASE" \
    | gzip > "$SAFETY_BACKUP_DIR/database_current.sql.gz" 2>/dev/null

# Backup current files
tar -czf "$SAFETY_BACKUP_DIR/files_current.tar.gz" -C "$APP_DIR" storage/app 2>/dev/null

log_info "Safety backup created at: $SAFETY_BACKUP_DIR"

###############################################################################
# 3. RESTORE DATABASE
###############################################################################

log_info "Restoring database..."

# Decompress database backup
gunzip -c "$DB_BACKUP" > "/tmp/restore_${BACKUP_DATE}.sql"

# Restore database
if mysql \
    --user="$DB_USERNAME" \
    --password="$DB_PASSWORD" \
    --host="${DB_HOST:-127.0.0.1}" \
    --port="${DB_PORT:-3306}" \
    < "/tmp/restore_${BACKUP_DATE}.sql" 2>/dev/null; then

    log_info "Database restored successfully"
    rm "/tmp/restore_${BACKUP_DATE}.sql"
else
    log_error "Database restore failed"
    log_error "Restoring from safety backup..."

    gunzip -c "$SAFETY_BACKUP_DIR/database_current.sql.gz" | \
        mysql --user="$DB_USERNAME" --password="$DB_PASSWORD" \
        --host="${DB_HOST:-127.0.0.1}" --port="${DB_PORT:-3306}"

    log_info "Rolled back to previous state"
    rm "/tmp/restore_${BACKUP_DATE}.sql"
    exit 1
fi

###############################################################################
# 4. RESTORE FILES
###############################################################################

log_info "Restoring files..."

# Remove current storage/app directory
rm -rf "$APP_DIR/storage/app"

# Extract backup
if tar -xzf "$FILE_BACKUP" -C "$APP_DIR" 2>/dev/null; then
    log_info "Files restored successfully"
else
    log_error "File restore failed"
    log_error "Restoring from safety backup..."

    tar -xzf "$SAFETY_BACKUP_DIR/files_current.tar.gz" -C "$APP_DIR"

    log_info "Rolled back to previous state"
    exit 1
fi

# Fix permissions
chown -R www-data:www-data "$APP_DIR/storage/app" || true
chmod -R 775 "$APP_DIR/storage/app" || true

###############################################################################
# 5. RESTORE CONFIGURATION (optional)
###############################################################################

if [ -f "$ENV_BACKUP" ]; then
    log_warn "Encrypted .env backup found"
    read -p "Do you want to restore .env file? (yes/no): " -r

    if [[ $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
        KEY_FILE="$BACKUP_DIR/env_${BACKUP_DATE}.key"

        if [ -f "$KEY_FILE" ]; then
            # Backup current .env
            cp "$APP_DIR/.env" "$APP_DIR/.env.backup"

            # Decrypt and restore
            openssl enc -aes-256-cbc -d \
                -in "$ENV_BACKUP" \
                -out "$APP_DIR/.env" \
                -k "$(cat "$KEY_FILE")" 2>/dev/null

            log_info "Configuration restored (previous .env saved as .env.backup)"
        else
            log_error "Encryption key not found: $KEY_FILE"
            log_warn "Skipping .env restore"
        fi
    fi
elif [ -f "$ENV_BACKUP_TXT" ]; then
    log_warn "Unencrypted .env backup found"
    read -p "Do you want to restore .env file? (yes/no): " -r

    if [[ $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
        cp "$APP_DIR/.env" "$APP_DIR/.env.backup"
        cp "$ENV_BACKUP_TXT" "$APP_DIR/.env"
        chmod 600 "$APP_DIR/.env"
        log_info "Configuration restored (previous .env saved as .env.backup)"
    fi
fi

###############################################################################
# 6. CLEAR CACHES
###############################################################################

log_info "Clearing application caches..."

cd "$APP_DIR"
php artisan cache:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

log_info "Rebuilding caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

###############################################################################
# 7. RUN MIGRATIONS (if needed)
###############################################################################

log_warn "Checking if migrations are needed..."
read -p "Run migrations? (yes/no): " -r

if [[ $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
    php artisan migrate --force
    log_info "Migrations completed"
fi

###############################################################################
# 8. BRING APPLICATION BACK ONLINE
###############################################################################

log_info "Bringing application back online..."
php artisan up

###############################################################################
# 9. SUMMARY
###############################################################################

echo ""
echo "=========================================="
log_info "Restore completed successfully!"
echo "=========================================="
echo ""
echo "Restored from: $BACKUP_DATE"
echo "Database: $DB_BACKUP"
echo "Files: $FILE_BACKUP"
echo ""
echo "Safety backup created at: $SAFETY_BACKUP_DIR"
echo "(You can delete this after verifying the restore)"
echo ""
echo "Application is now online"
echo "Date: $(date)"
echo ""

log_warn "Please verify the application:"
log_warn "1. Check login works"
log_warn "2. Verify recent data"
log_warn "3. Test file uploads/downloads"
log_warn "4. Check task assignments"
echo ""

exit 0
