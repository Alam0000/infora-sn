<?php

class AdminArticleCreate extends Model
{
    /**
     * Ambil semua kategori.
     */
    public function getCategories()
    {
        $sql = "
            SELECT
                id,
                name,
                slug
            FROM categories
            ORDER BY name ASC
        ";

        $result = $this->db->query($sql);

        $categories = [];

        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    }

    /**
     * Ambil semua tag.
     *
     * Dibutuhkan supaya form admin (article_form.php) bisa
     * menampilkan daftar checkbox tag, sama seperti di form client.
     */
    public function getTags()
    {
        $sql = "
            SELECT
                id,
                name
            FROM tags
            ORDER BY name ASC
        ";

        $result = $this->db->query($sql);

        $tags = [];

        while ($row = $result->fetch_assoc()) {
            $tags[] = $row;
        }

        return $tags;
    }

    /**
     * Membuat artikel baru dari Admin.
     *
     * author_id:
     * ID admin yang membuat artikel untuk kebutuhan internal.
     *
     * author_override_name:
     * Nama penulis asli yang akan ditampilkan di portal publik.
     *
     * Return value:
     * ID artikel yang baru dibuat (int) kalau berhasil,
     * atau false kalau gagal. ID ini dibutuhkan untuk
     * menyimpan relasi tag lewat syncTags().
     */
    public function createArticle(
        $authorId,
        $authorOverride,
        $title,
        $slug,
        $excerpt,
        $content,
        $thumbnailPath,
        $categoryId,
        $status
    ) {
        $authorId = (int) $authorId;
        $categoryId = (int) $categoryId;

        $submittedAt = null;
        $publishedAt = null;

        if ($status === 'MENUNGGU_REVIEW') {
            $submittedAt = date('Y-m-d H:i:s');
        }

        if ($status === 'PUBLISHED') {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $sql = "
            INSERT INTO articles (
                author_id,
                category_id,
                title,
                slug,
                excerpt,
                content,
                thumbnail_path,
                status,
                author_override_name,
                submitted_at,
                published_at
            )
            VALUES (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bind_param(
            'iisssssssss',
            $authorId,
            $categoryId,
            $title,
            $slug,
            $excerpt,
            $content,
            $thumbnailPath,
            $status,
            $authorOverride,
            $submittedAt,
            $publishedAt
        );

        $success = $stmt->execute();

        $newId = $stmt->insert_id;

        $stmt->close();

        if (!$success) {
            return false;
        }

        return $newId;
    }

    /**
     * Simpan relasi artikel <-> tag ke tabel pivot article_tags.
     *
     * Dipanggil setelah createArticle() berhasil dan mengembalikan
     * ID artikel. Menghapus dulu relasi lama (jaga-jaga dipakai
     * ulang untuk update) baru insert ulang sesuai $tagIds.
     *
     * PENTING: sesuaikan nama tabel/kolom "article_tags",
     * "article_id", "tag_id" kalau di skema kamu berbeda.
     */
    public function syncTags($articleId, array $tagIds)
    {
        $articleId = (int) $articleId;

        $stmt = $this->db->prepare(
            "
            DELETE FROM article_tags
            WHERE article_id = ?
            "
        );

        $stmt->bind_param('i', $articleId);
        $stmt->execute();
        $stmt->close();

        if (empty($tagIds)) {
            return true;
        }

        $stmt = $this->db->prepare(
            "
            INSERT INTO article_tags (
                article_id,
                tag_id
            )
            VALUES (
                ?,
                ?
            )
            "
        );

        foreach ($tagIds as $tagId) {

            $tagId = (int) $tagId;

            $stmt->bind_param(
                'ii',
                $articleId,
                $tagId
            );

            $stmt->execute();
        }

        $stmt->close();

        return true;
    }

    /**
     * Mengecek apakah slug sudah digunakan.
     */
    public function slugExists($slug)
    {
        $stmt = $this->db->prepare(
            "
            SELECT id
            FROM articles
            WHERE slug = ?
            LIMIT 1
            "
        );

        $stmt->bind_param(
            's',
            $slug
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $exists = $result->num_rows > 0;

        $stmt->close();

        return $exists;
    }

    /**
     * Membuat slug dari judul artikel.
     */
    public function makeSlug($text)
    {
        $text = strtolower(trim($text));

        $text = preg_replace(
            '/[^a-z0-9\s-]/',
            '',
            $text
        );

        $text = preg_replace(
            '/[\s-]+/',
            '-',
            $text
        );

        $text = trim(
            $text,
            '-'
        );

        return $text;
    }
}