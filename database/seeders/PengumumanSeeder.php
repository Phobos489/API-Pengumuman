<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengumuman;
use Carbon\Carbon;

class PengumumanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pengumumans = [
            [
                'judul' => 'Pengumuman Libur Akhir Tahun 2024',
                'deskripsi' => 'Dengan hormat kami sampaikan bahwa akan ada libur akhir tahun mulai tanggal 25 Desember 2024 hingga 1 Januari 2025. Kegiatan akan kembali normal pada tanggal 2 Januari 2025.',
                'kategori' => 'umum',
                'status' => 'published',
                'tanggal_mulai' => Carbon::now(),
                'tanggal_selesai' => Carbon::now()->addDays(30),
                'penerbit' => 'Admin Sistem',
                'views' => 0,
            ],
            [
                'judul' => 'Pemberitahuan Penting: Maintenance Server',
                'deskripsi' => 'Akan dilakukan maintenance server pada hari Minggu, 22 Desember 2024 pukul 00:00 - 06:00 WIB. Selama maintenance, layanan tidak dapat diakses. Mohon maaf atas ketidaknyamanannya.',
                'kategori' => 'penting',
                'status' => 'published',
                'tanggal_mulai' => Carbon::now()->subDays(5),
                'tanggal_selesai' => Carbon::now()->addDays(2),
                'penerbit' => 'IT Support',
                'views' => 0,
            ],
            [
                'judul' => 'Jadwal Ujian Akhir Semester Genap 2024',
                'deskripsi' => 'Ujian Akhir Semester akan dilaksanakan mulai tanggal 15 Januari 2025. Silakan cek jadwal lengkap di portal akademik masing-masing.',
                'kategori' => 'akademik',
                'status' => 'published',
                'tanggal_mulai' => Carbon::now(),
                'tanggal_selesai' => Carbon::now()->addDays(60),
                'penerbit' => 'Bagian Akademik',
                'views' => 0,
            ],
            [
                'judul' => 'Kegiatan Bakti Sosial dan Donor Darah',
                'deskripsi' => 'Kami mengajak seluruh civitas akademika untuk berpartisipasi dalam kegiatan bakti sosial dan donor darah yang akan diadakan pada tanggal 20 Desember 2024.',
                'kategori' => 'kegiatan',
                'status' => 'published',
                'tanggal_mulai' => Carbon::now(),
                'tanggal_selesai' => Carbon::now()->addDays(4),
                'penerbit' => 'BEM',
                'views' => 0,
            ],
            [
                'judul' => 'Draft: Perubahan Kurikulum 2025',
                'deskripsi' => 'Draft pengumuman mengenai perubahan kurikulum untuk tahun ajaran 2025/2026. Masih dalam tahap review.',
                'kategori' => 'akademik',
                'status' => 'draft',
                'tanggal_mulai' => Carbon::now()->addDays(30),
                'tanggal_selesai' => null,
                'penerbit' => 'Bagian Kurikulum',
                'views' => 0,
            ],
        ];

        foreach ($pengumumans as $pengumuman) {
            Pengumuman::create($pengumuman);
        }
    }
}