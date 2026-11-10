<?php

$pageTitle =
    'Tambah Artikel';

$pageDescription =
    'Admin dapat membuat dan menerbitkan artikel atas nama penulis tanpa membuat akun client.';

$user =
    $user ?? Auth::user();

$article =
    $article ?? [];

$categories =
    $categories ?? [];

$tags =
    $tags ?? [];

$errors =
    $errors ?? [];

$baseUrl =
    Auth::baseUrl();


/*
|--------------------------------------------------------------------------
| Lokasi partial (dipakai bersama form penulis)
|--------------------------------------------------------------------------
| Partial custom-tag-field.php & thumbnail-cropper.php aslinya ada di
| folder view client. Kalau kamu pindahkan ke folder lain (misal
| module/view/partials), cukup ubah baris ini saja.
*/

$partialsDir =
    __DIR__ . '/../client/partials';


$authorOverride =
    $article['author_override']
    ?? '';

$title =
    $article['title']
    ?? '';

$excerpt =
    $article['excerpt']
    ?? '';

$content =
    $article['content']
    ?? '';

$categoryId =
    (int) (
        $article['category_id']
        ?? 0
    );

$status =
    $article['status']
    ?? 'DRAFT';


/*
|--------------------------------------------------------------------------
| Tag terpilih sebelumnya (saat validasi error)
|--------------------------------------------------------------------------
*/

$oldTags =
    $_POST['tags']
    ?? [];

if (!is_array($oldTags)) {
    $oldTags = [];
}

$oldTags =
    array_map('intval', $oldTags);


/*
|--------------------------------------------------------------------------
| Isian "Tag Lainnya" (dipakai partial custom-tag-field.php)
|--------------------------------------------------------------------------
*/

$custom_tags =
    $custom_tags
    ?? ($_POST['custom_tags'] ?? '');


/*
|--------------------------------------------------------------------------
| Isi editor saat validasi error
|--------------------------------------------------------------------------
*/

$editorContent =
    $_POST['content']
    ?? $content;

?>

<link
    href="https://cdn.quilljs.com/1.3.7/quill.snow.css"
    rel="stylesheet"
>

<?php include __DIR__ . '/layout/sidebar.php'; ?>

<div class="admin-main">

    <?php include __DIR__ . '/layout/header.php'; ?>


    <main class="admin-content">

        <div class="article-form-page">

            <!-- =====================================================
                 PAGE HEADER
            ====================================================== -->

            <div class="article-form-top">

                <div>

                    <div class="article-form-kicker">
                        ARTIKEL ADMIN
                    </div>

                    <h1>
                        Tambah Artikel
                    </h1>

                    <p>
                        Buat artikel untuk penulis yang mengirimkan
                        naskah kepada redaksi tanpa perlu membuat akun.
                    </p>

                </div>


                <a
                    href="<?= $baseUrl ?>/admin/articles"
                    class="article-form-back"
                >
                    ← Kembali
                </a>

            </div>


            <!-- =====================================================
                 ERROR
            ====================================================== -->

            <?php if (!empty($errors)): ?>

                <div class="article-form-alert error">

                    <strong>
                        Artikel belum dapat disimpan.
                    </strong>

                    <ul>

                        <?php foreach ($errors as $error): ?>

                            <li>
                                <?= htmlspecialchars($error) ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>


            <!-- =====================================================
                 FORM
            ====================================================== -->

            <form
                id="articleForm"
                action="<?= $baseUrl ?>/admin/articles/store"
                method="POST"
                enctype="multipart/form-data"
                class="article-form"
            >


                <!-- =================================================
                     01. PENULIS
                ================================================== -->

                <section class="article-form-card">

                    <div class="article-form-card-header">

                        <div>

                            <span>
                                01
                            </span>

                            <h2>
                                Informasi Penulis
                            </h2>

                        </div>

                    </div>


                    <div class="article-form-field">

                        <label for="author_override">

                            Nama Penulis

                            <small>
                                *
                            </small>

                        </label>


                        <input
                            type="text"
                            id="author_override"
                            name="author_override"
                            value="<?= htmlspecialchars($authorOverride) ?>"
                            placeholder="Contoh: Budi Santoso"
                            maxlength="150"
                            required
                        >


                        <p class="article-form-help">

                            Nama ini yang akan tampil sebagai penulis
                            artikel di portal publik.

                        </p>

                    </div>

                </section>


                <!-- =================================================
                     02. INFORMASI ARTIKEL
                ================================================== -->

                <section class="article-form-card">

                    <div class="article-form-card-header">

                        <div>

                            <span>
                                02
                            </span>

                            <h2>
                                Informasi Artikel
                            </h2>

                        </div>

                    </div>


                    <!-- JUDUL -->

                    <div class="article-form-field">

                        <label for="title">

                            Judul Artikel

                            <small>
                                *
                            </small>

                        </label>


                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="<?= htmlspecialchars($title) ?>"
                            placeholder="Masukkan judul artikel"
                            maxlength="255"
                            required
                        >

                    </div>


                    <!-- KATEGORI + STATUS -->

                    <div class="article-form-grid">


                        <div class="article-form-field">

                            <label for="category_id">

                                Kategori

                                <small>
                                    *
                                </small>

                            </label>


                            <select
                                id="category_id"
                                name="category_id"
                                required
                            >

                                <option value="">
                                    Pilih kategori
                                </option>


                                <?php foreach ($categories as $category): ?>

                                    <option
                                        value="<?= (int) $category['id'] ?>"
                                        <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>
                                    >

                                        <?= htmlspecialchars(
                                            $category['name']
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="article-form-field">

                            <label for="status">

                                Status Publikasi

                            </label>


                            <select
                                id="status"
                                name="status"
                            >

                                <option
                                    value="DRAFT"
                                    <?= $status === 'DRAFT' ? 'selected' : '' ?>
                                >
                                    Draft
                                </option>


                                <option
                                    value="MENUNGGU_REVIEW"
                                    <?= $status === 'MENUNGGU_REVIEW' ? 'selected' : '' ?>
                                >
                                    Menunggu Review
                                </option>


                                <option
                                    value="PUBLISHED"
                                    <?= $status === 'PUBLISHED' ? 'selected' : '' ?>
                                >
                                    Publikasikan
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- TAG -->

                    <div class="article-form-field">

                        <label>
                            Tag Artikel
                        </label>


                        <div class="tag-list">

                            <?php foreach ($tags as $tag): ?>

                                <label class="tag-item">

                                    <input
                                        type="checkbox"
                                        name="tags[]"
                                        value="<?= (int) $tag['id'] ?>"
                                        <?= in_array(
                                            (int) $tag['id'],
                                            $oldTags,
                                            true
                                        )
                                            ? 'checked'
                                            : ''
                                        ?>
                                    >

                                    <span>

                                        <?= htmlspecialchars(
                                            $tag['name']
                                        ) ?>

                                    </span>

                                </label>

                            <?php endforeach; ?>


                            <!-- KARTU "LAINNYA" + INPUT TAG BARU -->

                            <?php require $partialsDir . '/custom-tag-field.php'; ?>

                        </div>


                        <p class="article-form-help">

                            Pilih tag yang sesuai dengan isi artikel.
                            Kalau tidak ada yang cocok, pilih
                            <strong>Lainnya</strong>
                            lalu tulis tag baru.

                        </p>

                    </div>


                    <!-- RINGKASAN -->

                    <div class="article-form-field">

                        <label for="excerpt">

                            Ringkasan

                        </label>


                        <textarea
                            id="excerpt"
                            name="excerpt"
                            rows="4"
                            maxlength="500"
                            placeholder="Tuliskan ringkasan singkat artikel..."
                        ><?= htmlspecialchars($excerpt) ?></textarea>

                    </div>

                </section>


                <!-- =================================================
                     03. THUMBNAIL
                ================================================== -->

                <section class="article-form-card">

                    <div class="article-form-card-header">

                        <div>

                            <span>
                                03
                            </span>

                            <h2>
                                Thumbnail
                            </h2>

                        </div>

                    </div>


                    <div class="article-form-field">

                        <label for="thumbnail">

                            Gambar Thumbnail

                        </label>


                        <?php

                        // Untuk admin thumbnail dibuat opsional.
                        // Ganti ke true kalau mau wajib seperti form penulis.
                        $thumbnailRequired   = false;
                        $currentThumbnailUrl = '';

                        require $partialsDir . '/thumbnail-cropper.php';

                        ?>


                        <p class="article-form-help">

                            Gunakan rasio 16:9. Maksimal 5 MB.
                            Format JPG, PNG, WEBP, atau GIF.

                        </p>

                    </div>

                </section>


                <!-- =================================================
                     04. ISI ARTIKEL
                ================================================== -->

                <section class="article-form-card">

                    <div class="article-form-card-header">

                        <div>

                            <span>
                                04
                            </span>

                            <h2>
                                Isi Artikel
                            </h2>

                        </div>

                    </div>


                    <div class="article-form-field">

                        <label>

                            Isi Artikel

                            <small>
                                *
                            </small>

                        </label>


                        <div id="editor"></div>


                        <input
                            type="hidden"
                            name="content"
                            id="content"
                            value=""
                        >


                        <p class="article-form-help">

                            Gunakan toolbar di atas untuk mengatur
                            ukuran huruf, tebal/miring, rata teks,
                            daftar, kutipan, dan tautan.

                        </p>

                    </div>

                </section>


                <!-- =================================================
                     ACTION
                ================================================== -->

                <div class="article-form-actions">

                    <a
                        href="<?= $baseUrl ?>/admin/articles"
                        class="article-form-button secondary"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        class="article-form-button primary"
                    >
                        Simpan Artikel
                    </button>

                </div>

            </form>

        </div>

    </main>

</div>


<!-- =============================================================
     QUILL
============================================================== -->

<script
    src="https://cdn.quilljs.com/1.3.7/quill.min.js"
></script>


<script>

    /*
    |--------------------------------------------------------------------------
    | QUILL EDITOR (sama dengan form penulis)
    |--------------------------------------------------------------------------
    */

    const quill = new Quill(
        '#editor',
        {
            theme: 'snow',

            placeholder:
                'Mulai tulis artikel di sini...',

            modules: {

                toolbar: [

                    [
                        {
                            header: [
                                1,
                                2,
                                3,
                                false
                            ]
                        }
                    ],

                    [
                        'bold',
                        'italic',
                        'underline',
                        'strike'
                    ],

                    [
                        {
                            size: [
                                'small',
                                false,
                                'large',
                                'huge'
                            ]
                        }
                    ],

                    [
                        {
                            align: []
                        }
                    ],

                    [
                        {
                            list: 'ordered'
                        },

                        {
                            list: 'bullet'
                        }
                    ],

                    [
                        'blockquote'
                    ],

                    [
                        'link'
                    ],

                    [
                        'clean'
                    ]

                ]

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | KEMBALIKAN ISI SAAT VALIDASI ERROR
    |--------------------------------------------------------------------------
    */

    <?php if (!empty($editorContent)): ?>

        quill.root.innerHTML =
            <?= json_encode(
                $editorContent,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_HEX_TAG
            ) ?>;

    <?php endif; ?>


    /*
    |--------------------------------------------------------------------------
    | SUBMIT FORM
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('articleForm')
        .addEventListener(
            'submit',
            function (event) {

                const plainText =
                    quill.getText()
                        .trim();


                if (plainText === '') {

                    event.preventDefault();

                    alert(
                        'Isi artikel wajib diisi.'
                    );

                    return;
                }


                document.getElementById(
                    'content'
                ).value =
                    quill.root.innerHTML;

            }
        );

</script>


<style>

/* ================================================================
   MAIN LAYOUT (sama dengan halaman daftar artikel)
================================================================ */

.admin-main {

    margin-left: 245px;

    min-height: 100vh;

    background: #f4f5f7;

}


.admin-content {

    padding: 36px 42px 60px;

    box-sizing: border-box;

}


/* ================================================================
   ARTICLE FORM PAGE
================================================================ */

.article-form-page {

    width: 100%;
    max-width: 1050px;

    margin: 0 auto;

    padding-bottom: 60px;

    box-sizing: border-box;
}


/* ================================================================
   TOP HEADER
================================================================ */

.article-form-top {

    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    gap: 30px;

    margin-bottom: 28px;

}


.article-form-kicker {

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 1.5px;

    color: #8b95a5;

    margin-bottom: 8px;

}


.article-form-top h1 {

    margin: 0 0 8px;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 30px;

    line-height: 1.2;

    color: #111827;

}


.article-form-top p {

    margin: 0;

    max-width: 650px;

    color: #6b7280;

    line-height: 1.6;

    font-size: 13px;

}


.article-form-back {

    display: inline-flex;

    align-items: center;

    text-decoration: none;

    color: #374151;

    font-weight: 700;

    font-size: 13px;

    white-space: nowrap;

}


.article-form-back:hover {

    color: #111827;

}


/* ================================================================
   ALERT
================================================================ */

.article-form-alert {

    padding: 16px 18px;

    border-radius: 12px;

    margin-bottom: 22px;

}


.article-form-alert.error {

    background: #fff1f2;

    border: 1px solid #fecdd3;

    color: #9f1239;

}


.article-form-alert strong {

    font-size: 13px;

}


.article-form-alert ul {

    margin: 8px 0 0 18px;

    padding: 0;

}


.article-form-alert li {

    margin-bottom: 4px;

    font-size: 12px;

}


/* ================================================================
   FORM
================================================================ */

.article-form {

    width: 100%;

}


/* ================================================================
   CARD
================================================================ */

.article-form-card {

    width: 100%;

    box-sizing: border-box;

    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 16px;

    padding: 26px;

    margin-bottom: 20px;

}


.article-form-card-header {

    margin-bottom: 24px;

}


.article-form-card-header > div {

    display: flex;

    align-items: center;

    gap: 12px;

}


.article-form-card-header span {

    display: inline-flex;

    width: 32px;

    height: 32px;

    align-items: center;

    justify-content: center;

    flex: 0 0 32px;

    border-radius: 9px;

    background: #111827;

    color: #ffffff;

    font-size: 11px;

    font-weight: 800;

}


.article-form-card-header h2 {

    margin: 0;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 19px;

    color: #111827;

}


/* ================================================================
   FIELD
================================================================ */

.article-form-field {

    width: 100%;

    margin-bottom: 20px;

    box-sizing: border-box;

}


.article-form-field:last-child {

    margin-bottom: 0;

}


/* label judul field saja (bukan label di dalam tag / partial) */

.article-form-field > label {

    display: block;

    margin-bottom: 8px;

    font-size: 13px;

    font-weight: 800;

    color: #374151;

}


.article-form-field > label small {

    color: #dc2626;

}


/* ================================================================
   INPUT / SELECT / TEXTAREA
   (checkbox, radio, file, hidden dikecualikan supaya tag &
   thumbnail cropper tidak ikut membesar)
================================================================ */

.article-form-field input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="hidden"]):not([type="range"]),
.article-form-field select,
.article-form-field textarea {

    display: block;

    width: 100%;

    max-width: 100%;

    box-sizing: border-box;

    border: 1px solid #d1d5db;

    border-radius: 10px;

    background: #ffffff;

    color: #111827;

    padding: 12px 14px;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 13px;

    line-height: 1.5;

    outline: none;

    transition:
        border-color .2s,
        box-shadow .2s;

}


.article-form-field textarea {

    resize: vertical;

    line-height: 1.7;

    min-height: 100px;

}


.article-form-field input:focus,
.article-form-field select:focus,
.article-form-field textarea:focus {

    border-color: #111827;

    box-shadow:
        0 0 0 3px
        rgba(17, 24, 39, .08);

}


/* ================================================================
   HELP TEXT
================================================================ */

.article-form-help {

    margin: 7px 0 0;

    font-size: 12px;

    line-height: 1.5;

    color: #9ca3af;

}


/* ================================================================
   GRID
================================================================ */

.article-form-grid {

    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr);

    gap: 18px;

    margin-bottom: 20px;

}


.article-form-grid .article-form-field {

    margin-bottom: 0;

}


/* ================================================================
   TAG (sama dengan form penulis)
================================================================ */

.tag-list {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fill,
            minmax(150px, 1fr)
        );

    gap: 9px;

}


.tag-item {

    display: flex;

    align-items: center;

    gap: 8px;

    padding: 10px 12px;

    border: 1px solid #e5e7eb;

    border-radius: 8px;

    background: #ffffff;

    cursor: pointer;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 13px;

    color: #374151;

}


.tag-item:hover {

    background: #f9fafb;

}


.tag-item input {

    width: 16px;

    height: 16px;

    margin: 0;

    cursor: pointer;

}


/* ================================================================
   QUILL EDITOR
================================================================ */

#editor {

    height: 430px;

    background: #ffffff;

}


.ql-toolbar.ql-snow {

    border-radius: 10px 10px 0 0;

    border-color: #d1d5db;

    font-family: Arial, Helvetica, sans-serif;

}


.ql-container.ql-snow {

    border-color: #d1d5db;

    border-radius: 0 0 10px 10px;

    font-size: 15px;

    font-family: Arial, Helvetica, sans-serif;

}


.ql-editor {

    min-height: 430px;

    line-height: 1.7;

}


/* input link di Quill jangan ikut style input form */

.article-form-field .ql-tooltip input[type="text"] {

    display: inline-block !important;

    width: 170px !important;

    height: 26px !important;

    padding: 3px 5px !important;

    border: 1px solid #ccc !important;

    border-radius: 0 !important;

    font-size: 13px !important;

    box-shadow: none !important;

}


/* ================================================================
   ACTION
================================================================ */

.article-form-actions {

    display: flex;

    justify-content: flex-end;

    align-items: center;

    gap: 12px;

    margin-top: 4px;

}


.article-form-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 44px;

    padding: 0 20px;

    border-radius: 10px;

    border: 0;

    text-decoration: none;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 13px;

    font-weight: 800;

    cursor: pointer;

    box-sizing: border-box;

}


.article-form-button.secondary {

    background: #f3f4f6;

    color: #374151;

}


.article-form-button.secondary:hover {

    background: #e5e7eb;

}


.article-form-button.primary {

    background: #111827;

    color: #ffffff;

}


.article-form-button.primary:hover {

    background: #252b38;

}


/* ================================================================
   RESPONSIVE
================================================================ */

@media (max-width: 1200px) {

    .article-form-page {

        max-width: 100%;

    }

}


@media (max-width: 900px) {

    .admin-main {

        margin-left: 215px;

    }


    .admin-content {

        padding: 28px 24px 50px;

    }


    .article-form-grid {

        grid-template-columns: 1fr;

    }

}


@media (max-width: 768px) {

    .article-form-top {

        flex-direction: column;

    }


    .article-form-card {

        padding: 20px;

        border-radius: 12px;

    }


    .article-form-actions {

        flex-direction: column-reverse;

        align-items: stretch;

    }


    .article-form-button {

        width: 100%;

    }

}


@media (max-width: 600px) {

    .admin-content {

        padding: 22px 15px 40px;

    }


    .article-form-top h1 {

        font-size: 25px;

    }


    #editor,
    .ql-editor {

        min-height: 350px;

    }


    #editor {

        height: 350px;

    }

}

</style>