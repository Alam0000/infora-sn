<?php

$pageTitle = 'Kelola Iklan';
$pageDescription = 'Kelola banner iklan yang tampil di sisi kanan halaman publik INFORA SN.';

$user = $user ?? Auth::user();
$ads = $ads ?? [];

$baseUrl = Auth::baseUrl();

$success = trim((string) ($_GET['success'] ?? ''));
$error   = trim((string) ($_GET['error'] ?? ''));


/*
|--------------------------------------------------------------------------
| HELPER URL GAMBAR IKLAN
|--------------------------------------------------------------------------
*/

function adminAdImageUrl($path)
{
    $path = trim((string) ($path ?? ''));

    if ($path === '') {
        return '';
    }

    /*
    | Jika sudah URL lengkap
    */
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    /*
    | Normalisasi slash
    */
    $path = str_replace('\\', '/', $path);
    $path = ltrim($path, '/');

    /*
    | Ambil base URL dengan aman
    */
    $baseUrl = Auth::baseUrl();

    if (!is_string($baseUrl)) {
        $baseUrl = '';
    }

    $baseUrl = rtrim($baseUrl, '/');

    /*
    | Jika path sudah mengandung base URL
    */
    $normalizedBase = trim(str_replace('\\', '/', $baseUrl), '/');

    if (
        $normalizedBase !== '' &&
        strpos($path, $normalizedBase . '/') === 0
    ) {
        return '/' . $path;
    }

    /*
    | Jika aplikasi berada di root
    */
    if ($baseUrl === '') {
        return '/' . $path;
    }

    return $baseUrl . '/' . $path;
}

?>

<?php include __DIR__ . '/layout/sidebar.php'; ?>

<div class="admin-page-wrapper">

    <?php include __DIR__ . '/layout/header.php'; ?>

    <main
        class="admin-main-content ads-page"
        style="padding: 32px 42px 60px !important; margin-top: 72px !important; box-sizing: border-box !important;"
    >

        <!-- =========================================================
             HEADER HALAMAN
        ========================================================== -->

        <section class="ads-header">

            <div class="ads-header-text">

                <div class="ads-kicker">
                    KONFIGURASI PORTAL
                </div>

                <h1>
                    Kelola Iklan
                </h1>

                <p>
                    Atur banner iklan yang tampil di kolom kanan portal publik INFORA SN.
                </p>

            </div>

            <div class="ads-header-action">

                <a
                    href="<?= htmlspecialchars($baseUrl) ?>/admin/ads/create"
                    class="ads-button ads-button-primary"
                >
                    <span>+</span>
                    Tambah Iklan
                </a>

            </div>

        </section>


        <!-- =========================================================
             ALERT
        ========================================================== -->

        <?php if ($success !== ''): ?>

            <div class="ads-alert ads-alert-success">

                <span class="ads-alert-icon">✓</span>

                <span>
                    <?= htmlspecialchars($success) ?>
                </span>

            </div>

        <?php endif; ?>


        <?php if ($error !== ''): ?>

            <div class="ads-alert ads-alert-error">

                <span class="ads-alert-icon">!</span>

                <span>
                    <?= htmlspecialchars($error) ?>
                </span>

            </div>

        <?php endif; ?>


        <!-- =========================================================
             INFO
        ========================================================== -->

        <section class="ads-info">

            <div class="ads-info-icon">
                ▧
            </div>

            <div class="ads-info-content">

                <strong>
                    Format Banner
                </strong>

                <p>
                    Gunakan gambar sekitar
                    <b>300 × 250 px</b>
                    untuk tampilan sidebar.
                    Gambar akan dapat diklik dan mengarah ke URL tujuan iklan.
                </p>

            </div>

        </section>


        <!-- =========================================================
             TABLE CARD
        ========================================================== -->

        <section class="ads-card">

            <div class="ads-card-header">

                <div>

                    <div class="ads-card-title">
                        Daftar Banner
                    </div>

                    <div class="ads-card-subtitle">
                        <?= count($ads) ?> iklan terdaftar
                    </div>

                </div>

                <div class="ads-card-label">
                    IKLAN PORTAL
                </div>

            </div>


            <?php if (empty($ads)): ?>

                <!-- =================================================
                     EMPTY STATE
                ================================================== -->

                <div class="ads-empty">

                    <div class="ads-empty-icon">
                        ▧
                    </div>

                    <h3>
                        Belum Ada Iklan
                    </h3>

                    <p>
                        Tambahkan banner pertama untuk ditampilkan
                        pada halaman Home INFORA SN.
                    </p>

                    <a
                        href="<?= htmlspecialchars($baseUrl) ?>/admin/ads/create"
                        class="ads-button ads-button-primary"
                    >
                        + Tambah Iklan
                    </a>

                </div>

            <?php else: ?>

                <!-- =================================================
                     TABLE
                ================================================== -->

                <div class="ads-table-container">

                    <table class="ads-table">

                        <thead>

                            <tr>

                                <th class="ads-col-banner">
                                    BANNER
                                </th>

                                <th class="ads-col-name">
                                    NAMA IKLAN
                                </th>

                                <th class="ads-col-url">
                                    URL TUJUAN
                                </th>

                                <th class="ads-col-order">
                                    URUTAN
                                </th>

                                <th class="ads-col-status">
                                    STATUS
                                </th>

                                <th class="ads-col-action">
                                    AKSI
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($ads as $ad): ?>

                            <?php

                            $image = adminAdImageUrl(
                                $ad['image_path'] ?? ''
                            );

                            $title = $ad['title'] ?? '';

                            $targetUrl = $ad['target_url'] ?? '';

                            $status = $ad['status'] ?? 'INACTIVE';

                            $sortOrder = (int) (
                                $ad['sort_order'] ?? 0
                            );

                            $adId = (int) (
                                $ad['id'] ?? 0
                            );

                            ?>

                            <tr>

                                <!-- =========================
                                     BANNER
                                ========================== -->

                                <td>

                                    <div class="ads-banner-preview">

                                        <?php if ($image !== ''): ?>

                                            <img
                                                src="<?= htmlspecialchars($image) ?>"
                                                alt="<?= htmlspecialchars($title) ?>"
                                            >

                                        <?php else: ?>

                                            <div class="ads-no-image">
                                                Tidak ada gambar
                                            </div>

                                        <?php endif; ?>

                                    </div>

                                </td>


                                <!-- =========================
                                     NAMA
                                ========================== -->

                                <td>

                                    <div class="ads-name">

                                        <?= htmlspecialchars($title) ?>

                                    </div>

                                </td>


                                <!-- =========================
                                     URL
                                ========================== -->

                                <td>

                                    <?php if ($targetUrl !== ''): ?>

                                        <a
                                            href="<?= htmlspecialchars($targetUrl) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="ads-url"
                                        >
                                            <?= htmlspecialchars($targetUrl) ?>
                                        </a>

                                    <?php else: ?>

                                        <span class="ads-muted">
                                            -
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =========================
                                     URUTAN
                                ========================== -->

                                <td>

                                    <span class="ads-order">
                                        <?= $sortOrder ?>
                                    </span>

                                </td>


                                <!-- =========================
                                     STATUS
                                ========================== -->

                                <td>

                                    <?php if ($status === 'ACTIVE'): ?>

                                        <span class="ads-status ads-status-active">
                                            Aktif
                                        </span>

                                    <?php else: ?>

                                        <span class="ads-status ads-status-inactive">
                                            Nonaktif
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =========================
                                     AKSI
                                ========================== -->

                                <td>

                                    <div class="ads-actions">

                                        <a
                                            href="<?= htmlspecialchars($baseUrl) ?>/admin/ads/<?= $adId ?>/edit"
                                            class="ads-action ads-action-edit"
                                        >
                                            Edit
                                        </a>


                                        <form
                                            method="POST"
                                            action="<?= htmlspecialchars($baseUrl) ?>/admin/ads/<?= $adId ?>/toggle"
                                        >

                                            <button
                                                type="submit"
                                                class="ads-action ads-action-toggle"
                                            >
                                                <?= $status === 'ACTIVE'
                                                    ? 'Nonaktifkan'
                                                    : 'Aktifkan'
                                                ?>
                                            </button>

                                        </form>


                                        <form
                                            method="POST"
                                            action="<?= htmlspecialchars($baseUrl) ?>/admin/ads/<?= $adId ?>/delete"
                                            onsubmit="return confirm('Hapus iklan ini?');"
                                        >

                                            <button
                                                type="submit"
                                                class="ads-action ads-action-delete"
                                            >
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

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


<style>

/* ================================================================
   ADS PAGE
   CSS SENGAJA DI-SCOPE KE .ads-page
   AGAR TIDAK MERUSAK CSS DASHBOARD ADMIN
================================================================ */


/* ---------------------------------------------------------------
   FIX: jarak kosong besar di atas konten.

   Class global ".admin-main-content" (didefinisikan di luar file
   ini) sepertinya punya padding-top besar yang tidak cocok
   dengan header sticky 72px milik layout/header.php, sehingga
   jaraknya jadi dobel. Di-override di sini secara eksplisit,
   disamakan dengan padding halaman admin lain (mis. clients.php).
---------------------------------------------------------------- */

.ads-page {

    padding: 32px 42px 60px !important;

    margin-top: 72px !important;

    box-sizing: border-box;

}


/*
    FIX: header ikut ke-scroll ke bawah (harusnya diam di atas).

    "position: sticky" pada .admin-header (di layout/header.php)
    kemungkinan rusak karena salah satu parent globalnya
    (.admin-page-wrapper / .admin-main-content) punya "overflow"
    yang memotong konteks sticky-nya.

    Di halaman ini dipaksa jadi "fixed" terhadap viewport supaya
    pasti diam di atas, sisi kiri disesuaikan lebar sidebar (260px).
    .ads-page di atas sudah dikasih margin-top 72px sebagai
    kompensasi supaya konten tidak ketiban header.
*/

.admin-header {

    position: fixed !important;

    top: 0 !important;

    left: 260px !important;

    right: 0 !important;

    z-index: 950 !important;

}

@media (max-width: 900px) {

    .admin-header {
        left: 225px !important;
    }

}


/* ---------------------------------------------------------------
   HEADER
---------------------------------------------------------------- */

.ads-page .ads-header {

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 24px;

    margin-bottom: 22px;

}

.ads-page .ads-header-text {
    min-width: 0;
}

.ads-page .ads-kicker {

    margin-bottom: 6px;

    color: #174bc8;

    font-size: 9px;

    font-weight: 900;

    letter-spacing: .12em;

}

.ads-page .ads-header h1 {

    margin: 0 0 5px;

    color: #111;

    font-family: Georgia, "Times New Roman", serif;

    font-size: 30px;

    line-height: 1.15;

}

.ads-page .ads-header p {

    margin: 0;

    color: #777;

    font-size: 10px;

    line-height: 1.5;

}


/* ---------------------------------------------------------------
   BUTTON
---------------------------------------------------------------- */

.ads-page .ads-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 6px;

    min-height: 34px;

    padding: 0 14px;

    box-sizing: border-box;

    border: 1px solid transparent;

    text-decoration: none;

    font-size: 9px;

    font-weight: 900;

    line-height: 1;

    cursor: pointer;

    transition:
        background .15s ease,
        border-color .15s ease,
        color .15s ease;

}

.ads-page .ads-button-primary {

    background: #174bc8;

    color: #fff;

    border-color: #174bc8;

}

.ads-page .ads-button-primary:hover {

    background: #103da8;

    border-color: #103da8;

}

.ads-page .ads-button span {

    font-size: 13px;

    line-height: 1;

}


/* ---------------------------------------------------------------
   ALERT
---------------------------------------------------------------- */

.ads-page .ads-alert {

    display: flex;

    align-items: center;

    gap: 9px;

    margin-bottom: 16px;

    padding: 11px 13px;

    box-sizing: border-box;

    border: 1px solid;

    font-size: 10px;

    font-weight: 700;

}

.ads-page .ads-alert-icon {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    width: 18px;

    height: 18px;

    flex: 0 0 18px;

    border-radius: 50%;

    font-size: 9px;

    font-weight: 900;

}

.ads-page .ads-alert-success {

    background: #f2f9df;

    border-color: #d6e9a6;

    color: #4d6800;

}

.ads-page .ads-alert-success .ads-alert-icon {

    background: #cce98a;

    color: #385000;

}

.ads-page .ads-alert-error {

    background: #fff2f2;

    border-color: #efcccc;

    color: #a00000;

}

.ads-page .ads-alert-error .ads-alert-icon {

    background: #f3c8c8;

    color: #8a0000;

}


/* ---------------------------------------------------------------
   INFO BOX
---------------------------------------------------------------- */

.ads-page .ads-info {

    display: flex;

    align-items: flex-start;

    gap: 12px;

    margin-bottom: 18px;

    padding: 13px 15px;

    box-sizing: border-box;

    background: #fff;

    border: 1px solid #dddcd6;

}

.ads-page .ads-info-icon {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 30px;

    height: 30px;

    flex: 0 0 30px;

    background: #eef3ff;

    color: #174bc8;

    font-size: 16px;

    font-weight: 900;

}

.ads-page .ads-info-content {

    min-width: 0;

}

.ads-page .ads-info-content strong {

    display: block;

    margin-bottom: 3px;

    color: #222;

    font-size: 10px;

    font-weight: 900;

}

.ads-page .ads-info-content p {

    margin: 0;

    color: #777;

    font-size: 9px;

    line-height: 1.6;

}


/* ---------------------------------------------------------------
   MAIN CARD
---------------------------------------------------------------- */

.ads-page .ads-card {

    overflow: hidden;

    background: #fff;

    border: 1px solid #dddcd6;

}

.ads-page .ads-card-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    min-height: 64px;

    padding: 14px 18px;

    box-sizing: border-box;

    border-bottom: 1px solid #e5e4df;

}

.ads-page .ads-card-title {

    margin-bottom: 4px;

    color: #151515;

    font-family: Georgia, "Times New Roman", serif;

    font-size: 18px;

    font-weight: 700;

}

.ads-page .ads-card-subtitle {

    color: #888;

    font-size: 9px;

}

.ads-page .ads-card-label {

    padding: 6px 8px;

    background: #f2f4f7;

    color: #666;

    font-size: 8px;

    font-weight: 900;

    letter-spacing: .07em;

}


/* ---------------------------------------------------------------
   TABLE WRAPPER
---------------------------------------------------------------- */

.ads-page .ads-table-container {

    width: 100%;

    overflow-x: auto;

}


/* ---------------------------------------------------------------
   TABLE
---------------------------------------------------------------- */

.ads-page .ads-table {

    width: 100%;

    min-width: 920px;

    border-collapse: collapse;

    table-layout: fixed;

}

.ads-page .ads-table th {

    padding: 11px 13px;

    background: #f7f7f4;

    border-bottom: 1px solid #deddd8;

    color: #666;

    font-size: 8px;

    font-weight: 900;

    text-align: left;

    text-transform: uppercase;

    letter-spacing: .06em;

}

.ads-page .ads-table td {

    padding: 12px 13px;

    border-bottom: 1px solid #ecebe7;

    color: #333;

    font-size: 9px;

    vertical-align: middle;

}

.ads-page .ads-table tbody tr:last-child td {

    border-bottom: 0;

}

.ads-page .ads-table tbody tr:hover {

    background: #fafafa;

}


/* ---------------------------------------------------------------
   COLUMN WIDTH
---------------------------------------------------------------- */

.ads-page .ads-col-banner {

    width: 145px;

}

.ads-page .ads-col-name {

    width: 165px;

}

.ads-page .ads-col-url {

    width: 230px;

}

.ads-page .ads-col-order {

    width: 60px;

}

.ads-page .ads-col-status {

    width: 85px;

}

.ads-page .ads-col-action {

    width: 215px;

}


/* ---------------------------------------------------------------
   BANNER
---------------------------------------------------------------- */

.ads-page .ads-banner-preview {

    width: 120px;

    height: 82px;

    overflow: hidden;

    background: #eef0f2;

    border: 1px solid #e2e2de;

}

.ads-page .ads-banner-preview img {

    display: block;

    width: 100%;

    height: 100%;

    object-fit: cover;

}

.ads-page .ads-no-image {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 100%;

    height: 100%;

    padding: 8px;

    box-sizing: border-box;

    color: #999;

    font-size: 8px;

    text-align: center;

}


/* ---------------------------------------------------------------
   NAME
---------------------------------------------------------------- */

.ads-page .ads-name {

    color: #171717;

    font-size: 10px;

    font-weight: 800;

    line-height: 1.45;

}


/* ---------------------------------------------------------------
   URL
---------------------------------------------------------------- */

.ads-page .ads-url {

    display: block;

    max-width: 220px;

    overflow: hidden;

    color: #174bc8;

    font-size: 9px;

    line-height: 1.45;

    text-decoration: none;

    text-overflow: ellipsis;

    white-space: nowrap;

}

.ads-page .ads-url:hover {

    text-decoration: underline;

}

.ads-page .ads-muted {

    color: #aaa;

}


/* ---------------------------------------------------------------
   ORDER
---------------------------------------------------------------- */

.ads-page .ads-order {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-width: 24px;

    height: 24px;

    padding: 0 5px;

    box-sizing: border-box;

    background: #f2f3f4;

    color: #555;

    font-size: 9px;

    font-weight: 900;

}


/* ---------------------------------------------------------------
   STATUS
---------------------------------------------------------------- */

.ads-page .ads-status {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding: 5px 7px;

    font-size: 8px;

    font-weight: 900;

    white-space: nowrap;

}

.ads-page .ads-status-active {

    background: #e2f5b7;

    color: #4a6800;

}

.ads-page .ads-status-inactive {

    background: #eeeeec;

    color: #777;

}


/* ---------------------------------------------------------------
   ACTIONS
---------------------------------------------------------------- */

.ads-page .ads-actions {

    display: flex;

    align-items: center;

    flex-wrap: wrap;

    gap: 5px;

}

.ads-page .ads-actions form {

    display: inline-flex;

    margin: 0;

    padding: 0;

}

.ads-page .ads-action {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 27px;

    padding: 0 8px;

    box-sizing: border-box;

    background: #fff;

    border: 1px solid #d7d7d2;

    color: #333;

    font-family: inherit;

    font-size: 8px;

    font-weight: 900;

    line-height: 1;

    text-decoration: none;

    white-space: nowrap;

    cursor: pointer;

    transition:
        background .15s ease,
        color .15s ease,
        border-color .15s ease;

}

.ads-page .ads-action-edit:hover {

    background: #174bc8;

    border-color: #174bc8;

    color: #fff;

}

.ads-page .ads-action-toggle:hover {

    background: #222;

    border-color: #222;

    color: #fff;

}

.ads-page .ads-action-delete {

    color: #a00000;

}

.ads-page .ads-action-delete:hover {

    background: #a00000;

    border-color: #a00000;

    color: #fff;

}


/* ---------------------------------------------------------------
   EMPTY
---------------------------------------------------------------- */

.ads-page .ads-empty {

    padding: 70px 20px;

    text-align: center;

}

.ads-page .ads-empty-icon {

    margin-bottom: 10px;

    color: #174bc8;

    font-size: 34px;

}

.ads-page .ads-empty h3 {

    margin: 0 0 6px;

    color: #171717;

    font-family: Georgia, "Times New Roman", serif;

    font-size: 21px;

}

.ads-page .ads-empty p {

    max-width: 380px;

    margin: 0 auto 18px;

    color: #888;

    font-size: 10px;

    line-height: 1.6;

}


/* ---------------------------------------------------------------
   RESPONSIVE
---------------------------------------------------------------- */

@media (max-width: 900px) {

    .ads-page .ads-header {

        align-items: flex-start;

    }

    .ads-page .ads-header h1 {

        font-size: 26px;

    }

}

@media (max-width: 640px) {

    .ads-page .ads-header {

        flex-direction: column;

        align-items: stretch;

    }

    .ads-page .ads-header-action {

        width: 100%;

    }

    .ads-page .ads-header-action .ads-button {

        width: 100%;

    }

    .ads-page .ads-card-header {

        align-items: flex-start;

        flex-direction: column;

    }

}

</style>

<?php include __DIR__ . '/layout/footer.php'; ?>