<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .map-preview {
            min-height: 320px;
            border: 1px solid #e3e6f0;
            border-radius: .35rem;
            overflow: hidden;
            background: #f8f9fc;
        }

        .map-preview iframe {
            width: 100%;
            height: 320px;
            border: 0;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="sidebar-brand-text mx-3">HadirinAja</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>User</span>
                </a>
            </li>

            <li class="nav-item active">
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
                            <h1 class="h3 mb-1 text-gray-800">Settings</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb bg-transparent p-0 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Settings</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                    @endif

                    <form id="settingsForm" method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-xl-5 col-lg-6">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Rule Presensi</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Hari Kerja</label>
                                            <div class="form-row">
                                                <div class="col">
                                                    <select name="workday_start" class="form-control">
                                                        @foreach ($days as $value => $label)
                                                        <option value="{{ $value }}" @selected(old('workday_start', $setting->workday_start) == $value)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-auto d-flex align-items-center">-</div>
                                                <div class="col">
                                                    <select name="workday_end" class="form-control">
                                                        @foreach ($days as $value => $label)
                                                        <option value="{{ $value }}" @selected(old('workday_end', $setting->workday_end) == $value)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Jam Kerja</label>
                                            <div class="form-row">
                                                <div class="col">
                                                    <input id="workStartTime" type="time" name="work_start_time" class="form-control" value="{{ old('work_start_time', substr($setting->work_start_time, 0, 5)) }}" required>
                                                </div>
                                                <div class="col-auto d-flex align-items-center">-</div>
                                                <div class="col">
                                                    <input id="workEndTime" type="time" name="work_end_time" class="form-control" value="{{ old('work_end_time', substr($setting->work_end_time, 0, 5)) }}" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Batas Terlambat</label>
                                            <input id="lateDeadline" type="time" name="late_deadline" class="form-control" value="{{ old('late_deadline', substr($setting->late_deadline, 0, 5)) }}" required>
                                            <div id="lateDeadlineFeedback" class="invalid-feedback">
                                                Batas terlambat harus berada di antara jam mulai dan jam selesai kerja.
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Radius Presensi (meter)</label>
                                            <input type="number" name="radius_meters" class="form-control" min="1" max="10000" value="{{ old('radius_meters', $setting->radius_meters) }}" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-save mr-1"></i>
                                            Simpan Settings
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-7 col-lg-6">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Lokasi Presensi</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Lokasi</label>
                                            <div class="input-group">
                                                <input id="locationName" type="text" name="location_name" class="form-control" value="{{ old('location_name', $setting->location_name) }}" placeholder="GK-1 Itera" required>
                                                <div class="input-group-append">
                                                    <button id="currentLocationButton" type="button" class="btn btn-outline-primary">
                                                        <i class="fas fa-location-crosshairs mr-1"></i>
                                                        Lokasi Saya
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Database hanya menyimpan titik koordinat. Preview maps di bawah memakai latitude dan longitude yang diinput.</small>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Latitude</label>
                                                <input id="latitude" type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude', $setting->latitude) }}" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Longitude</label>
                                                <input id="longitude" type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude', $setting->longitude) }}" required>
                                            </div>
                                        </div>

                                        <div class="map-preview">
                                            <iframe id="mapFrame" title="Preview lokasi presensi" loading="lazy"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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
        const locationInput = document.getElementById('locationName');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const mapFrame = document.getElementById('mapFrame');
        const settingsForm = document.getElementById('settingsForm');
        const workStartInput = document.getElementById('workStartTime');
        const workEndInput = document.getElementById('workEndTime');
        const lateDeadlineInput = document.getElementById('lateDeadline');

        function refreshMap() {
            const latitude = latitudeInput.value;
            const longitude = longitudeInput.value;
            const query = latitude && longitude ? `${latitude},${longitude}` : locationInput.value;

            mapFrame.src = `https://www.google.com/maps?q=${encodeURIComponent(query || 'Indonesia')}&output=embed`;
        }

        function useCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Browser tidak mendukung geolocation.');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    latitudeInput.value = position.coords.latitude.toFixed(7);
                    longitudeInput.value = position.coords.longitude.toFixed(7);
                    refreshMap();
                },
                () => {
                    alert('Gagal mengambil lokasi dari browser.');
                }
            );
        }

        function validateLateDeadline() {
            const workStart = workStartInput.value;
            const workEnd = workEndInput.value;
            const lateDeadline = lateDeadlineInput.value;
            const isValid = workStart && workEnd && lateDeadline && lateDeadline >= workStart && lateDeadline <= workEnd;

            lateDeadlineInput.min = workStart || '';
            lateDeadlineInput.max = workEnd || '';

            if (!isValid) {
                lateDeadlineInput.classList.add('is-invalid');
                lateDeadlineInput.setCustomValidity('Batas terlambat harus berada di antara jam mulai dan jam selesai kerja.');
                return false;
            }

            lateDeadlineInput.classList.remove('is-invalid');
            lateDeadlineInput.setCustomValidity('');
            return true;
        }

        document.getElementById('currentLocationButton').addEventListener('click', useCurrentLocation);
        latitudeInput.addEventListener('input', refreshMap);
        longitudeInput.addEventListener('input', refreshMap);
        locationInput.addEventListener('input', refreshMap);
        workStartInput.addEventListener('input', validateLateDeadline);
        workEndInput.addEventListener('input', validateLateDeadline);
        lateDeadlineInput.addEventListener('input', validateLateDeadline);
        settingsForm.addEventListener('submit', (event) => {
            if (!validateLateDeadline()) {
                event.preventDefault();
                lateDeadlineInput.reportValidity();
            }
        });
        document.addEventListener('DOMContentLoaded', () => {
            refreshMap();
            validateLateDeadline();
        });
    </script>
</body>

</html>
