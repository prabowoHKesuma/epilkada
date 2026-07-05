@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8"><h1 class="m-0">Kelola Pemilih — {{ $election->title }}</h1></div>
            <div class="col-sm-4">
                <a href="{{ route('elections.show', $election) }}" class="btn btn-default float-sm-right btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali ke Detail
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <livewire:election-voters-manager :election="$election" />
    </div>
</div>
@endsection