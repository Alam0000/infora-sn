<?php

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Halaman login.
     */
    public function login()
    {
        if (Auth::check()) {
            $this->redirectByRole();
        }

        $this->view('auth/login');
    }

    /**
     * Proses login.
     */
    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Auth::redirect('/login');
        }

        $email = trim(
            $_POST['email'] ?? ''
        );

        $password = $_POST['password'] ?? '';

        // Validasi input
        if ($email === '' || $password === '') {

            $this->view(
                'auth/login',
                [
                    'error' => 'Email dan password wajib diisi.',
                    'email' => $email
                ]
            );

            return;
        }

        // Cari user
        $user = $this->userModel
            ->findByEmail($email);

        // User tidak ditemukan
        if (!$user) {

            $this->view(
                'auth/login',
                [
                    'error' =>
                        'Email atau password salah.',
                    'email' => $email
                ]
            );

            return;
        }

        // Cek status akun
        if ($user['account_status'] !== 'active') {

            $this->view(
                'auth/login',
                [
                    'error' =>
                        'Akun Anda sedang tidak aktif.',
                    'email' => $email
                ]
            );

            return;
        }

        // Verifikasi password
        if (
            !password_verify(
                $password,
                $user['password']
            )
        ) {

            $this->view(
                'auth/login',
                [
                    'error' =>
                        'Email atau password salah.',
                    'email' => $email
                ]
            );

            return;
        }

        // Login
        Auth::login($user);

        // Redirect berdasarkan role
        $this->redirectByRole();
    }

    /**
     * Halaman register.
     */
    public function register()
    {
        if (Auth::check()) {
            $this->redirectByRole();
        }

        $this->view('auth/register');
    }

    /**
     * Proses register.
     */
    public function processRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Auth::redirect('/register');
        }

        $name = trim(
            $_POST['name'] ?? ''
        );

        $email = trim(
            $_POST['email'] ?? ''
        );

        $password = $_POST['password'] ?? '';

        $passwordConfirmation =
            $_POST['password_confirmation'] ?? '';

        // ========================================
        // VALIDASI
        // ========================================

        $errors = [];

        if ($name === '') {
            $errors[] = 'Nama wajib diisi.';
        }

        if (
            $email === '' ||
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors[] = 'Format email tidak valid.';
        }

        if (strlen($password) < 6) {
            $errors[] =
                'Password minimal 6 karakter.';
        }

        if (
            $password !==
            $passwordConfirmation
        ) {
            $errors[] =
                'Konfirmasi password tidak sesuai.';
        }

        // Cek email
        if (
            empty($errors) &&
            $this->userModel->emailExists($email)
        ) {
            $errors[] =
                'Email sudah terdaftar.';
        }

        // ========================================
        // JIKA ADA ERROR
        // ========================================

        if (!empty($errors)) {

            $this->view(
                'auth/register',
                [
                    'errors' => $errors,
                    'name' => $name,
                    'email' => $email
                ]
            );

            return;
        }

        // ========================================
        // SIMPAN USER
        // ========================================

        $userId = $this->userModel->create(
            $name,
            $email,
            $password
        );

        if (!$userId) {

            $this->view(
                'auth/register',
                [
                    'errors' => [
                        'Registrasi gagal. Silakan coba lagi.'
                    ],
                    'name' => $name,
                    'email' => $email
                ]
            );

            return;
        }

        // Setelah berhasil register
        Auth::redirect('/login?registered=1');
    }

    /**
     * Logout.
     */
    public function logout()
    {
        Auth::logout();

        Auth::redirect('/login');
    }

    /**
     * Redirect berdasarkan role.
     */
    private function redirectByRole()
    {
        if (Auth::hasRole('admin')) {
            Auth::redirect('/admin/dashboard');
        }

        if (Auth::hasRole('client')) {
            Auth::redirect('/client/dashboard');
        }

        Auth::logout();

        Auth::redirect('/login');
    }
}