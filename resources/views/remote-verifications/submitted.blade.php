<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengajuan Berhasil | e-PILKADA</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>.login-page { background-color: #f4f6f9; }</style>
</head>
<body class="hold-transition login-page">
<div class="login-box" style="width: 450px;">
    <div class="card card-outline card-success">
        <div class="card-body text-center p-5">
            <div class="mb-3">
                <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
            </div>
            <h3 class="font-weight-bold text-success mb-2">Pengajuan Terkirim</h3>
            <p class="text-muted text-sm mb-4">Simpan kode verifikasi di bawah ini untuk mengecek status persetujuan dari petugas:</p>
            
            <div class="bg-light p-3 rounded border mb-4">
                <span class="h2 font-weight-bold text-dark tracking-widest font-monospace" style="letter-spacing: 5px;">
                    {{ $verificationCode }}
                </span>
            </div>

            <div class="alert alert-warning text-xs text-left mb-4">
                <i class="fas fa-exclamation-circle mr-1"></i> <strong>Penting:</strong> Kode ini adalah satu-satunya kunci untuk melihat status dan mendapatkan token voting Anda nantinya.
            </div>

            <a href="{{ route('remote.status.form') }}" class="btn btn-outline-primary btn-block btn-lg font-weight-bold">
                <i class="fas fa-search mr-2"></i> Cek Status Sekarang
            </a>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>