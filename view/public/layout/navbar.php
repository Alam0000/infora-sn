<?php
/*
|--------------------------------------------------------------------------
| BASE URL FIXING (Aman untuk Semua Halaman)
|--------------------------------------------------------------------------
*/
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

// Ambil path sampai sebelum folder /view/public jika ada
if (strpos($scriptName, '/view/public') !== false) {
    $baseUrl = substr($scriptName, 0, strpos($scriptName, '/view/public'));
} else {
    $baseUrl = rtrim(dirname($scriptName), '/');
}

if ($baseUrl === '/' || $baseUrl === '\\' || $baseUrl === '.') {
    $baseUrl = '';
}

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$currentPath = rtrim($currentPath, '/');

if ($currentPath === '') {
    $currentPath = '/';
}

/*
|--------------------------------------------------------------------------
| MUAT MODEL MANDIRI
|--------------------------------------------------------------------------
*/
$baseModelPath = __DIR__ . '/../../core/Model.php';
if (is_file($baseModelPath)) {
    require_once $baseModelPath;
}

$publicArticlePath = __DIR__ . '/../../module/model/PublicArticle.php';
if (is_file($publicArticlePath)) {
    require_once $publicArticlePath;
}


/*
|--------------------------------------------------------------------------
| Fungsi menentukan menu aktif
|--------------------------------------------------------------------------
*/

$isActive = function ($path) use ($currentPath, $baseUrl) {

    $target = rtrim($baseUrl . $path, '/');

    if ($target === '') {
        $target = '/';
    }

    return $currentPath === $target;
};


/*
|--------------------------------------------------------------------------
| Deteksi slug kategori aktif (untuk highlight induk)
|--------------------------------------------------------------------------
*/

$activeCategorySlug = null;

$categoryPrefix = $baseUrl . '/category/';

if (strpos($currentPath, $categoryPrefix) === 0) {

    $activeCategorySlug = substr($currentPath, strlen($categoryPrefix));
}


/*
|--------------------------------------------------------------------------
| Ambil kategori dalam bentuk pohon (induk -> sub-kategori)
|--------------------------------------------------------------------------
| Jika kelas PublicArticle tersedia, ambil data dari database.
| Jika tidak tersedia, siapkan array kosong agar tidak crash.
|--------------------------------------------------------------------------
*/

$navCategoryTree = [];

if (class_exists('PublicArticle')) {
    $navModel = new PublicArticle();
    $navCategoryTree = $navModel->getCategoryTree();
}


/*
|--------------------------------------------------------------------------
| Cek apakah kategori aktif ada di dalam pohon kategori tertentu
| (baik dia sendiri induknya, atau salah satu sub-nya)
|--------------------------------------------------------------------------
*/

$isCategoryBranchActive = function ($cat) use ($activeCategorySlug) {

    if ($activeCategorySlug === null) {
        return false;
    }

    if ($cat['slug'] === $activeCategorySlug) {
        return true;
    }

    foreach ($cat['children'] as $child) {

        if ($child['slug'] === $activeCategorySlug) {
            return true;
        }
    }

    return false;
};

?>

<style>

    /* =====================================================
       NAVBAR INFORA SN
    ===================================================== */

    .site-navigation {

        position: relative;

        width: 100%;

        background: #ffffff;

        border-bottom: 1px solid #d7d7d1;

    }


    .navigation-inner {

        width: min(
            1240px,
            calc(100% - 110px)
        );

        min-height: 48px;

        margin-left: auto;
        margin-right: auto;

        display: flex;

        align-items: stretch;

        overflow-x: auto;

        overflow-y: visible;

        scrollbar-width: none;

    }


    .navigation-inner::-webkit-scrollbar {

        display: none;

    }


    /* =====================================================
       MENU
    ===================================================== */

    .navigation-menu {

        display: flex;

        align-items: stretch;

        gap: 0;

    }


    .navigation-item {

        position: relative;

    }


    .navigation-link {

        position: relative;

        height: 48px;

        padding: 0 15px;

        display: flex;

        align-items: center;

        justify-content: center;

        color: #101010;

        font-size: 11px;

        font-weight: 900;

        white-space: nowrap;

        transition:
            color .15s ease,
            background .15s ease;

    }


    .navigation-item:first-child .navigation-link {

        padding-left: 0;

        padding-right: 20px;

    }


    .navigation-link:hover {

        color: #174bc8;

    }


    /* =====================================================
       MENU AKTIF
    ===================================================== */

    .navigation-link.active::after {

        content: "";

        position: absolute;

        left: 15px;

        right: 15px;

        bottom: -1px;

        height: 2px;

        background: #174bc8;

    }


    .navigation-item:first-child .navigation-link.active::after {

        left: 0;

        right: 20px;

    }


    /* =====================================================
       DROPDOWN (2 KOLOM, POLA KOMPAS)
    ===================================================== */

    .navigation-dropdown {

        position: absolute;

        top: 100%;

        left: 0;

        z-index: 40;

        display: flex;

        min-width: 220px;

        padding: 10px 0;

        background: #ffffff;

        border: 1px solid #deded9;

        box-shadow: 0 10px 24px rgba(0,0,0,.09);

        opacity: 0;

        visibility: hidden;

        transform: translateY(4px);

        transition:
            opacity .15s ease,
            transform .15s ease,
            visibility .15s ease;

    }


    .navigation-item:hover .navigation-dropdown {

        opacity: 1;

        visibility: visible;

        transform: translateY(0);

    }


    .navigation-dropdown-col {

        display: flex;

        flex-direction: column;

        min-width: 190px;

        padding: 0 16px;

    }


    .navigation-dropdown-item {

        padding: 7px 0;

        color: #222222;

        font-size: 9px;

        font-weight: 700;

        white-space: nowrap;

    }


    .navigation-dropdown-item:hover {

        color: #174bc8;

        text-decoration: underline;

    }


    /* =====================================================
       RESPONSIVE
    ===================================================== */

    @media (max-width: 650px) {

        .navigation-link {

            padding-left: 12px !important;

            padding-right: 12px !important;

            font-size: 10px;

        }


        .navigation-dropdown {

            min-width: 190px;

        }

    }

</style>


<nav
    class="site-navigation"
    aria-label="Navigasi utama"
>

    <div class="navigation-inner">

        <div class="navigation-menu">


            <!-- =========================================
                 UTAMA (HOME)
            ========================================== -->

            <div class="navigation-item">

                
                    <a href="<?= htmlspecialchars($baseUrl . '/') ?>"
                    class="navigation-link <?= $isActive('/') ? 'active' : '' ?>"
                >

                    Utama

                </a>

            </div>


            <!-- =========================================
                 TIAP KATEGORI INDUK JADI ITEM SENDIRI
            ========================================== -->

            <?php foreach ($navCategoryTree as $cat): ?>

                <?php

                $hasChildren = !empty($cat['children']);

                $active = $isCategoryBranchActive($cat);

                ?>

                <div class="navigation-item">

                    
                        <a href="<?= htmlspecialchars(
                            $baseUrl . '/category/' . rawurlencode($cat['slug'])
                        ) ?>"
                        class="navigation-link <?= $active ? 'active' : '' ?>"
                    >

                        <?= htmlspecialchars($cat['name']) ?>

                    </a>


                    <?php if ($hasChildren): ?>

                        <div class="navigation-dropdown">

                            <div class="navigation-dropdown-col">

                                <?php foreach ($cat['children'] as $child): ?>

                                    
                                        <a href="<?= htmlspecialchars(
                                            $baseUrl . '/category/' . rawurlencode($child['slug'])
                                        ) ?>"
                                        class="navigation-dropdown-item"
                                    >

                                        <?= htmlspecialchars($child['name']) ?>

                                    </a>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>


        </div>

    </div>

</nav>