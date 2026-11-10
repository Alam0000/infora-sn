<?php

$user = $user ?? [];
$articles = $articles ?? [];

$baseUrl = Auth::baseUrl();

function revisionDate($date)
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

    <title>
        Revisi Artikel - INFORA SN
    </title>

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

        /* INFO */

        .info-box {
            background: #fff3cd;
            border: 1px solid #ffe69c;
            color: #664d03;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 25px;
        }

        .info-box strong {
            display: block;
            margin-bottom: 5px;
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
        }

        .card-header h2 {
            margin: 0;
            font-size: 20px;
        }

        /* ARTICLE */

        .article-item {
            padding: 22px 20px;
            border-bottom: 1px solid #eee;
        }

        .article-item:last-child {
            border-bottom: none;
        }

        .article-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #222;
        }

        .article-meta {
            font-size: 13px;
            color: #777;
            margin-bottom: 15px;
        }

        .revision-box {
            background: #fafafa;
            border-left: 4px solid #111827;
            padding: 15px 18px;
            margin-bottom: 18px;
        }

        .revision-label {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #777;
            margin-bottom: 7px;
        }

        .revision-note {
            line-height: 1.6;
            white-space: pre-line;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 7px;
            text-decoration: none;
            background: #111827;
            color: #fff;
            font-size: 14px;
        }

        .btn:hover {
            opacity: 0.9;
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
            margin: 0;
        }

        /* RESPONSIVE */

        @media (max-width: 800px) {

            .layout {
                display: block;
            }

            .sidebar {
                width: 100%;
                border-right: none;
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

            <a
                href="<?= $baseUrl ?>/client/dashboard"
            >
                Dashboard
            </a>

            <a
                href="<?= $baseUrl ?>/client/article/create"
            >
                Tulis Artikel
            </a>

            <a
                href="<?= $baseUrl ?>/client/articles"
            >
                Status Artikel
            </a>

            <a
                href="<?= $baseUrl ?>/client/revisions"
                class="active"
            >
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
                    Revisi Artikel
                </h1>

                <p>
                    Periksa catatan editor dan perbaiki artikel Anda.
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


        <!-- INFO -->

        <div class="info-box">

            <strong>
                Artikel yang membutuhkan revisi
            </strong>

            Perbaiki artikel berdasarkan catatan dari
            tim redaksi sebelum mengirimkannya kembali
            untuk proses review.

        </div>


        <!-- ARTICLE LIST -->

        <section class="card">

            <div class="card-header">

                <h2>
                    Artikel Butuh Revisi
                </h2>

            </div>


            <?php if (empty($articles)): ?>

                <div class="empty">

                    <h3>
                        Tidak Ada Artikel yang Perlu Direvisi
                    </h3>

                    <p>
                        Saat ini tidak ada artikel Anda
                        yang membutuhkan revisi.
                    </p>

                </div>

            <?php else: ?>


                <?php foreach ($articles as $article): ?>

                    <article class="article-item">

                        <div class="article-title">

                            <?= htmlspecialchars(
                                $article['title'] ?? 'Tanpa Judul'
                            ) ?>

                        </div>

                        <div class="article-meta">

                            Kategori:
                            <?= htmlspecialchars(
                                $article['category_name'] ?? '-'
                            ) ?>

                            &nbsp; • &nbsp;

                            Diperbarui:
                            <?= htmlspecialchars(
                                revisionDate(
                                    $article['updated_at'] ?? null
                                )
                            ) ?>

                        </div>


                        <div class="revision-box">

                            <div class="revision-label">
                                Catatan Revisi
                            </div>

                            <div class="revision-note">

                                <?= htmlspecialchars(
                                    $article['revision_note']
                                    ?? 'Tidak ada catatan revisi.'
                                ) ?>

                            </div>

                        </div>


                        <a
                            href="<?= $baseUrl ?>/client/article/edit/<?= (int) $article['id'] ?>"
                            class="btn"
                        >
                            Perbaiki Artikel
                        </a>

                    </article>

                <?php endforeach; ?>


            <?php endif; ?>

        </section>

    </main>

</div>

</body>

</html>