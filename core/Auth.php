<?php

class Auth
{
    /**
     * Memulai session.
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Login user.
     */
    public static function login($user)
    {
        self::start();

        // Mencegah session fixation
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'             => $user['id'],
            'name'           => $user['name'],
            'email'          => $user['email'],
            'role'           => $user['role'],
            'account_status' => $user['account_status']
        ];
    }

    /**
     * Mengecek apakah user sudah login.
     */
    public static function check()
    {
        self::start();

        return isset($_SESSION['user']);
    }

    /**
     * Mendapatkan user yang sedang login.
     */
    public static function user()
    {
        self::start();

        return $_SESSION['user'] ?? null;
    }

    /**
     * Mendapatkan ID user.
     */
    public static function id()
    {
        self::start();

        return $_SESSION['user']['id'] ?? null;
    }

    /**
     * Mendapatkan role user.
     */
    public static function role()
    {
        self::start();

        return $_SESSION['user']['role'] ?? null;
    }

    /**
     * Mengecek role user.
     */
    public static function hasRole($role)
    {
        self::start();

        return isset($_SESSION['user']['role'])
            && $_SESSION['user']['role'] === $role;
    }

    /**
     * Mendapatkan base URL aplikasi.
     *
     * Contoh:
     * http://localhost/infora-sn
     */
    public static function baseUrl()
    {
        $scriptName = str_replace(
            '\\',
            '/',
            $_SERVER['SCRIPT_NAME'] ?? ''
        );

        $basePath = dirname($scriptName);

        if ($basePath === '/' || $basePath === '\\') {
            return '';
        }

        return rtrim($basePath, '/');
    }

    /**
     * Redirect ke URL aplikasi.
     */
    public static function redirect($path)
    {
        $baseUrl = self::baseUrl();

        header(
            'Location: ' .
            $baseUrl .
            '/' .
            ltrim($path, '/')
        );

        exit;
    }

    /**
     * Wajib login.
     */
    public static function requireLogin()
    {
        self::start();

        if (!self::check()) {
            self::redirect('/login');
        }
    }

    /**
     * Wajib memiliki role tertentu.
     */
    public static function requireRole($role)
    {
        self::start();

        if (!self::check()) {
            self::redirect('/login');
        }

        if (!self::hasRole($role)) {
            http_response_code(403);

            die(
                '403 - Anda tidak memiliki akses ke halaman ini.'
            );
        }
    }

    /**
     * Logout.
     */
    public static function logout()
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}