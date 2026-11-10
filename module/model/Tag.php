<?php

class Tag extends Model
{
    public function getAll()
    {
        return $this->fetchAll(
            "
            SELECT id, name
            FROM tags
            ORDER BY name ASC
            "
        );
    }

    public function findById($id)
    {
        return $this->fetchOne(
            "
            SELECT id, name
            FROM tags
            WHERE id = ?
            LIMIT 1
            ",
            "i",
            [$id]
        );
    }

    public function create($name)
    {
        return $this->execute(
            "
            INSERT INTO tags (name)
            VALUES (?)
            ",
            "s",
            [$name]
        );
    }

    /**
     * =========================================================
     * FIND TAG BY NAME (tidak case-sensitive)
     * =========================================================
     */
    public function findByName($name)
    {
        return $this->fetchOne(
            "
            SELECT id, name
            FROM tags
            WHERE LOWER(name) = LOWER(?)
            LIMIT 1
            ",
            "s",
            [$name]
        );
    }

    /**
     * =========================================================
     * CREATE TAG DAN KEMBALIKAN ID-NYA
     * =========================================================
     *
     * create() lama tetap dipertahankan supaya kode lain
     * yang memakainya tidak berubah perilakunya.
     */
    public function createAndGetId($name)
    {
        $this->execute(
            "
            INSERT INTO tags (name)
            VALUES (?)
            ",
            "s",
            [$name]
        );

        return (int) $this->lastInsertId();
    }
}