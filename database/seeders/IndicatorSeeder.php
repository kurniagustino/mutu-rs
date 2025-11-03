<?php

namespace Database\Seeders;

use App\Models\HospitalSurveyIndicator;
use App\Models\ImutCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class IndicatorSeeder extends Seeder
{
    private $categoryIds = [];

    private $defKebersihanTangan;

    private $defAPD;

    private $defIdentifikasiPasien;

    private $defVisiteDokter;

    private $defHasilKritisLab;

    private $defPencegahanJatuh;

    private $defWaktuTanggapKomplain;

    private $defKepuasanPasien;

    private $defClinicalPathway;

    private $defKomunikasiEfektif;

    private $defFornas;

    private $defHAIs;

    private $defWaktuTungguRajal;

    private $defOperasiSC;

    private $defPenundaanOperasi;

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('hospital_survey_indicator')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->categoryIds = [
            'skp' => ImutCategory::where('imut_name_category', 'sasaran keselamatan pasien')->first()->imut_category_id,
            'klinis' => ImutCategory::where('imut_name_category', 'area klinis')->first()->imut_category_id,
            'manajerial' => ImutCategory::where('imut_name_category', 'area manajerial')->first()->imut_category_id,
            'lokal' => ImutCategory::where('imut_name_category', 'lokal')->first()->imut_category_id,
            'wajib' => ImutCategory::where('imut_name_category', 'wajib')->first()->imut_category_id,
        ];

        $this->loadDefinitionsFromPDF();

        // ✅ PERUBAHAN BESAR: Data tidak lagi di-grup per unit
        $uniqueIndicators = $this->getIndicatorData();

        foreach ($uniqueIndicators as $indicator) {

            $cleanedName = preg_replace('/^\d+\.\s*/', '', $indicator['name']);

            HospitalSurveyIndicator::create([
                'indicator_name' => $cleanedName,
                'dimensi_mutu' => $indicator['dim'],
                'tujuan' => $indicator['tuj'],
                'satuan_pengukuran' => $indicator['sat'],
                'indicator_definition' => $indicator['def'],
                'indicator_criteria_inclusive' => $indicator['inc'],
                'indicator_criteria_exclusive' => $indicator['exc'],
                'indicator_source_of_data' => $indicator['src'],

                // ❌ 'indicator_monitoring_area' DIHAPUS

                'indicator_category_id' => $this->getCategoryId($cleanedName), // Category ID tidak lagi butuh $unitName
                'indicator_type' => 'Proses',
                'indicator_frequency' => 'Bulanan',
                'indicator_target' => $indicator['target'] ?? '100',
                'indicator_record_status' => 'A',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // ✅ getCategoryId tidak lagi butuh $unitName
    private function getCategoryId($indicatorName)
    {
        $name = strtoupper($indicatorName);
        if (Str::contains($name, ['IDENTIFIKASI PASIEN', 'KOMUNIKASI EFEKTIF', 'PASIEN JATUH', 'STIKER KUNING', 'SIDE MARKING'])) {
            return $this->categoryIds['skp'];
        }
        if (Str::contains($name, ['PEGAWAI DATANG TEPAT WAKTU', 'ADMINISTRASI', 'PENGUSULAN UKP', 'LAPORAN UNIT'])) {
            return $this->categoryIds['manajerial'];
        }
        if (Str::contains($name, ['FARMASI', 'OBAT', 'FORNAS'])) {
            return $this->categoryIds['wajib']; // Contoh saja
        }

        return $this->categoryIds['lokal'];
    }

    private function buildIndicator($name, $data = null)
    {
        if ($data === null) {
            $data = ['dim' => null, 'tuj' => null, 'sat' => null, 'def' => null, 'inc' => null, 'exc' => null, 'src' => null, 'target' => '100'];
        }

        return array_merge($data, ['name' => $name]);
    }

    // ... (loadDefinitionsFromPDF() tetap sama persis, tidak perlu disalin ulang) ...
    // --- Salin fungsi loadDefinitionsFromPDF() dari seeder Anda sebelumnya ke sini ---
    private function loadDefinitionsFromPDF()
    {
        $this->defKebersihanTangan = [
            'dim' => 'Keselamatan', 'tuj' => 'Mengukur kepatuhan pemberi layanan kesehatan...', 'sat' => 'Persentase',
            'def' => 'Kepatuhan pemberi layanan kesehatan sebagai dasar...', 'inc' => 'Seluruh peluang...', 'exc' => 'Tidak ada',
            'src' => 'Hasil observasi', 'target' => '85',
        ];
        $this->defAPD = [
            'dim' => 'Keselamatan', 'tuj' => 'Mengukur kepatuhan petugas...', 'sat' => 'Persentase',
            'def' => 'Kepatuhan petugas dalam menggunakan APD...', 'inc' => 'Semua petugas yang terindikasi...', 'exc' => 'Tidak ada',
            'src' => 'Hasil observasi', 'target' => '100',
        ];
        $this->defIdentifikasiPasien = [
            'dim' => 'Keselamatan', 'tuj' => 'Mengukur kepatuhan pemberi pelayanan...', 'sat' => 'Persentase',
            'def' => 'Proses identifikasi yang dilakukan...', 'inc' => 'Semua pemberi pelayanan...', 'exc' => 'Tidak ada',
            'src' => 'Hasil observasi', 'target' => '100',
        ];
        $this->defVisiteDokter = [
            'dim' => 'Berorientasi kepada pasien', 'tuj' => 'Tergambarnya kepatuhan dokter...', 'sat' => 'Persentase',
            'def' => 'Waktu kunjungan dokter...', 'inc' => 'Visite dokter pada pasien rawat inap', 'exc' => 'Pasien yang baru masuk...', 'src' => 'Data sekunder rekam medik', 'target' => '80',
        ];
        $this->defHasilKritisLab = [
            'dim' => 'Tepat waktu, keselamatan', 'tuj' => 'Tergambarnya kecepatan pelayanan...', 'sat' => 'Persentase',
            'def' => 'Waktu yang dibutuhkan sejak hasil...', 'inc' => 'Semua hasil pemeriksaan...', 'exc' => 'Tidak ada', 'src' => 'Catatan Data Laporan Hasil Tes Kritis', 'target' => '100',
        ];
        $this->defPencegahanJatuh = [
            'dim' => 'Keselamatan', 'tuj' => 'Mengukur kepatuhan pemberi pelayanan...', 'sat' => 'Persentase',
            'def' => 'Pelaksanaan ketiga upaya pencegahan jatuh...', 'inc' => 'Pasien rawat inap berisiko tinggi jatuh', 'exc' => 'Pasien yang tidak dapat dilakukan...', 'src' => 'Data sekunder rekam medis', 'target' => '100',
        ];
        $this->defWaktuTanggapKomplain = [
            'dim' => 'Berorientasi pada Pasien', 'tuj' => 'Tergambarnya kecepatan rumah sakit...', 'sat' => 'Persentase',
            'def' => 'Rentang waktu Rumah sakit dalam...', 'inc' => 'Semua komplain...', 'exc' => 'Tidak ada', 'src' => 'Data sekunder dari catatan Komplain', 'target' => '80',
        ];
        $this->defKepuasanPasien = [
            'dim' => 'Berorientasi pada Pasien', 'tuj' => 'Mengukur tingkat kepuasan...', 'sat' => 'Indeks',
            'def' => 'Hasil pendapat dan penilaian pasien...', 'inc' => 'Seluruh pasien', 'exc' => 'Pasien yang tidak kompeten...', 'src' => 'Hasil survei', 'target' => '76.61',
        ];
        $this->defClinicalPathway = [
            'dim' => 'Efektif, integrasi', 'tuj' => 'Untuk menjamin kepatuhan PPA...', 'sat' => 'Persentase',
            'def' => 'Proses pelayanan secara terintegrasi...', 'inc' => 'Pasien yang menderita penyakit...', 'exc' => 'Pasien yang pulang atas permintaan sendiri...', 'src' => 'Data sekunder rekam medis', 'target' => '80',
        ];
        $this->defFornas = [
            'dim' => 'Efisien dan efektif', 'tuj' => 'Terwujudnya pelayanan obat...', 'sat' => 'Persentase',
            'def' => 'Peresepan obat (R/: recipe)...', 'inc' => 'Resep yang dilayani di RS', 'exc' => 'Obat yang diresepkan di luar FORNAS...', 'src' => 'Lembar resep di Instalasi Farmasi', 'target' => '80',
        ];
        $this->defWaktuTungguRajal = [
            'dim' => 'Berorientasi kepada pasien, tepat waktu', 'tuj' => 'Tergambarnya waktu pasien menunggu...', 'sat' => 'Persentase',
            'def' => 'Waktu yang dibutuhkan mulai saat pasien...', 'inc' => 'Pasien yang berobat di rawat jalan', 'exc' => 'Pasien medical check up...', 'src' => 'Catatan Pendaftaran Pasien...', 'target' => '80',
        ];
        $this->defOperasiSC = [
            'dim' => 'Tepat Waktu, Efektif, Keselamatan', 'tuj' => 'Tergambarnya pelayanan kegawatdaruratan...', 'sat' => 'Persentase',
            'def' => 'Waktu yang dibutuhkan pasien untuk...', 'inc' => 'Seksio sesarea emergensi kategori I', 'exc' => 'Tidak ada', 'src' => 'Data sekunder rekam medik, laporan operasi', 'target' => '80',
        ];
        $this->defPenundaanOperasi = [
            'dim' => 'Tepat waktu, efisiensi, berorientasi pada pasien', 'tuj' => 'Tergambarnya ketepatan pelayanan bedah...', 'sat' => 'Persentase',
            'def' => 'Tindakan operasi elektif yang tertunda...', 'inc' => 'Pasien operasi elektif', 'exc' => 'Penundaan operasi atas indikasi medis', 'src' => 'Data sekunder catatan pasien', 'target' => '5',
        ];
        $this->defHAIs = ['dim' => 'Keselamatan', 'tuj' => 'Mengukur angka kejadian infeksi', 'sat' => 'Per mil', 'def' => 'Angka Kejadian Infeksi...', 'inc' => null, 'exc' => null, 'src' => 'Data Surveilans PPI'];
        $this->defKomunikasiEfektif = ['dim' => 'Keselamatan', 'tuj' => 'Mengukur kepatuhan PPA...', 'sat' => 'Persentase', 'def' => 'Komunikasi yang dilakukan antar PPA...', 'inc' => null, 'exc' => null, 'src' => 'Hasil observasi, Rekam Medis'];
    }

    /**
     * ✅ FUNGSI INI SEKARANG HANYA MENGEMBALIKAN DAFTAR INDIKATOR UNIK
     */
    private function getIndicatorData()
    {
        return [
            // Indikator Umum (dari PDF & Teks)
            $this->buildIndicator('KEPATUHAN KEBERSIHAN TANGAN', $this->defKebersihanTangan),
            $this->buildIndicator('KEPATUHAN PENGGUNAAN ALAT PELINDUNG DIRI', $this->defAPD),
            $this->buildIndicator('KEPATUHAN IDENTIFIKASI PASIEN', $this->defIdentifikasiPasien),
            $this->buildIndicator('KEPATUHAN WAKTU VISITE DOKTER', $this->defVisiteDokter),
            $this->buildIndicator('PELAPORAN HASIL KRITIS LABORATORIUM', $this->defHasilKritisLab),
            $this->buildIndicator('KEPATUHAN UPAYA PENCEGAHAN RESIKO PASIEN JATUH', $this->defPencegahanJatuh),
            $this->buildIndicator('KEPATUHAN WAKTU TANGGAP TERHADAP KOMPLAIN', $this->defWaktuTanggapKomplain),
            $this->buildIndicator('KEPUASAN PASIEN', $this->defKepuasanPasien),
            $this->buildIndicator('KEPATUHAN PENGGUNAAN ALUR KLINIS', $this->defClinicalPathway),
            $this->buildIndicator('KEPATUHAN PELAKSANAAN KOMUNIKASI EFEKTIF', $this->defKomunikasiEfektif),
            $this->buildIndicator('ANGKA KEJADIAN HAIs DIRUMAH SAKIT', $this->defHAIs),
            $this->buildIndicator('KEPATUHAN PENGUNAAN FORNAS', $this->defFornas),
            $this->buildIndicator('PEMASANGAN STIKER KUNING PADA PASIEN RESIKO JATUH RAWAT INAP', $this->defPencegahanJatuh),
            $this->buildIndicator('KEPATUHAN PEGAWAI DATANG TEPAT WAKTU', ['dim' => 'Manajerial', 'tuj' => 'Kepatuhan pegawai', 'sat' => 'Persentase', 'def' => 'Kepatuhan pegawai unit terhadap jam masuk kerja', 'inc' => 'Semua pegawai unit', 'exc' => 'Pegawai cuti/libur', 'src' => 'Data absensi']),

            // Indikator Spesifik Unit (dari Teks)
            $this->buildIndicator('KEPATUHAN PENGISIAN RESUME KEPERAWATAN', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kelengkapan resume', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
            $this->buildIndicator('MENGURANGI RESIKO INFEKSI', ['dim' => 'Keselamatan', 'tuj' => 'Mengurangi risiko infeksi', 'sat' => 'Persentase', 'def' => 'Kepatuhan terhadap SOP Pencegahan Infeksi', 'inc' => null, 'exc' => null, 'src' => 'Observasi']),
            $this->buildIndicator('KESLAHAN PETUGAS RUANGAN DALAM PENGORDERAN OBAT SIMRS', ['dim' => 'Keselamatan', 'tuj' => 'Mengurangi kesalahan order', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'SIMRS Farmasi']),
            $this->buildIndicator('KELENGKAPAN RESUME MEDIS RAWAT JALAN', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kelengkapan resume', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
            $this->buildIndicator('WAKTU TUNGGU RAWAT JALAN', $this->defWaktuTungguRajal),
            $this->buildIndicator('KELENGKAPAN RESUME MEDIS RAWAT INAP', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kelengkapan resume', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
            $this->buildIndicator('KELENGKAPAN RESUME MEDIS NEONATUS', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kelengkapan resume', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
            $this->buildIndicator('KEPATUHAN BIDAN MENGEDUKASI DAN MENDOKUMENTASIKAN', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kepatuhan edukasi', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
            $this->buildIndicator('PENGUSULAN UKP DAN GAJI BERKALA TEPAT WAKTU', ['dim' => 'Manajerial', 'tuj' => 'Tepat waktu', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Bagian SDM/Kepegawaian']),
            $this->buildIndicator('KEPATUHAN PEGAWAI MENYELESAIKAN ADMINISTRASI SETELAH TERIMA GAJI', ['dim' => 'Manajerial', 'tuj' => 'Tepat waktu', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Bagian Keuangan']),
            $this->buildIndicator('KETERLAMBATAN PASIEN PINDAH DARI ICU KE RUANGAN', ['dim' => 'Efisiensi', 'tuj' => 'Tergambarnya efisiensi bed', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis, Data Pindah Pasien']),
            $this->buildIndicator('KEPATUHAN DAN KELENGKAPAN DALAM PELAKSANAAN SERAH TERIMA PASIEN', ['dim' => 'Keselamatan', 'tuj' => 'Tergambarnya kepatuhan serah terima', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Formulir Serah Terima, Observasi']),
            $this->buildIndicator('PENUNDAAN OPERASI ELEKTIF', $this->defPenundaanOperasi),
            $this->buildIndicator('WAKTU TANGGAP OPERASI SECIO CESARIA (EMERGENCY)', $this->defOperasiSC),
            $this->buildIndicator('KEJADIAN PASIEN HIPOTENSI INTRA HD', ['dim' => 'Keselamatan', 'tuj' => 'Tergambarnya keamanan pasien HD', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis Pasien HD']),
            $this->buildIndicator('WAKTU TUNGGU PELYANAN OBAT JADI <30 MENIT', ['dim' => 'Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan layanan farmasi', 'sat' => 'Persentase', 'def' => 'Waktu tunggu pelayanan obat non-racikan', 'inc' => 'Semua resep obat jadi', 'exc' => 'Resep obat racikan', 'src' => 'SIMRS Farmasi', 'target' => '85']),
            $this->buildIndicator('RESPON TIME PETUGAS MEMVALIDASI RESEP ERM', ['dim' => 'Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan validasi resep', 'sat' => 'Menit', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'SIMRS Farmasi']),
            $this->buildIndicator('WAKTU KELUAR PEMERIKSAAN DARAH RUTIN <2 JAM', ['dim' => 'Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan layanan lab', 'sat' => 'Persentase', 'def' => 'Waktu tunggu hasil pemeriksaan laboratorium < 140 menit', 'inc' => 'Semua pemeriksaan lab', 'exc' => 'Tidak ada', 'src' => 'Data sekunder', 'target' => '85']),
            $this->buildIndicator('KEPATUHAN PENGEMASAN ALAT MEDIS DAN LABELING ALAT STERIL', ['dim' => 'Keselamatan', 'tuj' => 'Tergambarnya kepatuhan sterilisasi', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Observasi CSSD']),
            $this->buildIndicator('KELENGKAPAN BERKAS RAWAT JALAN UNTUK PENGKLEMAN BPJS', ['dim' => 'Manajerial', 'tuj' => 'Tergambarnya kelengkapan berkas', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Data Klaim BPJS']),
            $this->buildIndicator('PASIEN NON GAWAT DARURAT YANG DI LAYANI', ['dim' => 'Efisiensi', 'tuj' => 'Tergambarnya ketepatan triase', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis IGD']),
            $this->buildIndicator('Emergency Respon Time < 5 Menit', ['dim' => 'Keselamatan, Tepat Waktu', 'tuj' => 'Terselenggaranya pelayanan kegawatdaruratan yang cepat', 'sat' => 'Persentase', 'def' => 'Waktu yang dibutuhkan pasien mulai dari pasien dilakukan triage di IGD sampai mendapatkan pelayanan dokter standar < 5 menit', 'inc' => 'Semua pasien gawat darurat', 'exc' => 'Pasien tidak gawat darurat', 'src' => 'Data sekunder berupa laporan IGD dalam rekam medik', 'target' => '100']),
            $this->buildIndicator('KEPATUHAN PETUGAS MENGINFUT DATA KE SIMRS', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kelengkapan input data', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'SIMRS Radiologi']),
            $this->buildIndicator('PELAPORAN HASIL KRITIS RADIOLOGI', ['dim' => 'Keselamatan, Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan lapor hasil kritis', 'sat' => 'Persentase', 'def' => 'Waktu lapor hasil kritis radiologi', 'inc' => 'Semua hasil radiologi kritis', 'exc' => null, 'src' => 'Catatan Laporan Hasil Kritis Radiologi']),
            $this->buildIndicator('KEPATUHAN PENILAIAN GIZI PASIEN RAWAT INAP 1X24 JAM', ['dim' => 'Keselamatan, Kesinambungan', 'tuj' => 'Tergambarnya kepatuhan asesmen gizi', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
            $this->buildIndicator('VISITE DAN PERENCANAAN DIET PASIEN DIABETES OLEH AHLI GIZI', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kepatuhan visite gizi', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
            $this->buildIndicator('Pencapaian sisa makanan pasien', ['dim' => 'Efisiensi, Berorientasi pasien', 'tuj' => 'Tergambarnya pelayanan gizi baik', 'sat' => 'Persentase', 'def' => 'Standar sisa makanan yang tidak termakan pada pasien 0% berarti tidak ada sisa makanan sama sekali dipiring pasien.', 'inc' => 'Semua jumlah sisa makanan pasien yang tidak di habiskan', 'exc' => 'Tidak ada', 'src' => 'Catatan Jumlah sisa makanan', 'target' => '0']),
            $this->buildIndicator('ketepatan pemberian diit pasien', ['dim' => 'Tepat Waktu, Berorientasi pasien', 'tuj' => 'Tergambarnya pelayanan gizi baik', 'sat' => 'Persentase', 'def' => 'Standar waktu ketepatan pemberian diit pada pasien berarti tidak ada keterlambatan pengantaran diit unuk pasien.', 'inc' => 'Semua pasien rawat inap', 'exc' => 'Tidak ada', 'src' => 'Catatan tepat waktu pemberian diit', 'target' => '85']),
            $this->buildIndicator('HASIL VISUM SELESAI DALAM 2 X 24 JAM PEMERIKSAAN', ['dim' => 'Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan layanan visum', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Data Dokpol']),
            $this->buildIndicator('KEPATUHAN PETUGAS IPRS MELAKUKAN PENGECEKAN LAPORAN UNIT', ['dim' => 'Manajerial', 'tuj' => 'Tergambarnya kepatuhan maintenance', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Logbook IPRS']),
            $this->buildIndicator('KELANGKAPAN PENGISIAN REKAM MEDIS', ['dim' => 'Kesinambungan, Keselamatan', 'tuj' => 'Tergambarnya kelengkapan RM', 'sat' => 'Persentase', 'def' => 'Kelengkapan pencatatan RM pada pasien operasi SC', 'inc' => 'Semua pemberi pelayanan', 'exc' => 'Tidak ada', 'src' => 'Rekam Medis', 'target' => '85']),
            $this->buildIndicator('RESPON TIME PETUGAS IT TERHADAP KOMPLAIN SIMRS < 10 MENIT', ['dim' => 'Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan respon IT', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Logbook IT']),
            $this->buildIndicator('Penginputan Soap pasien pada Simrs', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kelengkapan input SOAP', 'sat' => 'Persentase', 'def' => 'Pemberi Penginputan Soap Di Simrs Pada Pasien', 'inc' => 'Penginputan Soap Di Simrs semua pasien', 'exc' => 'Tidak ada', 'src' => 'Data sekunder inputan soap semua pasien', 'target' => '85']),
            $this->buildIndicator('KEPATUHAN PENGANGKUTAN LIMBAH PADAT TEPAT WAKTU', ['dim' => 'Keselamatan', 'tuj' => 'Tergambarnya kepatuhan angkut limbah', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Observasi IPAL']),
            $this->buildIndicator('KEJADIAN PASIEN FISIOTERAPI DROP OUT', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kesinambungan layanan', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Jadwal Fisioterapi']),
        ];
    }
}
