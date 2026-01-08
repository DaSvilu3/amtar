# AMTAR Office Management System - Test Cases

Complete testing guide with use cases, step-by-step instructions, and success criteria.

---

## Table of Contents

1. [Authentication & Authorization](#1-authentication--authorization)
2. [User Management](#2-user-management)
3. [Role Management](#3-role-management)
4. [Client Management](#4-client-management)
5. [Project Management](#5-project-management)
6. [Task Management](#6-task-management)
7. [Milestone Management](#7-milestone-management)
8. [Contract Management](#8-contract-management)
9. [File Management](#9-file-management)
10. [Service Configuration](#10-service-configuration)
11. [Document Types](#11-document-types)
12. [Integrations](#12-integrations)
13. [Analytics & Reports](#13-analytics--reports)
14. [Approvals Workflow](#14-approvals-workflow)
15. [Role-Based Access Control](#15-role-based-access-control)

---

## 1. Authentication & Authorization

### TC-1.1: Admin Login

**Use Case**: Administrator logs into the system

**Pre-conditions**:
- Admin user exists (created via `php artisan admin:create`)
- Application is running

**Test Steps**:
1. Navigate to http://localhost:8000/admin/login
2. Enter email: `admin@amtar.om`
3. Enter password: `Admin123!`
4. Click "Login" button

**Success Criteria**:
- [ ] User is redirected to `/admin/dashboard`
- [ ] Dashboard shows admin-specific content (system stats, user counts)
- [ ] Navigation sidebar shows all menu sections (System, Services, Templates, etc.)
- [ ] User name appears in top-right corner

---

### TC-1.2: Invalid Login Attempt

**Use Case**: User enters incorrect credentials

**Test Steps**:
1. Navigate to http://localhost:8000/admin/login
2. Enter email: `wrong@email.com`
3. Enter password: `wrongpassword`
4. Click "Login" button

**Success Criteria**:
- [ ] Error message displays: "These credentials do not match our records"
- [ ] User remains on login page
- [ ] No session is created

---

### TC-1.3: Logout

**Use Case**: User logs out of the system

**Pre-conditions**: User is logged in

**Test Steps**:
1. Click user dropdown in top-right corner
2. Click "Logout"

**Success Criteria**:
- [ ] User is redirected to login page
- [ ] Session is destroyed
- [ ] Accessing `/admin/dashboard` redirects to login

---

## 2. User Management

### TC-2.1: Create New User

**Use Case**: Admin creates a new user account

**Pre-conditions**: Logged in as Administrator

**Test Steps**:
1. Navigate to System > Users
2. Click "Add User" button
3. Fill in form:
   - Name: `John Engineer`
   - Email: `john@amtar.om`
   - Password: `Password123!`
   - Role: `Engineer`
4. Click "Create User"

**Success Criteria**:
- [ ] Success message: "User created successfully"
- [ ] New user appears in users list
- [ ] New user can login with credentials

---

### TC-2.2: Edit User

**Use Case**: Admin modifies user details

**Test Steps**:
1. Navigate to System > Users
2. Click "Edit" on a user
3. Change name to `John Senior Engineer`
4. Click "Update User"

**Success Criteria**:
- [ ] Success message: "User updated successfully"
- [ ] Updated name appears in users list

---

### TC-2.3: Deactivate User

**Use Case**: Admin deactivates a user account

**Test Steps**:
1. Navigate to System > Users
2. Click "Edit" on a user
3. Uncheck "Active" checkbox
4. Click "Update User"

**Success Criteria**:
- [ ] User status shows as "Inactive"
- [ ] Deactivated user cannot login

---

## 3. Role Management

### TC-3.1: View Roles

**Use Case**: View available system roles

**Test Steps**:
1. Navigate to System > Roles

**Success Criteria**:
- [ ] Three roles visible: Administrator, Project Manager, Engineer
- [ ] Each role shows associated permissions
- [ ] Permission counts are displayed

---

### TC-3.2: Edit Role Permissions

**Use Case**: Modify permissions for a role

**Test Steps**:
1. Navigate to System > Roles
2. Click "Edit" on Project Manager role
3. Add/remove a permission
4. Click "Update Role"

**Success Criteria**:
- [ ] Success message: "Role updated successfully"
- [ ] Permission changes are reflected
- [ ] Users with that role gain/lose access accordingly

---

## 4. Client Management

### TC-4.1: Create Client

**Use Case**: Add a new client to the system

**Pre-conditions**: Logged in as Admin or Project Manager

**Test Steps**:
1. Navigate to Clients
2. Click "Add Client"
3. Fill in form:
   - Company Name: `Al Bustan Properties`
   - Contact Person: `Ahmed Al Busaidi`
   - Email: `ahmed@albustan.om`
   - Phone: `+968 99123456`
   - Address: `Muscat, Oman`
4. Click "Create Client"

**Success Criteria**:
- [ ] Success message: "Client created successfully"
- [ ] Client appears in clients list
- [ ] Client details page shows all information

---

### TC-4.2: View Client Details

**Use Case**: View complete client information

**Test Steps**:
1. Navigate to Clients
2. Click on a client name

**Success Criteria**:
- [ ] Client details page loads
- [ ] Shows contact information
- [ ] Shows associated projects
- [ ] Shows associated contracts
- [ ] Shows uploaded documents

---

### TC-4.3: Edit Client

**Use Case**: Update client information

**Test Steps**:
1. Navigate to Clients
2. Click "Edit" on a client
3. Update phone number
4. Click "Update Client"

**Success Criteria**:
- [ ] Success message: "Client updated successfully"
- [ ] Updated information appears in client details

---

## 5. Project Management

### TC-5.1: Create Project with Wizard

**Use Case**: Create a new project using the step-by-step wizard

**Pre-conditions**: At least one client exists

**Test Steps**:
1. Navigate to Projects
2. Click "New Project"
3. **Step 1 - Basic Info**:
   - Project Name: `Villa Renovation - Al Khuwair`
   - Select Client: `Al Bustan Properties`
   - Project Number: `PRJ-2024-001`
   - Start Date: Today
   - End Date: 3 months from now
   - Click "Next"
4. **Step 2 - Services**:
   - Select service package or individual services
   - Click "Next"
5. **Step 3 - Review**:
   - Verify all information
   - Click "Create Project"

**Success Criteria**:
- [ ] Project is created successfully
- [ ] Redirected to project details page
- [ ] Tasks are auto-generated from service templates
- [ ] Project appears in projects list with correct status

---

### TC-5.2: View Project Details

**Use Case**: View complete project information

**Test Steps**:
1. Navigate to Projects
2. Click on a project name

**Success Criteria**:
- [ ] Overview tab shows project summary
- [ ] Tasks & Milestones tab shows generated tasks
- [ ] Documents tab shows uploaded files
- [ ] Services tab shows selected services
- [ ] Progress percentage is calculated correctly

---

### TC-5.3: Update Project Status

**Use Case**: Change project status

**Test Steps**:
1. Navigate to project details
2. Click "Edit Project"
3. Change status from "Active" to "On Hold"
4. Click "Update Project"

**Success Criteria**:
- [ ] Status badge changes color
- [ ] Status change is logged
- [ ] Project list reflects new status

---

### TC-5.4: Filter Projects

**Use Case**: Filter projects by status and client

**Test Steps**:
1. Navigate to Projects list
2. Use status filter dropdown
3. Use client filter dropdown

**Success Criteria**:
- [ ] Projects list updates based on filter selection
- [ ] Filter selections are maintained
- [ ] "Clear Filters" resets the list

---

## 6. Task Management

### TC-6.1: View Tasks List

**Use Case**: View all tasks with filters

**Test Steps**:
1. Navigate to Tasks

**Success Criteria**:
- [ ] Tasks are listed with status, priority, assignee
- [ ] Filter options are available (status, priority, assignee)
- [ ] Sorting works correctly
- [ ] Pagination works if many tasks

---

### TC-6.2: Create Task

**Use Case**: Manually create a new task

**Test Steps**:
1. Navigate to Tasks
2. Click "Create Task"
3. Fill in form:
   - Title: `Review architectural drawings`
   - Project: Select a project
   - Priority: High
   - Estimated Hours: 8
   - Due Date: 1 week from now
4. Click "Create Task"

**Success Criteria**:
- [ ] Task is created with "Pending" status
- [ ] Task appears in task list
- [ ] Task appears on project tasks tab

---

### TC-6.3: Auto-Assign Task

**Use Case**: System automatically assigns task to best-fit engineer

**Pre-conditions**:
- Task exists without assignee
- Engineers exist with matching skills

**Test Steps**:
1. Navigate to task details
2. Click "Auto Assign" button
3. Review suggestion
4. Click "Confirm Assignment"

**Success Criteria**:
- [ ] System suggests an engineer based on skills, workload, availability
- [ ] Assignment score is displayed
- [ ] After confirmation, task shows assigned user
- [ ] Notification is sent to assigned user (check logs)

---

### TC-6.4: Update Task Status

**Use Case**: Engineer updates task status

**Test Steps**:
1. Login as Engineer
2. Navigate to Tasks (shows only assigned tasks)
3. Click on a task
4. Change status from "Pending" to "In Progress"
5. Click "Update Status"

**Success Criteria**:
- [ ] Status changes immediately
- [ ] Progress is logged
- [ ] PM/Admin can see the status change

---

### TC-6.5: Submit Task for Review

**Use Case**: Engineer completes task and submits for review

**Test Steps**:
1. Login as Engineer
2. Open an "In Progress" task
3. Click "Submit for Review"
4. Add completion notes

**Success Criteria**:
- [ ] Task status changes to "Pending Review"
- [ ] Task appears in Approvals page for PM/Admin
- [ ] Timestamp is recorded

---

### TC-6.6: Batch Auto-Assign Tasks

**Use Case**: Assign multiple tasks at once

**Pre-conditions**: Multiple unassigned tasks exist

**Test Steps**:
1. Navigate to Tasks
2. Select multiple tasks using checkboxes
3. Click "Assign Selected"
4. Confirm batch assignment

**Success Criteria**:
- [ ] All selected tasks are assigned
- [ ] Assignment results are displayed
- [ ] Each task has appropriate assignee

---

## 7. Milestone Management

### TC-7.1: Create Milestone

**Use Case**: Create a project milestone

**Test Steps**:
1. Navigate to Milestones
2. Click "Add Milestone"
3. Fill in form:
   - Name: `Design Phase Complete`
   - Project: Select project
   - Due Date: 1 month from now
   - Description: `All design deliverables approved`
4. Click "Create Milestone"

**Success Criteria**:
- [ ] Milestone is created
- [ ] Appears on project timeline
- [ ] Status shows as "Pending"

---

### TC-7.2: Complete Milestone

**Use Case**: Mark milestone as complete

**Test Steps**:
1. Navigate to milestone details
2. Click "Mark Complete"
3. Add completion notes

**Success Criteria**:
- [ ] Status changes to "Completed"
- [ ] Completion date is recorded
- [ ] Project progress updates

---

## 8. Contract Management

### TC-8.1: Create Contract

**Use Case**: Create a new contract for a project

**Test Steps**:
1. Navigate to Contracts
2. Click "Create Contract"
3. Fill in form:
   - Title: `Interior Design Services Agreement`
   - Client: Select client
   - Project: Select project
   - Contract Value: 50000
   - Start Date: Today
   - End Date: 6 months from now
4. Click "Create Contract"

**Success Criteria**:
- [ ] Contract is created
- [ ] Contract number is auto-generated
- [ ] Appears in contracts list and project details

---

### TC-8.2: Print Contract

**Use Case**: Generate printable contract document

**Test Steps**:
1. Navigate to contract details
2. Click "Print Contract"

**Success Criteria**:
- [ ] Print-friendly page opens
- [ ] Company information is displayed correctly
- [ ] Phone number shows actual value (not placeholder)
- [ ] All contract details are formatted properly
- [ ] Print dialog opens when clicking print button

---

### TC-8.3: Update Contract Status

**Use Case**: Change contract status through lifecycle

**Test Steps**:
1. Open contract details
2. Click "Edit"
3. Change status from "Draft" to "Pending Signature"
4. Save changes

**Success Criteria**:
- [ ] Status badge updates
- [ ] Status history is maintained
- [ ] Appropriate actions available for each status

---

## 9. File Management

### TC-9.1: Upload File

**Use Case**: Upload a document to a project

**Test Steps**:
1. Navigate to project details
2. Go to Documents tab
3. Click "Upload Document"
4. Select file (PDF, image, or document)
5. Select document type
6. Click "Upload"

**Success Criteria**:
- [ ] File uploads successfully
- [ ] Progress indicator shows during upload
- [ ] File appears in documents list
- [ ] File type icon is correct
- [ ] File can be downloaded

---

### TC-9.2: Preview File

**Use Case**: Preview uploaded document

**Test Steps**:
1. Navigate to Files
2. Click on a file (PDF or image)

**Success Criteria**:
- [ ] Preview opens in modal or new tab
- [ ] Image files display correctly
- [ ] PDF files display in browser viewer

---

### TC-9.3: Delete File

**Use Case**: Remove a file from the system

**Test Steps**:
1. Navigate to file details
2. Click "Delete"
3. Confirm deletion

**Success Criteria**:
- [ ] Confirmation prompt appears
- [ ] File is removed from list
- [ ] File is removed from storage

---

## 10. Service Configuration

### TC-10.1: View Service Catalog

**Use Case**: Browse available services

**Test Steps**:
1. Navigate to Services > Overview

**Success Criteria**:
- [ ] All service categories are displayed
- [ ] Main services, sub-services, packages visible
- [ ] Service counts are accurate

---

### TC-10.2: Create Service Package

**Use Case**: Create a new service package

**Test Steps**:
1. Navigate to Services > Packages
2. Click "Add Package"
3. Fill in form:
   - Name: `Complete Interior Design`
   - Select included services
   - Set package price
4. Click "Create Package"

**Success Criteria**:
- [ ] Package is created
- [ ] Included services are linked
- [ ] Package available in project wizard

---

### TC-10.3: Manage Task Templates

**Use Case**: Create a task template for a service

**Test Steps**:
1. Navigate to Resources > Task Templates
2. Click "Add Template"
3. Fill in form:
   - Title: `Site Survey`
   - Service: Select service
   - Estimated Hours: 4
   - Required Skills: Select skills
4. Click "Create Template"

**Success Criteria**:
- [ ] Template is created
- [ ] Template generates tasks when service is used in project

---

## 11. Document Types

### TC-11.1: Create Document Type

**Use Case**: Add a new document type for file categorization

**Test Steps**:
1. Navigate to System > Document Types
2. Click "Add Document Type"
3. Fill in form:
   - Name: `Site Photos`
   - Entity Type: Project
   - Required: No
   - Allowed File Types: JPEG, PNG
4. Click "Create Document Type"

**Success Criteria**:
- [ ] Document type is created
- [ ] Appears in document upload dropdown
- [ ] File type restrictions are enforced

---

### TC-11.2: View Document Type Details

**Use Case**: View document type configuration

**Test Steps**:
1. Navigate to System > Document Types
2. Click on a document type name

**Success Criteria**:
- [ ] Details page shows all configuration
- [ ] Shows file count using this type
- [ ] Edit and Delete buttons available

---

## 12. Integrations

### TC-12.1: Configure Email Integration

**Use Case**: Set up email notification integration

**Test Steps**:
1. Navigate to System > Integrations
2. Click "Add Integration"
3. Select Type: Email
4. Fill in SMTP details:
   - Host: `smtp.example.com`
   - Port: `587`
   - Username/Password
5. Click "Create Integration"

**Success Criteria**:
- [ ] Integration is saved
- [ ] Test connection button works
- [ ] Status shows as Active/Inactive

---

### TC-12.2: Configure WhatsApp Integration

**Use Case**: Set up WhatsApp business messaging

**Test Steps**:
1. Navigate to System > Integrations
2. Click "Add Integration"
3. Select Type: WhatsApp
4. Fill in API credentials
5. Click "Create Integration"

**Success Criteria**:
- [ ] Integration is saved
- [ ] Configuration is encrypted/masked

---

## 13. Analytics & Reports

### TC-13.1: View Analytics Dashboard

**Use Case**: View system analytics and metrics

**Test Steps**:
1. Navigate to Analytics > Analytics

**Success Criteria**:
- [ ] Charts load correctly
- [ ] Task completion trends display
- [ ] Contract value distribution shows
- [ ] Team performance table populated
- [ ] Date filters work

---

### TC-13.2: Generate Report

**Use Case**: Generate a project status report

**Test Steps**:
1. Navigate to Analytics > Reports
2. Select "Project Status Report"
3. Set date range: Last 30 days
4. Select output format: PDF
5. Click "Generate Report"

**Success Criteria**:
- [ ] Report generates without errors
- [ ] PDF downloads correctly
- [ ] Report contains accurate data
- [ ] Formatting is professional

---

### TC-13.3: Custom Report

**Use Case**: Create a custom report

**Test Steps**:
1. Navigate to Analytics > Reports
2. Click "Custom Report"
3. Select data fields to include
4. Set filters and date range
5. Generate report

**Success Criteria**:
- [ ] Custom report generates
- [ ] Selected fields are included
- [ ] Filters are applied correctly

---

## 14. Approvals Workflow

### TC-14.1: View Pending Approvals

**Use Case**: Admin/PM views tasks awaiting approval

**Test Steps**:
1. Navigate to Analytics > Approvals

**Success Criteria**:
- [ ] Pending Tasks tab shows tasks in "Pending Review" status
- [ ] Pending Milestones tab shows milestones awaiting approval
- [ ] History tab shows past approvals/rejections

---

### TC-14.2: Approve Task

**Use Case**: Approve a completed task

**Pre-conditions**: Task in "Pending Review" status

**Test Steps**:
1. Navigate to Approvals
2. Find pending task
3. Click "Approve" button
4. Confirm approval

**Success Criteria**:
- [ ] Task status changes to "Completed"
- [ ] Approval is logged with timestamp and approver
- [ ] Task moves to History tab
- [ ] Assigned engineer is notified (check logs)

---

### TC-14.3: Reject Task

**Use Case**: Reject a task and return for rework

**Test Steps**:
1. Navigate to Approvals
2. Find pending task
3. Click "Reject" button
4. Enter rejection reason: `Drawings need revision per client feedback`
5. Confirm rejection

**Success Criteria**:
- [ ] Rejection modal appears
- [ ] Reason is required
- [ ] Task status changes to "Revision Required"
- [ ] Task is reassigned to original engineer
- [ ] Rejection reason is visible in task details

---

## 15. Role-Based Access Control

### TC-15.1: Engineer Access Restrictions

**Use Case**: Verify engineer can only see own tasks

**Test Steps**:
1. Create engineer user
2. Assign tasks to engineer
3. Login as engineer

**Success Criteria**:
- [ ] Navigation shows only: Dashboard, Projects (view), Tasks, Files
- [ ] Tasks list shows only assigned tasks
- [ ] Cannot access: Clients, Contracts, Milestones, System settings
- [ ] Attempting restricted URLs shows 403 error

---

### TC-15.2: Project Manager Access

**Use Case**: Verify PM has appropriate access

**Test Steps**:
1. Create PM user
2. Login as PM

**Success Criteria**:
- [ ] Navigation shows: Dashboard, Projects, Clients, Contracts, Tasks, Milestones, Files, Analytics
- [ ] Cannot access: System section (Users, Roles, Settings)
- [ ] Can create and manage projects
- [ ] Can approve tasks

---

### TC-15.3: Administrator Full Access

**Use Case**: Verify admin has all access

**Test Steps**:
1. Login as administrator

**Success Criteria**:
- [ ] All navigation sections visible
- [ ] Can access all pages
- [ ] Can manage users and roles
- [ ] Can configure system settings

---

## Quick Smoke Test (5 Minutes)

Run these tests to verify basic functionality:

1. [ ] Login as admin
2. [ ] Create a client
3. [ ] Create a project (use wizard)
4. [ ] Verify tasks were auto-generated
5. [ ] Assign a task
6. [ ] Update task status
7. [ ] View analytics page
8. [ ] Logout

If all pass, core functionality is working.

---

## Test Data Cleanup

To reset test data and start fresh:

```bash
php artisan migrate:fresh --seed --seeder=ProductionSeeder
php artisan admin:create --email=admin@amtar.om --password=Admin123!
```

---

## Reporting Issues

When reporting bugs, include:
1. Test case ID (e.g., TC-5.1)
2. Steps to reproduce
3. Expected result
4. Actual result
5. Screenshots if applicable
6. Browser and version
7. Any error messages from browser console or Laravel logs
