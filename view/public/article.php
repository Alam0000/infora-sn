<?php

/* =========================================================
   INFORA SN
   ARTICLE DETAIL
========================================================= */


/* =========================================================
   BASE URL
========================================================= */

function articleDetectBaseUrl()
{
    $projectRoot = realpath(dirname(__DIR__, 2));
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');

    if ($projectRoot === false || $documentRoot === false) {
        return '';
    }

    $projectRoot = str_replace('\\', '/', $projectRoot);
    $documentRoot = rtrim(
        str_replace('\\', '/', $documentRoot),
        '/'
    );

    if (stripos($projectRoot, $documentRoot) === 0) {
        return rtrim(
            substr(
                $projectRoot,
                strlen($documentRoot)
            ),
            '/'
        );
    }

    return '';
}

$baseUrl = articleDetectBaseUrl();


/* =========================================================
   DATA DEFAULT
========================================================= */

$article = $article ?? [];

$tags = $tags ?? [];

$related = $related ?? [];

$popular = $popular ?? [];


/* =========================================================
   AMBIL IKLAN AKTIF
========================================================= */

/*
|--------------------------------------------------------------------------
| Ambil iklan lewat model AdminAd (sama seperti panel admin
| kelola iklan), bukan lewat variabel koneksi mysqli mentah.
|
| Ini lebih aman karena koneksinya ditangani oleh base class
| Model, tidak bergantung pada require_once config/database.php
| yang bisa jadi no-op kalau file itu sudah pernah di-require
| sebelumnya di request yang sama (menyebabkan $koneksi tidak
| pernah ter-set di scope file ini).
|
| Iklan pertama (berdasarkan sort_order) digunakan di sidebar
| detail artikel.
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__, 2) . '/module/model/AdminAd.php';

$activeAds = [];

try {

    $adModel = new AdminAd();

    $allAds = $adModel->getAllAds();

    foreach ($allAds as $adRow) {

        if (($adRow['status'] ?? '') === 'ACTIVE') {
            $activeAds[] = $adRow;
        }

    }

    usort(
        $activeAds,
        function ($a, $b) {
            return (int) ($a['sort_order'] ?? 0)
                <=> (int) ($b['sort_order'] ?? 0);
        }
    );

} catch (Throwable $e) {

    error_log(
        'Gagal mengambil iklan artikel: '
        . $e->getMessage()
    );

}

$adTop = $activeAds[0] ?? null;


/* =========================================================
   ARTICLE DEFAULT
========================================================= */

if (empty($article)) {

    http_response_code(404);

    $article = [
        'title' => 'Artikel Tidak Ditemukan',
        'excerpt' => '',
        'content' => '<p>Artikel yang Anda cari tidak tersedia.</p>',
        'thumbnail_path' => '',
        'author_name' => 'Redaksi INFORA',
        'category_name' => '',
        'category_slug' => '',
        'published_at' => null,
        'updated_at' => null,
        'view_count' => 0,
        'slug' => ''
    ];

}


/* =========================================================
   HELPER IMAGE
========================================================= */

function articleImageUrl($path)
{
    global $baseUrl;

    $path = trim(
        (string) $path
    );

    if ($path === '') {
        return '';
    }


    if (
        preg_match(
            '/^https?:\/\//i',
            $path
        )
    ) {
        return $path;
    }


    $path = str_replace(
        '\\',
        '/',
        $path
    );

    $path = ltrim(
        $path,
        '/'
    );


    $normalizedBase = trim(
        str_replace(
            '\\',
            '/',
            (string) $baseUrl
        ),
        '/'
    );


    if (
        $normalizedBase !== '' &&
        strpos(
            $path,
            $normalizedBase . '/'
        ) === 0
    ) {

        return '/' . $path;

    }


    return '/infora-sn/' . $path;
}


/* =========================================================
   ARTICLE URL
========================================================= */

function articleUrl(
    $baseUrl,
    $slug
) {

    return $baseUrl
        . '/article/'
        . rawurlencode(
            $slug
        );

}


/* =========================================================
   CATEGORY URL
========================================================= */

function articleCategoryUrl(
    $baseUrl,
    $slug
) {

    return $baseUrl
        . '/category/'
        . rawurlencode(
            $slug
        );

}


/* =========================================================
   DATE INDONESIA
========================================================= */

function articleDateIndonesia($date)
{
    if (empty($date)) {
        return '-';
    }


    $timestamp = strtotime(
        $date
    );


    if (!$timestamp) {
        return '-';
    }


    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];


    return date(
        'd',
        $timestamp
    )
        . ' '
        . $months[
            (int) date(
                'n',
                $timestamp
            )
        ]
        . ' '
        . date(
            'Y',
            $timestamp
        )
        . ', '
        . date(
            'H:i',
            $timestamp
        )
        . ' WIB';
}


/* =========================================================
   SHORT DATE
========================================================= */

function articleShortDate($date)
{
    if (empty($date)) {
        return '-';
    }


    $timestamp = strtotime(
        $date
    );


    if (!$timestamp) {
        return '-';
    }


    return date(
        'd/m/Y',
        $timestamp
    );
}


/* =========================================================
   READING TIME
========================================================= */

function articleReadingTime($content)
{
    $text = trim(
        strip_tags(
            $content ?? ''
        )
    );


    if ($text === '') {
        return 1;
    }


    $words = preg_split(
        '/\s+/',
        $text
    );


    return max(
        1,
        (int) ceil(
            count($words) / 200
        )
    );
}


/* =========================================================
   INITIAL
========================================================= */

function articleInitial($name)
{
    $name = trim(
        (string) $name
    );


    if ($name === '') {
        return '?';
    }


    return mb_strtoupper(
        mb_substr(
            $name,
            0,
            1
        )
    );
}


/* =========================================================
   PAGE META
========================================================= */

$pageTitle =
    ($article['title'] ?? 'Artikel')
    . ' - INFORA SN';


$pageDescription =
    !empty(
        $article['excerpt']
    )
        ? $article['excerpt']
        : 'INFORA SN - Jurnalisme Publik & Analisis.';


/* =========================================================
   SHARE URL
========================================================= */

$sharePath = articleUrl(
    $baseUrl,
    $article['slug'] ?? ''
);


/*
|--------------------------------------------------------------------------
| URL absolut untuk share.
|--------------------------------------------------------------------------
*/

if (
    preg_match(
        '/^https?:\/\//i',
        $sharePath
    )
) {

    $shareUrl = $sharePath;

} else {

    $scheme =
        (
            !empty(
                $_SERVER['HTTPS']
            )
            &&
            $_SERVER['HTTPS'] !== 'off'
        )
        ||
        (
            isset(
                $_SERVER['SERVER_PORT']
            )
            &&
            (int) $_SERVER['SERVER_PORT'] === 443
        )
            ? 'https'
            : 'http';


    $host =
        $_SERVER['HTTP_HOST']
        ?? 'localhost';


    $shareUrl =
        $scheme
        . '://'
        . $host
        . $sharePath;

}


$encodedShareUrl =
    rawurlencode(
        $shareUrl
    );


$encodedShareTitle =
    rawurlencode(
        $article['title']
        ?? 'Artikel INFORA SN'
    );


/* =========================================================
   HEADER
========================================================= */

require __DIR__ . '/layout/header.php';


$baseUrl = Auth::baseUrl();


/* =========================================================
   NAVBAR
========================================================= */

require __DIR__ . '/layout/navbar.php';

?>


<style>

/* =========================================================
   ARTICLE PAGE
========================================================= */

.infora-article {

    width: 100%;

    background: #f7f7f4;

    color: #111111;

    padding-bottom: 50px;

}


.article-inner {

    width: min(
        1240px,
        calc(100% - 70px)
    );

    margin: 0 auto;

}


/* =========================================================
   BREADCRUMB
========================================================= */

.article-breadcrumb {

    display: flex;

    align-items: center;

    flex-wrap: wrap;

    gap: 6px;

    padding: 14px 0 10px;

    color: #888888;

    font-size: 8px;

}


.article-breadcrumb a {

    color: #174bc8;

    font-weight: 800;

}


.article-breadcrumb .current {

    color: #555555;

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;

    max-width: 350px;

}


/* =========================================================
   MAIN LAYOUT
========================================================= */

.article-layout {

    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        285px;

    gap: 18px;

    align-items: start;

}


/* =========================================================
   ARTICLE MAIN
========================================================= */

.article-main {

    background: #ffffff;

    border: 1px solid #deded9;

    overflow: hidden;

}


.article-header {

    padding: 24px 26px 0;

}


.article-category {

    display: inline-block;

    margin-bottom: 10px;

    padding: 4px 8px;

    background: #101827;

    color: #d8ff00;

    font-size: 7px;

    font-weight: 900;

    text-transform: uppercase;

    letter-spacing: .05em;

}


.article-title {

    margin: 0;

    color: #111111;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size:
        clamp(
            23px,
            2.6vw,
            34px
        );

    line-height: 1.13;

    letter-spacing: -.02em;

}


.article-excerpt {

    margin: 12px 0 0;

    color: #555555;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 11px;

    line-height: 1.55;

}


.article-meta-row {

    display: flex;

    align-items: center;

    flex-wrap: wrap;

    gap: 8px;

    padding: 16px 0 18px;

    border-bottom: 1px solid #e4e4df;

    color: #777777;

    font-size: 8px;

}


.article-meta-avatar {

    width: 23px;

    height: 23px;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #174bc8;

    color: #ffffff;

    font-size: 9px;

    font-weight: 900;

}


.article-meta-author {

    color: #111111;

    font-weight: 800;

}


.article-meta-divider {

    color: #cccccc;

}


/* =========================================================
   FEATURE IMAGE
========================================================= */

/* =========================================================
   FEATURE IMAGE
   Mengikuti rasio gambar asli hasil crop di admin (tanpa dipotong lagi)
========================================================= */

.article-featured-image {

    width: 100%;

    overflow: hidden;

    background: #e1e3e5;

    line-height: 0;

}


.article-featured-image img {

    width: 100%;

    height: auto;

    display: block;

}


/* Placeholder kalau artikel tidak punya thumbnail */

.article-featured-placeholder {

    width: 100%;

    aspect-ratio: 16 / 9;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #999999;

    font-size: 10px;

    font-weight: 900;

    line-height: normal;

}


/* =========================================================
   CONTENT
========================================================= */

.article-content {

    padding: 24px 26px 20px;

    color: #222222;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 12px;

    line-height: 1.85;

}


.article-content p {

    margin: 0 0 15px;

}


.article-content h2,
.article-content h3 {

    color: #111111;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    line-height: 1.3;

}


.article-content h2 {

    margin: 24px 0 10px;

    font-size: 18px;

}


.article-content h3 {

    margin: 20px 0 8px;

    font-size: 15px;

}


.article-content img {

    max-width: 100%;

    height: auto;

    display: block;

    margin: 15px 0;

}


.article-content a {

    color: #174bc8;

    text-decoration: underline;

}


.article-content blockquote {

    margin: 18px 0;

    padding: 10px 16px;

    border-left: 3px solid #174bc8;

    background: #f5f6f7;

    color: #444444;

    font-style: italic;

}


.article-content ul,
.article-content ol {

    padding-left: 20px;

    margin-bottom: 15px;

}


/* =========================================================
   TAGS
========================================================= */

.article-tags {

    display: flex;

    flex-wrap: wrap;

    gap: 6px;

    padding: 0 26px 20px;

}


.article-tag {

    padding: 4px 8px;

    background: #eef2ff;

    color: #174bc8;

    font-size: 7px;

    font-weight: 800;

}


/* =========================================================
   SHARE
========================================================= */

.article-share {

    margin: 0 26px 20px;

    padding-top: 14px;

    border-top: 1px solid #e4e4df;

}


.article-share-title {

    margin: 0 0 9px;

    color: #111111;

    font-size: 8px;

    font-weight: 900;

    text-transform: uppercase;

    letter-spacing: .05em;

}


.article-share-buttons {

    display: flex;

    flex-wrap: wrap;

    gap: 6px;

}


.article-share-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 6px;

    min-height: 30px;

    padding: 0 10px;

    border: 1px solid #dddddd;

    border-radius: 3px;

    background: #ffffff;

    color: #333333;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 7px;

    font-weight: 900;

    text-decoration: none;

    cursor: pointer;

    box-sizing: border-box;

    transition: .15s ease;

}


.article-share-button:hover {

    transform: translateY(-1px);

    background: #f5f5f5;

}


.article-share-icon {

    width: 15px;

    height: 15px;

    border-radius: 50%;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    color: #ffffff;

    font-size: 6px;

    font-weight: 900;

}


.share-whatsapp .article-share-icon {

    background: #25D366;

}


.share-facebook .article-share-icon {

    background: #1877F2;

}


.share-x .article-share-icon {

    background: #111111;

}


.share-telegram .article-share-icon {

    background: #229ED9;

}


.share-copy .article-share-icon {

    background: #777777;

}


.article-share-copy-message {

    display: none;

    margin-top: 7px;

    color: #174bc8;

    font-size: 7px;

    font-weight: 800;

}


.article-share-copy-message.show {

    display: block;

}


/* =========================================================
   STATS
========================================================= */

.article-stats-bar {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 10px;

    margin: 0 26px 20px;

    padding: 10px 12px;

    background: #f7f7f4;

    border: 1px solid #e4e4df;

    color: #777777;

    font-size: 7px;

}


/* =========================================================
   AUTHOR
========================================================= */

.article-author-box {

    display: flex;

    align-items: center;

    gap: 12px;

    margin: 0 26px 26px;

    padding: 15px;

    background: #f7f7f4;

    border: 1px solid #e4e4df;

}


.article-author-avatar {

    width: 42px;

    height: 42px;

    flex: 0 0 42px;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #174bc8;

    color: #ffffff;

    font-size: 15px;

    font-weight: 900;

}


.article-author-name {

    margin: 0 0 3px;

    color: #111111;

    font-size: 10px;

    font-weight: 900;

}


.article-author-role {

    margin: 0;

    color: #888888;

    font-size: 7px;

}


/* =========================================================
   SIDEBAR
========================================================= */

.article-sidebar-panel {

    background: #ffffff;

    border: 1px solid #deded9;

}


.article-sidebar-header {

    padding: 10px 11px;

    border-bottom: 1px solid #deded9;

}


.article-sidebar-title {

    display: flex;

    align-items: center;

    gap: 6px;

    margin: 0;

    color: #111111;

    font-size: 12px;

    font-weight: 900;

}


.article-sidebar-title::before {

    content: '';

    width: 4px;

    height: 14px;

    background: #174bc8;

}


/* =========================================================
   POPULAR
========================================================= */

.sidebar-popular-list {

    padding: 0 11px;

}


.sidebar-popular-item {

    display: grid;

    grid-template-columns:
        27px
        minmax(0, 1fr);

    gap: 7px;

    padding: 11px 0;

    border-bottom: 1px solid #e4e4df;

}


.sidebar-popular-item:last-child {

    border-bottom: none;

}


.sidebar-popular-number {

    color: #d0d0d0;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 20px;

    line-height: 1;

}


.sidebar-popular-category {

    margin-bottom: 3px;

    color: #174bc8;

    font-size: 6px;

    font-weight: 900;

    text-transform: uppercase;

}


.sidebar-popular-title {

    margin: 0;

    color: #222222;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 11px;

    line-height: 1.3;

}


.sidebar-popular-title a {

    color: inherit;

    text-decoration: none;

}


.sidebar-popular-title a:hover {

    color: #174bc8;

}


.sidebar-popular-meta {

    margin-top: 5px;

    color: #888888;

    font-size: 6px;

}


.sidebar-empty {

    padding: 18px 11px;

    color: #999999;

    font-size: 8px;

}


/* =========================================================
   IKLAN
========================================================= */

.article-sidebar-ad {

    width: 100%;

    margin-top: 15px;

    background: #ffffff;

    border: 1px dashed #c9cdd3;

    overflow: hidden;

    box-sizing: border-box;

}


.article-sidebar-ad-link {

    display: block;

    width: 100%;

    height: 250px;

}


.article-sidebar-ad-image {

    width: 100%;

    height: 250px;

    display: block;

    object-fit: cover;

}


.article-sidebar-ad-placeholder {

    width: 100%;

    height: 250px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #ffffff;

    color: #9aa0a8;

    font-size: 9px;

    font-weight: 900;

    text-transform: uppercase;

    letter-spacing: .06em;

}


/* =========================================================
   RELATED
========================================================= */

.related-section {

    margin-top: 25px;

}


.related-heading {

    display: flex;

    align-items: center;

    gap: 7px;

    margin-bottom: 12px;

}


.related-heading::before {

    content: '';

    width: 4px;

    height: 15px;

    background: #174bc8;

}


.related-heading h2 {

    margin: 0;

    color: #111111;

    font-size: 13px;

    font-weight: 900;

}


.related-grid {

    display: grid;

    grid-template-columns:
        repeat(
            4,
            minmax(0, 1fr)
        );

    gap: 12px;

}


.related-card {

    background: #ffffff;

    border: 1px solid #deded9;

    overflow: hidden;

}


.related-card-image {

    aspect-ratio: 16 / 9;

    overflow: hidden;

    background: #e1e3e5;

}


.related-card-image img {

    width: 100%;

    height: 100%;

    display: block;

    object-fit: cover;

}


.related-card-placeholder {

    width: 100%;

    height: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #999999;

    font-size: 8px;

    font-weight: 900;

}


.related-card-body {

    padding: 9px 10px 10px;

}


.related-card-category {

    margin-bottom: 4px;

    color: #174bc8;

    font-size: 6px;

    font-weight: 900;

    text-transform: uppercase;

}


.related-card-title {

    margin: 0;

    color: #111111;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 12px;

    line-height: 1.3;

}


.related-card-title a {

    color: inherit;

    text-decoration: none;

}


.related-card-title a:hover {

    color: #174bc8;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1000px) {

    .article-inner {

        width: calc(100% - 40px);

    }


    .article-layout {

        grid-template-columns: 1fr;

    }


    .related-grid {

        grid-template-columns:
            repeat(
                2,
                minmax(0, 1fr)
            );

    }

}


@media (max-width: 550px) {

    .article-inner {

        width: calc(100% - 24px);

    }


    .article-header {

        padding: 18px 16px 0;

    }


    .article-content {

        padding: 18px 16px 20px;

    }


    .article-tags {

        padding: 0 16px 18px;

    }


    .article-share {

        margin: 0 16px 18px;

    }


    .article-stats-bar {

        margin: 0 16px 18px;

    }


    .article-author-box {

        margin: 0 16px 20px;

    }


    .article-share-button {

        flex: 1 1 calc(50% - 5px);

    }


    .related-grid {

        grid-template-columns: 1fr;

    }

}

</style>


<main class="infora-article">

    <div class="article-inner">


        <!-- =====================================================
             BREADCRUMB
        ====================================================== -->

        <nav class="article-breadcrumb">

            <a href="<?= $baseUrl ?>/">
                Utama
            </a>

            <span>/</span>


            <?php if (
                !empty(
                    $article['category_slug']
                )
            ): ?>

                <a
                    href="<?= articleCategoryUrl(
                        $baseUrl,
                        $article['category_slug']
                    ) ?>"
                >

                    <?= htmlspecialchars(
                        $article['category_name']
                        ?? ''
                    ) ?>

                </a>

                <span>/</span>

            <?php endif; ?>


            <span class="current">

                <?= htmlspecialchars(
                    $article['title']
                    ?? ''
                ) ?>

            </span>

        </nav>


        <!-- =====================================================
             MAIN CONTENT + SIDEBAR
        ====================================================== -->

        <div class="article-layout">


            <!-- =================================================
                 ARTICLE
            ================================================== -->

            <article class="article-main">


                <div class="article-header">


                    <?php if (
                        !empty(
                            $article['category_name']
                        )
                    ): ?>

                        <a
                            href="<?= articleCategoryUrl(
                                $baseUrl,
                                $article['category_slug']
                                ?? ''
                            ) ?>"
                            class="article-category"
                        >

                            <?= htmlspecialchars(
                                $article['category_name']
                            ) ?>

                        </a>

                    <?php endif; ?>


                    <h1 class="article-title">

                        <?= htmlspecialchars(
                            $article['title']
                            ?? 'Artikel'
                        ) ?>

                    </h1>


                    <?php if (
                        !empty(
                            $article['excerpt']
                        )
                    ): ?>

                        <p class="article-excerpt">

                            <?= htmlspecialchars(
                                $article['excerpt']
                            ) ?>

                        </p>

                    <?php endif; ?>


                    <div class="article-meta-row">


                        <span class="article-meta-avatar">

                            <?= articleInitial(
                                $article['author_name']
                                ?? ''
                            ) ?>

                        </span>


                        <span class="article-meta-author">

                            <?= htmlspecialchars(
                                $article['author_name']
                                ?? 'Redaksi INFORA'
                            ) ?>

                        </span>


                        <span class="article-meta-divider">
                            •
                        </span>


                        <span>

                            <?= articleDateIndonesia(
                                $article['published_at']
                                ?? null
                            ) ?>

                        </span>


                        <span class="article-meta-divider">
                            •
                        </span>


                        <span>

                            <?= articleReadingTime(
                                $article['content']
                                ?? ''
                            ) ?>

                            menit baca

                        </span>


                    </div>


                </div>


                <!-- =================================================
                     FEATURE IMAGE
                ================================================== -->

                <?php

                $featuredImage =
                    articleImageUrl(
                        $article[
                            'thumbnail_path'
                        ] ?? ''
                    );

                ?>


                <div class="article-featured-image">


                    <?php if (
                        $featuredImage !== ''
                    ): ?>

                        <img
                            src="<?= htmlspecialchars(
                                $featuredImage
                            ) ?>"
                            alt="<?= htmlspecialchars(
                                $article['title']
                                ?? 'Artikel'
                            ) ?>"
                        >

                    <?php else: ?>

                        <div
                            class="article-featured-placeholder"
                        >
                            INFORA SN
                        </div>

                    <?php endif; ?>


                </div>


                <!-- =================================================
                     CONTENT
                ================================================== -->

                <div class="article-content">

                    <?php

                    $articleContent =
                        trim(
                            (string) (
                                $article['content']
                                ?? ''
                            )
                        );

                    echo $articleContent !== ''
                        ? $articleContent
                        : '<p>Konten artikel belum tersedia.</p>';

                    ?>

                </div>


                <!-- =================================================
                     TAG
                ================================================== -->

                <?php if (
                    !empty($tags)
                ): ?>

                    <div class="article-tags">


                        <?php foreach (
                            $tags
                            as $tag
                        ): ?>

                            <span class="article-tag">

                                #<?= htmlspecialchars(
                                    $tag['name']
                                    ?? ''
                                ) ?>

                            </span>

                        <?php endforeach; ?>


                    </div>

                <?php endif; ?>


                <!-- =================================================
                     SHARE
                ================================================== -->

                <div class="article-share">


                    <h3 class="article-share-title">

                        Bagikan Artikel

                    </h3>


                    <div class="article-share-buttons">


                        <!-- WHATSAPP -->

                        <a
                            href="https://api.whatsapp.com/send?text=<?= $encodedShareTitle ?>%20<?= $encodedShareUrl ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="article-share-button share-whatsapp"
                        >

                            <span class="article-share-icon">
                                WA
                            </span>

                            WhatsApp

                        </a>


                        <!-- FACEBOOK -->

                        <a
                            href="https://www.facebook.com/sharer/sharer.php?u=<?= $encodedShareUrl ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="article-share-button share-facebook"
                        >

                            <span class="article-share-icon">
                                f
                            </span>

                            Facebook

                        </a>


                        <!-- X -->

                        <a
                            href="https://twitter.com/intent/tweet?text=<?= $encodedShareTitle ?>&url=<?= $encodedShareUrl ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="article-share-button share-x"
                        >

                            <span class="article-share-icon">
                                X
                            </span>

                            X

                        </a>


                        <!-- TELEGRAM -->

                        <a
                            href="https://t.me/share/url?url=<?= $encodedShareUrl ?>&text=<?= $encodedShareTitle ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="article-share-button share-telegram"
                        >

                            <span class="article-share-icon">
                                TG
                            </span>

                            Telegram

                        </a>


                        <!-- COPY LINK -->

                        <button
                            type="button"
                            class="article-share-button share-copy"
                            id="copyArticleLink"
                            data-url="<?= htmlspecialchars(
                                $shareUrl,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                            <span class="article-share-icon">
                                ↗
                            </span>

                            Salin Link

                        </button>


                    </div>


                    <div
                        id="copyArticleMessage"
                        class="article-share-copy-message"
                    >
                        ✓ Link artikel berhasil disalin.
                    </div>


                </div>


                <!-- =================================================
                     STATISTIK
                ================================================== -->

                <div class="article-stats-bar">


                    <span>

                        <?= (int) (
                            $article['view_count']
                            ?? 0
                        ) ?>

                        kali dibaca

                    </span>


                    <span>

                        Terakhir diperbarui:

                        <?= articleShortDate(
                            $article['updated_at']
                            ??
                            (
                                $article[
                                    'published_at'
                                ]
                                ?? null
                            )
                        ) ?>

                    </span>


                </div>


                <!-- =================================================
                     AUTHOR
                ================================================== -->

                <div class="article-author-box">


                    <div class="article-author-avatar">

                        <?= articleInitial(
                            $article['author_name']
                            ?? ''
                        ) ?>

                    </div>


                    <div>

                        <p class="article-author-name">

                            <?= htmlspecialchars(
                                $article['author_name']
                                ?? 'Redaksi INFORA'
                            ) ?>

                        </p>


                        <p class="article-author-role">

                            Kontributor / Penulis INFORA SN

                        </p>

                    </div>


                </div>


            </article>


            <!-- =====================================================
                 SIDEBAR KANAN
            ====================================================== -->

            <aside>


                <!-- =================================================
                     TERPOPULER
                ================================================== -->

                <div class="article-sidebar-panel">


                    <div class="article-sidebar-header">

                        <h2 class="article-sidebar-title">

                            Terpopuler

                        </h2>

                    </div>


                    <div class="sidebar-popular-list">


                        <?php if (
                            !empty($popular)
                        ): ?>


                            <?php

                            $popularNumber = 1;

                            ?>


                            <?php foreach (
                                array_slice(
                                    $popular,
                                    0,
                                    5
                                )
                                as $item
                            ): ?>


                                <article
                                    class="sidebar-popular-item"
                                >


                                    <div
                                        class="sidebar-popular-number"
                                    >

                                        <?= str_pad(
                                            $popularNumber,
                                            2,
                                            '0',
                                            STR_PAD_LEFT
                                        ) ?>

                                    </div>


                                    <div>


                                        <?php if (
                                            !empty(
                                                $item[
                                                    'category_name'
                                                ]
                                            )
                                        ): ?>

                                            <div
                                                class="sidebar-popular-category"
                                            >

                                                <?= htmlspecialchars(
                                                    $item[
                                                        'category_name'
                                                    ]
                                                ) ?>

                                            </div>

                                        <?php endif; ?>


                                        <h3
                                            class="sidebar-popular-title"
                                        >

                                            <a
                                                href="<?= articleUrl(
                                                    $baseUrl,
                                                    $item[
                                                        'slug'
                                                    ] ?? ''
                                                ) ?>"
                                            >

                                                <?= htmlspecialchars(
                                                    $item[
                                                        'title'
                                                    ] ?? ''
                                                ) ?>

                                            </a>

                                        </h3>


                                        <div
                                            class="sidebar-popular-meta"
                                        >

                                            <?= articleShortDate(
                                                $item[
                                                    'published_at'
                                                ] ?? null
                                            ) ?>

                                            &nbsp;•&nbsp;

                                            <?= (int) (
                                                $item[
                                                    'view_count'
                                                ] ?? 0
                                            ) ?>

                                            views

                                        </div>


                                    </div>


                                </article>


                                <?php

                                $popularNumber++;

                                ?>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <div class="sidebar-empty">

                                Belum ada artikel terpopuler.

                            </div>


                        <?php endif; ?>


                    </div>


                </div>


                <!-- =================================================
                     IKLAN
                     DIAMBIL LEWAT MODEL AdminAd
                ================================================== -->

                <div class="article-sidebar-ad">


                    <?php if (
                        $adTop !== null
                        &&
                        !empty(
                            $adTop['image_path']
                        )
                    ): ?>


                        <?php

                        $adImage =
                            articleImageUrl(
                                $adTop[
                                    'image_path'
                                ]
                            );


                        $adTarget =
                            trim(
                                (string) (
                                    $adTop[
                                        'target_url'
                                    ]
                                    ?? ''
                                )
                            );


                        if (
                            $adTarget === ''
                        ) {

                            $adTarget = '#';

                        }

                        ?>


                        <a
                            href="<?= htmlspecialchars(
                                $adTarget,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            target="_blank"
                            rel="noopener sponsored"
                            class="article-sidebar-ad-link"
                        >

                            <img
                                src="<?= htmlspecialchars(
                                    $adImage,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="<?= htmlspecialchars(
                                    $adTop[
                                        'title'
                                    ]
                                    ?? 'Iklan'
                                ) ?>"
                                class="article-sidebar-ad-image"
                            >

                        </a>


                    <?php else: ?>


                        <div
                            class="article-sidebar-ad-placeholder"
                        >

                            Ruang Iklan

                        </div>


                    <?php endif; ?>


                </div>


            </aside>


        </div>


        <!-- =========================================================
             ARTIKEL TERKAIT
        ========================================================== -->

        <?php if (
            !empty($related)
        ): ?>


            <section class="related-section">


                <div class="related-heading">

                    <h2>
                        Artikel Terkait
                    </h2>

                </div>


                <div class="related-grid">


                    <?php foreach (
                        $related
                        as $rel
                    ): ?>


                        <?php

                        $relatedImage =
                            articleImageUrl(
                                $rel[
                                    'thumbnail_path'
                                ] ?? ''
                            );

                        ?>


                        <article
                            class="related-card"
                        >


                            <div
                                class="related-card-image"
                            >


                                <?php if (
                                    $relatedImage !== ''
                                ): ?>

                                    <img
                                        src="<?= htmlspecialchars(
                                            $relatedImage
                                        ) ?>"
                                        alt="<?= htmlspecialchars(
                                            $rel[
                                                'title'
                                            ] ?? ''
                                        ) ?>"
                                        loading="lazy"
                                    >

                                <?php else: ?>

                                    <div
                                        class="related-card-placeholder"
                                    >
                                        INFORA SN
                                    </div>

                                <?php endif; ?>


                            </div>


                            <div
                                class="related-card-body"
                            >


                                <?php if (
                                    !empty(
                                        $rel[
                                            'category_name'
                                        ]
                                    )
                                ): ?>

                                    <div
                                        class="related-card-category"
                                    >

                                        <?= htmlspecialchars(
                                            $rel[
                                                'category_name'
                                            ]
                                        ) ?>

                                    </div>

                                <?php endif; ?>


                                <h3
                                    class="related-card-title"
                                >

                                    <a
                                        href="<?= articleUrl(
                                            $baseUrl,
                                            $rel[
                                                'slug'
                                            ] ?? ''
                                        ) ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $rel[
                                                'title'
                                            ] ?? ''
                                        ) ?>

                                    </a>

                                </h3>


                            </div>


                        </article>


                    <?php endforeach; ?>


                </div>


            </section>


        <?php endif; ?>


    </div>

</main>


<script>

/* =========================================================
   COPY LINK
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {


        const copyButton =
            document.getElementById(
                'copyArticleLink'
            );


        const copyMessage =
            document.getElementById(
                'copyArticleMessage'
            );


        if (!copyButton) {
            return;
        }


        copyButton.addEventListener(
            'click',
            async function () {


                const url =
                    copyButton.getAttribute(
                        'data-url'
                    );


                if (!url) {
                    return;
                }


                try {


                    if (
                        navigator.clipboard
                        &&
                        window.isSecureContext
                    ) {

                        await navigator
                            .clipboard
                            .writeText(url);

                    } else {


                        const textarea =
                            document.createElement(
                                'textarea'
                            );


                        textarea.value =
                            url;


                        textarea.style.position =
                            'fixed';

                        textarea.style.left =
                            '-9999px';


                        document.body.appendChild(
                            textarea
                        );


                        textarea.focus();

                        textarea.select();


                        document.execCommand(
                            'copy'
                        );


                        textarea.remove();

                    }


                    if (copyMessage) {

                        copyMessage.classList.add(
                            'show'
                        );

                    }


                    const oldHtml =
                        copyButton.innerHTML;


                    copyButton.innerHTML =
                        '<span class="article-share-icon">✓</span> Tersalin';


                    setTimeout(
                        function () {

                            if (copyMessage) {

                                copyMessage.classList.remove(
                                    'show'
                                );

                            }


                            copyButton.innerHTML =
                                oldHtml;

                        },
                        2000
                    );


                } catch (error) {


                    console.error(
                        'Gagal menyalin link:',
                        error
                    );


                    alert(
                        'Link artikel gagal disalin.'
                    );

                }

            }
        );

    }
);

</script>


<?php

/* =========================================================
   FOOTER
========================================================= */

require __DIR__ . '/layout/footer.php';

?>