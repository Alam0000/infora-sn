<?php

$baseUrl = Auth::baseUrl();

$pageTitle = $pageTitle ?? 'INFORA SN';

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
        <?= htmlspecialchars($pageTitle) ?>
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f6f8;
            color: #222222;
        }


        /* =========================================================
           LAYOUT
        ========================================================== */

        .layout {
            display: flex;
            min-height: 100vh;
        }


        .page {
            flex: 1;
            min-width: 0;
            min-height: 100vh;

            display: flex;
            flex-direction: column;
        }


        /* =========================================================
           SIDEBAR
        ========================================================== */

        .sidebar {
            width: 245px;
            flex: 0 0 245px;

            background: #ffffff;

            border-right: 1px solid #dddcd6;

            padding: 26px 18px;
        }


        .logo {
            margin-bottom: 6px;

            font-family: Georgia, "Times New Roman", serif;

            font-size: 21px;
            font-weight: 700;

            color: #111111;
        }


        .logo span {
            font-weight: 400;
            color: #174bc8;
        }


        .sidebar-tagline {
            margin-bottom: 30px;

            color: #999999;

            font-size: 11px;

            letter-spacing: .04em;
        }


        .menu-title {
            margin-bottom: 10px;

            color: #999999;

            font-size: 11px;
            font-weight: 800;

            letter-spacing: .12em;

            text-transform: uppercase;
        }


        .menu {
            display: flex;
            flex-direction: column;

            gap: 2px;
        }


        .menu a {
            padding: 12px 12px;

            color: #444444;

            font-size: 13px;
            font-weight: 700;

            text-decoration: none;

            border-left: 2px solid transparent;

            transition:
                background .15s ease,
                color .15s ease,
                border-color .15s ease;
        }


        .menu a:hover {
            background: #f7f7f4;
            color: #174bc8;
        }


        .menu a.active {
            background: #eef2ff;

            color: #174bc8;

            border-left-color: #174bc8;
        }


        /* =========================================================
           MAIN
        ========================================================== */

        .main {
            flex: 1;

            min-width: 0;

            padding: 32px 40px;
        }


        /* =========================================================
           TOPBAR
        ========================================================== */

        .topbar {
            display: flex;

            justify-content: space-between;
            align-items: flex-start;

            gap: 20px;

            margin-bottom: 26px;
        }


        .topbar-kicker {
            margin-bottom: 6px;

            color: #174bc8;

            font-size: 9px;
            font-weight: 900;

            letter-spacing: .12em;

            text-transform: uppercase;
        }


        .topbar h1 {
            margin: 0 0 6px;

            font-family: Georgia, "Times New Roman", serif;

            font-size: 28px;
            font-weight: 700;

            color: #111111;
        }


        .topbar p {
            margin: 0;

            color: #777777;

            font-size: 11px;
        }


        .user-info {
            flex: 0 0 auto;

            text-align: right;
        }


        .user-name {
            color: #171717;

            font-size: 11px;
            font-weight: 800;
        }


        .user-role {
            margin-top: 3px;

            color: #999999;

            font-size: 9px;

            text-transform: uppercase;

            letter-spacing: .06em;
        }


        /* =========================================================
           STATS
        ========================================================== */

        .stats {
            display: grid;

            grid-template-columns: repeat(5, 1fr);

            gap: 12px;

            margin-bottom: 24px;
        }


        .stat-card {
            background: #ffffff;

            border: 1px solid #dddcd6;

            padding: 16px 17px;
        }


        .stat-title {
            margin-bottom: 9px;

            color: #888888;

            font-size: 9px;
            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: .05em;
        }


        .stat-number {
            font-family: Georgia, "Times New Roman", serif;

            font-size: 26px;
            font-weight: 700;

            color: #111111;
        }


        /* =========================================================
           CARD
        ========================================================== */

        .card {
            background: #ffffff;

            border: 1px solid #dddcd6;

            overflow: hidden;
        }


        .card-header {
            display: flex;

            justify-content: space-between;
            align-items: center;

            gap: 15px;

            min-height: 60px;

            padding: 14px 18px;

            border-bottom: 1px solid #e5e4df;
        }


        .card-header h2 {
            margin: 0;

            font-family: Georgia, "Times New Roman", serif;

            font-size: 17px;
            font-weight: 700;

            color: #151515;
        }


        /* =========================================================
           BUTTON
        ========================================================== */

        .btn {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            min-height: 34px;

            padding: 0 14px;

            background: #174bc8;

            border: 1px solid #174bc8;

            color: #ffffff;

            font-size: 9px;
            font-weight: 900;

            text-decoration: none;

            transition:
                background .15s ease,
                border-color .15s ease;
        }


        .btn:hover {
            background: #103da8;

            border-color: #103da8;
        }


        /* =========================================================
           TABLE
        ========================================================== */

        .table-wrap {
            width: 100%;

            overflow-x: auto;
        }


        table {
            width: 100%;

            min-width: 700px;

            border-collapse: collapse;
        }


        th,
        td {
            padding: 13px 18px;

            text-align: left;

            border-bottom: 1px solid #ecebe7;
        }


        th {
            background: #f7f7f4;

            color: #666666;

            font-size: 8px;
            font-weight: 900;

            text-transform: uppercase;

            letter-spacing: .06em;
        }


        td {
            color: #333333;

            font-size: 10px;
        }


        tbody tr:last-child td {
            border-bottom: 0;
        }


        tbody tr:hover {
            background: #fafafa;
        }


        /* =========================================================
           STATUS
        ========================================================== */

        .status {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            padding: 5px 8px;

            background: #eeeeec;

            color: #555555;

            font-size: 8px;
            font-weight: 900;

            white-space: nowrap;
        }


        .status.draft {
            background: #eeeeec;
            color: #666666;
        }


        .status.menunggu-review {
            background: #fff3cd;
            color: #8a6d1a;
        }


        .status.revisi {
            background: #ffe5e5;
            color: #a00000;
        }


        .status.ditolak {
            background: #f8d7da;
            color: #8a1f2b;
        }


        .status.published {
            background: #e2f5b7;
            color: #4a6800;
        }


        /* =========================================================
           ARTICLE TITLE
        ========================================================== */

        .article-title {
            color: #171717;

            font-weight: 800;

            text-decoration: none;
        }


        .article-title:hover {
            color: #174bc8;

            text-decoration: underline;
        }


        /* =========================================================
           EMPTY
        ========================================================== */

        .empty {
            padding: 60px 20px;

            text-align: center;
        }


        .empty p {
            margin: 0 0 16px;

            color: #888888;

            font-size: 11px;
        }


        /* =========================================================
           FOOTER
        ========================================================== */

        .footer {
            margin-top: auto;

            padding: 18px 40px;

            background: #ffffff;

            border-top: 1px solid #dddcd6;

            color: #999999;

            font-size: 9px;

            text-align: center;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1100px) {

            .stats {
                grid-template-columns: repeat(3, 1fr);
            }

        }


        @media (max-width: 900px) {

            .sidebar {
                width: 210px;

                flex: 0 0 210px;
            }


            .main {
                padding: 26px 24px;
            }


            .footer {
                padding: 18px 24px;
            }

        }


        @media (max-width: 700px) {

            .layout {
                flex-direction: column;
            }


            .sidebar {
                width: 100%;

                flex: 0 0 auto;

                border-right: 0;

                border-bottom: 1px solid #dddcd6;
            }


            .stats {
                grid-template-columns: repeat(2, 1fr);
            }


            .main {
                padding: 20px;
            }


            .topbar {
                flex-direction: column;
            }


            .user-info {
                text-align: left;
            }


            .footer {
                padding: 16px 20px;
            }

        }


        @media (max-width: 450px) {

            .stats {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>

<div class="layout">