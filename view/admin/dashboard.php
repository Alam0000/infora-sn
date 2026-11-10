<?php

$user = $user ?? Auth::user();

$stats = $stats ?? [];

$articles = $articles ?? [];

$baseUrl = Auth::baseUrl();


/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/

$totalArticles = (int) ($stats['total_articles'] ?? 0);

$waitingReview = (int) ($stats['waiting_review'] ?? 0);

$revision = (int) ($stats['revision'] ?? 0);

$published = (int) ($stats['published'] ?? 0);

$draft = (int) ($stats['draft'] ?? 0);

$totalClients = (int) ($stats['total_clients'] ?? 0);

$rejected = (int) ($stats['rejected'] ?? 0);


/*
|--------------------------------------------------------------------------
| STATUS LABEL
|--------------------------------------------------------------------------
*/

function dashboardStatusLabel($status)
{
    $labels = [
        'DRAFT'            => 'DRAFT',
        'MENUNGGU_REVIEW'  => 'MENUNGGU REVIEW',
        'REVISI'           => 'BUTUH REVISI',
        'DITOLAK'          => 'DITOLAK',
        'PUBLISHED'        => 'PUBLISHED'
    ];

    return $labels[$status] ?? $status;
}


/*
|--------------------------------------------------------------------------
| STATUS CLASS
|--------------------------------------------------------------------------
*/

function dashboardStatusClass($status)
{
    switch ($status) {

        case 'DRAFT':
            return 'status-draft';

        case 'MENUNGGU_REVIEW':
            return 'status-review';

        case 'REVISI':
            return 'status-revisi';

        case 'DITOLAK':
            return 'status-ditolak';

        case 'PUBLISHED':
            return 'status-published';

        default:
            return 'status-default';
    }
}


/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

function dashboardDate($date)
{
    if (!$date) {
        return '-';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return '-';
    }

    return date('d M Y', $timestamp);
}

?>

<?php include __DIR__ . '/layout/sidebar.php'; ?>

<div class="admin-main">

    <?php include __DIR__ . '/layout/header.php'; ?>


    <!-- =========================================================
         CONTENT
    ========================================================== -->

    <main class="admin-content">


        <!-- =====================================================
             PAGE HEADER
        ====================================================== -->

        <div class="dashboard-heading">

            <div>
                <h1>Dashboard Redaksi</h1>

                <p>
                    Kelola naskah, review, kurasi, dan publikasi INFORA SN.
                </p>
            </div>

        </div>



        <!-- =====================================================
             STATISTICS
        ====================================================== -->

        <section class="stats-grid">


            <!-- TOTAL NASKAH -->

            <a
                href="<?= $baseUrl ?>/admin/articles"
                class="stat-card"
            >

                <div class="stat-label">
                    TOTAL NASKAH
                </div>

                <div class="stat-number">
                    <?= $totalArticles ?>
                </div>

                <div class="stat-footer">
                    <span>Semua artikel</span>
                    <span class="stat-arrow">→</span>
                </div>

            </a>



            <!-- MENUNGGU REVIEW -->

            <a
                href="<?= $baseUrl ?>/admin/articles?status=MENUNGGU_REVIEW"
                class="stat-card stat-review"
            >

                <div class="stat-label">
                    MENUNGGU REVIEW
                </div>

                <div class="stat-number">
                    <?= $waitingReview ?>
                </div>

                <div class="stat-footer">
                    <span>Perlu ditinjau</span>
                    <span class="stat-arrow">→</span>
                </div>

            </a>



            <!-- REVISI -->

            <a
                href="<?= $baseUrl ?>/admin/articles?status=REVISI"
                class="stat-card stat-revision"
            >

                <div class="stat-label">
                    BUTUH REVISI
                </div>

                <div class="stat-number">
                    <?= $revision ?>
                </div>

                <div class="stat-footer">
                    <span>Perlu perbaikan</span>
                    <span class="stat-arrow">→</span>
                </div>

            </a>



            <!-- PUBLISHED -->

            <a
                href="<?= $baseUrl ?>/admin/articles?status=PUBLISHED"
                class="stat-card stat-published"
            >

                <div class="stat-label">
                    PUBLISHED
                </div>

                <div class="stat-number">
                    <?= $published ?>
                </div>

                <div class="stat-footer">
                    <span>Tayang di portal</span>
                    <span class="stat-arrow">→</span>
                </div>

            </a>



            <!-- DRAFT -->

            <a
                href="<?= $baseUrl ?>/admin/articles?status=DRAFT"
                class="stat-card stat-draft"
            >

                <div class="stat-label">
                    DRAFT
                </div>

                <div class="stat-number">
                    <?= $draft ?>
                </div>

                <div class="stat-footer">
                    <span>Naskah tersimpan</span>
                    <span class="stat-arrow">→</span>
                </div>

            </a>



            <!-- CLIENT -->

            <a
                href="<?= $baseUrl ?>/admin/clients"
                class="stat-card stat-client"
            >

                <div class="stat-label">
                    PENULIS / CLIENT
                </div>

                <div class="stat-number">
                    <?= $totalClients ?>
                </div>

                <div class="stat-footer">
                    <span>Total penulis</span>
                    <span class="stat-arrow">→</span>
                </div>

            </a>

        </section>



        <!-- =====================================================
             MAIN DASHBOARD GRID
        ====================================================== -->

        <section class="dashboard-grid">


            <!-- =================================================
                 ANTRIAN REVIEW
            ================================================== -->

            <div class="dashboard-panel">

                <div class="panel-header">

                    <div>
                        <h2>Antrian Review</h2>

                        <p>
                            Artikel yang menunggu pemeriksaan redaksi.
                        </p>
                    </div>

                    <a
                        href="<?= $baseUrl ?>/admin/articles?status=MENUNGGU_REVIEW"
                        class="panel-link"
                    >
                        Lihat Semua →
                    </a>

                </div>


                <div class="panel-body review-body">

                    <?php

                    $reviewArticles = [];

                    foreach ($articles as $article) {

                        if (
                            ($article['status'] ?? '') ===
                            'MENUNGGU_REVIEW'
                        ) {
                            $reviewArticles[] = $article;
                        }

                    }

                    ?>


                    <?php if (empty($reviewArticles)): ?>

                        <div class="empty-state">

                            <div class="empty-icon">
                                ✓
                            </div>

                            <div class="empty-title">
                                Belum ada artikel yang menunggu review.
                            </div>

                            <div class="empty-text">
                                Artikel yang dikirim penulis akan muncul di sini.
                            </div>

                        </div>

                    <?php else: ?>


                        <div class="review-list">

                            <?php foreach ($reviewArticles as $article): ?>

                                <div class="review-item">

                                    <div class="review-info">

                                        <a
                                            href="<?= $baseUrl ?>/admin/article/<?= (int) $article['id'] ?>/review"
                                            class="review-title"
                                        >
                                            <?= htmlspecialchars(
                                                $article['title'] ?? '-'
                                            ) ?>
                                        </a>

                                        <div class="review-meta">

                                            <span>
                                                <?= htmlspecialchars(
                                                    $article['author_name'] ?? '-'
                                                ) ?>
                                            </span>

                                            <span>•</span>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $article['category_name'] ?? '-'
                                                ) ?>
                                            </span>

                                            <span>•</span>

                                            <span>
                                                <?= dashboardDate(
                                                    $article['submitted_at']
                                                    ?? $article['created_at']
                                                    ?? null
                                                ) ?>
                                            </span>

                                        </div>

                                    </div>


                                    <a
                                        href="<?= $baseUrl ?>/admin/article/<?= (int) $article['id'] ?>/review"
                                        class="review-button"
                                    >
                                        Review
                                    </a>

                                </div>

                            <?php endforeach; ?>

                        </div>


                    <?php endif; ?>

                </div>

            </div>



            <!-- =================================================
                 ARTIKEL TERBARU
            ================================================== -->

            <div class="dashboard-panel">

                <div class="panel-header">

                    <div>
                        <h2>Artikel Terbaru</h2>

                        <p>
                            Naskah terbaru yang masuk ke sistem.
                        </p>
                    </div>

                    <a
                        href="<?= $baseUrl ?>/admin/articles"
                        class="panel-link"
                    >
                        Semua →
                    </a>

                </div>


                <div class="panel-body recent-body">

                    <?php if (empty($articles)): ?>

                        <div class="empty-state">

                            <div class="empty-icon">
                                —
                            </div>

                            <div class="empty-title">
                                Belum ada artikel.
                            </div>

                            <div class="empty-text">
                                Artikel akan muncul setelah penulis membuat naskah.
                            </div>

                        </div>

                    <?php else: ?>

                        <div class="recent-list">

                            <?php foreach ($articles as $article): ?>

                                <div class="recent-item">

                                    <div class="recent-content">

                                        <a
                                            href="<?= $baseUrl ?>/admin/article/<?= (int) $article['id'] ?>/review"
                                            class="recent-title"
                                        >
                                            <?= htmlspecialchars(
                                                $article['title'] ?? '-'
                                            ) ?>
                                        </a>


                                        <div class="recent-meta">

                                            <?= htmlspecialchars(
                                                $article['author_name'] ?? '-'
                                            ) ?>

                                            <span>•</span>

                                            <?= dashboardDate(
                                                $article['created_at'] ?? null
                                            ) ?>

                                        </div>

                                    </div>


                                    <span
                                        class="status-badge <?= dashboardStatusClass(
                                            $article['status'] ?? ''
                                        ) ?>"
                                    >
                                        <?= dashboardStatusLabel(
                                            $article['status'] ?? ''
                                        ) ?>
                                    </span>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </section>



        <!-- =====================================================
             STATUS OVERVIEW
        ====================================================== -->

        <section class="dashboard-panel status-overview">

            <div class="panel-header">

                <div>
                    <h2>Kontrol Status Artikel</h2>

                    <p>
                        Pilih status untuk melihat dan mengelola artikel.
                    </p>
                </div>

            </div>


            <div class="status-control-grid">


                <a
                    href="<?= $baseUrl ?>/admin/articles?status=DRAFT"
                    class="status-control"
                >

                    <span class="status-dot dot-draft"></span>

                    <div>
                        <strong>DRAFT</strong>
                        <small>
                            <?= $draft ?> artikel
                        </small>
                    </div>

                    <span class="control-arrow">→</span>

                </a>


                <a
                    href="<?= $baseUrl ?>/admin/articles?status=MENUNGGU_REVIEW"
                    class="status-control"
                >

                    <span class="status-dot dot-review"></span>

                    <div>
                        <strong>MENUNGGU REVIEW</strong>
                        <small>
                            <?= $waitingReview ?> artikel
                        </small>
                    </div>

                    <span class="control-arrow">→</span>

                </a>


                <a
                    href="<?= $baseUrl ?>/admin/articles?status=REVISI"
                    class="status-control"
                >

                    <span class="status-dot dot-revisi"></span>

                    <div>
                        <strong>REVISI</strong>
                        <small>
                            <?= $revision ?> artikel
                        </small>
                    </div>

                    <span class="control-arrow">→</span>

                </a>


                <a
                    href="<?= $baseUrl ?>/admin/articles?status=DITOLAK"
                    class="status-control"
                >

                    <span class="status-dot dot-ditolak"></span>

                    <div>
                        <strong>DITOLAK</strong>
                        <small>
                            <?= $rejected ?> artikel
                        </small>
                    </div>

                    <span class="control-arrow">→</span>

                </a>


                <a
                    href="<?= $baseUrl ?>/admin/articles?status=PUBLISHED"
                    class="status-control"
                >

                    <span class="status-dot dot-published"></span>

                    <div>
                        <strong>PUBLISHED</strong>
                        <small>
                            <?= $published ?> artikel
                        </small>
                    </div>

                    <span class="control-arrow">→</span>

                </a>

            </div>

        </section>


    </main>

</div>



<style>

/* =============================================================
   RESET
============================================================= */

.admin-main {
    margin-left: 245px;
    min-height: 100vh;
    background: #f4f5f7;
}


/* =============================================================
   CONTENT
============================================================= */

.admin-content {
    padding: 38px 42px 60px;
    box-sizing: border-box;
}


/* =============================================================
   PAGE HEADING
============================================================= */

.dashboard-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;

    margin-bottom: 28px;
}


.dashboard-heading h1 {
    margin: 0;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 27px;
    font-weight: 800;

    letter-spacing: -0.8px;

    color: #111111;
}


.dashboard-heading p {
    margin: 7px 0 0;

    font-size: 12px;

    color: #777777;
}


/* =============================================================
   STATISTICS GRID
============================================================= */

.stats-grid {
    display: grid;

    grid-template-columns:
        repeat(6, minmax(0, 1fr));

    gap: 14px;

    margin-bottom: 30px;
}


.stat-card {
    position: relative;

    min-height: 145px;

    padding: 22px 20px 17px;

    box-sizing: border-box;

    background: #ffffff;

    border: 1px solid #dddddd;

    text-decoration: none;

    color: #111111;

    overflow: hidden;

    transition:
        transform 0.18s ease,
        box-shadow 0.18s ease,
        border-color 0.18s ease;
}


.stat-card::after {
    content: "";

    position: absolute;

    left: 20px;
    right: 20px;

    bottom: 0;

    height: 4px;

    background: #caff00;
}


.stat-card:hover {
    transform: translateY(-2px);

    border-color: #caff00;

    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
}


.stat-label {
    font-family: Georgia, serif;

    font-size: 10px;

    letter-spacing: 0.5px;

    color: #777777;

    white-space: nowrap;
}


.stat-number {
    margin-top: 16px;

    font-family: Georgia, serif;

    font-size: 32px;

    font-weight: 700;

    line-height: 1;

    color: #000000;
}


.stat-footer {
    position: absolute;

    left: 20px;
    right: 20px;
    bottom: 12px;

    display: flex;

    justify-content: space-between;
    align-items: center;

    font-size: 9px;

    color: #999999;
}


.stat-arrow {
    font-size: 13px;

    color: #555555;
}


/* =============================================================
   DASHBOARD GRID
============================================================= */

.dashboard-grid {
    display: grid;

    grid-template-columns:
        minmax(0, 1.55fr)
        minmax(0, 1fr);

    gap: 20px;

    margin-bottom: 20px;
}


/* =============================================================
   PANEL
============================================================= */

.dashboard-panel {
    background: #ffffff;

    border: 1px solid #dcdcdc;
}


.panel-header {
    min-height: 74px;

    padding: 20px 20px 17px;

    box-sizing: border-box;

    display: flex;

    justify-content: space-between;
    align-items: flex-start;

    border-bottom: 1px solid #e2e2e2;
}


.panel-header h2 {
    margin: 0;

    font-family: Georgia, "Times New Roman", serif;

    font-size: 15px;

    font-weight: 700;

    color: #111111;
}


.panel-header p {
    margin: 5px 0 0;

    font-size: 10px;

    color: #999999;
}


.panel-link {
    margin-top: 3px;

    font-family: Georgia, serif;

    font-size: 10px;

    font-weight: 700;

    color: #111111;

    text-decoration: none;

    white-space: nowrap;
}


.panel-link:hover {
    color: #6e8b00;
}


/* =============================================================
   PANEL BODY
============================================================= */

.panel-body {
    min-height: 180px;
}


/* =============================================================
   EMPTY STATE
============================================================= */

.empty-state {
    min-height: 180px;

    display: flex;

    flex-direction: column;

    justify-content: center;

    align-items: center;

    text-align: center;
}


.empty-icon {
    width: 36px;
    height: 36px;

    margin-bottom: 12px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #f2f2f2;

    color: #888888;

    font-size: 15px;
}


.empty-title {
    font-family: Georgia, serif;

    font-size: 12px;

    font-weight: 700;

    color: #555555;
}


.empty-text {
    margin-top: 6px;

    font-size: 10px;

    color: #999999;
}


/* =============================================================
   REVIEW LIST
============================================================= */

.review-list {
    width: 100%;
}


.review-item {
    min-height: 75px;

    padding: 15px 20px;

    box-sizing: border-box;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    border-bottom: 1px solid #eeeeee;
}


.review-item:last-child {
    border-bottom: none;
}


.review-info {
    min-width: 0;
}


.review-title {
    display: block;

    font-family: Georgia, serif;

    font-size: 12px;

    font-weight: 700;

    color: #171717;

    text-decoration: none;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


.review-title:hover {
    text-decoration: underline;
}


.review-meta {
    display: flex;

    flex-wrap: wrap;

    gap: 5px;

    margin-top: 7px;

    font-size: 9px;

    color: #999999;
}


.review-button {
    flex: 0 0 auto;

    padding: 7px 12px;

    background: #caff00;

    color: #111111;

    font-size: 9px;

    font-weight: 800;

    text-decoration: none;

    border-radius: 3px;
}


.review-button:hover {
    background: #bce900;
}


/* =============================================================
   RECENT LIST
============================================================= */

.recent-list {
    width: 100%;
}


.recent-item {
    min-height: 75px;

    padding: 15px 18px;

    box-sizing: border-box;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 10px;

    border-bottom: 1px solid #eeeeee;
}


.recent-item:last-child {
    border-bottom: none;
}


.recent-content {
    min-width: 0;
}


.recent-title {
    display: block;

    font-family: Georgia, serif;

    font-size: 11px;

    font-weight: 700;

    line-height: 1.4;

    color: #171717;

    text-decoration: none;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


.recent-title:hover {
    text-decoration: underline;
}


.recent-meta {
    display: flex;

    gap: 5px;

    margin-top: 6px;

    font-size: 8px;

    color: #999999;
}


/* =============================================================
   STATUS BADGE
============================================================= */

.status-badge {
    flex: 0 0 auto;

    padding: 5px 8px;

    font-family: Georgia, serif;

    font-size: 8px;

    font-weight: 700;

    white-space: nowrap;
}


.status-draft {
    background: #eeeeee;

    color: #555555;
}


.status-review {
    background: #fff4d6;

    color: #806100;
}


.status-revisi {
    background: #fff0d9;

    color: #8b5700;
}


.status-ditolak {
    background: #f7dddd;

    color: #8a2222;
}


.status-published {
    background: #e8f7bd;

    color: #466500;
}


.status-default {
    background: #eeeeee;

    color: #555555;
}


/* =============================================================
   STATUS OVERVIEW
============================================================= */

.status-overview {
    margin-bottom: 20px;
}


.status-control-grid {
    display: grid;

    grid-template-columns:
        repeat(5, minmax(0, 1fr));

    gap: 10px;

    padding: 18px 20px 20px;
}


.status-control {
    min-height: 66px;

    padding: 12px 13px;

    box-sizing: border-box;

    display: flex;

    align-items: center;

    gap: 10px;

    border: 1px solid #e1e1e1;

    text-decoration: none;

    color: #111111;

    transition:
        border-color 0.18s ease,
        background 0.18s ease;
}


.status-control:hover {
    border-color: #caff00;

    background: #fbfff1;
}


.status-control div {
    min-width: 0;

    flex: 1;
}


.status-control strong {
    display: block;

    font-size: 9px;

    font-weight: 800;

    white-space: nowrap;
}


.status-control small {
    display: block;

    margin-top: 5px;

    font-size: 9px;

    color: #999999;
}


.control-arrow {
    font-size: 14px;

    color: #777777;
}


/* =============================================================
   STATUS DOT
============================================================= */

.status-dot {
    width: 9px;
    height: 9px;

    flex: 0 0 9px;

    border-radius: 50%;
}


.dot-draft {
    background: #999999;
}


.dot-review {
    background: #d9a500;
}


.dot-revisi {
    background: #e18c00;
}


.dot-ditolak {
    background: #b33131;
}


.dot-published {
    background: #7ca600;
}


/* =============================================================
   RESPONSIVE
============================================================= */

@media (max-width: 1200px) {

    .stats-grid {
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }


    .status-control-grid {
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }

}


@media (max-width: 900px) {

    .admin-main {
        margin-left: 215px;
    }


    .admin-content {
        padding: 28px 25px 50px;
    }


    .dashboard-grid {
        grid-template-columns: 1fr;
    }


    .status-control-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

}


@media (max-width: 650px) {

    .stats-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }


    .status-control-grid {
        grid-template-columns: 1fr;
    }


    .dashboard-heading h1 {
        font-size: 23px;
    }

}

</style>