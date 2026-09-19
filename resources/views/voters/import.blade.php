@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Import Pemilih dari Excel</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('import_errors') && count(session('import_errors')))
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Catatan Baris yang Dilewati:</h5>
                        <ul class="mb-0 list-unstyled" style="max-height: 200px; overflow-y: auto;">
                            @foreach (session('import_errors') as $err)
                                <li><i class="fas fa-chevron-right text-xs"></i> {{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Petunjuk & Upload File Excel</h3>
                    </div>
                    
                    <form action="{{ route('voters.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            
                            <div class="callout callout-warning bg-light">
                                <h5>Format Kolom Excel Wajib:</h5>
                                <p class="mb-2">Baris pertama adalah header (nama bebas), susunan urutan kolom dari A sampai F harus mutlak seperti ini:</p>
                                <code>name, nik, kk, address, phone, region_code</code>
                                
                                <hr>
                                <h6 class="font-weight-bold text-danger"><i class="fas fa-exclamation-circle"></i> TIPS PENTING NIK & KK:</h6>
                                <p class="mb-2 text-sm">Pastikan Anda mengubah format kolom NIK dan KK di Microsoft Excel menjadi <strong>"Text"</strong> sebelum mengetik angka. Jika menggunakan format General/Number, angka 16 digit akan otomatis rusak menjadi (misal: <code>3.52E+15</code>) dan akan ditolak oleh sistem.</p>
                                
                                <p class="mt-3 mb-0 text-sm text-muted">
                                    * Catatan: Kolom <code>region_code</code> harus persis seperti kode RT/RW yang telah terdaftar (contoh: <code>RW01-RT01</code>).
                                </p>
                            </div>

                            <div class="form-group mt-4">
                                <label for="excel_file">Pilih File Excel (.xls / .xlsx)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="file" accept=".xlsx, .xls" class="custom-file-input" id="excel_file" required>
                                        <label class="custom-file-label" for="excel_file">Pilih file Excel ...</label>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload"></i> Upload & Import Data
                            </button>
                            <a href="{{ route('voters.index') }}" class="btn btn-default float-right">Kembali</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection