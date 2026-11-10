<?php

class AdminAd extends Model
{
    /**
     * Ambil semua iklan untuk halaman Admin
     */
    public function getAllAds()
    {
        $sql = "
            SELECT *
            FROM ads
            ORDER BY sort_order ASC, id DESC
        ";

        $result = $this->db->query($sql);

        $ads = [];

        while ($row = $result->fetch_assoc()) {
            $ads[] = $row;
        }

        return $ads;
    }


    /**
     * Ambil iklan aktif untuk Home
     */
    public function getActiveAds($limit = 5)
    {
        $limit = max(1, (int) $limit);

        $sql = "
            SELECT *
            FROM ads
            WHERE status = 'ACTIVE'
            ORDER BY sort_order ASC, id DESC
            LIMIT ?
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bind_param(
            'i',
            $limit
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $ads = [];

        while ($row = $result->fetch_assoc()) {
            $ads[] = $row;
        }

        $stmt->close();

        return $ads;
    }


    /**
     * Ambil satu iklan berdasarkan ID
     */
    public function getAdById($id)
    {
        $id = (int) $id;

        $stmt = $this->db->prepare(
            "
            SELECT *
            FROM ads
            WHERE id = ?
            LIMIT 1
            "
        );

        $stmt->bind_param(
            'i',
            $id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $ad = $result->fetch_assoc();

        $stmt->close();

        return $ad ?: null;
    }


    /**
     * Tambah iklan
     */
    public function createAd(
        $title,
        $imagePath,
        $targetUrl,
        $status = 'ACTIVE',
        $sortOrder = 0
    ) {
        $stmt = $this->db->prepare(
            "
            INSERT INTO ads
            (
                title,
                image_path,
                target_url,
                status,
                sort_order
            )
            VALUES (?, ?, ?, ?, ?)
            "
        );

        $stmt->bind_param(
            'ssssi',
            $title,
            $imagePath,
            $targetUrl,
            $status,
            $sortOrder
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }


    /**
     * Update iklan
     */
    public function updateAd(
        $id,
        $title,
        $targetUrl,
        $status,
        $sortOrder,
        $imagePath = null
    ) {
        $id = (int) $id;

        if ($imagePath !== null) {

            $stmt = $this->db->prepare(
                "
                UPDATE ads
                SET
                    title = ?,
                    image_path = ?,
                    target_url = ?,
                    status = ?,
                    sort_order = ?
                WHERE id = ?
                "
            );

            $stmt->bind_param(
                'ssssii',
                $title,
                $imagePath,
                $targetUrl,
                $status,
                $sortOrder,
                $id
            );

        } else {

            $stmt = $this->db->prepare(
                "
                UPDATE ads
                SET
                    title = ?,
                    target_url = ?,
                    status = ?,
                    sort_order = ?
                WHERE id = ?
                "
            );

            $stmt->bind_param(
                'sssii',
                $title,
                $targetUrl,
                $status,
                $sortOrder,
                $id
            );
        }

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }


    /**
     * Hapus iklan
     */
    public function deleteAd($id)
    {
        $id = (int) $id;

        $stmt = $this->db->prepare(
            "
            DELETE FROM ads
            WHERE id = ?
            "
        );

        $stmt->bind_param(
            'i',
            $id
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }


    /**
     * Aktif / nonaktifkan iklan
     */
    public function toggleStatus($id)
    {
        $ad = $this->getAdById($id);

        if (!$ad) {
            return false;
        }

        $newStatus =
            $ad['status'] === 'ACTIVE'
                ? 'INACTIVE'
                : 'ACTIVE';

        $stmt = $this->db->prepare(
            "
            UPDATE ads
            SET status = ?
            WHERE id = ?
            "
        );

        $id = (int) $id;

        $stmt->bind_param(
            'si',
            $newStatus,
            $id
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }
}