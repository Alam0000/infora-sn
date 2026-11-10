<?php

$error = $error ?? '';

$email = $email ?? '';

$registered =
    isset($_GET['registered']) &&
    $_GET['registered'] === '1';

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

    <title>Masuk - INFORA SN</title>

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

        .login-page {

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 30px;

        }


        /* =====================================================
           LOGIN WRAPPER
        ====================================================== */

        .login-wrapper {

            width: 100%;

            max-width: 1040px;

            min-height: 610px;

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
           LEFT BRAND PANEL
        ====================================================== */

        .login-brand {

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


        /* BRAND FOOTER */

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
           RIGHT LOGIN PANEL
        ====================================================== */

        .login-panel {

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 55px;

            background: #ffffff;

        }


        .login-form-wrapper {

            width: 100%;

            max-width: 390px;

        }


        /* HEADER */

        .login-kicker {

            margin-bottom: 9px;

            color: #888888;

            font-size: 9px;

            font-weight: 800;

            letter-spacing: 1.8px;

            text-transform: uppercase;

        }


        .login-title {

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


        .login-subtitle {

            margin-bottom: 30px;

            color: #777777;

            font-size: 12px;

            line-height: 1.6;

        }



        /* =====================================================
           ALERT
        ====================================================== */

        .alert {

            margin-bottom: 20px;

            padding: 13px 15px;

            border: 1px solid;

            font-size: 11px;

            line-height: 1.6;

        }


        .alert-success {

            background: #f7ffe3;

            border-color: #d5ef80;

            color: #4f6500;

        }


        .alert-error {

            background: #fff4f4;

            border-color: #e6b5b5;

            color: #a11d1d;

        }



        /* =====================================================
           FORM
        ====================================================== */

        .form-group {

            margin-bottom: 19px;

        }


        .form-label {

            display: block;

            margin-bottom: 8px;

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

            height: 48px;

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


        /* PASSWORD TOGGLE */

        .password-input {

            padding-right: 70px;

        }


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

        .login-button {

            width: 100%;

            height: 49px;

            margin-top: 5px;

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


        .login-button:hover {

            background: #b9eb00;

            transform: translateY(-1px);

        }


        .login-button:active {

            transform: translateY(0);

        }



        /* =====================================================
           REGISTER
        ====================================================== */

        .register-box {

            margin-top: 24px;

            padding-top: 22px;

            border-top: 1px solid #eeeeee;

            text-align: center;

        }


        .register-box p {

            color: #888888;

            font-size: 11px;

        }


        .register-link {

            color: #111111;

            font-weight: 800;

            text-decoration: none;

        }


        .register-link:hover {

            color: #7c9e00;

            text-decoration: underline;

        }



        /* =====================================================
           BACK TO PORTAL
        ====================================================== */

        .portal-link {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            margin-top: 22px;

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

            .login-wrapper {

                grid-template-columns: 1fr;

                max-width: 500px;

            }


            .login-brand {

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


            .login-panel {

                padding: 45px 35px;

            }

        }


        @media (max-width: 520px) {

            .login-page {

                padding: 15px;

            }


            .login-brand {

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


            .login-panel {

                padding: 35px 25px;

            }


            .login-title {

                font-size: 27px;

            }

        }

    </style>

</head>


<body>


<div class="login-page">


    <div class="login-wrapper">


        <!-- =====================================================
             BRAND
        ====================================================== -->

        <section class="login-brand">


            <div class="brand-content">


                <div class="brand-logo">

                    <span>INFORA</span><i>.</i>

                </div>


                <div class="brand-kicker">

                    MEDIA &amp; NEWS PORTAL

                </div>


                <h1 class="brand-title">

                    Ruang informasi untuk suara yang berarti.

                </h1>


                <p class="brand-description">

                    Selamat datang di INFORA SN.
                    Masuk untuk mengelola tulisan,
                    mengirim berita, dan menjadi bagian
                    dari ruang informasi kami.

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
             LOGIN
        ====================================================== -->

        <section class="login-panel">


            <div class="login-form-wrapper">


                <div class="login-kicker">

                    AKSES AKUN

                </div>


                <h2 class="login-title">

                    Masuk ke INFORA SN

                </h2>


                <p class="login-subtitle">

                    Gunakan email dan password Anda
                    untuk melanjutkan.

                </p>



                <!-- =================================================
                     SUCCESS
                ================================================== -->

                <?php if ($registered): ?>

                    <div class="alert alert-success">

                        <strong>Registrasi berhasil.</strong>

                        Silakan masuk menggunakan akun
                        yang baru saja Anda daftarkan.

                    </div>

                <?php endif; ?>



                <!-- =================================================
                     ERROR
                ================================================== -->

                <?php if ($error): ?>

                    <div class="alert alert-error">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>



                <!-- =================================================
                     FORM
                ================================================== -->

                <form
                    action="<?= $baseUrl ?>/login"
                    method="POST"
                >


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
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                            >


                            <button
                                type="button"
                                class="toggle-password"
                                id="togglePassword"
                            >
                                TAMPILKAN
                            </button>


                        </div>

                    </div>



                    <!-- BUTTON -->

                    <button
                        type="submit"
                        class="login-button"
                    >

                        MASUK KE AKUN

                    </button>


                </form>



                <!-- REGISTER -->

                <div class="register-box">

                    <p>

                        Belum memiliki akun?

                        <a
                            href="<?= $baseUrl ?>/register"
                            class="register-link"
                        >
                            Daftar sekarang
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

    const togglePassword =
        document.getElementById('togglePassword');

    const passwordInput =
        document.getElementById('password');


    togglePassword.addEventListener(
        'click',
        function () {

            if (
                passwordInput.type === 'password'
            ) {

                passwordInput.type = 'text';

                togglePassword.textContent =
                    'SEMBUNYIKAN';

            } else {

                passwordInput.type = 'password';

                togglePassword.textContent =
                    'TAMPILKAN';

            }

        }
    );

</script>


</body>

</html>