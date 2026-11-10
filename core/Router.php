<?php

class Router
{
    private $routes = [];

    /**
     * Route GET
     */
    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Route POST
     */
    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Menyimpan route
     */
    private function addRoute($method, $path, $handler)
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler
        ];
    }

    /**
     * Menjalankan route
     */
    public function dispatch($url, $method)
    {
        // Ambil path saja, tanpa query string
        $url = parse_url($url, PHP_URL_PATH);

        // ========================================
        // BASE PATH PROJECT
        // ========================================

        /*
         * Jika project:
         *
         * C:/xampp/htdocs/infora-sn
         *
         * dan URL:
         *
         * http://localhost/infora-sn/
         *
         * maka base path:
         *
         * /infora-sn
         */

        $scriptName = str_replace(
            '\\',
            '/',
            $_SERVER['SCRIPT_NAME']
        );

        $basePath = dirname($scriptName);

        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }

        $basePath = rtrim($basePath, '/');

        // ========================================
        // HAPUS BASE PATH DARI URL
        // ========================================

        if (
            $basePath !== '' &&
            strpos($url, $basePath) === 0
        ) {
            $url = substr(
                $url,
                strlen($basePath)
            );
        }

        // ========================================
        // NORMALISASI URL
        // ========================================

        if (
            $url === '' ||
            $url === false
        ) {
            $url = '/';
        }

        if ($url[0] !== '/') {
            $url = '/' . $url;
        }

        if ($url !== '/') {
            $url = rtrim($url, '/');
        }

        // ========================================
        // CARI ROUTE
        // ========================================

        foreach ($this->routes as $route) {

            if (
                strtoupper($route['method']) !==
                strtoupper($method)
            ) {
                continue;
            }

            $pattern = $this->convertPathToRegex(
                $route['path']
            );

            if (
                preg_match(
                    $pattern,
                    $url,
                    $matches
                )
            ) {

                array_shift($matches);

                return $this->callHandler(
                    $route['handler'],
                    $matches
                );
            }
        }

        // ========================================
        // 404
        // ========================================

        http_response_code(404);

        echo '404 - Halaman tidak ditemukan.';
    }

    /**
     * Mengubah route menjadi regex
     *
     * /article/{slug}
     * menjadi
     * /article/([^/]+)
     */
    private function convertPathToRegex($path)
    {
        $path = preg_replace(
            '/\{([a-zA-Z0-9_]+)\}/',
            '([^/]+)',
            $path
        );

        return '#^' . $path . '$#';
    }

    /**
     * Menjalankan handler
     */
    private function callHandler(
        $handler,
        $params = []
    ) {

        // ========================================
        // CLOSURE / FUNCTION
        // ========================================

        if (is_callable($handler)) {

            return call_user_func_array(
                $handler,
                $params
            );
        }

        // ========================================
        // CONTROLLER + METHOD
        // ========================================

        if (
            !is_array($handler) ||
            count($handler) !== 2
        ) {

            die(
                'Format handler route tidak valid.'
            );
        }

        [$controller, $method] = $handler;

        // ========================================
        // LOKASI CONTROLLER
        // ========================================

        $controllerFile =
            __DIR__ .
            '/../module/controller/' .
            $controller .
            '.php';

        if (!file_exists($controllerFile)) {

            die(
                'Controller tidak ditemukan: ' .
                htmlspecialchars($controller)
            );
        }

        require_once $controllerFile;

        // ========================================
        // CEK CLASS
        // ========================================

        if (!class_exists($controller)) {

            die(
                'Class controller tidak ditemukan: ' .
                htmlspecialchars($controller)
            );
        }

        // ========================================
        // OBJECT CONTROLLER
        // ========================================

        $controllerObject = new $controller();

        // ========================================
        // CEK METHOD
        // ========================================

        if (
            !method_exists(
                $controllerObject,
                $method
            )
        ) {

            die(
                'Method tidak ditemukan: ' .
                htmlspecialchars($method)
            );
        }

        // ========================================
        // JALANKAN METHOD
        // ========================================

        return call_user_func_array(
            [
                $controllerObject,
                $method
            ],
            $params
        );
    }
}