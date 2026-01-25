# AMTAR Engineering System - Deployment Checklist

## Pre-Deployment Preparation

### 1. Environment Setup
- [ ] Server provisioned (2GB RAM minimum, 4GB recommended)
- [ ] PHP 8.2+ installed with required extensions
- [ ] MySQL 8.0+ or MariaDB 10.3+ installed
- [ ] Web server installed (Apache 2.4+ or Nginx 1.18+)
- [ ] Composer 2.5+ installed
- [ ] Node.js 18+ and npm installed
- [ ] Git installed
- [ ] SSL certificate obtained (Let's Encrypt or commercial)

### 2. System Accounts
- [ ] Database user created with appropriate privileges
- [ ] Web server user configured (www-data or nginx)
- [ ] SSH user created for deployment
- [ ] Sudo privileges configured if needed

### 3. DNS Configuration
- [ ] Domain name registered
- [ ] DNS A record pointing to server IP
- [ ] DNS propagation verified (24-48 hours)
- [ ] SSL certificate installed and verified

---

## Code Deployment

### 4. Repository Setup
- [ ] Code repository accessible from server
- [ ] SSH keys configured for Git access
- [ ] Repository cloned to web directory (`/var/www/html/amtar`)
- [ ] Correct branch checked out (typically `main` or `production`)

### 5. Dependencies Installation
- [ ] `composer install --optimize-autoloader --no-dev` executed
- [ ] `npm install` executed
- [ ] `npm run build` completed successfully
- [ ] Vendor directory present and populated
- [ ] Public assets compiled (`public/build/` exists)

### 6. File Permissions
- [ ] Application directory owned by web server user
- [ ] Storage directory writable (775 permissions)
- [ ] Bootstrap/cache directory writable (775 permissions)
- [ ] .env file secured (600 permissions)
- [ ] Logs directory writable

### 7. Environment Configuration
- [ ] `.env` file created (copied from `.env.example`)
- [ ] `APP_KEY` generated (`php artisan key:generate`)
- [ ] Environment variables configured:
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `APP_URL` set to production domain
  - [ ] Database credentials entered
  - [ ] Mail settings configured
  - [ ] Cache and session drivers set
  - [ ] Company information updated

---

## Database Setup

### 8. Database Configuration
- [ ] Database created with UTF8MB4 charset
- [ ] Database user created and granted privileges
- [ ] Connection tested from application server
- [ ] Database credentials added to `.env`

### 9. Schema & Data
- [ ] Migrations executed (`php artisan migrate`)
- [ ] No migration errors reported
- [ ] Production seeder run (`php artisan db:seed --class=ProductionSeeder`)
- [ ] Essential data verified:
  - [ ] User roles created (Administrator, Project Manager, Engineer)
  - [ ] Document types seeded
  - [ ] Service categories seeded
  - [ ] Email templates populated
  - [ ] System settings configured

### 10. Initial Admin User
- [ ] Admin user created via tinker or seeder
- [ ] Admin credentials securely stored
- [ ] Admin login tested successfully
- [ ] Password meets security requirements

---

## Third-Party Integrations

### 11. Email Service
- [ ] SMTP provider configured (Gmail, Mailgun, SendGrid, etc.)
- [ ] Credentials added to `.env`:
  - [ ] `MAIL_HOST`
  - [ ] `MAIL_PORT`
  - [ ] `MAIL_USERNAME`
  - [ ] `MAIL_PASSWORD`
  - [ ] `MAIL_ENCRYPTION`
  - [ ] `MAIL_FROM_ADDRESS`
  - [ ] `MAIL_FROM_NAME`
- [ ] Test email sent successfully
- [ ] Email delivery verified in inbox (not spam)

### 12. Twilio (SMS & WhatsApp)
- [ ] Twilio account created
- [ ] Phone number purchased with SMS capability
- [ ] WhatsApp enabled on Twilio number
- [ ] Credentials added to `.env`:
  - [ ] `TWILIO_ACCOUNT_SID`
  - [ ] `TWILIO_AUTH_TOKEN`
  - [ ] `TWILIO_PHONE_NUMBER`
  - [ ] `TWILIO_WHATSAPP_NUMBER`
- [ ] Twilio SDK installed (`composer require twilio/sdk`)
- [ ] SMS integration tested
- [ ] WhatsApp integration tested

### 13. Additional Packages
- [ ] Excel export package installed (`composer require maatwebsite/excel`)
- [ ] Image processing installed (`composer require intervention/image`)
- [ ] All composer dependencies resolved without conflicts

---

## Web Server Configuration

### 14. Apache Configuration (if using Apache)
- [ ] Virtual host created for domain
- [ ] Document root set to `public/` directory
- [ ] `.htaccess` file present in public directory
- [ ] `mod_rewrite` enabled
- [ ] Site enabled (`a2ensite`)
- [ ] Apache restarted
- [ ] Site accessible via browser

### 15. Nginx Configuration (if using Nginx)
- [ ] Server block created for domain
- [ ] Root directive points to `public/` directory
- [ ] PHP-FPM configured and running
- [ ] try_files directive properly set
- [ ] Site symlinked to sites-enabled
- [ ] Nginx configuration tested (`nginx -t`)
- [ ] Nginx reloaded
- [ ] Site accessible via browser

### 16. SSL/TLS Configuration
- [ ] SSL certificate installed
- [ ] HTTPS enabled and working
- [ ] HTTP to HTTPS redirect configured
- [ ] Mixed content warnings resolved
- [ ] SSL Labs test passed (Grade A)
- [ ] Certificate auto-renewal configured

### 17. PHP-FPM Configuration
- [ ] PHP-FPM installed and running
- [ ] Pool configuration optimized:
  - [ ] `pm.max_children` set appropriately
  - [ ] `pm.start_servers` configured
  - [ ] `pm.min_spare_servers` set
  - [ ] `pm.max_spare_servers` set
- [ ] Upload limits configured:
  - [ ] `upload_max_filesize = 10M`
  - [ ] `post_max_size = 10M`
  - [ ] `max_execution_time = 300`
- [ ] Memory limit set (`memory_limit = 256M`)
- [ ] PHP-FPM restarted after changes

---

## Application Optimization

### 18. Laravel Optimization
- [ ] Configuration cached (`php artisan config:cache`)
- [ ] Routes cached (`php artisan route:cache`)
- [ ] Views cached (`php artisan view:cache`)
- [ ] Events cached (`php artisan event:cache`)
- [ ] Autoloader optimized (already done with `--optimize-autoloader`)
- [ ] No `.env` changes after caching (clears cache if changed)

### 19. Queue Workers
- [ ] Queue driver configured (`QUEUE_CONNECTION=database` in `.env`)
- [ ] Supervisor or Systemd service created
- [ ] Worker process started and running
- [ ] Multiple workers configured for redundancy
- [ ] Worker logs configured and accessible
- [ ] Failed job handling configured
- [ ] Queue monitoring set up

**Supervisor Configuration Verified:**
```ini
[program:amtar-worker]
command=php /var/www/html/amtar/artisan queue:work database
numprocs=2
autostart=true
autorestart=true
user=www-data
```

### 20. Task Scheduler
- [ ] Cron job added for Laravel scheduler
- [ ] Cron entry: `* * * * * cd /var/www/html/amtar && php artisan schedule:run >> /dev/null 2>&1`
- [ ] Cron running under correct user
- [ ] Scheduled tasks verified in logs
- [ ] Timezone configured correctly (`APP_TIMEZONE` in `.env`)

---

## Security Configuration

### 21. Application Security
- [ ] `APP_DEBUG=false` in production
- [ ] Error reporting disabled for users
- [ ] Detailed errors logged to files
- [ ] `.env` file has 600 permissions
- [ ] `.env` file excluded from git
- [ ] CSRF protection enabled (default)
- [ ] XSS protection enabled
- [ ] SQL injection prevention verified (using Eloquent/PDO)

### 22. Server Security
- [ ] Firewall configured (UFW or iptables)
- [ ] Only necessary ports open (22, 80, 443, 3306 if remote DB)
- [ ] SSH key authentication configured
- [ ] Root login disabled
- [ ] Fail2ban installed and configured
- [ ] Security updates enabled
- [ ] Server timezone set correctly

### 23. Database Security
- [ ] Database user has limited privileges (not root)
- [ ] Remote database access restricted to application server
- [ ] Database backups encrypted
- [ ] MySQL bind address configured (127.0.0.1 if local)

### 24. File Security
- [ ] Directory listing disabled
- [ ] Hidden files (.git, .env) not accessible via web
- [ ] Upload directory outside public directory or protected
- [ ] File permissions properly restricted
- [ ] Sensitive files not world-readable

---

## Monitoring & Logging

### 25. Application Logging
- [ ] Log channel configured (`LOG_CHANNEL=daily` in `.env`)
- [ ] Log level set to `error` or `warning` for production
- [ ] Logs rotating properly (daily rotation configured)
- [ ] Log retention policy set (keep 14 days)
- [ ] Logs accessible for debugging
- [ ] Error monitoring tool configured (optional: Sentry, Bugsnag)

### 26. Server Monitoring
- [ ] Disk space monitoring configured
- [ ] CPU and memory monitoring set up
- [ ] Uptime monitoring configured (optional: Uptime Robot, Pingdom)
- [ ] Alert thresholds defined
- [ ] Alert notifications configured (email, SMS)

### 27. Database Monitoring
- [ ] Database connection monitoring
- [ ] Slow query logging enabled
- [ ] Database size monitored
- [ ] Table optimization scheduled

---

## Backup Strategy

### 28. Database Backups
- [ ] Backup script created
- [ ] Backup schedule configured (daily recommended)
- [ ] Backup retention policy set (7-30 days)
- [ ] Backups stored off-server (S3, Dropbox, remote server)
- [ ] Backup restoration tested successfully
- [ ] Backup encryption configured (if sensitive data)

### 29. File Backups
- [ ] Storage directory backup configured
- [ ] Backup schedule set (daily or weekly)
- [ ] Backup includes uploaded files
- [ ] Backups stored securely
- [ ] Restoration procedure documented and tested

### 30. Configuration Backups
- [ ] `.env` file backed up securely (encrypted)
- [ ] Web server configuration backed up
- [ ] Database credentials documented securely
- [ ] SSL certificates backed up

---

## Testing & Verification

### 31. Functionality Testing
- [ ] Login page accessible
- [ ] Admin user can log in
- [ ] Dashboard loads correctly
- [ ] Create test client successfully
- [ ] Create test project successfully
- [ ] Create test task successfully
- [ ] Assign task to user successfully
- [ ] File upload works
- [ ] File download works
- [ ] Email notifications sent successfully
- [ ] SMS notifications sent (if configured)
- [ ] WhatsApp notifications sent (if configured)

### 32. Report Generation Testing
- [ ] Project summary report generates (PDF)
- [ ] Project summary report generates (Excel)
- [ ] Task status report generates
- [ ] Financial report generates
- [ ] Reports contain accurate data
- [ ] Report downloads work

### 33. User Workflow Testing
- [ ] Administrator workflow tested end-to-end
- [ ] Project Manager workflow tested
- [ ] Engineer workflow tested
- [ ] Task assignment workflow verified
- [ ] Task review workflow verified
- [ ] File upload and preview tested
- [ ] Notifications received correctly

### 34. Performance Testing
- [ ] Page load times acceptable (<3 seconds)
- [ ] Database queries optimized (no N+1 issues)
- [ ] Large file uploads tested (up to 10MB)
- [ ] Concurrent user testing completed
- [ ] Memory usage under control
- [ ] No memory leaks detected

### 35. Browser Compatibility
- [ ] Chrome/Edge tested
- [ ] Firefox tested
- [ ] Safari tested
- [ ] Mobile browsers tested (iOS Safari, Chrome Mobile)
- [ ] Responsive design verified on tablets
- [ ] All features work across browsers

---

## Documentation

### 36. User Documentation
- [ ] User guide accessible to users
- [ ] Administrator guide available
- [ ] Training materials prepared
- [ ] FAQ document available
- [ ] Video tutorials created (optional)

### 37. Technical Documentation
- [ ] Installation guide complete
- [ ] Deployment guide available
- [ ] API documentation (if applicable)
- [ ] Database schema documented
- [ ] Backup/restore procedures documented
- [ ] Troubleshooting guide available

### 38. Handover Documentation
- [ ] Server access credentials documented
- [ ] Database credentials documented
- [ ] Third-party service credentials documented
- [ ] Emergency contact information provided
- [ ] Maintenance schedule outlined
- [ ] Support escalation process defined

---

## Training & Handover

### 39. User Training
- [ ] Administrator training completed
- [ ] Project Manager training completed
- [ ] Engineer training completed
- [ ] Training materials distributed
- [ ] Q&A session conducted

### 40. Technical Handover
- [ ] Server access transferred to client IT team
- [ ] Database credentials shared securely
- [ ] Third-party integration credentials transferred
- [ ] Maintenance procedures explained
- [ ] Backup restoration demonstrated
- [ ] Emergency procedures reviewed

---

## Post-Deployment

### 41. Monitoring Period
- [ ] Monitor system for 24 hours post-deployment
- [ ] Check error logs frequently
- [ ] Monitor server resources (CPU, memory, disk)
- [ ] Watch for email delivery issues
- [ ] Track queue worker performance
- [ ] Verify scheduled tasks running

### 42. Performance Tuning
- [ ] Optimize slow queries identified in logs
- [ ] Adjust PHP-FPM pool settings if needed
- [ ] Configure opcode caching (OPcache)
- [ ] Enable Laravel route caching
- [ ] Set up Redis for caching (optional)
- [ ] Configure CDN for static assets (optional)

### 43. User Feedback
- [ ] Collect feedback from initial users
- [ ] Address critical issues immediately
- [ ] Document feature requests
- [ ] Plan updates and improvements

### 44. Compliance & Legal
- [ ] Privacy policy in place
- [ ] Terms of service displayed
- [ ] GDPR compliance verified (if applicable)
- [ ] Data retention policy configured
- [ ] User consent mechanisms in place

---

## Final Sign-Off

### 45. Deployment Approval
- [ ] Functionality testing passed
- [ ] Performance testing passed
- [ ] Security audit completed
- [ ] Backup strategy verified
- [ ] User training completed
- [ ] Documentation complete
- [ ] Client approval obtained

### 46. Go-Live
- [ ] DNS switched to production server
- [ ] Production URL accessible
- [ ] All users can log in
- [ ] No critical errors in logs
- [ ] Monitoring systems active
- [ ] Support team on standby

---

## Post-Go-Live Checklist (First Week)

### Day 1
- [ ] Monitor error logs every hour
- [ ] Check queue workers running
- [ ] Verify all scheduled tasks executed
- [ ] Test email delivery
- [ ] Monitor server resources

### Day 2-3
- [ ] Review user feedback
- [ ] Address any reported issues
- [ ] Monitor database performance
- [ ] Check backup completion
- [ ] Verify integrations working

### Day 4-7
- [ ] Conduct user satisfaction survey
- [ ] Optimize based on usage patterns
- [ ] Plan feature enhancements
- [ ] Document lessons learned
- [ ] Schedule regular maintenance

---

## Rollback Plan

In case of critical issues:

1. **Immediate Actions**
   - [ ] Put site in maintenance mode: `php artisan down`
   - [ ] Notify users of the issue
   - [ ] Document the problem

2. **Assess Situation**
   - [ ] Check error logs
   - [ ] Identify root cause
   - [ ] Determine if fixable in <30 minutes

3. **Decision**
   - **If Fixable**: Fix issue, test, bring site back up
   - **If Not Fixable**: Execute rollback

4. **Rollback Steps**
   - [ ] Switch to previous code version (git)
   - [ ] Restore previous database backup (if schema changed)
   - [ ] Clear all caches
   - [ ] Test rollback thoroughly
   - [ ] Bring site back up: `php artisan up`

5. **Post-Rollback**
   - [ ] Notify users system is restored
   - [ ] Conduct post-mortem
   - [ ] Fix issues in staging
   - [ ] Plan redeployment

---

## Maintenance Schedule

### Daily
- [ ] Review error logs
- [ ] Monitor queue workers
- [ ] Check disk space

### Weekly
- [ ] Review system performance
- [ ] Check backup completion
- [ ] Security updates review

### Monthly
- [ ] User data cleanup (soft-deleted records)
- [ ] Log file archival
- [ ] Performance optimization review
- [ ] User access audit

### Quarterly
- [ ] Security audit
- [ ] Dependency updates (`composer update`)
- [ ] Database optimization
- [ ] Disaster recovery drill

---

## Emergency Contacts

| Role | Name | Phone | Email |
|------|------|-------|-------|
| System Administrator | | | |
| Database Administrator | | | |
| Lead Developer | | | |
| Project Manager | | | |
| Client Contact | | | |

---

## Sign-Off

**Deployed By**: ________________________  **Date**: ____________

**Verified By**: ________________________  **Date**: ____________

**Approved By**: ________________________  **Date**: ____________

---

**Deployment Version**: 1.0
**Deployment Date**: ____________
**Server**: ____________
**Domain**: ____________

**Notes**:
_____________________________________________________________________________
_____________________________________________________________________________
_____________________________________________________________________________
