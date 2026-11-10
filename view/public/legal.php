<?php
// Muat model hanya jika tersedia.
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

/*
|--------------------------------------------------------------------------
| BASE URL FIXING
|--------------------------------------------------------------------------
*/
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$publicDir = str_replace('\\', '/', __DIR__);

// Mengambil root project (/infora-sn) dengan membuang '/view/public'
$appBaseUrl = rtrim(dirname(dirname(dirname($scriptName))), '/');
if ($appBaseUrl === '/' || $appBaseUrl === '.') {
    $appBaseUrl = '';
}

// Menentukan URL fisik relatif untuk PDF
$pdfRelativePath = 'assets/docs/AHU-THS_Law_Firm.pdf';
$pdfFilePath = __DIR__ . '/' . $pdfRelativePath;
$pdfUrl = $appBaseUrl . '/view/public/' . $pdfRelativePath;
$pdfExists = is_file($pdfFilePath);

/*
|--------------------------------------------------------------------------
| HEADER & NAVBAR WITH BUFFER CLEANUP
|--------------------------------------------------------------------------
*/
ob_start();
require __DIR__ . '/layout/header.php';
require __DIR__ . '/layout/navbar.php';
$layoutOutput = ob_get_clean();

// Memperbaiki URL Logo yang terduplikasi akibat concatenation $baseUrl . $logoPath
$correctedLogoUrl = $appBaseUrl . '/view/public/assets/img/Logo_infora.jpeg';
$layoutOutput = preg_replace('#(/view/public)+/assets/img/Logo_infora\.jpeg#i', '/view/public/assets/img/Logo_infora.jpeg', $layoutOutput);
$layoutOutput = preg_replace('#(/infora-sn)+/view/public/view/public/#i', $appBaseUrl . '/view/public/', $layoutOutput);

echo $layoutOutput;
?>

<style>
    .legal-wrapper {
        box-sizing: border-box;
        width: min(1000px, calc(100% - 40px));
        margin: 40px auto 60px;
        color: #222222;
        font-family: system-ui, -apple-system, sans-serif;
    }

    .legal-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .legal-header h1 {
        font-size: 26px;
        font-weight: 800;
        color: #101010;
        margin: 0 0 8px;
        text-transform: uppercase;
    }

    .legal-header p {
        color: #666666;
        font-size: 14px;
        margin: 0;
    }

    /* Container PDF Viewer */
    .pdf-viewer-container {
        position: relative;
        width: 100%;
        height: 750px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        background: #525659;
    }

    .pdf-viewer-container object,
    .pdf-viewer-container embed {
        width: 100%;
        height: 100%;
        border: none;
    }

    .pdf-actions {
        margin-top: 16px;
        text-align: right;
    }

    .btn-download {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        background: #101010;
        color: #ffffff;
        text-decoration: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        transition: background 0.15s ease;
    }

    .btn-download:hover {
        background: #2d2d2d;
    }

    .pdf-fallback {
        color: #fff;
        text-align: center;
        padding: 24px;
    }

    .pdf-fallback a {
        color: #d8ff00;
    }

    @media (max-width: 640px) {
        .pdf-viewer-container {
            height: 500px;
        }
    }
</style>

<main class="legal-wrapper">
    <header class="legal-header">
        <h1>Legalitas &amp; Surat Resmi</h1>
        <p>Dokumen hukum dan perizinan operasional publikasi INFORA SN Media Network</p>
    </header>

    <section class="pdf-viewer-container" aria-label="Pratinjau dokumen legalitas">
        <?php if ($pdfExists): ?>
            <object data="<?= htmlspecialchars($pdfUrl, ENT_QUOTES, 'UTF-8') ?>"
                    type="application/pdf"
                    width="100%"
                    height="100%">
                <div class="pdf-fallback">
                    Browser Anda tidak dapat menampilkan PDF secara otomatis.
                    <p><a href="<?= htmlspecialchars($pdfUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">
                        Buka dokumen PDF
                    </a></p>
                </div>
            </object>
        <?php else: ?>
            <div class="pdf-fallback">
                <p>Dokumen PDF belum ditemukan.</p>
                <small>Pastikan file tersedia di: <?= htmlspecialchars($pdfRelativePath, ENT_QUOTES, 'UTF-8') ?></small>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($pdfExists): ?>
        <div class="pdf-actions">
            <a href="<?= htmlspecialchars($pdfUrl, ENT_QUOTES, 'UTF-8') ?>"
               target="_blank"
               rel="noopener"
               class="btn-download"
               download>
                📥 Unduh Dokumen Resmi (PDF)
            </a>
        </div>
    <?php endif; ?>
</main>

<?php
// Output Buffer untuk footer agar logo di footer juga bebas 404
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