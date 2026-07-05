@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Terbitkan Token TPS — {{ $election->title }}</h1>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Notifikasi (biarkan tetap di sini) -->
        @if (session()->has('generated_token'))
            <div class="alert alert-success">
                Token untuk <strong>{{ session('voter_name') }}</strong>: 
                <h3 class="text-white">{{ session('generated_token') }}</h3>
            </div>
        @endif

<!-- Tabel Anda di bawah ini -->

        <!-- Panggil Komponen Livewire -->
        <livewire:tps-token-table :election="$election" />
    </div>
</div>
@endsection