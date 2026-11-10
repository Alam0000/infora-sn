<?php

$user = $user ?? Auth::user();

$articles = $articles ?? [];

$stats = $stats ?? [];

$keyword = $keyword ?? '';

$status = $status ?? '';

$baseUrl = Auth::baseUrl();


/*
|--------------------------------------------------------------------------
| STATUS LABEL
|--------------------------------------------------------------------------
*/

function articleStatusLabel($status)
{
    $labels = [
        'DRAFT'           => 'DRAFT',
        'MENUNGGU_REVIEW' => 'MENUNGGU REVIEW',
        'REVISI'          => 'BUTUH REVISI',
        'DITOLAK'         => 'DITOLAK',
        'PUBLISHED'       => 'PUBLISHED'
    ];

    return $labels[$status] ?? $status;
}


/*
|--------------------------------------------------------------------------
| STATUS CLASS
|--------------------------------------------------------------------------
*/

function articleStatusClass($status)
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
| FORMAT DATE
|--------------------------------------------------------------------------
*/

function articleDate($date)
{
    if (!$date) {
        return '-';
    }

    $time = strtotime($date);

    if (!$time) {
        return '-';
    }

    return date('d M Y H:i', $time);
}


/*
|--------------------------------------------------------------------------
| FLASH MESSAGE
|--------------------------------------------------------------------------
*/

$successMessage = '';

$errorMessage = '';

$success = $_GET['success'] ?? '';

$error = $_GET['error'] ?? '';


if ($success !== '') {
    $successMessage = htmlspecialchars($success);
}


$errorMessages = [
    'status_invalid' => 'Status artikel tidak valid.',
    'note_required'  => 'Catatan wajib diisi untuk status Revisi atau Ditolak.',
    'update_failed'  => 'Gagal mengubah status artikel.',
    'delete_failed'  => 'Gagal menghapus artikel.'
];


if (
    $error !== ''
    && isset($errorMessages[$error])
) {
    $errorMessage = $errorMessages[$error];
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

        <div class="page-heading">

            <div>

                <div class="breadcrumb">
                    DASHBOARD REDAKSI / ARTIKEL
                </div>
                <a href="<?= Auth::baseUrl() ?>/admin/articles/create"
                class="admin-add-article-button" >
                + Tambah Artikel
            </a>

                <h1>
                    Daftar Semua Artikel
                </h1>

                <p>
                    Kelola seluruh naskah, status, review, dan publikasi artikel.
                </p>

            </div>

        </div>



        <!-- =====================================================
             ALERT
        ====================================================== -->

        <?php if ($successMessage !== ''): ?>

            <div class="alert alert-success">
                <span class="alert-icon">✓</span>

                <span>
                    <?= $successMessage ?>
                </span>
            </div>

        <?php endif; ?>


        <?php if ($errorMessage !== ''): ?>

            <div class="alert alert-error">
                <span class="alert-icon">!</span>

                <span>
                    <?= htmlspecialchars($errorMessage) ?>
                </span>
            </div>

        <?php endif; ?>



        <!-- =====================================================
             MINI STATISTICS
        ====================================================== -->

        <div class="mini-stats">


            <a
                href="<?= $baseUrl ?>/admin/articles"
                class="mini-stat <?= $status === '' ? 'selected' : '' ?>"
            >

                <span class="mini-label">
                    SEMUA
                </span>

                <strong>
                    <?= (int) ($stats['total_articles'] ?? count($articles)) ?>
                </strong>

            </a>


            <a
                href="<?= $baseUrl ?>/admin/articles?status=DRAFT"
                class="mini-stat <?= $status === 'DRAFT' ? 'selected' : '' ?>"
            >

                <span class="mini-label">
                    DRAFT
                </span>

                <strong>
                    <?= (int) ($stats['draft'] ?? 0) ?>
                </strong>

            </a>


            <a
                href="<?= $baseUrl ?>/admin/articles?status=MENUNGGU_REVIEW"
                class="mini-stat <?= $status === 'MENUNGGU_REVIEW' ? 'selected' : '' ?>"
            >

                <span class="mini-label">
                    REVIEW
                </span>

                <strong>
                    <?= (int) ($stats['waiting_review'] ?? 0) ?>
                </strong>

            </a>


            <a
                href="<?= $baseUrl ?>/admin/articles?status=REVISI"
                class="mini-stat <?= $status === 'REVISI' ? 'selected' : '' ?>"
            >

                <span class="mini-label">
                    REVISI
                </span>

                <strong>
                    <?= (int) ($stats['revision'] ?? 0) ?>
                </strong>

            </a>


            <a
                href="<?= $baseUrl ?>/admin/articles?status=PUBLISHED"
                class="mini-stat <?= $status === 'PUBLISHED' ? 'selected' : '' ?>"
            >

                <span class="mini-label">
                    PUBLISHED
                </span>

                <strong>
                    <?= (int) ($stats['published'] ?? 0) ?>
                </strong>

            </a>


            <a
                href="<?= $baseUrl ?>/admin/articles?status=DITOLAK"
                class="mini-stat <?= $status === 'DITOLAK' ? 'selected' : '' ?>"
            >

                <span class="mini-label">
                    DITOLAK
                </span>

                <strong>
                    <?= (int) ($stats['rejected'] ?? 0) ?>
                </strong>

            </a>

        </div>



        <!-- =====================================================
             FILTER
        ====================================================== -->

        <section class="filter-panel">

            <form
                action="<?= $baseUrl ?>/admin/articles"
                method="GET"
                class="filter-form"
            >

                <div class="search-box">

                    <span class="search-icon">
                        ⌕
                    </span>

                    <input
                        type="text"
                        name="q"
                        value="<?= htmlspecialchars($keyword) ?>"
                        placeholder="Cari judul, penulis, atau kategori..."
                    >

                </div>


                <div class="status-filter">

                    <select name="status">

                        <option value="">
                            Semua Status
                        </option>

                        <option
                            value="DRAFT"
                            <?= $status === 'DRAFT' ? 'selected' : '' ?>
                        >
                            DRAFT
                        </option>

                        <option
                            value="MENUNGGU_REVIEW"
                            <?= $status === 'MENUNGGU_REVIEW' ? 'selected' : '' ?>
                        >
                            MENUNGGU REVIEW
                        </option>

                        <option
                            value="REVISI"
                            <?= $status === 'REVISI' ? 'selected' : '' ?>
                        >
                            REVISI
                        </option>

                        <option
                            value="DITOLAK"
                            <?= $status === 'DITOLAK' ? 'selected' : '' ?>
                        >
                            DITOLAK
                        </option>

                        <option
                            value="PUBLISHED"
                            <?= $status === 'PUBLISHED' ? 'selected' : '' ?>
                        >
                            PUBLISHED
                        </option>

                    </select>

                </div>


                <button
                    type="submit"
                    class="filter-button"
                >
                    Filter
                </button>


                <?php if ($keyword !== '' || $status !== ''): ?>

                    <a
                        href="<?= $baseUrl ?>/admin/articles"
                        class="reset-button"
                    >
                        Reset
                    </a>

                <?php endif; ?>

            </form>

        </section>



        <!-- =====================================================
             ARTICLE TABLE
        ====================================================== -->

        <section class="article-panel">

            <div class="article-panel-header">

                <div>

                    <h2>
                        Inventaris Artikel
                    </h2>

                    <p>
                        <?= count($articles) ?> artikel ditemukan.
                    </p>

                </div>

            </div>


            <?php if (empty($articles)): ?>

                <div class="empty-articles">

                    <div class="empty-symbol">
                        —
                    </div>

                    <h3>
                        Tidak ada artikel
                    </h3>

                    <p>
                        Belum ada artikel yang sesuai dengan filter.
                    </p>

                </div>

            <?php else: ?>


                <div class="table-wrapper">

                    <table class="article-table">

                        <thead>

                            <tr>

                                <th>
                                    ARTIKEL
                                </th>

                                <th>
                                    PENULIS
                                </th>

                                <th>
                                    KATEGORI
                                </th>

                                <th>
                                    STATUS
                                </th>

                                <th>
                                    DIBUAT
                                </th>

                                <th>
                                    AKSI
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($articles as $article): ?>

                                <?php

                                $articleId = (int) ($article['id'] ?? 0);

                                $articleStatus = $article['status'] ?? '';

                                ?>


                                <tr>


                                    <!-- ARTICLE -->

                                    <td class="article-cell">

                                        <a
                                            href="<?= $baseUrl ?>/admin/article/<?= $articleId ?>/review"
                                            class="article-title"
                                        >
                                            <?= htmlspecialchars(
                                                $article['title'] ?? '-'
                                            ) ?>
                                        </a>

                                        <?php if (!empty($article['excerpt'])): ?>

                                            <div class="article-excerpt">
                                                <?= htmlspecialchars(
                                                    mb_strimwidth(
                                                        strip_tags(
                                                            $article['excerpt']
                                                        ),
                                                        0,
                                                        100,
                                                        '...'
                                                    )
                                                ) ?>
                                            </div>

                                        <?php endif; ?>

                                    </td>


                                    <!-- AUTHOR -->

                                    <td>

                                        <div class="author-name">
                                            <?= htmlspecialchars(
                                                $article['author_name'] ?? '-'
                                            ) ?>
                                        </div>

                                    </td>


                                    <!-- CATEGORY -->

                                    <td>

                                        <span class="category-name">
                                            <?= htmlspecialchars(
                                                $article['category_name'] ?? '-'
                                            ) ?>
                                        </span>

                                    </td>


                                    <!-- STATUS -->

                                    <td>

                                        <span
                                            class="status-badge <?= articleStatusClass(
                                                $articleStatus
                                            ) ?>"
                                        >
                                            <?= articleStatusLabel(
                                                $articleStatus
                                            ) ?>
                                        </span>

                                    </td>


                                    <!-- DATE -->

                                    <td>

                                        <span class="article-date">
                                            <?= articleDate(
                                                $article['created_at'] ?? null
                                            ) ?>
                                        </span>

                                    </td>


                                    <!-- ACTION -->

                                    <td class="action-cell">

                                        <div class="action-buttons">


                                            <!-- REVIEW -->

                                            <a
                                                href="<?= $baseUrl ?>/admin/article/<?= $articleId ?>/review"
                                                class="action-button review-action"
                                            >
                                                Review
                                            </a>


                                            <!-- EDIT -->

                                            <a
                                                href="<?= $baseUrl ?>/admin/article/<?= $articleId ?>/edit"
                                                class="action-button edit-action"
                                            >
                                                Edit
                                            </a>


                                            <!-- STATUS -->

                                            <button
                                                type="button"
                                                class="action-button status-action"
                                                onclick="openStatusModal(
                                                    <?= $articleId ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $article['title'] ?? '-'
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $articleStatus
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                )"
                                            >
                                                Status
                                            </button>


                                            <!-- DELETE -->

                                            <button
                                                type="button"
                                                class="action-button delete-action"
                                                onclick="confirmDelete(
                                                    <?= $articleId ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $article['title'] ?? '-'
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                )"
                                            >
                                                Hapus
                                            </button>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


            <?php endif; ?>

        </section>


    </main>

</div>



<!-- =============================================================
     STATUS MODAL
============================================================= -->

<div
    id="statusModal"
    class="modal-overlay"
    onclick="closeStatusModal(event)"
>

    <div
        class="status-modal"
        onclick="event.stopPropagation()"
    >

        <div class="modal-header">

            <div>

                <div class="modal-kicker">
                    KONTROL ARTIKEL
                </div>

                <h3>
                    Ubah Status
                </h3>

            </div>

            <button
                type="button"
                class="modal-close"
                onclick="closeStatusModal()"
            >
                ×
            </button>

        </div>


        <div class="modal-body">

            <div class="selected-article">

                <span>
                    Artikel
                </span>

                <strong id="modalArticleTitle">
                    -
                </strong>

            </div>


            <form
                id="statusForm"
                method="POST"
            >

                <label>
                    STATUS BARU
                </label>

                <select
                    name="status"
                    id="modalStatus"
                    onchange="toggleNote()"
                >

                    <option value="DRAFT">
                        DRAFT
                    </option>

                    <option value="MENUNGGU_REVIEW">
                        MENUNGGU REVIEW
                    </option>

                    <option value="REVISI">
                        REVISI
                    </option>

                    <option value="DITOLAK">
                        DITOLAK
                    </option>

                    <option value="PUBLISHED">
                        PUBLISHED
                    </option>

                </select>


                <div
                    id="noteWrapper"
                    class="note-wrapper"
                >

                    <label>
                        CATATAN
                    </label>

                    <textarea
                        name="note"
                        id="modalNote"
                        rows="4"
                        placeholder="Masukkan catatan untuk penulis..."
                    ></textarea>

                    <small>
                        Catatan wajib diisi untuk status REVISI atau DITOLAK.
                    </small>

                </div>


                <div class="modal-actions">

                    <button
                        type="button"
                        class="cancel-button"
                        onclick="closeStatusModal()"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="save-status-button"
                    >
                        Simpan Status
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>



<!-- =============================================================
     DELETE FORM
============================================================= -->

<form
    id="deleteForm"
    method="POST"
    style="display:none;"
>
</form>



<style>

/* =============================================================
   MAIN
============================================================= */

.admin-main {
    margin-left: 245px;

    min-height: 100vh;

    background: #f4f5f7;
}


.admin-content {
    padding: 36px 42px 60px;

    box-sizing: border-box;
}

.admin-add-article-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 0 16px;
    border-radius: 10px;
    background: #111827;
    color: #fff;
    text-decoration: none;
    font-weight: 800;
}
/* =============================================================
   PAGE HEADING
============================================================= */

.page-heading {
    margin-bottom: 25px;
}


.breadcrumb {
    margin-bottom: 8px;

    font-size: 9px;

    font-weight: 700;

    letter-spacing: 1.4px;

    color: #999999;
}


.page-heading h1 {
    margin: 0;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 27px;

    font-weight: 800;

    letter-spacing: -0.7px;

    color: #111111;
}


.page-heading p {
    margin: 7px 0 0;

    font-size: 12px;

    color: #777777;
}


/* =============================================================
   ALERT
============================================================= */

.alert {
    display: flex;

    align-items: center;

    gap: 10px;

    padding: 13px 16px;

    margin-bottom: 18px;

    font-size: 11px;

    border: 1px solid;
}


.alert-success {
    background: #f5ffe1;

    border-color: #d8ee9a;

    color: #4c6500;
}


.alert-error {
    background: #fff0f0;

    border-color: #efc2c2;

    color: #8a2222;
}


.alert-icon {
    width: 20px;
    height: 20px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #ffffff;

    font-weight: 800;
}


/* =============================================================
   MINI STATS
============================================================= */

.mini-stats {
    display: grid;

    grid-template-columns:
        repeat(6, minmax(0, 1fr));

    gap: 10px;

    margin-bottom: 18px;
}


.mini-stat {
    min-height: 78px;

    padding: 14px 15px;

    box-sizing: border-box;

    display: flex;

    flex-direction: column;

    justify-content: space-between;

    text-decoration: none;

    background: #ffffff;

    border: 1px solid #dddddd;

    transition:
        border-color 0.18s ease,
        transform 0.18s ease;
}


.mini-stat:hover {
    border-color: #caff00;

    transform: translateY(-1px);
}


.mini-stat.selected {
    border-color: #b5d900;

    background: #fbfff0;
}


.mini-label {
    font-size: 8px;

    font-weight: 700;

    letter-spacing: 0.8px;

    color: #888888;
}


.mini-stat strong {
    font-family: Georgia, serif;

    font-size: 22px;

    color: #111111;
}


/* =============================================================
   FILTER
============================================================= */

.filter-panel {
    margin-bottom: 18px;

    padding: 14px;

    background: #ffffff;

    border: 1px solid #dddddd;
}


.filter-form {
    display: flex;

    align-items: center;

    gap: 9px;
}


.search-box {
    position: relative;

    flex: 1;
}


.search-icon {
    position: absolute;

    left: 12px;
    top: 50%;

    transform: translateY(-50%);

    font-size: 18px;

    color: #999999;
}


.search-box input {
    width: 100%;

    height: 40px;

    padding: 0 13px 0 37px;

    box-sizing: border-box;

    border: 1px solid #d8d8d8;

    outline: none;

    font-size: 11px;

    color: #222222;
}


.search-box input:focus {
    border-color: #b8d900;
}


.status-filter select {
    height: 40px;

    min-width: 180px;

    padding: 0 12px;

    border: 1px solid #d8d8d8;

    background: #ffffff;

    outline: none;

    font-size: 10px;

    color: #333333;
}


.filter-button {
    height: 40px;

    padding: 0 18px;

    border: none;

    background: #111111;

    color: #ffffff;

    font-size: 10px;

    font-weight: 700;

    cursor: pointer;
}


.filter-button:hover {
    background: #333333;
}


.reset-button {
    height: 40px;

    padding: 0 14px;

    display: flex;

    align-items: center;

    box-sizing: border-box;

    border: 1px solid #d8d8d8;

    color: #555555;

    text-decoration: none;

    font-size: 10px;
}


.reset-button:hover {
    background: #f4f4f4;
}


/* =============================================================
   ARTICLE PANEL
============================================================= */

.article-panel {
    background: #ffffff;

    border: 1px solid #dddddd;
}


.article-panel-header {
    min-height: 72px;

    padding: 18px 20px;

    box-sizing: border-box;

    display: flex;

    align-items: center;

    border-bottom: 1px solid #e3e3e3;
}


.article-panel-header h2 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 15px;

    color: #111111;
}


.article-panel-header p {
    margin: 5px 0 0;

    font-size: 9px;

    color: #999999;
}


/* =============================================================
   TABLE
============================================================= */

.table-wrapper {
    width: 100%;

    overflow-x: auto;
}


.article-table {
    width: 100%;

    min-width: 1050px;

    border-collapse: collapse;
}


.article-table th {
    padding: 12px 14px;

    text-align: left;

    background: #fafafa;

    border-bottom: 1px solid #dddddd;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 0.8px;

    color: #888888;
}


.article-table td {
    padding: 15px 14px;

    vertical-align: middle;

    border-bottom: 1px solid #eeeeee;
}


.article-table tbody tr:hover {
    background: #fcfcfc;
}


.article-table tbody tr:last-child td {
    border-bottom: none;
}


/* =============================================================
   ARTICLE
============================================================= */

.article-cell {
    width: 30%;

    max-width: 350px;
}


.article-title {
    display: block;

    font-family: Georgia, serif;

    font-size: 12px;

    font-weight: 700;

    line-height: 1.45;

    color: #151515;

    text-decoration: none;
}


.article-title:hover {
    text-decoration: underline;
}


.article-excerpt {
    margin-top: 5px;

    font-size: 9px;

    line-height: 1.4;

    color: #999999;
}


.author-name {
    font-size: 10px;

    font-weight: 600;

    color: #333333;
}


.category-name {
    font-size: 10px;

    color: #777777;
}


.article-date {
    font-size: 9px;

    color: #888888;

    white-space: nowrap;
}


/* =============================================================
   STATUS
============================================================= */

.status-badge {
    display: inline-block;

    padding: 6px 8px;

    font-size: 8px;

    font-weight: 800;

    white-space: nowrap;
}


.status-draft {
    background: #eeeeee;

    color: #555555;
}


.status-review {
    background: #fff3cf;

    color: #826500;
}


.status-revisi {
    background: #fff0d8;

    color: #8a5700;
}


.status-ditolak {
    background: #f8dddd;

    color: #8c2525;
}


.status-published {
    background: #e7f5ba;

    color: #4d6800;
}


.status-default {
    background: #eeeeee;

    color: #555555;
}


/* =============================================================
   ACTION
============================================================= */

.action-cell {
    min-width: 260px;
}


.action-buttons {
    display: flex;

    flex-wrap: wrap;

    gap: 5px;
}


.action-button {
    height: 28px;

    padding: 0 9px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    box-sizing: border-box;

    border: 1px solid #d7d7d7;

    background: #ffffff;

    color: #444444;

    text-decoration: none;

    font-size: 8px;

    font-weight: 700;

    cursor: pointer;
}


.action-button:hover {
    background: #f4f4f4;
}


.review-action {
    background: #111111;

    border-color: #111111;

    color: #ffffff;
}


.review-action:hover {
    background: #333333;

    color: #ffffff;
}


.edit-action:hover {
    border-color: #b8d900;

    background: #f8ffdf;
}


.status-action:hover {
    border-color: #b8d900;

    background: #f8ffdf;
}


.delete-action {
    color: #9a2929;
}


.delete-action:hover {
    border-color: #e0a7a7;

    background: #fff0f0;
}


/* =============================================================
   EMPTY
============================================================= */

.empty-articles {
    min-height: 280px;

    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;

    text-align: center;
}


.empty-symbol {
    width: 42px;
    height: 42px;

    display: flex;

    align-items: center;
    justify-content: center;

    margin-bottom: 12px;

    border-radius: 50%;

    background: #f0f0f0;

    color: #888888;

    font-size: 18px;
}


.empty-articles h3 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 13px;

    color: #555555;
}


.empty-articles p {
    margin: 6px 0 0;

    font-size: 10px;

    color: #999999;
}


/* =============================================================
   MODAL
============================================================= */

.modal-overlay {
    position: fixed;

    inset: 0;

    z-index: 5000;

    display: none;

    align-items: center;
    justify-content: center;

    padding: 20px;

    box-sizing: border-box;

    background: rgba(0, 0, 0, 0.48);
}


.modal-overlay.show {
    display: flex;
}


.status-modal {
    width: 100%;

    max-width: 470px;

    background: #ffffff;

    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}


.modal-header {
    min-height: 76px;

    padding: 18px 20px;

    box-sizing: border-box;

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    border-bottom: 1px solid #e1e1e1;
}


.modal-kicker {
    margin-bottom: 5px;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 1.2px;

    color: #999999;
}


.modal-header h3 {
    margin: 0;

    font-family: Georgia, serif;

    font-size: 18px;

    color: #111111;
}


.modal-close {
    width: 30px;
    height: 30px;

    border: none;

    background: transparent;

    color: #777777;

    font-size: 24px;

    line-height: 1;

    cursor: pointer;
}


.modal-close:hover {
    color: #111111;
}


.modal-body {
    padding: 20px;
}


.selected-article {
    margin-bottom: 18px;

    padding: 12px 14px;

    background: #f6f6f6;

    border-left: 3px solid #caff00;
}


.selected-article span {
    display: block;

    margin-bottom: 5px;

    font-size: 8px;

    font-weight: 700;

    color: #999999;
}


.selected-article strong {
    display: block;

    font-family: Georgia, serif;

    font-size: 12px;

    line-height: 1.4;

    color: #222222;
}


.modal-body label {
    display: block;

    margin-bottom: 7px;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 0.8px;

    color: #777777;
}


.modal-body select,
.modal-body textarea {
    width: 100%;

    box-sizing: border-box;

    border: 1px solid #d7d7d7;

    outline: none;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 11px;

    color: #222222;
}


.modal-body select {
    height: 40px;

    padding: 0 11px;

    background: #ffffff;
}


.modal-body textarea {
    padding: 11px;

    resize: vertical;
}


.modal-body select:focus,
.modal-body textarea:focus {
    border-color: #b8d900;
}


.note-wrapper {
    display: none;

    margin-top: 17px;
}


.note-wrapper.show {
    display: block;
}


.note-wrapper small {
    display: block;

    margin-top: 6px;

    font-size: 9px;

    color: #999999;
}


.modal-actions {
    display: flex;

    justify-content: flex-end;

    gap: 8px;

    margin-top: 20px;
}


.cancel-button,
.save-status-button {
    height: 40px;

    padding: 0 16px;

    border: none;

    font-size: 10px;

    font-weight: 700;

    cursor: pointer;
}


.cancel-button {
    background: #eeeeee;

    color: #555555;
}


.save-status-button {
    background: #caff00;

    color: #111111;
}


.save-status-button:hover {
    background: #bce900;
}


/* =============================================================
   RESPONSIVE
============================================================= */

@media (max-width: 1200px) {

    .mini-stats {
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }

}


@media (max-width: 900px) {

    .admin-main {
        margin-left: 215px;
    }


    .admin-content {
        padding: 28px 24px 50px;
    }


    .filter-form {
        flex-wrap: wrap;
    }


    .search-box {
        flex-basis: 100%;
    }


    .status-filter {
        flex: 1;
    }


    .status-filter select {
        width: 100%;
    }

}


@media (max-width: 600px) {

    .mini-stats {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }


    .admin-content {
        padding: 22px 15px 40px;
    }

}

</style>



<script>

/* =============================================================
   STATUS MODAL
============================================================= */

function openStatusModal(articleId, title, currentStatus)
{
    const modal = document.getElementById('statusModal');

    const form = document.getElementById('statusForm');

    const titleElement =
        document.getElementById('modalArticleTitle');

    const statusSelect =
        document.getElementById('modalStatus');

    const note =
        document.getElementById('modalNote');


    titleElement.textContent = title;

    statusSelect.value = currentStatus;

    note.value = '';


    form.action =
        '<?= $baseUrl ?>/admin/article/'
        + articleId
        + '/status';


    toggleNote();

    modal.classList.add('show');

    document.body.style.overflow = 'hidden';
}


/* =============================================================
   CLOSE MODAL
============================================================= */

function closeStatusModal(event)
{
    if (
        event
        && event.target
        && event.target.id !== 'statusModal'
    ) {
        return;
    }

    const modal =
        document.getElementById('statusModal');

    modal.classList.remove('show');

    document.body.style.overflow = '';
}


/* =============================================================
   NOTE TOGGLE
============================================================= */

function toggleNote()
{
    const status =
        document.getElementById('modalStatus').value;

    const wrapper =
        document.getElementById('noteWrapper');

    const note =
        document.getElementById('modalNote');


    if (
        status === 'REVISI'
        || status === 'DITOLAK'
    ) {

        wrapper.classList.add('show');

        note.required = true;

    } else {

        wrapper.classList.remove('show');

        note.required = false;

        note.value = '';

    }
}


/* =============================================================
   DELETE
============================================================= */

function confirmDelete(articleId, title)
{
    const answer = confirm(
        'Hapus artikel "' +
        title +
        '"?\n\n' +
        'Artikel yang dihapus tidak dapat dikembalikan.'
    );


    if (!answer) {
        return;
    }


    const form =
        document.getElementById('deleteForm');


    form.action =
        '<?= $baseUrl ?>/admin/article/'
        + articleId
        + '/delete';


    form.submit();
}


/* =============================================================
   ESCAPE MODAL
============================================================= */

document.addEventListener(
    'keydown',
    function(event)
    {
        if (event.key === 'Escape') {
            closeStatusModal();
        }
    }
);

</script>