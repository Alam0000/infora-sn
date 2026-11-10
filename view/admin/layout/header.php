<?php

$user = $user ?? Auth::user();

$baseUrl = Auth::baseUrl();

$userName = $user['name'] ?? 'Admin';

$userInitial = strtoupper(
    mb_substr(
        trim($userName),
        0,
        1
    )
);

?>

<header class="admin-header">

    <div class="admin-header-left">

        <div class="admin-page-title">
            <?= htmlspecialchars(
                $pageTitle ?? 'Dashboard Redaksi'
            ) ?>
        </div>

        <div class="admin-page-description">
            <?= htmlspecialchars(
                $pageDescription
                ?? 'Kelola naskah, review, kurasi, dan publikasi INFORA SN.'
            ) ?>
        </div>

    </div>


    <div class="admin-header-right">

        <div class="admin-user-avatar">
            <?= htmlspecialchars($userInitial) ?>
        </div>

        <div class="admin-user-info">

            <div class="admin-user-name">
                <?= htmlspecialchars($userName) ?>
            </div>

            <div class="admin-user-role">
                ADMIN / REDAKSI
            </div>

        </div>

    </div>

</header>


<style>

/* =============================================================
   ADMIN HEADER
============================================================= */

.admin-header {

    position: sticky;

    top: 0;

    z-index: 900;

    height: 72px;

    width: 100%;

    box-sizing: border-box;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 26px 0 30px;

    background: #ffffff;

    border-bottom: 1px solid #dddddd;

}


/* =============================================================
   LEFT
============================================================= */

.admin-header-left {

    min-width: 0;

}


.admin-page-title {

    margin: 0;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 21px;

    font-weight: 800;

    line-height: 1.15;

    letter-spacing: -0.5px;

    color: #111111;

}


.admin-page-description {

    margin-top: 4px;

    font-size: 9px;

    line-height: 1.3;

    color: #888888;

}


/* =============================================================
   RIGHT
============================================================= */

.admin-header-right {

    display: flex;

    align-items: center;

    gap: 10px;

    flex-shrink: 0;

}


.admin-user-avatar {

    width: 35px;

    height: 35px;

    display: flex;

    align-items: center;

    justify-content: center;

    box-sizing: border-box;

    border-radius: 50%;

    background: #111111;

    color: #caff00;

    font-family: Georgia, serif;

    font-size: 12px;

    font-weight: 700;

}


.admin-user-info {

    min-width: 82px;

}


.admin-user-name {

    font-size: 10px;

    font-weight: 800;

    color: #222222;

}


.admin-user-role {

    margin-top: 3px;

    font-size: 6px;

    letter-spacing: 0.7px;

    color: #999999;

}


/* =============================================================
   RESPONSIVE
============================================================= */

@media (max-width: 700px) {

    .admin-header {

        padding: 0 18px;

    }


    .admin-page-title {

        font-size: 18px;

    }


    .admin-page-description {

        display: none;

    }


    .admin-user-info {

        display: none;

    }

}

</style>