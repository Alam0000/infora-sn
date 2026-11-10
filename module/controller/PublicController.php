<?php

require_once __DIR__ . '/../model/AdminAd.php';

class PublicController extends Controller
{
    private $articleModel;
    private $adModel;

    public function __construct()
    {
        $this->articleModel = new PublicArticle();
        $this->adModel = new AdminAd();
    }

    /*
    |--------------------------------------------------------------------------
    | HOMEPAGE
    |--------------------------------------------------------------------------
    | URL:
    | /
    |--------------------------------------------------------------------------
    */
   public function home()
{
    /*
    |--------------------------------------------------------------------------
    | BERITA UTAMA
    |--------------------------------------------------------------------------
    */
    $hero = $this->articleModel->getHeroArticle();

    /*
    |--------------------------------------------------------------------------
    | BREAKING / TERKINI
    |--------------------------------------------------------------------------
    */
    $breakingNews = $this->articleModel->getBreakingNews(5);

    /*
    |--------------------------------------------------------------------------
    | ARTIKEL TERKINI
    |--------------------------------------------------------------------------
    */
    $latest = $this->articleModel->getLatest(12);

    /*
    |--------------------------------------------------------------------------
    | PILIHAN REDAKSI
    |--------------------------------------------------------------------------
    */
    $editorsChoice = $this->articleModel->getEditorsChoice(4);

    /*
    |--------------------------------------------------------------------------
    | TRENDING / YANG SEDANG DIBAHAS
    |--------------------------------------------------------------------------
    */
    $trending = $this->articleModel->getPopularSevenDays(6);

    /*
    |--------------------------------------------------------------------------
    | TERPOPULER
    |--------------------------------------------------------------------------
    */
    $popularSevenDays = $this->articleModel->getPopularSevenDays(5);
    $popular = $this->articleModel->getPopular(5);

    /*
    |--------------------------------------------------------------------------
    | OPINI & ANALISIS
    |--------------------------------------------------------------------------
    */
    $opinion = $this->articleModel->getOpinionArticles(4);

    /*
    |--------------------------------------------------------------------------
    | KATEGORI
    |--------------------------------------------------------------------------
    */
    $categories = $this->articleModel->getCategories();

    /*
    |--------------------------------------------------------------------------
    | BERITA PER KATEGORI
    |--------------------------------------------------------------------------
    |
    | Kita ambil beberapa kategori utama untuk ditampilkan
    | langsung di homepage.
    |
    */
    $homeCategorySlugs = [
        'daerah',
        'ekonomi',
        'nasional',
        'teknologi',
        'pendidikan',
        'politik'
    ];

    $categoryNews = [];

    foreach ($homeCategorySlugs as $categorySlug) {
        $articles = $this->articleModel->getHomeCategoryArticles(
            $categorySlug,
            3
        );

        if (!empty($articles)) {
            $categoryNews[$categorySlug] = $articles;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | IKLAN
    |--------------------------------------------------------------------------
    */
    $activeAds = $this->adModel->getActiveAds(2);

    $adTop = $activeAds[0] ?? null;
    $adBottom = $activeAds[1] ?? null;

    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */
    $this->view('public/home', [
        'hero' => $hero,

        'breakingNews' => $breakingNews,

        'latest' => $latest,

        'editorsChoice' => $editorsChoice,

        'trending' => $trending,

        'popularSevenDays' => $popularSevenDays,

        'popular' => $popular,

        'opinion' => $opinion,

        'categories' => $categories,

        'categoryNews' => $categoryNews,

        'adTop' => $adTop,

        'adBottom' => $adBottom
    ]);
}

    /*
    |--------------------------------------------------------------------------
    | DAFTAR ARTIKEL PUBLIK
    |--------------------------------------------------------------------------
    | URL:
    | /articles
    |--------------------------------------------------------------------------
    */
    public function articles()
    {
        $page = isset($_GET['page'])
            ? (int) $_GET['page']
            : 1;

        if ($page < 1) {
            $page = 1;
        }

        $perPage = 12;

        $total =
            $this->articleModel
                ->countPublishedArticles();

        $totalPages = max(
            1,
            (int) ceil($total / $perPage)
        );

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;

        $articles =
            $this->articleModel
                ->getPublishedArticles(
                    $perPage,
                    $offset
                );

        $categories =
            $this->articleModel
                ->getCategories();

        $this->view(
            'public/articles',
            [
                'articles' => $articles,
                'categories' => $categories,
                'page' => $page,
                'totalPages' => $totalPages,
                'total' => $total,
                'pageTitle' => 'Semua Artikel',
                'pageDescription' =>
                    'Kumpulan artikel dan berita terbaru INFORA SN.'
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ARTIKEL BERDASARKAN KATEGORI
    |--------------------------------------------------------------------------
    | URL:
    | /category/{slug}
    |--------------------------------------------------------------------------
    */
    public function category($slug)
    {
        $slug = trim($slug);

        $category =
            $this->articleModel
                ->getCategoryBySlug($slug);

        if (!$category) {
            http_response_code(404);

            $this->view(
                'public/articles',
                [
                    'articles' => [],
                    'categories' =>
                        $this->articleModel
                            ->getCategories(),
                    'page' => 1,
                    'totalPages' => 1,
                    'total' => 0,
                    'pageTitle' =>
                        'Kategori Tidak Ditemukan',
                    'pageDescription' =>
                        'Kategori yang Anda cari tidak tersedia.'
                ]
            );

            return;
        }

        $page = isset($_GET['page'])
            ? (int) $_GET['page']
            : 1;

        if ($page < 1) {
            $page = 1;
        }

        $perPage = 12;

        $total =
            $this->articleModel
                ->countArticlesByCategory($slug);

        $totalPages = max(
            1,
            (int) ceil($total / $perPage)
        );

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;

        $articles =
            $this->articleModel
                ->getArticlesByCategory(
                    $slug,
                    $perPage,
                    $offset
                );

        $this->view(
            'public/articles',
            [
                'articles' => $articles,
                'categories' =>
                    $this->articleModel
                        ->getCategories(),
                'page' => $page,
                'totalPages' => $totalPages,
                'total' => $total,
                'pageTitle' => $category['name'],
                'pageDescription' =>
                    !empty($category['description'])
                        ? $category['description']
                        : 'Berita dan artikel terbaru kategori ' .
                          $category['name'] . '.',
                'category' => $category
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL ARTIKEL
    |--------------------------------------------------------------------------
    | URL:
    | /article/{slug}
    |--------------------------------------------------------------------------
    */
    public function article($slug)
    {
        $slug = trim($slug);

        $article =
            $this->articleModel
                ->getPublishedBySlug($slug);

        if (!$article) {
            http_response_code(404);

            die('Artikel tidak ditemukan.');
        }

        $this->articleModel->incrementView(
            (int) $article['id']
        );

        $article['view_count'] =
            (int) ($article['view_count'] ?? 0) + 1;

        $tags =
            $this->articleModel
                ->getArticleTags(
                    (int) $article['id']
                );

        $related = [];

        if (!empty($article['category_id'])) {
            $related =
                $this->articleModel
                    ->getRelatedArticles(
                        (int) $article['category_id'],
                        (int) $article['id'],
                        4
                    );
        }

        $popular =
            $this->articleModel
                ->getPopularExcept(
                    (int) $article['id'],
                    5
                );

        $this->view(
            'public/article',
            [
                'article' => $article,
                'tags' => $tags,
                'related' => $related,
                'popular' => $popular
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PENCARIAN ARTIKEL
    |--------------------------------------------------------------------------
    | URL:
    | /search?q=kata
    |--------------------------------------------------------------------------
    */
    public function search()
    {
        $keyword = isset($_GET['q'])
            ? trim($_GET['q'])
            : '';

        $page = isset($_GET['page'])
            ? (int) $_GET['page']
            : 1;

        if ($page < 1) {
            $page = 1;
        }

        $perPage = 12;

        if ($keyword === '') {
            $articles = [];
            $total = 0;
        } else {
            $total =
                $this->articleModel
                    ->countSearchArticles($keyword);

            $totalPages = max(
                1,
                (int) ceil($total / $perPage)
            );

            if ($page > $totalPages) {
                $page = $totalPages;
            }

            $offset = ($page - 1) * $perPage;

            $articles =
                $this->articleModel
                    ->searchArticles(
                        $keyword,
                        $perPage,
                        $offset
                    );
        }

        $totalPages = max(
            1,
            (int) ceil($total / $perPage)
        );

        $this->view(
            'public/articles',
            [
                'articles' => $articles,
                'categories' =>
                    $this->articleModel
                        ->getCategories(),
                'page' => $page,
                'totalPages' => $totalPages,
                'total' => $total,
                'keyword' => $keyword,
                'pageTitle' =>
                    $keyword !== ''
                        ? 'Hasil Pencarian: ' . $keyword
                        : 'Pencarian Artikel',
                'pageDescription' =>
                    'Cari berita dan artikel di INFORA SN.'
            ]
        );
    }
}