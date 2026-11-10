<?php
/**
 * =========================================================
 * PARTIAL: INPUT THUMBNAIL + CROP / ATUR GAMBAR
 * =========================================================
 *
 * Gantikan <input type="file" name="thumbnail"> yang lama
 * dengan include partial ini. Nama field tetap "thumbnail",
 * jadi validasi dan upload di ArticleController tidak berubah.
 *
 *     <?php
 *     $thumbnailRequired   = true;   // false untuk form edit
 *     $currentThumbnailUrl = '';     // isi URL thumbnail lama di form edit
 *     require __DIR__ . '/partials/thumbnail-cropper.php';
 *     ?>
 *
 * Contoh di form edit:
 *     $thumbnailRequired   = false;
 *     $currentThumbnailUrl = '/infora-sn/' . ltrim($article['thumbnail_path'] ?? '', '/');
 */

$thumbnailRequired   = $thumbnailRequired ?? true;
$currentThumbnailUrl = $currentThumbnailUrl ?? '';
?>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css"
>

<style>
    .thc-box {
        padding: 16px 18px;
        border: 2px dashed #d1d5db;
        border-radius: 10px;
        background: #f9fafb;
    }

    .thc-hint {
        margin: 8px 0 0;
        color: #6b7280;
        font-size: 12px;
    }

    .thc-preview {
        margin-top: 12px;
    }

    .thc-preview[hidden] {
        display: none;
    }

    .thc-preview img {
        display: block;
        max-width: 100%;
        width: 360px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .thc-preview-label {
        margin: 0 0 6px;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
    }

    .thc-btn {
        padding: 8px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;
        color: #111827;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .thc-btn:hover {
        background: #f3f4f6;
    }

    .thc-btn.is-active {
        border-color: #111827;
        background: #111827;
        color: #ffffff;
    }

    .thc-btn-primary {
        border-color: #111827;
        background: #111827;
        color: #ffffff;
    }

    .thc-btn-primary:hover {
        background: #1f2937;
    }

    .thc-recrop {
        margin-top: 10px;
    }

    /* ----- Modal ----- */
    .thc-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(0, 0, 0, .65);
    }

    .thc-modal[hidden] {
        display: none;
    }

    .thc-dialog {
        width: min(760px, 100%);
        max-height: 100%;
        overflow: auto;
        padding: 20px;
        border-radius: 12px;
        background: #ffffff;
        box-sizing: border-box;
    }

    .thc-dialog h3 {
        margin: 0 0 12px;
        font-size: 17px;
    }

    .thc-stage {
        height: 400px;
        max-height: 50vh;
        background: #f3f4f6;
    }

    .thc-stage img {
        display: block;
        max-width: 100%;
    }

    .thc-toolbar,
    .thc-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .thc-toolbar-sep {
        width: 1px;
        margin: 0 4px;
        background: #e5e7eb;
    }

    .thc-actions {
        justify-content: flex-end;
    }
</style>

<div class="thc-box">
    <input
        type="file"
        id="thumbnail"
        name="thumbnail"
        accept="image/jpeg,image/png,image/webp"
        <?= $thumbnailRequired ? 'required' : '' ?>
    >

    <p class="thc-hint">
        Format JPG, PNG, atau WEBP. Maksimal 5 MB.
        Setelah memilih gambar, kamu bisa mengatur potongan, zoom, rasio, dan rotasi.
    </p>

    <div class="thc-preview" id="thcPreview" <?= $currentThumbnailUrl === '' ? 'hidden' : '' ?>>
        <p class="thc-preview-label" id="thcPreviewLabel">
            <?= $currentThumbnailUrl === '' ? 'Hasil thumbnail:' : 'Thumbnail saat ini:' ?>
        </p>
        <img
            id="thcPreviewImg"
            alt="Preview thumbnail"
            src="<?= htmlspecialchars($currentThumbnailUrl, ENT_QUOTES, 'UTF-8') ?>"
        >
        <button type="button" class="thc-btn thc-recrop" id="thcRecrop" hidden>
            Atur ulang gambar
        </button>
    </div>
</div>

<div class="thc-modal" id="thcModal" hidden>
    <div class="thc-dialog" role="dialog" aria-modal="true" aria-labelledby="thcTitle">
        <h3 id="thcTitle">Atur Thumbnail</h3>

        <div class="thc-stage">
            <img id="thcImage" alt="">
        </div>

        <div class="thc-toolbar">
            <button type="button" class="thc-btn is-active" data-ratio="1.7778">16:9</button>
            <button type="button" class="thc-btn" data-ratio="2">2:1</button>
            <button type="button" class="thc-btn" data-ratio="1.3333">4:3</button>
            <button type="button" class="thc-btn" data-ratio="1">1:1</button>
            <button type="button" class="thc-btn" data-ratio="free">Bebas</button>

            <span class="thc-toolbar-sep"></span>

            <button type="button" class="thc-btn" id="thcZoomIn">Zoom +</button>
            <button type="button" class="thc-btn" id="thcZoomOut">Zoom −</button>
            <button type="button" class="thc-btn" id="thcRotate">Putar 90°</button>
            <button type="button" class="thc-btn" id="thcReset">Reset</button>
        </div>

        <div class="thc-actions">
            <button type="button" class="thc-btn" id="thcCancel">Batal</button>
            <button type="button" class="thc-btn thc-btn-primary" id="thcSave">Gunakan Gambar</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<script>
(function () {
    var MAX_BYTES = 5 * 1024 * 1024;
    var ALLOWED   = ['image/jpeg', 'image/png', 'image/webp'];

    var input        = document.getElementById('thumbnail');
    var modal        = document.getElementById('thcModal');
    var image        = document.getElementById('thcImage');
    var preview      = document.getElementById('thcPreview');
    var previewImg   = document.getElementById('thcPreviewImg');
    var previewLabel = document.getElementById('thcPreviewLabel');
    var recropBtn    = document.getElementById('thcRecrop');
    var ratioBtns    = modal.querySelectorAll('[data-ratio]');

    var cropper       = null;
    var originalSrc   = null;   // data URL gambar asli (untuk atur ulang)
    var originalName  = 'thumbnail';
    var hasResult     = false;  // sudah ada hasil crop yang tersimpan di input
    var objectUrl     = null;

    function setActiveRatio(value) {
        ratioBtns.forEach(function (btn) {
            btn.classList.toggle('is-active', btn.getAttribute('data-ratio') === value);
        });
    }

    function destroyCropper() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    function openModal(src) {
        image.src = src;
        modal.hidden = false;
        document.body.style.overflow = 'hidden';

        destroyCropper();

        cropper = new Cropper(image, {
            aspectRatio: 16 / 9,
            viewMode: 1,
            autoCropArea: 1,
            background: false,
            responsive: true
        });

        setActiveRatio('1.7778');
    }

    function closeModal() {
        modal.hidden = true;
        document.body.style.overflow = '';
        destroyCropper();
    }

    /* ---------- Pilih file ---------- */
    input.addEventListener('change', function () {
        var file = input.files && input.files[0];

        if (!file) {
            return;
        }

        if (ALLOWED.indexOf(file.type) === -1) {
            alert('Format thumbnail hanya boleh JPG, PNG, atau WEBP.');
            input.value = '';
            return;
        }

        if (file.size > MAX_BYTES) {
            alert('Ukuran gambar maksimal 5 MB.');
            input.value = '';
            return;
        }

        originalName = file.name.replace(/\.[^.]+$/, '') || 'thumbnail';

        var reader = new FileReader();

        reader.onload = function (e) {
            originalSrc = e.target.result;
            hasResult = false;
            openModal(originalSrc);
        };

        reader.readAsDataURL(file);
    });

    /* ---------- Toolbar ---------- */
    ratioBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!cropper) {
                return;
            }

            var value = btn.getAttribute('data-ratio');

            cropper.setAspectRatio(value === 'free' ? NaN : parseFloat(value));
            setActiveRatio(value);
        });
    });

    document.getElementById('thcZoomIn').addEventListener('click', function () {
        if (cropper) { cropper.zoom(0.1); }
    });

    document.getElementById('thcZoomOut').addEventListener('click', function () {
        if (cropper) { cropper.zoom(-0.1); }
    });

    document.getElementById('thcRotate').addEventListener('click', function () {
        if (cropper) { cropper.rotate(90); }
    });

    document.getElementById('thcReset').addEventListener('click', function () {
        if (cropper) { cropper.reset(); }
    });

    /* ---------- Batal ---------- */
    document.getElementById('thcCancel').addEventListener('click', function () {
        closeModal();

        // Kalau belum pernah ada hasil crop, kosongkan pilihan file.
        if (!hasResult) {
            input.value = '';
        }
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            document.getElementById('thcCancel').click();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hidden) {
            document.getElementById('thcCancel').click();
        }
    });

    /* ---------- Simpan hasil crop ke input file ---------- */
    document.getElementById('thcSave').addEventListener('click', function () {
        if (!cropper) {
            return;
        }

        var canvas = cropper.getCroppedCanvas({
            maxWidth: 1920,
            maxHeight: 1920,
            fillColor: '#ffffff',
            imageSmoothingQuality: 'high'
        });

        if (!canvas) {
            alert('Gambar gagal diproses. Coba pilih gambar lain.');
            return;
        }

        canvas.toBlob(function (blob) {
            if (!blob) {
                alert('Gambar gagal diproses. Coba pilih gambar lain.');
                return;
            }

            if (blob.size > MAX_BYTES) {
                alert('Hasil gambar masih lebih dari 5 MB. Perkecil area potongan.');
                return;
            }

            var file = new File([blob], originalName + '.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now()
            });

            var dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;

            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
            }

            objectUrl = URL.createObjectURL(blob);
            previewImg.src = objectUrl;
            previewLabel.textContent = 'Hasil thumbnail:';
            preview.hidden = false;
            recropBtn.hidden = false;
            hasResult = true;

            closeModal();
        }, 'image/jpeg', 0.9);
    });

    /* ---------- Atur ulang gambar yang sama ---------- */
    recropBtn.addEventListener('click', function () {
        if (originalSrc) {
            openModal(originalSrc);
        }
    });
})();
</script>