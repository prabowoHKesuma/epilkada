<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Suara Berhasil Mendarat | e-PILKADA</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>.login-page { background-color: #f4f6f9; }</style>
</head>
<body class="hold-transition login-page">

<div class="login-box" style="width: 480px;">
    <div class="card card-outline card-success shadow-lg">
        <div class="card-body text-center p-5">
            
            <div class="mb-4">
                <div style="width: 90px; height: 90px; line-height: 90px; border-radius: 50%; background-color: #e8f5e9; display: inline-block;">
                    <i class="fas fa-check text-success" style="font-size: 48px;"></i>
                </div>
            </div>
            
            <h2 class="font-weight-bold text-success mb-2">Terima Kasih!</h2>
            <h5 class="text-dark font-weight-bold mb-3">Suara Anda Telah Berhasil Dicatat</h5>
            
            <p class="text-muted text-sm mb-4">
                Partisipasi Anda sangat berarti untuk pemilihan ini. Suara Anda telah diamankan dan dienkripsi ke dalam kotak suara digital.
            </p>

            <div class="alert alert-light border text-left text-xs text-muted mb-4 p-3" style="border-radius: 8px;">
                <i class="fas fa-info-circle text-primary mr-1"></i> <strong>Notes:</strong><br>
                Silakan tinggalkan perangkat ini dan serahkan kembali bilik suara kepada petugas atau pemilih dalam antrean berikutnya.
            </div>

            <a href="{{ route('voting.token-form') }}" class="btn btn-outline-success btn-block btn-lg font-weight-bold shadow-sm">
                <i class="fas fa-redo-alt mr-2"></i> Kembali ke Layar Awal
            </a>

        </div>
    </div>
    
    <div class="text-center mt-3 text-muted text-xs">
        Sesi Anda telah diakhiri secara otomatis demi keamanan.
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
    // Mencegah user menekan tombol back ke halaman voting
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
</script>
</body>
</html>