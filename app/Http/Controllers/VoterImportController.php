<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voter;
use App\Models\Region;
use App\Services\PiiHasher;

class VoterImportController extends Controller
{
    public function form()
    {
        return view('voters.import');
    }

    public function process(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');

        $header = fgetcsv($handle); // baris pertama = nama kolom, dilewati
        $success = 0;
        $skipped = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Format kolom CSV yang diharapkan:
            // name, nik, kk, address, phone, region_code
            [$name, $nik, $kk, $address, $phone, $regionCode] = array_pad($row, 6, null);

            if (! $name || ! $nik || ! $kk || ! $regionCode) {
                $errors[] = "Baris $rowNumber: ada kolom wajib yang kosong, dilewati.";
                $skipped++;
                continue;
            }

            if (! preg_match('/^\d{16}$/', $nik) || ! preg_match('/^\d{16}$/', $kk)) {
                $errors[] = "Baris $rowNumber: NIK/KK harus 16 digit angka, dilewati.";
                $skipped++;
                continue;
            }

            $region = Region::where('code', $regionCode)->first();
            if (! $region) {
                $errors[] = "Baris $rowNumber: kode wilayah '$regionCode' tidak ditemukan, dilewati.";
                $skipped++;
                continue;
            }

            $nikHash = PiiHasher::hash($nik);

            if (Voter::where('nik_hash', $nikHash)->exists()) {
                $errors[] = "Baris $rowNumber: NIK sudah terdaftar sebelumnya, dilewati.";
                $skipped++;
                continue;
            }

            Voter::create([
                'voter_code' => 'PMH-'.strtoupper(uniqid()),
                'organization_id' => auth()->user()->organization_id,
                'region_id' => $region->id,
                'name' => trim($name),
                'nik_hash' => $nikHash,
                'kk_hash' => PiiHasher::hash($kk),
                'address' => $address,
                'phone' => $phone,
                'is_active' => true,
            ]);

            $success++;
        }

        fclose($handle);

        return back()->with([
            'status' => "Import selesai: $success pemilih berhasil ditambahkan, $skipped dilewati.",
            'import_errors' => $errors,
        ]);
    }
}
