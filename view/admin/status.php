<?php

$user = $user ?? Auth::user();

$article = $article ?? [];

$baseUrl = Auth::baseUrl();

$error = $_GET['error'] ?? '';

?>

<?php include __DIR__ . '/layout/sidebar.php'; ?>


<div class="admin-main">

    <?php

    $pageTitle = 'Ubah Status Artikel';

    $pageDescription =
        'Kelola status dan proses publikasi artikel.';

    ?>

    <?php include __DIR__ . '/layout/header.php'; ?>


    <main class="admin-content">


        <!-- =====================================================
             HEADER
        ====================================================== -->

        <div class="page-heading">

            <div>

                <div class="breadcrumb">
                    DASHBOARD REDAKSI / ARTIKEL / STATUS
                </div>

                <h1>
                    Ubah Status Artikel
                </h1>

                <p>
                    Admin dapat mengubah status artikel
                    sesuai proses editorial.
                </p>

            </div>


            <a
                href="<?= $baseUrl ?>/admin/articles"
                class="back-button"
            >
                ← Kembali
            </a>

        </div>



        <!-- =====================================================
             ERROR
        ====================================================== -->

        <?php if ($error === 'note_required'): ?>

            <div class="error-box">
                Catatan wajib diisi untuk status
                REVISI atau DITOLAK.
            </div>

        <?php elseif ($error === 'status_invalid'): ?>

            <div class="error-box">
                Status artikel tidak valid.
            </div>

        <?php elseif ($error === 'update_failed'): ?>

            <div class="error-box">
                Gagal memperbarui status artikel.
            </div>

        <?php endif; ?>



        <!-- =====================================================
             ARTICLE INFO
        ====================================================== -->

        <section class="article-info-card">

            <div class="article-info-main">

                <div class="article-label">
                    ARTIKEL
                </div>

                <h2>
                    <?= htmlspecialchars(
                        $article['title'] ?? '-'
                    ) ?>
                </h2>

                <div class="article-meta">

                    <span>
                        Penulis:
                        <strong>
                            <?= htmlspecialchars(
                                $article['author_name'] ?? '-'
                            ) ?>
                        </strong>
                    </span>

                    <span>
                        Kategori:
                        <strong>
                            <?= htmlspecialchars(
                                $article['category_name'] ?? '-'
                            ) ?>
                        </strong>
                    </span>

                </div>

            </div>


            <div class="current-status-box">

                <div>
                    STATUS SAAT INI
                </div>

                <span>
                    <?= htmlspecialchars(
                        $article['status'] ?? '-'
                    ) ?>
                </span>

            </div>

        </section>



        <!-- =====================================================
             STATUS FORM
        ====================================================== -->

        <section class="status-card">

            <div class="card-header">

                <div>

                    <div class="card-kicker">
                        KONTROL REDAKSI
                    </div>

                    <h2>
                        Pilih Status Baru
                    </h2>

                </div>

            </div>


            <form
                method="POST"
                action="<?= $baseUrl ?>/admin/article/<?= (int) $article['id'] ?>/status"
                class="status-form"
            >


                <!-- STATUS -->

                <div class="form-group">

                    <label>
                        STATUS ARTIKEL
                    </label>


                    <select
                        name="status"
                        id="status"
                        required
                    >

                        <option value="">
                            — Pilih Status —
                        </option>


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

                </div>



                <!-- NOTE -->

                <div
                    class="form-group"
                    id="note-group"
                >

                    <label>
                        CATATAN
                    </label>

                    <textarea
                        name="note"
                        id="note"
                        rows="6"
                        placeholder="Masukkan catatan untuk penulis jika diperlukan..."
                    ></textarea>

                    <div class="form-help">
                        Catatan digunakan terutama ketika
                        artikel dikembalikan untuk revisi
                        atau ditolak.
                    </div>

                </div>



                <!-- BUTTON -->

                <div class="form-actions">

                    <a
                        href="<?= $baseUrl ?>/admin/articles"
                        class="cancel-button"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        class="save-button"
                    >
                        Simpan Status
                    </button>

                </div>

            </form>

        </section>



        <!-- =====================================================
             STATUS INFORMATION
        ====================================================== -->

        <section class="status-guide">

            <div class="guide-title">
                KETERANGAN STATUS
            </div>


            <div class="guide-grid">

                <div>
                    <strong>DRAFT</strong>
                    <span>
                        Artikel masih berupa naskah dan
                        belum dikirim untuk review.
                    </span>
                </div>


                <div>
                    <strong>MENUNGGU REVIEW</strong>
                    <span>
                        Artikel menunggu pemeriksaan
                        dari admin/redaksi.
                    </span>
                </div>


                <div>
                    <strong>REVISI</strong>
                    <span>
                        Artikel perlu diperbaiki oleh penulis.
                    </span>
                </div>


                <div>
                    <strong>DITOLAK</strong>
                    <span>
                        Artikel tidak diterima untuk
                        dipublikasikan.
                    </span>
                </div>


                <div>
                    <strong>PUBLISHED</strong>
                    <span>
                        Artikel sudah dapat ditampilkan
                        pada portal publik.
                    </span>
                </div>

            </div>

        </section>


    </main>

</div>



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
    padding: 32px 42px 60px;

    box-sizing: border-box;
}


/* =============================================================
   HEADER
============================================================= */

.page-heading {

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 25px;
}


.breadcrumb {

    margin-bottom: 8px;

    font-size: 9px;

    font-weight: 700;

    letter-spacing: 1.4px;

    color: #999;
}


.page-heading h1 {

    margin: 0;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 27px;

    font-weight: 800;

    color: #111;
}


.page-heading p {

    margin: 7px 0 0;

    font-size: 11px;

    color: #777;
}


.back-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    height: 38px;

    padding: 0 15px;

    background: #fff;

    border: 1px solid #d7d7d7;

    color: #555;

    text-decoration: none;

    font-size: 10px;

    font-weight: 700;
}


.back-button:hover {

    background: #f0f0f0;
}


/* =============================================================
   ERROR
============================================================= */

.error-box {

    margin-bottom: 18px;

    padding: 14px 16px;

    background: #fff0f0;

    border: 1px solid #e2bcbc;

    color: #8a2929;

    font-size: 10px;

    line-height: 1.6;
}


/* =============================================================
   ARTICLE INFO
============================================================= */

.article-info-card {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 25px;

    padding: 22px;

    margin-bottom: 18px;

    background: #fff;

    border: 1px solid #ddd;
}


.article-info-main {

    min-width: 0;
}


.article-label {

    margin-bottom: 7px;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 1px;

    color: #999;
}


.article-info-main h2 {

    margin: 0;

    font-family: Georgia, serif;

    font-size: 20px;

    line-height: 1.35;

    color: #111;
}


.article-meta {

    display: flex;

    flex-wrap: wrap;

    gap: 15px;

    margin-top: 12px;

    font-size: 9px;

    color: #888;
}


.article-meta strong {

    color: #444;
}


.current-status-box {

    flex: 0 0 175px;

    padding: 15px;

    background: #f5f5f5;

    text-align: center;

    box-sizing: border-box;
}


.current-status-box div {

    margin-bottom: 8px;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 1px;

    color: #999;
}


.current-status-box span {

    font-size: 10px;

    font-weight: 900;

    color: #111;
}


/* =============================================================
   STATUS CARD
============================================================= */

.status-card {

    background: #fff;

    border: 1px solid #ddd;

    margin-bottom: 18px;
}


.card-header {

    padding: 18px 22px;

    border-bottom: 1px solid #e4e4e4;
}


.card-kicker {

    margin-bottom: 5px;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 1px;

    color: #999;
}


.card-header h2 {

    margin: 0;

    font-family: Georgia, serif;

    font-size: 17px;

    color: #111;
}


.status-form {

    padding: 22px;
}


.form-group {

    margin-bottom: 20px;
}


.form-group label {

    display: block;

    margin-bottom: 7px;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 1px;

    color: #777;
}


.form-group select,
.form-group textarea {

    width: 100%;

    box-sizing: border-box;

    border: 1px solid #d4d4d4;

    background: #fff;

    outline: none;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 11px;

    color: #222;
}


.form-group select {

    height: 44px;

    padding: 0 12px;
}


.form-group textarea {

    padding: 12px;

    resize: vertical;

    line-height: 1.6;
}


.form-group select:focus,
.form-group textarea:focus {

    border-color: #b9d900;
}


.form-help {

    margin-top: 7px;

    font-size: 9px;

    color: #999;

    line-height: 1.5;
}


/* =============================================================
   ACTION
============================================================= */

.form-actions {

    display: flex;

    justify-content: flex-end;

    gap: 9px;

    padding-top: 5px;
}


.cancel-button,
.save-button {

    min-width: 120px;

    min-height: 40px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    box-sizing: border-box;

    text-decoration: none;

    border: none;

    font-size: 9px;

    font-weight: 800;

    cursor: pointer;
}


.cancel-button {

    background: #eee;

    border: 1px solid #ddd;

    color: #555;
}


.cancel-button:hover {

    background: #e5e5e5;
}


.save-button {

    background: #caff00;

    color: #111;
}


.save-button:hover {

    background: #b9e900;
}


/* =============================================================
   GUIDE
============================================================= */

.status-guide {

    padding: 20px 22px;

    background: #fff;

    border: 1px solid #ddd;
}


.guide-title {

    margin-bottom: 15px;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 1px;

    color: #888;
}


.guide-grid {

    display: grid;

    grid-template-columns:
        repeat(5, minmax(0, 1fr));

    gap: 10px;
}


.guide-grid > div {

    padding: 13px;

    background: #f7f7f7;

    border: 1px solid #e5e5e5;
}


.guide-grid strong {

    display: block;

    margin-bottom: 7px;

    font-size: 8px;

    color: #222;
}


.guide-grid span {

    display: block;

    font-size: 9px;

    line-height: 1.5;

    color: #888;
}


/* =============================================================
   RESPONSIVE
============================================================= */

@media (max-width: 1000px) {

    .article-info-card {

        align-items: flex-start;

        flex-direction: column;
    }


    .current-status-box {

        width: 100%;
    }


    .guide-grid {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }
}


@media (max-width: 700px) {

    .admin-main {

        margin-left: 215px;
    }


    .admin-content {

        padding: 25px 20px 50px;
    }


    .guide-grid {

        grid-template-columns: 1fr;
    }


    .form-actions {

        flex-direction: column;
    }


    .cancel-button,
    .save-button {

        width: 100%;
    }
}

</style>