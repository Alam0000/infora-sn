<?php

class Article extends Model
{
    /**
     * =========================================================
     * CLIENT DASHBOARD
     * =========================================================
     */
    public function getClientStats($authorId)
    {
        $result = $this->fetchOne(
            "
            SELECT
                COUNT(*) AS total,
                SUM(status = 'DRAFT') AS draft,
                SUM(status = 'MENUNGGU_REVIEW') AS menunggu_review,
                SUM(status = 'REVISI') AS revisi,
                SUM(status = 'PUBLISHED') AS published
            FROM articles
            WHERE author_id = ?
            ",
            "i",
            [$authorId]
        );

        return [
            'total' => (int) ($result['total'] ?? 0),
            'draft' => (int) ($result['draft'] ?? 0),
            'menunggu_review' => (int) ($result['menunggu_review'] ?? 0),
            'revisi' => (int) ($result['revisi'] ?? 0),
            'published' => (int) ($result['published'] ?? 0)
        ];
    }

    /**
     * =========================================================
     * GET ARTICLE BY AUTHOR
     * =========================================================
     */
    public function getByAuthor($authorId)
    {
        return $this->fetchAll(
            "
            SELECT
                a.id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.thumbnail_path,
                a.status,
                a.revision_note,
                a.rejection_reason,
                a.submitted_at,
                a.published_at,
                a.created_at,
                a.updated_at,
                c.name AS category_name
            FROM articles a
            LEFT JOIN categories c
                ON c.id = a.category_id
            WHERE a.author_id = ?
            ORDER BY a.updated_at DESC
            ",
            "i",
            [$authorId]
        );
    }

    /**
     * =========================================================
     * CREATE ARTICLE / DRAFT
     * =========================================================
     */
    public function createDraft(
        $authorId,
        $categoryId,
        $title,
        $slug,
        $excerpt,
        $content
    ) {
        $this->execute(
            "
            INSERT INTO articles
            (
                author_id,
                category_id,
                title,
                slug,
                excerpt,
                content,
                status,
                last_edited_by,
                created_at,
                updated_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                'DRAFT',
                ?,
                NOW(),
                NOW()
            )
            ",
            "iissssi",
            [
                $authorId,
                $categoryId,
                $title,
                $slug,
                $excerpt,
                $content,
                $authorId
            ]
        );

        return $this->lastInsertId();
    }

    /**
     * =========================================================
     * FIND ARTICLE MILIK CLIENT
     * =========================================================
     */
    public function findByIdAndAuthor(
        $articleId,
        $authorId
    ) {
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
                a.status,
                a.revision_note,
                a.rejection_reason,
                a.submitted_at,
                a.published_at,
                a.created_at,
                a.updated_at,
                c.name AS category_name
            FROM articles a
            LEFT JOIN categories c
                ON c.id = a.category_id
            WHERE a.id = ?
              AND a.author_id = ?
            LIMIT 1
            ",
            "ii",
            [
                $articleId,
                $authorId
            ]
        );
    }

    /**
     * =========================================================
     * UPDATE ARTICLE / DRAFT
     * =========================================================
     */
    public function updateDraft(
        $articleId,
        $authorId,
        $categoryId,
        $title,
        $excerpt,
        $content
    ) {
        return $this->execute(
            "
            UPDATE articles
            SET
                category_id = ?,
                title = ?,
                excerpt = ?,
                content = ?,
                last_edited_by = ?,
                updated_at = NOW()
            WHERE id = ?
              AND author_id = ?
              AND status IN ('DRAFT', 'REVISI')
            ",
            "isssiii",
            [
                $categoryId,
                $title,
                $excerpt,
                $content,
                $authorId,
                $articleId,
                $authorId
            ]
        );
    }

    /**
     * =========================================================
     * DELETE ARTICLE
     * =========================================================
     *
     * Digunakan untuk membersihkan artikel apabila
     * proses pembuatan artikel gagal.
     */
    public function deleteArticle(
        $articleId,
        $authorId
    ) {
        return $this->execute(
            "
            DELETE FROM articles
            WHERE id = ?
              AND author_id = ?
            ",
            "ii",
            [
                $articleId,
                $authorId
            ]
        );
    }

    /**
     * =========================================================
     * THUMBNAIL
     * =========================================================
     */
    public function updateThumbnail(
        $articleId,
        $thumbnailPath
    ) {
        return $this->execute(
            "
            UPDATE articles
            SET
                thumbnail_path = ?,
                updated_at = NOW()
            WHERE id = ?
            ",
            "si",
            [
                $thumbnailPath,
                $articleId
            ]
        );
    }

    /**
     * =========================================================
     * GET THUMBNAIL IMAGE
     * =========================================================
     */
    public function getThumbnailImage(
        $articleId
    ) {
        return $this->fetchOne(
            "
            SELECT
                id,
                article_id,
                file_name,
                file_path,
                file_size,
                file_format,
                is_thumbnail,
                created_at
            FROM article_images
            WHERE article_id = ?
              AND is_thumbnail = 1
            ORDER BY id DESC
            LIMIT 1
            ",
            "i",
            [$articleId]
        );
    }

    /**
     * =========================================================
     * REMOVE THUMBNAIL IMAGES
     * =========================================================
     */
    public function removeThumbnailImages(
        $articleId
    ) {
        return $this->execute(
            "
            DELETE FROM article_images
            WHERE article_id = ?
              AND is_thumbnail = 1
            ",
            "i",
            [$articleId]
        );
    }

    /**
     * =========================================================
     * ADD THUMBNAIL IMAGE
     * =========================================================
     */
    public function addThumbnailImage(
        $articleId,
        $fileName,
        $filePath,
        $fileSize,
        $fileFormat
    ) {
        return $this->execute(
            "
            INSERT INTO article_images
            (
                article_id,
                file_name,
                file_path,
                file_size,
                file_format,
                is_thumbnail,
                created_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                1,
                NOW()
            )
            ",
            "issis",
            [
                $articleId,
                $fileName,
                $filePath,
                $fileSize,
                $fileFormat
            ]
        );
    }

    /**
     * =========================================================
     * TAG
     * =========================================================
     */
    public function addTag(
        $articleId,
        $tagId
    ) {
        return $this->execute(
            "
            INSERT INTO article_tags
            (
                article_id,
                tag_id
            )
            VALUES
            (
                ?,
                ?
            )
            ",
            "ii",
            [
                $articleId,
                $tagId
            ]
        );
    }

    /**
     * =========================================================
     * GET TAGS
     * =========================================================
     */
    public function getTags(
        $articleId
    ) {
        return $this->fetchAll(
            "
            SELECT
                t.id,
                t.name
            FROM article_tags at
            INNER JOIN tags t
                ON t.id = at.tag_id
            WHERE at.article_id = ?
            ORDER BY t.name ASC
            ",
            "i",
            [$articleId]
        );
    }

    /**
     * =========================================================
     * DELETE TAGS
     * =========================================================
     */
    public function deleteTags(
        $articleId
    ) {
        return $this->execute(
            "
            DELETE FROM article_tags
            WHERE article_id = ?
            ",
            "i",
            [$articleId]
        );
    }

    /**
     * =========================================================
     * SUBMIT ARTICLE
     * =========================================================
     */
    public function submitArticle(
        $articleId,
        $authorId
    ) {
        return $this->execute(
            "
            UPDATE articles
            SET
                status = 'MENUNGGU_REVIEW',
                submitted_at = NOW(),
                revision_note = NULL,
                rejection_reason = NULL,
                updated_at = NOW(),
                last_edited_by = ?
            WHERE id = ?
              AND author_id = ?
              AND status IN ('DRAFT', 'REVISI')
            ",
            "iii",
            [
                $authorId,
                $articleId,
                $authorId
            ]
        );
    }

    /**
     * =========================================================
     * ADD STATUS LOG
     * =========================================================
     */
    public function addStatusLog(
        $articleId,
        $changedBy,
        $previousStatus,
        $newStatus,
        $note = ''
    ) {
        return $this->execute(
            "
            INSERT INTO article_status_logs
            (
                article_id,
                changed_by,
                previous_status,
                new_status,
                note,
                created_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW()
            )
            ",
            "iisss",
            [
                $articleId,
                $changedBy,
                $previousStatus,
                $newStatus,
                $note
            ]
        );
    }
}