<?php

namespace App\Services;

use App\Models\User;

class NavigationService
{
    protected ?User $user;

    public function __construct(?User $user = null)
    {
        $this->user = $user;
    }

    public function getNavigationItems(): array
    {
        return array_filter([
            $this->getMainSection(),
            $this->getServicesSection(),
            $this->getResourcesSection(),
            $this->getTemplatesSection(),
            $this->getSystemSection(),
        ]);
    }

    protected function getMainSection(): ?array
    {
        $items = array_filter([
            $this->menuItem('Dashboard', 'admin/dashboard', 'fa-home', 'admin.dashboard'),
            $this->canAccessProjects() ? $this->menuItem('Projects', 'admin/projects*', 'fa-project-diagram', 'admin.projects.index') : null,
            $this->canAccessClients() ? $this->menuItem('Clients', 'admin/clients*', 'fa-users', 'admin.clients.index') : null,
            $this->canAccessContracts() ? $this->menuItem('Contracts', 'admin/contracts*', 'fa-file-contract', 'admin.contracts.index') : null,
            $this->canAccessFiles() ? $this->menuItem('Files', 'admin/files*', 'fa-folder', 'admin.files.index') : null,
            $this->canAccessTasks() ? $this->menuItem('Tasks', 'admin/tasks*', 'fa-tasks', 'admin.tasks.index') : null,
            $this->canAccessMilestones() ? $this->menuItem('Milestones', 'admin/milestones*', 'fa-flag-checkered', 'admin.milestones.index') : null,
        ]);

        return count($items) > 0 ? ['title' => null, 'items' => $items] : null;
    }

    protected function getServicesSection(): ?array
    {
        if (!$this->canAccessServices()) {
            return null;
        }

        $items = [
            $this->menuItem('Overview', 'admin/services', 'fa-cogs', 'admin.services.index'),
            $this->menuItem('Main Services', 'admin/services/main*', 'fa-building', 'admin.services.main.index'),
            $this->menuItem('Sub Services', 'admin/services/sub*', 'fa-code-branch', 'admin.services.sub.index'),
            $this->menuItem('Packages', 'admin/services/packages*', 'fa-box', 'admin.services.packages.index'),
            $this->menuItem('Stages', 'admin/services/stages*', 'fa-layer-group', 'admin.services.stages.index'),
            $this->menuItem('All Services', 'admin/services/services*', 'fa-wrench', 'admin.services.services.index'),
        ];

        return ['title' => 'Services', 'items' => $items];
    }

    protected function getResourcesSection(): ?array
    {
        if (!$this->canAccessResources()) {
            return null;
        }

        $items = [
            $this->menuItem('Skills', 'admin/skills*', 'fa-graduation-cap', 'admin.skills.index'),
            $this->menuItem('Task Templates', 'admin/task-templates*', 'fa-clipboard-list', 'admin.task-templates.index'),
            $this->menuItem('Pending Reviews', 'admin/tasks/pending-reviews*', 'fa-clipboard-check', 'admin.tasks.pending-reviews'),
        ];

        return ['title' => 'Resources', 'items' => $items];
    }

    protected function getTemplatesSection(): ?array
    {
        if (!$this->canAccessTemplates()) {
            return null;
        }

        $items = [
            $this->menuItem('Notifications', 'admin/notification-templates*', 'fa-bell', 'admin.notification-templates.index'),
            $this->menuItem('Emails', 'admin/email-templates*', 'fa-envelope', 'admin.email-templates.index'),
            $this->menuItem('Messages', 'admin/message-templates*', 'fa-comments', 'admin.message-templates.index'),
        ];

        return ['title' => 'Templates', 'items' => $items];
    }

    protected function getSystemSection(): ?array
    {
        if (!$this->canAccessSystem()) {
            return null;
        }

        $items = [
            $this->menuItem('Users', 'admin/users*', 'fa-users-cog', 'admin.users.index'),
            $this->menuItem('Roles', 'admin/roles*', 'fa-user-shield', 'admin.roles.index'),
            $this->menuItem('Document Types', 'admin/document-types*', 'fa-file-alt', 'admin.document-types.index'),
            $this->menuItem('Settings', 'admin/settings*', 'fa-cog', 'admin.settings.index'),
        ];

        return ['title' => 'System', 'items' => $items];
    }

    protected function menuItem(string $label, string $activePattern, string $icon, string $route): array
    {
        return [
            'label' => $label,
            'route' => $route,
            'icon' => $icon,
            'activePattern' => $activePattern,
        ];
    }

    protected function isAdmin(): bool
    {
        return $this->user && $this->user->hasRole('administrator');
    }

    protected function isProjectManager(): bool
    {
        return $this->user && $this->user->hasRole('project-manager');
    }

    protected function isEngineer(): bool
    {
        return $this->user && $this->user->hasRole('engineer');
    }

    protected function canAccessProjects(): bool
    {
        // All roles can view projects (engineers view-only)
        return $this->isAdmin() || $this->isProjectManager() || $this->isEngineer();
    }

    protected function canAccessClients(): bool
    {
        // Only admin and PM can access clients
        return $this->isAdmin() || $this->isProjectManager();
    }

    protected function canAccessContracts(): bool
    {
        // Only admin and PM can access contracts
        return $this->isAdmin() || $this->isProjectManager();
    }

    protected function canAccessFiles(): bool
    {
        // All roles can access files
        return $this->isAdmin() || $this->isProjectManager() || $this->isEngineer();
    }

    protected function canAccessTasks(): bool
    {
        // All roles can access tasks (engineers see only their own)
        return $this->isAdmin() || $this->isProjectManager() || $this->isEngineer();
    }

    protected function canAccessMilestones(): bool
    {
        // Only admin and PM can access milestones
        return $this->isAdmin() || $this->isProjectManager();
    }

    protected function canAccessServices(): bool
    {
        // Only admin can manage services
        return $this->isAdmin();
    }

    protected function canAccessResources(): bool
    {
        // Only admin can manage resources (skills, templates)
        return $this->isAdmin();
    }

    protected function canAccessTemplates(): bool
    {
        // Only admin can manage communication templates
        return $this->isAdmin();
    }

    protected function canAccessSystem(): bool
    {
        // Only admin can access system settings
        return $this->isAdmin();
    }
}
