<?php
/**
 * =========================================================
 * PARTIAL: TAG LAINNYA (INPUT TAG BUATAN SENDIRI)
 * =========================================================
 *
 * Cara pakai:
 * Include SETELAH foreach yang mencetak checkbox tag,
 * masih di dalam wadah (grid/flex) yang sama supaya kartu
 * "Lainnya" tampil sebagai kartu terakhir.
 *
 *     <?php foreach ($tags as $tag): ?>
 *         ... checkbox tag ...
 *     <?php endforeach; ?>
 *
 *     <?php require __DIR__ . '/partials/custom-tag-field.php'; ?>
 *
 * Variabel opsional: $custom_tags (dikirim controller saat
 * terjadi error validasi, supaya isian tidak hilang).
 */

$customTagsValue = trim((string) ($custom_tags ?? ''));
$customTagsOpen = $customTagsValue !== '';
?>

<style>
    .ctag-card {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-sizing: border-box;
    }

    .ctag-wrap {
        grid-column: 1 / -1;
        flex: 0 0 100%;
        width: 100%;
        margin-top: 2px;
    }

    .ctag-wrap[hidden] {
        display: none;
    }

    .ctag-input {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;
        font-size: 14px;
        box-sizing: border-box;
    }

    .ctag-input:focus {
        outline: none;
        border-color: #111827;
    }

    .ctag-hint {
        display: block;
        margin-top: 6px;
        color: #6b7280;
        font-size: 12px;
    }

    .ctag-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    .ctag-chip {
        padding: 4px 10px;
        border-radius: 999px;
        background: #eef2ff;
        color: #174bc8;
        font-size: 12px;
        font-weight: 700;
    }
</style>

<label class="ctag-card">
    <input
        type="checkbox"
        id="ctagToggle"
        <?= $customTagsOpen ? 'checked' : '' ?>
    >
    Lainnya
</label>

<div class="ctag-wrap" id="ctagWrap" <?= $customTagsOpen ? '' : 'hidden' ?>>
    <input
        type="text"
        class="ctag-input"
        name="custom_tags"
        id="ctagInput"
        maxlength="200"
        autocomplete="off"
        placeholder="Tulis tag baru, pisahkan dengan koma. Contoh: Kesehatan, Hiburan"
        value="<?= htmlspecialchars($customTagsValue, ENT_QUOTES, 'UTF-8') ?>"
    >
    <small class="ctag-hint">
        Maksimal 5 tag baru, tiap tag maksimal 30 karakter. Tag yang sudah ada akan dipakai otomatis.
    </small>
    <div class="ctag-preview" id="ctagPreview"></div>
</div>

<script>
(function () {
    var toggle  = document.getElementById('ctagToggle');
    var wrap    = document.getElementById('ctagWrap');
    var input   = document.getElementById('ctagInput');
    var preview = document.getElementById('ctagPreview');

    if (!toggle || !wrap || !input || !preview) {
        return;
    }

    function parseNames(raw) {
        var seen = {};
        var out = [];

        raw.split(',').forEach(function (part) {
            var name = part.replace(/<[^>]*>/g, '')
                           .replace(/\s+/g, ' ')
                           .replace(/^#+/, '')
                           .trim()
                           .slice(0, 30);

            if (name === '') {
                return;
            }

            var key = name.toLowerCase();

            if (!seen[key]) {
                seen[key] = true;
                out.push(name);
            }
        });

        return out.slice(0, 5);
    }

    function renderPreview() {
        preview.innerHTML = '';

        parseNames(input.value).forEach(function (name) {
            var chip = document.createElement('span');
            chip.className = 'ctag-chip';
            chip.textContent = '#' + name;
            preview.appendChild(chip);
        });
    }

    toggle.addEventListener('change', function () {
        wrap.hidden = !toggle.checked;

        if (toggle.checked) {
            input.focus();
        } else {
            input.value = '';
            renderPreview();
        }
    });

    input.addEventListener('input', renderPreview);

    renderPreview();
})();
</script>