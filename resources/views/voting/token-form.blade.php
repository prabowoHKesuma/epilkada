<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Bilik Suara | e-PILKADA</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>.login-page { background-color: #f4f6f9; }</style>
</head>
<body class="hold-transition login-page">

<div class="login-box" style="width: 420px;">
    <div class="login-logo mb-3">
        <a href="#"><b>Bilik</b>Suara <span class="text-primary font-weight-bold">TPS</span></a>
    </div>

    <div class="card card-outline card-primary shadow">
        <div class="card-header text-center py-3">
            <span class="h5 font-weight-bold">Masukkan Kode Token TPS</span>
        </div>
        <div class="card-body p-4">
            <p class="login-box-msg text-muted text-sm mb-3">Gunakan token 8 digit yang diberikan oleh petugas KPPS untuk mulai memilih.</p>

            @if($errors->has('token'))
                <div class="alert alert-danger alert-dismissible text-sm">
                    <i class="icon fas fa-ban mr-1"></i> {{ $errors->first('token') }}
                </div>
            @endif

            <form action="{{ route('voting.verify') }}" method="POST">
                @csrf
                <div class="input-group mb-4">
                    <input type="text" 
                           name="token" 
                           class="form-control form-control-lg text-center font-monospace" 
                           placeholder="XXXXXXXX" 
                           maxlength="8" 
                           style="letter-spacing: 6px; font-weight: bold; text-transform: uppercase; font-size: 22px;"
                           required 
                           autofocus 
                           autocomplete="off">
                    <div class="input-group-append">
                        <div class="input-group-text bg-light"><span class="fas fa-key text-primary"></span></div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow-sm">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk ke Bilik Suara
                </button>
            </form>
        </div>
    </div>
    <div class="text-center mt-3 text-muted text-xs">
        &copy; 2026 e-PILKADA System. All rights reserved.
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>