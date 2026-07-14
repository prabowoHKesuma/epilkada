<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Verifikasi Remote | e-PILKADA</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>.login-page { background-color: #f4f6f9; }</style>
</head>
<body class="hold-transition login-page">

<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Status</b>Verifikasi</a>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <span class="h5 font-weight-bold">Pantau Pengajuan Anda</span>
        </div>
        <div class="card-body">
            <p class="login-box-msg text-muted">Masukkan kode verifikasi 8 digit yang Anda terima saat pengajuan.</p>

            @if($errors->has('verification_code'))
                <div class="alert alert-danger alert-dismissible text-sm">
                    <i class="icon fas fa-ban mr-1"></i> {{ $errors->first('verification_code') }}
                </div>
            @endif

            <form action="{{ route('remote.status.check') }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" 
                           name="verification_code" 
                           maxlength="8" 
                           class="form-control form-control-lg text-center font-monospace" 
                           style="letter-spacing: 4px; text-transform: uppercase; font-weight: bold;" 
                           placeholder="XXXXXXXX" 
                           required autofocus autocomplete="off">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-search"></span></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-check-circle mr-2"></i> Cek Status Sekarang
                </button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>