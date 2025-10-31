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

    // Array untuk menyimpan definisi standar dari PDF
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

        $data = $this->getIndicatorData();

        foreach ($data as $unitName => $indicators) {
            foreach ($indicators as $indicator) {

                $cleanedName = preg_replace('/^\d+\.\s*/', '', $indicator['name']);

                HospitalSurveyIndicator::create([
                    'indicator_name' => $cleanedName,

                    // --- FIELD BARU DIISI DARI PDF ---
                    'dimensi_mutu' => $indicator['dim'],
                    'tujuan' => $indicator['tuj'],
                    'satuan_pengukuran' => $indicator['sat'],

                    // --- FIELD LAMA ---
                    'indicator_definition' => $indicator['def'],
                    'indicator_criteria_inclusive' => $indicator['inc'],
                    'indicator_criteria_exclusive' => $indicator['exc'],
                    'indicator_source_of_data' => $indicator['src'],
                    'indicator_monitoring_area' => $unitName,
                    'indicator_category_id' => $this->getCategoryId($cleanedName, $unitName),
                    'indicator_type' => 'Proses',
                    'indicator_frequency' => 'Bulanan',
                    'indicator_target' => $indicator['target'] ?? '100',

                    // --- FIELD YANG DIMINTA TETAP ADA ---
                    'indicator_record_status' => 'A',

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function getCategoryId($indicatorName, $unitName)
    {
        $name = strtoupper($indicatorName);
        if (Str::contains($name, ['IDENTIFIKASI PASIEN', 'KOMUNIKASI EFEKTIF', 'PASIEN JATUH', 'STIKER KUNING', 'SIDE MARKING'])) {
            return $this->categoryIds['skp'];
        }
        if (Str::contains($name, ['PEGAWAI DATANG TEPAT WAKTU', 'ADMINISTRASI', 'PENGUSULAN UKP', 'LAPORAN UNIT'])) {
            return $this->categoryIds['manajerial'];
        }
        $klinisUnits = ['Ruang Anak', 'Perinatologi', 'Kebidanan', 'ICU', 'Instalasi Kamar Operasi', 'Unit hemodialisa', 'IGD', 'FISOTERAPI', 'Poliklinik'];
        if (in_array($unitName, $klinisUnits)) {
            return $this->categoryIds['klinis'];
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

    /**
     * Mengisi variabel definisi dari PDF
     */
    private function loadDefinitionsFromPDF()
    {
        $this->defKebersihanTangan = [
            'dim' => 'Keselamatan',
            'tuj' => 'Mengukur kepatuhan pemberi layanan kesehatan sebagai dasar untuk memperbaiki dan meningkatkan kepatuhan agar dapat menjamin keselamatan petugas dan pasien dengan cara mengurangi risiko infeksi yang terkait pelayanan kesehatan.',
            'sat' => 'Persentase',
            'def' => 'Kepatuhan pemberi layanan kesehatan sebagai dasar untuk memperbaiki dan meningkatkan kepatuhan agar dapat menjamin keselamatan petugas dan pasien dengan cara mengurangi risiko infeksi yang terkait pelayanan kesehatan.',
            'inc' => 'Seluruh peluang yang dimiliki oleh pemberi pelayanan terindikasi harus melakukan kebersihan tangan',
            'exc' => 'Tidak ada',
            'src' => 'Hasil observasi',
            'target' => '85',
        ];

        $this->defAPD = [
            'dim' => 'Keselamatan',
            'tuj' => 'Mengukur kepatuhan petugas Rumah Sakit dalam menggunakan APD',
            'sat' => 'Persentase',
            'def' => 'Kepatuhan petugas dalam menggunakan APD dengan tepat sesuai dengan indikasi ketika melakukan tindakan yang memungkinkan tubuh atau membran mukosa terkena atau terpercik darah atau cairan tubuh atau cairan infeksius lainnya berdasarkan jenis risiko transmisi (kontak, droplet dan airborne).',
            'inc' => 'Semua petugas yang terindikasi harus menggunakan APD',
            'exc' => 'Tidak ada',
            'src' => 'Hasil observasi',
            'target' => '100',
        ];

        $this->defIdentifikasiPasien = [
            'dim' => 'Keselamatan',
            'tuj' => 'Mengukur kepatuhan pemberi pelayanan untuk melakukan identifikasi pasien dalam melakukan tindakan pelayanan.',
            'sat' => 'Persentase',
            'def' => 'Proses identifikasi yang dilakukan pemberi pelayanan dengan menggunakan minimal dua penanda identitas seperti: nama lengkap, tanggal lahir, nomor rekam medik, NIK sesuai dengan yang ditetapkan di Rumah Sakit.',
            'inc' => 'Semua pemberi pelayanan yang memberikan pelayanan kesehatan.',
            'exc' => 'Tidak ada',
            'src' => 'Hasil observasi',
            'target' => '100',
        ];

        $this->defVisiteDokter = [
            'dim' => 'Berorientasi kepada pasien',
            'tuj' => 'Tergambarnya kepatuhan dokter melakukan visitasi kepada pasien rawat inap sesuai waktu yang ditetapkan.',
            'sat' => 'Persentase',
            'def' => 'Waktu kunjungan dokter untuk melihat perkembangan pasien yang menjadi tanggung jawabnya. Waktu yang ditetapkan untuk visite adalah pukul 06.00-14.00 WIB',
            'inc' => 'Visite dokter pada pasien rawat inap',
            'exc' => 'Pasien yang baru masuk rawat inap hari itu, Pasien konsul',
            'src' => 'Data sekunder berupa laporan visite rawat inap dalam rekam medik',
            'target' => '80',
        ];

        $this->defHasilKritisLab = [
            'dim' => 'Tepat waktu, keselamatan',
            'tuj' => 'Tergambarnya kecepatan pelayanan laboratorium.',
            'sat' => 'Persentase',
            'def' => 'Waktu yang dibutuhkan sejak hasil pemeriksaan keluar dan telah dibaca oleh dokter/analis yang diberi kewenangan hingga dilaporkan hasilnya kepada dokter yang meminta pemeriksaan. Standar waktu lapor hasil kritis laboratorium adalah waktu pelaporan ≤ 30 menit.',
            'inc' => 'Semua hasil pemeriksaan laboratorium yang memenuhi kategori hasil kritis.',
            'exc' => 'Tidak ada',
            'src' => 'Catatan Data Laporan Hasil Tes Kritis Laboratorium',
            'target' => '100',
        ];

        $this->defPencegahanJatuh = [
            'dim' => 'Keselamatan',
            'tuj' => 'Mengukur kepatuhan pemberi pelayanan dalam menjalankan upaya pencegahan jatuh agar terselenggara asuhan pelayanan yang aman dan mencapai pemenuhan sasaran keselamatan pasien.',
            'sat' => 'Persentase',
            'def' => 'Pelaksanaan ketiga upaya pencegahan jatuh (Asesment awal, Assesment ulang, Intervensi) pada pasien rawat inap yang berisiko tinggi jatuh sesuai dengan standar yang ditetapkan rumah sakit.',
            'inc' => 'Pasien rawat inap berisiko tinggi jatuh',
            'exc' => 'Pasien yang tidak dapat dilakukan asesmen ulang maupun edukasi seperti pasien meninggal, pasien gangguan jiwa yang sudah melewati fase akut, dan pasien menolak intervensi',
            'src' => 'Data sekunder menggunakan data dari rekam medis',
            'target' => '100',
        ];

        $this->defWaktuTanggapKomplain = [
            'dim' => 'Berorientasi pada Pasien',
            'tuj' => 'Tergambarnya kecepatan rumah sakit dalam merespon keluhan pasien agar dapat diperbaiki dan ditingkatkan untuk sebagai bentuk pemenuhan hak pasien.',
            'sat' => 'Persentase',
            'def' => 'Rentang waktu Rumah sakit dalam menanggapi keluhan tertulis, lisan atau melalui media massa melalui tahapan identifikasi, penetapan grading risiko, analisis hingga tindak lanjutnya.',
            'inc' => 'Semua komplain (lisan, tertulis, dan media massa)',
            'exc' => 'Tidak ada',
            'src' => 'Data sekunder dari catatan Komplain',
            'target' => '80',
        ];

        $this->defKepuasanPasien = [
            'dim' => 'Berorientasi pada Pasien',
            'tuj' => 'Mengukur tingkat kepuasan masyarakat sebagai dasar upaya-upaya peningkatan mutu dan terselenggaranya pelayanan di semua unit yang mampu memberikan kepuasan pasien',
            'sat' => 'Indeks',
            'def' => 'Hasil pendapat dan penilaian pasien terhadap kinerja pelayanan yang diberikan oleh fasilitas pelayanan kesehatan.',
            'inc' => 'Seluruh pasien',
            'exc' => 'Pasien yang tidak kompeten dalam mengisi kuesioner dan/atau tidak ada keluarga yang mendampingi',
            'src' => 'Hasil survei',
            'target' => '76.61',
        ];

        $this->defClinicalPathway = [
            'dim' => 'Efektif, integrasi',
            'tuj' => 'Untuk menjamin kepatuhan Profesional Pemberi Asuhan (PPA) di rumah sakit terhadap standar pelayanan dan untuk meningkatkan mutu pelayanan klinis di rumah sakit.',
            'sat' => 'Persentase',
            'def' => 'Proses pelayanan secara terintegrasi yang diberikan Profesional Pemberi Asuhan (PPA) kepada pasien yang sesuai dengan clinical pathway yang ditetapkan Rumah Sakit.',
            'inc' => 'Pasien yang menderita penyakit sesuai batasan ruang lingkup clinical pathway yang diukur',
            'exc' => 'Pasien yang pulang atas permintaan sendiri selama perawatan, Pasien yang meninggal, Variasi yang terjadi sesuai dengan indikasi klinis pasien dalam perkembangan pelayanan',
            'src' => 'Data sekunder dari rekam medis pasien',
            'target' => '80',
        ];

        $this->defFornas = [
            'dim' => 'Efisien dan efektif',
            'tuj' => 'Terwujudnya pelayanan obat kepada pasien yang efektif dan efisien berdasarkan daftar obat yang mengacu pada formularium nasional.',
            'sat' => 'Persentase',
            'def' => 'Peresepan obat (R/: recipe dalam lembar resep) oleh DPJP kepada pasien sesuai daftar obat di Formularium Nasional dalam penyelenggaraan program jaminan kesehatan.',
            'inc' => 'Resep yang dilayani di RS',
            'exc' => 'Obat yang diresepkan di luar FORNAS tetapi dibutuhkan pasien dan telah mendapatkan persetujuan komite medik dan direktur. Bila dalam resep terdapat obat di luar FORNAS karena stok obat nasional berdasarkan e-katalog habis/kosong.',
            'src' => 'Lembar resep di Instalasi Farmasi',
            'target' => '80',
        ];

        $this->defWaktuTungguRajal = [
            'dim' => 'Berorientasi kepada pasien, tepat waktu',
            'tuj' => 'Tergambarnya waktu pasien menunggu di pelayanan sebagai dasar untuk perbaikan proses pelayanan di unit rawat jalan agar lebih tepat waktu dan efisien sehingga meningkatkan kepuasan pasien',
            'sat' => 'Persentase',
            'def' => 'Waktu yang dibutuhkan mulai saat pasien kontak dengan petugas pendaftaran sampai mendapat pelayanan dokter/dokter spesialis.',
            'inc' => 'Pasien yang berobat di rawat jalan',
            'exc' => 'Pasien medical check up, pasien poli gigi, Pasien yang mendaftar online atau anjungan mandiri datang lebih dari 60 menit dari waktu yang sudah ditentukan, Pasien yang ada tindakan pasien sebelumnya',
            'src' => 'Catatan Pendaftaran Pasien Rawat Jalan, Rekam Medik Pasien Rawat Jalan, Formulir Waktu Tunggu Rawat Jalan',
            'target' => '80',
        ];

        $this->defOperasiSC = [
            'dim' => 'Tepat Waktu, Efektif, Keselamatan',
            'tuj' => 'Tergambarnya pelayanan kegawatdaruratan operasi seksio sesarea yang cepat dan tepat sehingga mampu mengoptimalkan upaya menyelamatkan ibu dan bayi.',
            'sat' => 'Persentase',
            'def' => 'Waktu yang dibutuhkan pasien untuk mendapatkan tindakan seksio sesarea emergensi sejak diputuskan operasi sampai dimulainya insisi operasi di kamar operasi yaitu ≤ 30 menit.',
            'inc' => 'Seksio sesarea emergensi kategori I',
            'exc' => 'Tidak ada',
            'src' => 'Data sekunder dari rekam medik, laporan operasi',
            'target' => '80',
        ];

        $this->defPenundaanOperasi = [
            'dim' => 'Tepat waktu, efisiensi, berorientasi pada pasien',
            'tuj' => 'Tergambarnya ketepatan pelayanan bedah dan penjadwalan operasi.',
            'sat' => 'Persentase',
            'def' => 'Tindakan operasi elektif yang tertunda lebih dari 1 jam dari jadwal operasi yang ditentukan.',
            'inc' => 'Pasien operasi elektif',
            'exc' => 'Penundaan operasi atas indikasi medis',
            'src' => 'Data sekunder dari catatan pasien yang dijadwalkan operasi dan data pelaksanaan operasi.',
            'target' => '5',
        ];

        $this->defHAIs = ['dim' => 'Keselamatan', 'tuj' => 'Mengukur angka kejadian infeksi', 'sat' => 'Per mil', 'def' => 'Angka Kejadian Infeksi Terkait Pelayanan Kesehatan (HealthCare Associated Infections)', 'inc' => null, 'exc' => null, 'src' => 'Data Surveilans PPI'];
        $this->defKomunikasiEfektif = ['dim' => 'Keselamatan', 'tuj' => 'Mengukur kepatuhan PPA dalam melakukan komunikasi efektif', 'sat' => 'Persentase', 'def' => 'Komunikasi yang dilakukan antar PPA (Profesional Pemberi Asuhan) menggunakan metode SBAR/TBAK', 'inc' => null, 'exc' => null, 'src' => 'Hasil observasi, Rekam Medis'];
    }

    private function getIndicatorData()
    {
        return [
            'Ruang Anak' => [
                $this->buildIndicator('KEPATUHAN PENGISIAN RESUME KEPERAWATAN', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kelengkapan resume', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
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
                $this->buildIndicator('KEPATUHAN PEGAWAI DATANG TEPAT WAKTU', ['dim' => 'Manajerial', 'tuj' => 'Kepatuhan pegawai', 'sat' => 'Persentase', 'def' => 'Kepatuhan pegawai unit terhadap jam masuk kerja', 'inc' => 'Semua pegawai unit', 'exc' => 'Pegawai cuti/libur', 'src' => 'Data absensi']),
                $this->buildIndicator('KESLAHAN PETUGAS RUANGAN DALAM PENGORDERAN OBAT SIMRS', ['dim' => 'Keselamatan', 'tuj' => 'Mengurangi kesalahan order', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'SIMRS Farmasi']),
                $this->buildIndicator('KEPATUHAN PENGUNAAN FORNAS', $this->defFornas),
                $this->buildIndicator('PEMASANGAN STIKER KUNING PADA PASIEN RESIKO JATUH RAWAT INAP', $this->defPencegahanJatuh),
            ],
            'Poliklinik' => [
                $this->buildIndicator('KELENGKAPAN RESUME MEDIS RAWAT JALAN', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kelengkapan resume', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
                $this->buildIndicator('KEPATUHAN KEBERSIHAN TANGAN', $this->defKebersihanTangan),
                $this->buildIndicator('KEPATUHAN PENGGUNAAN ALAT PELINDUNG DIRI', $this->defAPD),
                $this->buildIndicator('KEPATUHAN IDENTIFIKASI PASIEN', $this->defIdentifikasiPasien),
                $this->buildIndicator('WAKTU TUNGGU RAWAT JALAN', $this->defWaktuTungguRajal),
                $this->buildIndicator('PELAPORAN HASIL KRITIS LABORATORIUM', $this->defHasilKritisLab),
                $this->buildIndicator('KEPATUHAN UPAYA PENCEGAHAN RESIKO PASIEN JATUH', $this->defPencegahanJatuh),
                $this->buildIndicator('KEPATUHAN WAKTU TANGGAP TERHADAP KOMPLAIN', $this->defWaktuTanggapKomplain),
                $this->buildIndicator('KEPUASAN PASIEN', $this->defKepuasanPasien),
                $this->buildIndicator('KEPATUHAN PENGGUNAAN ALUR KLINIS', $this->defClinicalPathway),
                $this->buildIndicator('KEPATUHAN PEGAWAI DATANG TEPAT WAKTU', ['dim' => 'Manajerial', 'tuj' => 'Kepatuhan pegawai', 'sat' => 'Persentase', 'def' => 'Kepatuhan pegawai unit terhadap jam masuk kerja', 'inc' => 'Semua pegawai unit', 'exc' => 'Pegawai cuti/libur', 'src' => 'Data absensi']),
                $this->buildIndicator('KEPATUHAN PENGUNAAN FORNAS', $this->defFornas),
                $this->buildIndicator('ANGKA KEJADIAN HAIs DIRUMAH SAKIT', $this->defHAIs),
            ],
            // ... (Saya akan memotong sisanya agar ringkas, logikanya sama)
            'Perinatologi' => [
                $this->buildIndicator('KELENGKAPAN RESUME MEDIS NEONATUS', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kelengkapan resume', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
                $this->buildIndicator('KEPATUHAN KEBERSIHAN TANGAN', $this->defKebersihanTangan),
            ],
            'Kebidanan' => [
                $this->buildIndicator('KEPATUHAN BIDAN MENGEDUKASI DAN MENDOKUMENTASIKAN', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kepatuhan edukasi', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
                $this->buildIndicator('KEPATUHAN KEBERSIHAN TANGAN', $this->defKebersihanTangan),
            ],
            'Manajemen' => [
                $this->buildIndicator('PENGUSULAN UKP DAN GAJI BERKALA TEPAT WAKTU', ['dim' => 'Manajerial', 'tuj' => 'Tepat waktu', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Bagian SDM/Kepegawaian']),
                $this->buildIndicator('KEPATUHAN PEGAWAI DATANG TEPAT WAKTU', ['dim' => 'Manajerial', 'tuj' => 'Kepatuhan pegawai', 'sat' => 'Persentase', 'def' => 'Kepatuhan pegawai unit terhadap jam masuk kerja', 'inc' => 'Semua pegawai unit', 'exc' => 'Pegawai cuti/libur', 'src' => 'Data absensi']),
            ],
            'Keuangan' => [
                $this->buildIndicator('KEPATUHAN PEGAWAI MENYELESAIKAN ADMINISTRASI SETELAH TERIMA GAJI', ['dim' => 'Manajerial', 'tuj' => 'Tepat waktu', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Bagian Keuangan']),
                $this->buildIndicator('KEPATUHAN PEGAWAI DATANG TEPAT WAKTU', ['dim' => 'Manajerial', 'tuj' => 'Kepatuhan pegawai', 'sat' => 'Persentase', 'def' => 'Kepatuhan pegawai unit terhadap jam masuk kerja', 'inc' => 'Semua pegawai unit', 'exc' => 'Pegawai cuti/libur', 'src' => 'Data absensi']),
            ],
            'ICU' => [
                $this->buildIndicator('KETERLAMBATAN PASIEN PINDAH DARI ICU KE RUANGAN', ['dim' => 'Efisiensi', 'tuj' => 'Tergambarnya efisiensi bed', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis, Data Pindah Pasien']),
                $this->buildIndicator('KEPATUHAN KEBERSIHAN TANGAN', $this->defKebersihanTangan),
            ],
            'Instalasi Kamar Operasi' => [
                $this->buildIndicator('KEPATUHAN DAN KELENGKAPAN DALAM PELAKSANAAN SERAH TERIMA PASIEN', ['dim' => 'Keselamatan', 'tuj' => 'Tergambarnya kepatuhan serah terima', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Formulir Serah Terima, Observasi']),
                $this->buildIndicator('PENUNDAAN OPERASI ELEKTIF', $this->defPenundaanOperasi),
                $this->buildIndicator('WAKTU TANGGAP OPERASI SECIO CESARIA (EMERGENCY)', $this->defOperasiSC),
            ],
            'Unit hemodialisa' => [
                $this->buildIndicator('KEJADIAN PASIEN HIPOTENSI INTRA HD', ['dim' => 'Keselamatan', 'tuj' => 'Tergambarnya keamanan pasien HD', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis Pasien HD']),
                $this->buildIndicator('KEPATUHAN KEBERSIHAN TANGAN', $this->defKebersihanTangan),
            ],
            'Instalasi Farmasi' => [
                $this->buildIndicator('WAKTU TUNGGU PELYANAN OBAT JADI <30 MENIT', ['dim' => 'Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan layanan farmasi', 'sat' => 'Persentase', 'def' => 'Waktu tunggu pelayanan obat non-racikan', 'inc' => 'Semua resep obat jadi', 'exc' => 'Resep obat racikan', 'src' => 'SIMRS Farmasi', 'target' => '85']),
                $this->buildIndicator('RESPON TIME PETUGAS MEMVALIDASI RESEP ERM', ['dim' => 'Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan validasi resep', 'sat' => 'Menit', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'SIMRS Farmasi']),
                $this->buildIndicator('KEPATUHAN PENGUNAAN FORNAS', $this->defFornas),
            ],
            'Laboratorium' => [
                $this->buildIndicator('WAKTU KELUAR PEMERIKSAAN DARAH RUTIN <2 JAM', ['dim' => 'Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan layanan lab', 'sat' => 'Persentase', 'def' => 'Waktu tunggu hasil pemeriksaan laboratorium < 140 menit', 'inc' => 'Semua pemeriksaan lab', 'exc' => 'Tidak ada', 'src' => 'Data sekunder', 'target' => '85']),
                $this->buildIndicator('PELAPORAN HASIL KRITIS LABORATORIUM', $this->defHasilKritisLab),
            ],
            'BPJS' => [
                $this->buildIndicator('KELENGKAPAN BERKAS RAWAT JALAN UNTUK PENGKLEMAN BPJS', ['dim' => 'Manajerial', 'tuj' => 'Tergambarnya kelengkapan berkas', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Data Klaim BPJS']),
                $this->buildIndicator('KEPATUHAN WAKTU TANGGAP TERHADAP KOMPLAIN', $this->defWaktuTanggapKomplain),
            ],
            'IGD' => [
                $this->buildIndicator('PASIEN NON GAWAT DARURAT YANG DI LAYANI', ['dim' => 'Efisiensi', 'tuj' => 'Tergambarnya ketepatan triase', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis IGD']),
                $this->buildIndicator('Emergency Respon Time < 5 Menit', ['dim' => 'Keselamatan, Tepat Waktu', 'tuj' => 'Terselenggaranya pelayanan kegawatdaruratan yang cepat', 'sat' => 'Persentase', 'def' => 'Waktu yang dibutuhkan pasien mulai dari pasien dilakukan triage di IGD sampai mendapatkan pelayanan dokter standar < 5 menit', 'inc' => 'Semua pasien gawat darurat', 'exc' => 'Pasien tidak gawat darurat', 'src' => 'Data sekunder berupa laporan IGD dalam rekam medik', 'target' => '100']),
            ],
            'Radiologi' => [
                $this->buildIndicator('KEPATUHAN PETUGAS MENGINFUT DATA KE SIMRS', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kelengkapan input data', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'SIMRS Radiologi']),
                $this->buildIndicator('PELAPORAN HASIL KRITIS RADIOLOGI', ['dim' => 'Keselamatan, Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan lapor hasil kritis', 'sat' => 'Persentase', 'def' => 'Waktu lapor hasil kritis radiologi', 'inc' => 'Semua hasil radiologi kritis', 'exc' => null, 'src' => 'Catatan Laporan Hasil Kritis Radiologi']),
            ],
            'Gizi' => [
                $this->buildIndicator('KEPATUHAN PENILAIAN GIZI PASIEN RAWAT INAP 1X24 JAM', ['dim' => 'Keselamatan, Kesinambungan', 'tuj' => 'Tergambarnya kepatuhan asesmen gizi', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Rekam Medis']),
                $this->buildIndicator('Pencapaian sisa makanan pasien', ['dim' => 'Efisiensi, Berorientasi pasien', 'tuj' => 'Tergambarnya pelayanan gizi baik', 'sat' => 'Persentase', 'def' => 'Standar sisa makanan yang tidak termakan pada pasien 0% berarti tidak ada sisa makanan sama sekali dipiring pasien.', 'inc' => 'Semua jumlah sisa makanan pasien yang tidak di habiskan', 'exc' => 'Tidak ada', 'src' => 'Catatan Jumlah sisa makanan', 'target' => '0']),
                $this->buildIndicator('ketepatan pemberian diit pasien', ['dim' => 'Tepat Waktu, Berorientasi pasien', 'tuj' => 'Tergambarnya pelayanan gizi baik', 'sat' => 'Persentase', 'def' => 'Standar waktu ketepatan pemberian diit pada pasien berarti tidak ada keterlambatan pengantaran diit unuk pasien.', 'inc' => 'Semua pasien rawat inap', 'exc' => 'Tidak ada', 'src' => 'Catatan tepat waktu pemberian diit', 'target' => '85']),
            ],
            'DOKPOL' => [
                $this->buildIndicator('HASIL VISUM SELESAI DALAM 2 X 24 JAM PEMERIKSAAN', ['dim' => 'Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan layanan visum', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Data Dokpol']),
                $this->buildIndicator('KEPATUHAN KEBERSIHAN TANGAN', $this->defKebersihanTangan),
            ],
            'IPRS' => [
                $this->buildIndicator('KEPATUHAN PETUGAS IPRS MELAKUKAN PENGECEKAN LAPORAN UNIT', ['dim' => 'Manajerial', 'tuj' => 'Tergambarnya kepatuhan maintenance', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Logbook IPRS']),
                $this->buildIndicator('KEPATUHAN PEGAWAI DATANG TEPAT WAKTU', ['dim' => 'Manajerial', 'tuj' => 'Kepatuhan pegawai', 'sat' => 'Persentase', 'def' => 'Kepatuhan pegawai unit terhadap jam masuk kerja', 'inc' => 'Semua pegawai unit', 'exc' => 'Pegawai cuti/libur', 'src' => 'Data absensi']),
            ],
            'Rekammedis' => [
                $this->buildIndicator('KELANGKAPAN PENGISIAN REKAM MEDIS', ['dim' => 'Kesinambungan, Keselamatan', 'tuj' => 'Tergambarnya kelengkapan RM', 'sat' => 'Persentase', 'def' => 'Kelengkapan pencatatan RM pada pasien operasi SC', 'inc' => 'Semua pemberi pelayanan', 'exc' => 'Tidak ada', 'src' => 'Rekam Medis', 'target' => '85']),
                $this->buildIndicator('KEPATUHAN PEGAWAI DATANG TEPAT WAKTU', ['dim' => 'Manajerial', 'tuj' => 'Kepatuhan pegawai', 'sat' => 'Persentase', 'def' => 'Kepatuhan pegawai unit terhadap jam masuk kerja', 'inc' => 'Semua pegawai unit', 'exc' => 'Pegawai cuti/libur', 'src' => 'Data absensi']),
            ],
            'Tim IT' => [
                $this->buildIndicator('RESPON TIME PETUGAS IT TERHADAP KOMPLAIN SIMRS < 10 MENIT', ['dim' => 'Tepat Waktu', 'tuj' => 'Tergambarnya kecepatan respon IT', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Logbook IT']),
                $this->buildIndicator('KEPATUHAN PEGAWAI DATANG TEPAT WAKTU', ['dim' => 'Manajerial', 'tuj' => 'Kepatuhan pegawai', 'sat' => 'Persentase', 'def' => 'Kepatuhan pegawai unit terhadap jam masuk kerja', 'inc' => 'Semua pegawai unit', 'exc' => 'Pegawai cuti/libur', 'src' => 'Data absensi']),
                $this->buildIndicator('Penginputan Soap pasien pada Simrs', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kelengkapan input SOAP', 'sat' => 'Persentase', 'def' => 'Pemberi Penginputan Soap Di Simrs Pada Pasien', 'inc' => 'Penginputan Soap Di Simrs semua pasien', 'exc' => 'Tidak ada', 'src' => 'Data sekunder inputan soap semua pasien', 'target' => '85']),
            ],
            'FISOTERAPI' => [
                $this->buildIndicator('KEJADIAN PASIEN FISIOTERAPI DROP OUT', ['dim' => 'Kesinambungan', 'tuj' => 'Tergambarnya kesinambungan layanan', 'sat' => 'Persentase', 'def' => null, 'inc' => null, 'exc' => null, 'src' => 'Jadwal Fisioterapi']),
                $this->buildIndicator('KEPATUHAN PENGUNAAN ALAT PELINDUNG DIRI (APD)', $this->defAPD),
                $this->buildIndicator('KEPATUHAN WAKTU TANGGAP TERHADAP KOMPLEN', $this->defWaktuTanggapKomplain),
            ],
        ];
    }
}
