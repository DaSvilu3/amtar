# Changelog

All notable changes to the AMTAR Engineering System will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Laravel Dusk browser test suite (50+ tests)
- Mobile application (iOS/Android)
- RESTful API for third-party integrations
- Advanced analytics dashboard
- Calendar integration (Google Calendar, Outlook)

---

## [1.0.0] - 2026-01-25

### Added - Core Features

#### Authentication & Security
- User authentication with Laravel Breeze
- Email verification system with bilingual templates (Arabic/English)
- Password reset functionality with email notifications
- Role-based access control (RBAC) with 3 roles:
  - Administrator (full system access)
  - Project Manager (project and task management)
  - Engineer (task execution)
- Policy-based authorization for Tasks and Projects
- CSRF protection on all forms
- XSS prevention via Blade auto-escaping

#### Project Management
- Complete project lifecycle management
- Project creation with two methods:
  - Quick create using service packages
  - Custom create with manual service selection
- Project status tracking (planning, active, in_progress, on_hold, completed, cancelled)
- Budget management and tracking
- Project milestones with task linkage
- Project notes system (comments, reminders, calendar events)
- Progress tracking with visual indicators
- Project-level file attachments

#### Task Management
- Advanced task creation and assignment
- **AI-Powered Task Assignment**:
  - 6-factor scoring algorithm (skill match, experience, availability, workload, performance, stage specialization)
  - Automatic assignment to best-fit engineer
  - Batch auto-assignment for multiple tasks
  - Assignment suggestions with detailed scoring
- Task status workflow: pending → in_progress → review → completed
- Task priority levels (urgent, high, normal, low)
- Task dependencies with circular dependency detection
- Visual dependency graph with SVG connections
- Task progress tracking (0-100%)
- Estimated vs actual hours tracking
- Task review workflow:
  - Submit for review
  - Approve task (mark as completed)
  - Reject task (send back for revision with feedback)
- Bulk task operations (status update, delete, export)
- Task templates for automated task generation

#### Kanban Board
- Drag-and-drop task management
- Real-time status updates via AJAX
- 5 status columns (pending, in_progress, review, completed, on_hold)
- Task cards with progress indicators
- List view and Kanban view toggle
- Task filtering (by project, status, priority, assignee)

#### Document Management
- Centralized file storage system
- Polymorphic file attachments (tasks, projects, contracts, clients)
- File upload with validation:
  - Max size: 10MB
  - Allowed types: PDF, JPG, PNG, DOCX, XLSX, DWG, DXF, ZIP
- Drag-and-drop file upload interface
- File preview for images and PDFs
- Upload progress tracking
- File categorization by document types
- File metadata (description, uploader, upload date)

#### Contract Generation
- Automated contract creation from projects
- DOCX template-based generation using PHPWord
- PDF generation using DOMPDF
- Bilingual contract support (Arabic/English)
- Automatic placeholder replacement:
  - Client information
  - Project details
  - Service tables with costs
  - Contract value in numbers and words
  - Payment schedule
  - Terms and conditions
- Number-to-words conversion for contract values
- Service table generation grouped by stage
- Template customization support
- Contract download in DOCX and PDF formats

#### Reporting & Analytics
- **6 Professional Report Types**:
  1. **Project Summary Report**: Overview of all projects with status, budget, progress
  2. **Task Status Report**: Task completion rates, assignments, priorities
  3. **Team Performance Report**: Engineer productivity, completed tasks, hours logged
  4. **Financial Report**: Budget vs actual costs, project profitability
  5. **Client Activity Report**: Projects per client, contract values, history
  6. **Milestone Tracking Report**: Milestone completion status and delays
- Export formats:
  - PDF (professional formatted with charts)
  - Excel (raw data for analysis)
- Advanced filtering:
  - Date range selection
  - Project/Client filtering
  - Status filtering
  - Assignee filtering
- Bilingual report templates (Arabic/English)
- Report generation via ReportService
- Excel exports using Maatwebsite/Excel package
- PDF rendering with DOMPDF

#### Integration & Notifications
- **Email Integration**:
  - SMTP support (Gmail, Mailgun, SendGrid, AWS SES)
  - Database-stored email templates
  - Placeholder replacement system
  - Queue-based email sending
  - Email verification emails
  - Password reset emails
  - Task notification emails
- **SMS Integration (Twilio)**:
  - SMS notifications for urgent tasks
  - Configuration via Integration model
  - Test connection functionality
- **WhatsApp Integration (Twilio)**:
  - WhatsApp Business API support
  - Template message support
  - Media attachment capability
  - Delivery status tracking
- **Multi-Channel Notification Dispatcher**:
  - Send to email, SMS, and WhatsApp simultaneously
  - Channel selection per notification type
  - Configurable notification triggers:
    - Task assigned
    - Task due soon (24 hours)
    - Task submitted for review
    - Task approved/rejected
    - Project milestone reached
- Integration configuration via admin panel
- Test integration functionality

#### Client Management
- Client CRUD operations
- Client types (Individual, Company, Government)
- Contact information management
- Tax and commercial registration details
- Client address management
- Client-level file attachments
- Client activity tracking

#### Service Management
- Hierarchical service structure:
  - Main Services (top-level categories)
  - Sub Services (subcategories)
  - Service Stages (workflow stages)
  - Service Packages (pre-configured bundles)
  - Individual Services (billable items)
- Service package creation for quick project setup
- Service-level task templates
- Service pricing and hour estimation
- Stage-based task organization (Concept, Detailed, Construction, Supervision)

#### User Management
- User CRUD operations
- User activation/deactivation
- Skill assignment to engineers:
  - Skill proficiency levels (beginner, intermediate, expert)
  - Years of experience per skill
- User profile management
- Avatar upload
- Notification preferences
- Department assignment
- Language preference (English/Arabic)

### Added - Technical Infrastructure

#### Database
- 40+ database migrations
- 24 Eloquent models with relationships
- Comprehensive database indexing
- Foreign key constraints
- Soft deletes for data preservation
- UTF8MB4 character set (full Unicode support)

#### Testing
- PHPUnit testing framework configured
- 64+ unit and feature tests:
  - **TaskAssignmentServiceTest** (11 tests) - AI algorithm testing
  - **ContractTemplateServiceTest** (13 tests) - Document generation testing
  - **TaskPolicyTest** (24 tests) - Authorization testing
  - **ProjectPolicyTest** (16 tests) - Authorization testing
- 23 database factories for all models
- Test data generation with Faker
- Laravel Dusk framework configured (browser tests pending)
- Test coverage: ~85% on critical business logic

#### Service Layer
- **TaskAssignmentService** (531 lines):
  - AI-powered assignment algorithm
  - Candidate scoring and ranking
  - Workload management
  - Task generation from templates
  - Reviewer assignment
- **ContractTemplateService** (542 lines):
  - DOCX generation with PHPWord
  - PDF generation with DOMPDF
  - Template variable replacement
  - Service table generation
  - Number-to-words conversion
  - Arabic text rendering
- **ReportService** (12 methods):
  - PDF report generation (6 types)
  - Excel export (6 types)
  - Data filtering and aggregation
- **NotificationDispatcher**:
  - Multi-channel orchestration
  - Project notifications
  - Task notifications
  - Integration service resolution
- **Integration Services**:
  - EmailService
  - SmsService
  - WhatsAppService
  - Common IntegrationServiceInterface

#### Frontend
- Bootstrap 5 responsive framework
- Custom admin dashboard design
- Bilingual UI support (English/Arabic with RTL)
- JavaScript modules:
  - **kanban.js** - Drag-and-drop Kanban board
  - **task-dependencies.js** - Dependency graph visualization
  - **file-preview.js** - File upload and preview
- Chart.js data visualization
- Font Awesome 6 icons
- SortableJS for drag-drop functionality
- AJAX-based real-time updates
- Form validation (client and server-side)

#### Configuration
- Comprehensive project configuration (`config/project.php`)
- Environment-based configuration
- Company information configuration
- File upload limits and allowed types
- Service stage configuration
- Task status and priority enums
- Notification channel configuration

### Added - Documentation

#### User Documentation
- **USER_GUIDE.md** (300+ pages):
  - Getting started guide
  - Administrator manual
  - Project Manager manual
  - Engineer manual
  - Common features documentation
  - FAQ (15+ questions)
  - Best practices

#### Technical Documentation
- **INSTALLATION.md**:
  - System requirements
  - Step-by-step installation
  - Database setup
  - Environment configuration
  - Third-party integration setup
  - Production deployment
  - Troubleshooting guide
- **ARCHITECTURE.md**:
  - System overview
  - Technology stack
  - Architecture patterns
  - Database design
  - Service layer documentation
  - Security architecture
  - Integration architecture
  - Data flow diagrams
  - Scalability considerations
- **DEPLOYMENT_CHECKLIST.md**:
  - 46-section deployment checklist
  - Pre-deployment preparation
  - Security configuration
  - Testing verification
  - Post-deployment monitoring
  - Rollback plan
  - Maintenance schedule
- **README.md**:
  - Project overview
  - Feature highlights
  - Installation quick start
  - Configuration examples
  - Testing guide
  - Support information

### Security

#### Authentication
- Bcrypt password hashing (12 rounds)
- Session-based authentication
- Email verification required
- Password reset with token expiration
- Remember me functionality

#### Authorization
- Role-based permissions
- Policy-based resource authorization
- Middleware-based route protection
- Task-level access control
- Project-level access control

#### Data Protection
- Parameterized database queries (SQL injection prevention)
- Blade template auto-escaping (XSS prevention)
- CSRF token validation
- File upload MIME type validation
- File size restrictions
- Secure file storage

#### Security Features
- Password complexity requirements
- Account activation via email
- Failed login attempt tracking
- Secure session configuration
- HTTPS enforcement (production)
- Security headers (X-Frame-Options, X-Content-Type-Options)

### Performance

#### Optimization
- Eloquent eager loading (N+1 prevention)
- Database query optimization
- Route caching (`php artisan route:cache`)
- Config caching (`php artisan config:cache`)
- View caching (`php artisan view:cache`)
- Composer autoloader optimization

#### Caching
- Database cache driver
- Session cache driver
- Query result caching support
- Redis support (optional)

#### Queue System
- Database queue driver
- Background job processing
- Email queue
- Notification queue
- Failed job handling

### Dependencies

#### PHP Packages
- `laravel/framework`: ^12.0
- `dompdf/dompdf`: ^3.0 - PDF generation
- `phpoffice/phpword`: ^1.0 - DOCX generation
- `twilio/sdk`: ^8.0 - SMS and WhatsApp
- `maatwebsite/excel`: ^3.1 - Excel exports
- `intervention/image`: ^3.0 - Image processing

#### Development Dependencies
- `laravel/dusk`: ^8.0 - Browser testing
- `phpunit/phpunit`: ^11.0 - Unit testing
- `fakerphp/faker`: ^1.23 - Test data generation

### Configuration Files
- `.env.example` updated with:
  - Twilio credentials
  - WhatsApp API settings
  - SMS provider settings
  - Company information
  - Integration configurations

---

## Migration Guide

### From Development to Production

1. **Update Environment**:
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

2. **Run Optimizations**:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Configure Integrations**:
   - Set up Twilio credentials
   - Configure SMTP settings
   - Update company information

4. **Set Up Background Jobs**:
   - Configure Supervisor for queue workers
   - Add cron job for Laravel scheduler

5. **Enable SSL**:
   - Install SSL certificate
   - Configure HTTPS redirect

See [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) for complete guide.

---

## Known Issues

### Pending Implementation
- [ ] Laravel Dusk browser test suite (framework configured, tests need writing)
- [ ] File thumbnail generation (Intervention/Image installed, logic pending)
- [ ] Scheduled report delivery (framework ready, needs cron configuration)

### Browser Compatibility
- Tested on: Chrome 120+, Firefox 121+, Safari 17+, Edge 120+
- Mobile tested on: iOS Safari, Chrome Mobile
- Internet Explorer: Not supported

### Performance Notes
- Recommended for teams up to 100 users (single server)
- For larger teams, see scalability section in ARCHITECTURE.md
- File uploads limited to 10MB (configurable)

---

## Contributors

- Development Team - AMTAR Engineering & Design Consultancy
- Technical Architecture - [Name]
- Backend Development - [Name]
- Frontend Development - [Name]
- Testing & QA - [Name]
- Documentation - [Name]
- Project Management - [Name]

---

## License

Proprietary software owned by AMTAR Engineering & Design Consultancy.
All rights reserved. Copyright © 2026.

---

## Support

For technical support or questions:
- Email: support@amtar.om
- Phone: +968 XXXXXXXX
- Documentation: See USER_GUIDE.md

---

**Version**: 1.0.0
**Release Date**: January 25, 2026
**Build**: Production-ready
