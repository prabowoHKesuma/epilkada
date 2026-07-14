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
<div class="login-box" style="width: 480px;">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <span class="h5 font-weight-bold">Status Verifikasi Remote</span>
        </div>
        <div class="card-body p-4 text-center">
            
            @if($rv->status === 'pending')
                <div class="py-4">
                    <i class="fas fa-clock text-warning mb-3" style="font-size: 56px;"></i>
                    <h4 class="font-weight-bold">Masih Dalam Proses</h4>
                    <p class="text-muted text-sm mt-2">
                        Pengajuan Anda sedang diperiksa oleh petugas verifikator (Tahap 1 & 2). Silakan periksa kembali secara berkala.
                    </p>
                    <a href="{{ route('remote.status.form') }}" class="btn btn-default btn-sm mt-3">
                        <i class="fas fa-sync-alt mr-1"></i> Segarkan Status
                    </a>
                </div>

            @elseif($rv->status === 'rejected')
                <div class="py-4">
                    <i class="fas fa-times-circle text-danger mb-3" style="font-size: 56px;"></i>
                    <h4 class="font-weight-bold text-danger">Pengajuan Ditolak</h4>
                    <div class="alert alert-danger mt-3 text-left text-sm">
                        <strong>Alasan Penolakan:</strong><br>
                        {{ $rv->reject_reason }}
                    </div>
                    <p class="text-muted text-xs">Silakan perbaiki dokumen Anda dan lakukan pengajuan ulang jika sesi verifikasi masih dibuka.</p>
                </div>

            @elseif($rv->status === 'approved')
                @if($token)
                    <div class="py-3">
                        <i class="fas fa-shield-alt text-success mb-3" style="font-size: 56px;"></i>
                        <h4 class="font-weight-bold text-success">Verifikasi Disetujui!</h4>
                        <p class="text-muted text-sm">Berikut adalah token rahasia untuk masuk ke bilik suara digital:</p>
                        
                        <div class="bg-light border border-success rounded p-3 my-3">
                            <span class="h1 font-weight-bold text-success font-monospace" style="letter-spacing: 6px;">
                                {{ $token }}
                            </span>
                        </div>

                        <div class="alert alert-warning text-xs text-left mb-4">
                            <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Perhatian:</strong> Catat atau salin token ini sekarang! Halaman ini <u>tidak akan menampilkannya lagi</u> setelah ditutup. Token berlaku selama 2 hari.
                        </div>

                        <a href="{{ route('voting.token-form.remote') }}" class="btn btn-success btn-block btn-lg font-weight-bold shadow">
                            <i class="fas fa-vote-yea mr-2"></i> Lanjut ke Bilik Suara Remote
                        </a>
                    </div>
                @else
                    <div class="py-4">
                        <i class="fas fa-exclamation-circle text-secondary mb-3" style="font-size: 56px;"></i>
                        <h4 class="font-weight-bold">Token Tidak Tersedia</h4>
                        <p class="text-muted text-sm mt-2">
                            Verifikasi Anda disetujui, namun token sudah pernah diambil atau masa berlakunya telah habis.
                        </p>
                        <p class="text-xs text-muted">Hubungi panitia pemilihan jika Anda merasa ini adalah sebuah kesalahan.</p>
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>