<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di e-PILKADA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        .login-container {
            min-height: 100vh;
        }
        .brand-title {
            font-size: 3rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -1px;
        }
        .card-login {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            max-width: 420px;
            width: 100%;
        }
        .btn-primary-custom {
            background-color: #0d6efd;
            border: none;
            padding: 10px;
            font-weight: 600;
        }
        .btn-primary-custom:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row login-container align-items-center">
        
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center text-center p-5">
            <img src="{{ asset('/images/logo.png') }}" alt="Mascot e-PILKADA" class="img-fluid mb-4" style="max-height: 600px; object-fit: contain;">
        </div>

        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center p-5 bg-white h-100">
            
            <div class="text-center mb-4">
                <h5 class="text-primary fw-semibold mb-1">Selamat Datang di e-PILKADA</h5>
                <small class="text-muted">Aplikasi PILKADA Online</small>
            </div>

            <div class="card card-login p-4">
                <h5 class="fw-bold mb-4 text-dark">Login Panitia</h5>
                
                <form action="{{ route('login') }}" method="POST">
                    @csrf <div class="mb-3">
                        <label for="username" class="form-label text-muted small fw-bold">Username</label>
                        <input type="text" class="form-control bg-light py-2" id="username" name="username" placeholder="admin" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label text-muted small fw-bold">Password</label>
                        <input type="password" class="form-control bg-light py-2" id="password" name="password" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 text-white rounded-3">Masuk</button>
                </form>
            </div>

            <div class="mt-5 text-center">
                <small class="text-muted">&copy; 2026 JUMPQ Innovations, PT. Khalifa Andara Solusindo Group</small>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>