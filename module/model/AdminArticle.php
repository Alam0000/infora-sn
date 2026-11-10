<?php

class AdminArticle extends Model
{
    /*
    |--------------------------------------------------------------------------
    | DATABASE
    |--------------------------------------------------------------------------
    |
    | Harus protected karena Model juga memiliki property $db protected.
    |
    */

    protected $db;


    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';

        $this->db = $koneksi;
    }


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD STATISTICS
    |--------------------------------------------------------------------------
    */

    public function getDashboardStats()
    {
        $stats = [
            'total_articles' => 0,
            'waiting_review' => 0,
            'revision'       => 0,
            'published'      => 0,
            'draft'          => 0,
            'rejected'       => 0,
            'clients'        => 0
        ];


        $query = $this->db->query("
            SELECT
                COUNT(*) AS total_articles,

                SUM(
                    status = 'MENUNGGU_REVIEW'
                ) AS waiting_review,

                SUM(
                    status = 'REVISI'
                ) AS revision,

                SUM(
                    status = 'PUBLISHED'
                ) AS published,

                SUM(
                    status = 'DRAFT'
                ) AS draft,

                SUM(
                    status = 'DITOLAK'
                ) AS rejected

            FROM articles
        ");


        if ($query) {

            $row = $query->fetch_assoc();

            $stats['total_articles'] =
                (int) ($row['total_articles'] ?? 0);

            $stats['waiting_review'] =
                (int) ($row['waiting_review'] ?? 0);

            $stats['revision'] =
                (int) ($row['revision'] ?? 0);

            $stats['published'] =
                (int) ($row['published'] ?? 0);

            $stats['draft'] =
                (int) ($row['draft'] ?? 0);

            $stats['rejected'] =
                (int) ($row['rejected'] ?? 0);
        }


        $clientQuery = $this->db->query("
            SELECT COUNT(*) AS total
            FROM users
            WHERE role = 'client'
        ");


        if ($clientQuery) {

            $row = $clientQuery->fetch_assoc();

            $stats['clients'] =
                (int) ($row['total'] ?? 0);
        }


        return $stats;
    }


    /*
    |--------------------------------------------------------------------------
    | RECENT ARTICLES
    |--------------------------------------------------------------------------
    */

    public function getRecentArticles()
    {
        $articles = [];


        $query = $this->db->query("
            SELECT
                a.*,
                u.name AS author_name,
                c.name AS category_name

            FROM articles a

            LEFT JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            ORDER BY a.created_at DESC

            LIMIT 10
        ");


        if ($query) {

            while ($row = $query->fetch_assoc()) {

                $articles[] = $row;
            }
        }


        return $articles;
    }


    /*
    |--------------------------------------------------------------------------
    | ALL ARTICLES
    |--------------------------------------------------------------------------
    */

    public function getAllArticles(
        $keyword = '',
        $status = ''
    ) {

        $articles = [];


        $sql = "
            SELECT
                a.*,
                u.name AS author_name,
                c.name AS category_name

            FROM articles a

            LEFT JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE 1 = 1
        ";


        $params = [];

        $types = '';


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($keyword !== '') {

            $sql .= "
                AND (
                    a.title LIKE ?
                    OR a.excerpt LIKE ?
                    OR u.name LIKE ?
                    OR c.name LIKE ?
                )
            ";


            $search = '%' . $keyword . '%';


            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;


            $types .= 'ssss';
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if ($status !== '') {

            $sql .= "
                AND a.status = ?
            ";


            $params[] = $status;

            $types .= 's';
        }


        $sql .= "
            ORDER BY a.created_at DESC
        ";


        $stmt = $this->db->prepare($sql);


        if (!$stmt) {
            return [];
        }


        if (!empty($params)) {

            $stmt->bind_param(
                $types,
                ...$params
            );
        }


        $stmt->execute();


        $result = $stmt->get_result();


        while ($row = $result->fetch_assoc()) {

            $articles[] = $row;
        }


        $stmt->close();


        return $articles;
    }


    /*
    |--------------------------------------------------------------------------
    | GET ARTICLE BY ID
    |--------------------------------------------------------------------------
    */

    public function getArticleById($id)
    {
        $id = (int) $id;


        if ($id <= 0) {
            return null;
        }


        $stmt = $this->db->prepare("
            SELECT
                a.*,
                u.name AS author_name,
                u.email AS author_email,
                c.name AS category_name

            FROM articles a

            LEFT JOIN users u
                ON u.id = a.author_id

            LEFT JOIN categories c
                ON c.id = a.category_id

            WHERE a.id = ?

            LIMIT 1
        ");


        $stmt->bind_param(
            'i',
            $id
        );


        $stmt->execute();


        $result = $stmt->get_result();


        $article = $result->fetch_assoc();


        $stmt->close();


        if (!$article) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | TAGS
        |--------------------------------------------------------------------------
        */

        $article['tags'] = [];


        $tagStmt = $this->db->prepare("
            SELECT
                t.id,
                t.name

            FROM article_tags at

            INNER JOIN tags t
                ON t.id = at.tag_id

            WHERE at.article_id = ?

            ORDER BY t.name ASC
        ");


        if ($tagStmt) {

            $tagStmt->bind_param(
                'i',
                $id
            );

            $tagStmt->execute();

            $tagResult =
                $tagStmt->get_result();


            while (
                $tag = $tagResult->fetch_assoc()
            ) {

                $article['tags'][] = $tag;
            }


            $tagStmt->close();
        }


        /*
        |--------------------------------------------------------------------------
        | IMAGES
        |--------------------------------------------------------------------------
        */

        $article['images'] = [];


        $imageStmt = $this->db->prepare("
            SELECT *
            FROM article_images
            WHERE article_id = ?
            ORDER BY is_thumbnail DESC, id ASC
        ");


        if ($imageStmt) {

            $imageStmt->bind_param(
                'i',
                $id
            );

            $imageStmt->execute();

            $imageResult =
                $imageStmt->get_result();


            while (
                $image = $imageResult->fetch_assoc()
            ) {

                $article['images'][] = $image;
            }


            $imageStmt->close();
        }


        return $article;
    }


    /*
    |--------------------------------------------------------------------------
    | GET CATEGORIES
    |--------------------------------------------------------------------------
    */

    public function getCategories()
    {
        $categories = [];


        $result = $this->db->query("
            SELECT
                id,
                name

            FROM categories

            ORDER BY name ASC
        ");


        if ($result) {

            while (
                $row = $result->fetch_assoc()
            ) {

                $categories[] = $row;
            }
        }


        return $categories;
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE ARTICLE
    |--------------------------------------------------------------------------
    |
    | $thumbnailPath:
    | - null   => kolom thumbnail_path TIDAK diubah (dipakai kalau admin
    |             tidak upload gambar baru saat edit)
    | - string => thumbnail_path diupdate ke path baru
    |
    */

    public function updateArticle(
        $articleId,
        $title,
        $excerpt,
        $content,
        $categoryId,
        $thumbnailPath = null
    ) {

        $articleId = (int) $articleId;

        $categoryId = (int) $categoryId;


        if (
            $articleId <= 0
            || $title === ''
            || $content === ''
            || $categoryId <= 0
        ) {

            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | SLUG
        |--------------------------------------------------------------------------
        */

        $slug = $this->createUniqueSlug(
            $title,
            $articleId
        );


        $adminId = Auth::id();


        /*
        |--------------------------------------------------------------------------
        | QUERY (beda tergantung ada thumbnail baru atau tidak)
        |--------------------------------------------------------------------------
        */

        if ($thumbnailPath !== null) {

            $stmt = $this->db->prepare("
                UPDATE articles

                SET
                    title = ?,
                    slug = ?,
                    excerpt = ?,
                    content = ?,
                    category_id = ?,
                    thumbnail_path = ?,
                    last_edited_by = ?,
                    updated_at = NOW()

                WHERE id = ?
            ");


            if (!$stmt) {
                return false;
            }


            // FIX: urutan tipe harus sama dengan urutan variabel:
            // title(s) slug(s) excerpt(s) content(s) categoryId(i)
            // thumbnailPath(s) adminId(i) articleId(i)
            // Sebelumnya 'sssssiii' sehingga thumbnailPath dibaca sebagai
            // integer dan tersimpan sebagai 0.
            $stmt->bind_param(
                'ssssisii',
                $title,
                $slug,
                $excerpt,
                $content,
                $categoryId,
                $thumbnailPath,
                $adminId,
                $articleId
            );

        } else {

            $stmt = $this->db->prepare("
                UPDATE articles

                SET
                    title = ?,
                    slug = ?,
                    excerpt = ?,
                    content = ?,
                    category_id = ?,
                    last_edited_by = ?,
                    updated_at = NOW()

                WHERE id = ?
            ");


            if (!$stmt) {
                return false;
            }


            $stmt->bind_param(
                'ssssiii',
                $title,
                $slug,
                $excerpt,
                $content,
                $categoryId,
                $adminId,
                $articleId
            );
        }


        $success = $stmt->execute();


        $stmt->close();


        return $success;
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE UNIQUE SLUG
    |--------------------------------------------------------------------------
    */

    private function createUniqueSlug(
        $title,
        $excludeId = 0
    ) {

        $slug = strtolower(
            trim($title)
        );


        $slug = preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $slug
        );


        $slug = trim(
            $slug,
            '-'
        );


        if ($slug === '') {

            $slug = 'artikel';
        }


        $baseSlug = $slug;

        $counter = 1;


        while (true) {

            if ($excludeId > 0) {

                $stmt = $this->db->prepare("
                    SELECT id
                    FROM articles
                    WHERE slug = ?
                    AND id != ?
                    LIMIT 1
                ");

                $stmt->bind_param(
                    'si',
                    $slug,
                    $excludeId
                );

            } else {

                $stmt = $this->db->prepare("
                    SELECT id
                    FROM articles
                    WHERE slug = ?
                    LIMIT 1
                ");

                $stmt->bind_param(
                    's',
                    $slug
                );
            }


            $stmt->execute();


            $result =
                $stmt->get_result();


            $exists =
                $result->num_rows > 0;


            $stmt->close();


            if (!$exists) {
                break;
            }


            $counter++;


            $slug =
                $baseSlug .
                '-' .
                $counter;
        }


        return $slug;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE ARTICLE
    |--------------------------------------------------------------------------
    */

    public function deleteArticle($articleId)
    {
        $articleId = (int) $articleId;


        if ($articleId <= 0) {
            return false;
        }


        $stmt = $this->db->prepare("
            DELETE FROM articles
            WHERE id = ?
        ");


        if (!$stmt) {
            return false;
        }


        $stmt->bind_param(
            'i',
            $articleId
        );


        $success = $stmt->execute();


        $stmt->close();


        return $success;
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        $articleId,
        $newStatus,
        $adminId,
        $note = ''
    ) {

        $allowedStatuses = [
            'DRAFT',
            'MENUNGGU_REVIEW',
            'REVISI',
            'DITOLAK',
            'PUBLISHED'
        ];


        if (
            !in_array(
                $newStatus,
                $allowedStatuses,
                true
            )
        ) {

            return false;
        }


        $article =
            $this->getArticleById(
                $articleId
            );


        if (!$article) {
            return false;
        }


        $previousStatus =
            $article['status'];


        $this->db->begin_transaction();


        try {

            if ($newStatus === 'PUBLISHED') {

                $stmt = $this->db->prepare("
                    UPDATE articles

                    SET
                        status = ?,
                        published_at = NOW(),
                        unpublished_at = NULL,
                        revision_note = NULL,
                        rejection_reason = NULL,
                        last_edited_by = ?,
                        updated_at = NOW()

                    WHERE id = ?
                ");

            } elseif (
                $newStatus === 'REVISI'
            ) {

                $stmt = $this->db->prepare("
                    UPDATE articles

                    SET
                        status = ?,
                        revision_note = ?,
                        last_edited_by = ?,
                        updated_at = NOW()

                    WHERE id = ?
                ");

            } elseif (
                $newStatus === 'DITOLAK'
            ) {

                $stmt = $this->db->prepare("
                    UPDATE articles

                    SET
                        status = ?,
                        rejection_reason = ?,
                        last_edited_by = ?,
                        updated_at = NOW()

                    WHERE id = ?
                ");

            } else {

                $stmt = $this->db->prepare("
                    UPDATE articles

                    SET
                        status = ?,
                        last_edited_by = ?,
                        updated_at = NOW()

                    WHERE id = ?
                ");
            }


            if (
                !$stmt
            ) {

                throw new Exception(
                    'Gagal membuat query status.'
                );
            }


            if ($newStatus === 'PUBLISHED') {

                $stmt->bind_param(
                    'sii',
                    $newStatus,
                    $adminId,
                    $articleId
                );

            } elseif (
                $newStatus === 'REVISI'
                || $newStatus === 'DITOLAK'
            ) {

                $stmt->bind_param(
                    'ssii',
                    $newStatus,
                    $note,
                    $adminId,
                    $articleId
                );

            } else {

                $stmt->bind_param(
                    'sii',
                    $newStatus,
                    $adminId,
                    $articleId
                );
            }


            $stmt->execute();

            $stmt->close();


            /*
            |--------------------------------------------------------------------------
            | LOG
            |--------------------------------------------------------------------------
            */

            $logStmt = $this->db->prepare("
                INSERT INTO article_status_logs
                (
                    article_id,
                    changed_by,
                    previous_status,
                    new_status,
                    note
                )

                VALUES (?, ?, ?, ?, ?)
            ");


            if ($logStmt) {

                $logStmt->bind_param(
                    'iisss',
                    $articleId,
                    $adminId,
                    $previousStatus,
                    $newStatus,
                    $note
                );


                $logStmt->execute();

                $logStmt->close();
            }


            $this->db->commit();


            return true;

        } catch (
            Throwable $e
        ) {

            $this->db->rollback();

            error_log(
                'Update status artikel: ' .
                $e->getMessage()
            );


            return false;
        }
    }
}