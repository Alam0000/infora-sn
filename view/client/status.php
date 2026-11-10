<?php

$user = $user ?? [];
$articles = $articles ?? [];

$baseUrl = Auth::baseUrl();

function clientStatusLabel($status)
{
    $labels = [
        'DRAFT' => 'Draft',
        'MENUNGGU_REVIEW' => 'Menunggu Review',
        'REVISI' => 'Butuh Revisi',
        'DITOLAK' => 'Ditolak',
        'PUBLISHED' => 'Published'
    ];

    return $labels[$status] ?? $status;
}

function clientStatusClass($status)
{
    return strtolower(
        str_replace('_', '-', $status)
    );
}

function clientStatusDate($date)
{
    if (empty($date)) {
        return '-';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return $date;
    }

    return date('d/m/Y H:i', $timestamp);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Status Artikel - INFORA SN</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            color: #222;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */

        .sidebar {
            width: 250px;
            background: #fff;
            border-right: 1px solid #e5e5e5;
            padding: 25px 18px;
            flex-shrink: 0;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 35px;
        }

        .logo span {
            font-weight: normal;
        }

        .menu-title {
            font-size: 12px;
            color: #888;
            margin-bottom: 10px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .menu a {
            text-decoration: none;
            color: #444;
            padding: 12px 14px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .menu a:hover {
            background: #f0f2f5;
        }

        .menu a.active {
            background: #111827;
            color: #fff;
        }

        /* MAIN */

        .main {
            flex: 1;
            padding: 30px;
            min-width: 0;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        .topbar h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .topbar p {
            margin: 0;
            color: #777;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: bold;
        }

        .user-role {
            font-size: 13px;
            color: #777;
            margin-top: 4px;
        }

        /* CARD */

        .card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .card-header h2 {
            margin: 0;
            font-size: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 7px;
            text-decoration: none;
            background: #111827;
            color: #fff;
            font-size: 14px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* TABLE */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th,
        td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            font-size: 13px;
            color: #777;
            background: #fafafa;
            white-space: nowrap;
        }

        td {
            font-size: 14px;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #fafafa;
        }

        .article-title {
            font-weight: bold;
            color: #222;
            text-decoration: none;
        }

        .article-title:hover {
            text-decoration: underline;
        }

        /* STATUS */

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status.draft {
            background: #eee;
            color: #555;
        }

        .status.menunggu-review {
            background: #fff3cd;
            color: #856404;
        }

        .status.revisi {
            background: #ffe5e5;
            color: #a61b1b;
        }

        .status.ditolak {
            background: #f8d7da;
            color: #842029;
        }

        .status.published {
            background: #d1e7dd;
            color: #0f5132;
        }

        /* ACTION */

        .action {
            white-space: nowrap;
        }

        .action a {
            color: #111827;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }

        .action a:hover {
            text-decoration: underline;
        }

        /* EMPTY */

        .empty {
            padding: 60px 30px;
            text-align: center;
            color: #777;
        }

        .empty h3 {
            margin: 0 0 8px;
            color: #333;
        }

        .empty p {
            margin: 0 0 20px;
        }

        /* RESPONSIVE */

        @media (max-width: 800px) {

            .layout {
                display: block;
            }

            .sidebar {
                width: 100%;
                border-right: 0;
                border-bottom: 1px solid #e5e5e5;
            }

            .main {
                padding: 20px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .user-info {
                text-align: left;
            }

        }

    </style>

</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo">
            INFORA <span>SN</span>
        </div>

        <div class="menu-title">
            MENU PENULIS
        </div>

        <nav class="menu">

            <a href="<?= $baseUrl ?>/client/dashboard">
                Dashboard
            </a>

            <a href="<?= $baseUrl ?>/client/article/create">
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

            <a href="<?= $baseUrl ?>/logout">
                Logout
            </a>

        </nav>

    </aside>


    <!-- MAIN -->

    <main class="main">

        <div class="topbar">

            <div>

                <h1>
                    Status Artikel
                </h1>

                <p>
                    Pantau status seluruh artikel yang Anda kirimkan.
                </p>

            </div>

            <div class="user-info">

                <div class="user-name">
                    <?= htmlspecialchars(
                        $user['name'] ?? 'User'
                    ) ?>
                </div>

                <div class="user-role">
                    Penulis
                </div>

            </div>

        </div>


        <!-- ARTICLE LIST -->

        <section class="card">

            <div class="card-header">

                <h2>
                    Artikel Saya
                </h2>

                <a
                    href="<?= $baseUrl ?>/client/article/create"
                    class="btn"
                >
                    + Tulis Artikel
                </a>

            </div>


            <?php if (empty($articles)): ?>

                <div class="empty">

                    <h3>
                        Belum Ada Artikel
                    </h3>

                    <p>
                        Anda belum memiliki artikel.
                    </p>

                    <a
                        href="<?= $baseUrl ?>/client/article/create"
                        class="btn"
                    >
                        Tulis Artikel Pertama
                    </a>

                </div>

            <?php else: ?>

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Judul
                                </th>

                                <th>
                                    Kategori
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Diperbarui
                                </th>

                                <th>
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($articles as $article): ?>

                            <?php
                            $status = $article['status'] ?? '';
                            ?>

                            <tr>

                                <td>

                                    <a
                                        class="article-title"
                                        href="<?= $baseUrl ?>/client/article/edit/<?= (int) $article['id'] ?>"
                                    >
                                        <?= htmlspecialchars(
                                            $article['title'] ?? 'Tanpa Judul'
                                        ) ?>
                                    </a>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $article['category_name'] ?? '-'
                                    ) ?>

                                </td>

                                <td>

                                    <span
                                        class="status <?= htmlspecialchars(
                                            clientStatusClass($status)
                                        ) ?>"
                                    >

                                        <?= htmlspecialchars(
                                            clientStatusLabel($status)
                                        ) ?>

                                    </span>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        clientStatusDate(
                                            $article['updated_at'] ?? null
                                        )
                                    ) ?>

                                </td>

                                <td class="action">

                                    <a
                                        href="<?= $baseUrl ?>/client/article/edit/<?= (int) $article['id'] ?>"
                                    >
                                        Lihat / Edit
                                    </a>

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

</body>

</html>