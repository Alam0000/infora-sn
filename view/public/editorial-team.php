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
$pageTitle = 'Tim Redaksi - INFORA SN';

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
| SHARED HEADER & NAVBAR WITH BUFFER CLEANUP
|--------------------------------------------------------------------------
*/
ob_start();
require __DIR__ . '/layout/header.php';
require __DIR__ . '/layout/navbar.php';
$layoutOutput = ob_get_clean();

// Pembersihan URL Logo yang terduplikasi secara otomatis
$layoutOutput = preg_replace('#(/view/public)+/assets/img/Logo_infora\.jpeg#i', '/view/public/assets/img/Logo_infora.jpeg', $layoutOutput);
$layoutOutput = preg_replace('#(/infora-sn)+/view/public/view/public/#i', $appBaseUrl . '/view/public/', $layoutOutput);

echo $layoutOutput;
?>

<style>
    .editorial-wrapper {
        box-sizing: border-box;
        width: min(1000px, calc(100% - 40px));
        margin: 40px auto 60px;
        color: #222222;
        font-family: system-ui, -apple-system, sans-serif;
        line-height: 1.6;
    }

    .editorial-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .editorial-header h1 {
        font-size: 28px;
        font-weight: 800;
        color: #101010;
        margin: 0 0 10px;
        text-transform: uppercase;
        letter-spacing: -0.01em;
    }

    .editorial-header p {
        color: #666666;
        font-size: 14px;
        max-width: 650px;
        margin: 0 auto;
    }

    .team-group {
        margin-bottom: 40px;
    }

    .group-title {
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #101010;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .group-title::before {
        content: '';
        display: inline-block;
        width: 4px;
        height: 14px;
        background: #1755d1;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 20px;
    }

    .member-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .member-card:hover {
        border-color: #1755d1;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(23, 85, 209, 0.08);
    }

    .member-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #101010;
        color: #d8ff00;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    .member-info h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: #101010;
        line-height: 1.35;
    }

    .member-info p {
        margin: 4px 0 0;
        font-size: 12px;
        font-weight: 700;
        color: #1755d1;
    }

    .member-info .affiliation {
        font-size: 11px;
        color: #6b7280;
        margin-top: 3px;
    }

    @media (max-width: 640px) {
        .editorial-wrapper {
            margin: 24px auto 40px;
        }

        .editorial-header h1 {
            font-size: 22px;
        }

        .team-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<main class="editorial-wrapper">
    
    <header class="editorial-header">
        <h1>Susunan Tim Redaksi</h1>
        <p>Jajaran pimpinan, editor pelaksana, dan penyunting akademis yang mengelola operasional publikasi pemberitaan dan jurnal di INFORA SN Media Network.</p>
    </header>

    <!-- PIMPINAN REDAKSI -->
    <section class="team-group">
        <h2 class="group-title">Pimpinan &amp; Editor Pelaksana</h2>
        <div class="team-grid">
            
            <article class="member-card">
                <div class="member-avatar">AA</div>
                <div class="member-info">
                    <h3>Dr. (c) Ade Aspandi, S.Pd.I., M.Pd.</h3>
                    <p>Pemimpin Redaksi</p>
                    <div class="affiliation">Universitas Islam Al-Ihya Kuningan</div>
                </div>
            </article>

            <article class="member-card">
                <div class="member-avatar">AS</div>
                <div class="member-info">
                    <h3>Assoc. Prof. Dr. Aip Syarifudin, M.Pd.</h3>
                    <p>Editor Pelaksana</p>
                    <div class="affiliation">Universitas Muhammadiyah Cirebon</div>
                </div>
            </article>

        </div>
    </section>

    <!-- EDITOR & PENYUNTING -->
    <section class="team-group">
        <h2 class="group-title">Editor &amp; Tim Penyunting</h2>
        <div class="team-grid">

            <article class="member-card">
                <div class="member-avatar">TH</div>
                <div class="member-info">
                    <h3>Dr. (c) Taufik Hidayat, S.H., M.H.</h3>
                    <p>Editor Pendamping</p>
                    <div class="affiliation">Universitas Pamulang</div>
                </div>
            </article>

            <article class="member-card">
                <div class="member-avatar">WM</div>
                <div class="member-info">
                    <h3>Wildan Miftahussurur, S.Ag., S.H., M.Pd.</h3>
                    <p>Penyunting Naskah</p>
                    <div class="affiliation">IAI At-Taqwa Bondowoso</div>
                </div>
            </article>

            <article class="member-card">
                <div class="member-avatar">DA</div>
                <div class="member-info">
                    <h3>Diya Ul Akmal, S.H., M.H.</h3>
                    <p>Editor Tata Letak</p>
                    <div class="affiliation">Universitas Pamulang</div>
                </div>
            </article>

        </div>
    </section>

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