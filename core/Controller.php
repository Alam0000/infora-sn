<?php

class Controller
{
    /**
     * Memuat view.
     *
     * Contoh:
     *
     * $this->view('auth/login');
     *
     * akan mencari:
     *
     * view/auth/login.php
     */
    protected function view($view, $data = [])
    {
        $viewPath =
            __DIR__ .
            '/../view/' .
            $view .
            '.php';

        if (!file_exists($viewPath)) {

            die(
                'View tidak ditemukan: ' .
                htmlspecialchars($view)
            );
        }

        // Mengubah array menjadi variabel
        extract($data);

        require $viewPath;
    }

    /**
     * Redirect
     */
    protected function redirect($url)
    {
        header(
            'Location: ' . $url
        );

        exit;
    }

    /**
     * Response JSON
     */
    protected function json(
        $data,
        $statusCode = 200
    ) {
        http_response_code(
            $statusCode
        );

        header(
            'Content-Type: application/json; charset=utf-8'
        );

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );

        exit;
    }
}