<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('judul') | {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; background: #f6e6d5; overflow-x: hidden; }

        .left-side { position: sticky; top: 0; align-self: flex-start; height: 100vh; padding: 0; overflow: hidden; background: #f6e6d5; }
        .hero-image { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center; }

        .right-side { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: white; padding: 20px; }
        .login-card { background: white; border-radius: 22px; padding: 30px 42px; max-width: 460px; width: 100%; box-shadow: 0 20px 50px rgba(90, 58, 40, 0.12); }

        .login-header { text-align: center; margin-bottom: 22px; }
        .login-logo-icon { width: 48px; height: 48px; background: #f5e6d3; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-size: 24px; color: #8b5e34; }
        .login-title { font-size: 24px; font-weight: 700; color: #3a2418; margin-bottom: 2px; }
        .login-subtitle { font-size: 14px; color: #8b7355; }
        .login-welcome { font-size: 13px; color: #6b4c3b; margin-top: 2px; }

        .form-label-custom { font-size: 13px; font-weight: 600; color: #3a2418; margin-bottom: 6px; display: block; }
        .input-group-custom { position: relative; margin-bottom: 14px; }
        .input-group-custom .form-control { padding: 11px 14px 11px 42px; border: 1.5px solid #e8d5c4; border-radius: 10px; font-size: 14px; transition: all .3s; background: #faf8f5; }
        .input-group-custom .form-control:focus { border-color: #8b5e34; box-shadow: 0 0 0 3px rgba(139, 94, 52, .1); background: white; }
        .input-group-custom .form-control.is-invalid { border-color: #c8574f; }
        .input-group-custom .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #8b7355; font-size: 17px; }
        .input-group-custom .toggle-password { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #8b7355; cursor: pointer; font-size: 17px; padding: 0; }
        .pesan-error { color: #c8574f; font-size: 12px; margin: -8px 0 12px; }

        .form-check-custom { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #5a3a28; margin-bottom: 18px; }
        .form-check-custom input { width: 16px; height: 16px; accent-color: #8b5e34; }

        .btn-login { width: 100%; padding: 12px; background: #8b5e34; color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; transition: all .3s; margin-top: 4px; }
        .btn-login:hover { background: #6d4a28; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(139, 94, 52, .3); }

        .divider { display: flex; align-items: center; margin: 18px 0 10px; color: #8b7355; font-size: 12px; }
        .divider::before, .divider::after { content: ""; flex: 1; height: 1px; background: #e8d5c4; }
        .divider span { padding: 0 14px; }
        .pindah-halaman { text-align: center; font-size: 13px; }
        .pindah-halaman a { color: #8b5e34; font-weight: 600; text-decoration: none; }
        .pindah-halaman a:hover { text-decoration: underline; }

        .alert-custom { background: #fff3cd; border: 1px solid #ffe69c; color: #856404; padding: 10px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 14px; display: flex; align-items: center; gap: 10px; }

        @media (max-height: 760px) { .login-card { padding: 22px 38px; } .login-logo-icon { display: none; } .login-header { margin-bottom: 14px; } }
        @media (max-width: 992px) { .left-side { display: none; } .right-side { padding: 24px 16px; } }
        @media (max-width: 500px) { .login-card { padding: 26px 22px; } }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row g-0">
            <div class="col-lg-6 left-side">
                <img src="{{ asset('images/login-hero.jpg') }}"
                     alt="Toko Kue: Manisnya Setiap Momen. Kue tart stroberi, cupcake, cookies, dan croissant."
                     class="hero-image">
            </div>

            <div class="col-lg-6 right-side">
                <div class="login-card">
                    <div class="login-header">
                        <div class="login-logo-icon"><i class="bi bi-cake2"></i></div>
                        <h2 class="login-title">Toko Kue</h2>
                        <p class="login-subtitle">@yield('judul-kartu')</p>
                        <p class="login-welcome">@yield('sub-judul')</p>
                    </div>

                    @yield('isi')
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const tampil = input.type === 'password';
            input.type = tampil ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !tampil);
            icon.classList.toggle('bi-eye-slash', tampil);
        }
    </script>
</body>
</html>