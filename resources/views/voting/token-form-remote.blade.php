<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bilik Suara Remote | e-PILKADA</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>.login-page { background-color: #f4f6f9; }</style>
</head>
<body class="hold-transition login-page">

<div class="login-box" style="width: 420px;">
    <div class="login-logo mb-3">
        <a href="#"><b>Bilik</b>Suara <span class="text-success font-weight-bold">Remote</span></a>
    </div>

    <div class="card card-outline card-success shadow">
        <div class="card-header text-center py-3">
            <span class="h5 font-weight-bold">Verifikasi Token Online</span>
        </div>
        <div class="card-body p-4">
            <p class="login-box-msg text-muted text-sm mb-3">Masukkan token yang Anda dapatkan setelah persetujuan verifikasi wajah/KTP.</p>

            @if($errors->has('token'))
                <div class="alert alert-danger alert-dismissible text-sm">
                    <i class="icon fas fa-ban mr-1"></i> {{ $errors->first('token') }}
                </div>
            @endif

            <form action="{{ route('voting.verify.remote') }}" method="POST">
                @csrf
                <div class="input-group mb-4">
                    <input type="text" 
                           name="token" 
                           class="form-control form-control-lg text-center font-monospace border-success" 
                           placeholder="XXXXXXXXXX" 
                           maxlength="10" 
                           style="letter-spacing: 5px; font-weight: bold; text-transform: uppercase; font-size: 22px;"
                           required 
                           autofocus 
                           autocomplete="off">
                    <div class="input-group-append">
                        <div class="input-group-text bg-light"><span class="fas fa-shield-alt text-success"></span></div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success btn-block btn-lg font-weight-bold shadow-sm">
                    <i class="fas fa-laptop-house mr-2"></i> Masuk Bilik Remote
                </button>
            </form>
        </div>
    </div>
    <div class="text-center mt-3 text-muted text-xs">
        Pastikan koneksi internet Anda stabil sebelum memilih.
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>