<?php

/* =========================================================
   INFORA SN - PUBLIC HOME
   =========================================================
   Struktur:
   1. Header
   2. Navbar
   3. Breaking / Info Terkini
   4. Kilas Utama + Berita Pendamping
   5. Trending
   6. Pilihan Redaksi
   7. Artikel Terkini + Terpopuler
   8. Opini & Analisis
   9. Berita per Kategori
   10. Live Feed Publik
   11. Fokus INFORA
   12. Ruang Kontributor
   13. Integritas
   14. Footer
========================================================= */


/* =========================================================
   BASE URL
========================================================= */

function homeDetectBaseUrl()
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

$baseUrl = homeDetectBaseUrl();


/* =========================================================
   DATA DARI CONTROLLER
========================================================= */

$hero = $hero ?? null;
$latest = $latest ?? [];
$editorsChoice = $editorsChoice ?? [];
$popularSevenDays = $popularSevenDays ?? [];
$popular = $popular ?? [];
$categories = $categories ?? [];

$adTop = $adTop ?? null;
$adBottom = $adBottom ?? null;


/* =========================================================
   HELPER
========================================================= */

function homeEscape($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


function homeImageUrl($path)
{
    $path = trim((string) $path);

    if ($path === '') {
        return '';
    }

    // Kalau sudah URL lengkap
    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }

    // Normalisasi slash
    $path = str_replace('\\', '/', $path);
    $path = ltrim($path, '/');

    /*
     * Ambil base folder project dari URL yang sedang dijalankan.
     * Contoh:
     * /infora-sn/index.php
     * menjadi:
     * /infora-sn
     */
    $scriptName = str_replace(
        '\\',
        '/',
        $_SERVER['SCRIPT_NAME'] ?? ''
    );

    $baseUrl = dirname($scriptName);

    if ($baseUrl === '/' || $baseUrl === '\\' || $baseUrl === '.') {
        $baseUrl = '';
    }

    $baseUrl = rtrim($baseUrl, '/');

    /*
     * DATABASE:
     * uploads/articles/nama.jpg
     *
     * FILE:
     * public/uploads/articles/nama.jpg
     */
    if (strpos($path, 'uploads/') === 0) {
        return $baseUrl . '/public/' . $path;
    }

    /*
     * DATABASE:
     * public/uploads/articles/nama.jpg
     */
    if (strpos($path, 'public/uploads/') === 0) {
        return $baseUrl . '/' . $path;
    }

    /*
     * Kalau database hanya menyimpan nama file
     */
    return $baseUrl . '/public/uploads/articles/' . $path;
}


function homeArticleUrl($baseUrl, $slug)
{
    return rtrim($baseUrl, '/')
        . '/article/'
        . rawurlencode((string) $slug);
}


function homeCategoryUrl($baseUrl, $slug)
{
    return rtrim($baseUrl, '/')
        . '/category/'
        . rawurlencode((string) $slug);
}


function homeDateIndonesia($date)
{
    if (empty($date)) {
        return '-';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return '-';
    }

    $months = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    return date('d', $timestamp)
        . ' '
        . $months[(int) date('n', $timestamp)]
        . ' '
        . date('Y', $timestamp);
}


function homeShortDate($date)
{
    if (empty($date)) {
        return '-';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return '-';
    }

    return date('d/m/Y', $timestamp);
}


/*
 * Format waktu relatif sederhana.
 */
function homeTimeAgo($date)
{
    if (empty($date)) {
        return '';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return '';
    }

    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'Baru saja';
    }

    if ($diff < 3600) {
        return floor($diff / 60) . ' menit lalu';
    }

    if ($diff < 86400) {
        return floor($diff / 3600) . ' jam lalu';
    }

    if ($diff < 604800) {
        return floor($diff / 86400) . ' hari lalu';
    }

    return homeShortDate($date);
}


/*
 * Mengambil kategori artikel.
 */
function homeCategoryName($article)
{
    return trim(
        (string) (
            $article['category_name']
            ?? $article['nama_kategori']
            ?? ''
        )
    );
}


/*
 * Mengambil slug kategori jika tersedia.
 */
function homeCategorySlug($article)
{
    return trim(
        (string) (
            $article['category_slug']
            ?? $article['category_slug_name']
            ?? ''
        )
    );
}


/*
 * Cek apakah artikel termasuk Opini / Analisis.
 */
function homeIsOpinion($article)
{
    $category = strtolower(
        homeCategoryName($article)
    );

    return (
        strpos($category, 'opini') !== false ||
        strpos($category, 'analisis') !== false
    );
}


/*
 * Hilangkan artikel duplikat berdasarkan ID / slug.
 */
function homeUniqueArticles($articles)
{
    $result = [];
    $used = [];

    foreach ($articles as $article) {

        $key = '';

        if (!empty($article['id'])) {
            $key = 'id_' . $article['id'];
        } elseif (!empty($article['article_id'])) {
            $key = 'article_' . $article['article_id'];
        } elseif (!empty($article['slug'])) {
            $key = 'slug_' . $article['slug'];
        } else {
            $key = 'title_' . ($article['title'] ?? '');
        }

        if (isset($used[$key])) {
            continue;
        }

        $used[$key] = true;
        $result[] = $article;
    }

    return $result;
}


/* =========================================================
   PAGE META
========================================================= */

$pageTitle =
    'INFORA SN - Jurnalisme Publik & Analisis';

$pageDescription =
    'INFORA SN - Jurnalisme Publik & Analisis. '
    . 'Berita terkini, informasi publik, opini, '
    . 'dan analisis mendalam.';


/* =========================================================
   SHARED HEADER
========================================================= */

require __DIR__ . '/layout/header.php';

if (class_exists('Auth')) {
    $baseUrl = Auth::baseUrl();
}


/* =========================================================
   SHARED NAVBAR
========================================================= */

require __DIR__ . '/layout/navbar.php';


/* =========================================================
   DATA TURUNAN
========================================================= */

/*
 * Berita pendamping hero.
 */
$supportingStories = [];

foreach ($latest as $article) {

    if (
        $hero &&
        !empty($hero['slug']) &&
        !empty($article['slug']) &&
        $hero['slug'] === $article['slug']
    ) {
        continue;
    }

    $supportingStories[] = $article;
}

$supportingStories = array_slice(
    $supportingStories,
    0,
    3
);


/*
 * Trending mengambil artikel populer.
 */
$trendingArticles = !empty($popularSevenDays)
    ? $popularSevenDays
    : $popular;

$trendingArticles = array_slice(
    homeUniqueArticles($trendingArticles),
    0,
    6
);


/*
 * Data Opini & Analisis.
 */
$opinionPool = array_merge(
    $latest,
    $editorsChoice,
    $popularSevenDays,
    $popular
);

$opinionPool = homeUniqueArticles(
    $opinionPool
);

$opinionArticles = [];

foreach ($opinionPool as $article) {

    if (homeIsOpinion($article)) {
        $opinionArticles[] = $article;
    }
}

$opinionArticles = array_slice(
    $opinionArticles,
    0,
    4
);


/*
 * Berita per kategori.
 *
 * Tidak membutuhkan query tambahan.
 * Data diambil dari berita terbaru yang sudah
 * diberikan controller.
 */
$categoryArticles = [];

foreach ($latest as $article) {

    $categoryName = homeCategoryName($article);

    if ($categoryName === '') {
        continue;
    }

    if (!isset($categoryArticles[$categoryName])) {
        $categoryArticles[$categoryName] = [];
    }

    if (count($categoryArticles[$categoryName]) < 3) {
        $categoryArticles[$categoryName][] = $article;
    }
}


/*
 * Jangan tampilkan terlalu banyak kategori.
 */
$categoryArticles = array_slice(
    $categoryArticles,
    0,
    4,
    true
);

?>


<style>

/* =========================================================
   ROOT
========================================================= */

.infora-home {
    width: 100%;
    background: #f4f5f7;
    color: #111827;
    padding-bottom: 70px;
}

.home-inner {
    width: min(1240px, calc(100% - 70px));
    margin: 0 auto;
}

.home-inner a {
    text-decoration: none;
}


/* =========================================================
   INFO TERKINI
========================================================= */

.home-info-bar {
    display: flex;
    align-items: center;

    min-height: 40px;

    margin-top: 15px;

    background: #ffffff;

    border-top: 1px solid #dfe3e8;
    border-bottom: 1px solid #dfe3e8;
}

.home-info-label {
    display: flex;
    align-items: center;
    gap: 8px;

    padding: 10px 15px;

    background: #174bc8;

    color: #ffffff;

    font-size: 8px;
    font-weight: 900;

    text-transform: uppercase;
    letter-spacing: .06em;

    white-space: nowrap;
}

.home-info-dot {
    width: 7px;
    height: 7px;

    border-radius: 50%;

    background: #d8ff00;
}

.home-info-content {
    display: flex;
    align-items: center;
    gap: 10px;

    min-width: 0;

    padding: 0 13px;
}

.home-info-live {
    color: #174bc8;

    font-size: 7px;
    font-weight: 900;

    text-transform: uppercase;
}

.home-info-text {
    overflow: hidden;

    color: #555e6c;

    font-size: 9px;

    white-space: nowrap;
    text-overflow: ellipsis;
}

.home-info-date {
    margin-left: auto;

    color: #9299a3;

    font-size: 7px;

    white-space: nowrap;
}


/* =========================================================
   COMMON SECTION
========================================================= */

.home-section {
    padding-top: 28px;
    padding-bottom: 5px;
}

.home-section-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 14px;
}

.home-section-title {
    position: relative;

    margin: 0;
    padding-left: 12px;

    color: #111827;

    font-size: 17px;
    font-weight: 900;
}

.home-section-title::before {
    content: '';

    position: absolute;

    left: 0;
    top: 2px;
    bottom: 2px;

    width: 4px;

    background: #174bc8;

    border-radius: 2px;
}

.home-section-subtitle {
    margin: 5px 0 0;

    color: #7a828d;

    font-size: 8px;
    line-height: 1.5;
}

.home-section-link {
    color: #174bc8;

    font-size: 8px;
    font-weight: 900;

    white-space: nowrap;
}

.home-section-link:hover {
    text-decoration: underline;
}


/* =========================================================
   HERO
========================================================= */

.home-hero-section {
    padding-top: 22px;
    padding-bottom: 4px;
}

.hero-kicker {
    display: flex;
    align-items: center;
    gap: 8px;

    margin-bottom: 11px;

    color: #111827;

    font-size: 9px;
    font-weight: 900;

    text-transform: uppercase;
    letter-spacing: .08em;
}

.hero-kicker span {
    display: inline-flex;
    align-items: center;

    padding: 5px 8px;

    background: #174bc8;

    color: #ffffff;

    font-size: 7px;
}

.hero-layout {
    display: grid;

    grid-template-columns:
        minmax(0, 1.55fr)
        minmax(300px, .75fr);

    gap: 14px;
}


/* =========================================================
   HERO MAIN
========================================================= */

.hero-main {
    position: relative;

    min-height: 455px;

    overflow: hidden;

    background: #111827;
}

.hero-main-image {
    position: absolute;

    inset: 0;

    background: #222b38;
}

.hero-main-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    transition: transform .5s ease;
}

.hero-main:hover .hero-main-image img {
    transform: scale(1.035);
}

.hero-main-image::after {
    content: '';

    position: absolute;

    inset: 0;

    background:
        linear-gradient(
            180deg,
            rgba(0,0,0,.02) 15%,
            rgba(0,0,0,.12) 35%,
            rgba(0,0,0,.88) 100%
        );
}

.hero-placeholder {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #88919f;

    font-size: 10px;
    font-weight: 900;
}

.hero-main-content {
    position: relative;
    z-index: 2;

    min-height: 455px;

    display: flex;
    flex-direction: column;
    justify-content: flex-end;

    padding: 34px;
}

.hero-category {
    margin-bottom: 9px;

    color: #d8ff00;

    font-size: 8px;
    font-weight: 900;

    text-transform: uppercase;
    letter-spacing: .1em;
}

.hero-title {
    max-width: 780px;

    margin: 0;

    color: #ffffff;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: clamp(28px, 3.1vw, 45px);

    line-height: 1.06;

    letter-spacing: -.025em;
}

.hero-excerpt {
    max-width: 650px;

    margin: 13px 0 0;

    color: #d0d5dd;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 10px;

    line-height: 1.55;
}

.hero-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 7px;

    margin-top: 14px;

    color: #b8bec8;

    font-size: 7px;
}

.hero-meta-author {
    color: #ffffff;
    font-weight: 900;
}

.hero-meta-category {
    color: #d8ff00;
}

.hero-read {
    display: inline-flex;
    align-items: center;

    width: fit-content;

    margin-top: 17px;

    padding: 9px 12px;

    background: #174bc8;

    color: #ffffff;

    font-size: 7px;
    font-weight: 900;

    text-transform: uppercase;
    letter-spacing: .04em;

    transition: .2s ease;
}

.hero-read:hover {
    background: #d8ff00;
    color: #111111;
}


/* =========================================================
   SUPPORTING STORIES
========================================================= */

.hero-supporting {
    display: flex;
    flex-direction: column;

    gap: 14px;
}

.support-card {
    display: grid;

    grid-template-columns: 125px minmax(0, 1fr);

    min-height: 142px;

    background: #ffffff;

    border: 1px solid #dfe3e8;

    overflow: hidden;

    transition:
        transform .2s ease,
        box-shadow .2s ease;
}

.support-card:hover {
    transform: translateY(-2px);

    box-shadow:
        0 9px 22px rgba(15,23,42,.08);
}

.support-image {
    min-height: 142px;

    overflow: hidden;

    background: #e2e5e9;
}

.support-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    transition: transform .35s ease;
}

.support-card:hover .support-image img {
    transform: scale(1.04);
}

.support-placeholder {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #8d95a0;

    font-size: 7px;
    font-weight: 900;
}

.support-content {
    display: flex;
    flex-direction: column;

    padding: 13px;
}

.support-category {
    margin-bottom: 6px;

    color: #174bc8;

    font-size: 6px;
    font-weight: 900;

    text-transform: uppercase;
    letter-spacing: .05em;
}

.support-title {
    margin: 0;

    color: #111111;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 14px;

    line-height: 1.25;
}

.support-title a {
    color: inherit;
}

.support-title a:hover {
    color: #174bc8;
}

.support-meta {
    margin-top: auto;
    padding-top: 9px;

    color: #858c96;

    font-size: 6px;
}

.support-author {
    color: #333333;
    font-weight: 800;
}


/* =========================================================
   TRENDING
========================================================= */

.trending-section {
    padding-top: 22px;
}

.trending-wrapper {
    display: grid;

    grid-template-columns: 150px minmax(0, 1fr);

    background: #111827;

    overflow: hidden;
}

.trending-label {
    display: flex;
    align-items: center;

    padding: 15px;

    background: #174bc8;

    color: #ffffff;

    font-size: 8px;
    font-weight: 900;

    text-transform: uppercase;
}

.trending-list {
    display: flex;
    align-items: center;

    gap: 8px;

    padding: 10px;

    overflow-x: auto;
}

.trending-item {
    flex: 0 0 auto;

    display: flex;
    align-items: center;

    gap: 8px;

    padding: 7px 9px;

    border: 1px solid rgba(255,255,255,.1);

    background: rgba(255,255,255,.045);

    color: #e1e5eb;
}

.trending-number {
    color: #d8ff00;

    font-size: 7px;
    font-weight: 900;
}

.trending-title {
    max-width: 190px;

    overflow: hidden;

    color: #ffffff;

    font-size: 7px;
    font-weight: 700;

    white-space: nowrap;
    text-overflow: ellipsis;
}

.trending-item:hover {
    border-color: #d8ff00;
    background: #d8ff00;
    color: #111111;
}

.trending-item:hover .trending-number,
.trending-item:hover .trending-title {
    color: #111111;
}


/* =========================================================
   EDITOR CHOICE
========================================================= */

.editor-grid {
    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 15px;
}

.editor-card {
    background: #ffffff;

    border: 1px solid #dfe3e8;

    overflow: hidden;

    transition:
        transform .2s ease,
        box-shadow .2s ease;
}

.editor-card:hover {
    transform: translateY(-3px);

    box-shadow:
        0 10px 25px rgba(15,23,42,.08);
}

.editor-image {
    position: relative;

    aspect-ratio: 16 / 9;

    overflow: hidden;

    background: #e1e4e8;
}

.editor-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    transition: transform .35s ease;
}

.editor-card:hover .editor-image img {
    transform: scale(1.04);
}

.editor-placeholder {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #9198a2;

    font-size: 8px;
    font-weight: 900;
}

.editor-body {
    padding: 14px;
}

.editor-category {
    margin-bottom: 6px;

    color: #174bc8;

    font-size: 7px;
    font-weight: 900;

    text-transform: uppercase;
}

.editor-title {
    margin: 0;

    color: #111111;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 17px;

    line-height: 1.25;
}

.editor-title a {
    color: inherit;
}

.editor-title a:hover {
    color: #174bc8;
}

.editor-excerpt {
    display: -webkit-box;

    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;

    overflow: hidden;

    margin: 7px 0 0;

    color: #68707d;

    font-size: 8px;

    line-height: 1.5;
}

.editor-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 6px;

    margin-top: 11px;

    color: #818894;

    font-size: 7px;
}

.editor-author {
    color: #222222;
    font-weight: 800;
}


/* =========================================================
   LATEST + SIDEBAR
========================================================= */

.latest-section {
    padding-top: 30px;
}

.latest-layout {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        285px;

    gap: 17px;

    align-items: start;
}


/* =========================================================
   LATEST PANEL
========================================================= */

.latest-panel {
    background: #ffffff;

    border: 1px solid #dfe3e8;
}

.latest-panel-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 15px;

    border-bottom: 1px solid #e0e4e8;
}

.latest-panel-heading h2 {
    margin: 0;

    color: #111827;

    font-size: 15px;
    font-weight: 900;
}

.latest-panel-heading p {
    margin: 4px 0 0;

    color: #7c8490;

    font-size: 7px;
}

.latest-list {
    padding: 0 15px;
}

.latest-item {
    display: grid;

    grid-template-columns:
        170px
        minmax(0, 1fr);

    gap: 14px;

    padding: 14px 0;

    border-bottom: 1px solid #e3e6e9;
}

.latest-item:last-child {
    border-bottom: none;
}

.latest-image {
    aspect-ratio: 16 / 10;

    overflow: hidden;

    background: #e0e3e7;
}

.latest-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    transition: transform .3s ease;
}

.latest-item:hover .latest-image img {
    transform: scale(1.035);
}

.latest-placeholder {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #8d95a0;

    font-size: 7px;
    font-weight: 900;
}

.latest-category {
    margin-bottom: 5px;

    color: #174bc8;

    font-size: 7px;
    font-weight: 900;

    text-transform: uppercase;
}

.latest-title {
    margin: 0;

    color: #111111;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 16px;

    line-height: 1.28;
}

.latest-title a {
    color: inherit;
}

.latest-title a:hover {
    color: #174bc8;
}

.latest-excerpt {
    display: -webkit-box;

    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;

    overflow: hidden;

    margin: 7px 0 0;

    color: #6c7480;

    font-size: 8px;

    line-height: 1.55;
}

.latest-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 6px;

    margin-top: 9px;

    color: #858b94;

    font-size: 7px;
}

.latest-author {
    color: #222222;
    font-weight: 800;
}


/* =========================================================
   SIDEBAR
========================================================= */

.home-sidebar {
    display: flex;
    flex-direction: column;

    gap: 15px;
}


/* =========================================================
   ADS
========================================================= */

.sidebar-ad-box {
    width: 100%;
    min-height: 250px;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    background: #ffffff;

    border: 1px dashed #cbd0d6;

    color: #9ca2aa;

    font-size: 8px;
    font-weight: 900;

    text-transform: uppercase;
    letter-spacing: .07em;
}

.sidebar-ad-link {
    display: block;

    width: 100%;
    height: 250px;
}

.sidebar-ad-image {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
}


/* =========================================================
   POPULAR
========================================================= */

.popular-panel {
    background: #ffffff;

    border: 1px solid #dfe3e8;
}

.popular-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 13px;

    border-bottom: 1px solid #e0e4e8;
}

.popular-heading h2 {
    position: relative;

    margin: 0;
    padding-left: 10px;

    color: #111111;

    font-size: 14px;
    font-weight: 900;
}

.popular-heading h2::before {
    content: '';

    position: absolute;

    left: 0;
    top: 1px;
    bottom: 1px;

    width: 4px;

    background: #174bc8;
}

.popular-period {
    padding: 4px 6px;

    background: #edf1ff;

    color: #174bc8;

    font-size: 6px;
    font-weight: 900;
}

.popular-list {
    padding: 0 13px;
}

.popular-item {
    display: grid;

    grid-template-columns:
        31px
        minmax(0, 1fr);

    gap: 8px;

    padding: 12px 0;

    border-bottom: 1px solid #e4e7ea;
}

.popular-item:last-child {
    border-bottom: none;
}

.popular-number {
    color: #d4d8de;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 22px;

    line-height: 1;
}

.popular-category {
    margin-bottom: 4px;

    color: #174bc8;

    font-size: 6px;
    font-weight: 900;

    text-transform: uppercase;
}

.popular-title {
    margin: 0;

    color: #222222;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 11px;

    line-height: 1.35;
}

.popular-title a {
    color: inherit;
}

.popular-title a:hover {
    color: #174bc8;
}

.popular-meta {
    margin-top: 5px;

    color: #8a9098;

    font-size: 6px;
}


/* =========================================================
   FOLLOW
========================================================= */

.sidebar-follow {
    background: #ffffff;

    border: 1px solid #dfe3e8;
}

.sidebar-follow-heading {
    padding: 13px;

    border-bottom: 1px solid #e0e4e8;
}

.sidebar-follow-heading h2 {
    margin: 0;

    color: #111111;

    font-size: 14px;
    font-weight: 900;
}

.sidebar-follow-list {
    padding: 5px 0;
}

.sidebar-follow-item {
    display: flex;
    align-items: center;

    gap: 10px;

    padding: 9px 13px;

    color: #252a31;

    font-size: 8px;
    font-weight: 800;

    transition: .2s ease;
}

.sidebar-follow-item:hover {
    background: #f6f7f9;

    color: #174bc8;
}

.follow-icon {
    width: 27px;
    height: 27px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex: 0 0 27px;

    border-radius: 50%;

    color: #ffffff;

    font-size: 8px;
    font-weight: 900;
}

.follow-wa {
    background: #25d366;
}

.follow-ig {
    background: #d6249f;
}

.follow-fb {
    background: #1877f2;
}

.follow-x {
    background: #111111;
}

.follow-yt {
    background: #ff0000;
}

.follow-tg {
    background: #26a5e4;
}


/* =========================================================
   OPINI & ANALISIS
========================================================= */

.opinion-section {
    padding-top: 30px;
}

.opinion-layout {
    display: grid;

    grid-template-columns:
        1.25fr
        .75fr;

    gap: 15px;
}

.opinion-feature {
    position: relative;

    min-height: 310px;

    overflow: hidden;

    background: #111827;
}

.opinion-feature-image {
    position: absolute;

    inset: 0;
}

.opinion-feature-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
}

.opinion-feature-image::after {
    content: '';

    position: absolute;

    inset: 0;

    background:
        linear-gradient(
            180deg,
            rgba(0,0,0,.03),
            rgba(0,0,0,.88)
        );
}

.opinion-feature-content {
    position: relative;
    z-index: 2;

    min-height: 310px;

    display: flex;
    flex-direction: column;
    justify-content: flex-end;

    padding: 25px;
}

.opinion-label {
    margin-bottom: 7px;

    color: #d8ff00;

    font-size: 7px;
    font-weight: 900;

    text-transform: uppercase;
    letter-spacing: .08em;
}

.opinion-feature-title {
    margin: 0;

    color: #ffffff;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 25px;

    line-height: 1.1;
}

.opinion-feature-title a {
    color: inherit;
}

.opinion-feature-meta {
    margin-top: 10px;

    color: #c2c7cf;

    font-size: 7px;
}

.opinion-side {
    display: flex;
    flex-direction: column;

    gap: 10px;
}

.opinion-small {
    display: grid;

    grid-template-columns: 95px minmax(0, 1fr);

    min-height: 95px;

    background: #ffffff;

    border: 1px solid #dfe3e8;
}

.opinion-small-image {
    overflow: hidden;

    background: #e2e5e9;
}

.opinion-small-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
}

.opinion-small-content {
    padding: 10px;
}

.opinion-small-category {
    color: #174bc8;

    font-size: 6px;
    font-weight: 900;

    text-transform: uppercase;
}

.opinion-small-title {
    margin: 5px 0 0;

    color: #222222;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 12px;

    line-height: 1.3;
}

.opinion-small-title a {
    color: inherit;
}

.opinion-small-title a:hover {
    color: #174bc8;
}


/* =========================================================
   CATEGORY NEWS
========================================================= */

.category-news-section {
    padding-top: 30px;
}

.category-news-grid {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 15px;
}

.category-block {
    background: #ffffff;

    border: 1px solid #dfe3e8;
}

.category-block-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 12px 14px;

    border-bottom: 1px solid #e0e4e8;
}

.category-block-heading h3 {
    margin: 0;

    color: #111111;

    font-size: 13px;
    font-weight: 900;
}

.category-block-heading a {
    color: #174bc8;

    font-size: 7px;
    font-weight: 900;
}

.category-block-list {
    padding: 0 14px;
}

.category-block-item {
    display: grid;

    grid-template-columns:
        95px
        minmax(0, 1fr);

    gap: 10px;

    padding: 11px 0;

    border-bottom: 1px solid #e5e7ea;
}

.category-block-item:last-child {
    border-bottom: none;
}

.category-block-image {
    aspect-ratio: 16 / 10;

    overflow: hidden;

    background: #e1e4e8;
}

.category-block-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
}

.category-block-title {
    margin: 0;

    color: #222222;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 12px;

    line-height: 1.3;
}

.category-block-title a {
    color: inherit;
}

.category-block-title a:hover {
    color: #174bc8;
}

.category-block-meta {
    margin-top: 5px;

    color: #858c95;

    font-size: 6px;
}


/* =========================================================
   LIVE FEED
========================================================= */

.live-section {
    padding-top: 30px;
}

.live-wrapper {
    background: #ffffff;

    border: 1px solid #dfe3e8;
}

.live-list {
    display: grid;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));
}

.live-item {
    min-height: 145px;

    padding: 15px;

    border-right: 1px solid #e0e4e8;
}

.live-item:last-child {
    border-right: none;
}

.live-time {
    margin-bottom: 8px;

    color: #174bc8;

    font-size: 7px;
    font-weight: 900;
}

.live-title {
    margin: 0;

    color: #111111;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 13px;

    line-height: 1.35;
}

.live-title a {
    color: inherit;
}

.live-title a:hover {
    color: #174bc8;
}

.live-category {
    margin-top: 10px;

    color: #888e96;

    font-size: 6px;
    font-weight: 900;

    text-transform: uppercase;
}


/* =========================================================
   FOKUS INFORA
========================================================= */

.focus-section {
    padding-top: 30px;
}

.focus-box {
    display: grid;

    grid-template-columns:
        1fr
        1fr;

    min-height: 280px;

    overflow: hidden;

    background: #101827;
}

.focus-content {
    display: flex;
    flex-direction: column;
    justify-content: center;

    padding: 35px;
}

.focus-label {
    margin-bottom: 9px;

    color: #d8ff00;

    font-size: 7px;
    font-weight: 900;

    text-transform: uppercase;
    letter-spacing: .1em;
}

.focus-title {
    margin: 0;

    color: #ffffff;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 34px;

    line-height: 1.04;
}

.focus-text {
    max-width: 500px;

    margin: 12px 0 0;

    color: #aeb5bf;

    font-size: 8px;

    line-height: 1.65;
}

.focus-image {
    overflow: hidden;

    background: #222b38;
}

.focus-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    transition: transform .4s ease;
}

.focus-box:hover .focus-image img {
    transform: scale(1.03);
}

.focus-placeholder {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #737d8c;

    font-size: 9px;
    font-weight: 900;
}


/* =========================================================
   KONTRIBUTOR
========================================================= */

.contributor-section {
    padding-top: 30px;
}

.contributor-box {
    display: grid;

    grid-template-columns:
        1.1fr
        .9fr;

    background: #ffffff;

    border: 1px solid #dfe3e8;
}

.contributor-content {
    padding: 30px;
}

.contributor-label {
    margin-bottom: 7px;

    color: #174bc8;

    font-size: 7px;
    font-weight: 900;

    text-transform: uppercase;
    letter-spacing: .08em;
}

.contributor-title {
    margin: 0;

    color: #111111;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 29px;

    line-height: 1.08;
}

.contributor-text {
    max-width: 520px;

    margin: 11px 0 0;

    color: #6a727c;

    font-size: 8px;

    line-height: 1.65;
}

.contributor-button {
    display: inline-flex;

    margin-top: 15px;

    padding: 9px 13px;

    background: #174bc8;

    color: #ffffff;

    font-size: 7px;
    font-weight: 900;

    text-transform: uppercase;

    transition: .2s ease;
}

.contributor-button:hover {
    background: #111111;
    color: #ffffff;
}

.contributor-process {
    display: grid;

    grid-template-columns:
        repeat(2, 1fr);
}

.contributor-step {
    display: flex;
    flex-direction: column;
    justify-content: center;

    padding: 17px;

    border-left: 1px solid #e0e4e8;
    border-bottom: 1px solid #e0e4e8;
}

.contributor-step:nth-child(3),
.contributor-step:nth-child(4) {
    border-bottom: none;
}

.contributor-number {
    color: #174bc8;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 23px;
}

.contributor-step-title {
    margin-top: 4px;

    color: #222222;

    font-size: 7px;
    font-weight: 900;

    text-transform: uppercase;
}


/* =========================================================
   INTEGRITAS
========================================================= */

.integrity-section {
    padding-top: 30px;
}

.integrity-box {
    display: flex;
    align-items: center;

    gap: 15px;

    padding: 18px;

    background: #edf1ff;

    border: 1px solid #d7def5;
}

.integrity-icon {
    width: 41px;
    height: 41px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex: 0 0 41px;

    background: #174bc8;

    color: #ffffff;

    font-size: 17px;
    font-weight: 900;
}

.integrity-title {
    margin: 0 0 4px;

    color: #111111;

    font-size: 11px;
    font-weight: 900;
}

.integrity-text {
    margin: 0;

    color: #666f7a;

    font-size: 7px;

    line-height: 1.55;
}


/* =========================================================
   EMPTY
========================================================= */

.home-empty {
    padding: 70px 20px;

    text-align: center;
}

.home-empty h2 {
    margin: 0 0 8px;

    color: #111111;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 27px;
}

.home-empty p {
    margin: 0;

    color: #777777;

    font-size: 9px;
}


/* =========================================================
   RESPONSIVE 1050
========================================================= */

@media (max-width: 1050px) {

    .home-inner {
        width: calc(100% - 40px);
    }

    .hero-layout {
        grid-template-columns: 1fr;
    }

    .hero-main {
        min-height: 420px;
    }

    .hero-main-content {
        min-height: 420px;
    }

    .hero-supporting {
        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }

    .support-card {
        display: block;
        min-height: 0;
    }

    .support-image {
        height: 145px;
        min-height: 0;
    }

    .support-content {
        min-height: 125px;
    }

    .latest-layout {
        grid-template-columns: 1fr;
    }

    .home-sidebar {
        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        align-items: start;
    }

    .opinion-layout {
        grid-template-columns: 1fr;
    }

    .opinion-side {
        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }
}


/* =========================================================
   RESPONSIVE 800
========================================================= */

@media (max-width: 800px) {

    .hero-supporting {
        grid-template-columns: 1fr;
    }

    .support-card {
        display: grid;

        grid-template-columns:
            120px
            minmax(0, 1fr);
    }

    .support-image {
        height: auto;
        min-height: 130px;
    }

    .editor-grid {
        grid-template-columns: 1fr;
    }

    .category-news-grid {
        grid-template-columns: 1fr;
    }

    .live-list {
        grid-template-columns:
            repeat(2, 1fr);
    }

    .live-item:nth-child(2) {
        border-right: none;
    }

    .live-item:nth-child(3),
    .live-item:nth-child(4) {
        border-top: 1px solid #e0e4e8;
    }

    .focus-box {
        grid-template-columns: 1fr;
    }

    .focus-image {
        min-height: 230px;
    }

    .contributor-box {
        grid-template-columns: 1fr;
    }

    .contributor-process {
        border-top: 1px solid #e0e4e8;
    }

    .contributor-step {
        border-left: none;
    }

    .contributor-step:nth-child(even) {
        border-left: 1px solid #e0e4e8;
    }
}


/* =========================================================
   RESPONSIVE 600
========================================================= */

@media (max-width: 600px) {

    .home-inner {
        width: calc(100% - 24px);
    }

    .home-info-bar {
        display: block;
    }

    .home-info-label {
        display: flex;
    }

    .home-info-content {
        padding: 9px 11px;
    }

    .home-info-date {
        display: none;
    }

    .hero-main {
        min-height: 430px;
    }

    .hero-main-content {
        min-height: 430px;

        padding: 24px;
    }

    .hero-title {
        font-size: 29px;
    }

    .hero-excerpt {
        font-size: 9px;
    }

    .trending-wrapper {
        display: block;
    }

    .trending-label {
        padding: 11px 13px;
    }

    .trending-list {
        padding: 9px;
    }

    .home-sidebar {
        grid-template-columns: 1fr;
    }

    .latest-item {
        grid-template-columns:
            105px
            minmax(0, 1fr);

        gap: 9px;
    }

    .latest-title {
        font-size: 13px;
    }

    .latest-excerpt {
        display: none;
    }

    .latest-meta {
        font-size: 6px;
    }

    .opinion-side {
        grid-template-columns: 1fr;
    }

    .opinion-feature-title {
        font-size: 22px;
    }

    .live-list {
        grid-template-columns: 1fr;
    }

    .live-item,
    .live-item:nth-child(2),
    .live-item:nth-child(3) {
        border-right: none;
        border-bottom: 1px solid #e0e4e8;
    }

    .live-item:last-child {
        border-bottom: none;
    }

    .focus-content {
        padding: 24px;
    }

    .focus-title {
        font-size: 27px;
    }

    .contributor-content {
        padding: 24px;
    }

    .contributor-title {
        font-size: 25px;
    }

    .integrity-box {
        align-items: flex-start;
    }
}


/* =========================================================
   RESPONSIVE 450
========================================================= */

@media (max-width: 450px) {

    .home-inner {
        width: calc(100% - 18px);
    }

    .hero-main {
        min-height: 400px;
    }

    .hero-main-content {
        min-height: 400px;

        padding: 20px;
    }

    .hero-title {
        font-size: 25px;
    }

    .support-card {
        grid-template-columns:
            100px
            minmax(0, 1fr);
    }

    .support-image {
        min-height: 115px;
    }

    .support-title {
        font-size: 12px;
    }

    .editor-body {
        padding: 12px;
    }

    .category-block-item {
        grid-template-columns:
            85px
            minmax(0, 1fr);
    }

    .focus-image {
        min-height: 180px;
    }

    .contributor-process {
        grid-template-columns: 1fr 1fr;
    }

}

</style>


<main class="infora-home">

<div class="home-inner">


    <!-- =====================================================
         INFO TERKINI
    ====================================================== -->

    <div class="home-info-bar">

        <div class="home-info-label">

            <span class="home-info-dot"></span>

            INFO TERKINI

        </div>


        <div class="home-info-content">

            <span class="home-info-live">
                TERBARU
            </span>

            <span class="home-info-text">

                <?php if ($hero): ?>

                    <?= homeEscape(
                        $hero['title']
                        ?? 'INFORA SN menghadirkan informasi terkini'
                    ) ?>

                <?php else: ?>

                    INFORA SN menghadirkan informasi publik,
                    berita terkini, dan perspektif dari berbagai
                    daerah Indonesia.

                <?php endif; ?>

            </span>

            <?php if ($hero): ?>

                <span class="home-info-date">

                    <?= homeTimeAgo(
                        $hero['published_at']
                        ?? null
                    ) ?>

                </span>

            <?php endif; ?>

        </div>

    </div>


    <!-- =====================================================
         KILAS UTAMA
    ====================================================== -->

    <?php if ($hero): ?>

        <section class="home-hero-section">

            <div class="hero-kicker">

                <span>
                    UTAMA
                </span>

                Kilas Utama

            </div>


            <div class="hero-layout">


                <!-- HERO UTAMA -->

                <article class="hero-main">

                    <div class="hero-main-image">

                        <?php
                        $heroImage = homeImageUrl(
                            $hero['thumbnail_path'] ?? ''
                        );
                        ?>

                        <?php if ($heroImage): ?>

                            <img
                                src="<?= homeEscape($heroImage) ?>"
                                alt="<?= homeEscape(
                                    $hero['title']
                                    ?? 'INFORA SN'
                                ) ?>"
                            >

                        <?php else: ?>

                            <div class="hero-placeholder">
                                INFORA SN
                            </div>

                        <?php endif; ?>

                    </div>


                    <div class="hero-main-content">

                        <?php if (
                            !empty($hero['category_name'])
                        ): ?>

                            <div class="hero-category">

                                <?= homeEscape(
                                    $hero['category_name']
                                ) ?>

                            </div>

                        <?php endif; ?>


                        <h1 class="hero-title">

                            <?= homeEscape(
                                $hero['title'] ?? ''
                            ) ?>

                        </h1>


                        <?php if (
                            !empty($hero['excerpt'])
                        ): ?>

                            <p class="hero-excerpt">

                                <?= homeEscape(
                                    $hero['excerpt']
                                ) ?>

                            </p>

                        <?php endif; ?>


                        <div class="hero-meta">

                            <span class="hero-meta-author">

                                <?= homeEscape(
                                    $hero['author_name']
                                    ?? 'Redaksi'
                                ) ?>

                            </span>

                            <span>•</span>

                            <span>

                                <?= homeDateIndonesia(
                                    $hero['published_at']
                                    ?? null
                                ) ?>

                            </span>

                            <?php if (
                                isset($hero['view_count'])
                            ): ?>

                                <span>•</span>

                                <span>

                                    <?= (int) (
                                        $hero['view_count']
                                        ?? 0
                                    ) ?>

                                    views

                                </span>

                            <?php endif; ?>

                        </div>


                        <a
                            href="<?= homeArticleUrl(
                                $baseUrl,
                                $hero['slug'] ?? ''
                            ) ?>"
                            class="hero-read"
                        >

                            Baca Selengkapnya
                            →
                        
                        </a>

                    </div>

                </article>


                <!-- BERITA PENDAMPING -->

                <div class="hero-supporting">

                    <?php if (
                        !empty($supportingStories)
                    ): ?>

                        <?php foreach (
                            $supportingStories
                            as $support
                        ): ?>

                            <?php
                            $supportImage = homeImageUrl(
                                $support['thumbnail_path']
                                ?? ''
                            );
                            ?>

                            <article class="support-card">

                                <div class="support-image">

                                    <?php if (
                                        $supportImage
                                    ): ?>

                                        <img
                                            src="<?= homeEscape(
                                                $supportImage
                                            ) ?>"
                                            alt="<?= homeEscape(
                                                $support['title']
                                                ?? 'Artikel'
                                            ) ?>"
                                            loading="lazy"
                                        >

                                    <?php else: ?>

                                        <div class="support-placeholder">
                                            INFORA SN
                                        </div>

                                    <?php endif; ?>

                                </div>


                                <div class="support-content">

                                    <?php if (
                                        homeCategoryName(
                                            $support
                                        ) !== ''
                                    ): ?>

                                        <div class="support-category">

                                            <?= homeEscape(
                                                homeCategoryName(
                                                    $support
                                                )
                                            ) ?>

                                        </div>

                                    <?php endif; ?>


                                    <h2 class="support-title">

                                        <a
                                            href="<?= homeArticleUrl(
                                                $baseUrl,
                                                $support['slug']
                                                ?? ''
                                            ) ?>"
                                        >

                                            <?= homeEscape(
                                                $support['title']
                                                ?? ''
                                            ) ?>

                                        </a>

                                    </h2>


                                    <div class="support-meta">

                                        <span class="support-author">

                                            <?= homeEscape(
                                                $support[
                                                    'author_name'
                                                ]
                                                ?? 'Redaksi'
                                            ) ?>

                                        </span>

                                        &nbsp;•&nbsp;

                                        <?= homeShortDate(
                                            $support[
                                                'published_at'
                                            ]
                                            ?? null
                                        ) ?>

                                    </div>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

        </section>

    <?php else: ?>

        <section class="home-empty">

            <h2>
                Belum Ada Artikel
            </h2>

            <p>
                Belum ada artikel yang dipublikasikan.
            </p>

        </section>

    <?php endif; ?>


    <!-- =====================================================
         TRENDING
    ====================================================== -->

    <section class="trending-section">

        <div class="trending-wrapper">

            <div class="trending-label">

                🔥
                &nbsp;
                Yang Sedang Dibahas

            </div>


            <div class="trending-list">

                <?php if (
                    !empty($trendingArticles)
                ): ?>

                    <?php
                    $trendNumber = 1;
                    ?>

                    <?php foreach (
                        $trendingArticles
                        as $trend
                    ): ?>

                        <a
                            href="<?= homeArticleUrl(
                                $baseUrl,
                                $trend['slug'] ?? ''
                            ) ?>"
                            class="trending-item"
                        >

                            <span class="trending-number">

                                #<?= $trendNumber ?>

                            </span>

                            <span class="trending-title">

                                <?= homeEscape(
                                    $trend['title']
                                    ?? ''
                                ) ?>

                            </span>

                        </a>

                        <?php
                        $trendNumber++;
                        ?>

                    <?php endforeach; ?>

                <?php else: ?>

                    <span class="trending-item">

                        <span class="trending-number">
                            #1
                        </span>

                        <span class="trending-title">
                            Informasi Publik
                        </span>

                    </span>

                    <span class="trending-item">

                        <span class="trending-number">
                            #2
                        </span>

                        <span class="trending-title">
                            Berita Terkini
                        </span>

                    </span>

                    <span class="trending-item">

                        <span class="trending-number">
                            #3
                        </span>

                        <span class="trending-title">
                            Indonesia
                        </span>

                    </span>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- =====================================================
         PILIHAN REDAKSI
    ====================================================== -->

    <?php if (
        !empty($editorsChoice)
    ): ?>

        <section class="home-section">

            <div class="home-section-header">

                <div>

                    <h2 class="home-section-title">
                        Pilihan Redaksi
                    </h2>

                    <p class="home-section-subtitle">
                        Artikel yang dipilih dan dikurasi
                        oleh redaksi INFORA SN.
                    </p>

                </div>


                <a
                    href="<?= rtrim(
                        $baseUrl,
                        '/'
                    ) ?>/category/nasional"
                    class="home-section-link"
                >

                    Lihat Semua →

                </a>

            </div>


            <div class="editor-grid">

                <?php foreach (
                    array_slice(
                        $editorsChoice,
                        0,
                        3
                    )
                    as $editor
                ): ?>

                    <?php
                    $editorImage = homeImageUrl(
                        $editor['thumbnail_path']
                        ?? ''
                    );
                    ?>

                    <article class="editor-card">

                        <div class="editor-image">

                            <?php if (
                                $editorImage
                            ): ?>

                                <img
                                    src="<?= homeEscape(
                                        $editorImage
                                    ) ?>"
                                    alt="<?= homeEscape(
                                        $editor['title']
                                        ?? 'Artikel'
                                    ) ?>"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <div class="editor-placeholder">
                                    INFORA SN
                                </div>

                            <?php endif; ?>

                        </div>


                        <div class="editor-body">

                            <?php if (
                                !empty(
                                    $editor[
                                        'category_name'
                                    ]
                                )
                            ): ?>

                                <div class="editor-category">

                                    <?= homeEscape(
                                        $editor[
                                            'category_name'
                                        ]
                                    ) ?>

                                </div>

                            <?php endif; ?>


                            <h3 class="editor-title">

                                <a
                                    href="<?= homeArticleUrl(
                                        $baseUrl,
                                        $editor['slug']
                                        ?? ''
                                    ) ?>"
                                >

                                    <?= homeEscape(
                                        $editor['title']
                                        ?? ''
                                    ) ?>

                                </a>

                            </h3>


                            <?php if (
                                !empty(
                                    $editor['excerpt']
                                )
                            ): ?>

                                <p class="editor-excerpt">

                                    <?= homeEscape(
                                        $editor['excerpt']
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <div class="editor-meta">

                                <span class="editor-author">

                                    <?= homeEscape(
                                        $editor[
                                            'author_name'
                                        ]
                                        ?? 'Redaksi'
                                    ) ?>

                                </span>

                                <span>•</span>

                                <span>

                                    <?= homeShortDate(
                                        $editor[
                                            'published_at'
                                        ]
                                        ?? null
                                    ) ?>

                                </span>

                                <?php if (
                                    isset(
                                        $editor[
                                            'view_count'
                                        ]
                                    )
                                ): ?>

                                    <span>•</span>

                                    <span>

                                        <?= (int) (
                                            $editor[
                                                'view_count'
                                            ]
                                            ?? 0
                                        ) ?>

                                        views

                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        </section>

    <?php endif; ?>


    <!-- =====================================================
         ARTIKEL TERKINI + SIDEBAR
    ====================================================== -->

    <section class="latest-section">

        <div class="latest-layout">


            <!-- =================================================
                 ARTIKEL TERKINI
            ================================================== -->

            <section class="latest-panel">

                <div class="latest-panel-heading">

                    <div>

                        <h2>
                            Berita Terkini
                        </h2>

                        <p>
                            Perkembangan terbaru yang
                            dihimpun redaksi INFORA SN.
                        </p>

                    </div>

                </div>


                <div class="latest-list">

                    <?php if (
                        !empty($latest)
                    ): ?>

                        <?php foreach (
                            array_slice(
                                $latest,
                                0,
                                5
                            )
                            as $item
                        ): ?>

                            <?php
                            $latestImage = homeImageUrl(
                                $item[
                                    'thumbnail_path'
                                ]
                                ?? ''
                            );
                            ?>

                            <article class="latest-item">


                                <div class="latest-image">

                                    <?php if (
                                        $latestImage
                                    ): ?>

                                        <img
                                            src="<?= homeEscape(
                                                $latestImage
                                            ) ?>"
                                            alt="<?= homeEscape(
                                                $item['title']
                                                ?? 'Artikel'
                                            ) ?>"
                                            loading="lazy"
                                        >

                                    <?php else: ?>

                                        <div class="latest-placeholder">
                                            INFORA SN
                                        </div>

                                    <?php endif; ?>

                                </div>


                                <div>

                                    <?php if (
                                        homeCategoryName(
                                            $item
                                        ) !== ''
                                    ): ?>

                                        <div class="latest-category">

                                            <?= homeEscape(
                                                homeCategoryName(
                                                    $item
                                                )
                                            ) ?>

                                        </div>

                                    <?php endif; ?>


                                    <h3 class="latest-title">

                                        <a
                                            href="<?= homeArticleUrl(
                                                $baseUrl,
                                                $item['slug']
                                                ?? ''
                                            ) ?>"
                                        >

                                            <?= homeEscape(
                                                $item['title']
                                                ?? ''
                                            ) ?>

                                        </a>

                                    </h3>


                                    <?php if (
                                        !empty(
                                            $item['excerpt']
                                        )
                                    ): ?>

                                        <p class="latest-excerpt">

                                            <?= homeEscape(
                                                $item[
                                                    'excerpt'
                                                ]
                                            ) ?>

                                        </p>

                                    <?php endif; ?>


                                    <div class="latest-meta">

                                        <span class="latest-author">

                                            <?= homeEscape(
                                                $item[
                                                    'author_name'
                                                ]
                                                ?? 'Redaksi'
                                            ) ?>

                                        </span>

                                        <span>•</span>

                                        <span>

                                            <?= homeShortDate(
                                                $item[
                                                    'published_at'
                                                ]
                                                ?? null
                                            ) ?>

                                        </span>

                                        <?php if (
                                            isset(
                                                $item[
                                                    'view_count'
                                                ]
                                            )
                                        ): ?>

                                            <span>•</span>

                                            <span>

                                                <?= (int) (
                                                    $item[
                                                        'view_count'
                                                    ]
                                                    ?? 0
                                                ) ?>

                                                views

                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="home-empty">

                            <h2>
                                Belum Ada Artikel
                            </h2>

                            <p>
                                Belum ada artikel terbaru.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </section>


            <!-- =================================================
                 SIDEBAR
            ================================================== -->

            <aside class="home-sidebar">


                <!-- IKLAN ATAS -->

                <div class="sidebar-ad-box">

                    <?php if (
                        $adTop &&
                        !empty(
                            $adTop['image_path']
                        )
                    ): ?>

                        <a
                            href="<?= homeEscape(
                                $adTop['target_url']
                                ?? '#'
                            ) ?>"
                            target="_blank"
                            rel="noopener sponsored"
                            class="sidebar-ad-link"
                        >

                            <img
                                src="<?= homeEscape(
                                    homeImageUrl(
                                        $adTop[
                                            'image_path'
                                        ]
                                    )
                                ) ?>"
                                alt="<?= homeEscape(
                                    $adTop['title']
                                    ?? 'Iklan'
                                ) ?>"
                                class="sidebar-ad-image"
                            >

                        </a>

                    <?php else: ?>

                        Ruang Iklan

                    <?php endif; ?>

                </div>


                <!-- TERPOPULER -->

                <div class="popular-panel">

                    <div class="popular-heading">

                        <h2>
                            Terpopuler
                        </h2>

                        <span class="popular-period">
                            7 HARI
                        </span>

                    </div>


                    <div class="popular-list">

                        <?php

                        $popularItems =
                            !empty(
                                $popularSevenDays
                            )
                                ? $popularSevenDays
                                : $popular;

                        $popularItems =
                            array_slice(
                                $popularItems,
                                0,
                                5
                            );

                        ?>


                        <?php if (
                            !empty($popularItems)
                        ): ?>

                            <?php
                            $number = 1;
                            ?>

                            <?php foreach (
                                $popularItems
                                as $item
                            ): ?>

                                <article class="popular-item">

                                    <div class="popular-number">

                                        <?= str_pad(
                                            $number,
                                            2,
                                            '0',
                                            STR_PAD_LEFT
                                        ) ?>

                                    </div>


                                    <div>

                                        <?php if (
                                            homeCategoryName(
                                                $item
                                            ) !== ''
                                        ): ?>

                                            <div class="popular-category">

                                                <?= homeEscape(
                                                    homeCategoryName(
                                                        $item
                                                    )
                                                ) ?>

                                            </div>

                                        <?php endif; ?>


                                        <h3 class="popular-title">

                                            <a
                                                href="<?= homeArticleUrl(
                                                    $baseUrl,
                                                    $item['slug']
                                                    ?? ''
                                                ) ?>"
                                            >

                                                <?= homeEscape(
                                                    $item[
                                                        'title'
                                                    ]
                                                    ?? ''
                                                ) ?>

                                            </a>

                                        </h3>


                                        <div class="popular-meta">

                                            <?= homeShortDate(
                                                $item[
                                                    'published_at'
                                                ]
                                                ?? null
                                            ) ?>

                                            &nbsp;•&nbsp;

                                            <?= (int) (
                                                $item[
                                                    'view_count'
                                                ]
                                                ?? 0
                                            ) ?>

                                            views

                                        </div>

                                    </div>

                                </article>


                                <?php
                                $number++;
                                ?>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <div
                                style="
                                    padding:20px 0;
                                    color:#888;
                                    font-size:8px;
                                "
                            >
                                Belum ada artikel
                                terpopuler.
                            </div>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- IKUTI KAMI -->

                <div class="sidebar-follow">

                    <div class="sidebar-follow-heading">

                        <h2>
                            Ikuti Kami
                        </h2>

                    </div>


                    <div class="sidebar-follow-list">

                        <a
                            href="https://www.instagram.com/pendampingan_akademik"
                            class="sidebar-follow-item"
                        >

                            <span class="follow-icon follow-ig">
                                IG
                            </span>

                            Instagram

                        </a>

                        <a
                            href="https://www.tiktok.com/@ruangtugas_juniormz"
                            class="sidebar-follow-item"
                        >

                            <span class="follow-icon follow-x">
                                TK
                            </span>

                            Tiktok

                        </a>

                    </div>

                </div>


                <!-- IKLAN BAWAH -->

                <div class="sidebar-ad-box">

                    <?php if (
                        $adBottom &&
                        !empty(
                            $adBottom['image_path']
                        )
                    ): ?>

                        <a
                            href="<?= homeEscape(
                                $adBottom[
                                    'target_url'
                                ]
                                ?? '#'
                            ) ?>"
                            target="_blank"
                            rel="noopener sponsored"
                            class="sidebar-ad-link"
                        >

                            <img
                                src="<?= homeEscape(
                                    homeImageUrl(
                                        $adBottom[
                                            'image_path'
                                        ]
                                    )
                                ) ?>"
                                alt="<?= homeEscape(
                                    $adBottom[
                                        'title'
                                    ]
                                    ?? 'Iklan'
                                ) ?>"
                                class="sidebar-ad-image"
                            >

                        </a>

                    <?php else: ?>

                        Ruang Iklan

                    <?php endif; ?>

                </div>


            </aside>

        </div>

    </section>


    <!-- =====================================================
         OPINI & ANALISIS
    ====================================================== -->

    <?php if (
        !empty($opinionArticles)
    ): ?>

        <section class="opinion-section">

            <div class="home-section-header">

                <div>

                    <h2 class="home-section-title">
                        Opini & Analisis
                    </h2>

                    <p class="home-section-subtitle">
                        Perspektif, gagasan, dan pembacaan
                        mendalam terhadap isu yang berkembang.
                    </p>

                </div>

            </div>


            <div class="opinion-layout">


                <?php
                $opinionMain =
                    $opinionArticles[0]
                    ?? null;

                $opinionImage =
                    $opinionMain
                        ? homeImageUrl(
                            $opinionMain[
                                'thumbnail_path'
                            ] ?? ''
                        )
                        : '';
                ?>


                <?php if ($opinionMain): ?>

                    <article class="opinion-feature">

                        <div class="opinion-feature-image">

                            <?php if (
                                $opinionImage
                            ): ?>

                                <img
                                    src="<?= homeEscape(
                                        $opinionImage
                                    ) ?>"
                                    alt="<?= homeEscape(
                                        $opinionMain[
                                            'title'
                                        ]
                                        ?? 'Opini'
                                    ) ?>"
                                    loading="lazy"
                                >

                            <?php endif; ?>

                        </div>


                        <div class="opinion-feature-content">

                            <div class="opinion-label">
                                OPINI & ANALISIS
                            </div>


                            <h3 class="opinion-feature-title">

                                <a
                                    href="<?= homeArticleUrl(
                                        $baseUrl,
                                        $opinionMain[
                                            'slug'
                                        ] ?? ''
                                    ) ?>"
                                >

                                    <?= homeEscape(
                                        $opinionMain[
                                            'title'
                                        ]
                                        ?? ''
                                    ) ?>

                                </a>

                            </h3>


                            <div class="opinion-feature-meta">

                                <?= homeEscape(
                                    $opinionMain[
                                        'author_name'
                                    ]
                                    ?? 'Redaksi'
                                ) ?>

                                &nbsp;•&nbsp;

                                <?= homeShortDate(
                                    $opinionMain[
                                        'published_at'
                                    ]
                                    ?? null
                                ) ?>

                            </div>

                        </div>

                    </article>

                <?php endif; ?>


                <div class="opinion-side">

                    <?php foreach (
                        array_slice(
                            $opinionArticles,
                            1,
                            3
                        )
                        as $opinion
                    ): ?>

                        <?php
                        $opinionSmallImage =
                            homeImageUrl(
                                $opinion[
                                    'thumbnail_path'
                                ] ?? ''
                            );
                        ?>

                        <article class="opinion-small">

                            <div class="opinion-small-image">

                                <?php if (
                                    $opinionSmallImage
                                ): ?>

                                    <img
                                        src="<?= homeEscape(
                                            $opinionSmallImage
                                        ) ?>"
                                        alt="<?= homeEscape(
                                            $opinion[
                                                'title'
                                            ]
                                            ?? 'Opini'
                                        ) ?>"
                                        loading="lazy"
                                    >

                                <?php endif; ?>

                            </div>


                            <div class="opinion-small-content">

                                <div class="opinion-small-category">

                                    <?= homeEscape(
                                        homeCategoryName(
                                            $opinion
                                        )
                                    ) ?>

                                </div>


                                <h3 class="opinion-small-title">

                                    <a
                                        href="<?= homeArticleUrl(
                                            $baseUrl,
                                            $opinion[
                                                'slug'
                                            ] ?? ''
                                        ) ?>"
                                    >

                                        <?= homeEscape(
                                            $opinion[
                                                'title'
                                            ]
                                            ?? ''
                                        ) ?>

                                    </a>

                                </h3>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </div>

        </section>

    <?php endif; ?>


    <!-- =====================================================
         BERITA PER KATEGORI
    ====================================================== -->

    <?php if (
        !empty($categoryArticles)
    ): ?>

        <section class="category-news-section">

            <div class="home-section-header">

                <div>

                    <h2 class="home-section-title">
                        Berita per Kategori
                    </h2>

                    <p class="home-section-subtitle">
                        Ikuti perkembangan berita dari
                        berbagai bidang dan kategori.
                    </p>

                </div>


                <?php if (
                    !empty($categories)
                ): ?>

                    <a
                        href="<?= rtrim(
                            $baseUrl,
                            '/'
                        ) ?>/category/nasional"
                        class="home-section-link"
                    >

                        Semua Kategori →

                    </a>

                <?php endif; ?>

            </div>


            <div class="category-news-grid">

                <?php foreach (
                    $categoryArticles
                    as $categoryName =>
                    $categoryItems
                ): ?>

                    <section class="category-block">

                        <div class="category-block-heading">

                            <h3>

                                <?= homeEscape(
                                    $categoryName
                                ) ?>

                            </h3>


                            <?php
                            $firstCategory =
                                $categoryItems[0]
                                ?? [];

                            $categorySlug =
                                homeCategorySlug(
                                    $firstCategory
                                );
                            ?>

                            <?php if (
                                $categorySlug !== ''
                            ): ?>

                                <a
                                    href="<?= homeCategoryUrl(
                                        $baseUrl,
                                        $categorySlug
                                    ) ?>"
                                >
                                    Lihat →
                                </a>

                            <?php endif; ?>

                        </div>


                        <div class="category-block-list">

                            <?php foreach (
                                $categoryItems
                                as $categoryItem
                            ): ?>

                                <?php
                                $categoryImage =
                                    homeImageUrl(
                                        $categoryItem[
                                            'thumbnail_path'
                                        ] ?? ''
                                    );
                                ?>

                                <article
                                    class="category-block-item"
                                >

                                    <div
                                        class="category-block-image"
                                    >

                                        <?php if (
                                            $categoryImage
                                        ): ?>

                                            <img
                                                src="<?= homeEscape(
                                                    $categoryImage
                                                ) ?>"
                                                alt="<?= homeEscape(
                                                    $categoryItem[
                                                        'title'
                                                    ]
                                                    ?? 'Artikel'
                                                ) ?>"
                                                loading="lazy"
                                            >

                                        <?php endif; ?>

                                    </div>


                                    <div>

                                        <h4
                                            class="category-block-title"
                                        >

                                            <a
                                                href="<?= homeArticleUrl(
                                                    $baseUrl,
                                                    $categoryItem[
                                                        'slug'
                                                    ] ?? ''
                                                ) ?>"
                                            >

                                                <?= homeEscape(
                                                    $categoryItem[
                                                        'title'
                                                    ]
                                                    ?? ''
                                                ) ?>

                                            </a>

                                        </h4>


                                        <div
                                            class="category-block-meta"
                                        >

                                            <?= homeShortDate(
                                                $categoryItem[
                                                    'published_at'
                                                ]
                                                ?? null
                                            ) ?>

                                            &nbsp;•&nbsp;

                                            <?= homeEscape(
                                                $categoryItem[
                                                    'author_name'
                                                ]
                                                ?? 'Redaksi'
                                            ) ?>

                                        </div>

                                    </div>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    </section>

                <?php endforeach; ?>

            </div>

        </section>

    <?php endif; ?>


    <!-- =====================================================
         LIVE FEED PUBLIK
    ====================================================== -->

    <?php if (
        !empty($popularSevenDays)
    ): ?>

        <section class="live-section">

            <div class="home-section-header">

                <div>

                    <h2 class="home-section-title">
                        Live Feed Publik
                    </h2>

                    <p class="home-section-subtitle">
                        Berita yang mendapat perhatian
                        pembaca dalam tujuh hari terakhir.
                    </p>

                </div>

                <span class="home-section-subtitle">
                    7 Hari Terakhir
                </span>

            </div>


            <div class="live-wrapper">

                <div class="live-list">

                    <?php foreach (
                        array_slice(
                            $popularSevenDays,
                            0,
                            4
                        )
                        as $feed
                    ): ?>

                        <article class="live-item">

                            <div class="live-time">

                                <?= homeShortDate(
                                    $feed[
                                        'published_at'
                                    ]
                                    ?? null
                                ) ?>

                            </div>


                            <h3 class="live-title">

                                <a
                                    href="<?= homeArticleUrl(
                                        $baseUrl,
                                        $feed['slug']
                                        ?? ''
                                    ) ?>"
                                >

                                    <?= homeEscape(
                                        $feed['title']
                                        ?? ''
                                    ) ?>

                                </a>

                            </h3>


                            <?php if (
                                homeCategoryName(
                                    $feed
                                ) !== ''
                            ): ?>

                                <div class="live-category">

                                    <?= homeEscape(
                                        homeCategoryName(
                                            $feed
                                        )
                                    ) ?>

                                </div>

                            <?php endif; ?>

                        </article>

                    <?php endforeach; ?>

                </div>

            </div>

        </section>

    <?php endif; ?>


    <!-- =====================================================
         FOKUS INFORA
    ====================================================== -->

    <section class="focus-section">

        <div class="focus-box">


            <div class="focus-content">

                <div class="focus-label">
                    Fokus INFORA
                </div>


                <h2 class="focus-title">

                    Membaca Indonesia
                    Lebih Dalam.

                </h2>


                <p class="focus-text">

                    INFORA SN menghadirkan liputan,
                    analisis, dan perspektif untuk
                    membantu pembaca memahami berbagai
                    isu publik secara lebih menyeluruh.

                </p>

            </div>


            <div class="focus-image">

                <?php if (
                    $hero &&
                    !empty(
                        $hero[
                            'thumbnail_path'
                        ]
                    )
                ): ?>

                    <img
                        src="<?= homeEscape(
                            homeImageUrl(
                                $hero[
                                    'thumbnail_path'
                                ]
                            )
                        ) ?>"
                        alt="Fokus INFORA"
                        loading="lazy"
                    >

                <?php else: ?>

                    <div class="focus-placeholder">
                        INFORA SN
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- =====================================================
         RUANG KONTRIBUTOR
    ====================================================== -->

    <section class="contributor-section">

        <div class="contributor-box">


            <div class="contributor-content">

                <div class="contributor-label">
                    Ruang Kontributor
                </div>


                <h2 class="contributor-title">

                    Punya cerita,
                    data, atau
                    perspektif?

                </h2>


                <p class="contributor-text">

                    Kirimkan tulisan Anda melalui
                    INFORA SN. Setiap artikel akan
                    melalui proses review dan kurasi
                    redaksi sebelum dipublikasikan.

                </p>


                <a
                    href="<?= rtrim(
                        $baseUrl,
                        '/'
                    ) ?>/client/article/create"
                    class="contributor-button"
                >

                    Tulis Artikel →

                </a>

            </div>


            <div class="contributor-process">


                <div class="contributor-step">

                    <div class="contributor-number">
                        01
                    </div>

                    <div class="contributor-step-title">
                        Kirim Tulisan
                    </div>

                </div>


                <div class="contributor-step">

                    <div class="contributor-number">
                        02
                    </div>

                    <div class="contributor-step-title">
                        Review
                    </div>

                </div>


                <div class="contributor-step">

                    <div class="contributor-number">
                        03
                    </div>

                    <div class="contributor-step-title">
                        Kurasi
                    </div>

                </div>


                <div class="contributor-step">

                    <div class="contributor-number">
                        04
                    </div>

                    <div class="contributor-step-title">
                        Publikasi
                    </div>

                </div>


            </div>

        </div>

    </section>


    <!-- =====================================================
         INTEGRITAS
    ====================================================== -->

    <section class="integrity-section">

        <div class="integrity-box">


            <div class="integrity-icon">
                ✓
            </div>


            <div>

                <h2 class="integrity-title">

                    Jurnalisme dengan Integritas

                </h2>


                <p class="integrity-text">

                    INFORA SN berkomitmen menyajikan
                    informasi yang faktual, berimbang,
                    dan dapat dipertanggungjawabkan.

                </p>

            </div>

        </div>

    </section>


</div>

</main>


<?php

/* =========================================================
   SHARED FOOTER
========================================================= */

require __DIR__ . '/layout/footer.php';

?>