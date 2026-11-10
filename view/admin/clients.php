<?php

$baseUrl = Auth::baseUrl();

$clients = $clients ?? [];

$stats = $stats ?? [
    'total' => 0,
    'active' => 0,
    'inactive' => 0
];

$keyword = $keyword ?? '';

$status = $status ?? '';

$success = $_GET['success'] ?? '';

$error = $_GET['error'] ?? '';

?>

<?php include __DIR__ . '/layout/sidebar.php'; ?>

<div class="admin-main">

    <?php
    $pageTitle = 'Kelola Penulis & Mitra';
    $pageDescription =
        'Kelola akun penulis dan kontributor INFORA SN.';
    ?>

    <?php include __DIR__ . '/layout/header.php'; ?>

    <main class="admin-content">

        <div class="page-heading">

            <div>
                <div class="breadcrumb">
                    DASHBOARD REDAKSI / MANAJEMEN
                </div>

                <h1>
                    Kelola Penulis &amp; Mitra
                </h1>

                <p>
                    Kelola akun penulis yang terdaftar
                    pada sistem INFORA SN.
                </p>
            </div>

            <a
                href="<?= $baseUrl ?>/admin/client/create"
                class="primary-button"
            >
                + Tambah Penulis
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


        <div class="stats-grid">

            <div class="stat-card">
                <div class="stat-label">
                    TOTAL PENULIS
                </div>
                <div class="stat-number">
                    <?= $stats['total'] ?>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">
                    AKTIF
                </div>
                <div class="stat-number">
                    <?= $stats['active'] ?>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">
                    NONAKTIF
                </div>
                <div class="stat-number">
                    <?= $stats['inactive'] ?>
                </div>
            </div>

        </div>


        <section class="content-card">

            <div class="card-toolbar">

                <form
                    method="GET"
                    action="<?= $baseUrl ?>/admin/clients"
                >

                    <input
                        type="text"
                        name="q"
                        value="<?= htmlspecialchars($keyword) ?>"
                        placeholder="Cari nama atau email..."
                    >

                    <select name="status">

                        <option value="">
                            Semua Status
                        </option>

                        <option
                            value="active"
                            <?= $status === 'active' ? 'selected' : '' ?>
                        >
                            Aktif
                        </option>

                        <option
                            value="inactive"
                            <?= $status === 'inactive' ? 'selected' : '' ?>
                        >
                            Nonaktif
                        </option>

                    </select>

                    <button type="submit">
                        Cari
                    </button>

                </form>

            </div>


            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>
                            <th>NO</th>
                            <th>PENULIS</th>
                            <th>EMAIL</th>
                            <th>STATUS</th>
                            <th>TERDAFTAR</th>
                            <th>AKSI</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($clients)): ?>

                            <tr>
                                <td
                                    colspan="6"
                                    class="empty"
                                >
                                    Belum ada penulis.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach (
                                $clients as $index => $client
                            ): ?>

                                <tr>

                                    <td>
                                        <?= $index + 1 ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars(
                                                $client['name']
                                            ) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $client['email']
                                        ) ?>
                                    </td>

                                    <td>

                                        <span
                                            class="status <?= $client['account_status'] ?>"
                                        >
                                            <?= strtoupper(
                                                $client['account_status']
                                            ) ?>
                                        </span>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            date(
                                                'd M Y',
                                                strtotime(
                                                    $client['created_at']
                                                )
                                            )
                                        ) ?>
                                    </td>

                                    <td>

                                        <div class="actions">

                                            <a
                                                href="<?= $baseUrl ?>/admin/client/<?= (int) $client['id'] ?>/edit"
                                                class="edit"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="<?= $baseUrl ?>/admin/client/<?= (int) $client['id'] ?>/delete"
                                                onsubmit="return confirm('Hapus penulis ini?')"
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
    font-weight: 800;
}

.page-heading p {
    margin: 7px 0 0;
    font-size: 11px;
    color: #777;
}

.primary-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 0 18px;
    background: #caff00;
    color: #111;
    text-decoration: none;
    font-size: 10px;
    font-weight: 800;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}

.stat-card {
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
}

.stat-label {
    font-size: 9px;
    letter-spacing: 1px;
    color: #888;
}

.stat-number {
    margin-top: 9px;
    font-size: 28px;
    font-weight: 800;
}

.alert {
    padding: 13px 16px;
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
    color: #8b2525;
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

.card-toolbar input,
.card-toolbar select {
    height: 40px;
    border: 1px solid #d5d5d5;
    padding: 0 12px;
    font-size: 10px;
}

.card-toolbar input {
    flex: 1;
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

.status {
    display: inline-block;
    padding: 5px 8px;
    font-size: 8px;
    font-weight: 800;
}

.status.active {
    background: #eaffb5;
    color: #3e5200;
}

.status.inactive {
    background: #eee;
    color: #777;
}

.actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

.actions a,
.actions button {
    border: 0;
    padding: 7px 10px;
    font-size: 9px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}

.actions .edit {
    background: #eee;
    color: #222;
}

.actions .delete {
    background: #ffe4e4;
    color: #a12626;
}

.empty {
    text-align: center;
    padding: 35px !important;
    color: #999;
}

@media (max-width: 800px) {

    .admin-main {
        margin-left: 215px;
    }

    .admin-content {
        padding: 25px 20px;
    }

    .page-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .card-toolbar form {
        flex-direction: column;
    }

}

</style>