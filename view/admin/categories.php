<?php

$baseUrl = Auth::baseUrl();

$categories = $categories ?? [];

$keyword = $keyword ?? '';

$success = $_GET['success'] ?? '';

$error = $_GET['error'] ?? '';

?>

<?php include __DIR__ . '/layout/sidebar.php'; ?>

<div class="admin-main">

    <?php
    $pageTitle = 'Kelola Kategori Berita';
    $pageDescription =
        'Kelola rubrikasi dan kategori berita INFORA SN.';
    ?>

    <?php include __DIR__ . '/layout/header.php'; ?>

    <main class="admin-content">

        <div class="page-heading">

            <div>

                <div class="breadcrumb">
                    DASHBOARD REDAKSI / MANAJEMEN
                </div>

                <h1>
                    Kelola Kategori Berita
                </h1>

                <p>
                    Atur kategori yang digunakan
                    pada artikel portal.
                </p>

            </div>

            <a
                href="<?= $baseUrl ?>/admin/category/create"
                class="primary-button"
            >
                + Tambah Kategori
            </a>

        </div>


        <?php if ($success !== ''): ?>

            <div class="alert success">
                <?= htmlspecialchars($success) ?>
            </div>

        <?php endif; ?>


        <?php if ($error !== ''): ?>

            <div class="alert error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <section class="content-card">

            <div class="card-toolbar">

                <form
                    method="GET"
                    action="<?= $baseUrl ?>/admin/categories"
                >

                    <input
                        type="text"
                        name="q"
                        value="<?= htmlspecialchars($keyword) ?>"
                        placeholder="Cari kategori..."
                    >

                    <button type="submit">
                        Cari
                    </button>

                </form>

            </div>


            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>
                                NO
                            </th>

                            <th>
                                KATEGORI
                            </th>

                            <th>
                                SLUG
                            </th>

                            <th>
                                DESKRIPSI
                            </th>

                            <th>
                                ARTIKEL
                            </th>

                            <th>
                                AKSI
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php if (empty($categories)): ?>

                            <tr>

                                <td
                                    colspan="6"
                                    class="empty"
                                >
                                    Belum ada kategori.
                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach (
                                $categories as $index => $category
                            ): ?>

                                <tr>

                                    <td>
                                        <?= $index + 1 ?>
                                    </td>

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $category['name']
                                            ) ?>
                                        </strong>

                                    </td>

                                    <td>

                                        <code>
                                            <?= htmlspecialchars(
                                                $category['slug']
                                            ) ?>
                                        </code>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $category['description']
                                                ?: '-'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= (int) (
                                            $category['article_count']
                                                ?? 0
                                        ) ?>
                                    </td>

                                    <td>

                                        <div class="actions">

                                            <a
                                                href="<?= $baseUrl ?>/admin/category/<?= (int) $category['id'] ?>/edit"
                                                class="edit"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="<?= $baseUrl ?>/admin/category/<?= (int) $category['id'] ?>/delete"
                                                onsubmit="return confirm('Hapus kategori ini?')"
                                            >

                                                <button
                                                    type="submit"
                                                    class="delete"
                                                >
                                                    Hapus
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>


<style>

.admin-main {
    margin-left: 245px;
    min-height: 100vh;
    background: #f4f5f7;
}

.admin-content {
    padding: 32px 42px 60px;
}

.page-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 20px;
    margin-bottom: 25px;
}

.breadcrumb {
    margin-bottom: 8px;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1.3px;
    color: #999;
}

.page-heading h1 {
    margin: 0;
    font-size: 27px;
}

.page-heading p {
    margin: 7px 0 0;
    font-size: 11px;
    color: #777;
}

.primary-button {
    display: inline-flex;
    align-items: center;
    min-height: 40px;
    padding: 0 18px;
    background: #caff00;
    color: #111;
    text-decoration: none;
    font-size: 9px;
    font-weight: 800;
}

.alert {
    padding: 13px 15px;
    margin-bottom: 18px;
    font-size: 10px;
}

.alert.success {
    background: #f0ffd0;
    border: 1px solid #caff00;
}

.alert.error {
    background: #fff0f0;
    border: 1px solid #e0baba;
    color: #8b2929;
}

.content-card {
    background: #fff;
    border: 1px solid #ddd;
}

.card-toolbar {
    padding: 18px;
    border-bottom: 1px solid #e4e4e4;
}

.card-toolbar form {
    display: flex;
    gap: 8px;
}

.card-toolbar input {
    flex: 1;
    height: 40px;
    border: 1px solid #d5d5d5;
    padding: 0 12px;
    font-size: 10px;
}

.card-toolbar button {
    border: 0;
    padding: 0 18px;
    background: #111;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    padding: 13px 15px;
    text-align: left;
    background: #fafafa;
    border-bottom: 1px solid #ddd;
    font-size: 8px;
    letter-spacing: 1px;
    color: #888;
}

td {
    padding: 15px;
    border-bottom: 1px solid #eee;
    font-size: 10px;
    color: #555;
}

td strong {
    color: #111;
}

code {
    padding: 4px 7px;
    background: #f1f1f1;
    font-size: 9px;
}

.actions {
    display: flex;
    gap: 6px;
}

.actions a,
.actions button {
    padding: 7px 10px;
    border: 0;
    font-size: 9px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}

.edit {
    background: #eee;
    color: #222;
}

.delete {
    background: #ffe4e4;
    color: #a12626;
}

.empty {
    text-align: center;
    padding: 35px !important;
    color: #999;
}

</style>