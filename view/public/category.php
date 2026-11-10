<?php

/*
|--------------------------------------------------------------------------
| INFORA SN - CATEGORY PAGE
|--------------------------------------------------------------------------
| Header + Navbar + Footer menggunakan layout bersama.
|--------------------------------------------------------------------------
*/

$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

if ($baseUrl === '/' || $baseUrl === '\\') {
    $baseUrl = '';
}

$category = $category ?? null;
$articles = $articles ?? [];
$popular = $popular ?? [];


/*
|--------------------------------------------------------------------------
| PAGE META
|--------------------------------------------------------------------------
*/

$categoryName = $category['name'] ?? 'Kategori';

$pageTitle = $categoryName . ' - INFORA SN';

$pageDescription = !empty($category['description'])
    ? $category['description']
    : 'Berita dan analisis terbaru kategori ' . $categoryName . ' di INFORA SN.';


/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/

function categoryImageUrl($path, $baseUrl)
{
    if (empty($path)) {
        return '';
    }

    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }

    return $baseUrl . '/' . ltrim($path, '/');
}


function categoryDate($date)
{
    if (empty($date)) {
        return '-';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return '-';
    }

    $months = [
        1  => 'Jan',
        2  => 'Feb',
        3  => 'Mar',
        4  => 'Apr',
        5  => 'Mei',
        6  => 'Jun',
        7  => 'Jul',
        8  => 'Agu',
        9  => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des'
    ];

    return date('d', $timestamp)
        . ' '
        . $months[(int) date('n', $timestamp)]
        . ' '
        . date('Y', $timestamp);
}


function categoryReadingTime($content)
{
    if (empty($content)) {
        return 1;
    }

    $text = trim(
        preg_replace(
            '/\s+/',
            ' ',
            strip_tags($content)
        )
    );

    if ($text === '') {
        return 1;
    }

    $words = str_word_count(
        $text,
        0,
        '0123456789'
    );

    return max(
        1,
        (int) ceil($words / 200)
    );
}


function categoryArticleUrl($baseUrl, $slug)
{
    return $baseUrl
        . '/article/'
        . rawurlencode($slug);
}

?>


<?php require __DIR__ . '/layout/header.php'; ?>

<?php require __DIR__ . '/layout/navbar.php'; ?>


<style>

/* ==========================================================================
   CATEGORY PAGE
========================================================================== */

.infora-category-page {

    width: 100%;

    min-height: 100vh;

    background: #f4f6f9;

    padding: 8px 0 35px;

}


.category-container {

    width: min(
        1240px,
        calc(100% - 42px)
    );

    margin: 0 auto;

}


/* ==========================================================================
   BREADCRUMB
========================================================================== */

.category-breadcrumb {

    display: flex;

    align-items: center;

    gap: 5px;

    margin-bottom: 7px;

    color: #8a9097;

    font-size: 6px;

}


.category-breadcrumb a {

    color: #1755d1;

    font-weight: 900;

}


.category-breadcrumb span {

    color: #b4b9bf;

}


/* ==========================================================================
   CATEGORY HEADER
========================================================================== */

.category-header {

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 15px;

    margin-bottom: 10px;

    padding: 12px 13px;

    background: #ffffff;

    border: 1px solid #dfe3e8;

    border-radius: 3px;

}


.category-heading-left {

    min-width: 0;

}


.category-label {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    color: #1755d1;

    font-size: 6px;

    font-weight: 900;

    text-transform: uppercase;

}


.category-label::before {

    content: '';

    width: 4px;

    height: 11px;

    background: #1755d1;

}


.category-title {

    margin: 5px 0 0;

    color: #10151c;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 27px;

    line-height: 1.05;

    font-weight: 900;

}


.category-description {

    max-width: 750px;

    margin: 5px 0 0;

    color: #7b8189;

    font-size: 6px;

    line-height: 1.5;

}


.category-count {

    flex-shrink: 0;

    padding: 5px 7px;

    background: #f1f4f8;

    border: 1px solid #e1e5e9;

    border-radius: 2px;

    color: #646b74;

    font-size: 5px;

    font-weight: 900;

}


/* ==========================================================================
   MAIN GRID
========================================================================== */

.category-layout {

    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        300px;

    gap: 12px;

    align-items: start;

}


/* ==========================================================================
   ARTICLE LIST PANEL
========================================================================== */

.category-articles {

    min-width: 0;

    background: #ffffff;

    border: 1px solid #dfe3e8;

    border-radius: 3px;

}


.category-section-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 10px;

    padding: 9px 10px;

    border-bottom: 1px solid #e1e5e9;

}


.category-section-title {

    display: flex;

    align-items: center;

    gap: 5px;

    margin: 0;

    color: #161a20;

    font-size: 8px;

    font-weight: 900;

}


.category-section-title::before {

    content: '';

    width: 4px;

    height: 11px;

    background: #1755d1;

}


.category-section-info {

    color: #8b9097;

    font-size: 5px;

}


/* ==========================================================================
   ARTICLE ITEM
========================================================================== */

.category-article-item {

    display: grid;

    grid-template-columns:
        205px
        minmax(0, 1fr);

    gap: 10px;

    padding: 10px;

    border-bottom: 1px solid #e5e8eb;

}


.category-article-item:last-child {

    border-bottom: none;

}


.category-article-image {

    position: relative;

    width: 100%;

    aspect-ratio: 16 / 9;

    overflow: hidden;

    background: #e6e9ed;

    border-radius: 2px;

}


.category-article-image img {

    display: block;

    width: 100%;

    height: 100%;

    object-fit: cover;

    transition:
        transform .25s ease;

}


.category-article-item:hover
.category-article-image img {

    transform: scale(1.03);

}


.category-image-placeholder {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 100%;

    height: 100%;

    color: #8b9198;

    font-size: 7px;

    font-weight: 900;

}


.category-article-content {

    min-width: 0;

    display: flex;

    flex-direction: column;

    justify-content: center;

}


.category-article-category {

    color: #1755d1;

    font-size: 5px;

    font-weight: 900;

    text-transform: uppercase;

}


.category-article-title {

    margin: 4px 0 0;

    color: #1b2026;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 13px;

    line-height: 1.18;

    font-weight: 900;

}


.category-article-title a {

    color: inherit;

}


.category-article-title a:hover {

    color: #1755d1;

}


.category-article-excerpt {

    display: -webkit-box;

    overflow: hidden;

    margin: 5px 0 0;

    color: #777e86;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    font-size: 6px;

    line-height: 1.45;

    -webkit-line-clamp: 2;

    -webkit-box-orient: vertical;

}


.category-article-meta {

    display: flex;

    align-items: center;

    flex-wrap: wrap;

    gap: 5px;

    margin-top: 7px;

    color: #92979d;

    font-size: 5px;

}


.category-article-meta strong {

    color: #3d444c;

}


.category-article-meta .dot {

    color: #c2c6cb;

}


/* ==========================================================================
   EMPTY
========================================================================== */

.category-empty {

    padding: 55px 20px;

    text-align: center;

}


.category-empty-icon {

    margin-bottom: 7px;

    color: #b3b8bf;

    font-size: 22px;

}


.category-empty-title {

    margin: 0;

    color: #353a40;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 15px;

}


.category-empty-text {

    margin: 5px 0 0;

    color: #8a9097;

    font-size: 6px;

}


/* ==========================================================================
   SIDEBAR
========================================================================== */

.category-sidebar {

    display: flex;

    flex-direction: column;

    gap: 9px;

}


/* ==========================================================================
   POPULAR PANEL
========================================================================== */

.category-popular {

    overflow: hidden;

    background: #ffffff;

    border: 1px solid #dfe3e8;

    border-radius: 3px;

}


.category-popular-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 8px 9px;

    border-bottom: 1px solid #e2e5e8;

}


.category-popular-title {

    display: flex;

    align-items: center;

    gap: 5px;

    margin: 0;

    color: #171b20;

    font-size: 7px;

    font-weight: 900;

}


.category-popular-title::before {

    content: '';

    width: 4px;

    height: 10px;

    background: #1755d1;

}


.category-popular-label {

    color: #91969d;

    font-size: 5px;

}


.category-popular-list {

    padding: 2px 9px 6px;

}


.category-popular-item {

    display: grid;

    grid-template-columns:
        20px
        minmax(0, 1fr);

    gap: 5px;

    padding: 8px 0;

    border-bottom: 1px solid #e7e9ec;

}


.category-popular-item:last-child {

    border-bottom: none;

}


.category-popular-number {

    color: #c5cad0;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 15px;

    line-height: 1;

}


.category-popular-category {

    margin-bottom: 2px;

    color: #1755d1;

    font-size: 4.5px;

    font-weight: 900;

    text-transform: uppercase;

}


.category-popular-name {

    margin: 0;

    color: #33383f;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 6px;

    line-height: 1.35;

}


.category-popular-name a {

    color: inherit;

}


.category-popular-name a:hover {

    color: #1755d1;

}


.category-popular-meta {

    margin-top: 3px;

    color: #969ba1;

    font-size: 4.5px;

}


/* ==========================================================================
   CONTRIBUTOR
========================================================================== */

.category-contributor {

    position: relative;

    overflow: hidden;

    padding: 12px;

    background:
        linear-gradient(
            135deg,
            #111827,
            #020617
        );

    border-radius: 3px;

}


.category-contributor::after {

    content: '';

    position: absolute;

    right: -40px;

    bottom: -50px;

    width: 125px;

    height: 125px;

    border: 1px solid rgba(
        216,
        255,
        0,
        .4
    );

    border-radius: 50%;

}


.category-contributor-label {

    position: relative;

    z-index: 2;

    color: #d8ff00;

    font-size: 5px;

    font-weight: 900;

    text-transform: uppercase;

}


.category-contributor-title {

    position: relative;

    z-index: 2;

    margin: 6px 0 0;

    color: #ffffff;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 15px;

    line-height: 1.1;

}


.category-contributor-text {

    position: relative;

    z-index: 2;

    margin: 6px 0 0;

    color: #aeb7c4;

    font-size: 5px;

    line-height: 1.45;

}


.category-contributor-button {

    position: relative;

    z-index: 2;

    display: inline-flex;

    margin-top: 9px;

    padding: 5px 7px;

    background: #1755d1;

    color: #ffffff;

    font-size: 5px;

    font-weight: 900;

}


.category-contributor-button:hover {

    background: #d8ff00;

    color: #111111;

}


/* ==========================================================================
   CATEGORY NAV
========================================================================== */

.category-navigation {

    display: flex;

    align-items: center;

    flex-wrap: wrap;

    gap: 4px;

    margin-bottom: 10px;

}


.category-navigation a {

    padding: 5px 7px;

    background: #ffffff;

    border: 1px solid #dfe3e8;

    border-radius: 2px;

    color: #5e656d;

    font-size: 5px;

    font-weight: 900;

}


.category-navigation a:hover,

.category-navigation a.active {

    background: #1755d1;

    border-color: #1755d1;

    color: #ffffff;

}


/* ==========================================================================
   RESPONSIVE
========================================================================== */

@media (max-width: 900px) {

    .category-layout {

        grid-template-columns:
            minmax(0, 1fr)
            260px;

    }


    .category-article-item {

        grid-template-columns:
            175px
            minmax(0, 1fr);

    }


    .category-title {

        font-size: 24px;

    }

}


@media (max-width: 760px) {

    .category-layout {

        grid-template-columns: 1fr;

    }


    .category-sidebar {

        display: grid;

        grid-template-columns:
            1fr
            1fr;

    }


    .category-contributor {

        grid-column: 1 / -1;

    }

}


@media (max-width: 560px) {

    .category-container {

        width: calc(100% - 18px);

    }


    .category-header {

        align-items: flex-start;

        flex-direction: column;

    }


    .category-title {

        font-size: 22px;

    }


    .category-article-item {

        grid-template-columns: 120px minmax(0, 1fr);

        gap: 8px;

        padding: 8px;

    }


    .category-article-title {

        font-size: 10px;

    }


    .category-article-excerpt {

        display: none;

    }


    .category-article-meta {

        margin-top: 5px;

    }


    .category-sidebar {

        grid-template-columns: 1fr;

    }


    .category-contributor {

        grid-column: auto;

    }

}

</style>


<main class="infora-category-page">


    <div class="category-container">


        <!-- =============================================================
             BREADCRUMB
        ============================================================== -->

        <div class="category-breadcrumb">


            <a href="<?= $baseUrl ?>/">

                Utama

            </a>


            <span>/</span>


            <span>

                <?= htmlspecialchars(
                    $categoryName
                ) ?>

            </span>


        </div>



        <!-- =============================================================
             CATEGORY HEADER
        ============================================================== -->

        <section class="category-header">


            <div class="category-heading-left">


                <div class="category-label">

                    Rubrik

                </div>


                <h1 class="category-title">

                    <?= htmlspecialchars(
                        $categoryName
                    ) ?>

                </h1>


                <?php if (
                    !empty(
                        $category[
                            'description'
                        ]
                    )
                ): ?>

                    <p class="category-description">

                        <?= htmlspecialchars(
                            $category[
                                'description'
                            ]
                        ) ?>

                    </p>

                <?php else: ?>

                    <p class="category-description">

                        Kumpulan berita,
                        informasi, dan analisis
                        terbaru dari rubrik
                        <?= htmlspecialchars(
                            $categoryName
                        ) ?>.

                    </p>

                <?php endif; ?>


            </div>


            <div class="category-count">

                <?= count($articles) ?>

                Artikel

            </div>


        </section>



        <!-- =============================================================
             CATEGORY NAVIGATION
        ============================================================== -->

        <?php if (
            !empty($categories)
        ): ?>


            <div class="category-navigation">


                <?php foreach (
                    $categories as $cat
                ): ?>


                    <?php

                    $isCurrent =
                        isset(
                            $category['slug']
                        )
                        &&
                        $category['slug']
                        ===
                        ($cat['slug'] ?? '');

                    ?>


                    <a
                        href="<?= $baseUrl ?>/category/<?= rawurlencode(
                            $cat['slug']
                        ) ?>"
                        class="<?= $isCurrent ? 'active' : '' ?>"
                    >

                        <?= htmlspecialchars(
                            $cat['name']
                        ) ?>

                    </a>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>



        <!-- =============================================================
             CONTENT GRID
        ============================================================== -->

        <div class="category-layout">


            <!-- =========================================================
                 ARTICLES
            ========================================================== -->

            <section class="category-articles">


                <div class="category-section-header">


                    <h2 class="category-section-title">

                        Artikel Terkini

                    </h2>


                    <span class="category-section-info">

                        Terbaru dari
                        <?= htmlspecialchars(
                            $categoryName
                        ) ?>

                    </span>


                </div>



                <?php if (
                    !empty($articles)
                ): ?>


                    <?php foreach (
                        $articles as $article
                    ): ?>


                        <?php

                        $image =
                            categoryImageUrl(
                                $article[
                                    'thumbnail_path'
                                ] ?? '',
                                $baseUrl
                            );

                        $slug =
                            $article[
                                'slug'
                            ] ?? '';

                        ?>


                        <article
                            class="
                                category-article-item
                            "
                        >


                            <!-- IMAGE -->

                            <a
                                href="<?= categoryArticleUrl(
                                    $baseUrl,
                                    $slug
                                ) ?>"
                                class="
                                    category-article-image
                                "
                            >


                                <?php if (
                                    $image
                                ): ?>

                                    <img
                                        src="<?= htmlspecialchars(
                                            $image
                                        ) ?>"
                                        alt="<?= htmlspecialchars(
                                            $article[
                                                'title'
                                            ]
                                        ) ?>"
                                        loading="lazy"
                                    >

                                <?php else: ?>

                                    <div
                                        class="
                                            category-image-placeholder
                                        "
                                    >

                                        INFORA SN

                                    </div>

                                <?php endif; ?>


                            </a>



                            <!-- CONTENT -->

                            <div
                                class="
                                    category-article-content
                                "
                            >


                                <div
                                    class="
                                        category-article-category
                                    "
                                >

                                    <?= htmlspecialchars(
                                        $article[
                                            'category_name'
                                        ]
                                        ??
                                        $categoryName
                                    ) ?>

                                </div>


                                <h3
                                    class="
                                        category-article-title
                                    "
                                >

                                    <a
                                        href="<?= categoryArticleUrl(
                                            $baseUrl,
                                            $slug
                                        ) ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $article[
                                                'title'
                                            ]
                                        ) ?>

                                    </a>

                                </h3>


                                <?php if (
                                    !empty(
                                        $article[
                                            'excerpt'
                                        ]
                                    )
                                ): ?>

                                    <p
                                        class="
                                            category-article-excerpt
                                        "
                                    >

                                        <?= htmlspecialchars(
                                            $article[
                                                'excerpt'
                                            ]
                                        ) ?>

                                    </p>

                                <?php endif; ?>


                                <div
                                    class="
                                        category-article-meta
                                    "
                                >


                                    <strong>

                                        <?= htmlspecialchars(
                                            $article[
                                                'author_name'
                                            ]
                                            ??
                                            'INFORA SN'
                                        ) ?>

                                    </strong>


                                    <span class="dot">

                                        •

                                    </span>


                                    <span>

                                        <?= categoryDate(
                                            $article[
                                                'published_at'
                                            ] ?? ''
                                        ) ?>

                                    </span>


                                    <span class="dot">

                                        •

                                    </span>


                                    <span>

                                        <?= categoryReadingTime(
                                            $article[
                                                'content'
                                            ] ?? ''
                                        ) ?>

                                        menit baca

                                    </span>


                                    <span class="dot">

                                        •

                                    </span>


                                    <span>

                                        <?= (int) (
                                            $article[
                                                'view_count'
                                            ] ?? 0
                                        ) ?>

                                        views

                                    </span>


                                </div>


                            </div>


                        </article>


                    <?php endforeach; ?>


                <?php else: ?>


                    <div class="category-empty">


                        <div class="category-empty-icon">

                            ○

                        </div>


                        <h3
                            class="
                                category-empty-title
                            "
                        >

                            Belum Ada Artikel

                        </h3>


                        <p
                            class="
                                category-empty-text
                            "
                        >

                            Belum terdapat artikel
                            yang dipublikasikan pada
                            kategori ini.

                        </p>


                    </div>


                <?php endif; ?>


            </section>



            <!-- =========================================================
                 SIDEBAR
            ========================================================== -->

            <aside class="category-sidebar">


                <!-- =====================================================
                     POPULAR
                ====================================================== -->

                <section class="category-popular">


                    <div class="category-popular-header">


                        <h2 class="category-popular-title">

                            Terpopuler

                        </h2>


                        <span
                            class="
                                category-popular-label
                            "
                        >

                            7 Hari Terakhir

                        </span>


                    </div>


                    <div class="category-popular-list">


                        <?php if (
                            !empty($popular)
                        ): ?>


                            <?php

                            $popularNumber = 1;

                            ?>


                            <?php foreach (
                                array_slice(
                                    $popular,
                                    0,
                                    5
                                ) as $item
                            ): ?>


                                <article
                                    class="
                                        category-popular-item
                                    "
                                >


                                    <div
                                        class="
                                            category-popular-number
                                        "
                                    >

                                        <?= str_pad(
                                            $popularNumber,
                                            2,
                                            '0',
                                            STR_PAD_LEFT
                                        ) ?>

                                    </div>


                                    <div>


                                        <div
                                            class="
                                                category-popular-category
                                            "
                                        >

                                            <?= htmlspecialchars(
                                                $item[
                                                    'category_name'
                                                ]
                                                ??
                                                'Berita'
                                            ) ?>

                                        </div>


                                        <h3
                                            class="
                                                category-popular-name
                                            "
                                        >

                                            <a
                                                href="<?= categoryArticleUrl(
                                                    $baseUrl,
                                                    $item[
                                                        'slug'
                                                    ]
                                                ) ?>"
                                            >

                                                <?= htmlspecialchars(
                                                    $item[
                                                        'title'
                                                    ]
                                                ) ?>

                                            </a>

                                        </h3>


                                        <div
                                            class="
                                                category-popular-meta
                                            "
                                        >

                                            <?= categoryDate(
                                                $item[
                                                    'published_at'
                                                ] ?? ''
                                            ) ?>

                                            &nbsp; • &nbsp;

                                            <?= (int) (
                                                $item[
                                                    'view_count'
                                                ] ?? 0
                                            ) ?>

                                            views

                                        </div>


                                    </div>


                                </article>


                                <?php

                                $popularNumber++;

                                ?>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <div
                                style="
                                    padding:
                                        12px 0;
                                    color:
                                        #888;
                                    font-size:
                                        5px;
                                "
                            >

                                Belum ada artikel
                                terpopuler.

                            </div>


                        <?php endif; ?>


                    </div>


                </section>



                <!-- =====================================================
                     CONTRIBUTOR
                ====================================================== -->

                <section
                    class="
                        category-contributor
                    "
                >


                    <div
                        class="
                            category-contributor-label
                        "
                    >

                        Ruang Kontributor

                    </div>


                    <h2
                        class="
                            category-contributor-title
                        "
                    >

                        Punya Analisis
                        atau Riset
                        Bernilai?

                    </h2>


                    <p
                        class="
                            category-contributor-text
                        "
                    >

                        Bagikan perspektif,
                        penelitian, dan analisis
                        Anda melalui INFORA SN.

                    </p>


                    <a
                        href="<?= $baseUrl ?>/client/article/create"
                        class="
                            category-contributor-button
                        "
                    >

                        Kirim Naskah

                    </a>


                </section>


            </aside>


        </div>


    </div>

</main>


<?php require __DIR__ . '/layout/footer.php'; ?>

</body>
</html>