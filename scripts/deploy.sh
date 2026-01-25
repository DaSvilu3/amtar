#!/bin/bash

###############################################################################
# AMTAR Engineering System - Deployment Script
#
# This script automates the deployment process for production updates
#
# Usage: ./scripts/deploy.sh [options]
#
# Options:
#   --skip-backup     Skip pre-deployment backup
#   --skip-composer   Skip composer install
#   --skip-npm        Skip npm install
#   --skip-migrations Skip database migrations
#   --force           Skip all confirmations
#
# Example: ./scripts/deploy.sh --force
###############################################################################

set -e  # Exit on error

# Configuration
APP_DIR="/var/www/html/amtar"
BACKUP_DIR="/var/backups/amtar"
DEPLOY_DATE=$(date +%Y%m%d_%H%M%S)

# Parse command line arguments
SKIP_BACKUP=false
SKIP_COMPOSER=false
SKIP_NPM=false
SKIP_MIGRATIONS=false
FORCE=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --skip-backup)
            SKIP_BACKUP=true
            shift
            ;;
        --skip-composer)
            SKIP_COMPOSER=true
            shift
            ;;
        --skip-npm)
            SKIP_NPM=true
            shift
            ;;
        --skip-migrations)
            SKIP_MIGRATIONS=true
            shift
            ;;
        --force)
            FORCE=true
            shift
            ;;
        *)
            echo "Unknown option: $1"
            exit 1
            ;;
    esac
done

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
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

log_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Banner
echo ""
echo "=========================================="
echo "  AMTAR Engineering System - Deployment"
echo "=========================================="
echo ""

# Check if running from correct directory
if [ ! -f "$APP_DIR/artisan" ]; then
    log_error "Application not found at $APP_DIR"
    exit 1
fi

cd "$APP_DIR"

# Check git status
if [ -d ".git" ]; then
    log_info "Current branch: $(git branch --show-current)"
    log_info "Last commit: $(git log -1 --pretty=format:'%h - %s (%cr)')"

    if [ "$FORCE" = false ]; then
        echo ""
        read -p "Continue with deployment? (yes/no): " -r
        if [[ ! $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
            log_info "Deployment cancelled"
            exit 0
        fi
    fi
fi

echo ""
log_info "Starting deployment..."
log_info "Date: $(date)"

###############################################################################
# STEP 1: PRE-DEPLOYMENT BACKUP
###############################################################################

if [ "$SKIP_BACKUP" = false ]; then
    log_step "Step 1/10: Creating pre-deployment backup..."

    if [ -f "./scripts/backup.sh" ]; then
        bash ./scripts/backup.sh
    else
        log_warn "Backup script not found, skipping backup"
    fi
else
    log_warn "Skipping backup (--skip-backup)"
fi

###############################################################################
# STEP 2: ENABLE MAINTENANCE MODE
###############################################################################

log_step "Step 2/10: Enabling maintenance mode..."

php artisan down --message="System update in progress. Please wait..." --retry=60 || true

log_info "Application is now in maintenance mode"

###############################################################################
# STEP 3: PULL LATEST CODE (if git repo)
###############################################################################

log_step "Step 3/10: Updating code..."

if [ -d ".git" ]; then
    # Stash any local changes
    git stash save "Auto-stash before deployment $DEPLOY_DATE" || true

    # Pull latest code
    CURRENT_BRANCH=$(git branch --show-current)
    git pull origin "$CURRENT_BRANCH"

    log_info "Code updated from branch: $CURRENT_BRANCH"
else
    log_warn "Not a git repository, skipping code update"
fi

###############################################################################
# STEP 4: INSTALL/UPDATE COMPOSER DEPENDENCIES
###############################################################################

if [ "$SKIP_COMPOSER" = false ]; then
    log_step "Step 4/10: Installing Composer dependencies..."

    composer install --optimize-autoloader --no-dev --prefer-dist

    log_info "Composer dependencies installed"
else
    log_warn "Skipping composer install (--skip-composer)"
fi

###############################################################################
# STEP 5: INSTALL/UPDATE NPM DEPENDENCIES
###############################################################################

if [ "$SKIP_NPM" = false ]; then
    log_step "Step 5/10: Installing NPM dependencies..."

    npm ci --production

    log_info "NPM dependencies installed"
else
    log_warn "Skipping npm install (--skip-npm)"
fi

###############################################################################
# STEP 6: BUILD FRONTEND ASSETS
###############################################################################

log_step "Step 6/10: Building frontend assets..."

npm run build

log_info "Frontend assets built"

###############################################################################
# STEP 7: RUN DATABASE MIGRATIONS
###############################################################################

if [ "$SKIP_MIGRATIONS" = false ]; then
    log_step "Step 7/10: Running database migrations..."

    # Check if there are pending migrations
    PENDING=$(php artisan migrate:status 2>&1 | grep -c "Pending" || true)

    if [ "$PENDING" -gt 0 ]; then
        log_warn "Found $PENDING pending migrations"

        if [ "$FORCE" = false ]; then
            read -p "Run migrations now? (yes/no): " -r
            if [[ $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
                php artisan migrate --force
                log_info "Migrations completed"
            else
                log_warn "Skipping migrations"
            fi
        else
            php artisan migrate --force
            log_info "Migrations completed"
        fi
    else
        log_info "No pending migrations"
    fi
else
    log_warn "Skipping migrations (--skip-migrations)"
fi

###############################################################################
# STEP 8: CLEAR AND REBUILD CACHES
###############################################################################

log_step "Step 8/10: Clearing caches..."

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

log_info "Caches cleared"

log_step "Rebuilding caches..."

# Rebuild caches for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

log_info "Caches rebuilt"

###############################################################################
# STEP 9: SET CORRECT PERMISSIONS
###############################################################################

log_step "Step 9/10: Setting file permissions..."

# Set ownership (adjust www-data to your web server user)
chown -R www-data:www-data "$APP_DIR" || true

# Set directory permissions
find "$APP_DIR/storage" -type d -exec chmod 775 {} \; || true
find "$APP_DIR/bootstrap/cache" -type d -exec chmod 775 {} \; || true

# Set file permissions
find "$APP_DIR/storage" -type f -exec chmod 664 {} \; || true
find "$APP_DIR/bootstrap/cache" -type f -exec chmod 664 {} \; || true

# Secure .env file
chmod 600 "$APP_DIR/.env" || true

log_info "Permissions set"

###############################################################################
# STEP 10: RESTART SERVICES
###############################################################################

log_step "Step 10/10: Restarting services..."

# Restart queue workers (Supervisor)
if command -v supervisorctl &> /dev/null; then
    supervisorctl restart amtar-worker:* || true
    log_info "Queue workers restarted"
else
    log_warn "Supervisor not found, skipping queue worker restart"
fi

# Restart PHP-FPM
if command -v systemctl &> /dev/null; then
    systemctl restart php8.2-fpm || systemctl restart php-fpm || true
    log_info "PHP-FPM restarted"
fi

# Clear OPcache (if available)
if [ -f "/usr/bin/cachetool" ]; then
    cachetool opcache:reset --fcgi || true
    log_info "OPcache cleared"
fi

###############################################################################
# FINAL: BRING APPLICATION BACK ONLINE
###############################################################################

log_step "Bringing application online..."

php artisan up

log_info "Application is now online"

###############################################################################
# POST-DEPLOYMENT VERIFICATION
###############################################################################

echo ""
log_step "Running post-deployment checks..."

# Check if application is responding
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost || echo "000")

if [ "$HTTP_CODE" = "200" ]; then
    log_info "✓ Application is responding (HTTP $HTTP_CODE)"
else
    log_error "✗ Application may not be responding (HTTP $HTTP_CODE)"
fi

# Check database connection
if php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>&1 | grep -q "OK"; then
    log_info "✓ Database connection successful"
else
    log_error "✗ Database connection failed"
fi

# Check queue workers
if command -v supervisorctl &> /dev/null; then
    WORKER_STATUS=$(supervisorctl status amtar-worker:* 2>&1 | grep -c "RUNNING" || true)
    if [ "$WORKER_STATUS" -gt 0 ]; then
        log_info "✓ Queue workers running ($WORKER_STATUS processes)"
    else
        log_error "✗ Queue workers not running"
    fi
fi

# Check storage permissions
if [ -w "$APP_DIR/storage/logs" ]; then
    log_info "✓ Storage directory writable"
else
    log_error "✗ Storage directory not writable"
fi

###############################################################################
# DEPLOYMENT SUMMARY
###############################################################################

echo ""
echo "=========================================="
log_info "Deployment completed!"
echo "=========================================="
echo ""
echo "Deployment date: $(date)"
echo "Deployed to: $APP_DIR"
echo ""

if [ -d ".git" ]; then
    echo "Git information:"
    echo "  Branch: $(git branch --show-current)"
    echo "  Commit: $(git log -1 --pretty=format:'%h - %s')"
    echo "  Author: $(git log -1 --pretty=format:'%an <%ae>')"
    echo ""
fi

echo "Next steps:"
echo "  1. Test login functionality"
echo "  2. Verify recent changes"
echo "  3. Check error logs: tail -f storage/logs/laravel.log"
echo "  4. Monitor queue workers: supervisorctl status"
echo "  5. Check application metrics"
echo ""

log_warn "Post-deployment checklist:"
log_warn "  [ ] Login works for all user types"
log_warn "  [ ] File uploads work"
log_warn "  [ ] Email notifications work"
log_warn "  [ ] Task assignment works"
log_warn "  [ ] Reports generate correctly"
log_warn "  [ ] No errors in logs"
echo ""

# Create deployment log
DEPLOY_LOG="$BACKUP_DIR/deployments.log"
echo "$(date '+%Y-%m-%d %H:%M:%S') - Deployment completed - Branch: $(git branch --show-current 2>/dev/null || echo 'N/A') - Commit: $(git log -1 --pretty=format:'%h' 2>/dev/null || echo 'N/A')" >> "$DEPLOY_LOG"

exit 0
