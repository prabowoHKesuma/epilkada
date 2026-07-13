@extends('layouts.admin')

@section('content')
<style>
    .register-page { background-color: #f4f6f9; }
    .custom-file-label { overflow: hidden; }
</style>
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

<script>$(function () { bsCustomFileInput.init(); });</script>
@endsection