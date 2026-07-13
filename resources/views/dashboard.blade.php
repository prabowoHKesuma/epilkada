@extends('layouts.admin')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 font-weight-bold text-dark">
          <i class="fas fa-chart-pie mr-2 text-primary"></i>Panel Monitoring e-PILKADA
        </h1>
      </div>
      <div class="col-sm-6 text-md-right mt-2 mt-sm-0">
        <span class="badge badge-dark p-2 text-sm shadow-sm">
          <i class="fas fa-user-tag mr-1 text-warning"></i> Role: {{ strtoupper(str_replace('_', ' ', $role)) }}
        </span>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    
    {{-- ========================================== --}}
    {{-- PANEL VIEW: SUPERADMIN                     --}}
    {{-- ========================================== --}}
    @if($role === 'superadmin')
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['total_dpt']) }}</h3>
              <p>Total DPT Sistem</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
          </div>
        </div>
        
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success shadow-sm">
            <div class="inner">
              <h3>{{ $data['partisipasi'] }}<sup style="font-size: 20px">%</sup></h3>
              <p>Partisipasi Suara Masuk</p>
            </div>
            <div class="icon"><i class="fas fa-chart-bar"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['total_wilayah']) }}</h3>
              <p>Kelurahan Terintegrasi</p>
            </div>
            <div class="icon"><i class="fas fa-map-marked-alt"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['pending_remote']) }}</h3>
              <p>Pending Verifikasi KTP</p>
            </div>
            <div class="icon"><i class="fas fa-id-card"></i></div>
          </div>
        </div>
      </div>

    {{-- ========================================== --}}
    {{-- PANEL VIEW: ADMIN KELURAHAN                --}}
    {{-- ========================================== --}}
    @elseif($role === 'admin_kelurahan')
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['total_dpt']) }}</h3>
              <p>DPT Kelurahan Anda</p>
            </div>
            <div class="icon"><i class="fas fa-users font-weight-light"></i></div>
          </div>
        </div>
        
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success shadow-sm">
            <div class="inner">
              <h3>{{ $data['partisipasi'] }}<sup style="font-size: 20px">%</sup></h3>
              <p>Partisipasi Wilayah</p>
            </div>
            <div class="icon"><i class="fas fa-percent"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['total_sub_wilayah']) }}</h3>
              <p>Total Sub-Wilayah (RT/RW)</p>
            </div>
            <div class="icon"><i class="fas fa-sitemap"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-purple shadow-sm" style="background-color: #6f42c1; color: white;">
            <div class="inner">
              <h3>{{ number_format($data['pending_remote']) }}</h3>
              <p>Antrean Verifikasi Warga</p>
            </div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
          </div>
        </div>
      </div>

    {{-- ========================================== --}}
    {{-- PANEL VIEW: PETUGAS TPS                    --}}
    {{-- ========================================== --}}
    @elseif($role === 'petugas_tps')
      <div class="row mb-3">
        <div class="col-12">
          <div class="card card-primary card-outline shadow-sm">
            <div class="card-body">
              <h5 class="font-weight-bold text-dark"><i class="fas fa-bolt text-warning mr-2"></i>Aksi Cepat Meja TPS:</h5>
              <p class="text-muted text-sm mb-3">Gunakan menu di bawah ini untuk mencetak token langsung bagi pemilih fisik yang hadir di lokasi TPS.</p>
              <a href="#" class="btn btn-primary font-weight-bold"><i class="fas fa-plus-circle mr-2"></i>Terbitkan Token Booth Baru</a>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-navy shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['total_dpt_tps']) }}</h3>
              <p>Kuota DPT TPS Anda</p>
            </div>
            <div class="icon"><i class="fas fa-folder"></i></div>
          </div>
        </div>
        
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['token_terbit']) }}</h3>
              <p>Token Terbit (Hadir)</p>
            </div>
            <div class="icon"><i class="fas fa-ticket-alt"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-success shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['suara_masuk']) }}</h3>
              <p>Suara Masuk Kotak Digital</p>
            </div>
            <div class="icon"><i class="fas fa-box-open"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-secondary shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['belum_memilih']) }}</h3>
              <p>Sisa Pemilih Belum Hadir</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
          </div>
        </div>
      </div>

    {{-- ========================================== --}}
    {{-- PANEL VIEW: AUDITOR                        --}}
    {{-- ========================================== --}}
    @elseif($role === 'auditor')
      <div class="row">
        <div class="col-md-4 col-12">
          <div class="small-box bg-secondary shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['token_expired']) }}</h3>
              <p>Token Kedaluwarsa (Expired)</p>
            </div>
            <div class="icon"><i class="fas fa-hourglass-end"></i></div>
          </div>
        </div>

        <div class="col-md-4 col-12">
          <div class="small-box bg-danger shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['fraud_detection']) }}</h3>
              <p>Total Penolakan Berkas (Fraud)</p>
            </div>
            <div class="icon"><i class="fas fa-user-slash"></i></div>
          </div>
        </div>

        <div class="col-md-4 col-12">
          <div class="small-box bg-dark shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['total_logs']) }}</h3>
              <p>Total Rekam Jejak (Audit Logs)</p>
            </div>
            <div class="icon"><i class="fas fa-fingerprint"></i></div>
          </div>
        </div>
      </div>

    {{-- ========================================== --}}
    {{-- PANEL VIEW: VIEWER / SAKSI / KPU           --}}
    {{-- ========================================== --}}
    @else
      <div class="row">
        <div class="col-md-6 col-12">
          <div class="small-box bg-teal shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['total_ballots']) }}</h3>
              <p>Total Surat Suara Terhitung (Real-Time)</p>
            </div>
            <div class="icon"><i class="fas fa-poll"></i></div>
          </div>
        </div>
        <div class="col-md-6 col-12">
          <div class="small-box bg-lightblue shadow-sm">
            <div class="inner">
              <h3>{{ number_format($data['total_dpt_global']) }}</h3>
              <p>Total Pemilih Terdaftar Nasional</p>
            </div>
            <div class="icon"><i class="fas fa-globe"></i></div>
          </div>
        </div>
      </div>
    @endif

  </div>
</div>
@endsection