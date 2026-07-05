<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bilik Suara | Pemilu Digital</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        .login-page { background-color: #f4f6f9; }
    </style>
</head>
<body class="hold-transition login-page">

<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Bilik</b>Suara</a>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <span class="h5">Masukkan Kode Token Anda</span>
        </div>
        <div class="card-body">
            <p class="login-box-msg text-muted">Gunakan token yang diberikan petugas untuk mulai memilih.</p>

            @if($errors->has('token'))
                <div class="alert alert-danger alert-dismissible">
                    <i class="icon fas fa-ban"></i> {{ $errors->first('token') }}
                </div>
            @endif

            <form action="{{ route('voting.verify') }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" 
                           name="token" 
                           class="form-control form-control-lg text-center" 
                           placeholder="XXXXXXXX" 
                           maxlength="8" 
                           style="letter-spacing: 5px; font-weight: bold; text-transform: uppercase;"
                           required 
                           autofocus 
                           autocomplete="off">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-key"></span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i> Masuk ke Bilik Suara
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>
</html>