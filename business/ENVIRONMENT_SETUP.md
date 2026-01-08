# AMTAR Office Management System - Environment Setup Guide

This document explains how to configure the `.env` file before testing the system.

---

## Step 1: Copy Environment File

```bash
cp .env.example .env
```

---

## Step 2: Generate Application Key

```bash
php artisan key:generate
```

---

## Step 3: Database Configuration

### Option A: SQLite (Recommended for Testing)

```env
DB_CONNECTION=sqlite
# Comment out or remove MySQL settings
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=amtar_office
# DB_USERNAME=root
# DB_PASSWORD=
```

Create the SQLite database file:
```bash
touch database/database.sqlite
```

### Option B: MySQL (Production-like Testing)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=amtar_office
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password
```

Create the database:
```sql
CREATE DATABASE amtar_office CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## Step 4: Required Environment Variables

### Application Settings

```env
APP_NAME="AMTAR Office Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

### Session & Cache (Use database for testing)

```env
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
```

### Company Information (Used in contracts/documents)

```env
COMPANY_NAME="AMTAR Engineering & Design Consultancy"
COMPANY_ADDRESS="Muscat, Sultanate of Oman"
COMPANY_PHONE="+968 12345678"
COMPANY_EMAIL="info@amtar.om"
COMPANY_WEBSITE="www.amtar.om"
COMPANY_CR_NUMBER="123456789"
COMPANY_TAX_NUMBER="OM123456"
```

---

## Step 5: Email Configuration (Optional for Testing)

### Option A: Log Driver (No actual emails sent)

```env
MAIL_MAILER=log
```

Emails will be written to `storage/logs/laravel.log`

### Option B: Mailtrap (View sent emails in browser)

1. Create free account at https://mailtrap.io
2. Get SMTP credentials from inbox settings

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS="info@amtar.om"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Step 6: Run Migrations and Seeders

```bash
# Run all migrations
php artisan migrate

# Seed with production-safe data (roles, services, settings)
php artisan db:seed --class=ProductionSeeder

# Create admin user
php artisan admin:create --email=admin@amtar.om --password=Admin123!
```

---

## Step 7: Storage Link

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` for file uploads.

---

## Step 8: Start the Application

```bash
php artisan serve
```

Access the application at: http://localhost:8000

---

## Complete .env Example for Testing

```env
APP_NAME="AMTAR Office Management"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

LOG_CHANNEL=stack
LOG_LEVEL=debug

# SQLite for easy testing
DB_CONNECTION=sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
CACHE_STORE=database

# Email - use log for testing
MAIL_MAILER=log
MAIL_FROM_ADDRESS="info@amtar.om"
MAIL_FROM_NAME="${APP_NAME}"

# Company Information
COMPANY_NAME="AMTAR Engineering & Design Consultancy"
COMPANY_ADDRESS="Muscat, Sultanate of Oman"
COMPANY_PHONE="+968 12345678"
COMPANY_EMAIL="info@amtar.om"
COMPANY_WEBSITE="www.amtar.om"
COMPANY_CR_NUMBER="123456789"
COMPANY_TAX_NUMBER="OM123456"

VITE_APP_NAME="${APP_NAME}"
```

---

## Troubleshooting

### Permission Errors

```bash
chmod -R 775 storage bootstrap/cache
```

### Cache Issues

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Database Migration Errors

```bash
php artisan migrate:fresh --seed --seeder=ProductionSeeder
```

---

## Quick Start Commands Summary

```bash
# One-time setup
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed --class=ProductionSeeder
php artisan admin:create --email=admin@amtar.om --password=Admin123!
php artisan storage:link

# Start server
php artisan serve

# Login at http://localhost:8000/admin/login
# Email: admin@amtar.om
# Password: Admin123!
```
