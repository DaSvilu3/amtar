# AMTAR Office Management - Quick Start Checklist

A condensed checklist for getting the system up and running quickly.

---

## Pre-Testing Setup (10 minutes)

### 1. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate
```

### 2. Edit .env File

Open `.env` and set these values:

```env
# Use SQLite for easy testing (no MySQL needed)
DB_CONNECTION=sqlite

# Set your company info (appears on contracts)
COMPANY_NAME="AMTAR Engineering & Design Consultancy"
COMPANY_ADDRESS="Muscat, Sultanate of Oman"
COMPANY_PHONE="+968 12345678"
COMPANY_EMAIL="info@amtar.om"

# Use log for emails (no SMTP needed for testing)
MAIL_MAILER=log
```

### 3. Database Setup

```bash
# Create SQLite database file
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed essential data (roles, services, settings)
php artisan db:seed --class=ProductionSeeder

# Create admin user
php artisan admin:create --email=admin@amtar.om --password=Admin123!

# Link storage
php artisan storage:link
```

### 4. Start Server

```bash
php artisan serve
```

---

## First Login

1. Open browser: http://localhost:8000/admin/login
2. Email: `admin@amtar.om`
3. Password: `Admin123!`

---

## Quick Verification Checklist

After logging in, verify each item works:

| # | Test | How to Verify | Pass? |
|---|------|---------------|-------|
| 1 | Dashboard loads | See stats and quick links | [Y] |
| 2 | Navigation works | Click each sidebar item | [Y] |
| 3 | Create user | System > Users > Add User | [Y] |
| 4 | Create client | Clients > Add Client | [Y] |
| 5 | Create project | Projects > New Project (wizard) | [Y] |
| 6 | Tasks generated | View project > Tasks tab | [Y] |
| 7 | Assign task | Task > Auto Assign | [ ] |
| 8 | Update task | Change status to In Progress | [ ] |
| 9 | View analytics | Analytics > Analytics | [ ] |
| 10 | Print contract | Contracts > View > Print | [ ] |

---

## Pre-seeded Employees

The following employees are created automatically by the seeder:

### Leadership (2)

| Name | Email | Role | Specialization |
|------|-------|------|----------------|
| Ayman Alhandhali | ayman@amtar.om | Administrator | CEO |
| Nimah Al Rashdi | nimah@amtar.om | Project Manager | Founder & Head of Architecture |

### Technical Team (3)

| Name | Email | Role | Specialization |
|------|-------|------|----------------|
| Technical Staff 1 | technical1@amtar.om | Engineer | Technical / CAD & 3D |
| Follow-up Staff | followup@amtar.om | Engineer | Follow-up & Coordination |
| Site Staff | site@amtar.om | Engineer | Site Supervision |

### Freelancers (2)

| Name | Email | Role | Specialization |
|------|-------|------|----------------|
| Freelance MEP Engineer | mep.freelance@amtar.om | Engineer | MEP Engineering |
| Freelance Landscape Designer | landscape.freelance@amtar.om | Engineer | Landscape Design |

**Default Password:** `Amtar2024!`

Each employee has skills assigned based on their specialization for auto-task assignment.

---

## Role Access Summary

| Feature | Admin | PM | Engineer |
|---------|-------|-----|----------|
| Dashboard | Full | Full | Limited |
| Users/Roles | Yes | No | No |
| Clients | Yes | Yes | No |
| Projects | Full | Full | View Only |
| Tasks | All | All | Own Only |
| Contracts | Yes | Yes | No |
| Settings | Yes | No | No |
| Analytics | Yes | Yes | No |

---

## Common Issues & Fixes

### "Class not found" error
```bash
composer dump-autoload
```

### Permission denied on storage
```bash
chmod -R 775 storage bootstrap/cache
```

### Views not updating
```bash
php artisan view:clear
```

### Database errors after code changes
```bash
php artisan migrate:fresh --seed --seeder=ProductionSeeder
php artisan admin:create --email=admin@amtar.om --password=Admin123!
```

---

## Files Created in Business Folder

| File | Purpose |
|------|---------|
| `ENVIRONMENT_SETUP.md` | Detailed .env configuration guide |
| `TEST_CASES.md` | Complete test cases with success criteria |
| `QUICK_START_CHECKLIST.md` | This quick reference |

---

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Check browser console for JavaScript errors
- Review test cases in `TEST_CASES.md`
