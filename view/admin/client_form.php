<?php

$baseUrl = Auth::baseUrl();

$client = $client ?? null;
$errors = $errors ?? [];

$isEdit = !empty($client);

$clientId = $isEdit
    ? (int) ($client['id'] ?? 0)
    : 0;

$name =
    $client['name'] ?? '';

$email =
    $client['email'] ?? '';

$status =
    $client['account_status'] ?? 'active';

?>

<?php
/*
|--------------------------------------------------------------------------
| SIDEBAR ADMIN
|--------------------------------------------------------------------------
*/

require __DIR__ . '/layout/sidebar.php';
?>


<div class="admin-main">

    <?php
    /*
    |--------------------------------------------------------------------------
    | HEADER ADMIN
    |--------------------------------------------------------------------------
    */

    require __DIR__ . '/layout/header.php';
    ?>


    <main class="admin-content">


        <!-- =========================================================
             PAGE HEADER
        ========================================================== -->

        <div class="page-header">

            <div>

                <div class="page-kicker">
                    MANAJEMEN PENULIS & MITRA
                </div>

                <h1>

                    <?= $isEdit
                        ? 'Edit Penulis'
                        : 'Tambah Penulis'
                    ?>

                </h1>

                <p>

                    <?= $isEdit
                        ? 'Perbarui informasi akun penulis atau mitra.'
                        : 'Tambahkan akun penulis atau mitra baru.'
                    ?>

                </p>

            </div>


            <a
                href="<?= $baseUrl ?>/admin/clients"
                class="back-button"
            >
                ← Kembali
            </a>

        </div>



        <!-- =========================================================
             ERROR
        ========================================================== -->

        <?php if (!empty($errors)): ?>

            <div class="error-box">

                <div class="error-title">
                    Periksa kembali data berikut:
                </div>

                <ul>

                    <?php foreach ($errors as $error): ?>

                        <li>
                            <?= htmlspecialchars($error) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>



        <!-- =========================================================
             FORM CARD
        ========================================================== -->

        <section class="form-card">


            <div class="form-card-header">

                <div>

                    <h2>
                        Informasi Akun
                    </h2>

                    <p>
                        Kelola informasi akun penulis dan mitra INFORA SN.
                    </p>

                </div>

            </div>



            <form
                method="POST"
                action="<?= $isEdit

                    ? $baseUrl
                        . '/admin/client/'
                        . $clientId
                        . '/update'

                    : $baseUrl
                        . '/admin/client/store'

                ?>"
            >


                <!-- =================================================
                     NAMA
                ================================================== -->

                <div class="form-group">

                    <label for="name">
                        NAMA PENULIS
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?= htmlspecialchars($name) ?>"
                        placeholder="Masukkan nama penulis"
                        required
                    >

                </div>



                <!-- =================================================
                     EMAIL
                ================================================== -->

                <div class="form-group">

                    <label for="email">
                        EMAIL
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($email) ?>"
                        placeholder="Masukkan alamat email"
                        required
                    >

                </div>



                <!-- =================================================
                     PASSWORD
                ================================================== -->

                <div class="form-group">

                    <label for="password">

                        <?= $isEdit
                            ? 'PASSWORD BARU'
                            : 'PASSWORD'
                        ?>

                    </label>


                    <input
                        type="password"
                        id="password"
                        name="password"

                        placeholder="<?= $isEdit

                            ? 'Kosongkan jika tidak ingin mengubah password'

                            : 'Minimal 6 karakter'

                        ?>"

                        <?= $isEdit
                            ? ''
                            : 'required'
                        ?>

                        autocomplete="new-password"
                    >


                    <small class="field-help">

                        <?= $isEdit

                            ? 'Kosongkan jika password tidak ingin diubah.'

                            : 'Password minimal 6 karakter.'

                        ?>

                    </small>

                </div>



                <!-- =================================================
                     STATUS
                ================================================== -->

                <div class="form-group">

                    <label for="account_status">
                        STATUS AKUN
                    </label>


                    <select
                        id="account_status"
                        name="account_status"
                    >

                        <option
                            value="active"
                            <?= $status === 'active'
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Aktif
                        </option>


                        <option
                            value="inactive"
                            <?= $status === 'inactive'
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Tidak Aktif
                        </option>

                    </select>


                    <small class="field-help">

                        Akun tidak aktif tidak dapat digunakan untuk login.

                    </small>

                </div>



                <!-- =================================================
                     ROLE
                ================================================== -->

                <div class="role-info">

                    <div class="role-icon">
                        ◉
                    </div>


                    <div>

                        <strong>
                            Role: Client / Penulis
                        </strong>

                        <span>
                            Akun ini memiliki akses sebagai penulis
                            untuk membuat dan mengelola artikel.
                        </span>

                    </div>

                </div>



                <!-- =================================================
                     ACTION
                ================================================== -->

                <div class="form-actions">


                    <a
                        href="<?= $baseUrl ?>/admin/clients"
                        class="btn-cancel"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        class="btn-submit"
                    >

                        <?= $isEdit

                            ? 'Simpan Perubahan'

                            : 'Simpan Penulis'

                        ?>

                    </button>

                </div>


            </form>

        </section>


    </main>


    <?php
    /*
    |--------------------------------------------------------------------------
    | FOOTER ADMIN
    |--------------------------------------------------------------------------
    */

    require __DIR__ . '/layout/footer.php';
    ?>

</div>



<style>

/* =========================================================
   ADMIN MAIN
========================================================= */

.admin-main {

    margin-left: 245px;

    min-height: 100vh;

    background: #f4f5f7;

}



/* =========================================================
   CONTENT
========================================================= */

.admin-content {

    padding: 34px 44px 60px;

    box-sizing: border-box;

}



/* =========================================================
   PAGE HEADER
========================================================= */

.page-header {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 25px;

    margin-bottom: 28px;

}


.page-kicker {

    margin-bottom: 7px;

    color: #777777;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: 1.6px;

    text-transform: uppercase;

}


.page-header h1 {

    margin: 0 0 8px;

    color: #111111;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    font-size: 30px;

    font-weight: 900;

    letter-spacing: -1px;

}


.page-header p {

    margin: 0;

    color: #777777;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    font-size: 12px;

    line-height: 1.6;

}



/* =========================================================
   BACK BUTTON
========================================================= */

.back-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 40px;

    padding: 0 17px;

    box-sizing: border-box;

    background: #ffffff;

    border: 1px solid #d8d8d8;

    color: #222222;

    text-decoration: none;

    font-size: 11px;

    font-weight: 800;

    transition: .2s ease;

}


.back-button:hover {

    background: #111111;

    border-color: #111111;

    color: #ffffff;

}



/* =========================================================
   ERROR
========================================================= */

.error-box {

    max-width: 900px;

    margin-bottom: 22px;

    padding: 15px 18px;

    background: #fff4f4;

    border: 1px solid #e2b3b3;

    color: #9d1c1c;

    font-size: 12px;

    line-height: 1.6;

}


.error-title {

    margin-bottom: 5px;

    font-weight: 800;

}


.error-box ul {

    margin: 5px 0 0 18px;

    padding: 0;

}



/* =========================================================
   FORM CARD
========================================================= */

.form-card {

    width: 100%;

    max-width: 900px;

    background: #ffffff;

    border: 1px solid #dcdcdc;

    box-sizing: border-box;

}



/* =========================================================
   FORM HEADER
========================================================= */

.form-card-header {

    padding: 22px 25px;

    border-bottom: 1px solid #e5e5e5;

}


.form-card-header h2 {

    margin: 0 0 5px;

    color: #111111;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 20px;

    font-weight: 700;

}


.form-card-header p {

    margin: 0;

    color: #888888;

    font-size: 11px;

}



/* =========================================================
   FORM
========================================================= */

.form-card form {

    padding: 28px 25px 25px;

}



/* =========================================================
   FORM GROUP
========================================================= */

.form-group {

    margin-bottom: 22px;

}


.form-group label {

    display: block;

    margin-bottom: 8px;

    color: #555555;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: 1.2px;

    text-transform: uppercase;

}


.form-group input,
.form-group select {

    width: 100%;

    height: 44px;

    padding: 0 13px;

    box-sizing: border-box;

    background: #ffffff;

    border: 1px solid #d5d5d5;

    color: #111111;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    font-size: 12px;

    outline: none;

    transition: .2s ease;

}


.form-group input:focus,
.form-group select:focus {

    border-color: #b8e800;

    box-shadow:
        0 0 0 2px
        rgba(202, 255, 0, .16);

}


.field-help {

    display: block;

    margin-top: 7px;

    color: #8a8a8a;

    font-size: 10px;

    line-height: 1.5;

}



/* =========================================================
   ROLE INFO
========================================================= */

.role-info {

    display: flex;

    align-items: flex-start;

    gap: 12px;

    margin-top: 8px;

    margin-bottom: 25px;

    padding: 15px;

    background: #fafafa;

    border: 1px solid #e5e5e5;

}


.role-icon {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 32px;

    height: 32px;

    flex: 0 0 32px;

    background: #caff00;

    color: #111111;

    font-size: 13px;

    font-weight: 900;

}


.role-info strong {

    display: block;

    margin-bottom: 4px;

    color: #222222;

    font-size: 11px;

    font-weight: 800;

}


.role-info span {

    display: block;

    color: #777777;

    font-size: 10px;

    line-height: 1.5;

}



/* =========================================================
   ACTION
========================================================= */

.form-actions {

    display: flex;

    justify-content: flex-end;

    align-items: center;

    gap: 10px;

    padding-top: 22px;

    border-top: 1px solid #eeeeee;

}


.btn-cancel,
.btn-submit {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 42px;

    padding: 0 21px;

    box-sizing: border-box;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    font-size: 11px;

    font-weight: 800;

    text-decoration: none;

    cursor: pointer;

    transition: .2s ease;

}


.btn-cancel {

    background: #eeeeee;

    border: 1px solid #dddddd;

    color: #333333;

}


.btn-cancel:hover {

    background: #dddddd;

}


.btn-submit {

    background: #caff00;

    border: 1px solid #b6e600;

    color: #111111;

}


.btn-submit:hover {

    background: #b9eb00;

}



/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 900px) {

    .admin-main {

        margin-left: 215px;

    }

    .admin-content {

        padding: 28px 25px 50px;

    }

}


@media (max-width: 650px) {

    .admin-main {

        margin-left: 0;

    }

    .page-header {

        flex-direction: column;

    }

    .back-button {

        width: 100%;

    }

    .form-card form {

        padding: 22px 18px;

    }

    .form-card-header {

        padding: 20px 18px;

    }

    .form-actions {

        flex-direction: column-reverse;

    }

    .btn-cancel,
    .btn-submit {

        width: 100%;

    }

}

</style>