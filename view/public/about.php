<?php
// Muat model hanya jika tersedia (selaras dengan legal.php)
$baseModelPath = __DIR__ . '/../../core/Model.php';
if (is_file($baseModelPath)) {
    require_once $baseModelPath;
}

$publicArticlePath = __DIR__ . '/../../module/model/PublicArticle.php';
if (is_file($publicArticlePath)) {
    require_once $publicArticlePath;
}

$trendingArticles = $trendingArticles ?? [];
$categories = $categories ?? [];
$pageTitle = 'Tentang Kami - INFORA SN | Media Online Nasional';
$pageDescription = 'Mengenal INFORA SN, rumah media massa digital yang mengusung jurnalisme publik, analisis mendalam, akurat, dan berimbang.';

/*
|--------------------------------------------------------------------------
| BASE URL FIXING
|--------------------------------------------------------------------------
*/
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$appBaseUrl = rtrim(dirname(dirname(dirname($scriptName))), '/');
if ($appBaseUrl === '/' || $appBaseUrl === '.') {
    $appBaseUrl = '';
}

/*
|--------------------------------------------------------------------------
| HEADER & NAVBAR WITH BUFFER CLEANUP (Sama persis dengan legal.php)
|--------------------------------------------------------------------------
*/
ob_start();
require __DIR__ . '/layout/header.php';
require __DIR__ . '/layout/navbar.php';
$layoutOutput = ob_get_clean();

// Memperbaiki URL Logo yang terduplikasi
$layoutOutput = preg_replace('#(/view/public)+/assets/img/Logo_infora\.jpeg#i', '/view/public/assets/img/Logo_infora.jpeg', $layoutOutput);
$layoutOutput = preg_replace('#(/infora-sn)+/view/public/view/public/#i', $appBaseUrl . '/view/public/', $layoutOutput);

echo $layoutOutput;
?>

<style>
    .about-wrapper {
        box-sizing: border-box;
        width: min(1100px, calc(100% - 40px));
        margin: 40px auto 64px;
        color: #101010;
        font-family: Arial, Helvetica, sans-serif;
    }

    /* Hero Banner Profil */
    .about-hero {
        background: #101010;
        color: #ffffff;
        padding: 44px 48px;
        border-bottom: 4px solid #d8ff00;
        margin-bottom: 28px;
    }

    .about-kicker {
        display: inline-block;
        color: #d8ff00;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .16em;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .about-hero h1 {
        margin: 0 0 14px;
        font-family: "Arial Narrow", "Roboto Condensed", Arial, sans-serif;
        font-size: 32px;
        font-weight: 900;
        line-height: 1.15;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .about-hero p {
        margin: 0;
        max-width: 780px;
        color: #dcdcd6;
        font-size: 14.5px;
        line-height: 1.7;
    }

    /* Grid Konten */
    .about-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        align-items: start;
    }

    .about-card {
        background: #ffffff;
        border: 1px solid #d7d7d1;
        padding: 34px 38px;
    }

    .about-section {
        margin-bottom: 28px;
        padding-bottom: 24px;
        border-bottom: 1px solid #ecebe7;
    }

    .about-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: 0;
    }

    .about-heading {
        margin: 0 0 14px;
        font-size: 17px;
        font-weight: 900;
        color: #101010;
        text-transform: uppercase;
        letter-spacing: .04em;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .about-heading::before {
        content: "";
        display: inline-block;
        width: 4px;
        height: 17px;
        background: #174bc8;
    }

    .about-text {
        margin: 0 0 14px;
        color: #2b2b2b;
        font-size: 14px;
        line-height: 1.8;
    }

    .about-text:last-child {
        margin-bottom: 0;
    }

    /* Pilar Nilai Jurnalisme */
    .pillars-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin-top: 16px;
    }

    .pillar-box {
        background: #f9f9f7;
        border: 1px solid #e2e2dc;
        border-left: 3px solid #101010;
        padding: 16px 18px;
    }

    .pillar-title {
        margin: 0 0 6px;
        font-size: 12.5px;
        font-weight: 900;
        color: #101010;
        text-transform: uppercase;
    }

    .pillar-desc {
        margin: 0;
        font-size: 12.5px;
        color: #555555;
        line-height: 1.6;
    }

    /* Sidebar Informasi */
    .about-sidebar {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .sidebar-box {
        background: #ffffff;
        border: 1px solid #d7d7d1;
        padding: 26px;
    }

    .sidebar-title {
        margin: 0 0 14px;
        padding-bottom: 10px;
        border-bottom: 2px solid #101010;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .info-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .info-list li {
        padding: 10px 0;
        border-bottom: 1px solid #ecebe7;
        font-size: 13px;
        line-height: 1.5;
    }

    .info-list li:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .info-label {
        display: block;
        font-size: 10px;
        font-weight: 900;
        color: #6b6b6b;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .sidebar-links {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .sidebar-link-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 14px;
        border: 1px solid #d7d7d1;
        background: #f9f9f7;
        font-size: 12px;
        font-weight: 900;
        color: #101010;
        transition: all .15s ease;
    }

    .sidebar-link-btn:hover {
        background: #101010;
        color: #ffffff;
        border-color: #101010;
    }

    @media (max-width: 900px) {
        .about-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .about-wrapper {
            margin: 24px auto 44px;
        }

        .about-hero {
            padding: 30px 22px;
        }

        .about-hero h1 {
            font-size: 24px;
        }

        .about-card {
            padding: 24px 20px;
        }

        .pillars-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<main class="about-wrapper">

    <!-- HERO PROFIL -->
    <section class="about-hero">
        <span class="about-kicker">Tentang Rumah Media Massa</span>
        <h1>INFORA.SN — Jurnalisme Publik &amp; Analisis</h1>
        <p>
            Portal media massa digital nasional yang berfokus pada penyajian berita faktual, liputan kebijakan publik, wacana intelektual, serta analisis mendalam untuk memperkuat literasi informasi masyarakat Indonesia.
        </p>
    </section>

    <!-- KONTEN UTAMA & SIDEBAR -->
    <div class="about-grid">

        <!-- KOLOM KIRI: PROFIL LENGKAP -->
        <article class="about-card">

            <div class="about-section">
                <h2 class="about-heading">Identitas &amp; Latar Belakang</h2>
                <p class="about-text">
                    <strong>INFORA.SN</strong> hadir sebagai ruang publik digital yang menjembatani fakta lapangan dengan kedalaman analisis. Di tengah derasnya arus informasi cepat yang kerap mengabaikan konteks, kami menempatkan akurasi, verifikasi berlapis, dan keberimbangan sebagai fondasi utama dalam setiap produk jurnalistik.
                </p>
                <p class="about-text">
                    Sebagai rumah media massa modern, INFORA.SN tidak hanya menyajikan laporan peristiwa terkini, tetapi juga membuka ruang dialog bagi akademisi, praktisi, penulis, dan masyarakat luas untuk berkontribusi melalui rubrik opini serta kajian strategis.
                </p>
            </div>

            <div class="about-section">
                <h2 class="about-heading">Visi &amp; Misi Redaksi</h2>
                <p class="about-text">
                    <strong>Visi:</strong> Menjadi referensi media daring nasional yang kredibel, independen, dan mencerdaskan kehidupan publik melalui jurnalisme berbasis data dan nurani.
                </p>
                <p class="about-text">
                    <strong>Misi Utama:</strong>
                </p>
                <div class="pillars-grid">
                    <div class="pillar-box">
                        <h3 class="pillar-title">1. Akurasi &amp; Verifikasi</h3>
                        <p class="pillar-desc">Setiap laporan melewati proses penyuntingan dan cek fakta ketat sebelum dipublikasikan ke ruang publik.</p>
                    </div>
                    <div class="pillar-box">
                        <h3 class="pillar-title">2. Kedalaman Analisis</h3>
                        <p class="pillar-desc">Mengurai duduk perkara di balik peristiwa agar pembaca memahami dampak kebijakan secara utuh.</p>
                    </div>
                    <div class="pillar-box">
                        <h3 class="pillar-title">3. Independensi Redaksi</h3>
                        <p class="pillar-desc">Bebas dari intervensi kepentingan partisan serta menjunjung tinggi Kode Etik Jurnalistik dan UU Pers.</p>
                    </div>
                    <div class="pillar-box">
                        <h3 class="pillar-title">4. Ruang Partisipasi Publik</h3>
                        <p class="pillar-desc">Memberdayakan penulis daerah dan akademisi untuk menyuarakan gagasan konstruktif bagi bangsa.</p>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2 class="about-heading">Standar Kerja Jurnalistik</h2>
                <p class="about-text">
                    Seluruh jajaran wartawan, editor, dan kontributor INFORA.SN bekerja berpedoman pada Undang-Undang Nomor 40 Tahun 1999 tentang Pers, Kode Etik Jurnalistik (KEJ) Dewan Pers, serta Pedoman Pemberitaan Media Siber. Kami menjamin hak jawab dan hak koreksi secara proporsional demi menjaga kepercayaan pembaca.
                </p>
            </div>

        </article>

        <!-- KOLOM KANAN: SIDEBAR INFORMASI & NAVIGASI DOKUMEN -->
        <aside class="about-sidebar">

            <div class="sidebar-box">
                <h2 class="sidebar-title">Informasi Media</h2>
                <ul class="info-list">
                    <li>
                        <span class="info-label">Nama Media</span>
                        <strong>INFORA.SN (Media Online Nasional)</strong>
                    </li>
                    <li>
                        <span class="info-label">Domain Resmi</span>
                        https://infora-sn.com
                    </li>
                    <li>
                        <span class="info-label">Fokus Rubrik</span>
                        Nasional, Kebijakan Publik, Hukum, Ekonomi, Pendidikan, Teknologi &amp; Opini
                    </li>
                    <li>
                        <span class="info-label">Email Redaksi</span>
                        redaksi@infora-sn.com
                    </li>
                </ul>
            </div>

            <div class="sidebar-box">
                <h2 class="sidebar-title">Dokumen &amp; Legalitas</h2>
                <div class="sidebar-links">
                    <a href="<?= htmlspecialchars($appBaseUrl, ENT_QUOTES, 'UTF-8') ?>/legal" class="sidebar-link-btn">
                        <span>Legalitas &amp; Surat Resmi</span>
                        <span>→</span>
                    </a>
                    <a href="<?= htmlspecialchars($appBaseUrl, ENT_QUOTES, 'UTF-8') ?>/redaksi" class="sidebar-link-btn">
                        <span>Susunan Tim Redaksi</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

        </aside>

    </div>

</main>

<?php
// Output Buffer untuk footer (Sama persis dengan legal.php)
ob_start();
$footerPath = __DIR__ . '/layout/footer.php';
if (is_file($footerPath)) {
    include_once $footerPath;
}
$footerOutput = ob_get_clean();
$footerOutput = preg_replace('#(/view/public)+/assets/img/Logo_infora\.jpeg#i', '/view/public/assets/img/Logo_infora.jpeg', $footerOutput);
$footerOutput = preg_replace('#(/infora-sn)+/view/public/view/public/#i', $appBaseUrl . '/view/public/', $footerOutput);

echo $footerOutput;
?>