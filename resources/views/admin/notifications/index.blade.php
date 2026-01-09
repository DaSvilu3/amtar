@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Notifications</h1>
            <p class="text-muted mb-0">
                {{ $unreadCount }} unread notification{{ $unreadCount !== 1 ? 's' : '' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($unreadCount > 0)
            <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                <i class="fas fa-check-double me-2"></i>Mark All Read
            </button>
            @endif
            @if($notifications->count() > 0)
            <button class="btn btn-outline-danger" onclick="clearAllNotifications()">
                <i class="fas fa-trash me-2"></i>Clear All
            </button>
            @endif
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $isUnread = is_null($notification->read_at);
                            $colorClass = $data['color'] ?? 'primary';
                        @endphp
                        <div class="list-group-item notification-item {{ $isUnread ? 'bg-light' : '' }}" data-id="{{ $notification->id }}">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon bg-{{ $colorClass }} bg-opacity-10 text-{{ $colorClass }} me-3">
                                    <i class="fas {{ $data['icon'] ?? 'fa-bell' }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 {{ $isUnread ? 'fw-bold' : '' }}">
                                                {{ $data['title'] ?? 'Notification' }}
                                                @if($isUnread)
                                                    <span class="badge bg-primary ms-2" style="font-size: 10px;">NEW</span>
                                                @endif
                                            </h6>
                                            <p class="text-muted mb-2">{{ $data['message'] ?? '' }}</p>
                                            @if(isset($data['project_name']))
                                                <small class="text-muted">
                                                    <i class="fas fa-project-diagram me-1"></i>{{ $data['project_name'] }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                                            <div class="notification-actions mt-2">
                                                @if(isset($data['url']))
                                                    <a href="{{ $data['url'] }}" class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                @if($isUnread)
                                                    <button class="btn btn-sm btn-outline-success" onclick="markAsRead('{{ $notification->id }}')" title="Mark as read">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification('{{ $notification->id }}')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Notifications</h5>
                    <p class="text-muted">You're all caught up! No notifications to display.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.notification-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.notification-item {
    transition: background-color 0.2s ease;
    border-left: 3px solid transparent;
}

.notification-item.bg-light {
    border-left-color: var(--primary-color);
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-actions {
    display: flex;
    gap: 4px;
}

.notification-actions .btn {
    padding: 4px 8px;
    font-size: 12px;
}
</style>

@push('scripts')
<script>
function markAsRead(id) {
    fetch(`{{ url('admin/notifications') }}/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (item) {
                item.classList.remove('bg-light');
                const badge = item.querySelector('.badge');
                if (badge) badge.remove();
                const markBtn = item.querySelector('.btn-outline-success');
                if (markBtn) markBtn.remove();
            }
            updateHeaderBadge(data.unread_count);
        }
    });
}

function markAllAsRead() {
    fetch('{{ route("admin.notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteNotification(id) {
    if (!confirm('Delete this notification?')) return;

    fetch(`{{ url('admin/notifications') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (item) {
                item.style.opacity = '0';
                setTimeout(() => item.remove(), 300);
            }
            updateHeaderBadge(data.unread_count);
        }
    });
}

function clearAllNotifications() {
    if (!confirm('Are you sure you want to clear all notifications? This cannot be undone.')) return;

    fetch('{{ route("admin.notifications.clear-all") }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function updateHeaderBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
}
</script>
@endpush
@endsection
