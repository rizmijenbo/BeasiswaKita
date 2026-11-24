<?php

namespace Database\Seeders;

use App\Models\DocumentGuide;
use App\Models\Scholarship;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Data demo BeasiswaKita.
 *
 * PENTING:
 *  - Nama program, penyelenggara, dan ringkasan persyaratan didasarkan pada
 *    program beasiswa nyata di Indonesia, tetapi ditulis ringkas/umum. Ketentuan
 *    resmi berubah tiap periode; selalu verifikasi di situs resmi penyelenggara.
 *  - Tanggal buka/tutup dibuat RELATIF terhadap hari seeding (bukan jadwal resmi)
 *    agar countdown & kalender selalu terlihat hidup saat demo.
 *  - Seeder ini idempotent: aman dijalankan ulang (`php artisan db:seed`)
 *    untuk "menyegarkan" tanggal demo.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedDocumentGuides();
            $this->seedScholarships();
        });
    }

    // ------------------------------------------------------------------
    // Panduan dokumen
    // ------------------------------------------------------------------

    private function seedDocumentGuides(): void
    {
        $guides = [
            [
                'slug' => 'cv',
                'title' => 'Curriculum Vitae (CV)',
                'icon' => 'person-vcard',
                'summary' => 'Ringkasan pendidikan, prestasi, organisasi, dan pengalaman Anda dalam 1–2 halaman.',
                'steps' => [
                    'Kumpulkan data: riwayat pendidikan, prestasi, organisasi, kepanitiaan, kegiatan sosial, dan keterampilan.',
                    'Pilih format sederhana dan rapi (1–2 halaman, font mudah dibaca seperti Calibri atau Arial 11 pt).',
                    'Susun urutan: data diri singkat, pendidikan, prestasi, pengalaman organisasi/kerja, lalu keterampilan.',
                    'Tulis setiap poin dengan kata kerja aktif dan hasil yang terukur (mis. "Memimpin 20 anggota OSIS menyelenggarakan lomba tingkat kota").',
                    'Periksa ejaan, konsistensi tanggal, dan pastikan email serta nomor telepon aktif.',
                    'Simpan sebagai PDF dengan nama file jelas, misalnya CV_NamaLengkap.pdf.',
                ],
                'tips' => [
                    'Urutkan dari yang terbaru dan paling relevan dengan beasiswa yang dituju.',
                    'Gunakan alamat email yang profesional (hindari nama panggilan atau alamat bergaya bercanda).',
                    'Foto tidak wajib kecuali diminta penyelenggara.',
                ],
            ],
            [
                'slug' => 'motivation-letter',
                'title' => 'Motivation Letter / Esai Motivasi',
                'icon' => 'pencil-square',
                'summary' => 'Tulisan yang menjelaskan siapa Anda, mengapa layak menerima beasiswa, dan rencana masa depan Anda.',
                'steps' => [
                    'Baca panduan esai penyelenggara: batas jumlah kata, topik, dan bahasa yang diminta.',
                    'Tulis kerangka: pembuka yang kuat, latar belakang Anda, alasan memilih beasiswa/jurusan, rencana studi, dan kontribusi setelah lulus.',
                    'Gunakan cerita pengalaman nyata sebagai bukti (tantangan yang dihadapi, apa yang dilakukan, apa hasilnya).',
                    'Hubungkan tujuan pribadi Anda dengan misi penyelenggara beasiswa.',
                    'Revisi minimal dua kali, lalu minta guru, dosen, atau teman membaca dan memberi masukan.',
                    'Pastikan jumlah kata sesuai ketentuan, lalu simpan sebagai PDF.',
                ],
                'tips' => [
                    'Jangan menyalin contoh esai dari internet; penyeleksi mudah mengenalinya.',
                    'Tunjukkan, jangan hanya mengatakan: ceritakan kejadian konkret daripada sekadar menyebut "saya pekerja keras".',
                    'Akhiri dengan rencana konkret dan dampak yang ingin Anda berikan.',
                ],
            ],
            [
                'slug' => 'surat-rekomendasi',
                'title' => 'Surat Rekomendasi',
                'icon' => 'envelope-paper',
                'summary' => 'Surat dari guru, dosen, atau pembimbing yang menilai kemampuan dan karakter Anda.',
                'steps' => [
                    'Pilih perekomendasi yang mengenal Anda dengan baik (wali kelas, guru BK, dosen wali, atau pembimbing organisasi).',
                    'Minta paling lambat 2–3 minggu sebelum tenggat, dan jelaskan beasiswa yang Anda tuju.',
                    'Berikan bahan pendukung: CV, draf esai, dan daftar prestasi agar surat lebih spesifik.',
                    'Tanyakan format yang dibutuhkan: surat bertanda tangan basah, kop institusi, atau formulir online dari penyelenggara.',
                    'Pastikan surat memuat nama, jabatan, kontak perekomendasi, tanggal, tanda tangan, dan cap institusi bila ada.',
                    'Ingatkan dengan sopan menjelang tenggat, lalu ucapkan terima kasih setelah selesai.',
                ],
                'tips' => [
                    'Surat yang spesifik dan personal lebih kuat daripada surat yang umum.',
                    'Beberapa beasiswa meminta perekomendasi mengunggah langsung secara online; cek ketentuannya.',
                ],
            ],
            [
                'slug' => 'transkrip-rapor',
                'title' => 'Transkrip Nilai / Rapor',
                'icon' => 'journal-text',
                'summary' => 'Bukti prestasi akademik resmi: rapor untuk siswa SMP/SMA, transkrip untuk mahasiswa.',
                'steps' => [
                    'Siswa: minta salinan rapor semester terbaru ke wali kelas atau tata usaha. Mahasiswa: unduh atau minta transkrip dari sistem akademik/BAAK kampus.',
                    'Minta legalisir (cap dan tanda tangan resmi) bila penyelenggara mensyaratkannya.',
                    'Pindai (scan) semua halaman penting: identitas, nilai, dan keterangan naik kelas / IPK.',
                    'Pastikan hasil scan jelas, tidak terpotong, dan semua tulisan terbaca.',
                    'Gabungkan menjadi satu PDF dan cek ukuran file sesuai batas unggah (umumnya 1–2 MB).',
                ],
                'tips' => [
                    'Proses legalisir bisa memakan beberapa hari kerja, jadi urus lebih awal.',
                    'Untuk transkrip mahasiswa, pastikan IPK yang tercantum adalah yang paling mutakhir.',
                ],
            ],
            [
                'slug' => 'sktm',
                'title' => 'Surat Keterangan Tidak Mampu (SKTM)',
                'icon' => 'file-earmark-check',
                'summary' => 'Surat resmi dari pemerintah desa/kelurahan yang menerangkan kondisi ekonomi keluarga.',
                'steps' => [
                    'Siapkan KTP dan Kartu Keluarga orang tua/wali, serta bukti pendukung bila ada (KIP, KKS, PKH, atau slip penghasilan).',
                    'Minta surat pengantar dari Ketua RT dan RW.',
                    'Bawa pengantar ke kantor kelurahan/desa dan sampaikan keperluan untuk pengajuan beasiswa.',
                    'Isi formulir atau surat pernyataan yang diminta, lalu tunggu surat diterbitkan dan ditandatangani.',
                    'Jika diminta penyelenggara, lanjutkan pengesahan ke kecamatan.',
                    'Pindai surat dan simpan aslinya dengan baik untuk verifikasi.',
                ],
                'tips' => [
                    'Syarat dan nama dokumen bisa berbeda tiap daerah; tanyakan langsung ke kantor kelurahan/desa.',
                    'Perhatikan masa berlaku surat; sebagian penyelenggara hanya menerima surat yang diterbitkan dalam beberapa bulan terakhir.',
                    'Isi data dengan jujur. Pemalsuan data dapat menggugurkan beasiswa dan berakibat hukum.',
                ],
            ],
            [
                'slug' => 'kk-ktp',
                'title' => 'KK, KTP, atau Kartu Identitas Anak (KIA)',
                'icon' => 'person-badge',
                'summary' => 'Dokumen identitas dan kependudukan untuk verifikasi data diri dan keluarga.',
                'steps' => [
                    'Siapkan Kartu Keluarga (KK) terbaru dan KTP/KIA Anda serta orang tua/wali bila diminta.',
                    'Pastikan nama, NIK, dan tanggal lahir sama persis dengan data di ijazah/rapor dan sistem sekolah.',
                    'Jika ada data yang berbeda, urus perbaikan ke Dinas Kependudukan dan Pencatatan Sipil lebih awal.',
                    'Pindai dokumen dengan pencahayaan baik; semua sisi dan tulisan harus terlihat.',
                    'Simpan dalam format yang diminta (umumnya PDF atau JPG) dan periksa ukuran filenya.',
                ],
                'tips' => [
                    'Jangan mengunggah dokumen identitas ke situs yang tidak resmi. Hanya gunakan situs resmi penyelenggara.',
                    'Perbaikan data kependudukan bisa memakan waktu, jadi cek kecocokan data sedini mungkin.',
                ],
            ],
            [
                'slug' => 'sertifikat-prestasi',
                'title' => 'Sertifikat Prestasi & Organisasi',
                'icon' => 'award',
                'summary' => 'Bukti prestasi akademik/non-akademik, kegiatan organisasi, dan pengalaman sukarela.',
                'steps' => [
                    'Kumpulkan semua sertifikat, piagam, dan surat keterangan organisasi atau kepanitiaan.',
                    'Pilih yang paling relevan dan terbaru; urutkan dari yang tertinggi tingkatnya (internasional, nasional, provinsi, dan seterusnya).',
                    'Pindai tiap dokumen dengan jelas, lalu beri nama file yang mudah dikenali.',
                    'Gabungkan menjadi satu PDF bila penyelenggara hanya menyediakan satu kolom unggah.',
                    'Cocokkan dengan daftar prestasi di CV agar konsisten.',
                ],
                'tips' => [
                    'Kualitas lebih penting daripada kuantitas; pilih yang benar-benar mendukung profil Anda.',
                    'Bila sertifikat hilang, minta surat keterangan ke penyelenggara kegiatan atau sekolah/kampus.',
                ],
            ],
            [
                'slug' => 'sertifikat-bahasa',
                'title' => 'Sertifikat Kemampuan Bahasa (TOEFL/IELTS)',
                'icon' => 'translate',
                'summary' => 'Bukti kemampuan bahasa Inggris, dibutuhkan terutama untuk beasiswa pascasarjana atau studi luar negeri.',
                'steps' => [
                    'Cek jenis tes dan skor minimum yang diterima penyelenggara (mis. TOEFL iBT, TOEFL ITP, atau IELTS).',
                    'Cek masa berlaku sertifikat; umumnya hasil tes hanya berlaku sekitar 2 tahun.',
                    'Daftar tes di lembaga resmi, bayar biaya, dan pilih jadwal minimal 1–2 bulan sebelum tenggat.',
                    'Berlatih dengan soal latihan resmi agar siap dengan format ujian.',
                    'Setelah hasil keluar, unduh atau minta sertifikat resmi, lalu pindai dan simpan dalam PDF.',
                ],
                'tips' => [
                    'Jadwalkan tes lebih awal agar masih ada kesempatan mengulang bila skor belum cukup.',
                    'Pastikan nama di sertifikat sama persis dengan nama di KTP/paspor.',
                ],
            ],
        ];

        foreach ($guides as $i => $guide) {
            DocumentGuide::updateOrCreate(
                ['slug' => $guide['slug']],
                $guide + ['sort_order' => $i + 1],
            );
        }
    }

    // ------------------------------------------------------------------
    // Beasiswa
    // ------------------------------------------------------------------

    private function seedScholarships(): void
    {
        $today = Carbon::today();

        $data = [
            [
                'slug' => 'pip-smp',
                'name' => 'Program Indonesia Pintar (PIP) Jenjang SMP/MTs',
                'organizer' => 'Kementerian Pendidikan Dasar dan Menengah (Kemendikdasmen)',
                'level' => 'SMP',
                'summary' => 'Bantuan tunai pendidikan agar siswa SMP/MTs dari keluarga kurang mampu tetap dapat bersekolah.',
                'description' => 'Program Indonesia Pintar (PIP) adalah bantuan tunai pendidikan dari pemerintah bagi peserta didik usia sekolah dari keluarga kurang mampu atau rentan. Tujuannya membantu biaya pribadi siswa agar tidak putus sekolah. PIP berbasis kebutuhan ekonomi, bukan prestasi, dan pengusulannya umumnya dilakukan melalui sekolah.',
                'benefits' => [
                    'Bantuan tunai pendidikan yang disalurkan ke rekening atas nama siswa.',
                    'Dapat digunakan untuk membeli perlengkapan sekolah, seragam, transportasi, dan kebutuhan belajar lain.',
                    'Besaran bantuan mengikuti jenjang pendidikan (cek ketentuan terbaru di situs resmi).',
                ],
                'open_date' => $today->copy()->subDays(45),
                'deadline' => $today->copy()->addDays(5),
                'official_url' => 'https://pip.kemdikbud.go.id',
                'requirements' => [
                    'akademik' => [
                        'Terdaftar sebagai peserta didik aktif di SMP/MTs/SMPLB atau Paket B.',
                        'PIP berbasis kebutuhan, bukan prestasi: tidak ada nilai rapor minimum.',
                    ],
                    'administrasi' => [
                        'Tercatat di Dapodik/EMIS sekolah dengan NISN dan NIK yang valid.',
                        'Memiliki Kartu Indonesia Pintar (KIP) atau diusulkan oleh sekolah.',
                        'Bersedia membuka rekening penerima bantuan di bank penyalur saat dana ditetapkan.',
                    ],
                    'umum' => [
                        'Berusia 6–21 tahun.',
                        'Berasal dari keluarga pemegang KIP/KKS/PKH, terdaftar di data kesejahteraan sosial, atau termasuk kelompok prioritas (yatim/piatu, penyandang disabilitas, korban bencana, rentan putus sekolah).',
                    ],
                ],
                'documents' => ['kk-ktp', 'sktm', 'transkrip-rapor'],
            ],
            [
                'slug' => 'pip-sma-smk',
                'name' => 'Program Indonesia Pintar (PIP) Jenjang SMA/SMK/MA',
                'organizer' => 'Kementerian Pendidikan Dasar dan Menengah (Kemendikdasmen)',
                'level' => 'SMA',
                'summary' => 'Bantuan tunai pendidikan untuk siswa SMA/SMK/MA dari keluarga kurang mampu agar lanjut sekolah hingga lulus.',
                'description' => 'PIP jenjang menengah atas membantu siswa SMA, SMK, dan MA dari keluarga kurang mampu memenuhi biaya pribadi pendidikan. Siswa penerima dapat memakai bantuan untuk keperluan sekolah dan penunjang belajar. Pendaftaran biasanya dilakukan lewat sekolah, atau melalui usulan mandiri pada kanal resmi PIP.',
                'benefits' => [
                    'Bantuan tunai pendidikan untuk siswa SMA/SMK/MA yang disalurkan ke rekening siswa.',
                    'Membantu biaya buku, seragam, transportasi, praktik (SMK), dan kebutuhan sekolah lain.',
                    'Besaran bantuan mengikuti jenjang pendidikan (cek ketentuan terbaru di situs resmi).',
                ],
                'open_date' => $today->copy()->subDays(40),
                'deadline' => $today->copy()->addDays(9),
                'official_url' => 'https://pip.kemdikbud.go.id',
                'requirements' => [
                    'akademik' => [
                        'Terdaftar sebagai peserta didik aktif di SMA/SMK/MA/SMALB atau Paket C.',
                        'Tidak ada syarat nilai minimum; prioritas pada siswa yang berisiko putus sekolah.',
                    ],
                    'administrasi' => [
                        'Tercatat di Dapodik/EMIS dengan NISN dan NIK yang valid.',
                        'Memiliki KIP, atau mengajukan usulan melalui sekolah atau kanal resmi PIP.',
                        'Memiliki rekening penerima bantuan di bank penyalur.',
                    ],
                    'umum' => [
                        'Berusia 6–21 tahun.',
                        'Berasal dari keluarga kurang mampu: pemegang KIP/KKS/PKH, terdaftar di data kesejahteraan sosial, atau termasuk kelompok prioritas lain sesuai ketentuan.',
                    ],
                ],
                'documents' => ['kk-ktp', 'sktm', 'transkrip-rapor'],
            ],
            [
                'slug' => 'kip-kuliah',
                'name' => 'KIP Kuliah (Kartu Indonesia Pintar Kuliah)',
                'organizer' => 'Kementerian Pendidikan Tinggi, Sains, dan Teknologi (Kemdiktisaintek)',
                'level' => 'Kuliah',
                'summary' => 'Bantuan biaya kuliah dan biaya hidup bagi lulusan SMA/SMK sederajat dari keluarga kurang mampu yang berpotensi akademik baik.',
                'description' => 'KIP Kuliah adalah bantuan pembiayaan pendidikan tinggi dari pemerintah bagi calon mahasiswa berprestasi dari keluarga kurang mampu. Penerima dapat kuliah di perguruan tinggi negeri maupun swasta sesuai ketentuan program. Pendaftaran dilakukan melalui portal resmi KIP Kuliah, dengan verifikasi data ekonomi dan data penerimaan di perguruan tinggi.',
                'benefits' => [
                    'Bantuan biaya pendidikan (UKT/SPP) sesuai ketentuan program.',
                    'Bantuan biaya hidup bulanan (besaran bervariasi menurut wilayah).',
                    'Berlaku selama masa studi dengan evaluasi berkala sesuai ketentuan.',
                ],
                'open_date' => $today->copy()->subDays(30),
                'deadline' => $today->copy()->addDays(13),
                'official_url' => 'https://kip-kuliah.kemdikbud.go.id',
                'requirements' => [
                    'akademik' => [
                        'Lulusan SMA/SMK/MA atau sederajat pada tahun berjalan atau maksimal 2 tahun terakhir.',
                        'Memiliki potensi akademik baik dan lolos seleksi masuk perguruan tinggi (SNBP, SNBT, atau jalur mandiri).',
                        'Diterima di perguruan tinggi dan program studi yang memenuhi ketentuan akreditasi.',
                    ],
                    'administrasi' => [
                        'Membuat akun dan mendaftar di portal resmi KIP Kuliah.',
                        'Mengunggah bukti kondisi ekonomi (mis. KIP/KKS/PKH, surat keterangan penghasilan, atau SKTM).',
                        'Melengkapi dokumen pendukung verifikasi, misalnya foto rumah dan identitas orang tua/wali.',
                    ],
                    'umum' => [
                        'Warga Negara Indonesia.',
                        'Berasal dari keluarga kurang mampu secara ekonomi atau pemegang KIP/KKS/PKH.',
                        'Bersedia mematuhi seluruh ketentuan dan pakta integritas penerima bantuan.',
                    ],
                ],
                'documents' => ['kk-ktp', 'sktm', 'transkrip-rapor', 'sertifikat-prestasi'],
            ],
            [
                'slug' => 'djarum-beasiswa-plus',
                'name' => 'Djarum Beasiswa Plus',
                'organizer' => 'Djarum Foundation',
                'level' => 'Kuliah',
                'summary' => 'Beasiswa untuk mahasiswa S1 berprestasi yang dilengkapi pelatihan soft skills dan pengembangan karakter.',
                'description' => 'Djarum Beasiswa Plus adalah program beasiswa dari Djarum Foundation bagi mahasiswa S1 berprestasi di perguruan tinggi mitra. Selain dukungan dana, penerima mengikuti rangkaian pelatihan soft skills ("Plus") untuk membangun kepemimpinan, komunikasi, dan jiwa wirausaha. Seleksi umumnya meliputi seleksi berkas dan wawancara.',
                'benefits' => [
                    'Bantuan dana pendidikan/biaya hidup selama periode beasiswa.',
                    'Pelatihan soft skills dan pengembangan karakter ("Beasiswa Plus").',
                    'Jejaring dengan sesama penerima dan alumni dari berbagai kampus.',
                ],
                'open_date' => $today->copy()->subDays(14),
                'deadline' => $today->copy()->addDays(24),
                'official_url' => 'https://djarumbeasiswaplus.org',
                'requirements' => [
                    'akademik' => [
                        'Mahasiswa aktif S1 di perguruan tinggi mitra, umumnya pada semester 4.',
                        'IPK minimal sekitar 3,00 (cek angka pasti pada pengumuman resmi).',
                    ],
                    'administrasi' => [
                        'Mengisi formulir pendaftaran online dan mengunggah seluruh dokumen yang diminta.',
                        'Melampirkan transkrip nilai terbaru dan surat keterangan mahasiswa aktif.',
                        'Menyertakan esai atau pernyataan motivasi bila diminta.',
                    ],
                    'umum' => [
                        'Warga Negara Indonesia.',
                        'Aktif berorganisasi atau memiliki prestasi non-akademik.',
                        'Bersedia mengikuti seluruh rangkaian pelatihan dan kegiatan program.',
                    ],
                ],
                'documents' => ['cv', 'motivation-letter', 'surat-rekomendasi', 'transkrip-rapor', 'sertifikat-prestasi'],
            ],
            [
                'slug' => 'karya-salemba-empat',
                'name' => 'Beasiswa Karya Salemba Empat (KSE)',
                'organizer' => 'Yayasan Karya Salemba Empat',
                'level' => 'Kuliah',
                'summary' => 'Beasiswa untuk mahasiswa D3/D4/S1 berprestasi dan aktif berorganisasi, dilengkapi program pengembangan diri.',
                'description' => 'Karya Salemba Empat (KSE) memberikan beasiswa kepada mahasiswa berprestasi dan aktif di kampus mitra. Selain bantuan biaya pendidikan, penerima bergabung dalam komunitas pengembangan diri yang mendorong kepemimpinan, kepedulian sosial, dan kewirausahaan.',
                'benefits' => [
                    'Bantuan biaya pendidikan selama periode beasiswa.',
                    'Program pengembangan diri, kepemimpinan, dan kegiatan sosial.',
                    'Komunitas dan jaringan alumni KSE.',
                ],
                'open_date' => $today->copy()->subDays(7),
                'deadline' => $today->copy()->addDays(45),
                'official_url' => 'https://www.kse.or.id',
                'requirements' => [
                    'akademik' => [
                        'Mahasiswa aktif D3/D4/S1 di perguruan tinggi mitra KSE.',
                        'IPK minimal 3,00 (cek ketentuan terbaru).',
                        'Memenuhi batas semester pendaftaran sesuai pengumuman resmi.',
                    ],
                    'administrasi' => [
                        'Mendaftar secara online melalui kanal resmi KSE.',
                        'Melampirkan KTM atau surat keterangan aktif kuliah dan transkrip nilai.',
                        'Melampirkan dokumen pendukung kondisi ekonomi keluarga.',
                    ],
                    'umum' => [
                        'Warga Negara Indonesia.',
                        'Aktif dalam organisasi atau kegiatan sosial.',
                        'Membutuhkan dukungan finansial untuk melanjutkan studi.',
                    ],
                ],
                'documents' => ['cv', 'motivation-letter', 'surat-rekomendasi', 'transkrip-rapor', 'sktm', 'kk-ktp'],
            ],
            [
                'slug' => 'lpdp-reguler',
                'name' => 'Beasiswa LPDP Reguler (S2 & S3)',
                'organizer' => 'Lembaga Pengelola Dana Pendidikan (LPDP), Kementerian Keuangan RI',
                'level' => 'Kuliah',
                'summary' => 'Beasiswa pascasarjana (S2/S3) di dalam dan luar negeri dengan dukungan biaya pendidikan dan biaya hidup.',
                'description' => 'LPDP Reguler adalah beasiswa pemerintah bagi calon mahasiswa pascasarjana (magister dan doktor) di perguruan tinggi terbaik di dalam maupun luar negeri. Seleksi mencakup administrasi, penilaian esai, dan wawancara. Penerima diharapkan kembali berkontribusi bagi Indonesia setelah studi.',
                'benefits' => [
                    'Biaya pendidikan sesuai ketentuan program.',
                    'Tunjangan hidup bulanan selama masa studi.',
                    'Komponen pendukung lain (mis. tunjangan kedatangan, buku, penelitian) sesuai ketentuan.',
                    'Kesempatan studi di perguruan tinggi dalam dan luar negeri.',
                ],
                'open_date' => $today->copy()->subDays(20),
                'deadline' => $today->copy()->addDays(70),
                'official_url' => 'https://lpdp.kemenkeu.go.id',
                'requirements' => [
                    'akademik' => [
                        'Lulusan S1 (untuk S2) atau S2 (untuk S3) dengan IPK minimal 3,00 dari skala 4,00.',
                        'Memiliki sertifikat kemampuan bahasa (mis. TOEFL/IELTS) yang masih berlaku dan memenuhi skor minimum.',
                        'Memilih program studi dan perguruan tinggi tujuan yang termasuk dalam daftar LPDP.',
                    ],
                    'administrasi' => [
                        'Membuat akun dan mengisi pendaftaran di portal resmi LPDP.',
                        'Mengunggah dokumen: KTP, ijazah, transkrip, CV, esai, dan surat rekomendasi.',
                        'Menyatakan tidak sedang menerima beasiswa penuh dari sumber lain.',
                    ],
                    'umum' => [
                        'Warga Negara Indonesia.',
                        'Memenuhi batas usia sesuai ketentuan (umumnya S2 maksimal 35 tahun, S3 maksimal 40 tahun).',
                        'Bersedia kembali ke Indonesia dan berkontribusi setelah studi selesai.',
                    ],
                ],
                'documents' => ['cv', 'motivation-letter', 'surat-rekomendasi', 'transkrip-rapor', 'sertifikat-bahasa', 'sertifikat-prestasi', 'kk-ktp'],
            ],
            [
                'slug' => 'tanoto-foundation-scholarship',
                'name' => 'Beasiswa Tanoto Foundation',
                'organizer' => 'Tanoto Foundation',
                'level' => 'Kuliah',
                'summary' => 'Beasiswa mahasiswa S1 di kampus mitra dengan dukungan finansial, pengembangan kepemimpinan, dan mentoring.',
                'description' => 'Tanoto Foundation memberikan beasiswa kepada mahasiswa S1 berprestasi di perguruan tinggi mitra. Program ini tidak hanya menanggung biaya pendidikan, tetapi juga membina kepemimpinan dan karakter lewat pelatihan, mentoring, dan kegiatan komunitas penerima beasiswa.',
                'benefits' => [
                    'Dukungan biaya pendidikan dan biaya hidup sesuai ketentuan program.',
                    'Program kepemimpinan dan pengembangan karakter.',
                    'Mentoring dan jejaring dengan komunitas penerima beasiswa.',
                ],
                // Contoh status "Segera Dibuka".
                'open_date' => $today->copy()->addDays(20),
                'deadline' => $today->copy()->addDays(80),
                'official_url' => 'https://www.tanotofoundation.org',
                'requirements' => [
                    'akademik' => [
                        'Mahasiswa S1 di perguruan tinggi mitra Tanoto Foundation.',
                        'Prestasi akademik baik dengan IPK minimal sesuai pengumuman resmi.',
                    ],
                    'administrasi' => [
                        'Mendaftar secara online dan mengunggah seluruh dokumen yang diminta.',
                        'Melampirkan surat rekomendasi serta transkrip nilai terbaru.',
                        'Bersedia mengikuti wawancara atau seleksi tahap lanjut.',
                    ],
                    'umum' => [
                        'Warga Negara Indonesia.',
                        'Memiliki kebutuhan dukungan finansial untuk studi.',
                        'Memiliki jiwa kepemimpinan dan kepedulian terhadap lingkungan sekitar.',
                    ],
                ],
                'documents' => ['cv', 'motivation-letter', 'surat-rekomendasi', 'transkrip-rapor', 'sktm', 'kk-ktp'],
            ],
        ];

        $guideIds = DocumentGuide::pluck('id', 'slug');

        foreach ($data as $item) {
            $requirements = $item['requirements'];
            $documents = $item['documents'];
            unset($item['requirements'], $item['documents']);

            $scholarship = Scholarship::updateOrCreate(
                ['slug' => $item['slug']],
                $item + ['is_active' => true],
            );

            // Bangun ulang persyaratan agar seeder aman dijalankan berulang.
            $scholarship->requirements()->delete();

            foreach ($requirements as $category => $items) {
                foreach ($items as $order => $description) {
                    $scholarship->requirements()->create([
                        'category' => $category,
                        'description' => $description,
                        'sort_order' => $order + 1,
                    ]);
                }
            }

            $scholarship->documentGuides()->sync(
                collect($documents)->map(fn (string $slug) => $guideIds[$slug])->all()
            );
        }
    }
}
