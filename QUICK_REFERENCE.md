# AMTAR Engineering System - Quick Reference Guide

## 🚀 Quick Start Commands

### Development
```bash
# Start development server
php artisan serve

# Watch for frontend changes
npm run dev

# Run all tests
php artisan test

# Clear all caches
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear
```

### Database
```bash
# Run migrations
php artisan migrate

# Fresh database with seed data
php artisan migrate:fresh --seed

# Seed only production data
php artisan db:seed --class=ProductionSeeder

# Rollback last migration
php artisan migrate:rollback

# Check migration status
php artisan migrate:status
```

### Backup & Restore
```bash
# Create backup
./scripts/backup.sh

# Restore from backup
./scripts/restore.sh 20260125_143000

# List available backups
ls -lh /var/backups/amtar/database_*.sql.gz
```

### Deployment
```bash
# Full deployment
./scripts/deploy.sh

# Deployment without backup
./scripts/deploy.sh --skip-backup

# Deployment without prompts
./scripts/deploy.sh --force
```

---

## 📁 Important File Locations

### Configuration
- Environment: `.env`
- Project config: `config/project.php`
- Database config: `config/database.php`
- Mail config: `config/mail.php`

### Logs
- Application logs: `storage/logs/laravel.log`
- Queue logs: `storage/logs/worker.log`
- Web server logs: `/var/log/nginx/` or `/var/log/apache2/`

### Uploads
- User uploads: `storage/app/public/`
- Contracts: `storage/app/contracts/`
- Reports: `storage/app/reports/`
- Temp files: `storage/app/temp/`

### Templates
- Email templates: Database (`email_templates` table)
- Contract templates: `storage/app/contracts/templates/`
- Blade views: `resources/views/`

---

## 👤 User Roles & Permissions

### Administrator
**Can:**
- Everything (full system access)
- Create/edit/delete users
- Manage roles and permissions
- Configure system settings
- View all projects and tasks
- Access all reports

**Routes:** All `/admin/*` routes

### Project Manager
**Can:**
- Create and manage projects
- Create and assign tasks
- Review and approve tasks
- Manage clients
- View team workload
- Generate reports

**Cannot:**
- Manage users and roles
- Change system settings
- Delete other users

**Routes:** `/admin/projects/*`, `/admin/tasks/*`, `/admin/clients/*`

### Engineer
**Can:**
- View assigned tasks
- Update task progress
- Upload files to tasks
- Submit tasks for review
- View project details

**Cannot:**
- Create projects or tasks
- Assign tasks
- Manage clients
- Access reports
- Manage users

**Routes:** `/admin/tasks` (filtered to assigned only)

---

## 🔑 Common Artisan Commands

### Application
```bash
# Put app in maintenance mode
php artisan down --message="Maintenance in progress"

# Bring app back online
php artisan up

# Check application status
php artisan about

# List all routes
php artisan route:list

# List all commands
php artisan list
```

### Cache Management
```bash
# Clear specific cache
php artisan cache:forget key_name

# Clear config cache
php artisan config:clear

# Rebuild config cache
php artisan config:cache

# Clear route cache
php artisan route:clear

# Rebuild route cache
php artisan route:cache

# Clear view cache
php artisan view:clear

# Rebuild view cache
php artisan view:cache

# Clear all caches at once
php artisan optimize:clear
```

### Queue Management
```bash
# Start queue worker
php artisan queue:work

# Process one job
php artisan queue:work --once

# List failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry job_id

# Retry all failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### Development
```bash
# Create new model
php artisan make:model ModelName -m

# Create new controller
php artisan make:controller ControllerName

# Create new migration
php artisan make:migration create_table_name

# Create new seeder
php artisan make:seeder SeederName

# Create new factory
php artisan make:factory FactoryName

# Create new test
php artisan make:test TestName

# Interactive shell (Tinker)
php artisan tinker
```

---

## 💾 Database Quick Reference

### Connection Test
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Common Queries (via Tinker)
```php
// Count users
User::count()

// Get admin users
User::whereHas('roles', fn($q) => $q->where('slug', 'administrator'))->get()

// Count active projects
Project::where('status', 'active')->count()

// Get overdue tasks
Task::where('due_date', '<', now())->whereNotIn('status', ['completed'])->count()

// Find user by email
User::where('email', 'admin@amtar.om')->first()
```

### Manual Database Access
```bash
# MySQL command line
mysql -u amtar_user -p amtar_office

# Common SQL queries
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM projects WHERE status = 'active';
SELECT COUNT(*) FROM tasks WHERE status = 'completed';
```

---

## 🔧 Troubleshooting Commands

### Permission Issues
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Fix .env permissions
sudo chmod 600 .env
```

### Clear Everything
```bash
# Nuclear option - clear all caches and rebuild
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Check Services
```bash
# Check queue workers
sudo supervisorctl status amtar-worker:*

# Restart queue workers
sudo supervisorctl restart amtar-worker:*

# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Check web server
sudo systemctl status nginx
sudo systemctl restart nginx
```

### View Logs
```bash
# Application logs (last 50 lines)
tail -n 50 storage/logs/laravel.log

# Follow logs in real-time
tail -f storage/logs/laravel.log

# Search for errors
grep "ERROR" storage/logs/laravel.log

# View today's errors
grep "$(date +%Y-%m-%d)" storage/logs/laravel.log | grep "ERROR"
```

---

## 📧 Email Configuration Examples

### Gmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
```

### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.yourdomain.com
MAILGUN_SECRET=key-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAILGUN_ENDPOINT=api.mailgun.net
```

### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_ENCRYPTION=tls
```

---

## 📞 Twilio Configuration

```env
# Twilio Account (get from console.twilio.com)
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

# Phone Numbers (purchase from Twilio)
TWILIO_PHONE_NUMBER=+1234567890
TWILIO_WHATSAPP_NUMBER=+1234567890
```

**Test Twilio:**
```bash
php artisan tinker
>>> app(\App\Services\Integrations\SmsService::class)->test()
>>> app(\App\Services\Integrations\WhatsAppService::class)->test()
```

---

## 🔍 Useful Database Queries

### User Management
```sql
-- Find all administrators
SELECT u.name, u.email FROM users u
JOIN role_user ru ON u.id = ru.user_id
JOIN roles r ON ru.role_id = r.id
WHERE r.slug = 'administrator';

-- Count users by role
SELECT r.name, COUNT(*) as count FROM roles r
JOIN role_user ru ON r.id = ru.role_id
GROUP BY r.id, r.name;
```

### Project Statistics
```sql
-- Projects by status
SELECT status, COUNT(*) as count FROM projects
GROUP BY status;

-- Average project budget
SELECT AVG(budget) as avg_budget FROM projects;

-- Projects with overdue tasks
SELECT DISTINCT p.name, COUNT(t.id) as overdue_tasks
FROM projects p
JOIN tasks t ON p.id = t.project_id
WHERE t.due_date < NOW() AND t.status != 'completed'
GROUP BY p.id, p.name;
```

### Task Statistics
```sql
-- Tasks by status
SELECT status, COUNT(*) as count FROM tasks
GROUP BY status;

-- Overdue tasks
SELECT title, due_date, assigned_to, status
FROM tasks
WHERE due_date < NOW() AND status != 'completed';

-- Engineer workload
SELECT u.name, COUNT(t.id) as task_count
FROM users u
JOIN tasks t ON u.id = t.assigned_to
WHERE t.status IN ('pending', 'in_progress')
GROUP BY u.id, u.name
ORDER BY task_count DESC;
```

---

## 🚨 Emergency Procedures

### Application Down
```bash
# 1. Put in maintenance mode
php artisan down

# 2. Check logs
tail -f storage/logs/laravel.log

# 3. Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# 4. Clear caches
php artisan optimize:clear

# 5. Restart services
sudo systemctl restart php8.2-fpm nginx

# 6. Bring back up
php artisan up
```

### Database Issues
```bash
# 1. Check connection
mysql -u amtar_user -p

# 2. Check MySQL status
sudo systemctl status mysql

# 3. Restart MySQL
sudo systemctl restart mysql

# 4. Check slow queries
mysqladmin -u root -p processlist

# 5. Optimize tables
mysqlcheck -u root -p --optimize amtar_office
```

### High CPU/Memory
```bash
# 1. Check processes
top -c

# 2. Check queue workers
sudo supervisorctl status

# 3. Restart queue workers
sudo supervisorctl restart amtar-worker:*

# 4. Clear OPcache
sudo systemctl restart php8.2-fpm

# 5. Check disk space
df -h
```

### Rollback Deployment
```bash
# 1. Put in maintenance mode
php artisan down

# 2. Restore from backup
./scripts/restore.sh BACKUP_DATE

# 3. Or git rollback
git checkout previous_commit_hash

# 4. Rebuild
composer install --no-dev
npm run build
php artisan migrate

# 5. Clear caches
php artisan optimize:clear
php artisan config:cache

# 6. Bring back up
php artisan up
```

---

## 📊 Performance Tuning

### Enable OPcache
Edit `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### Use Redis for Cache
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_CLIENT=phpredis
```

### Database Optimization
```bash
# Analyze tables
mysqlcheck -u root -p --analyze amtar_office

# Optimize tables
mysqlcheck -u root -p --optimize amtar_office

# Check slow query log
tail -f /var/log/mysql/slow-query.log
```

---

## 🔐 Security Checklist

### Production Settings
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

### File Permissions
```bash
# Secure .env
chmod 600 .env

# Web directory
chmod 755 public

# Storage directory
chmod -R 775 storage
```

### Firewall Rules
```bash
# Allow only HTTP, HTTPS, SSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp
sudo ufw enable
```

---

## 📝 Daily Maintenance Tasks

### Morning Checks
```bash
# Check error logs
tail -n 100 storage/logs/laravel.log | grep ERROR

# Check queue workers
sudo supervisorctl status

# Check disk space
df -h

# Check database connections
mysqladmin -u root -p processlist
```

### Weekly Tasks
```bash
# Run backups
./scripts/backup.sh

# Review slow queries
grep "Slow query" /var/log/mysql/slow-query.log

# Clean old logs
find storage/logs -name "*.log" -mtime +30 -delete

# Update dependencies (in dev)
composer update
npm update
```

---

## 💡 Pro Tips

### Speed up Artisan commands
```bash
# Add to ~/.bashrc
alias art='php artisan'
alias tinker='php artisan tinker'
alias migrate='php artisan migrate'
```

### Quick database backup
```bash
# Add to ~/.bashrc
alias dbbackup='mysqldump -u amtar_user -p amtar_office | gzip > ~/backup_$(date +%Y%m%d).sql.gz'
```

### Monitor logs in real-time
```bash
# Multi-tail different logs
tail -f storage/logs/laravel.log /var/log/nginx/error.log
```

---

**For more detailed information, see:**
- [USER_GUIDE.md](USER_GUIDE.md) - Complete user manual
- [INSTALLATION.md](INSTALLATION.md) - Installation guide
- [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) - Deployment checklist
- [ARCHITECTURE.md](ARCHITECTURE.md) - System architecture

**Version**: 1.0 | **Last Updated**: January 2026
