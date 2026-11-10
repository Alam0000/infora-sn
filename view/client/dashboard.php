<?php

$user = $user ?? [];
$stats = $stats ?? [];
$articles = $articles ?? [];

$pageTitle = 'Dashboard Penulis - INFORA SN';

$baseUrl = Auth::baseUrl();

function statusLabel($status)
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

function statusClass($status)
{
    return strtolower(
        str_replace('_', '-', $status)
    );
}

require __DIR__ . '/layout/header.php';

require __DIR__ . '/layout/sidebar.php';
?>

<main class="main">

    <!-- TOPBAR -->
    <div class="topbar">

        <div>

            <div class="topbar-kicker">
                Dashboard Redaksi
            </div>

            <h1>
                Dashboard Penulis
            </h1>

            <p>
                Kelola artikel dan naskah Anda
                di INFORA SN.
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


    <!-- STATISTICS -->
    <section class="stats">

        <div class="stat-card">
            <div class="stat-title">
                Total Artikel
            </div>

            <div class="stat-number">
                <?= $stats['total'] ?? 0 ?>
            </div>
        </div>


        <div class="stat-card">
            <div class="stat-title">
                Draft
            </div>

            <div class="stat-number">
                <?= $stats['draft'] ?? 0 ?>
            </div>
        </div>


        <div class="stat-card">
            <div class="stat-title">
                Menunggu Review
            </div>

            <div class="stat-number">
                <?= $stats['menunggu_review'] ?? 0 ?>
            </div>
        </div>


        <div class="stat-card">
            <div class="stat-title">
                Butuh Revisi
            </div>

            <div class="stat-number">
                <?= $stats['revisi'] ?? 0 ?>
            </div>
        </div>


        <div class="stat-card">
            <div class="stat-title">
                Published
            </div>

            <div class="stat-number">
                <?= $stats['published'] ?? 0 ?>
            </div>
        </div>

    </section>


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

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Diperbarui</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($articles as $article): ?>

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
                                        statusClass(
                                            $article['status'] ?? ''
                                        )
                                    ) ?>"
                                >
                                    <?= htmlspecialchars(
                                        statusLabel(
                                            $article['status'] ?? ''
                                        )
                                    ) ?>
                                </span>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $article['updated_at'] ?? '-'
                                ) ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </section>

</main>

<?php
require __DIR__ . '/layout/footer.php';
?>