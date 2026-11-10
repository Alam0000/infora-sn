<?php

$baseUrl = Auth::baseUrl();

$currentPath = $_SERVER['REQUEST_URI'] ?? '';

function menuActive($path)
{
    global $currentPath;

    return strpos($currentPath, $path) !== false
        ? 'active'
        : '';
}

?>

<aside class="sidebar">

    <div class="logo">
        INFORA <span>SN</span>
    </div>


    <div class="sidebar-tagline">
        PORTAL PENULIS
    </div>


    <div class="menu-title">
        Menu Penulis
    </div>


    <nav class="menu">

        <a
            href="<?= $baseUrl ?>/client/dashboard"
            class="<?= menuActive('/client/dashboard') ?>"
        >
            Dashboard
        </a>


        <a
            href="<?= $baseUrl ?>/client/article/create"
            class="<?= menuActive('/client/article/create') ?>"
        >
            Tulis Artikel
        </a>


        <a
            href="<?= $baseUrl ?>/client/articles"
            class="<?= menuActive('/client/articles') ?>"
        >
            Status Artikel
        </a>


        <a
            href="<?= $baseUrl ?>/client/revisions"
            class="<?= menuActive('/client/revisions') ?>"
        >
            Revisi Artikel
        </a>


        <a href="<?= $baseUrl ?>/client/settings">
            Pengaturan Akun
        </a>


        <a href="<?= $baseUrl ?>/logout">
            Logout
        </a>

    </nav>

</aside>

<div class="page">