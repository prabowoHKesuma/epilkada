<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bilik Suara | {{ $election->title }}</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .candidate-card { 
            transition: all 0.25s ease-in-out; 
            border: 3px solid #dee2e6; 
            cursor: pointer;
            border-radius: 12px;
            overflow: hidden;
        }
        .candidate-card:hover { 
            border-color: #007bff; 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .candidate-card.active { 
            border-color: #28a745 !important; 
            background-color: #f8fff9; 
            box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.25) !important;
        }
        .candidate-img { height: 280px; object-fit: cover; width: 100%; }
        .badge-number { font-size: 1.25rem; px-3; py-2; }
    </style>
</head>
<body class="hold-transition layout-top-nav">

<div class="wrapper">
    <!-- Top Navbar Sederhana sebagai Header -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white border-bottom shadow-sm">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-vote-yea text-primary mr-2"></i>
                <span class="font-weight-bold">e-PILKADA</span> <span class="text-muted text-sm">| Bilik Suara Rahasia</span>
            </span>
            <div class="ml-auto">
                <span class="badge badge-warning px-3 py-2 text-sm"><i class="fas fa-lock mr-1"></i> Sesi Aktif</span>
            </div>
        </div>
    </nav>

    <div class="content-wrapper py-5">
        <div class="container" style="max-width: 1100px;">
            <!-- Header Judul Pemilihan -->
            <div class="text-center mb-5">
                <h1 class="font-weight-bold text-dark mb-2">{{ $election->title }}</h1>
                <p class="text-muted lead">Klik pada kartu foto kandidat pilihan Anda, lalu konfirmasi pilihan di bagian bawah.</p>
            </div>

            <!-- Pastikan Action Route disesuaikan: 
                 Gunakan route('voting.submit') untuk TPS 
                 Gunakan route('voting.submit.remote') untuk Remote -->
            <form action="{{ route('voting.submit.remote') }}" method="POST" id="voteForm">
                @csrf
                
                <!-- Grid Kandidat -->
                <div class="row justify-content-center">
                    @foreach($election->candidates as $candidate)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card candidate-card h-100 shadow-sm" 
                                 onclick="selectCandidate(this)" 
                                 data-id="{{ $candidate->id }}" 
                                 data-name="Kandidat No. {{ $candidate->number_order }} — {{ $candidate->name }}">
                                
                                <input type="radio" name="candidate_id" value="{{ $candidate->id }}" class="d-none" required>
                                
                                <div class="position-relative">
                                    <span class="badge badge-primary position-absolute font-weight-bold shadow" style="top: 15px; left: 15px; font-size: 1.1rem; padding: 8px 15px; border-radius: 20px;">
                                        No. {{ $candidate->number_order }}
                                    </span>

                                    @if($candidate->photo)
                                        <img src="{{ Storage::url($candidate->photo) }}" class="candidate-img" alt="{{ $candidate->name }}">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center candidate-img">
                                            <i class="fas fa-user-tie fa-6x text-secondary"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-body text-center d-flex flex-column justify-content-center p-4">
                                    <h4 class="card-title w-100 font-weight-bold text-dark mb-1">{{ $candidate->name }}</h4>
                                    <p class="text-muted text-sm mb-0">Klik untuk memilih kandidat ini</p>
                                </div>

                                <div class="card-footer bg-transparent text-center border-top-0 pb-3 pt-0 selected-indicator d-none">
                                    <span class="badge badge-success px-3 py-1"><i class="fas fa-check-circle mr-1"></i> DIPILIH</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Footer Action Panel -->
                <div class="row justify-content-center mt-4">
                    <div class="col-md-8 col-lg-6">
                        <div class="card shadow border-0 bg-white p-4 text-center" style="border-radius: 15px;">
                            <div id="statusBox" class="alert alert-secondary mb-3 font-weight-bold">
                                <i class="fas fa-info-circle mr-1"></i> Belum ada kandidat yang dipilih.
                            </div>
                            
                            <button type="button" id="btnSimpan" class="btn btn-primary btn-block btn-lg font-weight-bold py-3 shadow-sm" disabled>
                                <i class="fas fa-save mr-2"></i> Simpan Pilihan Saya
                            </button>

                            <button type="submit" id="btnKirim" class="btn btn-success btn-block btn-lg font-weight-bold py-3 shadow animate__animated animate__pulse d-none">
                                <i class="fas fa-paper-plane mr-2"></i> Konfirmasi & Kirim Suara Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
    
    <footer class="main-footer text-center border-top-0 bg-transparent text-muted text-xs pb-4">
        Suara Anda dijamin kerahasiaannya oleh sistem e-PILKADA.
    </footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
    // Cegah user menekan tombol Back setelah voting melalui cache browser
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
    
    function selectCandidate(card) {
        // Reset styling semua kartu
        $('.candidate-card').removeClass('active border-success');
        $('.selected-indicator').addClass('d-none');
        
        // Aktifkan kartu yang dipilih
        $(card).addClass('active');
        $(card).find('.selected-indicator').removeClass('d-none');
        
        // Pilih input radio secara otomatis
        card.querySelector('input[type=radio]').checked = true;
        
        // Update Panel UI di bawah
        const name = card.getAttribute('data-name');
        $('#statusBox').removeClass('alert-secondary alert-success').addClass('alert-info')
                       .html('<i class="fas fa-user-check mr-1"></i> Dipilih: <strong class="text-dark">' + name + '</strong>.<br><small>Klik tombol simpan di bawah untuk melanjutkan.</small>');
        
        $('#btnSimpan').prop('disabled', false).removeClass('d-none');
        $('#btnKirim').addClass('d-none');
    }

    $('#btnSimpan').click(function() {
        const selected = $('.candidate-card.active').data('name');
        
        $('#statusBox').removeClass('alert-info').addClass('alert-success')
                       .html('<i class="fas fa-check-double mr-1"></i> Pilihan dikunci: <strong>' + selected + '</strong>.<br><span class="text-danger font-weight-normal text-xs">Periksa kembali, suara tidak dapat diubah setelah dikirim!</span>');
        
        $(this).addClass('d-none');
        $('#btnKirim').removeClass('d-none');
        
        // Scroll otomatis ke tombol kirim agar user sadar
        $('html, body').animate({
            scrollTop: $("#btnKirim").offset().top - 200
        }, 300);
    });

    $('#voteForm').submit(function(e) {
        const selectedName = $('.candidate-card.active').data('name');
        if (!confirm('Peringatan Terakhir!\n\nYakin ingin mengirim suara untuk:\n' + selectedName + '?\n\nSuara yang sudah dikirim bersifat final dan TIDAK BISA diubah kembali.')) {
            e.preventDefault();
        }
    });
</script>
</body>
</html>