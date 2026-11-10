<?php

class PublicArticle extends Model
{
    /*
    |--------------------------------------------------------------------------
    | HOME - HERO ARTICLE
    |--------------------------------------------------------------------------
    */

    public function getHeroArticle()
    {
        return $this->fetchOne(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'

            ORDER BY
                a.published_at DESC,
                a.id DESC

            LIMIT 1
            "
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HOME - ARTIKEL TERBARU
    |--------------------------------------------------------------------------
    */

    public function getLatest($limit = 6)
    {
        $limit = max(
            1,
            min(20, (int) $limit)
        );

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'

            ORDER BY
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            "
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HOME - PILIHAN REDAKSI
    |--------------------------------------------------------------------------
    */

    public function getEditorsChoice($limit = 4)
    {
        $limit = max(
            1,
            min(20, (int) $limit)
        );

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'

            ORDER BY
                a.view_count DESC,
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            "
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HOME - TERPOPULER 7 HARI
    |--------------------------------------------------------------------------
    */

    public function getPopularSevenDays($limit = 5)
    {
        $limit = max(
            1,
            min(20, (int) $limit)
        );

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'
              AND a.published_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)

            ORDER BY
                a.view_count DESC,
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            "
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HOME - BREAKING / TERKINI
    |--------------------------------------------------------------------------
    */

    public function getBreakingNews($limit = 5)
    {
        $limit = max(
            1,
            min(10, (int) $limit)
        );

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'

            ORDER BY
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            "
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HOME - OPINI & ANALISIS
    |--------------------------------------------------------------------------
    */

    public function getOpinionArticles($limit = 4)
    {
        $limit = max(
            1,
            min(10, (int) $limit)
        );

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            INNER JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'
              AND c.slug = 'opini'

            ORDER BY
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            "
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HOME - BERITA PER KATEGORI
    |--------------------------------------------------------------------------
    */

    public function getHomeCategoryArticles(
        $categorySlug,
        $limit = 3
    ) {
        $limit = max(
            1,
            min(10, (int) $limit)
        );

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            INNER JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'
              AND c.slug = ?

            ORDER BY
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            ",
            "s",
            [$categorySlug]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SEMUA KATEGORI (FLAT)
    |--------------------------------------------------------------------------
    */

    public function getCategories()
    {
        return $this->fetchAll(
            "
            SELECT
                c.id,
                c.parent_id,
                c.name,
                c.slug,
                c.description,
                COUNT(a.id) AS article_count

            FROM categories c

            LEFT JOIN articles a
                ON a.category_id = c.id
               AND a.status = 'PUBLISHED'

            GROUP BY
                c.id,
                c.parent_id,
                c.name,
                c.slug,
                c.description

            ORDER BY
                (c.parent_id IS NULL) DESC,
                c.name ASC
            "
        );
    }


    /*
    |--------------------------------------------------------------------------
    | KATEGORI DALAM BENTUK POHON
    |--------------------------------------------------------------------------
    */

    public function getCategoryTree()
    {
        $all = $this->getCategories();

        $tree = [];
        $lookup = [];

        foreach ($all as $cat) {
            $cat['children'] = [];
            $lookup[$cat['id']] = $cat;
        }

        foreach ($lookup as $id => $cat) {
            if (empty($cat['parent_id'])) {
                $tree[$id] = $cat;
            }
        }

        foreach ($lookup as $id => $cat) {
            if (
                !empty($cat['parent_id'])
                && isset($tree[$cat['parent_id']])
            ) {
                $tree[$cat['parent_id']]['children'][] = $cat;
            }
        }

        return array_values($tree);
    }


    /*
    |--------------------------------------------------------------------------
    | KATEGORI BERDASARKAN SLUG
    |--------------------------------------------------------------------------
    */

    public function getCategoryBySlug($slug)
    {
        return $this->fetchOne(
            "
            SELECT
                id,
                parent_id,
                name,
                slug,
                description

            FROM categories

            WHERE slug = ?

            LIMIT 1
            ",
            "s",
            [$slug]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | JUMLAH SEMUA ARTIKEL PUBLISHED
    |--------------------------------------------------------------------------
    */

    public function countPublishedArticles()
    {
        $result = $this->fetchOne(
            "
            SELECT
                COUNT(*) AS total

            FROM articles

            WHERE status = 'PUBLISHED'
            "
        );

        return (int) ($result['total'] ?? 0);
    }


    /*
    |--------------------------------------------------------------------------
    | DAFTAR SEMUA ARTIKEL PUBLISHED
    |--------------------------------------------------------------------------
    */

    public function getPublishedArticles(
        $limit = 12,
        $offset = 0
    ) {
        $limit = max(
            1,
            min(50, (int) $limit)
        );

        $offset = max(
            0,
            (int) $offset
        );

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'

            ORDER BY
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            OFFSET {$offset}
            "
        );
    }


    /*
    |--------------------------------------------------------------------------
    | JUMLAH ARTIKEL DALAM KATEGORI
    |--------------------------------------------------------------------------
    */

    public function countArticlesByCategory($categorySlug)
    {
        $result = $this->fetchOne(
            "
            SELECT
                COUNT(*) AS total

            FROM articles a

            INNER JOIN categories c
                ON c.id = a.category_id

            WHERE c.slug = ?
              AND a.status = 'PUBLISHED'
            ",
            "s",
            [$categorySlug]
        );

        return (int) ($result['total'] ?? 0);
    }


    /*
    |--------------------------------------------------------------------------
    | ARTIKEL BERDASARKAN KATEGORI
    |--------------------------------------------------------------------------
    */

    public function getArticlesByCategory(
        $categorySlug,
        $limit = 12,
        $offset = 0
    ) {
        $limit = max(
            1,
            min(50, (int) $limit)
        );

        $offset = max(
            0,
            (int) $offset
        );

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            INNER JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'
              AND c.slug = ?

            ORDER BY
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            OFFSET {$offset}
            ",
            "s",
            [$categorySlug]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ARTIKEL POPULER
    |--------------------------------------------------------------------------
    */

    public function getPopular($limit = 5)
    {
        $limit = max(
            1,
            min(20, (int) $limit)
        );

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'

            ORDER BY
                a.view_count DESC,
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            "
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL ARTIKEL BERDASARKAN SLUG
    |--------------------------------------------------------------------------
    */

    public function getPublishedBySlug($slug)
    {
        return $this->fetchOne(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,
                a.created_at,
                a.updated_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                u.email AS author_email,

                c.name AS category_name,
                c.slug AS category_slug,
                c.description AS category_description

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.slug = ?
              AND a.status = 'PUBLISHED'

            LIMIT 1
            ",
            "s",
            [$slug]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TAG ARTIKEL
    |--------------------------------------------------------------------------
    */

    public function getArticleTags($articleId)
    {
        return $this->fetchAll(
            "
            SELECT
                t.id,
                t.name

            FROM article_tags at

            INNER JOIN tags t
                ON t.id = at.tag_id

            WHERE at.article_id = ?

            ORDER BY
                t.name ASC
            ",
            "i",
            [$articleId]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ARTIKEL TERKAIT
    |--------------------------------------------------------------------------
    */

    public function getRelatedArticles(
        $categoryId,
        $articleId,
        $limit = 4
    ) {
        $limit = max(
            1,
            min(20, (int) $limit)
        );

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'
              AND a.category_id = ?
              AND a.id != ?

            ORDER BY
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            ",
            "ii",
            [
                $categoryId,
                $articleId
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ARTIKEL POPULER KECUALI ARTIKEL YANG SEDANG DIBUKA
    |--------------------------------------------------------------------------
    */

    public function getPopularExcept(
        $articleId,
        $limit = 5
    ) {
        $limit = max(
            1,
            min(20, (int) $limit)
        );

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.title,
                a.slug,
                a.excerpt,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'
              AND a.id != ?

            ORDER BY
                a.view_count DESC,
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            ",
            "i",
            [$articleId]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH ARTIKEL
    |--------------------------------------------------------------------------
    */

    public function searchArticles(
        $keyword,
        $limit = 12,
        $offset = 0
    ) {
        $limit = max(
            1,
            min(50, (int) $limit)
        );

        $offset = max(
            0,
            (int) $offset
        );

        $keyword = trim($keyword);

        $search = '%' . $keyword . '%';

        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.author_id,
                a.category_id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.view_count,
                a.published_at,

                COALESCE(
                    NULLIF(a.author_override_name, ''),
                    u.name
                ) AS author_name,

                c.name AS category_name,
                c.slug AS category_slug

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'

              AND (
                    a.title LIKE ?
                    OR a.excerpt LIKE ?
                    OR a.content LIKE ?
                    OR c.name LIKE ?
                    OR u.name LIKE ?
                    OR a.author_override_name LIKE ?
              )

            ORDER BY
                a.published_at DESC,
                a.id DESC

            LIMIT {$limit}
            OFFSET {$offset}
            ",
            "ssssss",
            [
                $search,
                $search,
                $search,
                $search,
                $search,
                $search
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | JUMLAH HASIL SEARCH
    |--------------------------------------------------------------------------
    */

    public function countSearchArticles($keyword)
    {
        $keyword = trim($keyword);

        $search = '%' . $keyword . '%';

        $result = $this->fetchOne(
            "
            SELECT
                COUNT(*) AS total

            FROM articles a

            INNER JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.status = 'PUBLISHED'

              AND (
                    a.title LIKE ?
                    OR a.excerpt LIKE ?
                    OR a.content LIKE ?
                    OR c.name LIKE ?
                    OR u.name LIKE ?
                    OR a.author_override_name LIKE ?
              )
            ",
            "ssssss",
            [
                $search,
                $search,
                $search,
                $search,
                $search,
                $search
            ]
        );

        return (int) ($result['total'] ?? 0);
    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH VIEW ARTIKEL
    |--------------------------------------------------------------------------
    */

    public function incrementView($articleId)
    {
        return $this->execute(
            "
            UPDATE articles

            SET view_count = view_count + 1

            WHERE id = ?
              AND status = 'PUBLISHED'
            ",
            "i",
            [$articleId]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ALIAS
    |--------------------------------------------------------------------------
    |
    | Dipertahankan agar kompatibel jika ada kode lama.
    |
    |--------------------------------------------------------------------------
    */

    public function incrementViewCount($articleId)
    {
        return $this->incrementView($articleId);
    }
}