<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voter;
use App\Models\ElectionVoter;
use App\Models\Region;
use App\Models\RemoteVerification;
use App\Models\VotingToken;
use App\Models\TpsBoothToken;
use App\Models\AuditLog;
use App\Models\Ballot;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // 🔥 PERBAIKAN: Menggunakan method bawaan Spatie yang langsung mengembalikan string nama role
        // Ini jauh lebih aman dan tidak akan memicu "property on null" jika user belum punya role
        $role = $user->getRoleNames()->first() ?? 'viewer';
        
        $data = [];

        switch ($role) {
            case 'superadmin':
                $data['total_dpt'] = Voter::where('is_active', 1)->count();
                $totalVoted = ElectionVoter::where('has_voted', 1)->count();
                $data['partisipasi'] = $data['total_dpt'] > 0 
                    ? round(($totalVoted / $data['total_dpt']) * 100, 2) 
                    : 0;
                $data['total_wilayah'] = Region::where('level', 'kelurahan')->count();
                $data['pending_remote'] = RemoteVerification::where('status', 'pending')->count();
                break;

            case 'admin_kelurahan':
                $regionId = $user->region_id;
                $data['total_dpt'] = Voter::where('region_id', $regionId)->where('is_active', 1)->count();
                
                $votedWilayah = ElectionVoter::whereHas('voter', function($query) use ($regionId) {
                    $query->where('region_id', $regionId);
                })->where('has_voted', 1)->count();
                
                $data['partisipasi'] = $data['total_dpt'] > 0 
                    ? round(($votedWilayah / $data['total_dpt']) * 100, 2) 
                    : 0;
                $data['total_sub_wilayah'] = Region::where('parent_id', $regionId)->count();
                $data['pending_remote'] = RemoteVerification::whereHas('voter', function($query) use ($regionId) {
                    $query->where('region_id', $regionId);
                })->where('status', 'pending')->count();
                break;

            case 'petugas_tps':
                $regionId = $user->region_id;
                $data['total_dpt_tps'] = Voter::where('region_id', $regionId)->where('is_active', 1)->count();
                $data['token_terbit'] = TpsBoothToken::where('created_by', $user->id)->count();
                $data['suara_masuk'] = TpsBoothToken::where('created_by', $user->id)
                    ->whereNotNull('used_at')
                    ->count();
                $data['belum_memilih'] = max(0, $data['total_dpt_tps'] - $data['suara_masuk']);
                break;

            case 'auditor':
                $now = now();
                $data['token_expired'] = VotingToken::where('expires_at', '<', $now)->whereNull('used_at')->count() 
                    + TpsBoothToken::where('expires_at', '<', $now)->whereNull('used_at')->count();
                $data['fraud_detection'] = RemoteVerification::where('status', 'rejected')->count();
                $data['total_logs'] = AuditLog::count();
                break;

            default: 
                $data['total_ballots'] = Ballot::count();
                $data['total_dpt_global'] = Voter::where('is_active', 1)->count();
                break;
        }

        return view('dashboard', compact('data', 'role'));
    }
}