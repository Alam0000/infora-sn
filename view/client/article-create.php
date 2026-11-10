<?php

$baseUrl = rtrim(
    dirname(
        $_SERVER['SCRIPT_NAME'] ?? ''
    ),
    '/'
);

if ($baseUrl === '/' || $baseUrl === '\\') {
    $baseUrl = '';
}

$error = $error ?? null;
$categories = $categories ?? [];
$tags = $tags ?? [];

$title = $_POST['title'] ?? '';
$excerpt = $_POST['excerpt'] ?? '';
$categoryId = (int) ($_POST['category_id'] ?? 0);

$oldTags = $_POST['tags'] ?? [];

if (!is_array($oldTags)) {
    $oldTags = [];
}

$oldTags = array_map('intval', $oldTags);

/*
|--------------------------------------------------------------------------
| Isian "Tag Lainnya" (dipakai partial custom-tag-field.php)
|--------------------------------------------------------------------------
| Diambil dari controller kalau ada, kalau tidak dari $_POST,
| supaya isian tidak hilang saat terjadi error validasi.
*/

$custom_tags = $custom_tags ?? ($_POST['custom_tags'] ?? '');

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Tulis Artikel - INFORA SN
    </title>

    <link
        href="https://cdn.quilljs.com/1.3.7/quill.snow.css"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        body {
            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f6f8;
            color: #111827;
        }

        /* =========================================================
           LAYOUT UTAMA
           ========================================================= */

        .layout {
            display: flex;
            min-height: 100vh;
        }


        /* =========================================================
           SIDEBAR
           ========================================================= */

        .sidebar {
            width: 250px;
            min-width: 250px;

            background: #ffffff;

            border-right: 1px solid #e5e7eb;

            padding: 25px 18px;

            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
        }

        .brand {
            font-size: 24px;
            font-weight: 700;

            color: #111827;

            margin: 0 0 35px 0;
        }

        .menu-title {
            font-size: 12px;

            color: #6b7280;

            margin-bottom: 10px;

            text-transform: uppercase;
        }

        .menu {
            display: flex;
            flex-direction: column;

            gap: 4px;
        }

        .menu a {
            display: block;

            padding: 12px 14px;

            border-radius: 8px;

            text-decoration: none;

            color: #111827;

            font-size: 16px;
        }

        .menu a:hover {
            background: #f3f4f6;
        }

        .menu a.active {
            background: #111827;
            color: #ffffff;
        }


        /* =========================================================
           CONTENT KANAN
           ========================================================= */

        .main-content {
            margin-left: 250px;

            width: calc(100% - 250px);

            min-height: 100vh;

            padding: 32px 30px 50px;
        }


        /* =========================================================
           HEADER
           ========================================================= */

        .page-header {
            display: flex;

            justify-content: space-between;
            align-items: flex-start;

            margin-bottom: 28px;
        }

        .page-header-left h1 {
            margin: 0 0 8px;

            font-size: 30px;

            line-height: 1.2;
        }

        .page-header-left p {
            margin: 0;

            font-size: 16px;

            color: #111827;
        }

        .user-info {
            text-align: right;

            padding-top: 8px;
        }

        .user-name {
            font-size: 15px;

            font-weight: 700;
        }

        .user-role {
            font-size: 13px;

            color: #6b7280;
        }


        /* =========================================================
           ALERT
           ========================================================= */

        .alert {
            background: #fee2e2;

            border: 1px solid #fecaca;

            color: #991b1b;

            border-radius: 8px;

            padding: 13px 16px;

            margin-bottom: 20px;

            font-size: 14px;
        }


        /* =========================================================
           CARD
           ========================================================= */

        .card {
            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 10px;

            margin-bottom: 20px;

            overflow: hidden;
        }

        .card-header {
            padding: 20px 22px;

            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h2 {
            margin: 0;

            font-size: 20px;
        }

        .card-body {
            padding: 22px;
        }


        /* =========================================================
           FORM
           ========================================================= */

        .form-group {
            margin-bottom: 22px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;

            margin-bottom: 8px;

            font-size: 14px;

            font-weight: 700;
        }

        .required {
            color: #dc2626;
        }

        input[type="text"],
        select,
        textarea {
            width: 100%;

            padding: 12px 14px;

            border: 1px solid #d1d5db;

            border-radius: 7px;

            background: #ffffff;

            color: #111827;

            font-size: 15px;

            outline: none;
        }

        input[type="text"]:focus,
        select:focus,
        textarea:focus {
            border-color: #111827;
        }

        textarea {
            min-height: 110px;

            resize: vertical;

            font-family: Arial, Helvetica, sans-serif;
        }

        .help-text {
            margin-top: 7px;

            color: #6b7280;

            font-size: 13px;

            line-height: 1.5;
        }


        /* =========================================================
           TAG
           ========================================================= */

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

            border-radius: 7px;

            background: #ffffff;

            cursor: pointer;

            font-size: 14px;
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


        /* =========================================================
           QUILL EDITOR
           ========================================================= */

        #editor {
            height: 430px;

            background: #ffffff;
        }

        .ql-toolbar.ql-snow {
            border-radius: 7px 7px 0 0;

            border-color: #d1d5db;
        }

        .ql-container.ql-snow {
            border-color: #d1d5db;

            border-radius: 0 0 7px 7px;

            font-size: 16px;
        }

        .ql-editor {
            min-height: 430px;

            line-height: 1.7;
        }


        /* =========================================================
           PANDUAN
           ========================================================= */

        .guide {
            background: #f8fafc;

            border: 1px solid #e5e7eb;

            border-radius: 8px;

            padding: 16px 18px;
        }

        .guide-title {
            font-weight: 700;

            margin-bottom: 10px;

            font-size: 15px;
        }

        .guide ul {
            margin: 0;

            padding-left: 20px;
        }

        .guide li {
            margin-bottom: 6px;

            font-size: 14px;

            color: #4b5563;
        }

        .guide li:last-child {
            margin-bottom: 0;
        }


        /* =========================================================
           ACTION
           ========================================================= */

        .form-actions {
            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 10px;

            padding: 18px 22px;

            border-top: 1px solid #e5e7eb;
        }

        .action-right {
            display: flex;

            gap: 10px;
        }

        .btn {
            display: inline-block;

            border: none;

            border-radius: 7px;

            padding: 11px 18px;

            font-size: 14px;

            font-weight: 700;

            text-decoration: none;

            cursor: pointer;
        }

        .btn-cancel {
            background: #e5e7eb;

            color: #374151;
        }

        .btn-cancel:hover {
            background: #d1d5db;
        }

        .btn-draft {
            background: #f59e0b;

            color: #ffffff;
        }

        .btn-draft:hover {
            background: #d97706;
        }

        .btn-submit {
            background: #111827;

            color: #ffffff;
        }

        .btn-submit:hover {
            background: #1f2937;
        }


        /* =========================================================
           RESPONSIVE
           ========================================================= */

        @media (max-width: 900px) {

            .sidebar {
                width: 220px;

                min-width: 220px;
            }

            .main-content {
                margin-left: 220px;

                width: calc(100% - 220px);

                padding: 25px 20px;
            }

        }


        @media (max-width: 700px) {

            .layout {
                display: block;
            }

            .sidebar {
                position: static;

                width: 100%;

                min-width: 100%;

                border-right: none;

                border-bottom: 1px solid #e5e7eb;

                padding: 20px;
            }

            .brand {
                margin-bottom: 20px;
            }

            .menu {
                flex-direction: row;

                flex-wrap: wrap;
            }

            .menu a {
                font-size: 14px;
            }

            .main-content {
                margin-left: 0;

                width: 100%;

                padding: 22px 15px 40px;
            }

            .page-header {
                flex-direction: column;

                gap: 15px;
            }

            .user-info {
                text-align: left;
            }

            .page-header-left h1 {
                font-size: 25px;
            }

            .card-body {
                padding: 17px;
            }

            .form-actions {
                flex-direction: column;

                align-items: stretch;
            }

            .action-right {
                display: grid;

                grid-template-columns: 1fr 1fr;
            }

            .btn {
                text-align: center;
            }

        }


        @media (max-width: 450px) {

            .action-right {
                grid-template-columns: 1fr;
            }

            #editor {
                height: 350px;
            }

            .ql-editor {
                min-height: 350px;
            }

        }

    </style>

</head>


<body>


<div class="layout">


    <!-- =========================================================
         SIDEBAR
         ========================================================= -->

    <aside class="sidebar">

        <div class="brand">
            INFORA SN
        </div>


        <div class="menu-title">
            Menu Penulis
        </div>


        <nav class="menu">

            <a
                href="<?= htmlspecialchars($baseUrl) ?>/client/dashboard"
            >
                Dashboard
            </a>

            <a
                href="<?= htmlspecialchars($baseUrl) ?>/client/article/create"
                class="active"
            >
                Tulis Artikel
            </a>

            <a
                href="<?= $baseUrl ?>/client/articles"
            >
                Status Artikel
            </a>

            <a href="<?= $baseUrl ?>/client/revisions">
                Revisi Artikel
            </a>


            <a href="#">
                Pengaturan Akun
            </a>

            <a
                href="<?= htmlspecialchars($baseUrl) ?>/logout"
            >
                Logout
            </a>

        </nav>

    </aside>


    <!-- =========================================================
         KONTEN KANAN
         ========================================================= -->

    <main class="main-content">


        <!-- HEADER -->

        <div class="page-header">

            <div class="page-header-left">

                <h1>
                    Tulis Artikel
                </h1>

                <p>
                    Buat artikel baru untuk diterbitkan di INFORA SN.
                </p>

            </div>


            <div class="user-info">

                <?php

                $currentUser = Auth::user();

                ?>

                <div class="user-name">

                    <?= htmlspecialchars(
                        $currentUser['name']
                        ?? 'Penulis'
                    ) ?>

                </div>

                <div class="user-role">
                    Penulis
                </div>

            </div>

        </div>


        <!-- ERROR -->

        <?php if (!empty($error)): ?>

            <div class="alert">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- =====================================================
             FORM ARTIKEL
             ===================================================== -->

        <form
            id="articleForm"
            action="<?= htmlspecialchars($baseUrl) ?>/client/article/store"
            method="POST"
            enctype="multipart/form-data"
        >


            <!-- =================================================
                 INFORMASI DASAR
                 ================================================= -->

            <div class="card">

                <div class="card-header">

                    <h2>
                        Informasi Artikel
                    </h2>

                </div>


                <div class="card-body">


                    <!-- JUDUL -->

                    <div class="form-group">

                        <label for="title">

                            Judul Artikel

                            <span class="required">
                                *
                            </span>

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


                    <!-- KATEGORI -->

                    <div class="form-group">

                        <label for="category_id">

                            Kategori

                            <span class="required">
                                *
                            </span>

                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            required
                        >

                            <option value="">
                                -- Pilih Kategori --
                            </option>

                            <?php foreach ($categories as $category): ?>

                                <option
                                    value="<?= (int) $category['id'] ?>"
                                    <?= (
                                        $categoryId ===
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

                    </div>


                    <!-- TAG -->

                    <div class="form-group">

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

                            <?php require __DIR__ . '/partials/custom-tag-field.php'; ?>

                        </div>

                        <div class="help-text">
                            Pilih tag yang sesuai dengan isi artikel.
                            Kalau tidak ada yang cocok, pilih
                            <strong>Lainnya</strong>
                            lalu tulis tag baru.
                        </div>

                    </div>


                    <!-- THUMBNAIL -->

                    <div class="form-group">

                        <label for="thumbnail">

                            Thumbnail Artikel

                            <span class="required">
                                *
                            </span>

                        </label>

                        <?php

                        $thumbnailRequired   = true;
                        $currentThumbnailUrl = '';

                        require __DIR__ . '/partials/thumbnail-cropper.php';

                        ?>

                    </div>


                    <!-- RINGKASAN -->

                    <div class="form-group">

                        <label for="excerpt">
                            Ringkasan Artikel
                        </label>

                        <textarea
                            id="excerpt"
                            name="excerpt"
                            maxlength="500"
                            placeholder="Tuliskan ringkasan singkat artikel..."
                        ><?= htmlspecialchars($excerpt) ?></textarea>

                        <div class="help-text">

                            Ringkasan singkat yang menjelaskan isi artikel.

                        </div>

                    </div>


                </div>

            </div>


            <!-- =================================================
                 ISI ARTIKEL
                 ================================================= -->

            <div class="card">

                <div class="card-header">

                    <h2>
                        Isi Artikel
                    </h2>

                </div>


                <div class="card-body">

                    <div class="form-group">

                        <label>

                            Isi Artikel

                            <span class="required">
                                *
                            </span>

                        </label>


                        <div id="editor"></div>


                        <input
                            type="hidden"
                            name="content"
                            id="content"
                            value=""
                        >


                        <div class="help-text">

                            Tulis artikel dengan lengkap.
                            Pastikan isi artikel sudah diperiksa
                            sebelum dikirim ke Admin.

                        </div>

                    </div>

                </div>


                <!-- ACTION -->

                <div class="form-actions">

                    <a
                        href="<?= htmlspecialchars($baseUrl) ?>/client/dashboard"
                        class="btn btn-cancel"
                    >
                        Batal
                    </a>


                    <div class="action-right">

                        <button
                            type="submit"
                            name="action"
                            value="draft"
                            class="btn btn-draft"
                        >
                            Simpan Draft
                        </button>


                        <button
                            type="submit"
                            name="action"
                            value="submit"
                            class="btn btn-submit"
                        >
                            Kirim Artikel
                        </button>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 PANDUAN
                 ================================================= -->

            <div class="card">

                <div class="card-body">

                    <div class="guide">

                        <div class="guide-title">
                            Panduan Penulisan
                        </div>

                        <ul>

                            <li>
                                Gunakan judul yang jelas dan sesuai
                                dengan isi artikel.
                            </li>

                            <li>
                                Pilih kategori dan tag yang sesuai.
                            </li>

                            <li>
                                Gunakan thumbnail dengan rasio 16:9.
                            </li>

                            <li>
                                Pastikan isi artikel sudah diperiksa
                                sebelum dikirim.
                            </li>

                            <li>
                                Gunakan
                                <strong>Simpan Draft</strong>
                                jika artikel belum selesai.
                            </li>

                            <li>
                                Gunakan
                                <strong>Kirim Artikel</strong>
                                jika artikel sudah siap direview Admin.
                            </li>

                        </ul>

                    </div>

                </div>

            </div>


        </form>


    </main>

</div>


<!-- =============================================================
     QUILL
     ============================================================= -->

<script
    src="https://cdn.quilljs.com/1.3.7/quill.min.js"
></script>


<script>

    /*
    |--------------------------------------------------------------------------
    | QUILL EDITOR
    |--------------------------------------------------------------------------
    */

    const quill = new Quill(
        '#editor',
        {
            theme: 'snow',

            placeholder:
                'Mulai tulis artikel Anda di sini...',

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

    <?php if (!empty($_POST['content'])): ?>

        quill.root.innerHTML =
            <?= json_encode(
                $_POST['content'],
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            ) ?>;

    <?php endif; ?>


    /*
    |--------------------------------------------------------------------------
    | SUBMIT FORM
    |--------------------------------------------------------------------------
    | Preview thumbnail sekarang ditangani partial
    | thumbnail-cropper.php, jadi tidak perlu ditulis di sini.
    |--------------------------------------------------------------------------
    */

    const articleForm =
        document.getElementById(
            'articleForm'
        );


    articleForm.addEventListener(
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


</body>

</html>