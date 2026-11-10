<?php

class AdminCategory extends Model
{
    /*
    |--------------------------------------------------------------------------
    | AMBIL SEMUA KATEGORI
    |--------------------------------------------------------------------------
    */

    public function getAllCategories($keyword = '')
    {
        $keyword = trim($keyword);

        if ($keyword !== '') {

            $search = '%' . $keyword . '%';

            $stmt = $this->db->prepare("
                SELECT
                    c.id,
                    c.parent_id,
                    c.name,
                    c.slug,
                    c.description,
                    c.created_at,
                    c.updated_at,
                    p.name AS parent_name,
                    COUNT(a.id) AS article_count
                FROM categories c
                LEFT JOIN categories p
                    ON p.id = c.parent_id
                LEFT JOIN articles a
                    ON a.category_id = c.id
                WHERE
                    c.name LIKE ?
                    OR c.slug LIKE ?
                    OR c.description LIKE ?
                GROUP BY
                    c.id,
                    c.parent_id,
                    c.name,
                    c.slug,
                    c.description,
                    c.created_at,
                    c.updated_at,
                    p.name
                ORDER BY
                    (c.parent_id IS NULL) DESC,
                    c.name ASC
            ");

            $stmt->bind_param(
                "sss",
                $search,
                $search,
                $search
            );

            $stmt->execute();

            $result = $stmt->get_result();

        } else {

            $result = $this->db->query("
                SELECT
                    c.id,
                    c.parent_id,
                    c.name,
                    c.slug,
                    c.description,
                    c.created_at,
                    c.updated_at,
                    p.name AS parent_name,
                    COUNT(a.id) AS article_count
                FROM categories c
                LEFT JOIN categories p
                    ON p.id = c.parent_id
                LEFT JOIN articles a
                    ON a.category_id = c.id
                GROUP BY
                    c.id,
                    c.parent_id,
                    c.name,
                    c.slug,
                    c.description,
                    c.created_at,
                    c.updated_at,
                    p.name
                ORDER BY
                    (c.parent_id IS NULL) DESC,
                    c.name ASC
            ");
        }

        $categories = [];

        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    }


    /*
    |--------------------------------------------------------------------------
    | AMBIL KATEGORI INDUK (UNTUK DROPDOWN "KATEGORI INDUK")
    |--------------------------------------------------------------------------
    | Hanya kategori level-atas (parent_id IS NULL) yang boleh jadi induk.
    | Ini membatasi hierarki maksimal 2 level (induk -> sub), sesuai pola Kompas.
    */

    public function getParentCategories($exceptId = 0)
    {
        $exceptId = (int) $exceptId;

        $stmt = $this->db->prepare("
            SELECT
                id,
                name
            FROM categories
            WHERE parent_id IS NULL
              AND id != ?
            ORDER BY name ASC
        ");

        $stmt->bind_param(
            "i",
            $exceptId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $categories = [];

        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    }


    /*
    |--------------------------------------------------------------------------
    | AMBIL KATEGORI BERDASARKAN ID
    |--------------------------------------------------------------------------
    */

    public function getCategoryById($id)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT
                id,
                parent_id,
                name,
                slug,
                description,
                created_at,
                updated_at
            FROM categories
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc() ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | CEK NAMA KATEGORI
    |--------------------------------------------------------------------------
    */

    public function nameExists($name, $exceptId = 0)
    {
        $name = trim($name);
        $exceptId = (int) $exceptId;

        if ($exceptId > 0) {

            $stmt = $this->db->prepare("
                SELECT id
                FROM categories
                WHERE LOWER(name) = LOWER(?)
                AND id != ?
                LIMIT 1
            ");

            $stmt->bind_param(
                "si",
                $name,
                $exceptId
            );

        } else {

            $stmt = $this->db->prepare("
                SELECT id
                FROM categories
                WHERE LOWER(name) = LOWER(?)
                LIMIT 1
            ");

            $stmt->bind_param(
                "s",
                $name
            );
        }

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }


    /*
    |--------------------------------------------------------------------------
    | CEK SLUG
    |--------------------------------------------------------------------------
    */

    public function slugExists($slug, $exceptId = 0)
    {
        $slug = trim($slug);
        $exceptId = (int) $exceptId;

        if ($exceptId > 0) {

            $stmt = $this->db->prepare("
                SELECT id
                FROM categories
                WHERE LOWER(slug) = LOWER(?)
                AND id != ?
                LIMIT 1
            ");

            $stmt->bind_param(
                "si",
                $slug,
                $exceptId
            );

        } else {

            $stmt = $this->db->prepare("
                SELECT id
                FROM categories
                WHERE LOWER(slug) = LOWER(?)
                LIMIT 1
            ");

            $stmt->bind_param(
                "s",
                $slug
            );
        }

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }


    /*
    |--------------------------------------------------------------------------
    | BUAT SLUG
    |--------------------------------------------------------------------------
    */

    public function makeSlug($text)
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        $text = strtolower($text);

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

        return trim(
            $text,
            '-'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH KATEGORI
    |--------------------------------------------------------------------------
    */

    public function createCategory(
        $name,
        $slug,
        $description,
        $parentId = null
    ) {
        $name = trim($name);
        $slug = trim($slug);
        $description = trim($description);

        $parentId = ($parentId === null || (int) $parentId <= 0)
            ? null
            : (int) $parentId;

        $stmt = $this->db->prepare("
            INSERT INTO categories
            (
                parent_id,
                name,
                slug,
                description
            )
            VALUES
            (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isss",
            $parentId,
            $name,
            $slug,
            $description
        );

        return $stmt->execute();
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE KATEGORI
    |--------------------------------------------------------------------------
    */

    public function updateCategory(
        $id,
        $name,
        $slug,
        $description,
        $parentId = null
    ) {
        $id = (int) $id;

        $name = trim($name);
        $slug = trim($slug);
        $description = trim($description);

        $parentId = ($parentId === null || (int) $parentId <= 0)
            ? null
            : (int) $parentId;

        if ($id <= 0) {
            return false;
        }

        /*
        | Kategori tidak boleh jadi induk dari dirinya sendiri
        */

        if ($parentId === $id) {
            $parentId = null;
        }

        $stmt = $this->db->prepare("
            UPDATE categories
            SET
                parent_id = ?,
                name = ?,
                slug = ?,
                description = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "isssi",
            $parentId,
            $name,
            $slug,
            $description,
            $id
        );

        return $stmt->execute();
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS KATEGORI
    |--------------------------------------------------------------------------
    */

    public function deleteCategory($id)
    {
        $id = (int) $id;

        if ($id <= 0) {

            return [
                'success' => false,
                'message' => 'ID kategori tidak valid.'
            ];
        }


        /*
        | Cek kategori
        */

        $stmt = $this->db->prepare("
            SELECT id
            FROM categories
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if (!$result->fetch_assoc()) {

            return [
                'success' => false,
                'message' => 'Kategori tidak ditemukan.'
            ];
        }


        /*
        | Cek sub-kategori yang masih menggunakan kategori ini sebagai induk
        */

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM categories
            WHERE parent_id = ?
        ");

        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $totalChildren = (int) (
            $row['total'] ?? 0
        );

        if ($totalChildren > 0) {

            return [
                'success' => false,
                'message' =>
                    'Kategori tidak dapat dihapus karena masih memiliki '
                    . $totalChildren
                    . ' sub-kategori. Hapus atau pindahkan sub-kategori tersebut terlebih dahulu.'
            ];
        }


        /*
        | Cek artikel yang menggunakan kategori
        */

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM articles
            WHERE category_id = ?
        ");

        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $totalArticles = (int) (
            $row['total'] ?? 0
        );


        if ($totalArticles > 0) {

            return [
                'success' => false,
                'message' =>
                    'Kategori tidak dapat dihapus karena masih digunakan oleh '
                    . $totalArticles
                    . ' artikel.'
            ];
        }


        /*
        | Hapus kategori
        */

        $stmt = $this->db->prepare("
            DELETE FROM categories
            WHERE id = ?
        ");

        $stmt->bind_param(
            "i",
            $id
        );

        if (!$stmt->execute()) {

            return [
                'success' => false,
                'message' => 'Kategori gagal dihapus.'
            ];
        }


        return [
            'success' => true,
            'message' => 'Kategori berhasil dihapus.'
        ];
    }
}