<?php

$errors = $errors ?? [];

$name = $name ?? '';

$email = $email ?? '';

$baseUrl = Auth::baseUrl();

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Daftar - INFORA SN</title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {

            min-height: 100vh;

            background: #f4f5f7;

            color: #111111;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

        }


        /* =====================================================
           PAGE
        ====================================================== */

        .register-page {

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 30px;

        }


        /* =====================================================
           WRAPPER
        ====================================================== */

        .register-wrapper {

            width: 100%;

            max-width: 1040px;

            min-height: 650px;

            display: grid;

            grid-template-columns:
                1fr
                1fr;

            background: #ffffff;

            border: 1px solid #dedede;

            box-shadow:
                0 20px 60px
                rgba(0, 0, 0, .08);

        }


        /* =====================================================
           LEFT BRAND
        ====================================================== */

        .register-brand {

            position: relative;

            display: flex;

            flex-direction: column;

            justify-content: space-between;

            padding: 55px;

            overflow: hidden;

            background: #101010;

            color: #ffffff;

        }


        .brand-content {

            position: relative;

            z-index: 2;

        }


        /* LOGO */

        .brand-logo {

            display: inline-flex;

            align-items: baseline;

            margin-bottom: 45px;

            font-size: 39px;

            font-weight: 900;

            line-height: 1;

            letter-spacing: -2.5px;

        }


        .brand-logo span {

            color: #ffffff;

        }


        .brand-logo i {

            color: #caff00;

            font-style: normal;

        }


        /* KICKER */

        .brand-kicker {

            margin-bottom: 13px;

            color: #caff00;

            font-size: 10px;

            font-weight: 800;

            letter-spacing: 2px;

        }


        /* TITLE */

        .brand-title {

            max-width: 400px;

            margin-bottom: 18px;

            font-family:
                Georgia,
                "Times New Roman",
                serif;

            font-size: 43px;

            font-weight: 700;

            line-height: 1.08;

            letter-spacing: -1.5px;

        }


        .brand-description {

            max-width: 390px;

            color: #b8b8b8;

            font-size: 13px;

            line-height: 1.8;

        }


        /* DECORATION */

        .brand-decoration {

            position: absolute;

            right: -80px;

            bottom: -110px;

            width: 330px;

            height: 330px;

            border: 55px solid #caff00;

            border-radius: 50%;

            opacity: .95;

        }


        .brand-decoration::after {

            content: "";

            position: absolute;

            top: 45px;

            left: 45px;

            width: 135px;

            height: 135px;

            background: #101010;

            border-radius: 50%;

        }


        /* FOOTER BRAND */

        .brand-footer {

            position: relative;

            z-index: 2;

            display: flex;

            align-items: center;

            gap: 10px;

            color: #777777;

            font-size: 9px;

            font-weight: 700;

            letter-spacing: 1.2px;

        }


        .brand-footer-line {

            width: 28px;

            height: 2px;

            background: #caff00;

        }



        /* =====================================================
           RIGHT REGISTER
        ====================================================== */

        .register-panel {

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 50px 55px;

            background: #ffffff;

        }


        .register-form-wrapper {

            width: 100%;

            max-width: 390px;

        }


        /* HEADER */

        .register-kicker {

            margin-bottom: 9px;

            color: #888888;

            font-size: 9px;

            font-weight: 800;

            letter-spacing: 1.8px;

            text-transform: uppercase;

        }


        .register-title {

            margin-bottom: 8px;

            color: #111111;

            font-family:
                Georgia,
                "Times New Roman",
                serif;

            font-size: 31px;

            font-weight: 700;

            letter-spacing: -1px;

        }


        .register-subtitle {

            margin-bottom: 26px;

            color: #777777;

            font-size: 12px;

            line-height: 1.6;

        }



        /* =====================================================
           ERROR
        ====================================================== */

        .alert-error {

            margin-bottom: 20px;

            padding: 13px 15px;

            background: #fff4f4;

            border: 1px solid #e6b5b5;

            color: #a11d1d;

            font-size: 11px;

            line-height: 1.6;

        }


        .alert-error-title {

            margin-bottom: 6px;

            font-weight: 800;

        }


        .alert-error ul {

            margin: 0 0 0 17px;

            padding: 0;

        }


        .alert-error li {

            margin-bottom: 3px;

        }


        .alert-error li:last-child {

            margin-bottom: 0;

        }



        /* =====================================================
           FORM
        ====================================================== */

        .form-group {

            margin-bottom: 16px;

        }


        .form-label {

            display: block;

            margin-bottom: 7px;

            color: #333333;

            font-size: 10px;

            font-weight: 800;

            letter-spacing: 1px;

            text-transform: uppercase;

        }


        .input-wrapper {

            position: relative;

        }


        .form-input {

            width: 100%;

            height: 46px;

            padding: 0 14px;

            background: #ffffff;

            border: 1px solid #d5d5d5;

            color: #111111;

            outline: none;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            font-size: 12px;

            transition:
                border-color .2s ease,
                box-shadow .2s ease;

        }


        .form-input::placeholder {

            color: #aaaaaa;

        }


        .form-input:focus {

            border-color: #b7df00;

            box-shadow:
                0 0 0 3px
                rgba(202, 255, 0, .16);

        }


        .password-input {

            padding-right: 75px;

        }


        /* PASSWORD TOGGLE */

        .toggle-password {

            position: absolute;

            top: 50%;

            right: 12px;

            transform: translateY(-50%);

            border: none;

            background: transparent;

            color: #777777;

            font-size: 9px;

            font-weight: 800;

            letter-spacing: .5px;

            cursor: pointer;

        }


        .toggle-password:hover {

            color: #111111;

        }



        /* =====================================================
           SUBMIT
        ====================================================== */

        .register-button {

            width: 100%;

            height: 49px;

            margin-top: 7px;

            border: 1px solid #b5df00;

            background: #caff00;

            color: #101010;

            font-size: 11px;

            font-weight: 900;

            letter-spacing: .8px;

            cursor: pointer;

            transition:
                background .2s ease,
                transform .2s ease;

        }


        .register-button:hover {

            background: #b9eb00;

            transform: translateY(-1px);

        }


        .register-button:active {

            transform: translateY(0);

        }



        /* =====================================================
           LOGIN LINK
        ====================================================== */

        .login-box {

            margin-top: 22px;

            padding-top: 20px;

            border-top: 1px solid #eeeeee;

            text-align: center;

        }


        .login-box p {

            color: #888888;

            font-size: 11px;

        }


        .login-link {

            color: #111111;

            font-weight: 800;

            text-decoration: none;

        }


        .login-link:hover {

            color: #7c9e00;

            text-decoration: underline;

        }



        /* =====================================================
           PORTAL LINK
        ====================================================== */

        .portal-link {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            margin-top: 18px;

            color: #777777;

            font-size: 10px;

            font-weight: 700;

            text-decoration: none;

        }


        .portal-link:hover {

            color: #111111;

        }



        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 850px) {

            .register-wrapper {

                grid-template-columns: 1fr;

                max-width: 500px;

            }


            .register-brand {

                min-height: 300px;

                padding: 40px;

            }


            .brand-logo {

                margin-bottom: 30px;

                font-size: 32px;

            }


            .brand-title {

                font-size: 32px;

            }


            .brand-description {

                font-size: 12px;

            }


            .brand-footer {

                margin-top: 35px;

            }


            .brand-decoration {

                width: 240px;

                height: 240px;

                right: -80px;

                bottom: -100px;

                border-width: 40px;

            }


            .brand-decoration::after {

                top: 33px;

                left: 33px;

                width: 98px;

                height: 98px;

            }


            .register-panel {

                padding: 45px 35px;

            }

        }


        @media (max-width: 520px) {

            .register-page {

                padding: 15px;

            }


            .register-brand {

                min-height: 270px;

                padding: 30px;

            }


            .brand-title {

                font-size: 28px;

            }


            .brand-description {

                max-width: 300px;

                font-size: 11px;

            }


            .register-panel {

                padding: 35px 25px;

            }


            .register-title {

                font-size: 27px;

            }

        }

    </style>

</head>


<body>


<div class="register-page">


    <div class="register-wrapper">


        <!-- =====================================================
             BRAND
        ====================================================== -->

        <section class="register-brand">


            <div class="brand-content">


                <div class="brand-logo">

                    <span>INFORA</span><i>.</i>

                </div>


                <div class="brand-kicker">

                    MEDIA &amp; NEWS PORTAL

                </div>


                <h1 class="brand-title">

                    Jadilah bagian dari cerita yang berarti.

                </h1>


                <p class="brand-description">

                    Bergabung dengan INFORA SN dan
                    bagikan informasi, gagasan, serta
                    tulisan yang dapat memberikan
                    perspektif bagi pembaca.

                </p>


            </div>


            <div class="brand-footer">

                <span class="brand-footer-line"></span>

                <span>
                    INFORA SN &copy; <?= date('Y') ?>
                </span>

            </div>


            <div class="brand-decoration"></div>


        </section>



        <!-- =====================================================
             REGISTER
        ====================================================== -->

        <section class="register-panel">


            <div class="register-form-wrapper">


                <div class="register-kicker">

                    BUAT AKUN

                </div>


                <h2 class="register-title">

                    Daftar di INFORA SN

                </h2>


                <p class="register-subtitle">

                    Buat akun penulis untuk mulai
                    membuat dan mengirimkan artikel.

                </p>



                <!-- =================================================
                     ERROR
                ================================================== -->

                <?php if (!empty($errors)): ?>

                    <div class="alert-error">

                        <div class="alert-error-title">

                            Pendaftaran belum dapat diproses.

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



                <!-- =================================================
                     FORM
                ================================================== -->

                <form
                    action="<?= $baseUrl ?>/register"
                    method="POST"
                >


                    <!-- NAMA -->

                    <div class="form-group">

                        <label
                            for="name"
                            class="form-label"
                        >
                            Nama Lengkap
                        </label>


                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-input"
                            value="<?= htmlspecialchars($name) ?>"
                            placeholder="Masukkan nama lengkap"
                            autocomplete="name"
                            required
                        >

                    </div>



                    <!-- EMAIL -->

                    <div class="form-group">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Email
                        </label>


                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            value="<?= htmlspecialchars($email) ?>"
                            placeholder="Masukkan alamat email"
                            autocomplete="email"
                            required
                        >

                    </div>



                    <!-- PASSWORD -->

                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Password
                        </label>


                        <div class="input-wrapper">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input password-input"
                                placeholder="Minimal 6 karakter"
                                autocomplete="new-password"
                                required
                            >


                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password"
                            >
                                TAMPILKAN
                            </button>

                        </div>

                    </div>



                    <!-- KONFIRMASI PASSWORD -->

                    <div class="form-group">

                        <label
                            for="password_confirmation"
                            class="form-label"
                        >
                            Konfirmasi Password
                        </label>


                        <div class="input-wrapper">

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-input password-input"
                                placeholder="Ulangi password"
                                autocomplete="new-password"
                                required
                            >


                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password_confirmation"
                            >
                                TAMPILKAN
                            </button>

                        </div>

                    </div>



                    <!-- SUBMIT -->

                    <button
                        type="submit"
                        class="register-button"
                    >

                        BUAT AKUN

                    </button>


                </form>



                <!-- LOGIN -->

                <div class="login-box">

                    <p>

                        Sudah memiliki akun?

                        <a
                            href="<?= $baseUrl ?>/login"
                            class="login-link"
                        >
                            Masuk sekarang
                        </a>

                    </p>


                    <a
                        href="<?= $baseUrl ?>/"
                        class="portal-link"
                    >

                        ← Kembali ke Portal

                    </a>

                </div>


            </div>


        </section>


    </div>


</div>



<script>

    const toggleButtons =
        document.querySelectorAll(
            '.toggle-password'
        );


    toggleButtons.forEach(
        function (button) {

            button.addEventListener(
                'click',
                function () {

                    const targetId =
                        button.getAttribute(
                            'data-target'
                        );

                    const input =
                        document.getElementById(
                            targetId
                        );


                    if (
                        input.type === 'password'
                    ) {

                        input.type = 'text';

                        button.textContent =
                            'SEMBUNYIKAN';

                    } else {

                        input.type = 'password';

                        button.textContent =
                            'TAMPILKAN';

                    }

                }
            );

        }
    );

</script>


</body>

</html>