# AMTAR Engineering System - System Architecture

## Table of Contents
1. [System Overview](#system-overview)
2. [Technology Stack](#technology-stack)
3. [Architecture Patterns](#architecture-patterns)
4. [Database Design](#database-design)
5. [Service Layer](#service-layer)
6. [Security Architecture](#security-architecture)
7. [Integration Architecture](#integration-architecture)
8. [File Structure](#file-structure)
9. [Data Flow](#data-flow)
10. [Scalability Considerations](#scalability-considerations)

---

## System Overview

### Purpose
AMTAR Engineering System is a comprehensive project management platform designed for engineering consultancy firms to manage projects, tasks, clients, contracts, and team collaboration.

### Key Features
- **Project Management**: Complete lifecycle from planning to completion
- **Task Assignment**: AI-powered automatic task assignment based on skills and availability
- **Contract Generation**: Automated contract creation with bilingual support (Arabic/English)
- **Document Management**: Centralized file storage with version control
- **Reporting**: Professional PDF and Excel reports
- **Integrations**: Email, SMS, WhatsApp notifications
- **Role-Based Access**: Three-tier role system (Admin, PM, Engineer)

### System Architecture Type
**Monolithic MVC Architecture** with service layer separation for business logic.

---

## Technology Stack

### Backend Framework
- **Laravel 12** (PHP 8.2+)
  - Eloquent ORM for database interactions
  - Blade templating engine
  - Built-in authentication and authorization
  - Queue system for background jobs

### Database
- **MySQL 8.0+** / **MariaDB 10.3+**
  - InnoDB storage engine
  - UTF8MB4 character set (full Unicode support including emojis)
  - Foreign key constraints for referential integrity

### Frontend
- **HTML5 / CSS3**
- **JavaScript (ES6+)**
- **Bootstrap 5** - Responsive CSS framework
- **Font Awesome 6** - Icon library
- **SortableJS** - Drag-and-drop functionality
- **Chart.js** - Data visualization

### Third-Party Libraries

#### PHP Packages (composer.json)
```json
{
  "dompdf/dompdf": "PDF generation",
  "phpoffice/phpword": "DOCX contract generation",
  "twilio/sdk": "SMS and WhatsApp integration",
  "maatwebsite/excel": "Excel export functionality",
  "intervention/image": "Image processing and thumbnails"
}
```

#### Development Tools
```json
{
  "laravel/dusk": "Browser testing framework",
  "phpunit/phpunit": "Unit testing",
  "fakerphp/faker": "Test data generation"
}
```

---

## Architecture Patterns

### 1. MVC Pattern (Model-View-Controller)

**Models** (`app/Models/`)
- Eloquent ORM models representing database tables
- Define relationships between entities
- Contain business logic helper methods
- Handle data validation rules

**Views** (`resources/views/`)
- Blade templates for rendering UI
- Partials for reusable components
- Layouts for consistent page structure
- Support for Arabic (RTL) and English (LTR)

**Controllers** (`app/Http/Controllers/`)
- Handle HTTP requests
- Delegate business logic to services
- Return views or JSON responses
- Apply authorization policies

### 2. Service Layer Pattern

**Purpose**: Separate business logic from controllers

**Location**: `app/Services/`

**Key Services:**

```
app/Services/
├── TaskAssignmentService.php         # AI-powered task assignment
├── ContractTemplateService.php       # Contract generation (DOCX/PDF)
├── ReportService.php                 # Report generation
├── NotificationDispatcher.php        # Multi-channel notifications
└── Integrations/
    ├── IntegrationServiceInterface.php
    ├── EmailService.php
    ├── SmsService.php
    └── WhatsAppService.php
```

**Service Pattern Example:**
```php
class TaskAssignmentService
{
    // Dependency injection
    public function __construct(
        private UserRepository $users,
        private TaskRepository $tasks
    ) {}

    // Business logic encapsulated
    public function autoAssign(Task $task): bool
    {
        $bestCandidate = $this->findBestAssignee($task);
        if ($bestCandidate) {
            $task->assignTo($bestCandidate);
            return true;
        }
        return false;
    }
}
```

### 3. Repository Pattern (Partial Implementation)

Used implicitly through Eloquent models:
```php
// Model acts as repository
$projects = Project::with('client', 'tasks')
    ->where('status', 'active')
    ->get();
```

### 4. Policy Pattern (Authorization)

**Location**: `app/Policies/`

**Pattern:**
```php
class TaskPolicy
{
    public function update(User $user, Task $task): bool
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('project-manager') ||
               $task->assigned_to === $user->id;
    }
}
```

Registered in `AuthServiceProvider`:
```php
protected $policies = [
    Task::class => TaskPolicy::class,
    Project::class => ProjectPolicy::class,
];
```

### 5. Observer Pattern (Events & Listeners)

**Not explicitly implemented**, but can be added for:
- Audit logging
- Real-time notifications
- Webhook triggers

---

## Database Design

### Entity Relationship Overview

```
┌─────────┐      ┌──────────┐      ┌─────────┐
│  Client ├──────┤  Project ├──────┤  Task   │
└─────────┘      └────┬─────┘      └────┬────┘
                      │                  │
                      ├──────────────────┤
                      │                  │
                 ┌────▼────┐        ┌───▼────┐
                 │Contract │        │  File  │
                 └─────────┘        └────────┘
```

### Core Tables

#### 1. Users Table
```sql
users
├── id (PK)
├── name
├── email (unique)
├── password
├── phone
├── is_active
├── email_verified_at
├── remember_token
├── created_at
├── updated_at
```

**Relationships:**
- Has many Tasks (as assignee)
- Has many Tasks (as reviewer)
- Belongs to many Roles
- Belongs to many Skills

#### 2. Roles Table (RBAC)
```sql
roles
├── id (PK)
├── name
├── slug (unique)
├── description
├── permissions (JSON)
├── created_at
├── updated_at
```

**3 Default Roles:**
1. **Administrator**: Full system access
2. **Project Manager**: Project/task management
3. **Engineer**: Task execution only

#### 3. Projects Table
```sql
projects
├── id (PK)
├── client_id (FK → clients)
├── project_manager_id (FK → users)
├── main_service_id (FK → main_services)
├── sub_service_id (FK → sub_services)
├── name
├── project_number (unique)
├── description
├── status (enum: planning, active, in_progress, on_hold, completed, cancelled)
├── priority (enum: low, normal, high, urgent)
├── budget
├── start_date
├── end_date
├── location
├── progress (integer 0-100)
├── created_at
├── updated_at
```

**Relationships:**
- Belongs to Client
- Belongs to User (project manager)
- Has many Tasks
- Has many Milestones
- Has one Contract
- Has many ProjectServices
- Has many Files (polymorphic)

#### 4. Tasks Table
```sql
tasks
├── id (PK)
├── project_id (FK → projects)
├── project_service_id (FK → project_services)
├── milestone_id (FK → milestones)
├── task_template_id (FK → task_templates)
├── assigned_to (FK → users)
├── reviewed_by (FK → users)
├── created_by (FK → users)
├── title
├── description (text)
├── status (enum: pending, in_progress, review, completed, cancelled)
├── priority (enum: low, normal, high, urgent)
├── start_date
├── due_date
├── completed_at
├── reviewed_at
├── review_notes (text)
├── estimated_hours
├── actual_hours
├── progress (integer 0-100)
├── requires_review (boolean)
├── sort_order (integer)
├── created_at
├── updated_at
```

**Relationships:**
- Belongs to Project
- Belongs to User (assignee)
- Belongs to User (reviewer)
- Belongs to many Tasks (dependencies) - self-referencing many-to-many
- Has many Files (polymorphic)

#### 5. Task Dependencies Table (Many-to-Many)
```sql
task_dependencies
├── id (PK)
├── task_id (FK → tasks)
├── depends_on_task_id (FK → tasks)
├── dependency_type (enum: finish_to_start, start_to_start, finish_to_finish)
├── created_at
├── updated_at
```

**Purpose**: Defines task dependencies to prevent starting tasks before prerequisites are complete.

#### 6. Files Table (Polymorphic)
```sql
files
├── id (PK)
├── entity_type (polymorphic: Task, Project, Contract, Client)
├── entity_id (polymorphic ID)
├── document_type_id (FK → document_types)
├── name
├── original_name
├── file_path
├── mime_type
├── file_size (bytes)
├── category
├── description
├── uploaded_by (FK → users)
├── created_at
├── updated_at
```

**Polymorphic Relationships:**
- Can belong to Task, Project, Contract, or Client

### Service Hierarchy

```
┌─────────────────┐
│  Main Service   │  (e.g., Architectural Design)
└────────┬────────┘
         │
    ┌────▼─────┐
    │Sub Service│  (e.g., Residential Buildings)
    └────┬─────┘
         │
    ┌────▼────────────┐
    │ Service Package │  (e.g., Villa Design Package)
    └────┬────────────┘
         │
    ┌────▼───────┐
    │  Services  │  (e.g., Floor Plans, Elevations, 3D Views)
    └────────────┘
```

**Tables:**
1. `main_services` - Top-level categories
2. `sub_services` - Subcategories
3. `service_packages` - Pre-defined service bundles
4. `service_stages` - Workflow stages (Concept, Detailed, Construction)
5. `services` - Individual billable services
6. `project_services` - Services linked to specific projects

### Indexing Strategy

**Primary Indexes:**
- All foreign keys indexed automatically
- Unique indexes on: email, project_number, contract_number

**Composite Indexes:**
```sql
-- For faster task queries
INDEX idx_tasks_status_assigned (status, assigned_to, due_date)

-- For project filtering
INDEX idx_projects_status_client (status, client_id, created_at)

-- For polymorphic file queries
INDEX idx_files_entity (entity_type, entity_id)
```

---

## Service Layer

### TaskAssignmentService

**Purpose**: Automatically assign tasks to engineers based on AI-powered scoring algorithm

**Key Methods:**

```php
class TaskAssignmentService
{
    // Find best engineer for a task
    public function findBestAssignee(Task $task): ?User;

    // Get candidate engineers
    public function getCandidates(Task $task): Collection;

    // Calculate assignment score
    public function calculateAssignmentScore(User $user, Task $task): float;

    // Auto-assign task
    public function autoAssign(Task $task): bool;

    // Generate tasks from templates
    public function generateTasksFromTemplates(
        Project $project,
        ProjectService $projectService,
        ?Milestone $milestone,
        bool $autoAssign
    ): Collection;
}
```

**Scoring Algorithm:**

Weighted score calculation (0-100):
1. **Skill Match** (25%): Matches required skills with user skills
2. **Years of Experience** (20%): Favors experienced engineers
3. **Availability** (20%): Considers current capacity
4. **Current Workload** (15%): Balances team workload
5. **Past Performance** (10%): Reviews completion rate
6. **Stage Specialization** (10%): Prefers stage experts

```php
$score = (
    ($skillMatch * 0.25) +
    ($experienceScore * 0.20) +
    ($availabilityScore * 0.20) +
    ($workloadScore * 0.15) +
    ($performanceScore * 0.10) +
    ($stageScore * 0.10)
);
```

### ContractTemplateService

**Purpose**: Generate professional contracts in DOCX and PDF formats

**Key Methods:**

```php
class ContractTemplateService
{
    // Generate DOCX contract
    public function generateDocx(Contract $contract): string;

    // Generate PDF contract
    public function generatePdf(Contract $contract): string;

    // Replace placeholders
    private function replaceVariables(string $content, Contract $contract): string;

    // Generate service table
    private function generateServiceTable(Contract $contract): string;

    // Convert number to words
    private function numberToWords(float $number): string;
}
```

**Template Placeholders:**

```
{{client_name}}
{{client_address}}
{{company_name}}
{{contract_number}}
{{contract_date}}
{{contract_value}}
{{contract_value_words}}
{{project_name}}
{{project_description}}
{{service_table}}
{{payment_schedule}}
{{terms_and_conditions}}
```

### ReportService

**Purpose**: Generate analytical reports in PDF and Excel formats

**Report Types:**
1. Project Summary Report
2. Task Status Report
3. Team Performance Report
4. Financial Report
5. Client Activity Report
6. Milestone Tracking Report

**Export Formats:**
- **PDF**: Professional formatted reports with charts
- **Excel**: Raw data for further analysis

---

## Security Architecture

### Authentication

**Laravel Breeze/Fortify** (default Laravel authentication)

**Features:**
- Password hashing with bcrypt (12 rounds)
- Email verification
- Password reset via email tokens
- Remember me functionality
- Session-based authentication

### Authorization

**Role-Based Access Control (RBAC)**

**Implementation:**
```php
// Middleware-based
Route::middleware(['role:administrator'])->group(function() {
    // Admin-only routes
});

// Policy-based
$this->authorize('update', $task);

// Helper-based
if ($user->hasRole('administrator')) {
    // Admin logic
}
```

**Permission Structure:**
```php
'permissions' => [
    'projects' => ['view', 'create', 'edit', 'delete'],
    'tasks' => ['view', 'create', 'edit', 'delete', 'assign'],
    'users' => ['view', 'create', 'edit', 'delete'],
    'settings' => ['view', 'edit'],
]
```

### Data Protection

**Database Security:**
- Parameterized queries (Eloquent/PDO) - prevents SQL injection
- Foreign key constraints - maintains referential integrity
- Soft deletes - preserves data history

**XSS Protection:**
- Blade `{{ }}` auto-escapes output
- CSP headers configured
- User input sanitization

**CSRF Protection:**
- `@csrf` tokens on all forms
- Automatic validation

**File Upload Security:**
- MIME type validation
- File size limits (10MB default)
- Storage outside public directory
- Virus scanning (optional integration)

---

## Integration Architecture

### Email Service

**Provider**: Configurable (SMTP, Mailgun, SendGrid, SES)

**Email Types:**
- Transactional emails (password reset, verification)
- Notification emails (task assignment, review)
- Scheduled reports

**Template System:**
- Database-stored email templates
- Placeholder replacement
- Bilingual support (Arabic/English)

### SMS Integration (Twilio)

**Use Cases:**
- Task assignment alerts
- Urgent task notifications
- Password reset OTP

**Implementation:**
```php
class SmsService implements IntegrationServiceInterface
{
    public function send(string $recipient, string $message): bool
    {
        $client = new Twilio\Rest\Client($sid, $token);
        $client->messages->create($recipient, [
            'from' => $this->config['from_number'],
            'body' => $message
        ]);
        return true;
    }
}
```

### WhatsApp Integration (Twilio)

**Use Cases:**
- Project updates
- Document sharing links
- Team coordination

**Features:**
- Template messages
- Media attachments
- Delivery status tracking

### Multi-Channel Notification Dispatcher

**Pattern**: Strategy pattern for notification channels

```php
class NotificationDispatcher
{
    public function notify(User $user, string $message, array $channels)
    {
        foreach ($channels as $channel) {
            $service = match($channel) {
                'email' => app(EmailService::class),
                'sms' => app(SmsService::class),
                'whatsapp' => app(WhatsAppService::class),
            };
            $service->send($user->contact, $message);
        }
    }
}
```

---

## File Structure

```
amtar/
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Admin/          # Admin panel controllers
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/                 # Eloquent models (24 models)
│   ├── Notifications/          # Email/SMS notifications
│   ├── Policies/               # Authorization policies
│   ├── Providers/
│   ├── Services/               # Business logic layer
│   │   └── Integrations/       # Third-party integrations
│   └── Exports/                # Excel export classes
├── bootstrap/
├── config/                     # Configuration files
│   └── project.php             # Custom project config
├── database/
│   ├── factories/              # Model factories (23 factories)
│   ├── migrations/             # Database schema (40+ migrations)
│   └── seeders/                # Data seeders
├── public/
│   ├── css/
│   ├── js/
│   │   ├── kanban.js
│   │   ├── task-dependencies.js
│   │   └── file-preview.js
│   └── storage/                # Symlink to storage/app/public
├── resources/
│   ├── views/
│   │   ├── admin/              # Admin panel views
│   │   │   ├── dashboard/
│   │   │   ├── projects/
│   │   │   ├── tasks/
│   │   │   ├── clients/
│   │   │   ├── reports/
│   │   │   └── settings/
│   │   ├── auth/               # Authentication views
│   │   └── layouts/            # Page layouts
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php                 # Web routes (150+ routes)
│   ├── api.php                 # API routes
│   └── console.php             # Artisan commands
├── storage/
│   ├── app/
│   │   ├── public/             # Publicly accessible files
│   │   ├── contracts/          # Generated contracts
│   │   └── reports/            # Generated reports
│   ├── framework/
│   └── logs/
├── tests/
│   ├── Feature/
│   ├── Unit/
│   │   ├── Services/           # Service tests
│   │   └── Policies/           # Policy tests
│   └── Browser/                # Laravel Dusk tests
├── .env.example
├── composer.json
├── package.json
├── phpunit.xml
├── INSTALLATION.md
├── USER_GUIDE.md
├── DEPLOYMENT_CHECKLIST.md
└── ARCHITECTURE.md (this file)
```

---

## Data Flow

### Task Assignment Flow

```
User Creates Task
    ↓
TaskController@store
    ↓
Task Model Saved
    ↓
Auto-Assign? → Yes → TaskAssignmentService
    │                       ↓
    │                   findBestAssignee()
    │                       ↓
    │                   getCandidates()
    │                       ↓
    │                   calculateAssignmentScore()
    │                       ↓
    │                   Assign to Best Engineer
    │                       ↓
    └──────────────→ NotificationDispatcher
                            ↓
                    Email + SMS + WhatsApp
                            ↓
                    Engineer Notified
```

### Contract Generation Flow

```
Project Created
    ↓
Generate Contract Option Selected
    ↓
ContractTemplateService@generateDocx()
    ↓
Load Template (storage/app/contracts/templates/default.docx)
    ↓
Replace Placeholders ({{client_name}}, {{contract_value}}, etc.)
    ↓
Generate Service Table (list all project services)
    ↓
Save DOCX (storage/app/contracts/CNT-2026-XXXX.docx)
    ↓
Optional: Convert to PDF (DOMPDF)
    ↓
Download or Email to Client
```

### Report Generation Flow

```
User Requests Report
    ↓
ReportController@generateReport()
    ↓
Apply Filters (date range, project, client)
    ↓
ReportService → PDF or Excel?
    ↓
PDF Branch                  Excel Branch
    ↓                           ↓
loadView()                  Excel::download()
    ↓                           ↓
DOMPDF rendering            Maatwebsite\Excel export
    ↓                           ↓
download()                  download()
```

---

## Scalability Considerations

### Current Limitations (Monolithic)
- Single server deployment
- Synchronous request processing
- File storage on local disk
- Database on same server

### Recommended Scaling Path

#### Phase 1: Vertical Scaling (0-100 users)
- Upgrade server resources (CPU, RAM, SSD)
- Enable OPcache for PHP
- Database query optimization

#### Phase 2: Horizontal Scaling (100-500 users)
- **Load Balancer**: Nginx/HAProxy in front of multiple app servers
- **Separate Database Server**: Dedicated MySQL server
- **Redis**: For caching and session storage
- **Queue Workers**: Separate servers for background jobs
- **CDN**: Cloudflare or AWS CloudFront for static assets

**Architecture:**
```
Internet → Load Balancer → [App Server 1, App Server 2, App Server 3]
                                ↓
                          Redis (Cache + Sessions)
                                ↓
                          MySQL Database (Master)
                                ↓
                          Queue Workers
```

#### Phase 3: Cloud Migration (500+ users)
- **AWS/Azure Deployment**:
  - EC2/VMs for app servers (auto-scaling)
  - RDS for database (managed MySQL with replication)
  - S3/Blob Storage for file uploads
  - ElastiCache for Redis
  - SQS for queue management
  - CloudWatch for monitoring

### Database Optimization

**Indexing:**
- Add composite indexes for frequent queries
- Monitor slow query log

**Query Optimization:**
- Use eager loading to prevent N+1 queries
- Implement database query caching

**Read Replicas:**
- Master for writes, replicas for reads
- Separate reporting queries to replica

### Caching Strategy

**Cache Layers:**
1. **OPcache**: PHP opcode caching (already enabled in production)
2. **Application Cache**: Redis/Memcached for:
   - User sessions
   - Frequently accessed data (roles, permissions)
   - Query results
3. **HTTP Cache**: Varnish/Nginx for static content

### File Storage Optimization

**Current**: Local disk (`storage/app/`)

**Scalable Options:**
1. **Network File System (NFS)**: Shared storage across app servers
2. **Cloud Storage**:
   - AWS S3
   - Azure Blob Storage
   - Google Cloud Storage
3. **CDN Integration**: Serve uploaded files via CDN

### Queue System Enhancement

**Current**: Database queue driver

**Production Recommendations:**
- **Redis**: Faster queue processing
- **AWS SQS**: Fully managed, highly scalable
- **RabbitMQ**: Advanced message routing

---

## Performance Benchmarks

### Target Metrics
- Page Load Time: <2 seconds
- Database Query Time: <100ms
- File Upload: 10MB in <30 seconds
- Report Generation: <5 seconds (PDF/Excel)
- Concurrent Users: 100+ without degradation

### Monitoring Tools
- **Laravel Telescope**: Development debugging
- **New Relic**: APM for production
- **Sentry**: Error tracking
- **CloudWatch/Grafana**: Infrastructure monitoring

---

## Future Architecture Enhancements

### API-First Approach
- **RESTful API**: Expose all features via API
- **Mobile App**: iOS/Android native apps
- **Webhooks**: Real-time event notifications

### Microservices (Long-term)
Potential service separation:
1. **Auth Service**: User authentication and authorization
2. **Project Service**: Project and task management
3. **Document Service**: File storage and contract generation
4. **Notification Service**: Email, SMS, WhatsApp
5. **Reporting Service**: Analytics and report generation

### Event-Driven Architecture
- **Event Bus**: Apache Kafka or AWS EventBridge
- **Event Sourcing**: Complete audit trail
- **CQRS**: Separate read and write models

---

## Conclusion

The AMTAR Engineering System is built on a solid monolithic foundation with clear separation of concerns, robust security, and a scalable service layer. The architecture supports current business needs while providing a clear path for future growth and enhancement.

**Key Strengths:**
- Clean MVC architecture with service layer
- Comprehensive RBAC security model
- Flexible integration architecture
- Well-documented codebase
- Extensive testing infrastructure

**Recommended Next Steps:**
1. Implement comprehensive caching
2. Set up database replication
3. Migrate to cloud storage for files
4. Implement API layer for mobile apps
5. Enhance monitoring and alerting

---

**Document Version**: 1.0
**Last Updated**: January 2026
**Maintained By**: Development Team
