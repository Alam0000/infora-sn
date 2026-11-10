<?php

$user = $user ?? Auth::user();
$article = $article ?? [];
$categories = $categories ?? [];
$errors = $errors ?? [];
$baseUrl = Auth::baseUrl();

$partialsDir = __DIR__ . '/../client/partials';

$tags = $tags ?? [];

$oldTags = $_POST['tags'] ?? ($selectedTagIds ?? []);

if (!is_array($oldTags)) {
    $oldTags = [];
}

$oldTags = array_map('intval', $oldTags);

$custom_tags = $custom_tags ?? ($_POST['custom_tags'] ?? '');

$thumbnailPath = $article['thumbnail_path'] ?? '';

$currentThumbnailUrl = '';

if ($thumbnailPath !== '') {
    $currentThumbnailUrl = $baseUrl . '/' . ltrim($thumbnailPath, '/');
}

$thumbnailRequired = false;

?>

<link
    href="https://cdn.quilljs.com/1.3.7/quill.snow.css"
    rel="stylesheet"
>

<?php include __DIR__ . '/layout/sidebar.php'; ?>


<div class="admin-main">

    <?php

    $pageTitle = 'Edit Artikel';

    $pageDescription =
        'Perbarui isi dan informasi artikel.';

    ?>

    <?php include __DIR__ . '/layout/header.php'; ?>


    <main class="admin-content">


        <!-- =====================================================
             PAGE HEADER
        ====================================================== -->

        <div class="page-heading">

            <div>

                <div class="breadcrumb">
                    DASHBOARD REDAKSI / ARTIKEL / EDIT
                </div>

                <h1>
                    Edit Artikel
                </h1>

                <p>
                    Admin dapat memperbarui judul,
                    kategori, ringkasan, dan isi artikel.
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

        <?php if (!empty($errors)): ?>

            <div class="error-box">

                <?php foreach ($errors as $error): ?>

                    <div>
                        •
                        <?= htmlspecialchars($error) ?>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>



        <!-- =====================================================
             FORM
        ====================================================== -->

        <form
            id="editForm"
            action="<?= $baseUrl ?>/admin/article/<?= (int) $article['id'] ?>/update"
            method="POST"
            enctype="multipart/form-data"
            class="edit-form"
        >


            <!-- =================================================
                 MAIN CONTENT
            ================================================== -->

            <section class="editor-card">

                <div class="card-header">

                    <div>

                        <div class="card-kicker">
                            INFORMASI ARTIKEL
                        </div>

                        <h2>
                            Isi Naskah
                        </h2>

                    </div>

                    <span class="article-id">
                        #<?= (int) $article['id'] ?>
                    </span>

                </div>


                <div class="form-body">


                    <!-- TITLE -->

                    <div class="form-group">

                        <label>
                            JUDUL ARTIKEL
                        </label>

                        <input
                            type="text"
                            name="title"
                            value="<?= htmlspecialchars(
                                $article['title'] ?? ''
                            ) ?>"
                            placeholder="Masukkan judul artikel..."
                            required
                        >

                    </div>


                    <!-- EXCERPT -->

                    <div class="form-group">

                        <label>
                            RINGKASAN / EXCERPT
                        </label>

                        <textarea
                            name="excerpt"
                            rows="4"
                            placeholder="Masukkan ringkasan singkat artikel..."
                        ><?= htmlspecialchars(
                            $article['excerpt'] ?? ''
                        ) ?></textarea>

                    </div>


                    <!-- TAG -->

                    <div class="form-group">

                        <label>
                            TAG ARTIKEL
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

                        <p class="field-help">
                            Pilih tag yang sesuai. Kalau tidak ada yang
                            cocok, pilih <strong>Lainnya</strong>
                            lalu tulis tag baru.
                        </p>

                    </div>


                    <!-- THUMBNAIL -->

                    <div class="form-group">

                        <label>
                            THUMBNAIL ARTIKEL
                        </label>

                        <?php require $partialsDir . '/thumbnail-cropper.php'; ?>

                        <p class="field-help">
                            Kosongkan kalau tidak ingin mengganti
                            thumbnail. Gunakan rasio 16:9.
                        </p>

                    </div>


                    <!-- CONTENT (QUILL) -->

                    <div class="form-group">

                        <label>
                            ISI ARTIKEL
                        </label>

                        <div id="editor"></div>

                        <input
                            type="hidden"
                            name="content"
                            id="content"
                            value=""
                        >

                    </div>


                </div>

            </section>



            <!-- =================================================
                 SIDEBAR
            ================================================== -->

            <aside class="editor-sidebar">


                <!-- CATEGORY -->

                <section class="side-card">

                    <div class="side-card-title">
                        KATEGORI
                    </div>


                    <select
                        name="category_id"
                        required
                    >

                        <option value="">
                            Pilih Kategori
                        </option>


                        <?php foreach (
                            $categories
                            as $category
                        ): ?>

                            <option
                                value="<?= (int) $category['id'] ?>"
                                <?= (
                                    (int) (
                                        $article['category_id']
                                        ?? 0
                                    )
                                    ===
                                    (int) $category['id']
                                )
                                ? 'selected'
                                : ''
                                ?>
                            >

                                <?= htmlspecialchars(
                                    $category['name']
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </section>



                <!-- STATUS -->

                <section class="side-card">

                    <div class="side-card-title">
                        STATUS SAAT INI
                    </div>


                    <div class="current-status">

                        <?= htmlspecialchars(
                            $article['status']
                            ?? 'DRAFT'
                        ) ?>

                    </div>

                    <p>
                        Edit artikel tidak otomatis
                        mengubah status publikasi.
                    </p>

                </section>



                <!-- AUTHOR -->

                <section class="side-card">

                    <div class="side-card-title">
                        PENULIS
                    </div>


                    <div class="author-box">

                        <strong>
                            <?= htmlspecialchars(
                                $article['author_name']
                                ?? '-'
                            ) ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $article['author_email']
                                ?? ''
                            ) ?>
                        </span>

                    </div>

                </section>



                <!-- ACTION -->

                <section class="side-card action-card">

                    <button
                        type="submit"
                        class="save-button"
                    >
                        Simpan Perubahan
                    </button>


                    <a
                        href="<?= $baseUrl ?>/admin/article/<?= (int) $article['id'] ?>/review"
                        class="review-button"
                    >
                        Lihat Review
                    </a>


                    <a
                        href="<?= $baseUrl ?>/admin/articles"
                        class="cancel-button"
                    >
                        Batal
                    </a>

                </section>


            </aside>


        </form>


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
    | QUILL EDITOR (sama dengan form tulis artikel)
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
    | ISI AWAL ARTIKEL
    |--------------------------------------------------------------------------
    | Konten disimpan sebagai HTML, jadi langsung dimasukkan ke editor
    | supaya tampil sebagai teks berformat (bukan tag mentah).
    |
    | Artikel lama yang isinya teks biasa (tanpa tag HTML) diubah dulu
    | menjadi paragraf supaya baris barunya tidak hilang.
    |--------------------------------------------------------------------------
    */

    let initialContent =
        <?= json_encode(
            $article['content'] ?? '',
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_HEX_TAG
        ) ?>;


    if (
        initialContent.trim() !== '' &&
        !/<[a-z][\s\S]*>/i.test(initialContent)
    ) {

        const escapeHtml = function (text) {

            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

        };

        initialContent =
            initialContent
                .split(/\n{2,}/)
                .map(function (paragraph) {

                    return '<p>' +
                        escapeHtml(paragraph)
                            .replace(/\n/g, '<br>') +
                        '</p>';

                })
                .join('');

    }


    quill.root.innerHTML =
        initialContent;


    /*
    |--------------------------------------------------------------------------
    | SUBMIT FORM
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('editForm')
        .addEventListener(
            'submit',
            function (event) {

                if (quill.getText().trim() === '') {

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
   PAGE HEADER
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

    font-size: 11px;

    color: #777777;
}


.back-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    height: 38px;

    padding: 0 15px;

    box-sizing: border-box;

    background: #ffffff;

    border: 1px solid #d8d8d8;

    color: #555555;

    text-decoration: none;

    font-size: 10px;

    font-weight: 700;
}


.back-button:hover {

    background: #f1f1f1;
}


/* =============================================================
   ERROR
============================================================= */

.error-box {

    margin-bottom: 18px;

    padding: 14px 16px;

    background: #fff0f0;

    border: 1px solid #edc0c0;

    color: #8b2929;

    font-size: 10px;

    line-height: 1.8;
}


/* =============================================================
   FORM LAYOUT
============================================================= */

.edit-form {

    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        285px;

    gap: 18px;

    align-items: start;
}


/* =============================================================
   CARD
============================================================= */

.editor-card,
.side-card {

    background: #ffffff;

    border: 1px solid #dddddd;
}


.card-header {

    min-height: 76px;

    padding: 17px 20px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    box-sizing: border-box;

    border-bottom: 1px solid #e2e2e2;
}


.card-kicker {

    margin-bottom: 5px;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 1px;

    color: #999999;
}


.card-header h2 {

    margin: 0;

    font-family: Georgia, serif;

    font-size: 16px;

    color: #111111;
}


.article-id {

    padding: 6px 8px;

    background: #f1f1f1;

    color: #777777;

    font-size: 8px;

    font-weight: 700;
}


/* =============================================================
   FORM BODY
============================================================= */

.form-body {

    padding: 22px;
}


.form-group {

    margin-bottom: 21px;
}


.form-group:last-child {

    margin-bottom: 0;
}


.form-group > label {

    display: block;

    margin-bottom: 7px;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 0.8px;

    color: #777777;
}


.form-group input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="hidden"]):not([type="range"]),
.form-group textarea,
.side-card select {

    width: 100%;

    box-sizing: border-box;

    border: 1px solid #d5d5d5;

    outline: none;

    background: #ffffff;

    color: #222222;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 11px;
}


.form-group input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="hidden"]):not([type="range"]) {

    height: 44px;

    padding: 0 12px;
}


.form-group textarea {

    padding: 12px;

    resize: vertical;

    line-height: 1.6;
}


.form-group input:focus,
.form-group textarea:focus,
.side-card select:focus {

    border-color: #b9d900;
}


/* =============================================================
   TAG & TEKS BANTUAN
============================================================= */

.tag-list {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fill,
            minmax(150px, 1fr)
        );

    gap: 8px;
}


.tag-item {

    display: flex;

    align-items: center;

    gap: 8px;

    padding: 10px 12px;

    border: 1px solid #d5d5d5;

    background: #ffffff;

    cursor: pointer;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 11px;

    color: #333333;
}


.tag-item:hover {

    background: #f7f7f7;
}


.tag-item input {

    width: 15px;

    height: 15px;

    margin: 0;

    cursor: pointer;
}


.field-help {

    margin: 8px 0 0;

    font-size: 9px;

    line-height: 1.5;

    color: #999999;
}


/* =============================================================
   QUILL EDITOR
============================================================= */

#editor {

    height: 480px;

    background: #ffffff;
}


.ql-toolbar.ql-snow {

    border-color: #d5d5d5;

    font-family: Arial, Helvetica, sans-serif;
}


.ql-container.ql-snow {

    border-color: #d5d5d5;

    font-family: Georgia, serif;

    font-size: 14px;
}


.ql-editor {

    min-height: 480px;

    line-height: 1.75;
}


/* input link di Quill jangan ikut style input form */

.form-group .ql-tooltip input[type="text"] {

    display: inline-block !important;

    width: 170px !important;

    height: 26px !important;

    padding: 3px 5px !important;

    border: 1px solid #cccccc !important;

    font-size: 13px !important;
}


/* =============================================================
   EDITOR SIDEBAR
============================================================= */

.editor-sidebar {

    display: flex;

    flex-direction: column;

    gap: 12px;
}


.side-card {

    padding: 17px;
}


.side-card-title {

    margin-bottom: 11px;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 1px;

    color: #888888;
}


.side-card select {

    height: 40px;

    padding: 0 10px;
}


.current-status {

    display: inline-block;

    padding: 7px 9px;

    background: #eeeeee;

    color: #555555;

    font-size: 8px;

    font-weight: 800;
}


.side-card p {

    margin: 9px 0 0;

    font-size: 9px;

    line-height: 1.5;

    color: #999999;
}


/* =============================================================
   AUTHOR
============================================================= */

.author-box {

    display: flex;

    flex-direction: column;

    gap: 4px;
}


.author-box strong {

    font-family: Georgia, serif;

    font-size: 12px;

    color: #222222;
}


.author-box span {

    font-size: 9px;

    color: #999999;

    word-break: break-word;
}


/* =============================================================
   ACTION
============================================================= */

.action-card {

    display: flex;

    flex-direction: column;

    gap: 8px;
}


.save-button,
.review-button,
.cancel-button {

    width: 100%;

    min-height: 40px;

    display: flex;

    align-items: center;

    justify-content: center;

    box-sizing: border-box;

    text-decoration: none;

    border: none;

    font-size: 9px;

    font-weight: 800;

    cursor: pointer;
}


.save-button {

    background: #caff00;

    color: #111111;
}


.save-button:hover {

    background: #bce900;
}


.review-button {

    background: #111111;

    color: #ffffff;
}


.review-button:hover {

    background: #333333;
}


.cancel-button {

    background: #eeeeee;

    color: #555555;

    border: 1px solid #dddddd;
}


.cancel-button:hover {

    background: #e5e5e5;
}


/* =============================================================
   RESPONSIVE
============================================================= */

@media (max-width: 1000px) {

    .edit-form {

        grid-template-columns:
            1fr;
    }


    .editor-sidebar {

        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }


    .action-card {

        grid-column: 1 / -1;
    }

}


@media (max-width: 700px) {

    .admin-main {

        margin-left: 215px;
    }


    .admin-content {

        padding: 25px 20px 50px;
    }


    .page-heading {

        align-items: flex-start;

        flex-direction: column;
    }


    .editor-sidebar {

        display: flex;
    }


    #editor,
    .ql-editor {

        min-height: 380px;
    }


    #editor {

        height: 380px;
    }

}

</style>