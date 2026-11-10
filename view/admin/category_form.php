<?php

$baseUrl = Auth::baseUrl();

$isEdit = !empty($category);

$categoryId = $isEdit
    ? (int) ($category['id'] ?? 0)
    : 0;

$name = $isEdit
    ? ($category['name'] ?? '')
    : '';

$slug = $isEdit
    ? ($category['slug'] ?? '')
    : '';

$description = $isEdit
    ? ($category['description'] ?? '')
    : '';

$parentId = $isEdit
    ? ($category['parent_id'] ?? '')
    : '';

$parentCategories = $parentCategories ?? [];

$errors = $errors ?? [];

?>

<?php include __DIR__ . '/layout/sidebar.php'; ?>

<div class="admin-main">

    <?php
    $pageTitle = $isEdit
        ? 'Edit Kategori'
        : 'Tambah Kategori';

    $pageDescription = $isEdit
        ? 'Perbarui informasi kategori berita INFORA SN.'
        : 'Tambahkan kategori berita baru ke portal INFORA SN.';
    ?>

    <?php include __DIR__ . '/layout/header.php'; ?>

    <main class="admin-content">

        <!-- HEADER -->
        <div class="page-header">

            <div>
                <h1>
                    <?= $isEdit ? 'Edit Kategori' : 'Tambah Kategori' ?>
                </h1>

                <p>
                    <?= $isEdit
                        ? 'Perbarui informasi kategori berita.'
                        : 'Tambahkan kategori berita baru ke portal INFORA SN.'
                    ?>
                </p>
            </div>

            <a
                href="<?= $baseUrl ?>/admin/categories"
                class="back-button"
            >
                ← Kembali
            </a>

        </div>


        <!-- ERROR -->
        <?php if (!empty($errors)): ?>

            <div class="error-box">

                <?php foreach ($errors as $error): ?>

                    <div>
                        <?= htmlspecialchars($error) ?>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <!-- FORM -->
        <section class="form-card">

            <form
                method="POST"
                action="<?= $isEdit
                    ? $baseUrl . '/admin/category/' . $categoryId . '/update'
                    : $baseUrl . '/admin/category/store'
                ?>"
            >

                <!-- NAMA -->
                <div class="form-group">

                    <label for="name">
                        NAMA KATEGORI
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?= htmlspecialchars($name) ?>"
                        placeholder="Contoh: Politik"
                        required
                    >

                </div>


                <!-- SLUG -->
                <div class="form-group">

                    <label for="slug">
                        SLUG
                    </label>

                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="<?= htmlspecialchars($slug) ?>"
                        placeholder="Contoh: politik"
                    >

                    <small>
                        Slug digunakan untuk URL kategori.
                        Contoh: politik, ekonomi, teknologi.
                    </small>

                </div>


                <!-- DESKRIPSI -->
                <div class="form-group">

                    <label for="description">
                        DESKRIPSI
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        placeholder="Masukkan deskripsi kategori..."
                    ><?= htmlspecialchars($description) ?></textarea>

                </div>


                <!-- KATEGORI INDUK -->
                <div class="form-group">

                    <label for="parent_id">
                        KATEGORI INDUK
                    </label>

                    <select
                        id="parent_id"
                        name="parent_id"
                    >

                        <option value="">
                            -- Tidak ada (Kategori Utama) --
                        </option>

                        <?php foreach ($parentCategories as $parent): ?>

                            <option
                                value="<?= (int) $parent['id'] ?>"
                                <?= (string) $parentId === (string) $parent['id']
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                <?= htmlspecialchars($parent['name']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <small>
                        Kosongkan jika ini kategori utama (seperti "Bola").
                        Pilih induk jika ini sub-kategori
                        (seperti "Liga Inggris" di bawah "Bola").
                    </small>

                </div>


                <!-- ACTION -->
                <div class="form-actions">

                    <a
                        href="<?= $baseUrl ?>/admin/categories"
                        class="btn-cancel"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn-submit"
                    >
                        <?= $isEdit
                            ? 'Simpan Perubahan'
                            : 'Simpan Kategori'
                        ?>
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>


<style>

.admin-main {
    margin-left: 245px;
    min-height: 100vh;
    background: #f5f6f8;
}

.admin-content {
    padding: 32px 44px;
    background: #f5f6f8;
    min-height: calc(100vh - 80px);
    box-sizing: border-box;
}


/* =========================
   HEADER
========================= */

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 28px;
}

.page-header h1 {
    margin: 0 0 8px;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 30px;
    font-weight: 900;
    color: #111111;
}

.page-header p {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 13px;
    color: #777777;
}


/* =========================
   BACK BUTTON
========================= */

.back-button {
    display: inline-flex;
    align-items: center;
    padding: 11px 17px;
    background: #ffffff;
    border: 1px solid #d9d9d9;
    color: #222222;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    transition: 0.2s ease;
}

.back-button:hover {
    background: #111111;
    color: #ffffff;
}


/* =========================
   ERROR
========================= */

.error-box {
    margin-bottom: 20px;
    padding: 15px 18px;
    background: #fff1f1;
    border: 1px solid #e3aaaa;
    color: #a00000;
    font-size: 13px;
    line-height: 1.6;
}


/* =========================
   FORM CARD
========================= */

.form-card {
    max-width: 900px;
    background: #ffffff;
    border: 1px solid #dddddd;
    padding: 30px;
    box-sizing: border-box;
}


/* =========================
   FORM GROUP
========================= */

.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    margin-bottom: 9px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.2px;
    color: #555555;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    box-sizing: border-box;
    padding: 13px 14px;
    border: 1px solid #d5d5d5;
    background: #ffffff;
    color: #111111;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 13px;
    outline: none;
    transition: 0.2s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: #b6e600;
    box-shadow: 0 0 0 2px rgba(202, 255, 0, 0.18);
}

.form-group textarea {
    resize: vertical;
    min-height: 130px;
    line-height: 1.6;
}

.form-group small {
    display: block;
    margin-top: 7px;
    font-size: 11px;
    color: #888888;
}


/* =========================
   ACTION
========================= */

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 10px;
    border-top: 1px solid #eeeeee;
}

.btn-cancel,
.btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 0 20px;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
}

.btn-cancel {
    background: #eeeeee;
    border: 1px solid #dddddd;
    color: #333333;
}

.btn-cancel:hover {
    background: #dddddd;
}

.btn-submit {
    background: #caff00;
    border: 1px solid #b6e600;
    color: #111111;
}

.btn-submit:hover {
    background: #b9ec00;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 700px) {

    .admin-content {
        padding: 25px 20px;
    }

    .page-header {
        flex-direction: column;
        gap: 15px;
    }

    .form-card {
        padding: 20px;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-cancel,
    .btn-submit {
        width: 100%;
    }

}

</style>