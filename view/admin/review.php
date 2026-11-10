<?php

$pageTitle = 'Review Artikel';

$pageDescription =
    'Periksa naskah sebelum diterbitkan di portal INFORA SN.';

$user = $user ?? Auth::user();

$article = $article ?? null;

$baseUrl = Auth::baseUrl();

if (!$article) {
    http_response_code(404);
    die('Artikel tidak ditemukan.');
}

$status =
    $article['status'] ?? 'DRAFT';

$statusLabel =
    str_replace(
        '_',
        ' ',
        $status
    );

$success =
    $_GET['success'] ?? '';

$error =
    $_GET['error'] ?? '';

$thumbnail =
    trim(
        (string) (
            $article['thumbnail_path'] ?? ''
        )
    );

/**
 * =========================================================
 * HELPER URL GAMBAR
 * =========================================================
 *
 * Database menyimpan:
 * public/uploads/articles/nama-file.jpg
 *
 * Browser membutuhkan:
 * /infora-sn/public/uploads/articles/nama-file.jpg
 *
 * $baseUrl berasal dari Auth::baseUrl().
 */
function adminReviewImageUrl($path, $baseUrl)
{
    $path = trim((string) $path);

    if ($path === '') {
        return '';
    }

    /**
     * Jika URL sudah berupa URL lengkap.
     */
    if (
        preg_match(
            '#^https?://#i',
            $path
        )
    ) {
        return $path;
    }

    /**
     * Normalisasi slash.
     */
    $path = str_replace(
        '\\',
        '/',
        $path
    );

    /**
     * Hilangkan slash di awal.
     */
    $path = ltrim(
        $path,
        '/'
    );

    /**
     * Jika ternyata path sudah mengandung
     * base URL, jangan menggandakannya.
     */
    $normalizedBase =
        trim(
            str_replace(
                '\\',
                '/',
                $baseUrl
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

    return
        rtrim(
            $baseUrl,
            '/'
        ) .
        '/' .
        $path;
}

$thumbnailUrl =
    adminReviewImageUrl(
        $thumbnail,
        $baseUrl
    );

?>

<?php include __DIR__ . '/layout/sidebar.php'; ?>

<?php include __DIR__ . '/layout/header.php'; ?>


<div class="admin-page-wrapper">

    <main class="admin-main-content">

        <!-- =====================================================
             BACK
        ====================================================== -->

        <a
            href="<?= htmlspecialchars($baseUrl) ?>/admin/articles"
            class="review-back"
        >
            ← Kembali ke Daftar Artikel
        </a>


        <!-- =====================================================
             ALERT
        ====================================================== -->

        <?php if ($success !== ''): ?>

            <div class="review-alert success">

                <?php if ($success === 'publish'): ?>

                    Artikel berhasil dipublikasikan.

                <?php elseif ($success === 'revision'): ?>

                    Artikel berhasil dikembalikan kepada Client
                    untuk direvisi.

                <?php elseif ($success === 'reject'): ?>

                    Artikel berhasil ditolak.

                <?php else: ?>

                    Proses berhasil dilakukan.

                <?php endif; ?>

            </div>

        <?php endif; ?>


        <?php if ($error !== ''): ?>

            <div class="review-alert error">

                <?php if ($error === 'note'): ?>

                    Catatan wajib diisi untuk Revisi atau Tolak.

                <?php elseif ($error === 'action'): ?>

                    Aksi review tidak valid.

                <?php else: ?>

                    Proses gagal dilakukan.

                <?php endif; ?>

            </div>

        <?php endif; ?>


        <!-- =====================================================
             REVIEW HEADER
        ====================================================== -->

        <section class="review-top">

            <div>

                <div class="review-kicker">
                    MEJA REVIEW &amp; KURASI
                </div>

                <h2>
                    Review Artikel
                </h2>

                <p>
                    Periksa seluruh informasi artikel sebelum
                    menentukan keputusan editorial.
                </p>

            </div>


            <span
                class="review-status status-<?= htmlspecialchars(
                    strtolower($status)
                ) ?>"
            >
                <?= htmlspecialchars($statusLabel) ?>
            </span>

        </section>


        <!-- =====================================================
             REVIEW LAYOUT
        ====================================================== -->

        <div class="review-layout">


            <!-- =================================================
                 ARTICLE
            ================================================== -->

            <article class="review-article">

                <div class="article-category">

                    <?= htmlspecialchars(
                        $article['category_name']
                        ?? 'Tanpa Kategori'
                    ) ?>

                </div>


                <h1 class="article-title">

                    <?= htmlspecialchars(
                        $article['title']
                        ?? '-'
                    ) ?>

                </h1>


                <div class="article-meta">

                    <span>

                        Oleh

                        <strong>

                            <?= htmlspecialchars(
                                $article['author_name']
                                ?? 'Tidak diketahui'
                            ) ?>

                        </strong>

                    </span>


                    <span>

                        <?= !empty($article['created_at'])
                            ? date(
                                'd M Y H:i',
                                strtotime(
                                    $article['created_at']
                                )
                            )
                            : '-'
                        ?>

                    </span>

                </div>


                <!-- =================================================
                     THUMBNAIL
                ================================================== -->

                <?php if ($thumbnailUrl !== ''): ?>

                    <div class="article-thumbnail">

                        <img
                            src="<?= htmlspecialchars(
                                $thumbnailUrl
                            ) ?>"
                            alt="<?= htmlspecialchars(
                                $article['title']
                                ?? 'Thumbnail'
                            ) ?>"
                            onerror="this.parentElement.classList.add('image-error');"
                        >

                        <div class="image-error-message">

                            Gambar thumbnail tidak dapat dimuat.

                        </div>

                    </div>

                <?php elseif (!empty($article['images'])): ?>

                    <?php

                    $firstImage =
                        $article['images'][0];

                    $firstImageUrl =
                        adminReviewImageUrl(
                            $firstImage['file_path']
                            ?? '',
                            $baseUrl
                        );

                    ?>

                    <?php if ($firstImageUrl !== ''): ?>

                        <div class="article-thumbnail">

                            <img
                                src="<?= htmlspecialchars(
                                    $firstImageUrl
                                ) ?>"
                                alt="<?= htmlspecialchars(
                                    $article['title']
                                    ?? 'Gambar'
                                ) ?>"
                                onerror="this.parentElement.classList.add('image-error');"
                            >

                            <div class="image-error-message">

                                Gambar tidak dapat dimuat.

                            </div>

                        </div>

                    <?php endif; ?>

                <?php endif; ?>


                <!-- =================================================
                     CONTENT
                ================================================== -->

                <div class="article-content">

                    <?= $article['content'] ?? '' ?>

                </div>


                <!-- =================================================
                     TAG
                ================================================== -->

                <?php if (!empty($article['tags'])): ?>

                    <div class="article-tags">

                        <div class="tags-title">
                            TAG
                        </div>

                        <div class="tags-list">

                            <?php foreach (
                                $article['tags']
                                as $tag
                            ): ?>

                                <span class="tag">

                                    #<?= htmlspecialchars(
                                        $tag['name']
                                    ) ?>

                                </span>

                            <?php endforeach; ?>

                        </div>

                    </div>

                <?php endif; ?>


            </article>


            <!-- =================================================
                 SIDEBAR REVIEW
            ================================================== -->

            <aside class="review-panel">


                <!-- INFO ARTIKEL -->

                <div class="review-box">

                    <div class="review-box-title">
                        INFORMASI ARTIKEL
                    </div>


                    <div class="info-row">

                        <span>
                            Penulis
                        </span>

                        <strong>

                            <?= htmlspecialchars(
                                $article['author_name']
                                ?? '-'
                            ) ?>

                        </strong>

                    </div>


                    <div class="info-row">

                        <span>
                            Email
                        </span>

                        <strong>

                            <?= htmlspecialchars(
                                $article['author_email']
                                ?? '-'
                            ) ?>

                        </strong>

                    </div>


                    <div class="info-row">

                        <span>
                            Kategori
                        </span>

                        <strong>

                            <?= htmlspecialchars(
                                $article['category_name']
                                ?? '-'
                            ) ?>

                        </strong>

                    </div>


                    <div class="info-row">

                        <span>
                            Status
                        </span>

                        <strong>

                            <?= htmlspecialchars(
                                $statusLabel
                            ) ?>

                        </strong>

                    </div>


                    <?php if (
                        !empty(
                            $article['published_at']
                        )
                    ): ?>

                        <div class="info-row">

                            <span>
                                Published
                            </span>

                            <strong>

                                <?= date(
                                    'd M Y H:i',
                                    strtotime(
                                        $article['published_at']
                                    )
                                ) ?>

                            </strong>

                        </div>

                    <?php endif; ?>


                </div>


                <!-- =================================================
                     ACTION
                ================================================== -->

                <div class="review-box">

                    <div class="review-box-title">
                        KEPUTUSAN REDAKSI
                    </div>


                    <form
                        method="POST"
                        action="<?= htmlspecialchars(
                            $baseUrl
                        ) ?>/admin/article/<?= (int) $article['id'] ?>/review"
                        id="reviewForm"
                    >

                        <div class="note-group">

                            <label for="note">
                                Catatan / Alasan
                            </label>

                            <textarea
                                name="note"
                                id="note"
                                rows="6"
                                placeholder="Isi catatan jika meminta revisi atau menolak artikel..."
                            ></textarea>

                            <small>
                                Wajib diisi untuk Revisi dan Tolak.
                            </small>

                        </div>


                        <button
                            type="submit"
                            name="action"
                            value="publish"
                            class="decision-button publish"
                            onclick="return confirm('Publikasikan artikel ini?');"
                        >
                            ✓ Publish Artikel
                        </button>


                        <button
                            type="submit"
                            name="action"
                            value="revision"
                            class="decision-button revision"
                            onclick="return validateRevision();"
                        >
                            ↻ Request Revisi
                        </button>


                        <button
                            type="submit"
                            name="action"
                            value="reject"
                            class="decision-button reject"
                            onclick="return validateReject();"
                        >
                            × Tolak Artikel
                        </button>

                    </form>

                </div>


                <!-- =================================================
                     REVISION / REJECTION INFORMATION
                ================================================== -->

                <?php if (
                    !empty(
                        $article['revision_note']
                    )
                ): ?>

                    <div class="review-box note-box">

                        <div class="review-box-title">
                            CATATAN REVISI TERAKHIR
                        </div>

                        <p>

                            <?= nl2br(
                                htmlspecialchars(
                                    $article['revision_note']
                                )
                            ) ?>

                        </p>

                    </div>

                <?php endif; ?>


                <?php if (
                    !empty(
                        $article['rejection_reason']
                    )
                ): ?>

                    <div class="review-box note-box">

                        <div class="review-box-title">
                            ALASAN PENOLAKAN
                        </div>

                        <p>

                            <?= nl2br(
                                htmlspecialchars(
                                    $article['rejection_reason']
                                )
                            ) ?>

                        </p>

                    </div>

                <?php endif; ?>


            </aside>


        </div>


    </main>


</div>


<?php include __DIR__ . '/layout/footer.php'; ?>


<style>

/* =====================================================
   BACK
===================================================== */

.review-back {

    display: inline-block;

    margin-bottom: 22px;

    color: #555555;

    text-decoration: none;

    font-size: 10px;

    font-weight: 700;
}


.review-back:hover {

    color: #111111;

}


/* =====================================================
   ALERT
===================================================== */

.review-alert {

    padding: 13px 16px;

    margin-bottom: 20px;

    border: 1px solid;

    font-size: 11px;

    font-weight: 700;
}


.review-alert.success {

    background: #eef8dc;

    border-color: #c7df8c;

    color: #426500;
}


.review-alert.error {

    background: #fff0f0;

    border-color: #edc4c4;

    color: #8b2222;
}


/* =====================================================
   HEADER
===================================================== */

.review-top {

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 25px;
}


.review-kicker {

    margin-bottom: 7px;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: 1.5px;

    color: #777777;
}


.review-top h2 {

    margin: 0;

    font-family: Georgia, serif;

    font-size: 28px;

    color: #111111;
}


.review-top p {

    margin: 8px 0 0;

    font-size: 11px;

    color: #777777;
}


/* =====================================================
   STATUS
===================================================== */

.review-status {

    display: inline-block;

    padding: 8px 12px;

    font-size: 9px;

    font-weight: 800;

    background: #eeeeee;

    color: #666666;
}


.status-published {

    background: #e5f5c7;

    color: #3c6000;
}


.status-menunggu_review {

    background: #fff0c8;

    color: #795700;
}


.status-revisi {

    background: #ffe4c4;

    color: #814800;
}


.status-ditolak {

    background: #f8dddd;

    color: #8b2222;
}


.status-draft {

    background: #eeeeee;

    color: #666666;
}


/* =====================================================
   LAYOUT
===================================================== */

.review-layout {

    display: grid;

    grid-template-columns:
        minmax(0, 1.7fr)
        minmax(300px, 0.8fr);

    gap: 22px;

    align-items: start;
}


/* =====================================================
   ARTICLE
===================================================== */

.review-article {

    padding: 32px;

    background: #ffffff;

    border: 1px solid #d9d9d9;
}


.article-category {

    display: inline-block;

    margin-bottom: 12px;

    padding: 6px 9px;

    background: #caff00;

    color: #111111;

    font-size: 9px;

    font-weight: 800;

    text-transform: uppercase;
}


.article-title {

    margin: 0;

    font-family: Georgia, serif;

    font-size: 34px;

    line-height: 1.15;

    color: #111111;
}


.article-meta {

    display: flex;

    gap: 20px;

    margin-top: 15px;

    padding-bottom: 20px;

    border-bottom: 1px solid #eeeeee;

    font-size: 10px;

    color: #888888;
}


.article-meta strong {

    color: #333333;
}


/* =====================================================
   IMAGE
===================================================== */

.article-thumbnail {

    position: relative;

    width: 100%;

    margin: 25px 0;

    background: #eeeeee;

    overflow: hidden;
}


.article-thumbnail img {

    display: block;

    width: 100%;

    max-height: 500px;

    min-height: 220px;

    object-fit: cover;
}


/* =====================================================
   IMAGE ERROR
===================================================== */

.image-error-message {

    display: none;

    padding: 35px 20px;

    text-align: center;

    font-size: 11px;

    color: #888888;

}


.article-thumbnail.image-error img {

    display: none;

}


.article-thumbnail.image-error
.image-error-message {

    display: block;

}


/* =====================================================
   CONTENT
===================================================== */

.article-content {

    font-family: Georgia, serif;

    font-size: 16px;

    line-height: 1.8;

    color: #333333;
}


.article-content img {

    max-width: 100%;

    height: auto;
}


.article-content p {

    margin: 0 0 18px;
}


.article-content h1,
.article-content h2,
.article-content h3 {

    color: #111111;
}


/* =====================================================
   TAGS
===================================================== */

.article-tags {

    margin-top: 30px;

    padding-top: 20px;

    border-top: 1px solid #eeeeee;
}


.tags-title {

    margin-bottom: 10px;

    font-size: 9px;

    font-weight: 800;

    color: #777777;
}


.tags-list {

    display: flex;

    flex-wrap: wrap;

    gap: 7px;
}


.tag {

    padding: 6px 9px;

    background: #f1f1f1;

    font-size: 9px;

    color: #555555;
}


/* =====================================================
   REVIEW PANEL
===================================================== */

.review-panel {

    display: flex;

    flex-direction: column;

    gap: 18px;
}


.review-box {

    padding: 20px;

    background: #ffffff;

    border: 1px solid #d9d9d9;
}


.review-box-title {

    margin-bottom: 17px;

    padding-bottom: 12px;

    border-bottom: 1px solid #eeeeee;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: 0.8px;

    color: #777777;
}


/* =====================================================
   INFO
===================================================== */

.info-row {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 15px;

    padding: 11px 0;

    border-bottom: 1px solid #f0f0f0;
}


.info-row:last-child {

    border-bottom: none;
}


.info-row span {

    font-size: 10px;

    color: #888888;
}


.info-row strong {

    max-width: 65%;

    text-align: right;

    font-size: 10px;

    color: #333333;

    word-break: break-word;
}


/* =====================================================
   NOTE
===================================================== */

.note-group {

    margin-bottom: 15px;
}


.note-group label {

    display: block;

    margin-bottom: 7px;

    font-size: 10px;

    font-weight: 800;

    color: #333333;
}


.note-group textarea {

    width: 100%;

    min-height: 115px;

    padding: 11px;

    box-sizing: border-box;

    resize: vertical;

    border: 1px solid #d5d5d5;

    outline: none;

    font-family: Arial, sans-serif;

    font-size: 10px;

    line-height: 1.5;
}


.note-group textarea:focus {

    border-color: #111111;
}


.note-group small {

    display: block;

    margin-top: 6px;

    font-size: 8px;

    color: #999999;
}


/* =====================================================
   DECISION BUTTON
===================================================== */

.decision-button {

    width: 100%;

    height: 42px;

    margin-top: 8px;

    border: none;

    cursor: pointer;

    font-size: 10px;

    font-weight: 800;
}


.decision-button.publish {

    background: #caff00;

    color: #111111;
}


.decision-button.publish:hover {

    background: #b9ed00;
}


.decision-button.revision {

    background: #111111;

    color: #ffffff;
}


.decision-button.revision:hover {

    background: #292929;
}


.decision-button.reject {

    background: #eeeeee;

    color: #8b2222;
}


.decision-button.reject:hover {

    background: #f8dddd;
}


/* =====================================================
   NOTE BOX
===================================================== */

.note-box {

    background: #fafafa;
}


.note-box p {

    margin: 0;

    font-size: 10px;

    line-height: 1.6;

    color: #555555;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 950px) {

    .review-layout {

        grid-template-columns: 1fr;

    }

}


@media (max-width: 600px) {

    .review-article {

        padding: 20px;

    }


    .article-title {

        font-size: 25px;

    }


    .article-meta {

        flex-direction: column;

        gap: 7px;

    }


    .review-top {

        align-items: flex-start;

        flex-direction: column;

    }

}

</style>


<script>

function validateRevision()
{
    const note =
        document
            .getElementById('note')
            .value
            .trim();

    if (note === '') {

        alert(
            'Catatan revisi wajib diisi.'
        );

        document
            .getElementById('note')
            .focus();

        return false;
    }

    return confirm(
        'Kembalikan artikel ini kepada Client untuk direvisi?'
    );
}


function validateReject()
{
    const note =
        document
            .getElementById('note')
            .value
            .trim();

    if (note === '') {

        alert(
            'Alasan penolakan wajib diisi.'
        );

        document
            .getElementById('note')
            .focus();

        return false;
    }

    return confirm(
        'Apakah Anda yakin ingin menolak artikel ini?'
    );
}

</script>