<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top" data-users-url="{{ route('admin.api.users', absolute: false) }}" data-user-show-base-url="/admin/users" data-user-api-base-url="/admin/api/users" data-user-edit-base-url="/admin/users">
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
                            <h1 class="h3 mb-1 text-gray-800">Admin Dashboard</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb bg-transparent p-0 mb-0">
                                    <li class="breadcrumb-item">Dashboard</li>
                                    <li class="breadcrumb-item active" aria-current="page">User</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar User</h6>
                            <div>
                                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary mr-2">
                                    <i class="fas fa-plus fa-sm mr-1"></i>
                                    Add User
                                </a>
                                <button id="refreshUsers" type="button" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-sync-alt fa-sm mr-1"></i>
                                    Refresh
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif

                            <div id="usersAlert" class="alert alert-danger d-none" role="alert"></div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>NIK</th>
                                            <th>Attendances</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usersTableBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                                Memuat data user...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>
    <script>
        const usersUrl = document.body.dataset.usersUrl;
        const userShowBaseUrl = document.body.dataset.userShowBaseUrl;
        const userApiBaseUrl = document.body.dataset.userApiBaseUrl;
        const userEditBaseUrl = document.body.dataset.userEditBaseUrl;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const usersTableBody = document.getElementById('usersTableBody');
        const usersAlert = document.getElementById('usersAlert');
        const refreshUsersButton = document.getElementById('refreshUsers');

        function escapeHtml(value) {
            return String(value ?? '-')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderLoading() {
            usersTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Memuat data user...
                    </td>
                </tr>
            `;
        }

        function renderEmpty() {
            usersTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        Belum ada data user.
                    </td>
                </tr>
            `;
        }

        function renderUsers(users) {
            if (!users.length) {
                renderEmpty();
                return;
            }

            usersTableBody.innerHTML = users.map((user) => {
                const attendance = Number(user.attendance_percentage ?? 0).toFixed(1);

                return `
                    <tr>
                        <td>${escapeHtml(user.name)}</td>
                        <td>${escapeHtml(user.email)}</td>
                        <td>${escapeHtml(user.nik)}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="mr-3 font-weight-bold">${attendance}%</span>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: ${attendance}%;" aria-valuenow="${attendance}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group" aria-label="User actions">
                                <a href="${userShowBaseUrl}/${user.id}" class="btn btn-info" data-action="show" data-user-id="${user.id}">
                                    <i class="fas fa-eye mr-1"></i>
                                    Show
                                </a>
                                <a href="${userEditBaseUrl}/${user.id}/edit" class="btn btn-warning" data-action="edit" data-user-id="${user.id}">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                                <button type="button" class="btn btn-danger" data-action="delete" data-user-id="${user.id}">
                                    <i class="fas fa-trash mr-1"></i>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        async function loadUsers() {
            renderLoading();
            usersAlert.classList.add('d-none');
            refreshUsersButton.disabled = true;

            try {
                const response = await fetch(usersUrl, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil data user.');
                }

                const result = await response.json();
                renderUsers(result.data ?? []);
            } catch (error) {
                usersTableBody.innerHTML = '';
                usersAlert.textContent = error.message;
                usersAlert.classList.remove('d-none');
            } finally {
                refreshUsersButton.disabled = false;
            }
        }

        async function deleteUser(userId) {
            if (!confirm('Hapus user ini? Data attendance milik user juga akan ikut terhapus.')) {
                return;
            }

            usersAlert.classList.add('d-none');

            try {
                const response = await fetch(`${userApiBaseUrl}/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                if (!response.ok) {
                    throw new Error('Gagal menghapus user.');
                }

                await loadUsers();
            } catch (error) {
                usersAlert.textContent = error.message;
                usersAlert.classList.remove('d-none');
            }
        }

        refreshUsersButton.addEventListener('click', loadUsers);
        usersTableBody.addEventListener('click', (event) => {
            const button = event.target.closest('[data-action="delete"]');

            if (!button) {
                return;
            }

            deleteUser(button.dataset.userId);
        });
        document.addEventListener('DOMContentLoaded', loadUsers);
    </script>
</body>

</html>
