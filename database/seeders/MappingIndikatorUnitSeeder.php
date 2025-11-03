<?php

namespace Database\Seeders;

use App\Models\HospitalSurveyIndicator;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MappingIndikatorUnitSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel pivot
        DB::table('mapping_indikator_unit')->truncate();

        // 1. Ambil semua ID Indikator
        $indicators = HospitalSurveyIndicator::pluck('indicator_id', 'indicator_name');

        // 2. Ambil semua ID Unit
        $units = Unit::pluck('id', 'nama_unit');

        // 3. Buat Peta Relasi (Indikator -> [Daftar Unit])
        // Ini didasarkan pada file teks asli Anda
        $map = [
            // Indikator yang ada di SEMUA unit klinis
            'KEPATUHAN KEBERSIHAN TANGAN' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'Radiologi', 'Gizi', 'DOKPOL', 'Rekammedis', 'Tim IT', 'FISOTERAPI', 'IPRS', 'Poliklinik'],
            'KEPATUHAN PENGGUNAAN ALAT PELINDUNG DIRI' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'Radiologi', 'Gizi', 'DOKPOL', 'FISOTERAPI', 'Poliklinik'],
            'KEPATUHAN IDENTIFIKASI PASIEN' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'Radiologi', 'Gizi', 'DOKPOL', 'FISOTERAPI', 'Poliklinik'],
            'KEPATUHAN UPAYA PENCEGAHAN RESIKO PASIEN JATUH' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'Radiologi', 'Gizi', 'FISOTERAPI', 'Poliklinik'],
            'KEPATUHAN WAKTU TANGGAP TERHADAP KOMPLAIN' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'Radiologi', 'Gizi', 'DOKPOL', 'IPRS', 'Rekammedis', 'FISOTERAPI', 'BPJS', 'Poliklinik'],
            'KEPUASAN PASIEN' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'IPRS', 'Rekammedis', 'FISOTERAPI', 'BPJS', 'Poliklinik'],
            'KEPATUHAN PENGGUNAAN ALUR KLINIS' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'Radiologi', 'Gizi', 'DOKPOL', 'Poliklinik'],
            'KEPATUHAN PEGAWAI DATANG TEPAT WAKTU' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'Manajemen', 'Keuangan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'Radiologi', 'Gizi', 'DOKPOL', 'IPRS', 'Rekammedis', 'Tim IT', 'FISOTERAPI', 'BPJS', 'Poliklinik'],
            'KEPATUHAN PENGUNAAN FORNAS' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'Poliklinik', 'Instalasi Farmasi'],
            'ANGKA KEJADIAN HAIs DIRUMAH SAKIT' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'Poliklinik'],
            'PELAPORAN HASIL KRITIS LABORATORIUM' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'Poliklinik', 'Laboratorium'],
            'KESLAHAN PETUGAS RUANGAN DALAM PENGORDERAN OBAT SIMRS' => ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Unit hemodialisa', 'IGD', 'Instalasi Farmasi'],
            'KEPATUHAN PELAKSANAAN KOMUNIKASI EFEKTIF' => ['IGD', 'Radiologi', 'Gizi', 'DOKPOL', 'Rekammedis', 'Tim IT', 'FISOTERAPI'],
            'PEMASANGAN STIKER KUNING PADA PASIEN RESIKO JATUH RAWAT INAP' => ['Ruang Anak', 'Kebidanan', 'ICU'],

            // Indikator spesifik per unit
            'KEPATUHAN PENGISIAN RESUME KEPERAWATAN' => ['IGD', 'Gizi', 'DOKPOL', 'FISOTERAPI'],
            'MENGURANGI RESIKO INFEKSI' => ['CSSD'], // 'CSSD' tidak ada di UnitSeeder Anda, jadi ini akan diabaikan
            'KELENGKAPAN RESUME MEDIS RAWAT JALAN' => ['Poliklinik'],
            'WAKTU TUNGGU RAWAT JALAN' => ['Poliklinik'],
            'KELENGKAPAN RESUME MEDIS RAWAT INAP' => ['Ruang Anak'],
            'KELENGKAPAN RESUME MEDIS NEONATUS' => ['Perinatologi'],
            'KEPATUHAN BIDAN MENGEDUKASI DAN MENDOKUMENTASIKAN' => ['Kebidanan'],
            'PENGUSULAN UKP DAN GAJI BERKALA TEPAT WAKTU' => ['Manajemen'],
            'KEPATUHAN PEGAWAI MENYELESAIKAN ADMINISTRASI SETELAH TERIMA GAJI' => ['Keuangan'],
            'KETERLAMBATAN PASIEN PINDAH DARI ICU KE RUANGAN' => ['ICU'],
            'KEPATUHAN DAN KELENGKAPAN DALAM PELAKSANAAN SERAH TERIMA PASIEN' => ['Instalasi Kamar Operasi'],
            'PENUNDAAN OPERASI ELEKTIF' => ['Instalasi Kamar Operasi'],
            'WAKTU TANGGAP OPERASI SECIO CESARIA (EMERGENCY)' => ['Instalasi Kamar Operasi'],
            'KEJADIAN PASIEN HIPOTENSI INTRA HD' => ['Unit hemodialisa'],
            'WAKTU TUNGGU PELYANAN OBAT JADI <30 MENIT' => ['Instalasi Farmasi'],
            'RESPON TIME PETUGAS MEMVALIDASI RESEP ERM' => ['Instalasi Farmasi'],
            'WAKTU KELUAR PEMERIKSAAN DARAH RUTIN <2 JAM' => ['Laboratorium'],
            'KEPATUHAN PENGEMASAN ALAT MEDIS DAN LABELING ALAT STERIL' => ['CSSD'], // 'CSSD' tidak ada di UnitSeeder Anda
            'KELENGKAPAN BERKAS RAWAT JALAN UNTUK PENGKLEMAN BPJS' => ['BPJS'],
            'PASIEN NON GAWAT DARURAT YANG DI LAYANI' => ['IGD'],
            'KEPATUHAN PETUGAS MENGINFUT DATA KE SIMRS' => ['Radiologi'],
            'PELAPORAN HASIL KRITIS RADIOLOGI' => ['Radiologi'],
            'KEPATUHAN PENILAIAN GIZI PASIEN RAWAT INAP 1X24 JAM' => ['Gizi'],
            'VISITE DAN PERENCANAAN DIET PASIEN DIABETES OLEH AHLI GIZI' => ['Gizi'],
            'HASIL VISUM SELESAI DALAM 2 X 24 JAM PEMERIKSAAN' => ['DOKPOL'],
            'KEPATUHAN PETUGAS IPRS MELAKUKAN PENGECEKAN LAPORAN UNIT' => ['IPRS'],
            'KELANGKAPAN PENGISIAN REKAM MEDIS' => ['Rekammedis'],
            'RESPON TIME PETUGAS IT TERHADAP KOMPLAIN SIMRS < 10 MENIT' => ['Tim IT'],
            'Penginputan Soap pasien pada Simrs' => ['Tim IT'],
            'KEPATUHAN PENGANGKUTAN LIMBAH PADAT TEPAT WAKTU' => ['IPAL'], // 'IPAL' tidak ada di UnitSeeder Anda
            'KEJADIAN PASIEN FISIOTERAPI DROP OUT' => ['FISOTERAPI'],
        ];

        $mappingToInsert = [];

        // 4. Loop peta dan buat data pivot
        foreach ($map as $indicatorName => $unitNames) {
            // Cek apakah indikator ini ada di database
            if (isset($indicators[$indicatorName])) {
                $indicatorId = $indicators[$indicatorName];

                // Loop semua unit yang terhubung
                foreach ($unitNames as $unitName) {
                    // Cek apakah unit ini ada di database
                    if (isset($units[$unitName])) {
                        $unitId = $units[$unitName];

                        $mappingToInsert[] = [
                            'id_indikator' => $indicatorId,
                            'id_unit' => $unitId,
                        ];
                    }
                }
            }
        }

        // 5. Insert semua data pivot sekaligus
        if (! empty($mappingToInsert)) {
            DB::table('mapping_indikator_unit')->insert($mappingToInsert);
        }
    }
}
