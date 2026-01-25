# AMTAR Engineering System - Installation Guide

## Table of Contents
1. [System Requirements](#system-requirements)
2. [Installation Steps](#installation-steps)
3. [Database Setup](#database-setup)
4. [Environment Configuration](#environment-configuration)
5. [Third-Party Integrations](#third-party-integrations)
6. [Initial Data Setup](#initial-data-setup)
7. [Production Deployment](#production-deployment)
8. [Troubleshooting](#troubleshooting)

---

## System Requirements

### Server Requirements
- **PHP**: >= 8.2
- **Database**: MySQL 8.0+ or MariaDB 10.3+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Composer**: 2.5+
- **Node.js**: 18+ (for asset compilation)
- **Memory**: Minimum 512MB RAM (2GB recommended)
- **Disk Space**: 1GB minimum

### PHP Extensions Required
```
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO_MySQL
- Tokenizer
- XML
- GD or Imagick (for image processing)
- cURL (for third-party integrations)
- ZipArchive (for document generation)
```

### Optional Extensions
```
- Redis (for caching and queues)
- Memcached (for caching)
```

---

## Installation Steps

### 1. Clone the Repository
```bash
cd /var/www/html
git clone <repository-url> amtar
cd amtar
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install

# Build assets for production
npm run build
```

### 3. Set Permissions
```bash
# Set correct ownership (replace www-data with your web server user)
sudo chown -R www-data:www-data /var/www/html/amtar

# Set storage and cache permissions
chmod -R 775 storage bootstrap/cache
```

---

## Database Setup

### 1. Create Database
```sql
CREATE DATABASE amtar_office CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'amtar_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON amtar_office.* TO 'amtar_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Configure Database Connection
Copy `.env.example` to `.env` and update:
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=amtar_office
DB_USERNAME=amtar_user
DB_PASSWORD=secure_password_here
```

### 3. Run Migrations
```bash
php artisan migrate
```

---

## Environment Configuration

### Production Settings
Edit `.env` for production:

```env
APP_NAME="AMTAR Engineering System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error
```

### Company Information
Update company details in `.env`:

```env
COMPANY_NAME="AMTAR Engineering & Design Consultancy"
COMPANY_ADDRESS="Muscat, Sultanate of Oman"
COMPANY_PHONE="+968 XXXXXXXX"
COMPANY_EMAIL="info@amtar.om"
COMPANY_WEBSITE="www.amtar.om"
COMPANY_CR_NUMBER="CR-12345678"
COMPANY_TAX_NUMBER="TAX-87654321"
```

### Email Configuration

#### Option 1: SMTP (Recommended)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@amtar.om"
MAIL_FROM_NAME="AMTAR System"
```

#### Option 2: Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-mailgun-secret
MAILGUN_ENDPOINT=api.mailgun.net
```

---

## Third-Party Integrations

### 1. Twilio (SMS & WhatsApp)

#### Setup Steps:
1. Create account at https://www.twilio.com
2. Get Account SID and Auth Token from Console Dashboard
3. Purchase a phone number with SMS capabilities
4. Enable WhatsApp on your Twilio number

#### Configuration:
```env
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_PHONE_NUMBER=+1234567890
TWILIO_WHATSAPP_NUMBER=+1234567890
```

#### Install Twilio SDK:
```bash
composer require twilio/sdk
```

#### Test Integration:
```bash
php artisan tinker
>>> app(\App\Services\Integrations\SmsService::class)->test()
```

### 2. WhatsApp Business API (Alternative)

If using WhatsApp Business API directly:
```env
WHATSAPP_API_URL=https://graph.facebook.com/v18.0
WHATSAPP_API_TOKEN=your_permanent_token
WHATSAPP_BUSINESS_NUMBER=+1234567890
```

### 3. Email Templates

Email templates are stored in database. Configure via Admin Panel:
- Navigate to: Settings → Email Templates
- Customize templates for: Task Assignment, Review Notifications, etc.

---

## Initial Data Setup

### 1. Seed Essential Data
```bash
# Seed roles, permissions, and system settings
php artisan db:seed --class=ProductionSeeder
```

This seeds:
- User roles (Administrator, Project Manager, Engineer)
- Document types
- Service categories and packages
- Email templates
- System settings

### 2. Create Admin User
```bash
php artisan tinker
```

```php
$admin = \App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@amtar.om',
    'password' => bcrypt('SecurePassword123!'),
    'phone' => '+968XXXXXXXX',
    'is_active' => true,
    'email_verified_at' => now(),
]);

$adminRole = \App\Models\Role::where('slug', 'administrator')->first();
$admin->roles()->attach($adminRole);
```

### 3. Seed Demo Data (Optional)
For testing/demo purposes:
```bash
php artisan db:seed --class=DatabaseSeeder
```

This creates:
- 10 clients
- 20 projects
- 50+ tasks with assignments
- Sample contracts
- File attachments

---

## Production Deployment

### 1. Optimize Application
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize composer autoloader
composer install --optimize-autoloader --no-dev
```

### 2. Set Up Queue Workers

#### Using Supervisor (Recommended)
Create `/etc/supervisor/conf.d/amtar-worker.conf`:

```ini
[program:amtar-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/amtar/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/amtar/storage/logs/worker.log
stopwaitsecs=3600
```

Start workers:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start amtar-worker:*
```

#### Using Systemd
Create `/etc/systemd/system/amtar-worker.service`:

```ini
[Unit]
Description=AMTAR Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/html/amtar
ExecStart=/usr/bin/php artisan queue:work database --sleep=3 --tries=3
Restart=always

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable amtar-worker
sudo systemctl start amtar-worker
```

### 3. Set Up Scheduled Tasks

Add to crontab:
```bash
crontab -e
```

Add this line:
```
* * * * * cd /var/www/html/amtar && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Configure Web Server

#### Apache Virtual Host
Create `/etc/apache2/sites-available/amtar.conf`:

```apache
<VirtualHost *:80>
    ServerName amtar.yourdomain.com
    ServerAdmin admin@amtar.om
    DocumentRoot /var/www/html/amtar/public

    <Directory /var/www/html/amtar/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/amtar-error.log
    CustomLog ${APACHE_LOG_DIR}/amtar-access.log combined
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite amtar
sudo a2enmod rewrite
sudo systemctl reload apache2
```

#### Nginx Server Block
Create `/etc/nginx/sites-available/amtar`:

```nginx
server {
    listen 80;
    server_name amtar.yourdomain.com;
    root /var/www/html/amtar/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/amtar /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 5. SSL Certificate (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-apache  # For Apache
# OR
sudo apt install certbot python3-certbot-nginx   # For Nginx

sudo certbot --apache -d amtar.yourdomain.com
# OR
sudo certbot --nginx -d amtar.yourdomain.com
```

### 6. Security Hardening

#### Disable Directory Indexing
Already handled in web server configs above.

#### Secure .env File
```bash
chmod 600 .env
```

#### Configure CSP Headers
Add to `.env`:
```env
# Content Security Policy
APP_CSP_ENABLED=true
```

#### Set Up Firewall
```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp
sudo ufw enable
```

---

## Troubleshooting

### Issue: White Screen / 500 Error
**Solution:**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Issue: Permission Denied Errors
**Solution:**
```bash
sudo chown -R www-data:www-data /var/www/html/amtar
sudo chmod -R 775 storage bootstrap/cache
```

### Issue: Database Connection Failed
**Solution:**
1. Verify MySQL is running: `sudo systemctl status mysql`
2. Test credentials: `mysql -u amtar_user -p`
3. Check `.env` database settings
4. Verify firewall allows MySQL port 3306

### Issue: Email Not Sending
**Solution:**
1. Test SMTP connection:
```bash
php artisan tinker
>>> Mail::raw('Test', fn($msg) => $msg->to('test@example.com')->subject('Test'));
```
2. Check `storage/logs/laravel.log` for errors
3. Verify SMTP credentials in `.env`
4. For Gmail, use App Passwords, not account password

### Issue: Queue Jobs Not Processing
**Solution:**
```bash
# Check supervisor/systemd status
sudo supervisorctl status amtar-worker:*
# OR
sudo systemctl status amtar-worker

# Restart workers
sudo supervisorctl restart amtar-worker:*
# OR
sudo systemctl restart amtar-worker
```

### Issue: File Upload Fails
**Solution:**
1. Check PHP upload limits in `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
```
2. Restart PHP-FPM: `sudo systemctl restart php8.2-fpm`
3. Check storage permissions: `chmod -R 775 storage/app/public`

---

## Post-Installation Checklist

- [ ] Application accessible via browser
- [ ] Admin user can login
- [ ] Database migrations completed
- [ ] Email sending works (test password reset)
- [ ] File uploads work
- [ ] Queue workers running
- [ ] Cron jobs scheduled
- [ ] SSL certificate installed
- [ ] Backups configured
- [ ] Monitoring set up
- [ ] Third-party integrations tested (SMS, WhatsApp)
- [ ] All `.env` values updated with production credentials
- [ ] Application optimized (config/route/view cached)

---

## Support & Maintenance

### Regular Maintenance Tasks

**Daily:**
- Monitor application logs: `tail -f storage/logs/laravel.log`
- Check queue workers: `sudo supervisorctl status`

**Weekly:**
- Database backups
- Review error logs
- Check disk space: `df -h`

**Monthly:**
- Update dependencies: `composer update`
- Security patches: `php artisan about` (check versions)
- Clean old logs: `php artisan log:clear`

### Backup Strategy

#### Database Backup Script
Create `/usr/local/bin/backup-amtar.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/amtar"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="amtar_office"
DB_USER="amtar_user"
DB_PASS="your_password"

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/html/amtar/storage/app

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

Schedule daily backups:
```bash
sudo chmod +x /usr/local/bin/backup-amtar.sh
crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-amtar.sh
```

---

## Additional Resources

- **Laravel Documentation**: https://laravel.com/docs
- **System Configuration**: `config/project.php`
- **API Documentation**: `/docs/api` (if enabled)
- **Developer Guide**: See `DEVELOPMENT.md`

For technical support, contact: support@amtar.om
