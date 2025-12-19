<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PengumumanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengumuman::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by kategori
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter active announcements
        if ($request->has('active') && $request->active == 'true') {
            $query->active();
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Get all data without pagination
        $pengumumans = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pengumuman berhasil diambil',
            'data' => $pengumumans,
            'total' => $pengumumans->count()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kategori' => 'required|in:umum,penting,akademik,kegiatan,lainnya',
            'status' => 'nullable|in:draft,published,archived',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'penerbit' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('gambar');
        
        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'published';
        }

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $gambarName = time() . '_' . uniqid() . '.' . $gambar->getClientOriginalExtension();
            
            $uploadPath = env('UPLOAD_PATH', '/var/www/assets');
            
            // Create directory if not exists
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Move file to assets folder
            $gambar->move($uploadPath, $gambarName);
            
            $data['gambar'] = $gambarName;
        }

        $pengumuman = Pengumuman::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dibuat',
            'data' => $pengumuman
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pengumuman = Pengumuman::find($id);

        if (!$pengumuman) {
            return response()->json([
                'success' => false,
                'message' => 'Pengumuman tidak ditemukan'
            ], 404);
        }

        // Increment views
        $pengumuman->incrementViews();

        return response()->json([
            'success' => true,
            'message' => 'Detail pengumuman berhasil diambil',
            'data' => $pengumuman
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * Important: Use POST method with _method=PUT in form-data
     */
    public function update(Request $request, $id)
    {
        // Log untuk debugging
        Log::info('Update Request Data:', [
            'all' => $request->all(),
            'hasFile' => $request->hasFile('gambar'),
            'method' => $request->method()
        ]);

        $pengumuman = Pengumuman::find($id);

        if (!$pengumuman) {
            return response()->json([
                'success' => false,
                'message' => 'Pengumuman tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kategori' => 'sometimes|required|in:umum,penting,akademik,kegiatan,lainnya',
            'status' => 'nullable|in:draft,published,archived',
            'tanggal_mulai' => 'sometimes|required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'penerbit' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get all data except gambar and _method
        $data = $request->except(['gambar', '_method']);

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $uploadPath = env('UPLOAD_PATH', '/var/www/assets');
            
            // Delete old image if exists
            if ($pengumuman->gambar) {
                $oldImagePath = $uploadPath . '/' . $pengumuman->gambar;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                    Log::info('Old image deleted:', ['path' => $oldImagePath]);
                }
            }

            $gambar = $request->file('gambar');
            $gambarName = time() . '_' . uniqid() . '.' . $gambar->getClientOriginalExtension();
            
            // Create directory if not exists
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Move file to assets folder
            $gambar->move($uploadPath, $gambarName);
            
            $data['gambar'] = $gambarName;
            Log::info('New image uploaded:', ['filename' => $gambarName]);
        }

        // Update the pengumuman
        $pengumuman->update($data);

        // Refresh to get updated data
        $pengumuman = $pengumuman->fresh();

        Log::info('Pengumuman updated:', ['data' => $pengumuman]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diupdate',
            'data' => $pengumuman
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pengumuman = Pengumuman::find($id);

        if (!$pengumuman) {
            return response()->json([
                'success' => false,
                'message' => 'Pengumuman tidak ditemukan'
            ], 404);
        }

        // Delete image
        if ($pengumuman->gambar) {
            $uploadPath = env('UPLOAD_PATH', '/var/www/assets');
            $imagePath = $uploadPath . '/' . $pengumuman->gambar;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $pengumuman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus'
        ], 200);
    }

    /**
     * Get pengumuman by kategori
     */
    public function byKategori($kategori)
    {
        $pengumumans = Pengumuman::where('kategori', $kategori)
                                 ->published()
                                 ->active()
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pengumuman berhasil diambil',
            'data' => $pengumumans,
            'total' => $pengumumans->count()
        ], 200);
    }

    /**
     * Get published announcements
     */
    public function published()
    {
        $pengumumans = Pengumuman::published()
                                 ->active()
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pengumuman published berhasil diambil',
            'data' => $pengumumans,
            'total' => $pengumumans->count()
        ], 200);
    }
}