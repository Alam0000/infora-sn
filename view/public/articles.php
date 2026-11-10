<?php

/*
|--------------------------------------------------------------------------
| INFORA SN - PUBLIC ARTICLES LIST
|--------------------------------------------------------------------------
| Dipakai untuk 2 route:
| 1. /articles           -> PublicController::articles()
| 2. /category/{slug}    -> PublicController::category()
|
| Data yang dikirim controller:
| $articles, $categories, $page, $totalPages, $total,
| $pageTitle, $pageDescription, $category (opsional, hanya ada di /category)
|--------------------------------------------------------------------------
*/

$articles = $articles ?? [];
$categories = $categories ?? [];
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$total = $total ?? 0;
$category = $category ?? null;
$keyword = $keyword ?? '';

$pageTitle = $pageTitle ?? 'Semua Artikel';
$pageDescription = $pageDescription ?? 'Kumpulan artikel dan berita terbaru INFORA SN.';


/*
|--------------------------------------------------------------------------
| BASE URL
|--------------------------------------------------------------------------
*/

$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

if ($baseUrl === '/' || $baseUrl === '\\') {
    $baseUrl = '';
}


/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/

function listImageUrl($path)
{
    global $baseUrl;

    $path = trim((string) $path);

    if ($path === '') {
        return '';
    }

    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }

    $path = str_replace('\\', '/', $path);
    $path = ltrim($path, '/');

    $normalizedBase = trim(str_replace('\\', '/', (string) $baseUrl), '/');

    if ($normalizedBase !== '' && strpos($path, $normalizedBase . '/') === 0) {
        return '/' . $path;
    }

    // --- TES SEMENTARA: hardcode dulu, hapus lagi setelah ketauan hasilnya ---
    return '/infora-sn/' . $path;
}


function listDate($date)
{
    if (empty($date)) {
        return '-';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return '-';
    }

    $months = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];

    return date('d', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
}


function listReadingTime($content)
{
    $text = trim(strip_tags($content ?? ''));

    if ($text === '') {
        return 1;
    }

    $words = preg_split('/\s+/', $text);

    return max(1, (int) ceil(count($words) / 200));
}


function listArticleUrl($baseUrl, $slug)
{
    return $baseUrl . '/article/' . rawurlencode($slug);
}


function listCategoryUrl($baseUrl, $slug)
{
    return $baseUrl . '/category/' . rawurlencode($slug);
}


function listPageUrl($baseUrl, $basePath, $page, $keyword = '')
{
    $url = $baseUrl . $basePath;
    $params = [];

    if ($page > 1) {
        $params['page'] = $page;
    }

    if ($keyword !== '') {
        $params['q'] = $keyword;
    }

    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    return $url;
}


/* =========================================================
   SHARED HEADER & NAVBAR
========================================================= */

require __DIR__ . '/layout/header.php';

$baseUrl = Auth::baseUrl();

require __DIR__ . '/layout/navbar.php';

// Path dasar untuk pagination, tergantung ini halaman /articles, /category/x, atau /search
if (!empty($category['slug'])) {
    $basePath = '/category/' . rawurlencode($category['slug']);
} elseif ($keyword !== '') {
    $basePath = '/search';
} else {
    $basePath = '/articles';
}

?>


<style>

.infora-list-page {
    width: 100%;
    background: #f4f6f9;
    padding: 16px 0 45px;
}

.list-container {
    width: min(1240px, calc(100% - 42px));
    margin: 0 auto;
}

.list-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
    color: #8a9097;
    font-size: 11px;
}

.list-breadcrumb a {
    color: #1755d1;
    font-weight: 800;
}

.list-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 16px;
    padding: 18px 20px;
    background: #ffffff;
    border: 1px solid #dfe3e8;
    border-radius: 3px;
}

.list-label {
    color: #1755d1;
    font-size: 10px;
    font-weight: 900;
    text-transform: uppercase;
}

.list-title {
    margin: 6px 0 0;
    color: #10151c;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 30px;
    font-weight: 900;
    line-height: 1.1;
}

.list-description {
    max-width: 750px;
    margin: 8px 0 0;
    color: #7b8189;
    font-size: 12px;
    line-height: 1.5;
}

.list-count {
    flex-shrink: 0;
    padding: 6px 10px;
    background: #f1f4f8;
    border: 1px solid #e1e5e9;
    border-radius: 2px;
    color: #646b74;
    font-size: 11px;
    font-weight: 900;
}

.list-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 300px;
    gap: 16px;
    align-items: start;
}

.list-articles {
    min-width: 0;
    background: #ffffff;
    border: 1px solid #dfe3e8;
    border-radius: 3px;
}

.list-article-item {
    display: grid;
    grid-template-columns: 220px minmax(0, 1fr);
    gap: 14px;
    padding: 14px;
    border-bottom: 1px solid #e5e8eb;
}

.list-article-item:last-child {
    border-bottom: none;
}

.list-article-image {
    aspect-ratio: 16 / 9;
    overflow: hidden;
    background: #e6e9ed;
    border-radius: 2px;
}

.list-article-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.list-image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: #8b9198;
    font-size: 11px;
    font-weight: 900;
}

.list-article-category {
    color: #1755d1;
    font-size: 10px;
    font-weight: 900;
    text-transform: uppercase;
}

.list-article-title {
    margin: 6px 0 0;
    color: #1b2026;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 18px;
    line-height: 1.25;
    font-weight: 900;
}

.list-article-title a {
    color: inherit;
}

.list-article-title a:hover {
    color: #1755d1;
}

.list-article-excerpt {
    display: -webkit-box;
    overflow: hidden;
    margin: 6px 0 0;
    color: #777e86;
    font-size: 12px;
    line-height: 1.5;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.list-article-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
    color: #92979d;
    font-size: 11px;
}

.list-article-meta strong {
    color: #3d444c;
}

.list-empty {
    padding: 60px 20px;
    text-align: center;
}

.list-empty-title {
    margin: 0;
    color: #353a40;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 18px;
}

.list-empty-text {
    margin: 8px 0 0;
    color: #8a9097;
    font-size: 12px;
}

.list-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 16px;
}

.list-pagination a,
.list-pagination span {
    padding: 6px 11px;
    border: 1px solid #dfe3e8;
    border-radius: 2px;
    color: #5e656d;
    font-size: 11px;
    font-weight: 800;
}

.list-pagination a:hover {
    background: #1755d1;
    border-color: #1755d1;
    color: #ffffff;
}

.list-pagination .active {
    background: #1755d1;
    border-color: #1755d1;
    color: #ffffff;
}

.list-sidebar {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.list-sidebar-panel {
    background: #ffffff;
    border: 1px solid #dfe3e8;
    border-radius: 3px;
    overflow: hidden;
}

.list-sidebar-header {
    padding: 10px 12px;
    border-bottom: 1px solid #e2e5e8;
}

.list-sidebar-title {
    margin: 0;
    color: #171b20;
    font-size: 12px;
    font-weight: 900;
}

.list-sidebar-nav {
    display: flex;
    flex-direction: column;
}

.list-sidebar-nav a {
    padding: 9px 12px;
    border-bottom: 1px solid #eef0f2;
    color: #444a52;
    font-size: 12px;
    font-weight: 700;
}

.list-sidebar-nav a:last-child {
    border-bottom: none;
}

.list-sidebar-nav a:hover,
.list-sidebar-nav a.active {
    background: #eef2ff;
    color: #1755d1;
}

@media (max-width: 900px) {
    .list-layout { grid-template-columns: 1fr; }
    .list-article-item { grid-template-columns: 150px minmax(0, 1fr); }
}

@media (max-width: 560px) {
    .list-container { width: calc(100% - 18px); }
    .list-title { font-size: 22px; }
    .list-article-item { grid-template-columns: 100px minmax(0, 1fr); gap: 8px; padding: 10px; }
    .list-article-title { font-size: 13px; }
    .list-article-excerpt { display: none; }
}

</style>


<main class="infora-list-page">

    <div class="list-container">

        <!-- BREADCRUMB -->
        <nav class="list-breadcrumb">
            <a href="<?= $baseUrl ?>/">Utama</a>
            <span>/</span>
            <span><?= htmlspecialchars($pageTitle) ?></span>
        </nav>

        <!-- HEADER -->
        <section class="list-header">
            <div>
                <div class="list-label">
                    <?= !empty($category) ? 'Rubrik' : 'Artikel' ?>
                </div>
                <h1 class="list-title"><?= htmlspecialchars($pageTitle) ?></h1>
                <p class="list-description"><?= htmlspecialchars($pageDescription) ?></p>
            </div>
            <div class="list-count"><?= (int) $total ?> Artikel</div>
        </section>

        <div class="list-layout">

            <!-- ARTICLE LIST -->
            <section class="list-articles">

                <?php if (!empty($articles)): ?>

                    <?php foreach ($articles as $item): ?>

                        <?php
                        $image = listImageUrl($item['thumbnail_path'] ?? '');
                        $slug = $item['slug'] ?? '';
                        ?>

                        <article class="list-article-item">

                            <a href="<?= listArticleUrl($baseUrl, $slug) ?>" class="list-article-image">
                                <?php if ($image): ?>
                                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($item['title'] ?? '') ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="list-image-placeholder">INFORA SN</div>
                                <?php endif; ?>
                            </a>

                            <div>
                                <div class="list-article-category">
                                    <?= htmlspecialchars($item['category_name'] ?? '') ?>
                                </div>

                                <h3 class="list-article-title">
                                    <a href="<?= listArticleUrl($baseUrl, $slug) ?>">
                                        <?= htmlspecialchars($item['title'] ?? '') ?>
                                    </a>
                                </h3>

                                <?php if (!empty($item['excerpt'])): ?>
                                    <p class="list-article-excerpt"><?= htmlspecialchars($item['excerpt']) ?></p>
                                <?php endif; ?>

                                <div class="list-article-meta">
                                    <strong><?= htmlspecialchars($item['author_name'] ?? 'INFORA SN') ?></strong>
                                    <span>•</span>
                                    <span><?= listDate($item['published_at'] ?? '') ?></span>
                                    <span>•</span>
                                    <span><?= listReadingTime($item['content'] ?? '') ?> menit baca</span>
                                    <span>•</span>
                                    <span><?= (int) ($item['view_count'] ?? 0) ?> views</span>
                                </div>
                            </div>

                        </article>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="list-empty">
                        <h3 class="list-empty-title">Belum Ada Artikel</h3>
                        <p class="list-empty-text">Belum ada artikel yang dipublikasikan di sini.</p>
                    </div>

                <?php endif; ?>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>

                    <div class="list-pagination">

                        <?php if ($page > 1): ?>
                            <a href="<?= listPageUrl($baseUrl, $basePath, $page - 1, $keyword) ?>">‹ Sebelumnya</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="active"><?= $i ?></span>
                            <?php else: ?>
                                <a href="<?= listPageUrl($baseUrl, $basePath, $i, $keyword) ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="<?= listPageUrl($baseUrl, $basePath, $page + 1, $keyword) ?>">Selanjutnya ›</a>
                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            </section>

            <!-- SIDEBAR -->
            <aside class="list-sidebar">

                <?php if (!empty($categories)): ?>

                    <section class="list-sidebar-panel">
                        <div class="list-sidebar-header">
                            <h2 class="list-sidebar-title">Rubrik</h2>
                        </div>
                        <div class="list-sidebar-nav">
                            <?php foreach ($categories as $cat): ?>
                                <?php $isActive = !empty($category['slug']) && $category['slug'] === ($cat['slug'] ?? ''); ?>
                                <a href="<?= listCategoryUrl($baseUrl, $cat['slug'] ?? '') ?>" class="<?= $isActive ? 'active' : '' ?>">
                                    <?= htmlspecialchars($cat['name'] ?? '') ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>

                <?php endif; ?>

            </aside>

        </div>

    </div>

</main>


<?php require __DIR__ . '/layout/footer.php'; ?>