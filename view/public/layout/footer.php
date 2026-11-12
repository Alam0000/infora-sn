<?php

$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

if ($baseUrl === '/' || $baseUrl === '\\') {
    $baseUrl = '';
}

/*
|--------------------------------------------------------------------------
| LINK LAYANAN JURNAL
|--------------------------------------------------------------------------
| Ubah di sini jika alamatnya berganti.
*/
$journalHouseUrl = 'https://zaliljournal.com/scienta';

// Sementara diarahkan ke alamat yang sama. Ganti dengan link
// WhatsApp / halaman kontak jika sudah ada.
$journalWritingUrl = 'https://zaliljournal.com/scienta';


/*
|--------------------------------------------------------------------------
| LOGO
|--------------------------------------------------------------------------
| Sama dengan logo di layout/header.php.
*/
$logoPath = '/view/public/assets/img/Logo_infora.jpeg';


/*
|--------------------------------------------------------------------------
| MEDIA SOSIAL
|--------------------------------------------------------------------------
| Isi 'url' untuk mengaktifkan link. Kalau masih kosong (''),
| ikon tetap tampil tetapi belum bisa diklik.
*/
$socials = [

    [
        'name'  => 'Instagram',
        'short' => 'IG',
        'url'   => 'https://www.instagram.com/pendampingan_akademik',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" stroke="none"/></svg>',
    ],

    [
        'name'  => 'TikTok',
        'short' => 'TT',
        'url'   => 'https://www.tiktok.com/@ruangtugas_juniormz',
        'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 3c.4 2.2 1.8 3.7 4 3.9v2.9c-1.4.1-2.7-.3-4-1.1v6.4c0 3.3-2.7 5.9-6 5.9s-6-2.6-6-5.9 2.7-5.9 6-5.9c.3 0 .6 0 1 .1v3c-.3-.1-.6-.2-1-.2-1.6 0-2.9 1.3-2.9 3s1.3 3 2.9 3 3-1.3 3-3V3h3z"/></svg>',
    ],

];

?>

<style>

    /* =====================================================
       FOOTER INFORA SN
    ===================================================== */

    .site-footer {
        width: 100%;
        background: #101010;
        color: #ffffff;
        margin-top: 0;
    }

    .footer-container {
        width: min(1240px, calc(100% - 110px));
        margin-left: auto;
        margin-right: auto;
    }

    /* =====================================================
       FOOTER UTAMA
    ===================================================== */

    .footer-main {
        padding: 55px 0 45px;
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr;
        gap: 55px;
    }

    /* =====================================================
       FOOTER BRAND
    ===================================================== */

    .footer-brand {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        background: #ffffff;
        border-radius: 8px;
    }

    .footer-brand img {
        display: block;
        height: 64px;
        width: auto;
        max-width: 100%;
        object-fit: contain;
    }

    .footer-description {
        max-width: 420px;
        margin: 16px 0 0;
        color: #999999;
        font-size: 11px;
        line-height: 1.8;
    }

    /* =====================================================
       MEDIA SOSIAL
    ===================================================== */

    .footer-social-title {
        margin: 26px 0 12px;
        color: #ffffff;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .footer-social-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .footer-social {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #2c2c2c;
        border-radius: 8px;
        background: #161616;
        color: #bdbdbd;
        transition:
            color .15s ease,
            border-color .15s ease,
            background .15s ease;
    }

    .footer-social svg {
        width: 18px;
        height: 18px;
    }

    .footer-social:hover {
        border-color: #d8ff00;
        background: #d8ff00;
        color: #101010;
    }

    /* =====================================================
       FOOTER TITLE
    ===================================================== */

    .footer-title {
        margin: 0 0 16px;
        color: #ffffff;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    /* =====================================================
       FOOTER LINKS
    ===================================================== */

    .footer-title-sub {
        margin-top: 28px;
    }

    .footer-links {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .footer-links a {
        color: #999999;
        font-size: 11px;
        transition: color .15s ease;
    }

    .footer-links a:hover {
        color: #d8ff00;
    }

    /* =====================================================
       FOOTER CONTACT
    ===================================================== */

    .footer-contact-list {
        list-style: none;
        margin: 18px 0 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .footer-contact-list a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #999999;
        font-size: 11px;
        transition: color .15s ease;
    }

    .footer-contact-list a:hover {
        color: #d8ff00;
    }

    .footer-contact-list a:hover .footer-contact-icon {
        color: #101010;
        background: #d8ff00;
    }

    .footer-contact-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        flex: 0 0 20px;
        border-radius: 4px;
        background: #161616;
        color: #d8ff00;
        font-size: 10px;
        transition:
            color .15s ease,
            background .15s ease;
    }

    /* =====================================================
       FOOTER BOTTOM
    ===================================================== */

    .footer-bottom {
        padding: 18px 0;
        border-top: 1px solid #292929;
        color: #777777;
        font-size: 10px;
    }

    /* =====================================================
       RESPONSIVE
    ===================================================== */

    @media (max-width: 1000px) {
        .footer-container {
            width: calc(100% - 40px);
        }

        .footer-main {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 650px) {
        .footer-container {
            width: calc(100% - 28px);
        }

        .footer-main {
            grid-template-columns: 1fr;
            gap: 32px;
            padding: 45px 0 35px;
        }
    }

</style>


<footer class="site-footer">

    <div class="footer-container">

        <!-- =================================================
             FOOTER UTAMA
        ================================================== -->

        <div class="footer-main">

            <!-- =================================================
                 BRAND
            ================================================== -->

            <div>

                <a
                    href="<?= $baseUrl ?>/"
                    class="footer-brand"
                    aria-label="INFORA SN"
                >

                    <img
                        src="<?= $baseUrl . $logoPath ?>"
                        alt="INFORA SN"
                    >

                </a>

                <p class="footer-description">
                    Jurnalisme publik dan analisis
                    yang menghadirkan informasi secara
                    informatif, kritis, dan bertanggung
                    jawab.
                </p>                    

                <!-- MEDIA SOSIAL -->

                <h3 class="footer-social-title">
                    Ikuti Kami
                </h3>

                <div class="footer-social-list">

                    <?php foreach ($socials as $social): ?>

                        <?php $hasUrl = !empty($social['url']); ?>

                        <a
                            href="<?= $hasUrl
                                ? htmlspecialchars($social['url'])
                                : '#' ?>"
                            class="footer-social"
                            title="<?= htmlspecialchars($social['name']) ?>"
                            aria-label="<?= htmlspecialchars($social['name']) ?>"
                            <?php if ($hasUrl): ?>
                                target="_blank"
                                rel="noopener"
                            <?php else: ?>
                                onclick="return false;"
                            <?php endif; ?>
                        >
                            <?= $social['icon'] ?>
                        </a>

                    <?php endforeach; ?>

                </div>

            </div>


            <!-- =================================================
                 RUBRIKASI
            ================================================== -->

            <div>

                <h3 class="footer-title">
                    Rubrikasi
                </h3>

                <div class="footer-links">

                    <a href="<?= $baseUrl ?>/">
                        Utama
                    </a>

                    <a href="<?= $baseUrl ?>/category/politik">
                        Politik
                    </a>

                    <a href="<?= $baseUrl ?>/category/ekonomi">
                        Ekonomi
                    </a>

                    <a href="<?= $baseUrl ?>/category/teknologi">
                        Teknologi
                    </a>

                    <a href="<?= $baseUrl ?>/category/pendidikan">
                        Pendidikan
                    </a>

                    <a href="<?= $baseUrl ?>/category/lifestyle">
                        Lifestyle
                    </a>

                </div>

            </div>


            <!-- =================================================
                 INFORA NETWORK
            ================================================== -->

            <div>

                <h3 class="footer-title">
                    Infora Network
                </h3>

                <div class="footer-links">

                    <a href="<?= $baseUrl ?>/view/public/about.php">
                        Tentang INFORA SN
                    </a>

                    <a href="<?= $baseUrl ?>/view/public/editorial-team.php">
                        Tim Redaksi
                    </a>

                    <a href="<?= $baseUrl ?>/view/public/legal.php">
                        Legalitas &amp; Surat Resmi
                    </a>

                    <a href="<?= $baseUrl ?>/login">
                        Ruang Penulis
                    </a>

                    <a href="<?= $baseUrl ?>/register">
                        Menjadi Kontributor
                    </a>

                    <a href="<?= $baseUrl ?>/">
                        Pedoman &amp; Etika
                    </a>

                </div>


                <!-- LAYANAN JURNAL -->

                <h3 class="footer-title footer-title-sub">
                    Layanan Jurnal
                </h3>

                <div class="footer-links">

                    <a
                        href="<?= htmlspecialchars($journalWritingUrl) ?>"
                        target="_blank"
                        rel="noopener"
                    >
                        Bantuan Penulisan Jurnal
                    </a>

                    <a
                        href="<?= htmlspecialchars($journalHouseUrl) ?>"
                        target="_blank"
                        rel="noopener"
                    >
                        Rumah Jurnal (Scienta)
                    </a>

                </div>

            </div>

        </div>


        <!-- =================================================
             COPYRIGHT
        ================================================== -->

        <div class="footer-bottom">

            © <?= date('Y') ?>
            INFORA SN Media Network.
            Seluruh hak cipta dilindungi.

        </div>

    </div>

</footer>
<?php require_once __DIR__ . '/../shared/whatsapp.php'; ?>
