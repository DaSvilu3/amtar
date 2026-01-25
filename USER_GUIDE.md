# AMTAR Engineering System - User Guide

## Table of Contents
1. [Getting Started](#getting-started)
2. [Administrator Guide](#administrator-guide)
3. [Project Manager Guide](#project-manager-guide)
4. [Engineer Guide](#engineer-guide)
5. [Common Features](#common-features)
6. [FAQ](#faq)

---

## Getting Started

### Accessing the System

1. **Login URL**: https://your-domain.com/login
2. **Credentials**: Contact your administrator for login credentials
3. **First Login**:
   - You will be prompted to verify your email
   - Check your inbox for verification link
   - Click the link to activate your account

### Password Reset

If you forget your password:
1. Click "Forgot Password?" on login page
2. Enter your email address
3. Check your email for reset link
4. Click the link and set a new password
5. Password requirements:
   - Minimum 8 characters
   - At least one uppercase letter
   - At least one number
   - At least one special character

### Dashboard Overview

Upon login, you'll see the main dashboard with:
- **Statistics Cards**: Quick overview of projects, tasks, clients
- **Recent Activity**: Latest updates and notifications
- **Charts**: Visual representation of workload and progress
- **Quick Actions**: Shortcuts to common tasks

---

## Administrator Guide

Administrators have full access to all system features and settings.

### 1. User Management

#### Creating New Users

**Path**: Dashboard → Users → Create User

1. Click "Create User" button
2. Fill in required fields:
   - Full Name
   - Email Address
   - Phone Number
   - Department (optional)
3. Assign Role:
   - **Administrator**: Full system access
   - **Project Manager**: Manage projects and tasks
   - **Engineer**: View and work on assigned tasks
4. Assign Skills (for Engineers):
   - Select relevant technical skills
   - Set proficiency level (Beginner, Intermediate, Expert)
   - Enter years of experience
5. Click "Create User"

#### Managing User Permissions

1. Go to Users → [User Name] → Edit
2. Under "Roles & Permissions" section:
   - Select/deselect roles
   - View role-specific permissions
3. Save changes

#### Deactivating Users

1. Navigate to user profile
2. Click "Deactivate Account"
3. Confirm action
4. User will no longer be able to login
5. To reactivate: Click "Activate Account"

### 2. Client Management

#### Adding New Clients

**Path**: Dashboard → Clients → Create Client

1. Click "Create Client" button
2. Fill in client information:
   - **Basic Info**: Name, contact person, email, phone
   - **Address**: Full address details
   - **Tax Info**: Tax ID, Commercial Registration
   - **Type**: Individual, Company, Government
3. Upload documents (optional):
   - CR certificate
   - Tax registration
   - Trade license
4. Click "Create Client"

#### Managing Client Contracts

1. Go to client profile
2. Click "Contracts" tab
3. View all contracts for this client
4. Generate new contract from project

### 3. Service Management

#### Creating Service Packages

**Path**: Dashboard → Services → Service Packages → Create

Service packages are pre-defined collections of services for common project types.

1. Click "Create Package"
2. Enter package details:
   - **Name**: e.g., "Residential Villa Design"
   - **Description**: Brief description
   - **Main Service**: Select category (Architecture, Structural, MEP, etc.)
   - **Sub Service**: Select subcategory
3. Add services to package:
   - Click "Add Service"
   - Select service stage (Concept, Detailed, Construction, etc.)
   - Select specific service
   - Set default hours
   - Add task templates
4. Set pricing (optional)
5. Click "Save Package"

#### Managing Service Stages

**Path**: Dashboard → Services → Service Stages

Service stages organize the workflow:
1. **Concept Design**: Initial sketches and ideas
2. **Detailed Design**: Technical drawings
3. **Construction Documents**: Shop drawings, specifications
4. **Site Supervision**: Site visits, quality control

To modify stages:
1. Click on stage name
2. Edit stage details
3. Reorder stages using drag-drop
4. Save changes

### 4. Project Management

#### Creating Projects

**Path**: Dashboard → Projects → Create Project

Two methods available:

**Method 1: Quick Create (Using Package)**
1. Select client from dropdown
2. Choose service package (pre-configured)
3. Set project dates and budget
4. Click "Generate Project"
5. System automatically:
   - Creates project services
   - Generates tasks from templates
   - Auto-assigns tasks to engineers
   - Creates contract draft

**Method 2: Custom Create (Manual Setup)**
1. Select client
2. Choose services individually:
   - Select main service category
   - Select sub-service
   - Pick specific services needed
3. Set project details:
   - Project name and number
   - Start and end dates
   - Budget
   - Location
   - Project manager
4. Add milestones (optional)
5. Click "Create Project"
6. Manually add tasks later

#### Contract Generation

Contracts are automatically generated when creating projects with the "Generate Contract" option.

**To manually generate a contract:**
1. Go to project page
2. Click "Generate Contract" button
3. Select contract template
4. Review auto-populated fields:
   - Client information
   - Project scope
   - Service list with costs
   - Payment schedule
   - Terms and conditions
5. Customize if needed
6. Generate in format:
   - **DOCX**: Editable Word document
   - **PDF**: Final signed copy
7. Download or email to client

**Contract Templates:**
- Location: `storage/app/contracts/templates/`
- Use placeholders: `{{client_name}}`, `{{project_name}}`, `{{contract_value}}`, etc.
- Supports Arabic and English

#### Bulk Task Operations

**Path**: Project → Tasks → Select Tasks

1. Check multiple tasks using checkboxes
2. Click "Bulk Actions" dropdown
3. Available actions:
   - **Auto-Assign**: Assign all selected tasks to best engineers
   - **Update Status**: Change status for all selected
   - **Update Priority**: Set priority level
   - **Export**: Download task list as Excel/PDF
   - **Delete**: Remove selected tasks

### 5. Report Generation

**Path**: Dashboard → Reports

Available reports:
1. **Project Summary**: Overview of all projects with status, budget, progress
2. **Task Status Report**: Task completion rates by status, priority, assignee
3. **Team Performance**: Engineer productivity, completed tasks, hours logged
4. **Financial Report**: Budget vs actual costs, invoicing, payments
5. **Client Activity**: Projects per client, contract values, project history
6. **Milestone Tracking**: Milestone completion status and delays

**Generating Reports:**
1. Select report type
2. Apply filters:
   - Date range
   - Project/Client
   - Status
   - Assignee
3. Choose export format:
   - **PDF**: Professional formatted report
   - **Excel**: Data spreadsheet for analysis
4. Click "Generate Report"
5. Download file

**Scheduling Reports:**
1. Click "Schedule Report" button
2. Select frequency (Daily, Weekly, Monthly)
3. Choose recipients (email addresses)
4. Set delivery time
5. Save schedule

### 6. System Settings

**Path**: Dashboard → Settings

#### Email Templates

Customize automated email notifications:
1. Navigate to Settings → Email Templates
2. Select template to edit:
   - Task Assignment
   - Task Review Submitted
   - Task Approved/Rejected
   - Password Reset
   - Welcome Email
3. Edit subject and body
4. Use placeholders:
   - `{{user_name}}`: Recipient name
   - `{{task_title}}`: Task name
   - `{{project_name}}`: Project name
   - `{{assigned_by}}`: Who assigned the task
5. Preview email
6. Save template

#### Third-Party Integrations

**WhatsApp Integration:**
1. Go to Settings → Integrations → WhatsApp
2. Enter Twilio credentials:
   - Account SID
   - Auth Token
   - WhatsApp number
3. Click "Test Connection"
4. Enable integration
5. Configure notification triggers:
   - Task assigned
   - Task due soon
   - Project milestone reached

**SMS Integration:**
1. Similar setup to WhatsApp
2. Enter Twilio SMS credentials
3. Set SMS notification preferences

**Email Integration:**
1. Configure SMTP settings (if not using system default)
2. Test email delivery
3. Enable/disable email notifications

#### Document Types

**Path**: Settings → Document Types

Define document categories for file uploads:
1. Click "Add Document Type"
2. Enter details:
   - Name (e.g., "Shop Drawings", "Site Photos")
   - Category
   - Required/Optional flag
   - Allowed file types
3. Set retention policy
4. Save

### 7. Backup & Maintenance

**Manual Backup:**
```bash
# SSH into server
php artisan backup:run

# Download backup files from:
# storage/app/backups/
```

**Database Cleanup:**
```bash
# Clean old notifications (older than 90 days)
php artisan notifications:cleanup

# Clean soft-deleted records
php artisan model:prune
```

---

## Project Manager Guide

Project Managers can create and manage projects, assign tasks, and monitor team performance.

### 1. Creating and Managing Projects

**Path**: Dashboard → Projects → Create

See [Administrator Guide → Project Management](#4-project-management) for project creation steps.

**Project Manager Responsibilities:**
- Define project scope and timeline
- Create project milestones
- Assign tasks to engineers
- Monitor project progress
- Review completed tasks
- Manage project budget
- Communicate with clients

### 2. Task Assignment

#### Manual Assignment

1. Go to project page
2. Click "Tasks" tab
3. Click task to open details
4. Click "Assign" button
5. View **Assignment Suggestions**:
   - System shows ranked engineers based on:
     - Skill match (25%)
     - Years of experience (20%)
     - Current availability (20%)
     - Current workload (15%)
     - Past performance (10%)
     - Stage specialization (10%)
6. Review suggestions showing:
   - Engineer name and email
   - Match score
   - Available hours this month
   - Current workload
   - Matching skills
7. Click on engineer to assign
8. Confirm assignment

#### Auto-Assignment

**For Single Task:**
1. Open task details
2. Click "Auto-Assign" button
3. System automatically assigns to best engineer
4. Engineer receives notification

**For Multiple Tasks:**
1. Select tasks using checkboxes
2. Click "Bulk Actions" → "Auto-Assign"
3. System processes all tasks
4. Review assignment results

**Auto-Assignment Algorithm:**
The system considers:
- **Required Skills**: Matches task requirements with engineer skills
- **Availability**: Only assigns to engineers with capacity
- **Workload Balance**: Distributes work evenly
- **Urgency**: Prioritizes urgent tasks
- **Stage Expertise**: Assigns based on service stage experience

### 3. Task Review Workflow

When engineers submit tasks for review:

1. **Notification**: You receive email/system notification
2. **Review Task**:
   - Navigate to Dashboard → Tasks → Pending Reviews
   - Click on task to open
3. **Review Deliverables**:
   - Check uploaded files
   - Verify task completion
   - Review progress notes
4. **Decision**:
   - **Approve**:
     - Click "Approve" button
     - Add review notes (optional)
     - Task marked as completed
     - Engineer receives approval notification
   - **Reject**:
     - Click "Reject" button
     - Provide detailed feedback (required)
     - Task returns to "In Progress"
     - Engineer receives revision request with feedback

### 4. Team Workload Management

**Path**: Dashboard → Team Workload

View team capacity and assignments:
1. **Workload Chart**: Visual representation of each engineer's hours
2. **Availability Table**:
   - Engineer name
   - Current tasks
   - Hours allocated
   - Available hours
   - Capacity percentage
3. **Filters**:
   - By skill
   - By service stage
   - By department
4. **Actions**:
   - Reassign overloaded tasks
   - Balance workload across team
   - Identify available engineers

**Best Practices:**
- Keep engineers at 80-90% capacity
- Leave buffer for urgent tasks
- Monitor overdue task trends
- Regular one-on-ones to discuss workload

### 5. Milestone Tracking

**Creating Milestones:**
1. Go to project page
2. Click "Milestones" tab
3. Click "Create Milestone"
4. Fill in details:
   - Title (e.g., "Design Approval", "Construction Start")
   - Description
   - Due date
   - Payment percentage (if applicable)
5. Link tasks to milestone:
   - Select tasks that must be completed
   - Set milestone as dependency
6. Save milestone

**Monitoring Milestones:**
- Dashboard shows milestone progress
- Color-coded status:
  - **Green**: On track
  - **Yellow**: At risk (within 7 days of due date)
  - **Red**: Overdue
- Completion percentage based on linked tasks

### 6. Client Communication

**Project Notes:**
1. Go to project page
2. Click "Notes" tab
3. Add note:
   - Select type (Comment, Reminder, Meeting)
   - Enter content
   - Attach files if needed
   - Tag team members for visibility
   - Pin important notes to top
4. Notes are visible to all project team members

**Sharing Project Updates:**
1. Generate project status report (see Reports section)
2. Download as PDF
3. Email to client
4. Or use automated monthly reports (configure in Settings)

---

## Engineer Guide

Engineers receive task assignments, work on deliverables, and submit for review.

### 1. Viewing Assigned Tasks

**Path**: Dashboard → My Tasks

Dashboard shows:
- **Pending Tasks**: Not yet started
- **In Progress**: Currently working on
- **Pending Review**: Submitted for approval
- **Completed**: Approved tasks

**Task Filters:**
- By project
- By priority (Urgent, High, Normal, Low)
- By due date
- By status

**Task List View:**
- Task title
- Project name
- Priority badge
- Status badge
- Progress bar
- Due date (red if overdue)
- Estimated hours

**Kanban View:**
- Drag tasks between columns to update status
- Columns: Pending, In Progress, Review, Completed
- Visual workflow management

### 2. Working on Tasks

#### Starting a Task

1. Click on task to open details
2. Review task information:
   - Description and requirements
   - Linked service and stage
   - Estimated hours
   - Due date
   - Dependencies (tasks that must be completed first)
3. Check dependencies:
   - If task is blocked (red warning), dependencies must be completed first
   - Contact assigned engineers if needed
4. Click "Start Working" button
5. Task status changes to "In Progress"

#### Updating Progress

1. Open task details
2. Click "Update Progress" button
3. Set completion percentage (0-100%)
4. Enter actual hours spent
5. Add progress notes (optional)
6. Save

**Best Practices:**
- Update progress daily
- Be realistic with estimates
- Document any blockers in notes
- Update actual hours accurately for future planning

#### Uploading Files

1. Open task details
2. Click "Upload Document" button
3. Select file(s):
   - **Supported formats**: PDF, DWG, DXF, JPG, PNG, DOCX, XLSX, ZIP
   - **Maximum size**: 10MB per file
4. Add description (optional but recommended)
5. Click "Upload"

**File Organization:**
- Name files clearly: `ProjectName_Drawing_Rev1.dwg`
- Include revision numbers
- Add descriptions explaining what the file contains
- Delete obsolete files

### 3. Submitting for Review

When task is 100% complete:

1. Ensure all deliverables are uploaded
2. Review your work one final time
3. Click "Submit for Review" button
4. Select reviewer (or leave blank for auto-assignment)
5. Add submission notes explaining:
   - What was completed
   - Any deviations from requirements
   - Questions or clarifications needed
6. Click "Submit"
7. Task moves to "Review" status
8. You'll receive notification when reviewed

### 4. Handling Review Feedback

If task is rejected:

1. Check email notification with feedback
2. Open task to see reviewer comments
3. Address all feedback points
4. Make necessary revisions
5. Update files as needed
6. When complete, submit for review again

If task is approved:
- Task marked as completed
- Contributes to your performance metrics
- Released capacity for new tasks

### 5. Managing Workload

**Path**: Dashboard → My Workload

View your capacity:
- **Available Hours**: Remaining hours this month
- **Allocated Hours**: Hours assigned to current tasks
- **Capacity**: Percentage utilized
- **Overdue Tasks**: Tasks past due date

**Managing Overload:**
- If at 100% capacity, communicate with Project Manager
- Prioritize urgent and high-priority tasks
- Update realistic completion dates
- Don't accept ad-hoc work without PM approval

### 6. Time Tracking

**Why Track Time:**
- Helps improve future estimates
- Shows productivity metrics
- Identifies inefficiencies
- Justifies billing to clients

**How to Track:**
1. When updating progress, enter actual hours
2. Be honest and accurate
3. Include all time:
   - Design/drawing time
   - Research time
   - Meetings about the task
   - Revisions
4. Exclude:
   - Lunch breaks
   - General meetings
   - Personal time

---

## Common Features

### Notifications

**Notification Types:**
- Task assigned to you
- Task due soon (24 hours before)
- Task review completed (approved/rejected)
- Project milestone reached
- New comment on your task
- System announcements

**Managing Notifications:**
1. Click bell icon in top menu
2. View recent notifications
3. Click notification to go to related item
4. Mark as read or dismiss
5. Configure notification preferences in Profile → Settings:
   - Email notifications On/Off
   - SMS notifications On/Off
   - WhatsApp notifications On/Off
   - Notification frequency

### Profile Settings

**Path**: Top right menu → Profile → Edit

Update your information:
- Name and email
- Phone number
- Avatar/photo
- Password
- Skills and expertise (for engineers)
- Department
- Notification preferences
- Language preference (English/Arabic)

### File Preview

Supported file previews:
- **PDF**: View inline
- **Images** (JPG, PNG): View with zoom
- **Office Docs**: Online viewer
- **CAD Files**: Download to view

To preview:
1. Click on file name
2. File opens in modal viewer
3. Use zoom, rotate controls
4. Download original if needed

### Search

**Global Search** (top menu search bar):
- Search across projects, tasks, clients
- Use keywords, project numbers, client names
- Recent searches shown in dropdown
- Click result to navigate

**Advanced Search:**
- Click "Advanced" in search dropdown
- Filter by:
  - Type (Project, Task, Client, File)
  - Date range
  - Status
  - Assigned to
- Save search criteria for reuse

---

## FAQ

### General

**Q: How do I reset my password?**
A: Click "Forgot Password?" on the login page, enter your email, and follow the link sent to your inbox.

**Q: Can I access the system on mobile?**
A: Yes, the system is responsive and works on tablets and smartphones. For best experience, use a tablet or desktop.

**Q: How do I change the language to Arabic?**
A: Go to Profile → Settings → Language → Select Arabic. The interface will reload in Arabic.

### Tasks

**Q: What if I can't complete a task by the due date?**
A: Contact your Project Manager immediately. Update the task with current progress and explain the delay. Don't wait until the due date passes.

**Q: Can I reassign a task to someone else?**
A: Only Project Managers and Administrators can reassign tasks. If you need help, discuss with your PM.

**Q: What does "Blocked" status mean?**
A: The task has dependencies that aren't complete yet. Check the "Dependencies" section to see which tasks must be finished first.

### Files

**Q: Why can't I upload my file?**
A: Common reasons:
- File exceeds 10MB limit (compress or split it)
- File type not allowed (convert to supported format)
- Storage quota reached (contact administrator)

**Q: Can I delete a file I uploaded?**
A: Yes, if you're the uploader or task assignee. Click the trash icon next to the file. Files deleted permanently cannot be recovered.

### Projects

**Q: How do I add a new service to an existing project?**
A: Project Managers can edit the project and add services from the Services tab. This may require client approval if it affects the contract.

**Q: Can I see projects I'm not assigned to?**
A: Engineers can only see projects where they have assigned tasks. Project Managers and Administrators can see all projects.

### Technical

**Q: The page isn't loading. What should I do?**
A:
1. Refresh the page (Ctrl+F5 or Cmd+Shift+R)
2. Clear browser cache
3. Try a different browser
4. Contact IT support if issue persists

**Q: Can I use the system offline?**
A: No, an internet connection is required. Ensure stable connectivity before starting work.

---

## Getting Help

### Support Channels

- **Email**: support@amtar.om
- **Phone**: +968 XXXXXXXX (9 AM - 5 PM, Sun-Thu)
- **In-App**: Click "Help" button → "Contact Support"

### Training Resources

- **Video Tutorials**: Available in Help → Training Videos
- **User Manual**: Download PDF from Help → Documentation
- **Release Notes**: View latest updates in Help → What's New

---

## Best Practices

### For All Users

1. **Log out** when leaving your desk
2. **Update your status** regularly if working remotely
3. **Check notifications** at least twice daily
4. **Keep profile information** current
5. **Report bugs** or issues immediately

### For Project Managers

1. **Set realistic deadlines** with team input
2. **Review tasks weekly** to catch delays early
3. **Balance workload** across the team
4. **Provide clear task descriptions** to avoid confusion
5. **Communicate changes** to clients promptly

### For Engineers

1. **Start tasks early** - don't wait until the due date
2. **Ask questions** if requirements are unclear
3. **Submit quality work** the first time to avoid revisions
4. **Keep files organized** with clear naming
5. **Track time accurately** for better planning

---

**Version**: 1.0
**Last Updated**: January 2026
**For the latest version**, visit the system Help center.
