<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Undangan | {{ $election->title }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        .register-page { background-color: #f4f6f9; }
        .custom-file-label { overflow: hidden; }
    </style>
</head>
<body class="hold-transition register-page">

<div class="register-box" style="width: 500px;">
    <div class="register-logo">
        <a href="#"><b>Verifikasi</b>Remote</a>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header text-center">
            <span class="h5 font-weight-bold">Konfirmasi Identitas Undangan</span>
        </div>
        <div class="card-body">
            <!-- Info Pemilih -->
            <div class="callout callout-info py-2 px-3 mb-4 bg-light">
                <small class="text-muted d-block">Pengajuan untuk:</small>
                <strong class="text-primary">{{ $voterNameMasked }}</strong>
                <div class="text-xs text-muted mt-1"><i class="fas fa-vote-yea mr-1"></i> {{ $election->title }}</div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger text-sm">
                    <i class="icon fas fa-exclamation-triangle mr-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('remote.submit.invitation', $invitationToken) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>NIK (16 Digit)</label>
                    <input type="text" name="nik" maxlength="16" class="form-control" placeholder="Masukkan 16 digit NIK untuk konfirmasi" required autofocus>
                </div>

                <div class="form-group">
                    <label>Foto KTP</label>
                    <div class="custom-file">
                        <input type="file" name="ktp_photo" class="custom-file-input" id="ktpInput" accept="image/*" required>
                        <label class="custom-file-label" for="ktpInput">Pilih foto KTP...</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Foto Selfie (Memegang KTP)</label>
                    <div class="custom-file">
                        <input type="file" name="selfie_photo" class="custom-file-input" id="selfieInput" accept="image/*" required>
                        <label class="custom-file-label" for="selfieInput">Pilih foto selfie...</label>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <div class="icheck-primary">
                        <input type="checkbox" id="agreeTerms" name="consent_accepted" value="1" required>
                        <label for="agreeTerms" class="text-sm font-weight-normal">
                            Saya menyetujui data ini digunakan untuk keperluan verifikasi e-Pilkada.
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold">
                    <i class="fas fa-paper-plane mr-2"></i> Ajukan Verifikasi
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input@1.3.4/dist/bs-custom-file-input.min.js"></script>
<script>$(function () { bsCustomFileInput.init(); });</script>
</body>
</html>