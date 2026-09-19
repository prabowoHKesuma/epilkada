<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voter;
use App\Models\Region;
use App\Services\PiiHasher;
use PhpOffice\PhpSpreadsheet\IOFactory; // Library pembaca Excel
use Illuminate\Support\Facades\DB;

class VoterImportController extends Controller
{
    public function form()
    {
        return view('voters.import');
    }

    public function process(Request $request)
    {
        // 1. Ubah validasi dari csv menjadi ekstensi excel
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        set_time_limit(0);

        try {
            // 2. Load file Excel menggunakan PhpSpreadsheet
            $path = $request->file('file')->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal membaca file. Pastikan formatnya benar (.xls / .xlsx).']);
        }

        if (count($rows) <= 1) {
            return back()->withErrors(['file' => 'File Excel kosong atau hanya berisi header.']);
        }

        array_shift($rows); // Hapus baris pertama (header)

        $success = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // Index 0 adalah baris ke-2 di Excel

                // Ambil data dan pastikan formatnya string (array_pad mencegah error jika kolom kurang)
                [$name, $nik, $kk, $address, $phone, $regionCode] = array_pad($row, 6, null);

                // Bersihkan spasi tak terlihat yang sering muncul dari Excel
                $name = trim((string) $name);
                $nik = trim((string) $nik);
                $kk = trim((string) $kk);
                $regionCode = trim((string) $regionCode);

                if (! $name || ! $nik || ! $kk || ! $regionCode) {
                    $errors[] = "Baris $rowNumber: ada kolom wajib yang kosong, dilewati.";
                    $skipped++;
                    continue;
                }

                if (! preg_match('/^\d{16}$/', $nik) || ! preg_match('/^\d{16}$/', $kk)) {
                    $errors[] = "Baris $rowNumber: NIK/KK ($nik / $kk) harus 16 digit angka, dilewati.";
                    $skipped++;
                    continue;
                }

                $region = Region::where('code', $regionCode)->first();
                if (! $region) {
                    $errors[] = "Baris $rowNumber: kode wilayah '$regionCode' tidak ditemukan, dilewati.";
                    $skipped++;
                    continue;
                }

                // === PROTEKSI KEAMANAN LINTAS WILAYAH ===
                $user = auth()->user();
                if (!$user->hasRole('superadmin')) {
                    $isAllowed = Region::where('id', $region->id)
                        ->where(function($q) use ($user) {
                            $q->where('id', $user->region_id)
                            ->orWhere('parent_id', $user->region_id)
                            ->orWhereIn('parent_id', function($subQuery) use ($user) {
                                $subQuery->select('id')->from('regions')->where('parent_id', $user->region_id);
                            });
                        })->exists();

                    if (!$isAllowed) {
                        $errors[] = "Baris $rowNumber: Anda tidak memiliki akses ke wilayah '$regionCode', dilewati.";
                        $skipped++;
                        continue;
                    }
                }
                // =========================================

                $nikHash = PiiHasher::hash($nik);

                if (Voter::where('nik_hash', $nikHash)->exists()) {
                    $errors[] = "Baris $rowNumber: NIK '$nik' sudah terdaftar sebelumnya, dilewati.";
                    $skipped++;
                    continue;
                }

                Voter::create([
                    'voter_code' => 'PMH-'.strtoupper(uniqid()),
                    'organization_id' => $region->organization_id, // Mencegah hardcode milik admin
                    'region_id' => $region->id,
                    'name' => $name,
                    'nik_hash' => $nikHash,
                    'kk_hash' => PiiHasher::hash($kk),
                    'address' => trim((string) $address),
                    'phone' => trim((string) $phone),
                    'is_active' => true,
                ]);

                $success++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['file' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()]);
        }

        return back()->with([
            'status' => "Import selesai: $success pemilih ditambahkan, $skipped dilewati.",
            'import_errors' => $errors,
        ]);
    }
}