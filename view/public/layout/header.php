<?php

$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

if ($baseUrl === '/' || $baseUrl === '\\') {
    $baseUrl = '';
}

/*
|--------------------------------------------------------------------------
| LOGO
|--------------------------------------------------------------------------
| Simpan file logo di:
| public/assets/img/logo.png
|
| Kalau nama / format file berbeda (mis. logo.svg, logo.webp),
| cukup ubah nilai $logoPath di bawah ini.
*/
$logoPath = '/view/public/assets/img/Logo_infora.jpeg';

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($pageTitle ?? 'INFORA SN - Media Online Nasional') ?>
    </title>

    <!-- Favicon (opsional, memakai logo yang sama) -->
    <link
        rel="icon"
        href="<?= $baseUrl . $logoPath ?>"
    >

    <style>

        /* =====================================================
           RESET
        ===================================================== */

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            padding: 0;

            background: #f5f5f2;

            color: #101010;

            font-family:
                Arial,
                Helvetica,
                sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            display: block;
            max-width: 100%;
        }


        /* =====================================================
           CONTAINER
        ===================================================== */

        .site-container {
            width: min(
                1240px,
                calc(100% - 110px)
            );

            margin-left: auto;
            margin-right: auto;
        }


        /* =====================================================
           TOPIK HANGAT
        ===================================================== */

        .hot-topics {
            width: 100%;

            height: 36px;

            background: #101010;

            color: #ffffff;
        }

        .hot-topics-inner {
            width: min(
                1240px,
                calc(100% - 110px)
            );

            height: 36px;

            margin-left: auto;
            margin-right: auto;

            display: flex;

            align-items: center;

            gap: 25px;

            overflow: hidden;

            white-space: nowrap;
        }

        .hot-label {
            flex: 0 0 auto;

            color: #d8ff00;

            font-size: 11px;

            font-weight: 900;

            letter-spacing: .12em;
        }

        .hot-topic {
            flex: 0 0 auto;

            color: #dddddd;

            font-size: 11px;
        }


        /* =====================================================
           HEADER UTAMA
        ===================================================== */

        .site-header {
            width: 100%;

            background: #ffffff;

            border-bottom: 1px solid #d7d7d1;
        }

        .header-main {
            width: min(
                1240px,
                calc(100% - 110px)
            );

            height: 104px;

            margin-left: auto;
            margin-right: auto;

            display: grid;

            grid-template-columns:
                1fr
                1fr
                1fr;

            align-items: center;

            gap: 25px;
        }


        /* =====================================================
           LOGO
        ===================================================== */

        .brand-area {
            display: flex;

            align-items: center;

            padding-left: 20px;    /* ubah angka ini untuk geser logo ke kanan / kiri */
        }

        .brand {
            display: inline-flex;

            align-items: center;
        }

        .brand img {
            display: block;

            height: 80px;      /* ubah angka ini untuk memperbesar / memperkecil logo */

            width: auto;

            max-width: 100%;

            object-fit: contain;
        }


        /* =====================================================
           TAGLINE
           Baris atas  : MEDIA ONLINE NASIONAL (identitas utama)
           Baris bawah : JURNALISME PUBLIK & ANALISIS
        ===================================================== */

        .site-tagline {
            text-align: center;

            white-space: nowrap;
        }

        .tagline-main {
            display: block;
            color: #101010;

            font-family: "Arial Narrow", "Roboto Condensed", Arial, sans-serif;
            font-size: 18px;
            font-weight: 900;

            letter-spacing: .10em;
            line-height: 1;
            text-transform: uppercase;
        }

        .tagline-sub {
            display: block;

            margin-top: 6px;

            color: #6b6b6b;

            font-size: 9px;

            font-weight: 700;

            letter-spacing: .24em;
        }


        /* =====================================================
           HEADER ACTIONS
        ===================================================== */

        .header-actions {
            display: flex;

            align-items: center;

            justify-content: flex-end;

            gap: 10px;
        }


        /* =====================================================
           SEARCH
        ===================================================== */

        .search-box {
            width: 163px;

            height: 38px;

            display: flex;

            align-items: center;

            border: 1px solid #d7d7d1;

            background: #ffffff;
        }

        .search-box input {
            width: 100%;

            height: 100%;

            flex: 1;

            border: 0;

            outline: 0;

            padding: 0 12px;

            background: transparent;

            color: #101010;

            font-size: 11px;
        }

        .search-box input::placeholder {
            color: #777777;
        }

        .search-button {
            width: 38px;

            height: 100%;

            padding: 0;

            border: 0;

            background: transparent;

            color: #101010;

            font-size: 14px;

            cursor: pointer;
        }


        /* =====================================================
           TOMBOL MASUK
        ===================================================== */

        .login-button {
            height: 38px;

            padding: 0 17px;

            display: flex;

            align-items: center;

            justify-content: center;

            border: 1px solid #101010;

            background: #ffffff;

            color: #101010;

            font-size: 11px;

            font-weight: 900;

            white-space: nowrap;
        }

        .login-button:hover {
            background: #101010;

            color: #ffffff;
        }


        /* =====================================================
           RESPONSIVE HEADER
        ===================================================== */

        /*
        | Tablet: logo + tombol di baris pertama,
        | "MEDIA ONLINE NASIONAL" di baris kedua selebar header.
        | Baris "JURNALISME PUBLIK & ANALISIS" disembunyikan
        | supaya header tetap ringkas.
        */

        @media (max-width: 1000px) {

            .site-container,
            .hot-topics-inner,
            .header-main {
                width: calc(100% - 40px);
            }

            .header-main {
                grid-template-columns:
                    1fr
                    auto;

                height: auto;

                padding: 14px 0 12px;

                gap: 10px 25px;
            }

            .brand-area {
                order: 1;
            }

            .header-actions {
                order: 2;
            }

            .site-tagline {
                order: 3;

                grid-column: 1 / -1;

                padding-top: 10px;

                border-top: 1px solid #ecebe7;
            }

            .tagline-sub {
                display: none;
            }

        }


        /*
        | HP: susunan ditumpuk ke bawah,
        | kedua baris tagline tampil.
        */

        @media (max-width: 650px) {

            .site-container,
            .hot-topics-inner,
            .header-main {
                width: calc(100% - 28px);
            }

            .header-main {
                display: flex;

                flex-direction: column;

                align-items: stretch;

                height: auto;

                padding: 20px 0;

                gap: 17px;
            }

            .brand-area,
            .site-tagline,
            .header-actions {
                order: 0;
            }

            .brand-area {
                justify-content: center;

                padding-left: 0;
            }

            .brand img {
                height: 64px;
            }

            .site-tagline {
                padding-top: 0;

                border-top: 0;
            }

            .tagline-main {
                font-size: 11px;

                letter-spacing: .16em;
            }

            .tagline-sub {
                display: block;

                margin-top: 5px;

                font-size: 8px;

                letter-spacing: .16em;
            }

            .header-actions {
                width: 100%;

                justify-content: stretch;
            }

            .search-box {
                width: auto;

                flex: 1;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     TOPIK HANGAT
========================================================= -->

<div class="hot-topics">

    <div class="hot-topics-inner">

        <div class="hot-label">
            TOPIK HANGAT
        </div>

        <div class="hot-topic">
            #IKNNusantara
        </div>

        <div class="hot-topic">
            #TransisiEnergi
        </div>

        <div class="hot-topic">
            #RUUPenyiaran
        </div>

        <div class="hot-topic">
            #EtikaDigitalAI
        </div>

    </div>

</div>


<!-- =========================================================
     HEADER
========================================================= -->

<header class="site-header">

    <div class="header-main">


        <!-- =================================================
             LOGO
        ================================================== -->

        <div class="brand-area">

            <a
                href="<?= $baseUrl ?>/"
                class="brand"
                aria-label="INFORA SN"
            >

                <img
                    src="<?= $baseUrl . $logoPath ?>"
                    alt="INFORA SN"
                >

            </a>

        </div>


        <!-- =================================================
             TAGLINE
        ================================================== -->

        <div class="site-tagline">

            <span class="tagline-main">
                MEDIA ONLINE NASIONAL
            </span>

            <span class="tagline-sub">
                JURNALISME PUBLIK &amp; ANALISIS
            </span>

        </div>


        <!-- =================================================
             SEARCH + MASUK
        ================================================== -->

        <div class="header-actions">

            <form
                action="<?= $baseUrl ?>/search"
                method="GET"
                class="search-box"
            >

                <input
                    type="search"
                    name="q"
                    placeholder="Cari liputan, opini,"
                    value="<?= htmlspecialchars(
                        $_GET['q'] ?? ''
                    ) ?>"
                >

                <button
                    type="submit"
                    class="search-button"
                    aria-label="Cari"
                >
                    ⌕
                </button>

            </form>


            <a
                href="<?= $baseUrl ?>/login"
                class="login-button"
            >
                Masuk
            </a>

        </div>

    </div>

</header>