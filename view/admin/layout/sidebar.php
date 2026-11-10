<?php

$currentPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
);

$baseUrl = Auth::baseUrl();

/*
|--------------------------------------------------------------------------
| Active Menu
|--------------------------------------------------------------------------
*/
function adminMenuActive($path, $currentPath)
{
    return $currentPath === $path
        || strpos($currentPath, $path . '/') === 0;
}

/*
|--------------------------------------------------------------------------
| Khusus Menu Review
|--------------------------------------------------------------------------
| Review menggunakan URL:
| /admin/article/{id}/review
*/
function adminReviewActive($currentPath)
{
    return strpos($currentPath, '/admin/article/') === 0
        && strpos($currentPath, '/review') !== false;
}

?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">

<aside class="admin-sidebar">

    <!-- =========================================================
         LOGO
    ========================================================== -->
    <div class="admin-brand">

        <div class="brand-mark">
            <div class="brand-logo">
                <span>INFORA</span><i>.</i>
            </div>
            <div class="brand-subtitle">
                DASHBOARD REDAKSI
            </div>
        </div>

    </div>


    <!-- =========================================================
         MENU
    ========================================================== -->
    <nav class="admin-nav">

        <!-- REDAKSI -->
        <div class="nav-section-title">
            <span>Redaksi</span>
        </div>


        <!-- Dashboard -->
        <a
            href="<?= $baseUrl ?>/admin/dashboard"
            class="admin-nav-item <?= adminMenuActive('/admin/dashboard', $currentPath) ? 'active' : '' ?>"
        >
            <span class="nav-icon">▦</span>
            <span class="nav-label">Dashboard Redaksi</span>
        </a>


        <!-- Daftar Semua Artikel -->
        <a
            href="<?= $baseUrl ?>/admin/articles"
            class="admin-nav-item <?= adminMenuActive('/admin/articles', $currentPath) ? 'active' : '' ?>"
        >
            <span class="nav-icon">▤</span>
            <span class="nav-label">Daftar Semua Artikel</span>
        </a>


        <!-- Meja Review & Kurasi -->
        <a
            href="<?= $baseUrl ?>/admin/articles?status=MENUNGGU_REVIEW"
            class="admin-nav-item <?= adminReviewActive($currentPath) ? 'active' : '' ?>"
        >
            <span class="nav-icon">✓</span>
            <span class="nav-label">Meja Review &amp; Kurasi</span>
        </a>



        <!-- =====================================================
             MANAJEMEN
        ====================================================== -->
        <div class="nav-section-title management-title">
            <span>Manajemen</span>
        </div>


        <!-- Kelola Penulis & Mitra -->
        <a
            href="<?= $baseUrl ?>/admin/clients"
            class="admin-nav-item <?= adminMenuActive('/admin/clients', $currentPath) ? 'active' : '' ?>"
        >
            <span class="nav-icon">◉</span>
            <span class="nav-label">Kelola Penulis &amp; Mitra</span>
        </a>


        <!-- Kelola Kategori -->
        <a
            href="<?= $baseUrl ?>/admin/categories"
            class="admin-nav-item <?= adminMenuActive('/admin/categories', $currentPath) ? 'active' : '' ?>"
        >
            <span class="nav-icon">▥</span>
            <span class="nav-label">Kelola Kategori Berita</span>
        </a>

        <a
            href="<?= $baseUrl ?>/admin/ads"
            class="admin-nav-item <?= adminMenuActive('/admin/ads', $currentPath) ? 'active' : '' ?>"
        >
            <span class="nav-icon">◆</span>
            <span class="nav-label">Kelola Iklan</span>
        </a>


        <!-- Lihat Portal -->
        <a
            href="<?= $baseUrl ?>/"
            class="admin-nav-item nav-item-ghost"
            target="_blank"
        >
            <span class="nav-icon">↗</span>
            <span class="nav-label">Lihat Portal</span>
        </a>

    </nav>



    <!-- =========================================================
         LOGOUT
    ========================================================== -->
    <div class="admin-sidebar-bottom">

        <a
            href="<?= $baseUrl ?>/logout"
            class="admin-nav-item logout-item"
        >
            <span class="nav-icon">←</span>
            <span class="nav-label">Keluar</span>
        </a>

    </div>

</aside>



<style>

/* =============================================================
   ROOT TOKENS
============================================================= */

.admin-sidebar {
    --sb-bg: #0b0b0d;
    --sb-bg-soft: #131316;
    --sb-border: rgba(255, 255, 255, 0.07);
    --sb-text: #eaeaea;
    --sb-text-dim: #8b8b93;
    --sb-text-faint: #57575f;
    --sb-accent: #caff00;
    --sb-accent-soft: rgba(202, 255, 0, 0.12);
    --sb-danger: #ff5c5c;

    font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}


/* =============================================================
   SIDEBAR
============================================================= */

.admin-sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;

    width: 260px;

    background:
        radial-gradient(circle at 0% 0%, rgba(202, 255, 0, 0.05), transparent 40%),
        linear-gradient(180deg, var(--sb-bg) 0%, var(--sb-bg-soft) 100%);
    color: var(--sb-text);

    z-index: 1000;

    display: flex;
    flex-direction: column;

    overflow-y: auto;
    overflow-x: hidden;

    border-right: 1px solid var(--sb-border);
    box-shadow: 6px 0 30px rgba(0, 0, 0, 0.25);
}


/* =============================================================
   BRAND
============================================================= */

.admin-brand {
    padding: 30px 26px 24px;

    box-sizing: border-box;

    border-bottom: 1px solid var(--sb-border);

    position: relative;
}

.admin-brand::after {
    content: "";
    position: absolute;
    left: 26px;
    bottom: -1px;
    width: 32px;
    height: 2px;
    background: var(--sb-accent);
    border-radius: 2px;
}


.brand-logo {
    font-family: "JetBrains Mono", monospace;

    font-size: 24px;
    font-weight: 800;

    line-height: 1;

    letter-spacing: -0.5px;
}


.brand-logo span {
    color: #ffffff;
}


.brand-logo i {
    color: var(--sb-accent);

    font-style: normal;

    text-shadow: 0 0 14px rgba(202, 255, 0, 0.55);
}


.brand-subtitle {
    margin-top: 10px;

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 2.2px;
    text-transform: uppercase;

    color: var(--sb-text-dim);
}


/* =============================================================
   NAVIGATION
============================================================= */

.admin-nav {
    padding: 22px 14px 0;
}


.nav-section-title {
    padding: 0 12px;

    margin-bottom: 12px;

    font-size: 10.5px;
    font-weight: 700;

    letter-spacing: 1.8px;
    text-transform: uppercase;

    color: var(--sb-text-faint);
}


.management-title {
    margin-top: 30px;
    padding-top: 26px;
    border-top: 1px solid var(--sb-border);
}


/* =============================================================
   NAV ITEM
============================================================= */

.admin-nav-item {
    position: relative;

    display: flex;
    align-items: center;
    gap: 12px;

    width: 100%;
    min-height: 46px;

    box-sizing: border-box;

    padding: 0 14px;

    margin-bottom: 3px;

    color: var(--sb-text-dim);

    text-decoration: none;

    border-radius: 10px;

    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.1px;

    transition:
        background 0.18s ease,
        color 0.18s ease,
        transform 0.18s ease,
        box-shadow 0.18s ease;
}


/* Hover */

.admin-nav-item:hover {
    background: rgba(255, 255, 255, 0.045);

    color: #ffffff;

    transform: translateX(3px);
}


/* Active */

.admin-nav-item.active {
    background: linear-gradient(90deg, var(--sb-accent-soft), rgba(202, 255, 0, 0.03));

    color: var(--sb-accent);

    font-weight: 700;

    box-shadow: inset 0 0 0 1px rgba(202, 255, 0, 0.18);
}

.admin-nav-item.active::before {
    content: "";

    position: absolute;
    left: -14px;
    top: 50%;
    transform: translateY(-50%);

    width: 3px;
    height: 20px;

    background: var(--sb-accent);
    border-radius: 0 3px 3px 0;

    box-shadow: 0 0 10px rgba(202, 255, 0, 0.7);
}

.admin-nav-item.active .nav-icon {
    background: var(--sb-accent);
    color: #0c0c0c;
    box-shadow: 0 0 14px rgba(202, 255, 0, 0.4);
}


/* Active Hover */

.admin-nav-item.active:hover {
    color: var(--sb-accent);
    transform: translateX(3px);
}


/* Ghost item (Lihat Portal) */

.nav-item-ghost {
    color: var(--sb-text-faint);
}

.nav-item-ghost:hover {
    color: #ffffff;
}


/* =============================================================
   ICON
============================================================= */

.nav-icon {
    width: 26px;
    height: 26px;

    flex: 0 0 26px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 7px;

    background: rgba(255, 255, 255, 0.04);

    font-size: 12px;
    font-weight: 700;

    transition: background 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
}

.admin-nav-item:hover .nav-icon {
    background: rgba(255, 255, 255, 0.09);
}


/* Text */

.nav-label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* =============================================================
   SIDEBAR BOTTOM
============================================================= */

.admin-sidebar-bottom {
    margin-top: auto;

    padding: 16px 14px 26px;

    border-top: 1px solid var(--sb-border);
}


/* Logout */

.logout-item {
    color: #d8d8d8;
}

.logout-item .nav-icon {
    color: var(--sb-danger);
}

.logout-item:hover {
    background: rgba(255, 92, 92, 0.1);
    color: var(--sb-danger);
}

.logout-item:hover .nav-icon {
    background: rgba(255, 92, 92, 0.18);
    color: var(--sb-danger);
}


/* =============================================================
   SCROLLBAR
============================================================= */

.admin-sidebar::-webkit-scrollbar {
    width: 5px;
}


.admin-sidebar::-webkit-scrollbar-track {
    background: transparent;
}


.admin-sidebar::-webkit-scrollbar-thumb {
    background: #2a2a30;

    border-radius: 10px;
}

.admin-sidebar::-webkit-scrollbar-thumb:hover {
    background: #3a3a42;
}


/* =============================================================
   RESPONSIVE
============================================================= */

@media (max-width: 900px) {

    .admin-sidebar {
        width: 225px;
    }


    .admin-brand {
        padding-left: 20px;
        padding-right: 20px;
    }


    .admin-nav {
        padding-left: 10px;
        padding-right: 10px;
    }

}

</style>