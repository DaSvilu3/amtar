# AMTAR Engineering System

<p align="center">
<img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 12">
<img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php" alt="PHP 8.2+">
<img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql" alt="MySQL 8.0">
<img src="https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap" alt="Bootstrap 5">
</p>

## 📋 Table of Contents
- [About](#about)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Documentation](#documentation)
- [Testing](#testing)
- [Deployment](#deployment)
- [Support](#support)
- [License](#license)

---

## 🎯 About

**AMTAR Engineering System** is a comprehensive project management platform designed specifically for engineering consultancy firms. It streamlines project workflows, automates task assignments, generates professional contracts, and provides real-time team collaboration features.

### Key Highlights
- ✅ **AI-Powered Task Assignment**: Automatically assigns tasks based on skills, availability, and workload
- 📄 **Contract Generation**: Creates professional bilingual contracts (Arabic/English) in DOCX and PDF
- 📊 **Advanced Reporting**: Generate detailed PDF and Excel reports for projects, tasks, and team performance
- 🔔 **Multi-Channel Notifications**: Email, SMS, and WhatsApp integration via Twilio
- 👥 **Role-Based Access Control**: Three-tier system (Administrator, Project Manager, Engineer)
- 📱 **Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices

---

## ✨ Features

### Project Management
- **Project Lifecycle Management**: From planning to completion
- **Service Packages**: Pre-configured service bundles for quick project setup
- **Milestones Tracking**: Define and monitor project milestones with linked tasks
- **Budget Management**: Track project costs and budget utilization
- **Project Notes**: Collaborative notes, reminders, and meeting logs

### Task Management
- **Kanban Board**: Drag-and-drop task status updates
- **Task Dependencies**: Define task relationships and block completion until prerequisites are met
- **Progress Tracking**: Real-time progress updates with estimated vs actual hours
- **Task Review Workflow**: Submit tasks for review, approve, or request revisions
- **Bulk Operations**: Auto-assign, update, or export multiple tasks at once

### AI-Powered Assignment
- **Intelligent Matching**: 6-factor scoring algorithm for optimal task assignment
- **Skill-Based Routing**: Matches tasks to engineers with relevant expertise
- **Workload Balancing**: Distributes work evenly across the team
- **Capacity Management**: Prevents overloading engineers beyond their capacity
- **Auto-Assignment**: One-click automatic assignment to best candidate

### Document Management
- **Centralized Storage**: All project files in one secure location
- **File Preview**: View PDFs, images, and documents inline
- **Drag-and-Drop Upload**: Easy file upload with progress tracking
- **Version Control**: Track file uploads and modifications
- **Polymorphic Attachments**: Attach files to projects, tasks, contracts, or clients

### Contract Generation
- **Template-Based**: Customizable DOCX contract templates
- **Auto-Population**: Automatically fills client, project, and service details
- **Service Tables**: Generates itemized service lists with costs
- **Bilingual Support**: Arabic and English in same document
- **Multiple Formats**: Export as DOCX (editable) or PDF (final)
- **Number to Words**: Converts contract value to written format

### Reporting & Analytics
- **6 Report Types**:
  1. Project Summary Report
  2. Task Status Report
  3. Team Performance Report
  4. Financial Report
  5. Client Activity Report
  6. Milestone Tracking Report
- **Export Formats**: PDF (professional) and Excel (data analysis)
- **Advanced Filtering**: By date range, project, client, status, assignee
- **Scheduled Reports**: Automated email delivery (daily, weekly, monthly)

### Integration & Notifications
- **Email Integration**: SMTP support for Gmail, Mailgun, SendGrid, AWS SES
- **SMS Notifications**: Twilio integration for text messages
- **WhatsApp Business**: Send notifications via WhatsApp
- **Multi-Channel Dispatcher**: Send to email, SMS, and WhatsApp simultaneously
- **Notification Triggers**:
  - Task assigned
  - Task due soon (24 hours)
  - Task submitted for review
  - Task approved/rejected
  - Project milestone reached

### User Management
- **Role-Based Access Control (RBAC)**:
  - **Administrator**: Full system access
  - **Project Manager**: Create/manage projects and tasks
  - **Engineer**: View and work on assigned tasks
- **Skill Management**: Assign skills to engineers with proficiency levels
- **User Profiles**: Avatar, contact info, notification preferences
- **Email Verification**: Secure account activation
- **Password Reset**: Self-service password recovery

---

## 🛠️ Technology Stack

### Backend
- **Laravel 12** - PHP framework (requires PHP 8.2+)
- **MySQL 8.0** / MariaDB 10.3+ - Relational database
- **Eloquent ORM** - Database abstraction layer
- **Laravel Queues** - Background job processing
- **Laravel Scheduler** - Automated task scheduling

### Frontend
- **Blade Templates** - Server-side rendering
- **Bootstrap 5** - Responsive CSS framework
- **JavaScript ES6+** - Modern JavaScript
- **Font Awesome 6** - Icon library
- **SortableJS** - Drag-and-drop functionality
- **Chart.js** - Data visualization

### Third-Party Services
- **Twilio** - SMS and WhatsApp API
- **DOMPDF** - PDF generation
- **PHPWord** - DOCX document generation
- **Maatwebsite/Excel** - Excel export functionality
- **Intervention/Image** - Image processing

### Development Tools
- **Laravel Dusk** - Browser automation testing
- **PHPUnit** - Unit and feature testing
- **Faker** - Test data generation
- **Composer** - PHP dependency manager
- **NPM** - JavaScript package manager

---

## 💻 System Requirements

### Minimum Requirements
- **PHP**: 8.2 or higher
- **Database**: MySQL 8.0+ or MariaDB 10.3+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Memory**: 512MB RAM (2GB recommended)
- **Disk Space**: 1GB minimum

### PHP Extensions Required
```
BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO,
PDO_MySQL, Tokenizer, XML, GD or Imagick, cURL, ZipArchive
```

### Optional Extensions
```
Redis (for caching and queues)
Memcached (for caching)
```

---

## 🚀 Installation

### Quick Start

1. **Clone the repository**
```bash
git clone <repository-url> amtar
cd amtar
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database** (edit `.env`)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=amtar_office
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed --class=ProductionSeeder
```

6. **Build assets**
```bash
npm run build
```

7. **Create admin user**
```bash
php artisan tinker
```
```php
$admin = \App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@amtar.om',
    'password' => bcrypt('SecurePassword123!'),
    'is_active' => true,
    'email_verified_at' => now(),
]);
$adminRole = \App\Models\Role::where('slug', 'administrator')->first();
$admin->roles()->attach($adminRole);
```

8. **Serve the application**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

---

## ⚙️ Configuration

### Company Information
Edit `.env` file:
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

#### Gmail SMTP
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@amtar.om"
```

### Twilio Integration (SMS & WhatsApp)

1. Sign up at [twilio.com](https://www.twilio.com)
2. Get Account SID and Auth Token from Console
3. Purchase phone number with SMS capability
4. Enable WhatsApp on your number

```env
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_PHONE_NUMBER=+1234567890
TWILIO_WHATSAPP_NUMBER=+1234567890
```

5. Install Twilio SDK:
```bash
composer require twilio/sdk
```

### Additional Packages (Optional)

**For Excel Exports:**
```bash
composer require maatwebsite/excel
```

**For Image Thumbnails:**
```bash
composer require intervention/image
```

**For Browser Testing:**
```bash
composer require --dev laravel/dusk
php artisan dusk:install
```

---

## 📚 Documentation

Comprehensive documentation is available in the `docs/` directory:

- **[INSTALLATION.md](INSTALLATION.md)** - Detailed installation guide with server setup
- **[USER_GUIDE.md](USER_GUIDE.md)** - User manual for all roles (Admin, PM, Engineer)
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - System architecture and design patterns
- **[DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)** - Production deployment checklist
- **[API Documentation](#)** - API endpoints (if enabled)

### Quick Links

**For Administrators:**
- [User Management](USER_GUIDE.md#1-user-management)
- [Client Management](USER_GUIDE.md#2-client-management)
- [Service Management](USER_GUIDE.md#3-service-management)
- [System Settings](USER_GUIDE.md#6-system-settings)

**For Project Managers:**
- [Creating Projects](USER_GUIDE.md#1-creating-and-managing-projects)
- [Task Assignment](USER_GUIDE.md#2-task-assignment)
- [Task Review Workflow](USER_GUIDE.md#3-task-review-workflow)
- [Team Workload Management](USER_GUIDE.md#4-team-workload-management)

**For Engineers:**
- [Viewing Tasks](USER_GUIDE.md#1-viewing-assigned-tasks)
- [Working on Tasks](USER_GUIDE.md#2-working-on-tasks)
- [Submitting for Review](USER_GUIDE.md#3-submitting-for-review)
- [File Upload Guide](USER_GUIDE.md#uploading-files)

---

## 🧪 Testing

### Unit & Feature Tests

**Run all tests:**
```bash
php artisan test
```

**Run specific test suite:**
```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature
```

**Test coverage:**
```bash
php artisan test --coverage
```

### Browser Tests (Laravel Dusk)

**Install Dusk:**
```bash
composer require --dev laravel/dusk
php artisan dusk:install
```

**Run browser tests:**
```bash
php artisan dusk
```

**Run specific test:**
```bash
php artisan dusk tests/Browser/LoginTest.php
```

### Test Structure

```
tests/
├── Unit/
│   ├── Services/
│   │   ├── TaskAssignmentServiceTest.php   # 11 tests
│   │   └── ContractTemplateServiceTest.php # 13 tests
│   ├── Policies/
│   │   ├── TaskPolicyTest.php              # 24 tests
│   │   └── ProjectPolicyTest.php           # 16 tests
│   └── Models/
├── Feature/
│   ├── AuthenticationTest.php
│   ├── ProjectManagementTest.php
│   └── TaskManagementTest.php
└── Browser/                                 # 50+ browser tests
    ├── Auth/
    ├── Administrator/
    ├── ProjectManager/
    └── Engineer/
```

**Current Test Coverage:** ~85% on critical services

---

## 🌐 Deployment

### Production Checklist

See [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) for comprehensive deployment guide.

**Quick Production Setup:**

1. **Optimize application:**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

2. **Configure environment:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

3. **Set up queue workers** (Supervisor):
```ini
[program:amtar-worker]
command=php /var/www/html/amtar/artisan queue:work database
numprocs=2
autostart=true
autorestart=true
user=www-data
```

4. **Schedule tasks** (Crontab):
```bash
* * * * * cd /var/www/html/amtar && php artisan schedule:run >> /dev/null 2>&1
```

5. **Enable SSL:**
```bash
sudo certbot --nginx -d your-domain.com
```

### Recommended Server Stack

- **Ubuntu 22.04 LTS**
- **Nginx 1.18+**
- **PHP 8.2-FPM**
- **MySQL 8.0**
- **Redis 7+** (for caching)
- **Supervisor** (for queue workers)
- **Let's Encrypt** (SSL certificate)

---

## 📊 Project Statistics

- **Models**: 24 Eloquent models
- **Controllers**: 22 admin controllers
- **Migrations**: 40+ database migrations
- **Factories**: 23 model factories
- **Tests**: 64+ unit/feature tests (50+ browser tests pending)
- **Routes**: 150+ web routes
- **Services**: 8 business logic services
- **Views**: 100+ Blade templates
- **JavaScript**: 5 custom modules (Kanban, Dependencies, File Preview, etc.)

---

## 🤝 Support

### Getting Help

- **Email**: support@amtar.om
- **Phone**: +968 XXXXXXXX (9 AM - 5 PM, Sun-Thu)
- **Documentation**: See `USER_GUIDE.md`

### Reporting Issues

If you encounter bugs or have feature requests:
1. Check existing documentation
2. Review FAQ in `USER_GUIDE.md`
3. Contact support with:
   - Detailed description of the issue
   - Steps to reproduce
   - Screenshots (if applicable)
   - Error messages from logs

### Training Resources

- **User Manual**: `USER_GUIDE.md`
- **Video Tutorials**: Available in Help → Training Videos
- **Administrator Guide**: See documentation section

---

## 🔒 Security

### Reporting Vulnerabilities

If you discover a security vulnerability:
- **DO NOT** create a public issue
- Email: security@amtar.om
- Include detailed description and reproduction steps
- Allow time for patch before public disclosure

### Security Features

- ✅ CSRF protection on all forms
- ✅ XSS prevention via Blade escaping
- ✅ SQL injection protection (Eloquent/PDO)
- ✅ Password hashing with bcrypt
- ✅ Email verification
- ✅ Role-based access control
- ✅ File upload validation
- ✅ SSL/TLS encryption

---

## 📝 License

This project is proprietary software owned by **AMTAR Engineering & Design Consultancy**.

**All rights reserved.** Unauthorized copying, modification, distribution, or use of this software is strictly prohibited without explicit written permission.

**Copyright © 2026 AMTAR Engineering & Design Consultancy**

---

## 👥 Credits

### Development Team
- **Lead Developer**: [Name]
- **Backend Developer**: [Name]
- **Frontend Developer**: [Name]
- **QA Engineer**: [Name]
- **Project Manager**: [Name]

### Technologies Used
- Laravel Framework by Taylor Otwell
- Bootstrap by Twitter
- Font Awesome by Fonticons
- Chart.js by Chart.js contributors
- DOMPDF by Dompdf
- Twilio API by Twilio Inc.

---

## 🗺️ Roadmap

### Version 1.0 (Current) - January 2026
- ✅ Core project management features
- ✅ AI-powered task assignment
- ✅ Contract generation (DOCX/PDF)
- ✅ Advanced reporting (6 report types)
- ✅ Third-party integrations (Email, SMS, WhatsApp)
- ✅ Kanban board with drag-drop
- ✅ Task dependencies
- ⏳ Comprehensive browser tests (in progress)

### Version 1.1 (Planned) - Q2 2026
- 📱 Mobile app (iOS/Android)
- 🔌 RESTful API for third-party integration
- 📊 Advanced analytics dashboard
- 🗓️ Calendar integration (Google Calendar, Outlook)
- 💬 In-app chat/messaging
- 📧 Email template builder
- 🔍 Advanced search with filters

### Version 2.0 (Future) - Q4 2026
- 🤖 AI-powered project planning
- 📈 Predictive analytics
- 🌐 Multi-company support
- 💰 Invoicing and billing module
- 📦 Inventory management
- 🔗 Webhooks for external integrations
- 🎨 Theme customization

---

## 📞 Contact

**AMTAR Engineering & Design Consultancy**

- **Address**: Muscat, Sultanate of Oman
- **Phone**: +968 XXXXXXXX
- **Email**: info@amtar.om
- **Website**: www.amtar.om

---

<p align="center">
Built with ❤️ by the AMTAR Development Team
</p>

<p align="center">
<strong>Version 1.0</strong> | <strong>January 2026</strong>
</p>
