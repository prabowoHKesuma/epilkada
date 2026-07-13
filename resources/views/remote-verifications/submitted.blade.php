@extends('layouts.admin')

@section('content')
<style>.login-page { background-color: #f4f6f9; }</style>
<div class="login-box" style="width: 450px;">
    <div class="card card-outline card-success">
        <div class="card-body text-center p-5">
            <div class="mb-3">
                <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
            </div>
            <h3 class="font-weight-bold text-success mb-2">Pengajuan Terkirim</h3>
            <p class="text-muted text-sm mb-4">Simpan kode verifikasi di bawah ini untuk mengecek status persetujuan dari petugas:</p>
            
            <div class="bg-light p-3 rounded border mb-4">
                <span class="h2 font-weight-bold text-dark tracking-widest font-monospace" style="letter-spacing: 5px;">
                    {{ $verificationCode }}
                </span>
            </div>

            <div class="alert alert-warning text-xs text-left mb-4">
                <i class="fas fa-exclamation-circle mr-1"></i> <strong>Penting:</strong> Kode ini adalah satu-satunya kunci untuk melihat status dan mendapatkan token voting Anda nantinya.
            </div>

            <a href="{{ route('remote.status.form') }}" class="btn btn-outline-primary btn-block btn-lg font-weight-bold">
                <i class="fas fa-search mr-2"></i> Cek Status Sekarang
            </a>
        </div>
    </div>
</div>
@endsection