<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Organization;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Region::with(['parent', 'organization'])->orderBy('level')->orderBy('name');

        // DATA SCOPING: Menyaring tampilan tabel berdasarkan Role
        if ($user->hasRole('admin-kelurahan')) {
            // Admin Kelurahan melihat Kelurahannya, RW-nya, dan RT-nya
            $query->where(function($q) use ($user) {
                $q->where('id', $user->region_id) // Kelurahan
                  ->orWhere('parent_id', $user->region_id) // RW
                  ->orWhereIn('parent_id', function($subQuery) use ($user) {
                      $subQuery->select('id')->from('regions')->where('parent_id', $user->region_id);
                  }); // RT (anak dari RW)
            });
        } elseif ($user->hasRole('admin-rw')) {
            // Admin RW hanya melihat RW-nya dan RT di bawahnya
            $query->where('id', $user->region_id)
                  ->orWhere('parent_id', $user->region_id);
        }

        $regions = $query->get();
        return view('regions.index', compact('regions'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $organizations = Organization::where('is_active', true)->get();
        
        // Menentukan level yang boleh dibuat berdasarkan Role
        $allowedLevels = ['kelurahan', 'rw', 'rt', 'custom']; // Default Superadmin
        
        // Membatasi pilihan wilayah induk (parent)
        $queryRegions = Region::query();

        if ($user->hasRole('admin-kelurahan')) {
            $allowedLevels = ['rw', 'rt'];
            // Hanya boleh melihat Kelurahannya sendiri dan RW di bawahnya sebagai parent
            $queryRegions->where('id', $user->region_id)
                         ->orWhere('parent_id', $user->region_id);
        } elseif ($user->hasRole('admin-rw')) {
            $allowedLevels = ['rt'];
            // Hanya boleh melihat RW-nya sendiri sebagai parent
            $queryRegions->where('id', $user->region_id);
        }

        $allRegions = $queryRegions->get(['id', 'name', 'code', 'level']); 
        
        return view('regions.create', compact('organizations', 'allRegions', 'allowedLevels', 'user'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Amankan form dari manipulasi inspect element (HTML)
        // Paksa organization_id sesuai milik user jika bukan superadmin
        if (!$user->hasRole('superadmin')) {
            $request->merge([
                'organization_id' => $user->organization_id
            ]);
        }

        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'level'           => 'required|in:kota,kecamatan,kelurahan,rw,rt,custom',
            'name'            => 'required|string|max:150',
            'parent_id'       => 'nullable|exists:regions,id',
            'code'            => 'nullable|string|max:50|unique:regions,code',
        ]);

        // Validasi Ekstra Keamanan (Mencegah Admin RW membuat RW baru via API/Postman)
        if ($user->hasRole('admin-rw') && $validated['level'] !== 'rt') {
            abort(403, 'Anda hanya diizinkan menambahkan wilayah tingkat RT.');
        }

        if (empty($validated['code'])) {
            $validated['code'] = $this->generateRegionCode($validated['level'], $validated['name'], $validated['parent_id']);
        }

        Region::create($validated);

        return redirect()->route('regions.index')->with('success', 'Data wilayah berhasil ditambahkan.');
    }

    public function edit(Request $request, Region $region)
    {
        $user = $request->user();
        $organizations = Organization::where('is_active', true)->get();
        
        // 1. Tentukan level yang boleh diedit berdasarkan Role
        $allowedLevels = ['kelurahan', 'rw', 'rt', 'custom']; // Default Superadmin
        
        // 2. Batasi pilihan wilayah induk (parent)
        $queryRegions = Region::where('id', '!=', $region->id); // Kecualikan diri sendiri

        if ($user->hasRole('admin-kelurahan')) {
            $allowedLevels = ['rw', 'rt'];
            // Admin Kelurahan hanya boleh melihat wilayahnya sebagai parent
            $queryRegions->where(function($q) use ($user) {
                $q->where('id', $user->region_id)
                  ->orWhere('parent_id', $user->region_id);
            });
            
            // Proteksi Tambahan: Cegah Admin Kelurahan mengedit wilayah di luar wewenangnya
            if ($region->id !== $user->region_id && $region->parent_id !== $user->region_id) {
                 abort(403, 'Anda tidak memiliki akses untuk mengedit wilayah ini.');
            }
            
        } elseif ($user->hasRole('admin-rw')) {
            $allowedLevels = ['rt'];
            $queryRegions->where('id', $user->region_id);
            
            // Proteksi Tambahan: Cegah Admin RW mengedit selain RW-nya dan RT-nya
            if ($region->id !== $user->region_id && $region->parent_id !== $user->region_id) {
                 abort(403, 'Anda tidak memiliki akses untuk mengedit wilayah ini.');
            }
        }

        $allRegions = $queryRegions->get(['id', 'name', 'code', 'level']); 
        
        // Jangan lupa pastikan view edit.blade.php Anda sudah mem-parsing variabel $allowedLevels dan $user
        return view('regions.edit', compact('region', 'organizations', 'allRegions', 'allowedLevels', 'user'));
    }

    public function update(Request $request, Region $region)
    {
        $user = $request->user();

        // Amankan dari Inspect Element: Timpa paksa organization_id milik user (selain Superadmin)
        if (!$user->hasRole('superadmin')) {
            $request->merge([
                'organization_id' => $user->organization_id
            ]);
        }

        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'level'           => 'required|in:kota,kecamatan,kelurahan,rw,rt,custom',
            'name'            => 'required|string|max:150',
            'parent_id'       => 'nullable|exists:regions,id',
            'code'            => 'nullable|string|max:50|unique:regions,code,' . $region->id,
        ]);

        // Cegah bypass level via manipulasi HTTP Request
        if ($user->hasRole('admin-rw') && $validated['level'] !== 'rt') {
            abort(403, 'Anda hanya diizinkan mengubah wilayah tingkat RT.');
        }

        // Generate kode jika dikosongkan
        if (empty($validated['code'])) {
            $validated['code'] = $this->generateRegionCode($validated['level'], $validated['name'], $validated['parent_id']);
        }

        $region->update($validated);

        return redirect()->route('regions.index')->with('success', 'Data wilayah berhasil diperbarui.');
    }

    public function destroy(Region $region)
    {
        // PROTEKSI 1: Tolak penghapusan jika masih ada data anak (RW/RT) di bawahnya
        if ($region->children()->count() > 0) {
            return redirect()->route('regions.index')->with('error', 'Gagal menghapus! Wilayah ini masih memiliki sub-wilayah (anak) di bawahnya.');
        }

        // PROTEKSI 2: Tangkap error Foreign Key (dari Audit Log, User, dll)
        try {
            $region->delete();
            return redirect()->route('regions.index')->with('success', 'Data wilayah berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Cek apakah error-nya adalah 23000 (Integrity constraint violation)
            if ($e->getCode() == "23000") {
                return redirect()->route('regions.index')->with('error', 'Gagal menghapus! Wilayah ini tidak bisa dihapus karena sudah memiliki rekam jejak di sistem (misal: terkait dengan Audit Log, Petugas, atau Pemilih).');
            }
            
            // Lemparkan error generic jika terjadi masalah database lainnya
            return redirect()->route('regions.index')->with('error', 'Terjadi kesalahan sistem saat mencoba menghapus data.');
        }
    }

    /**
     * Fungsi Bantuan untuk generate kode otomatis (Misal: RW004-RT01)
     */
    private function generateRegionCode($level, $name, $parentId)
    {
        // Hapus spasi dari nama (contoh: "RT 01" menjadi "RT01")
        $cleanName = strtoupper(str_replace(' ', '', $name)); 

        if ($parentId) {
            $parent = Region::find($parentId);
            return $parent->code . '-' . $cleanName; // Hasil: RW004-RT01
        }

        // Jika tidak punya parent (misal kelurahan)
        return 'REG-' . $cleanName; 
    }
}