-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3311
-- Waktu pembuatan: 22 Sep 2026 pada 13.30
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `infora_sn_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `ads`
--

CREATE TABLE `ads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `target_url` varchar(500) NOT NULL,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ads`
--

INSERT INTO `ads` (`id`, `title`, `image_path`, `target_url`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Junal terpercaya', 'public/uploads/ads/ad_20260921_154750_9566d3c53f.jpg', 'https://zaliljournal.com/index.php', 'ACTIVE', 0, '2026-09-10 06:49:02', '2026-09-21 13:47:50'),
(2, 'iklan shampo', 'public/uploads/ads/ad_20260911_113140_24cc42f87f.jpg', 'https://www.youtube.com/c/PanteneIndonesia', 'ACTIVE', 1, '2026-09-11 09:31:40', '2026-09-11 09:31:40'),
(3, 'iklan sepatu adidas', 'public/uploads/ads/ad_20260911_113258_0ee4c4d3c5.jpg', 'https://www.instagram.com/p/DCqv6ODyBtr/', 'ACTIVE', 3, '2026-09-11 09:32:58', '2026-09-11 09:32:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED NOT NULL,
  `author_override_name` varchar(150) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `excerpt` varchar(500) DEFAULT NULL,
  `content` longtext NOT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `status` enum('DRAFT','MENUNGGU_REVIEW','REVISI','DITOLAK','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
  `revision_note` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `last_edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `view_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `submitted_at` datetime DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `unpublished_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `articles`
--

INSERT INTO `articles` (`id`, `author_id`, `author_override_name`, `category_id`, `title`, `slug`, `excerpt`, `content`, `thumbnail_path`, `status`, `revision_note`, `rejection_reason`, `last_edited_by`, `view_count`, `submitted_at`, `published_at`, `unpublished_at`, `created_at`, `updated_at`) VALUES
(4, 1, NULL, 10, 'Didesak Gerakan Mahasiswa, Tambang Ilegal di Rumpin Bogor Akhirnya Digerebek!', 'didesak-gerakan-mahasiswa-tambang-ilegal-di-rumpin-bogor-akhirnya-digerebek-1788770138', 'Didorong oleh desakan dan aksi protes mahasiswa Rumpin beserta aliansi mahasiswa Bogor, aparat kepolisian akhirnya menggerebek lokasi tambang galian C ilegal di kawasan Rumpin, Kabupaten Bogor. Dalam operasi tersebut, petugas menyita puluhan unit alat berat dan memasang garis polisi setelah gerakan mahasiswa membongkar dampak buruk aktivitas liar tersebut terhadap lingkungan dan keselamatan warga. [1] (https://www.lead.co.id/bem-se-bogor-desak-polisi-tutup-tambang-ilegal-di-bogor-barat/), [2]', '<p><strong>Didesak Aksi Mahasiswa Rumpin, Polisi Gerebek Tambang Ilegal dan Sita Puluhan Alat Berat</strong></p><p><strong>BOGOR</strong> — Gerakan konsisten dari aliansi mahasiswa yang tergabung dalam Himpunan Mahasiswa Rumpin (HMR) bersama BEM Se-Bogor Raya akhirnya membuahkan hasil. Aparat kepolisian bergerak cepat melakukan penggerebekan dan menutup aktivitas penambangan galian C ilegal di kawasan Rumpin, Kabupaten Bogor. [<a href=\"https://www.youtube.com/watch?v=LQFHLQXByiI\" rel=\"noopener noreferrer\" target=\"_blank\">1</a>, <a href=\"https://www.lead.co.id/bem-se-bogor-desak-polisi-tutup-tambang-ilegal-di-bogor-barat/\" rel=\"noopener noreferrer\" target=\"_blank\">2</a>]</p><p>Sebelum penggerebekan dilakukan, para mahasiswa gencar melakukan aksi menyuarakan keresahan warga terkait kerusakan lingkungan, ancaman longsor, hingga jatuhnya korban jiwa akibat kecelakaan truk tambang. Berbekal data lapangan yang dihimpun oleh mahasiswa, petugas langsung menghentikan seluruh aktivitas pengerukan tanah liar di lokasi dan memasang garis polisi (<em>police line</em>). [<a href=\"https://www.youtube.com/watch?v=LQFHLQXByiI\" rel=\"noopener noreferrer\" target=\"_blank\">1</a>, <a href=\"https://www.lead.co.id/bem-se-bogor-desak-polisi-tutup-tambang-ilegal-di-bogor-barat/\" rel=\"noopener noreferrer\" target=\"_blank\">2</a>]</p><p>Di tempat kejadian perkara (TKP), petugas mengamankan area penambangan yang tengah beroperasi. Polisi menyita puluhan unit alat berat, termasuk ekskavator dan truk jungkit (<em>dump truck</em>) yang digunakan sebagai sarana utama mengangkut hasil tambang ilegal tersebut.</p><p>Perwakilan mahasiswa menegaskan bahwa mereka akan terus mengawal kasus ini hingga tuntas agar para mafia tambang dihukum seberat-beratnya sesuai undang-undang yang berlaku. Sementara itu, pihak kepolisian kini telah membawa pengelola lapangan beserta beberapa saksi ke markas kepolisian untuk menjalani pemeriksaan mendalam demi mengungkap jaringan intelektual di balik bisnis ilegal ini.</p><p><br></p><p><br></p><p><br></p><p><br></p>', 'public/uploads/articles/article_4_a958e26c1da8e199.jpg', 'PUBLISHED', NULL, NULL, 2, 32, NULL, '2026-09-07 16:49:24', NULL, '2026-09-07 08:35:38', '2026-09-21 16:11:31'),
(5, 1, NULL, 7, 'Gunung Krakatau Kembali Erupsi, Semburkan Abu Vulkanik Setinggi 1.500 Meter', 'gunung-krakatau-kembali-erupsi-semburkan-abu-vulkanik-setinggi-1-500-meter-1788886589', 'Gunung Anak Krakatau di Selat Sunda kembali mengalami erupsi dengan melontarkan kolom abu abu-hitam setinggi 1.500 meter di atas puncak. Masyarakat dan wisatawan diimbau untuk tidak mendekat dalam radius bahaya 5 kilometer.', '<p>Gunung Anak Krakatau yang terletak di Selat Sunda, Provinsi Lampung, kembali menunjukkan peningkatan aktivitas vulkanik berupa erupsi beruntun. Pos Pengamatan Gunung Api (PGA) mencatat tinggi kolom abu teramati mencapai sekitar 1.500 meter di atas puncak atau 1.657 meter di atas permukaan laut.</p><p>Kolom abu vulkanik teramati berwarna kelabu hingga hitam dengan intensitas tebal yang condong mengarah ke timur laut dan timur. Erupsi ini terekam di seismogram dengan amplitudo maksimum 60 mm dan durasi sekitar 2 menit 15 detik.</p><p>Pusat Vulkanologi dan Mitigasi Bencana Geologi (PVMBG) menetapkan status Gunung Anak Krakatau berada pada <strong>Level III (Siaga)</strong>. Atas kondisi ini, PVMBG mengeluarkan beberapa imbauan keselamatan bagi masyarakat sekitar:</p><p><br></p><ul><li><strong>Radius Aman:</strong> Masyarakat, wisatawan, maupun pendaki dilarang mendekati Gunung Anak Krakatau atau beraktivitas dalam radius 5 kilometer dari kawah aktif.</li><li><strong>Penggunaan Masker:</strong> Warga di wilayah terdampak hujan abu imbau menggunakan masker dan kacamata saat beraktivitas di luar ruangan untuk menghindari gangguan pernapasan (ISPA) dan iritasi mata.</li><li><strong>Waspada Hoaks:</strong> Masyarakat diminta tetap tenang dan hanya mempercayai informasi resmi dari PVMBG, Badan Nasional Penanggulangan Bencana (BNPB), atau BPBD setempat.</li></ul><p><br></p><p>Hingga saat ini, aktivitas pelayaran di Selat Sunda dilaporkan masih berjalan normal, namun otoritas pelabuhan terus memantau perkembangan panduan dari BMKG dan PVMBG demi keamanan penyeberangan.</p>', 'public/uploads/articles/article_5_f754696fd29232f5.jpg', 'PUBLISHED', NULL, NULL, 2, 4, NULL, '2026-09-08 23:57:02', NULL, '2026-09-08 16:56:29', '2026-09-22 10:34:24'),
(6, 1, NULL, 6, 'Drama 120 Menit di Budapest: PSG Pertahankan Gelar Liga Champions 2025/2026 Usai Tekuk Arsenal', 'drama-120-menit-di-budapest-psg-pertahankan-gelar-liga-champions-2025-2026-usai-tekuk-arsenal-1789032795', 'Lewat laga sengit yang berlanjut hingga drama adu penalti, Paris Saint-Germain sukses mempertahankan gelar Liga Champions 2025/2026 dengan menundukkan Arsenal di Puskás Aréna, Budapest.', '<p><strong>BUDAPEST</strong> — Laga puncak UEFA Champions League (UCL) musim 2025/2026 menyajikan drama tingkat tinggi bagi para pecinta sepak bola dunia. Bertanding di Puskás Aréna, Budapest, raksasa Prancis Paris Saint-Germain (PSG) sukses mengunci gelar juara usai menumbangkan wakil Inggris, Arsenal, lewat adu penalti 4-3 setelah bermain imbang 1-1 hingga perpanjangan waktu.</p><p><strong>Jalannya Pertandingan</strong></p><p>Sejak kick-off babak pertama, kedua tim langsung memperagakan permainan terbuka dengan tempo cepat. Arsenal lebih dulu mengambil inisiatif serangan memanfaatkan kecepatan para pemain sayapnya. Tekanan <em>The Gunners</em> membuahkan hasil pada menit ke-34 lewat skema serangan balik cepat yang diselesaikan secara dingin, mengubah skor menjadi 1-0 untuk keunggulan tim asuhan Mikel Arteta.</p><p>Tertinggal satu gol membuat PSG merespons dengan meningkatkan intensitas serangan di babak kedua. Skuad asuhan Luis Enrique terus mengurung pertahanan Arsenal lewat penguasaan bola yang dominan. Gelombang tekanan tersebut akhirnya membuahkan hasil di menit ke-78 ketika tembakan terukur dari luar kotak penalti membentur tiang sebelum masuk ke gawang Arsenal. Skor berubah 1-1 dan bertahan hingga 90 menit usai.</p><p>Pada babak perpanjangan waktu 2x15 menit, kedua tim tampak berhati-hati dan mengandalkan kedisiplinan lini belakang. Fisik para pemain yang mulai terkuras membuat tak ada gol tambahan yang tercipta, sehingga pemenang harus ditentukan lewat adu tos-tosan.</p><p><strong>Drama Adu Penalti dan Rekor Back-to-Back</strong></p><p>Di babak adu penalti, ketenangan para algojo PSG menjadi kunci utama. Dari lima penendang Arsenal, dua di antaranya gagal menjalankan tugas setelah tendangannya berhasil ditepis oleh kiper PSG yang tampil luar biasa. Penendang terakhir PSG akhirnya memastikan kemenangan 4-3 sekaligus memicu selebrasi histeris dari para pendukung <em>Les Parisiens</em> di tribun stadion.</p><p>Keberhasilan ini menorehkan tinta emas bagi PSG yang sukses meraih gelar Liga Champions secara berturut-turut (<em>back-to-back</em>). Luis Enrique memuji mentalitas anak asuhnya yang pantang menyerah meski sempat tertinggal. Sementara itu, Mikel Arteta mengapresiasi kerja keras skuad Arsenal yang telah memberikan perlawanan maksimal hingga detik terakhir.</p>', 'public/uploads/articles/article_6_aa63a5253f67992d.jpg', 'PUBLISHED', NULL, NULL, 2, 9, NULL, '2026-09-10 16:33:47', NULL, '2026-09-10 09:33:15', '2026-09-22 10:52:20'),
(8, 1, NULL, 2, 'indonesia mau di bawa kemana 2027?', 'indonesia-mau-di-bawa-kemana-2027-1789994100', 'Menjelang tahun 2027, arah kebijakan nasional Indonesia berfokus pada disiplin fiskal, efisiensi belanja negara, serta perluasan hilirisasi industri dan digitalisasi perpajakan untuk memperkuat fondasi ekonomi bangsa.', '<p><strong>JAKARTA</strong> — Pertanyaan mengenai arah kebijakan dan masa depan bangsa kini mengemuka: Indonesia 2027 mau dibawa ke mana? Menjelang tahun 2027, pemerintah telah merancang peta jalan strategis yang menitikberatkan pada efisiensi anggaran, transformasi digital, serta penguatan sektor-sektor kunci pertumbuhan nasional.</p><p>Melalui Kerangka Ekonomi Makro dan Pokok-Pokok Kebijakan Fiskal (KEM-PPKF) 2027 bertajuk <em>\"Tumbuh Lebih Tinggi, Sejahtera Lebih Cepat\"</em>, arah pembangunan Indonesia diproyeksikan untuk menjaga keseimbangan antara pertumbuhan ekonomi dan stabilitas keuangan negara. Pemerintah berkomitmen menjaga defisit Anggaran Pendapatan dan Belanja Negara (APBN) berada di kisaran terukur 1,80 persen hingga 2,40 persen terhadap Produk Domestik Bruto (PDB).</p><p>Arah kebijakan nasional di tahun 2027 bertumpu pada beberapa pilar utama:</p><p><br></p><h4>1. Penguatan Hilirisasi dan Kemandirian Ekonomi</h4><p>Pemerintah memperluas skala hilirisasi industri tidak hanya di sektor pertambangan, tetapi juga mencakup sektor agraris dan kelautan. Langkah ini dirancang untuk menciptakan nilai tambah komoditas lokal sekaligus membuka lebih banyak lapangan kerja berkualitas di berbagai daerah.</p><p><br></p><h4>2. Kedaulatan Pangan, Energi, dan Infrastruktur</h4><p>Alokasi belanja negara diprioritaskan untuk memperkuat ketahanan pangan dan energi terbarukan, percepatan pembangunan infrastruktur dasar, serta penataan pemukiman dan mitigasi bencana demi meningkatkan kualitas hidup masyarakat.</p><p><br></p><h4>3. Modernisasi Perpajakan dan Efisiensi Belanja</h4><p>Di sektor penerimaan, pemerintah menargetkan rasio perpajakan (<em>tax ratio</em>) pada kisaran 12,01 persen hingga 12,40 persen dari PDB. Optimalisasi ini dicapai melalui pemanfaatan teknologi sistem perpajakan terpadu (<em>Core Tax System</em>) tanpa memberatkan iklim investasi. Sementara dari sisi pengeluaran, prinsip <em>spending better</em> diterapkan untuk memastikan setiap rupiah anggaran negara berdampak langsung pada kesejahteraan rakyat.</p><p>Dengan strategi yang terukur dan efisien tersebut, arah Indonesia di tahun 2027 ditujukan untuk membangun struktur ekonomi yang mandiri, adaptif terhadap ketidakpastian global, serta siap melangkah menuju visi Indonesia Emas.</p>', 'public/uploads/articles/article_8_d63bec0b09b5a4d0.jpg', 'MENUNGGU_REVIEW', 'ulang', NULL, 2, 1, NULL, '2026-09-21 19:40:50', NULL, '2026-09-21 12:35:00', '2026-09-22 07:32:31'),
(12, 2, 'Vernon Januar', 9, 'Pencopotan Purbaya Yudhi Sadewa: Reshuffle Biasa atau Sinyal Perubahan Arah Ekonomi Prabowo?', 'pencopotan-purbaya-yudhi-sadewa-reshuffle-biasa-atau-sinyal-perubahan-arah-ekonomi-prabowo', '', '<p>Redaksi INFORA.SN- Pergantian Menteri Keuangan secara mendadak selalu menjadi peristiwa politik yang tidak dapat dipandang sebagai sekadar pergantian pejabat administratif. Posisi Menteri Keuangan merupakan salah satu posisi paling strategis dalam pemerintahan karena berkaitan langsung dengan APBN, kebijakan fiskal, penerimaan negara, pembiayaan pembangunan, serta kepercayaan pasar terhadap pemerintah.</p><p>Pada Senin, 14 September 2026, Presiden Prabowo Subianto memberhentikan Purbaya Yudhi Sadewa dari jabatan Menteri Keuangan dan menunjuk Wakil Menteri Keuangan Suahasil Nazara sebagai penggantinya. Pergantian tersebut dilakukan sekitar satu tahun setelah Purbaya dilantik menggantikan Sri Mulyani Indrawati pada 8 September 2025.</p><p>Yang menarik bukan hanya pergantiannya, tetapi *\"waktu dan cara pergantian tersebut dilakukan\" . Purbaya pada Senin pagi masih menjalankan tugas sebagai Menteri Keuangan dan menghadiri rapat bersama DPD RI. Tidak lama kemudian, ia menerima pemberitahuan mengenai pergantian tersebut, sebelum Suahasil Nazara dilantik pada hari yang sama.</p><p><strong>Pergantian yang Menimbulkan Pertanyaan</strong></p><p>Secara konstitusional, Presiden memang memiliki kewenangan untuk mengangkat dan memberhentikan menteri. Karena itu, pergantian Purbaya tidak dapat langsung dianggap sebagai persoalan hukum.</p><p>Namun, dari perspektif politik, pergantian Menteri Keuangan dalam waktu relatif singkat tentu menimbulkan pertanyaan: apa yang sebenarnya sedang berubah dalam arah kebijakan ekonomi pemerintahan Prabowo?</p><p>Sampai saat ini, pemerintah belum memberikan penjelasan resmi yang secara spesifik menyatakan bahwa Purbaya diberhentikan karena satu kebijakan atau satu kesalahan tertentu. Karena itu, berbagai spekulasi yang beredar perlu ditempatkan sebagai analisis, bukan sebagai fakta yang telah terbukti.</p><p><strong>Pertama, Persoalan Koordinasi Kebijakan</strong></p><p>Salah satu kemungkinan yang patut diperhatikan adalah persoalan koordinasi antara Kementerian Keuangan dengan institusi ekonomi lainnya.</p><p>Menteri Keuangan tidak bekerja sendiri. Kebijakan fiskal harus berjalan beriringan dengan kebijakan moneter Bank Indonesia, kebijakan investasi, kebijakan perdagangan, serta kebijakan ekonomi Presiden.</p><p>Beberapa pengamat menilai gaya komunikasi dan kebijakan Purbaya selama menjabat cukup kontroversial dan berpotensi menimbulkan ketegangan dengan sejumlah pihak, termasuk pemerintah daerah dan pelaku pasar.</p><p>Jika benar terdapat persoalan koordinasi, maka pergantian Menteri Keuangan dapat dibaca sebagai upaya Presiden untuk mengembalikan keselarasan antaraktor ekonomi pemerintah.</p><p><strong>Kedua, Masalah Kepercayaan Pasar</strong></p><p>Menteri Keuangan bukan hanya bertanggung jawab terhadap APBN, tetapi juga memiliki peran penting dalam menjaga persepsi investor terhadap perekonomian Indonesia.</p><p>Kebijakan fiskal yang dianggap tidak konsisten atau sulit diprediksi dapat meningkatkan kekhawatiran investor. Reuters melaporkan bahwa masa jabatan Purbaya diwarnai kekhawatiran mengenai pelemahan rupiah, defisit fiskal, serta ketidakpastian kebijakan. Pergantian kepada Suahasil kemudian dipandang sebagian analis sebagai upaya untuk memperkuat kembali kredibilitas kebijakan fiskal.</p><p>Namun demikian, perlu ditegaskan bahwa menghubungkan seluruh persoalan tersebut secara langsung sebagai \"alasan pencopotan Purbaya\" masih merupakan kesimpulan yang terlalu jauh apabila tidak terdapat pernyataan resmi dari Presiden.</p><p><strong>Ketiga, Pesan Politik dari Penunjukan Suahasil Nazara</strong></p><p>Yang tidak kalah menarik adalah siapa yang dipilih sebagai pengganti.</p><p>Presiden Prabowo tidak memilih figur yang benar-benar berasal dari luar Kementerian Keuangan. Ia justru memilih Suahasil Nazara, yang sebelumnya menjabat sebagai Wakil Menteri Keuangan dan memiliki pengalaman panjang dalam kebijakan fiskal.</p><p>Pilihan ini dapat dibaca sebagai pesan bahwa pemerintah tidak ingin melakukan perubahan radikal terhadap sistem Kementerian Keuangan.</p><p>Sebaliknya, pemerintah tampaknya ingin melakukan koreksi dari dalam.</p><p>Dengan kata lain, pergantian Purbaya menjadi Suahasil dapat dipahami bukan sebagai pembongkaran total kebijakan fiskal, tetapi sebagai upaya melakukan penyesuaian terhadap cara kebijakan tersebut dijalankan.</p><p><strong>Keempat, Pertarungan antara Pertumbuhan dan Disiplin Fiskal</strong></p><p>Pemerintahan Prabowo memiliki ambisi besar untuk meningkatkan pertumbuhan ekonomi dan menjalankan berbagai program prioritas.</p><p>Di sisi lain, pemerintah tetap harus menjaga defisit APBN, stabilitas nilai tukar, kredibilitas fiskal, dan kepercayaan investor.</p><p>Di sinilah posisi Menteri Keuangan menjadi sangat strategis.</p><p>Pemerintah membutuhkan Menteri Keuangan yang mampu menjembatani dua kepentingan: mendorong pertumbuhan ekonomi sekaligus menjaga disiplin fiskal.</p><p>Suahasil sendiri setelah dilantik membawa pesan mengenai pentingnya menjaga kredibilitas APBN dan mempertahankan defisit sesuai batas yang ditentukan.</p><p>Hal tersebut menunjukkan bahwa pemerintah kemungkinan ingin memberikan penekanan lebih besar terhadap aspek stabilitas dan kredibilitas fiskal.</p><p><strong>Apakah Purbaya Gagal?</strong></p><p>Pertanyaan ini harus dijawab secara hati-hati.</p><p>Pencopotan seorang menteri tidak otomatis berarti menteri tersebut gagal menjalankan tugasnya.</p><p>Dalam sistem pemerintahan presidensial, menteri merupakan pembantu Presiden. Presiden memiliki hak politik untuk menilai apakah seorang menteri masih sesuai dengan kebutuhan dan arah pemerintahan.</p><p>Dengan demikian, pergantian Purbaya lebih tepat dilihat sebagai evaluasi politik dan kebijakan Presiden terhadap kabinetnya, bukan sebagai vonis bahwa seluruh kebijakan Purbaya merupakan kegagalan.</p><p>Justru pertanyaan yang lebih penting adalah: apa yang ingin dicapai Presiden Prabowo melalui pergantian tersebut?</p><p><strong>Pencopotan Purbaya sebagai Sinyal Politik</strong></p><p>Dalam perspektif politik, pergantian Menteri Keuangan dapat dibaca sebagai sebuah sinyal.</p><p>Sinyal pertama adalah bahwa Presiden Prabowo ingin menunjukkan bahwa tidak ada posisi menteri yang kebal terhadap evaluasi Presiden.</p><p>Sinyal kedua, pemerintah tampaknya ingin memberikan kepastian kepada pasar bahwa kebijakan fiskal akan tetap berada dalam koridor disiplin anggaran.</p><p>Sinyal ketiga adalah bahwa Presiden mungkin sedang melakukan konsolidasi internal agar seluruh instrumen ekonomi pemerintah bergerak dalam arah yang lebih seragam.</p><p>Dengan memilih Suahasil Nazara yang sudah memahami struktur Kementerian Keuangan, pemerintah juga dapat mengurangi risiko terjadinya kekosongan atau guncangan birokrasi akibat pergantian menteri.</p><p>Pencopotan Purbaya Yudhi Sadewa bukan sekadar pergantian seorang Menteri Keuangan. Peristiwa ini harus dibaca dalam konteks yang lebih luas, yaitu arah politik ekonomi pemerintahan Prabowo Subianto.</p><p>Sampai saat ini, alasan resmi dan spesifik mengenai pencopotan Purbaya belum disampaikan secara terbuka. Oleh karena itu, dugaan mengenai konflik kebijakan, persoalan koordinasi, tekanan pasar, maupun perbedaan pendekatan ekonomi harus tetap ditempatkan sebagai analisis, bukan fakta yang sudah terbukti.</p><p>Namun satu hal cukup jelas: dengan menunjuk Suahasil Nazara sebagai pengganti, Presiden Prabowo memberikan sinyal bahwa stabilitas, kredibilitas APBN, dan konsistensi kebijakan fiskal akan menjadi perhatian penting dalam fase pemerintahan berikutnya.</p><p>Pada akhirnya, ukuran keberhasilan reshuffle ini bukanlah siapa yang dicopot dan siapa yang dilantik.</p><p><em>Ukuran sebenarnya adalah apakah setelah pergantian Menteri Keuangan, ekonomi Indonesia menjadi lebih stabil, APBN semakin kredibel, kepercayaan investor meningkat, dan program-program prioritas pemerintah dapat berjalan tanpa mengorbankan kesehatan fiskal negara.</em></p><p>Jika tujuan tersebut tercapai, maka pencopotan Purbaya dapat dipandang sebagai keputusan strategis Presiden. Namun jika persoalan mendasar tetap tidak terselesaikan, publik berhak mempertanyakan apakah pergantian tersebut benar-benar merupakan solusi atau hanya perubahan personel di tengah persoalan ekonomi yang lebih besar.</p><p>Vernon Januar merupakan pengamat politik dan mahasiswa Fakultas Hukum Universitas Pamulang yang memiliki ketertarikan pada isu-isu politik nasional, hukum tata negara, kebijakan publik, serta dinamika pemerintahan Indonesia. Melalui tulisan dan analisisnya, penulis berupaya melihat berbagai kebijakan pemerintah secara kritis, objektif, dan berdasarkan perspektif hukum serta kepentingan masyarakat.</p><p>Penulis meyakini bahwa politik tidak hanya berkaitan dengan kekuasaan, tetapi juga menyangkut arah kebijakan negara dan dampaknya terhadap kehidupan masyarakat. Karena itu, setiap dinamika politik perlu dikaji secara rasional, berimbang, dan tetap mengedepankan kepentingan nasional.</p>', 'public/uploads/articles/article_20260922_092347_d392604055.jpg', 'PUBLISHED', NULL, NULL, NULL, 1, NULL, '2026-09-22 09:23:47', NULL, '2026-09-22 07:23:47', '2026-09-22 07:24:01'),
(13, 2, 'Siti Nurhaliza', 9, 'Kontrak Sudah Ditandatangani, Tetapi Apakah Selalu Harus Dipatuhi?', 'kontrak-sudah-ditandatangani-tetapi-apakah-selalu-harus-dipatuhi', 'Artikel ini membahas kekuatan mengikat kontrak berdasarkan asas pacta sunt servanda serta batas kebebasan berkontrak dalam praktik. Penulis menyoroti persoalan perjanjian baku, kesepakatan digital, itikad baik, dan ketidakseimbangan posisi para pihak. Pembaruan hukum perjanjian diperlukan agar kepastian hukum tetap berjalan seiring dengan keadilan dan perlindungan terhadap pihak yang lebih lemah.', '<p class=\"ql-align-justify\">Dalam kehidupan sehari-hari, perjanjian merupakan sesuatu yang hampir tidak dapat dipisahkan dari aktivitas manusia. Ketika seseorang membeli barang secara daring, menyewa rumah, meminjam uang, menggunakan jasa perbankan, bahkan ketika seseorang sekadar menekan tombol “Saya Setuju” pada sebuah aplikasi, sebenarnya telah terjadi hubungan hukum yang dapat melahirkan hak dan kewajiban bagi para pihak. Kontrak menjadi instrumen penting untuk memberikan kepastian mengenai apa yang harus dilakukan, apa yang boleh dilakukan, dan apa akibatnya apabila salah satu pihak tidak memenuhi kewajibannya.</p><p class=\"ql-align-justify\">Namun, muncul satu pertanyaan yang menurut saya semakin relevan untuk dibahas: apakah kontrak yang sudah ditandatangani selalu harus dipatuhi dalam keadaan apa pun?</p><p class=\"ql-align-justify\">Pertanyaan tersebut pada dasarnya berkaitan dengan prinsip fundamental dalam hukum perjanjian, yaitu asas pacta sunt servanda. Pasal 1338 ayat (1) Kitab Undang-Undang Hukum Perdata (KUHPerdata) menyatakan bahwa semua perjanjian yang dibuat secara sah berlaku sebagai undang-undang bagi mereka yang membuatnya. Ketentuan tersebut menunjukkan betapa kuatnya kedudukan suatu perjanjian. Ketika para pihak telah sepakat dan memenuhi persyaratan yang ditentukan oleh hukum, maka perjanjian tersebut pada prinsipnya mengikat dan harus dilaksanakan.</p><p class=\"ql-align-justify\">Akan tetapi, persoalannya tidak sesederhana itu.</p><p class=\"ql-align-justify\">Dalam praktik, tidak semua pihak yang menandatangani kontrak berada dalam posisi yang benar-benar seimbang. Ada pihak yang memiliki kemampuan ekonomi, pengetahuan hukum, dan posisi tawar yang jauh lebih kuat dibandingkan pihak lainnya. Dalam kondisi tertentu, pihak yang lebih lemah hanya diberikan pilihan untuk menerima seluruh isi kontrak atau tidak melakukan perjanjian sama sekali. Keadaan seperti ini banyak ditemukan dalam perjanjian baku, misalnya dalam perjanjian pembiayaan, perbankan, asuransi, layanan digital, hingga transaksi perdagangan elektronik.</p><p class=\"ql-align-justify\">Di sinilah prinsip kebebasan berkontrak perlu dilihat secara lebih kritis.</p><p class=\"ql-align-justify\">Pasal 1320 KUHPerdata menentukan empat syarat sahnya suatu perjanjian, yaitu adanya kesepakatan para pihak, kecakapan untuk membuat suatu perikatan, suatu hal tertentu, dan suatu sebab yang halal. Dari keempat syarat tersebut, kesepakatan menjadi salah satu unsur yang sangat penting. Secara sederhana, perjanjian seharusnya lahir dari kehendak para pihak untuk mengikatkan dirinya.</p><p class=\"ql-align-justify\">Masalahnya, apakah setiap tanda tangan selalu menunjukkan adanya kesepakatan yang benar-benar bebas?</p><p class=\"ql-align-justify\">Dalam praktik modern, tanda tangan terkadang hanya menjadi formalitas. Seseorang dapat menandatangani puluhan halaman perjanjian tanpa benar-benar memahami seluruh konsekuensi hukum yang terkandung di dalamnya. Dalam transaksi digital, persoalan tersebut menjadi semakin kompleks. Pengguna cukup menekan tombol “I Agree” atau “Accept” untuk menyatakan persetujuan terhadap syarat dan ketentuan yang panjang dan sering kali sulit dipahami oleh masyarakat awam.</p><p class=\"ql-align-justify\">Secara hukum, persetujuan tersebut tentu tidak serta-merta dapat dianggap tidak sah. Namun, menurut saya, persoalan yang lebih penting adalah apakah konsep kesepakatan dalam hukum perjanjian masih cukup untuk menjawab perkembangan hubungan kontraktual modern.</p><p class=\"ql-align-justify\">Kontrak pada dasarnya bukan hanya persoalan tanda tangan. Kontrak adalah mengenai kehendak, kesepakatan, dan keseimbangan kepentingan para pihak. Karena itu, keberadaan tanda tangan seharusnya tidak membuat kita berhenti mempertanyakan bagaimana proses kesepakatan tersebut terbentuk.</p><p class=\"ql-align-justify\">Hal ini semakin penting ketika kontrak dibuat dalam bentuk perjanjian baku. Dalam perjanjian baku, isi kontrak pada umumnya telah ditentukan terlebih dahulu oleh salah satu pihak. Pihak lainnya sering kali tidak memiliki kesempatan yang memadai untuk melakukan negosiasi. Pilihannya sederhana: menerima atau menolak.</p><p class=\"ql-align-justify\">Situasi tersebut menimbulkan pertanyaan mengenai batas kebebasan berkontrak. Secara teori, kebebasan berkontrak memberikan kesempatan kepada para pihak untuk menentukan apakah mereka akan membuat perjanjian, dengan siapa perjanjian dibuat, apa isi perjanjiannya, serta bagaimana bentuk perjanjian tersebut. Namun, kebebasan tersebut bukanlah kebebasan tanpa batas.</p><p class=\"ql-align-justify\">Pasal 1338 ayat (3) KUHPerdata menegaskan bahwa perjanjian harus dilaksanakan dengan itikad baik. Artinya, meskipun suatu kontrak telah dibuat secara sah, pelaksanaannya tetap harus memperhatikan prinsip kepatutan, kejujuran, dan kewajaran dalam hubungan para pihak.</p><p class=\"ql-align-justify\">Menurut saya, ketentuan mengenai itikad baik menjadi sangat penting karena dapat menjadi jembatan antara kepastian hukum dan keadilan dalam kontrak. Kepastian hukum memang diperlukan agar para pihak dapat mempercayai perjanjian yang telah dibuat. Namun, kepastian hukum tidak seharusnya dipahami sebagai alasan untuk membenarkan segala bentuk ketentuan kontrak tanpa melihat keadaan yang melatarbelakanginya.</p><p class=\"ql-align-justify\">Kontrak memang harus memberikan kepastian. Tetapi kepastian hukum juga harus berjalan bersama dengan keadilan.</p><p class=\"ql-align-justify\">Dalam konteks ini, kita dapat melihat persoalan wanprestasi secara lebih luas. Ketika salah satu pihak tidak memenuhi kewajibannya, pihak lainnya dapat menuntut pemenuhan perjanjian atau meminta konsekuensi hukum sesuai dengan ketentuan yang berlaku. Namun, sebelum menyimpulkan bahwa seseorang telah melakukan wanprestasi, perlu dilihat terlebih dahulu bagaimana kewajiban tersebut lahir dan apakah kontrak yang menjadi dasarnya dibuat serta dilaksanakan secara patut.</p><p class=\"ql-align-justify\">Tidak semua persoalan kontrak dapat diselesaikan hanya dengan mengatakan, “Sudah tanda tangan, berarti harus patuh.”</p><p class=\"ql-align-justify\">Kalimat tersebut memang terdengar sederhana dan memiliki dasar dalam prinsip pacta sunt servanda. Namun, hukum tidak berhenti pada tanda tangan. Hukum juga melihat bagaimana perjanjian tersebut dibuat, apakah para pihak cakap, apakah terdapat kesepakatan, apakah objeknya jelas, apakah sebabnya halal, serta bagaimana perjanjian tersebut dilaksanakan.</p><p class=\"ql-align-justify\">Dalam keadaan tertentu, hukum bahkan mengenal kemungkinan pembatalan perjanjian. Hal tersebut menunjukkan bahwa asas mengikatnya kontrak bukanlah sebuah prinsip yang berdiri tanpa batas. Hukum perjanjian juga menyediakan mekanisme untuk menghadapi keadaan ketika suatu perjanjian mengandung persoalan hukum.</p><p class=\"ql-align-justify\">Perkembangan transaksi digital membuat persoalan ini semakin menarik. Masyarakat sekarang dapat membuat kontrak dengan pihak lain tanpa pernah bertemu secara langsung. Kesepakatan dapat terjadi hanya melalui layar telepon genggam. Pembayaran dilakukan secara elektronik, bukti transaksi tersimpan dalam sistem, dan syarat penggunaan layanan ditampilkan dalam bentuk digital.</p><p class=\"ql-align-justify\">Perubahan tersebut tentu memberikan kemudahan. Namun, kemudahan tidak boleh membuat perlindungan hukum menjadi semakin lemah.</p><p class=\"ql-align-justify\">Menurut saya, hukum perjanjian Indonesia perlu terus menyesuaikan diri dengan perkembangan teknologi dan pola transaksi masyarakat. Pembaruan hukum perjanjian tidak berarti meninggalkan prinsip-prinsip klasik seperti kebebasan berkontrak dan pacta sunt servanda. Justru prinsip-prinsip tersebut perlu ditafsirkan secara kontekstual agar tetap mampu menjawab persoalan baru.</p><p class=\"ql-align-justify\">Kita tidak mungkin mempertahankan cara pandang kontrak yang sepenuhnya dibangun berdasarkan hubungan konvensional ketika masyarakat sudah hidup dalam ekosistem digital.</p><p class=\"ql-align-justify\">Pada akhirnya, pertanyaan “kontrak sudah ditandatangani, apakah selalu harus dipatuhi?” tidak dapat dijawab hanya dengan “ya” atau “tidak”. Secara prinsip, perjanjian yang dibuat secara sah memang mengikat para pihak. Namun, keberlakuan dan pelaksanaannya tetap harus ditempatkan dalam kerangka hukum yang lebih luas, yaitu kesepakatan yang sah, itikad baik, kepatutan, keseimbangan kepentingan, serta perlindungan terhadap pihak yang berada dalam posisi lebih lemah.</p><p class=\"ql-align-justify\">Kontrak seharusnya menjadi instrumen untuk menciptakan kepastian, bukan alat untuk menghilangkan keadilan.</p><p class=\"ql-align-justify\">Karena itu, sudah saatnya masyarakat tidak lagi memandang kontrak hanya sebagai dokumen yang perlu ditandatangani. Kontrak adalah hubungan hukum yang membawa konsekuensi nyata. Membaca, memahami, dan mempertanyakan isi kontrak bukan sekadar tindakan administratif, tetapi merupakan bagian dari upaya melindungi hak seseorang.</p><p class=\"ql-align-justify\">Pada akhirnya, tanda tangan memang dapat menjadi bukti bahwa seseorang menyatakan persetujuan. Tetapi hukum seharusnya tidak berhenti pada pertanyaan apakah seseorang telah menandatangani kontrak. Pertanyaan yang tidak kalah penting adalah: apakah persetujuan tersebut benar-benar lahir dari kehendak yang bebas, dilakukan dengan itikad baik, dan menghasilkan hubungan hukum yang patut bagi para pihak?</p><p class=\"ql-align-justify\">Sebab, dalam hukum perjanjian, kepastian memang penting. Tetapi kepastian tanpa keadilan juga berpotensi kehilangan makna.</p><p class=\"ql-align-justify\"><br></p><p class=\"ql-align-justify\">Penulis, Siti Nurhaliza, merupakan salah satu mahasiswa Program Studi Ilmu Hukum Universitas Pamulang, Banten. Ia memiliki ketertarikan terhadap berbagai isu hukum, khususnya hukum perdata dan perkembangan hukum dalam menghadapi perubahan teknologi serta dinamika kehidupan masyarakat. Melalui tulisan dan kajian yang dilakukan, Siti Nurhaliza berupaya menyampaikan pandangan hukum secara kritis, sederhana, dan mudah dipahami oleh masyarakat, khususnya dalam melihat persoalan-persoalan hukum yang berkembang dalam kehidupan sehari-hari.</p><p><br></p>', 'public/uploads/articles/article_20260922_092631_e5b6bfee64.jpg', 'PUBLISHED', NULL, NULL, NULL, 1, NULL, '2026-09-22 09:26:31', NULL, '2026-09-22 07:26:31', '2026-09-22 07:26:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `article_images`
--

CREATE TABLE `article_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(10) UNSIGNED DEFAULT NULL,
  `file_format` varchar(20) DEFAULT NULL,
  `is_thumbnail` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `article_images`
--

INSERT INTO `article_images` (`id`, `article_id`, `file_name`, `file_path`, `file_size`, `file_format`, `is_thumbnail`, `created_at`) VALUES
(5, 4, 'article_4_a958e26c1da8e199.jpg', 'public/uploads/articles/article_4_a958e26c1da8e199.jpg', 353546, 'jpg', 1, '2026-09-07 08:35:38'),
(6, 5, 'article_5_f754696fd29232f5.jpg', 'public/uploads/articles/article_5_f754696fd29232f5.jpg', 107042, 'jpg', 1, '2026-09-08 16:56:30'),
(7, 6, 'article_6_aa63a5253f67992d.jpg', 'public/uploads/articles/article_6_aa63a5253f67992d.jpg', 151321, 'jpg', 1, '2026-09-10 09:33:16'),
(8, 8, 'article_8_d63bec0b09b5a4d0.jpg', 'public/uploads/articles/article_8_d63bec0b09b5a4d0.jpg', 280333, 'jpg', 1, '2026-09-21 12:35:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `article_status_logs`
--

CREATE TABLE `article_status_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `changed_by` bigint(20) UNSIGNED NOT NULL,
  `previous_status` enum('DRAFT','MENUNGGU_REVIEW','REVISI','DITOLAK','PUBLISHED') DEFAULT NULL,
  `new_status` enum('DRAFT','MENUNGGU_REVIEW','REVISI','DITOLAK','PUBLISHED') NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `article_status_logs`
--

INSERT INTO `article_status_logs` (`id`, `article_id`, `changed_by`, `previous_status`, `new_status`, `note`, `created_at`) VALUES
(5, 4, 1, NULL, 'DRAFT', 'Artikel dibuat sebagai draft.', '2026-09-07 08:35:38'),
(6, 4, 2, 'DRAFT', 'PUBLISHED', '', '2026-09-07 08:36:09'),
(7, 4, 2, 'PUBLISHED', 'PUBLISHED', '', '2026-09-07 09:49:24'),
(8, 5, 1, NULL, 'DRAFT', 'Artikel dibuat sebagai draft.', '2026-09-08 16:56:30'),
(9, 5, 2, 'DRAFT', 'PUBLISHED', '', '2026-09-08 16:57:02'),
(10, 6, 1, NULL, 'DRAFT', 'Artikel dibuat sebagai draft.', '2026-09-10 09:33:16'),
(11, 6, 2, 'DRAFT', 'PUBLISHED', '', '2026-09-10 09:33:47'),
(12, 8, 1, NULL, 'DRAFT', 'Artikel dibuat sebagai draft.', '2026-09-21 12:35:00'),
(13, 8, 2, 'DRAFT', 'PUBLISHED', '', '2026-09-21 12:40:50'),
(14, 8, 2, 'PUBLISHED', 'REVISI', 'ulang', '2026-09-21 12:42:58'),
(16, 8, 2, 'REVISI', 'MENUNGGU_REVIEW', '', '2026-09-22 07:32:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `article_tags`
--

CREATE TABLE `article_tags` (
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `article_tags`
--

INSERT INTO `article_tags` (`article_id`, `tag_id`) VALUES
(4, 1),
(4, 2),
(4, 3),
(4, 4),
(4, 6),
(4, 12),
(4, 13),
(5, 1),
(5, 2),
(5, 11),
(5, 12),
(6, 1),
(6, 9),
(8, 5),
(8, 6),
(8, 14),
(8, 16),
(12, 1),
(12, 2),
(12, 5),
(12, 8),
(12, 11),
(13, 1),
(13, 5),
(13, 6),
(13, 11),
(13, 12);

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Politik', 'politik', 'Berita dan informasi seputar politik', '2026-09-05 09:02:45', '2026-09-05 09:02:45'),
(2, NULL, 'Ekonomi', 'ekonomi', 'Berita dan informasi seputar ekonomi', '2026-09-05 09:02:45', '2026-09-05 09:02:45'),
(3, NULL, 'Teknologi', 'teknologi', 'Berita dan informasi seputar teknologi', '2026-09-05 09:02:45', '2026-09-05 09:02:45'),
(4, NULL, 'Pendidikan', 'pendidikan', 'Berita dan informasi seputar pendidikan', '2026-09-05 09:02:45', '2026-09-05 09:02:45'),
(5, NULL, 'Lifestyle', 'lifestyle', 'Berita dan informasi seputar gaya hidup', '2026-09-05 09:02:45', '2026-09-05 09:02:45'),
(6, NULL, 'Olahraga', 'olahraga', 'Berita dan informasi seputar olahraga', '2026-09-05 09:02:45', '2026-09-05 09:02:45'),
(7, NULL, 'Nasional', 'nasional', 'Berita dan informasi nasional', '2026-09-05 09:02:45', '2026-09-05 09:02:45'),
(8, NULL, 'Daerah', 'daerah', 'Berita dan informasi seputar daerah', '2026-09-05 09:02:45', '2026-09-05 09:02:45'),
(9, NULL, 'Opini', 'opini', 'Artikel opini dan pandangan', '2026-09-05 09:02:45', '2026-09-05 09:02:45'),
(10, NULL, 'Investigasi', 'investigasi', 'Berita dan laporan investigasi', '2026-09-05 09:02:45', '2026-09-05 09:02:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tags`
--

CREATE TABLE `tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(1, 'Berita Terkini'),
(3, 'Bogor'),
(12, 'Daerah'),
(6, 'Ekonomi'),
(14, 'Hukum'),
(16, 'hukumindo'),
(19, 'hukumm'),
(2, 'Indonesia'),
(13, 'Investigasi'),
(4, 'Jawa Barat'),
(18, 'journal'),
(10, 'Lifestyle'),
(11, 'Nasional'),
(9, 'Olahraga'),
(17, 'paper'),
(8, 'Pendidikan'),
(5, 'Politik'),
(15, 'Sosial'),
(7, 'Teknologi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client','admin') NOT NULL DEFAULT 'client',
  `account_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `account_status`, `created_at`, `updated_at`) VALUES
(1, 'Tajul Alamsyah', 'alamsyaht933@gmail.com', '$2y$10$2hqd6vKdwviPg/nY4EOTgOKsohmyTsU3RdRX2aAH2/0adKhSU/Wle', 'client', 'active', '2026-09-05 08:28:42', '2026-09-05 08:28:42'),
(2, 'Zalil', 'zalil@gmail.com', '$2y$10$K1zELCJXGc84hmsZgTMlVuJJCU.kN7YPzEfFBx6Jv/kWHnfctHH8K', 'admin', 'active', '2026-09-06 14:36:43', '2026-09-06 14:40:34');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ads_status_order` (`status`,`sort_order`);

--
-- Indeks untuk tabel `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_articles_editor` (`last_edited_by`),
  ADD KEY `idx_articles_status` (`status`),
  ADD KEY `idx_articles_author` (`author_id`),
  ADD KEY `idx_articles_category` (`category_id`),
  ADD KEY `idx_articles_published_at` (`published_at`);
ALTER TABLE `articles` ADD FULLTEXT KEY `ft_articles_search` (`title`,`content`);

--
-- Indeks untuk tabel `article_images`
--
ALTER TABLE `article_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_images_article` (`article_id`);

--
-- Indeks untuk tabel `article_status_logs`
--
ALTER TABLE `article_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_logs_user` (`changed_by`),
  ADD KEY `idx_logs_article` (`article_id`);

--
-- Indeks untuk tabel `article_tags`
--
ALTER TABLE `article_tags`
  ADD PRIMARY KEY (`article_id`,`tag_id`),
  ADD KEY `fk_at_tag` (`tag_id`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indeks untuk tabel `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `ads`
--
ALTER TABLE `ads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `article_images`
--
ALTER TABLE `article_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `article_status_logs`
--
ALTER TABLE `article_status_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `tags`
--
ALTER TABLE `tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `fk_articles_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_articles_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_articles_editor` FOREIGN KEY (`last_edited_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `article_images`
--
ALTER TABLE `article_images`
  ADD CONSTRAINT `fk_images_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `article_status_logs`
--
ALTER TABLE `article_status_logs`
  ADD CONSTRAINT `fk_logs_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `article_tags`
--
ALTER TABLE `article_tags`
  ADD CONSTRAINT `fk_at_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_at_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
