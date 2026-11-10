<?php

$user = $user ?? Auth::user();
$ad = $ad ?? null;
$errors = $errors ?? [];
$baseUrl = Auth::baseUrl();

$isEdit = !empty($ad['id']);

$pageTitle = $isEdit ? 'Edit Iklan' : 'Tambah Iklan';
$pageDescription = 'Kelola banner iklan yang tampil di sisi kanan halaman publik INFORA SN.';

$formAction = $isEdit
    ? $baseUrl . '/admin/ads/' . (int) $ad['id'] . '/update'
    : $baseUrl . '/admin/ads/store';

function adFormImageUrl($path)
{
    $path = trim((string) ($path ?? ''));

    if ($path === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $path = str_replace('\\', '/', $path);
    $path = ltrim($path, '/');

    return rtrim((string) Auth::baseUrl(), '/') . '/' . $path;
}

$currentImage = adFormImageUrl($ad['image_path'] ?? '');

?>

<?php include __DIR__ . '/layout/sidebar.php'; ?>

<div class="admin-page-wrapper">

    <?php include __DIR__ . '/layout/header.php'; ?>

    <main class="admin-main-content">

        <section class="ad-page-head">
            <div>
                <div class="ad-kicker">KONFIGURASI PORTAL</div>
                <h1><?= $isEdit ? 'Edit Iklan' : 'Tambah Iklan' ?></h1>
                <p>Isi detail banner iklan yang akan tampil di kolom kanan portal publik.</p>
            </div>

            <a href="<?= $baseUrl ?>/admin/ads" class="ad-secondary-button">
                ← Kembali
            </a>
        </section>

        <?php if (!empty($errors)): ?>
            <div class="ad-alert error">
                <ul style="margin:0;padding-left:16px;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="ad-form-card">

            <form
                method="POST"
                action="<?= htmlspecialchars($formAction) ?>"
                enctype="multipart/form-data"
                class="ad-form"
            >

                <div class="ad-form-grid">

                    <div class="ad-form-main">

                        <div class="ad-field">
                            <label for="title">Nama Iklan</label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="<?= htmlspecialchars($ad['title'] ?? '') ?>"
                                placeholder="Contoh: Banner Promo Agustus"
                                required
                            >
                        </div>

                        <div class="ad-field">
                            <label for="target_url">URL Tujuan</label>
                            <input
                                type="url"
                                id="target_url"
                                name="target_url"
                                value="<?= htmlspecialchars($ad['target_url'] ?? '') ?>"
                                placeholder="https://contoh-tujuan-iklan.com"
                                required
                            >
                        </div>

                        <div class="ad-field-row">

                            <div class="ad-field">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <option value="ACTIVE" <?= (($ad['status'] ?? 'ACTIVE') === 'ACTIVE') ? 'selected' : '' ?>>
                                        Aktif
                                    </option>
                                    <option value="INACTIVE" <?= (($ad['status'] ?? '') === 'INACTIVE') ? 'selected' : '' ?>>
                                        Nonaktif
                                    </option>
                                </select>
                            </div>

                            <div class="ad-field">
                                <label for="sort_order">Urutan Tampil</label>
                                <input
                                    type="number"
                                    id="sort_order"
                                    name="sort_order"
                                    value="<?= (int) ($ad['sort_order'] ?? 0) ?>"
                                    min="0"
                                >
                            </div>

                        </div>

                    </div>

                    <div class="ad-form-side">

                        <div class="ad-field">
                            <label for="image">
                                Gambar Banner
                                <span class="ad-field-hint">(disarankan 300 × 250 px)</span>
                            </label>

                            <?php if ($currentImage): ?>
                                <div class="ad-current-image">
                                    <img src="<?= htmlspecialchars($currentImage) ?>" alt="Gambar iklan saat ini">
                                </div>
                            <?php endif; ?>

                            <input
                                type="file"
                                id="image"
                                name="image"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                                <?= $isEdit ? '' : 'required' ?>
                            >

                            <?php if ($isEdit): ?>
                                <p class="ad-field-note">
                                    Kosongkan jika tidak ingin mengganti gambar.
                                </p>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>

                <div class="ad-form-actions">
                    <button type="submit" class="ad-primary-button">
                        <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Iklan' ?>
                    </button>

                    <a href="<?= $baseUrl ?>/admin/ads" class="ad-cancel-button">
                        Batal
                    </a>
                </div>

            </form>

        </section>

    </main>
</div>

<style>
.admin-page-wrapper{margin-left:245px;box-sizing:border-box;}
.admin-main-content{padding:32px 34px;box-sizing:border-box;background:#f7f7f4;min-height:100vh;}
@media (max-width:900px){.admin-page-wrapper{margin-left:215px;}}

.ad-page-head{display:flex;justify-content:space-between;gap:20px;align-items:flex-end;margin-bottom:20px}
.ad-kicker{font-size:9px;font-weight:900;letter-spacing:.12em;color:#174bc8}
.ad-page-head h1{margin:5px 0;font-family:Georgia,serif;font-size:32px;color:#111}
.ad-page-head p{margin:0;color:#777;font-size:11px}
.ad-primary-button{display:inline-flex;align-items:center;justify-content:center;padding:11px 15px;background:#174bc8;color:#fff;font-size:9px;font-weight:900;text-decoration:none;border:0;cursor:pointer}
.ad-secondary-button{display:inline-flex;align-items:center;justify-content:center;padding:11px 15px;background:#fff;border:1px solid #d5d5d0;color:#333;font-size:9px;font-weight:900;text-decoration:none}
.ad-cancel-button{display:inline-flex;align-items:center;justify-content:center;padding:11px 15px;background:#fff;border:1px solid #d5d5d0;color:#333;font-size:9px;font-weight:900;text-decoration:none}
.ad-alert{padding:12px 14px;margin-bottom:14px;font-size:10px;font-weight:700}
.ad-alert.error{background:#fff0f0;color:#a00000;border:1px solid #efc4c4}
.ad-form-card{background:#fff;border:1px solid #dddcd6;padding:24px}
.ad-form-grid{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:24px}
.ad-field{margin-bottom:16px}
.ad-field label{display:block;margin-bottom:6px;font-size:10px;font-weight:900;color:#333;text-transform:uppercase;letter-spacing:.04em}
.ad-field-hint{color:#999;font-weight:600;text-transform:none;letter-spacing:0}
.ad-field input[type="text"],
.ad-field input[type="url"],
.ad-field input[type="number"],
.ad-field select{
    width:100%;
    box-sizing:border-box;
    padding:10px 12px;
    border:1px solid #d5d5d0;
    font-size:11px;
    color:#111;
    background:#fff;
}
.ad-field input[type="file"]{
    width:100%;
    box-sizing:border-box;
    padding:9px;
    border:1px dashed #c9cdd3;
    font-size:10px;
    background:#fafafa;
}
.ad-field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.ad-field-note{margin:6px 0 0;color:#888;font-size:9px}
.ad-current-image{width:100%;aspect-ratio:300/250;max-width:260px;overflow:hidden;background:#eef0f2;margin-bottom:10px;border:1px solid #e4e3dd}
.ad-current-image img{width:100%;height:100%;object-fit:cover;display:block}
.ad-form-actions{display:flex;gap:8px;padding-top:8px;border-top:1px solid #eee}

@media (max-width: 760px){
    .ad-form-grid{grid-template-columns:1fr}
}
</style>

<?php include __DIR__ . '/layout/footer.php'; ?>