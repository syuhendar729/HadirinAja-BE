<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail User</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <style>
        .profile-photo {
            width: 180px;
            height: 180px;
            border-radius: 1rem;
            object-fit: cover;
            background: #eef2f7;
        }

        .profile-placeholder {
            width: 180px;
            height: 180px;
            border-radius: 1rem;
            display: grid;
            place-items: center;
            background: #eef2f7;
            color: #858796;
            font-size: 4rem;
        }

        .stat-card {
            border-left: .25rem solid #4e73df;
        }

        .proof-image {
            max-height: 360px;
            object-fit: contain;
            background: #f8f9fc;
        }

        .calendar-legend {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem 1rem;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            font-size: .875rem;
            color: #5a5c69;
        }

        .legend-color {
            width: .875rem;
            height: .875rem;
            border-radius: .2rem;
            margin-right: .4rem;
        }

        #attendanceCalendar .fc-day-sat,
        #attendanceCalendar .fc-day-sun {
            background: #eef0f4;
        }

        #attendanceCalendar .fc-event {
            border: 0;
            font-weight: 700;
            padding: 2px 4px;
        }

        #attendanceCalendar .fc-toolbar-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #4e73df;
        }
    </style>
</head>

<body
    id="page-top"
    data-user-detail-url="{{ route('admin.api.users.show', $user) }}"
    data-permission-base-url="{{ url('/admin/api/users/' . $user->id . '/permissions') }}">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="sidebar-brand-text mx-3">HadirinAja</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item active">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>User</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.settings.edit') }}">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item d-flex align-items-center mr-3">
                            <span class="text-gray-600 small">{{ Auth::user()->name }}</span>
                        </li>
                        <li class="nav-item mr-2">
                            <a href="{{ route('admin.settings.edit') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-cog fa-sm mr-1"></i>
                                Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sign-out-alt fa-sm mr-1"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h1 class="h3 mb-1 text-gray-800">Detail User</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb bg-transparent p-0 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">User</a>
                                    </li>
                                    <li id="breadcrumbUserName" class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div id="detailAlert" class="alert alert-danger d-none" role="alert"></div>

                    <div class="row">
                        <div class="col-xl-4 col-lg-5 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-body text-center">
                                    <div id="profilePhotoWrapper" class="mb-4 d-flex justify-content-center">
                                        <div class="profile-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>

                                    <h5 id="profileName" class="font-weight-bold text-gray-900 mb-1">{{ $user->name }}</h5>
                                    <p id="profileEmail" class="text-muted mb-4">{{ $user->email }}</p>

                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning mr-2">
                                            <i class="fas fa-edit mr-1"></i>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user? Their attendance data will also be deleted.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash mr-1"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8 col-lg-7 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Informasi User</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-4">Name</dt>
                                                <dd id="userName" class="col-sm-8">-</dd>

                                                <dt class="col-sm-4">NPW</dt>
                                                <dd id="userNik" class="col-sm-8">-</dd>

                                                <dt class="col-sm-4">Email</dt>
                                                <dd id="userEmail" class="col-sm-8">-</dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-4">Position</dt>
                                                <dd id="userPosition" class="col-sm-8">-</dd>

                                                <dt class="col-sm-4">Notelp</dt>
                                                <dd id="userPhone" class="col-sm-8">-</dd>

                                                <dt class="col-sm-4">Alamat</dt>
                                                <dd id="userAddress" class="col-sm-8">-</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-xl-3 mb-4">
                                    <div class="card stat-card shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">PRESENT</div>
                                            <div id="presentCount" class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-3 mb-4">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">LATE</div>
                                            <div id="lateCount" class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-3 mb-4">
                                    <div class="card border-left-danger shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">ABSENT</div>
                                            <div id="absentCount" class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-3 mb-4">
                                    <div class="card border-left-info shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">LEAVE</div>
                                            <div id="leaveCount" class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12 mb-4">
                                    <div class="card shadow h-100">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Weekday</div>
                                            <div id="totalWeekday" class="h4 mb-0 font-weight-bold text-gray-800">0</div>
                                            <p class="small text-muted mt-2 mb-0">Calculated from the user start date until today, excluding weekends.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Leave Requests</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="permissionRequestsTableBody">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                                Loading leave requests...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Attendance Calendar</h6>
                            <div class="calendar-legend">
                                <span class="legend-item"><span class="legend-color" style="background: #1cc88a;"></span>PRESENT</span>
                                <span class="legend-item"><span class="legend-color" style="background: #858796;"></span>Weekend</span>
                                <span class="legend-item"><span class="legend-color" style="background: #f6c23e;"></span>LEAVE</span>
                                <span class="legend-item"><span class="legend-color" style="background: #e74a3b;"></span>ABSENT</span>
                                <span class="legend-item"><span class="legend-color" style="background: #006b3c;"></span>LATE</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="attendanceCalendar"></div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>HadirinAja Admin</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <div class="modal fade" id="proofModal" tabindex="-1" role="dialog" aria-labelledby="proofModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="proofModalLabel">Leave Proof</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="proofContent" class="text-center mb-3"></div>
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-2">Notes</div>
                            <p id="permissionNotes" class="mb-0">-</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="rejectPermissionButton" type="button" class="btn btn-danger">
                        <i class="fas fa-times mr-1"></i>
                        Reject
                    </button>
                    <button id="approvePermissionButton" type="button" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i>
                        Approve
                    </button>
                </div>
            </div>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        const detailUrl = document.body.dataset.userDetailUrl;
        const permissionBaseUrl = document.body.dataset.permissionBaseUrl;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        let permissionRequests = [];
        let calendar = null;
        let selectedPermission = null;

        function escapeHtml(value) {
            return String(value || '-')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function setText(id, value) {
            document.getElementById(id).textContent = value || '-';
        }

        function formatDate(value) {
            if (!value) {
                return '-';
            }

            if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                const [year, month, day] = value.split('-');
                return `${day}/${month}/${year}`;
            }

            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
            }).format(date);
        }

        function renderStatusBadge(status) {
            const labels = {
                pending: 'Pending',
                approved: 'Approved',
                rejected: 'Rejected',
            };

            const classes = {
                pending: 'badge badge-warning',
                approved: 'badge badge-success',
                rejected: 'badge badge-danger',
            };

            return `<span class="${classes[status] || 'badge badge-secondary'}">${labels[status] || '-'}</span>`;
        }

        function renderPhoto(user) {
            const wrapper = document.getElementById('profilePhotoWrapper');

            if (!user.profile_picture) {
                wrapper.innerHTML = `
                    <div class="profile-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                `;
                return;
            }

            wrapper.innerHTML = `<img class="profile-photo" src="${escapeHtml(user.profile_picture)}" alt="Foto ${escapeHtml(user.name)}">`;
        }

        function renderProof(permission) {
            const proofContent = document.getElementById('proofContent');
            const notes = document.getElementById('permissionNotes');
            const approveButton = document.getElementById('approvePermissionButton');
            const rejectButton = document.getElementById('rejectPermissionButton');

            approveButton.classList.add('d-none');
            rejectButton.classList.add('d-none');

            if (!permission) {
                proofContent.innerHTML = '<p class="text-muted mb-0">No leave proof yet.</p>';
                notes.textContent = '-';
                return;
            }

            notes.textContent = permission.notes || '-';

            if (permission.permission_status === 'pending') {
                approveButton.classList.remove('d-none');
                rejectButton.classList.remove('d-none');
            }

            if (!permission.proof_url) {
                proofContent.innerHTML = '<p class="text-muted mb-0">Leave proof is not available yet.</p>';
                return;
            }

            proofContent.innerHTML = `
                <a href="${escapeHtml(permission.proof_url)}" target="_blank" rel="noopener">
                    <img class="img-fluid rounded proof-image" src="${escapeHtml(permission.proof_url)}" alt="Leave proof">
                </a>
            `;
        }

        function renderPermissionRequests(requests) {
            const tableBody = document.getElementById('permissionRequestsTableBody');

            if (!requests.length) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            No leave requests yet.
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = requests.map((permission) => {
                const isPending = permission.permission_status === 'pending';

                return `
                    <tr>
                        <td>${formatDate(permission.date || permission.created_at)}</td>
                        <td>${renderStatusBadge(permission.permission_status)}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Leave request actions">
                                <button type="button" class="btn btn-info" data-action="detail" data-permission-id="${permission.id}">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </button>
                                ${isPending ? `
                                    <button type="button" class="btn btn-success" data-action="approve" data-permission-id="${permission.id}">
                                        <i class="fas fa-check mr-1"></i>
                                        Approve
                                    </button>
                                    <button type="button" class="btn btn-danger" data-action="reject" data-permission-id="${permission.id}">
                                        <i class="fas fa-times mr-1"></i>
                                        Reject
                                    </button>
                                ` : `
                                    <button type="button" class="btn btn-warning" disabled>
                                        <i class="fas fa-edit mr-1"></i>
                                        Edit
                                    </button>
                                `}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function attendanceEventColor(status) {
            const colors = {
                PRESENT: '#1cc88a',
                LEAVE: '#f6c23e',
                ABSENT: '#e74a3b',
                LATE: '#006b3c',
            };

            return colors[status] || '#4e73df';
        }

        function renderCalendar(attendances) {
            const calendarElement = document.getElementById('attendanceCalendar');
            const events = attendances.map((attendance) => {
                const color = attendanceEventColor(attendance.status);

                return {
                    id: attendance.id,
                    title: attendance.status,
                    start: attendance.date,
                    allDay: true,
                    backgroundColor: color,
                    textColor: attendance.status === 'LEAVE' ? '#1f2937' : '#ffffff',
                    extendedProps: {
                        notes: attendance.notes || '-',
                    },
                };
            });

            if (!calendar) {
                calendar = new FullCalendar.Calendar(calendarElement, {
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    locale: 'en',
                    firstDay: 0,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth',
                    },
                    dayMaxEvents: true,
                    events,
                    eventDidMount: function(info) {
                        info.el.title = `${info.event.title} - ${info.event.extendedProps.notes}`;
                    },
                });
                calendar.render();
                return;
            }

            calendar.removeAllEvents();
            calendar.addEventSource(events);
        }

        function findPermission(permissionId) {
            return permissionRequests.find((permission) => String(permission.id) === String(permissionId));
        }

        function openPermissionDetail(permissionId) {
            selectedPermission = findPermission(permissionId);
            renderProof(selectedPermission);
            $('#proofModal').modal('show');
        }

        function renderDetail(user) {
            permissionRequests = user.permission_requests || [];

            document.getElementById('breadcrumbUserName').textContent = user.name || '-';
            document.getElementById('profileName').textContent = user.name || '-';
            document.getElementById('profileEmail').textContent = user.email || '-';

            setText('userName', user.name);
            setText('userNik', user.nik);
            setText('userEmail', user.email);
            setText('userPosition', user.position);
            setText('userPhone', user.phone);
            setText('userAddress', user.alamat);

            document.getElementById('presentCount').textContent = user.total.present;
            document.getElementById('lateCount').textContent = user.total.late;
            document.getElementById('absentCount').textContent = user.total.absent;
            document.getElementById('leaveCount').textContent = user.total.leave;
            document.getElementById('totalWeekday').textContent = user.total_weekday;

            renderPhoto(user);
            renderPermissionRequests(permissionRequests);
            renderCalendar(user.calendar_attendances || []);
        }

        async function loadDetail() {
            const alert = document.getElementById('detailAlert');
            alert.classList.add('d-none');

            try {
                const response = await fetch(detailUrl, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil detail user.');
                }

                const result = await response.json();
                renderDetail(result.data);
            } catch (error) {
                alert.textContent = error.message;
                alert.classList.remove('d-none');
            }
        }

        async function updatePermission(permissionId, action) {
            const url = `${permissionBaseUrl}/${permissionId}/${action}`;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (!response.ok) {
                throw new Error('Failed to update leave request.');
            }

            $('#proofModal').modal('hide');
            await loadDetail();
        }

        document.getElementById('approvePermissionButton').addEventListener('click', async () => {
            if (selectedPermission) {
                await updatePermission(selectedPermission.id, 'approve');
            }
        });

        document.getElementById('rejectPermissionButton').addEventListener('click', async () => {
            if (selectedPermission) {
                await updatePermission(selectedPermission.id, 'reject');
            }
        });

        document.getElementById('permissionRequestsTableBody').addEventListener('click', async (event) => {
            const button = event.target.closest('[data-action]');

            if (!button) {
                return;
            }

            const action = button.dataset.action;
            const permissionId = button.dataset.permissionId;

            if (action === 'detail') {
                openPermissionDetail(permissionId);
                return;
            }

            if (action === 'approve' || action === 'reject') {
                await updatePermission(permissionId, action);
            }
        });

        document.addEventListener('DOMContentLoaded', loadDetail);
    </script>
</body>

</html>
