<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-user-shield fa-3x text-primary"></i>
                                </div>
                                <h1 class="h4 text-gray-900 mb-2">Admin Login</h1>
                                <p class="text-muted mb-4">Masuk ke dashboard admin HadirinAja</p>
                            </div>

                            @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                {{ $errors->first() }}
                            </div>
                            @endif

                            <form class="user" method="POST" action="{{ route('admin.login.process') }}">
                                @csrf

                                <div class="form-group">
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        class="form-control form-control-user"
                                        value="{{ old('email') }}"
                                        placeholder="Email"
                                        required
                                        autofocus>
                                </div>

                                <div class="form-group">
                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        class="form-control form-control-user"
                                        placeholder="Password"
                                        required>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Login
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>
</body>

</html>
