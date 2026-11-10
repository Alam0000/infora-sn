<?php

class Category extends Model
{
    public function getAll()
    {
        return $this->fetchAll(
            "
            SELECT
                id,
                name,
                slug,
                description
            FROM categories
            ORDER BY name ASC
            "
        );
    }

    public function findById($id)
    {
        return $this->fetchOne(
            "
            SELECT
                id,
                name,
                slug,
                description
            FROM categories
            WHERE id = ?
            LIMIT 1
            ",
            "i",
            [$id]
        );
    }
}