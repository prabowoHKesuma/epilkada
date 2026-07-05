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
        .candidate-card { transition: all 0.3s; border: 3px solid transparent; }
        .candidate-card:hover { border-color: #007bff; transform: scale(1.02); }
        .candidate-card.active { border-color: #007bff; background-color: #e9ecef; }
        .candidate-img { height: 200px; object-fit: cover; }
    </style>
</head>
<body class="hold-transition layout-top-nav">

<div class="wrapper">
    <div class="content-wrapper p-3">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-4">
                <h1 class="font-weight-bold">{{ $election->title }}</h1>
                <p class="text-muted">Klik foto kandidat untuk memilih, lalu tekan tombol simpan.</p>
            </div>

            <form action="{{ route('voting.submit') }}" method="POST" id="voteForm">
                @csrf
                <!-- Grid Kandidat -->
                <div class="row justify-content-center">
                    @foreach($election->candidates as $candidate)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card candidate-card h-100 shadow-sm" 
                                 onclick="selectCandidate(this)" 
                                 data-id="{{ $candidate->id }}" 
                                 data-name="No. {{ $candidate->number_order }} — {{ $candidate->name }}">
                                <input type="radio" name="candidate_id" value="{{ $candidate->id }}" class="d-none">
                                
                                @if($candidate->photo)
                                    <img src="{{ Storage::url($candidate->photo) }}" class="card-img-top candidate-img">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center candidate-img">
                                        <i class="fas fa-user fa-5x text-gray"></i>
                                    </div>
                                @endif
                                
                                <div class="card-body text-center">
                                    <h5 class="card-title w-100 font-weight-bold">No. {{ $candidate->number_order }}</h5>
                                    <p class="card-text text-muted mt-2">{{ $candidate->name }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Footer Action -->
                <div class="row justify-content-center mt-3">
                    <div class="col-md-6">
                        <div id="statusBox" class="alert alert-secondary text-center">
                            Belum ada kandidat dipilih.
                        </div>
                        
                        <button type="button" id="btnSimpan" class="btn btn-primary btn-block btn-lg" disabled>
                            <i class="fas fa-check-circle mr-2"></i> Simpan Pilihan
                        </button>

                        <button type="submit" id="btnKirim" class="btn btn-success btn-block btn-lg d-none">
                            <i class="fas fa-paper-plane mr-2"></i> Konfirmasi & Kirim Suara
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
<script>
    function selectCandidate(card) {
        // Reset semua
        $('.candidate-card').removeClass('active');
        card.classList.add('active');
        
        // Pilih Radio
        card.querySelector('input[type=radio]').checked = true;
        
        // Update UI
        const name = card.getAttribute('data-name');
        $('#statusBox').removeClass('alert-secondary alert-success').addClass('alert-info')
                       .html('Dipilih: <strong>' + name + '</strong>. Klik tombol simpan.');
        
        $('#btnSimpan').prop('disabled', false);
        $('#btnKirim').addClass('d-none');
        $('#btnSimpan').removeClass('d-none');
    }

    $('#btnSimpan').click(function() {
        const selected = $('.candidate-card.active').data('name');
        
        $('#statusBox').removeClass('alert-info').addClass('alert-success')
                       .html('Pilihan tersimpan: <strong>' + selected + '</strong>. Klik tombol hijau untuk kirim.');
        
        $(this).addClass('d-none');
        $('#btnKirim').removeClass('d-none');
    });

    $('#voteForm').submit(function(e) {
        const selectedName = $('.candidate-card.active').data('name');
        if (!confirm('Yakin ingin mengirim suara untuk ' + selectedName + '?\n\nSuara tidak bisa diubah.')) {
            e.preventDefault();
        }
    });
</script>
</body>
</html>