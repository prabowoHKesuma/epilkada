<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query log, relasikan dengan user jika ada
        $query = AuditLog::with('user')->latest();

        // Filter Berdasarkan Range Tanggal & Waktu Mulai
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }

        // Filter Berdasarkan Range Tanggal & Waktu Selesai
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        // Batasi 25 data per halaman & pertahankan parameter filter saat pindah halaman (withQueryString)
        $logs = $query->paginate(25)->withQueryString();

        return view('audit-logs.index', compact('logs'));
    }
}
