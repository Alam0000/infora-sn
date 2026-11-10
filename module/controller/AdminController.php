<?php

// Pastikan model iklan tersedia sebelum AdminController membuat instance AdminAd.
require_once __DIR__ . '/../model/AdminAd.php';

// Model khusus untuk pembuatan artikel oleh admin.
require_once __DIR__ . '/../model/AdminArticleCreate.php';

// Model tag (dipakai untuk daftar checkbox tag & tag custom di form admin).
require_once __DIR__ . '/../model/Tag.php';

class AdminController extends Controller
{
    private $articleModel;
    private $articleCreateModel;
    private $clientModel;
    private $categoryModel;
    private $adModel;
    private $tagModel;


    public function __construct()
    {
        $this->articleModel =
            new AdminArticle();

        $this->articleCreateModel =
            new AdminArticleCreate();

        $this->clientModel =
            new AdminClient();

        $this->categoryModel =
            new AdminCategory();

        $this->adModel =
            new AdminAd();

        $this->tagModel =
            new Tag();
    }


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        Auth::requireRole('admin');

        $stats =
            $this->articleModel
            ->getDashboardStats();

        $articles =
            $this->articleModel
            ->getRecentArticles();

        $user = Auth::user();

        $this->view(
            'admin/dashboard',
            [
                'user' => $user,
                'stats' => $stats,
                'articles' => $articles
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ARTIKEL
    |--------------------------------------------------------------------------
    */

    public function articles()
    {
        Auth::requireRole('admin');

        $keyword =
            trim($_GET['q'] ?? '');

        $status =
            trim($_GET['status'] ?? '');

        $articles =
            $this->articleModel
            ->getAllArticles(
                $keyword,
                $status
            );

        $stats =
            $this->articleModel
            ->getDashboardStats();

        $this->view(
            'admin/articles',
            [
                'user' => Auth::user(),
                'articles' => $articles,
                'stats' => $stats,
                'keyword' => $keyword,
                'status' => $status
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH ARTIKEL OLEH ADMIN
    |--------------------------------------------------------------------------
    |
    | Admin dapat memasukkan artikel yang dikirim oleh orang lain
    | tanpa harus membuat akun client.
    |
    | author_id        = admin yang memasukkan artikel
    | author_override  = nama penulis asli yang tampil di publik
    |
    */

    public function articleCreate()
    {
        Auth::requireRole('admin');

        $categories =
            $this->articleCreateModel
            ->getCategories();

        $tags =
            $this->articleCreateModel
            ->getTags();

        $this->view(
            'admin/article_form',
            [
                'user' => Auth::user(),
                'article' => null,
                'categories' => $categories,
                'tags' => $tags,
                'errors' => []
            ]
        );
    }


    public function articleStore()
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {
            Auth::redirect('/admin/articles');
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA FORM
        |--------------------------------------------------------------------------
        */

        $authorOverride =
            trim(
                $_POST['author_override'] ?? ''
            );

        $title =
            trim(
                $_POST['title'] ?? ''
            );

        $excerpt =
            trim(
                $_POST['excerpt'] ?? ''
            );

        $content =
            trim(
                $_POST['content'] ?? ''
            );

        $categoryId =
            (int) (
                $_POST['category_id'] ?? 0
            );

        $status =
            $_POST['status'] ?? 'DRAFT';

        $errors = [];


        /*
        |--------------------------------------------------------------------------
        | AMBIL & SIAPKAN TAG
        |--------------------------------------------------------------------------
        */

        $tagIds =
            $_POST['tags'] ?? [];

        if (!is_array($tagIds)) {
            $tagIds = [];
        }

        $tagIds =
            array_map('intval', $tagIds);


        $customTags =
            trim(
                $_POST['custom_tags'] ?? ''
            );

        if ($customTags !== '') {

            $customNames =
                array_filter(
                    array_map(
                        'trim',
                        explode(',', $customTags)
                    )
                );

            foreach ($customNames as $customName) {

                if ($customName === '') {
                    continue;
                }

                $existingTag =
                    $this->tagModel
                    ->findByName($customName);

                if ($existingTag) {

                    $tagIds[] =
                        (int) $existingTag['id'];

                } else {

                    $tagIds[] =
                        $this->tagModel
                        ->createAndGetId($customName);
                }
            }
        }

        $tagIds =
            array_values(
                array_unique($tagIds)
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        if ($authorOverride === '') {

            $errors[] =
                'Nama penulis wajib diisi.';
        }


        if ($title === '') {

            $errors[] =
                'Judul artikel wajib diisi.';
        }


        if ($content === '') {

            $errors[] =
                'Isi artikel wajib diisi.';
        }


        if ($categoryId <= 0) {

            $errors[] =
                'Kategori artikel wajib dipilih.';
        }


        $allowedStatuses = [
            'DRAFT',
            'MENUNGGU_REVIEW',
            'PUBLISHED'
        ];


        if (
            !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {

            $status = 'DRAFT';
        }


        /*
        |--------------------------------------------------------------------------
        | BUAT SLUG
        |--------------------------------------------------------------------------
        */

        $slug =
            $this->articleCreateModel
            ->makeSlug($title);


        if ($slug === '') {

            $errors[] =
                'Slug artikel tidak dapat dibuat.';
        }


        /*
        |--------------------------------------------------------------------------
        | CEK SLUG
        |--------------------------------------------------------------------------
        */

        if (
            $slug !== ''
            &&
            $this->articleCreateModel
                ->slugExists($slug)
        ) {

            $slug .= '-' . time();
        }


        /*
        |--------------------------------------------------------------------------
        | UPLOAD THUMBNAIL
        |--------------------------------------------------------------------------
        */

        $thumbnailPath = '';

        $file =
            $_FILES['thumbnail'] ?? null;


        if (
            $file
            &&
            (
                $file['error']
                ??
                UPLOAD_ERR_NO_FILE
            )
            !== UPLOAD_ERR_NO_FILE
        ) {

            $uploadResult =
                $this->uploadArticleThumbnail(
                    $file
                );


            if (
                !$uploadResult['success']
            ) {

                $errors[] =
                    $uploadResult['message'];

            } else {

                $thumbnailPath =
                    $uploadResult['path'];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | JIKA ADA ERROR
        |--------------------------------------------------------------------------
        */

        if (!empty($errors)) {

            $this->view(
                'admin/article_form',
                [
                    'user' => Auth::user(),

                    'article' => [

                        'author_override' =>
                            $authorOverride,

                        'title' =>
                            $title,

                        'excerpt' =>
                            $excerpt,

                        'content' =>
                            $content,

                        'category_id' =>
                            $categoryId,

                        'status' =>
                            $status,

                        'thumbnail_path' =>
                            $thumbnailPath
                    ],

                    'categories' =>
                        $this->articleCreateModel
                        ->getCategories(),

                    'tags' =>
                        $this->articleCreateModel
                        ->getTags(),

                    'errors' =>
                        $errors
                ]
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN ARTIKEL
        |--------------------------------------------------------------------------
        */

        $articleId =
            $this->articleCreateModel
            ->createArticle(
                Auth::id(),
                $authorOverride,
                $title,
                $slug,
                $excerpt,
                $content,
                $thumbnailPath,
                $categoryId,
                $status
            );


        if ($articleId) {

            $this->articleCreateModel
                ->syncTags(
                    $articleId,
                    $tagIds
                );

            Auth::redirect(
                '/admin/articles?success=' .
                urlencode(
                    'Artikel berhasil ditambahkan oleh admin.'
                )
            );
        }


        Auth::redirect(
            '/admin/articles?error=' .
            urlencode(
                'Artikel gagal ditambahkan.'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD THUMBNAIL ARTIKEL ADMIN
    |--------------------------------------------------------------------------
    */

    private function uploadArticleThumbnail($file)
    {
        if (
            (
                $file['error']
                ??
                UPLOAD_ERR_OK
            )
            !== UPLOAD_ERR_OK
        ) {

            return [
                'success' => false,
                'message' =>
                    'Upload thumbnail gagal.'
            ];
        }


        $maxSize =
            5 * 1024 * 1024;


        if (
            ($file['size'] ?? 0)
            > $maxSize
        ) {

            return [
                'success' => false,
                'message' =>
                    'Ukuran thumbnail maksimal 5 MB.'
            ];
        }


        $allowed = [

            'image/jpeg' =>
                'jpg',

            'image/png' =>
                'png',

            'image/webp' =>
                'webp',

            'image/gif' =>
                'gif'
        ];


        $mime = '';


        if (
            function_exists('finfo_open')
        ) {

            $finfo =
                finfo_open(
                    FILEINFO_MIME_TYPE
                );

            $mime =
                finfo_file(
                    $finfo,
                    $file['tmp_name']
                );

            finfo_close($finfo);

        } else {

            $mime =
                mime_content_type(
                    $file['tmp_name']
                );
        }


        if (
            !isset(
                $allowed[$mime]
            )
        ) {

            return [
                'success' => false,
                'message' =>
                    'Format thumbnail harus JPG, PNG, WEBP, atau GIF.'
            ];
        }


        $extension =
            $allowed[$mime];


        $filename =
            'article_' .
            date('Ymd_His') .
            '_' .
            bin2hex(
                random_bytes(5)
            ) .
            '.' .
            $extension;


        $projectRoot =
            dirname(
                __DIR__,
                2
            );


        $uploadDirectory =
            $projectRoot .
            DIRECTORY_SEPARATOR .
            'public' .
            DIRECTORY_SEPARATOR .
            'uploads' .
            DIRECTORY_SEPARATOR .
            'articles';


        if (
            !is_dir(
                $uploadDirectory
            )
        ) {

            if (
                !mkdir(
                    $uploadDirectory,
                    0755,
                    true
                )
            ) {

                return [
                    'success' => false,
                    'message' =>
                        'Folder upload artikel tidak dapat dibuat.'
                ];
            }
        }


        $destination =
            $uploadDirectory .
            DIRECTORY_SEPARATOR .
            $filename;


        if (
            !move_uploaded_file(
                $file['tmp_name'],
                $destination
            )
        ) {

            return [
                'success' => false,
                'message' =>
                    'Thumbnail gagal disimpan ke server.'
            ];
        }


        return [

            'success' => true,

            'path' =>
                'public/uploads/articles/' .
                $filename
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | REVIEW
    |--------------------------------------------------------------------------
    */

    public function review($id)
    {
        Auth::requireRole('admin');

        $article =
            $this->articleModel
            ->getArticleById($id);

        if (!$article) {

            http_response_code(404);

            die('Artikel tidak ditemukan.');
        }

        $this->view(
            'admin/review',
            [
                'user' => Auth::user(),
                'article' => $article
            ]
        );
    }


    public function processReview($id)
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {
            Auth::redirect('/admin/articles');
        }

        $action =
            $_POST['action'] ?? '';

        $note =
            trim(
                $_POST['note'] ?? ''
            );

        $map = [

            'publish' =>
                'PUBLISHED',

            'revision' =>
                'REVISI',

            'reject' =>
                'DITOLAK'
        ];


        if (
            !isset(
                $map[$action]
            )
        ) {

            Auth::redirect(
                '/admin/article/' .
                (int) $id .
                '/review?error=status_invalid'
            );
        }


        $newStatus =
            $map[$action];


        if (
            (
                $newStatus === 'REVISI'
                ||
                $newStatus === 'DITOLAK'
            )
            &&
            $note === ''
        ) {

            Auth::redirect(
                '/admin/article/' .
                (int) $id .
                '/review?error=note_required'
            );
        }


        $success =
            $this->articleModel
            ->updateStatus(
                $id,
                $newStatus,
                Auth::id(),
                $note
            );


        if ($success) {

            Auth::redirect(
                '/admin/articles?success=' .
                urlencode(
                    'Status artikel berhasil diperbarui.'
                )
            );
        }


        Auth::redirect(
            '/admin/article/' .
            (int) $id .
            '/review?error=update_failed'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public function status($id)
    {
        Auth::requireRole('admin');

        $article =
            $this->articleModel
            ->getArticleById($id);

        if (!$article) {

            http_response_code(404);

            die('Artikel tidak ditemukan.');
        }

        $this->view(
            'admin/status',
            [
                'user' => Auth::user(),
                'article' => $article
            ]
        );
    }


    public function processStatus($id)
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {
            Auth::redirect('/admin/articles');
        }

        $newStatus =
            trim(
                $_POST['status'] ?? ''
            );

        $note =
            trim(
                $_POST['note'] ?? ''
            );

        $allowedStatuses = [

            'DRAFT',

            'MENUNGGU_REVIEW',

            'REVISI',

            'DITOLAK',

            'PUBLISHED'
        ];


        if (
            !in_array(
                $newStatus,
                $allowedStatuses,
                true
            )
        ) {

            Auth::redirect(
                '/admin/article/' .
                (int) $id .
                '/status?error=status_invalid'
            );
        }


        if (
            (
                $newStatus === 'REVISI'
                ||
                $newStatus === 'DITOLAK'
            )
            &&
            $note === ''
        ) {

            Auth::redirect(
                '/admin/article/' .
                (int) $id .
                '/status?error=note_required'
            );
        }


        $success =
            $this->articleModel
            ->updateStatus(
                $id,
                $newStatus,
                Auth::id(),
                $note
            );


        if ($success) {

            Auth::redirect(
                '/admin/articles?success=' .
                urlencode(
                    'Status artikel berhasil diubah.'
                )
            );
        }


        Auth::redirect(
            '/admin/article/' .
            (int) $id .
            '/status?error=update_failed'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT ARTIKEL
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        Auth::requireRole('admin');

        $article =
            $this->articleModel
            ->getArticleById($id);

        if (!$article) {

            http_response_code(404);

            die('Artikel tidak ditemukan.');
        }


        $categories =
            $this->articleModel
            ->getCategories();


        // FIX: tag yang sudah terpasang di artikel ini,
        // diambil dari $article['tags'] yang sudah disiapkan
        // oleh AdminArticle::getArticleById().
        $selectedTagIds =
            array_map(
                fn($tag) => (int) $tag['id'],
                $article['tags'] ?? []
            );


        $this->view(
            'admin/edit',
            [
                'user' => Auth::user(),
                'article' => $article,
                'categories' => $categories,

                // FIX: daftar semua tag untuk checkbox
                'tags' =>
                    $this->tagModel
                    ->getAll(),

                // FIX: tag yang sudah tercentang saat form pertama dibuka
                'selectedTagIds' => $selectedTagIds
            ]
        );
    }


    public function processEdit($id)
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {

            Auth::redirect(
                '/admin/article/' .
                (int) $id .
                '/edit'
            );
        }


        $article =
            $this->articleModel
            ->getArticleById($id);

        if (!$article) {

            http_response_code(404);

            die('Artikel tidak ditemukan.');
        }


        $title =
            trim(
                $_POST['title'] ?? ''
            );

        $excerpt =
            trim(
                $_POST['excerpt'] ?? ''
            );

        $content =
            trim(
                $_POST['content'] ?? ''
            );

        $categoryId =
            (int) (
                $_POST['category_id'] ?? 0
            );

        $errors = [];


        /*
        |--------------------------------------------------------------------------
        | FIX: AMBIL & SIAPKAN TAG (sebelumnya tidak pernah dibaca)
        |--------------------------------------------------------------------------
        */

        $tagIds =
            $_POST['tags'] ?? [];

        if (!is_array($tagIds)) {
            $tagIds = [];
        }

        $tagIds =
            array_map('intval', $tagIds);


        $customTags =
            trim(
                $_POST['custom_tags'] ?? ''
            );

        if ($customTags !== '') {

            $customNames =
                array_filter(
                    array_map(
                        'trim',
                        explode(',', $customTags)
                    )
                );

            foreach ($customNames as $customName) {

                if ($customName === '') {
                    continue;
                }

                $existingTag =
                    $this->tagModel
                    ->findByName($customName);

                if ($existingTag) {

                    $tagIds[] =
                        (int) $existingTag['id'];

                } else {

                    $tagIds[] =
                        $this->tagModel
                        ->createAndGetId($customName);
                }
            }
        }

        $tagIds =
            array_values(
                array_unique($tagIds)
            );


        if ($title === '') {

            $errors[] =
                'Judul artikel wajib diisi.';
        }


        if ($content === '') {

            $errors[] =
                'Isi artikel wajib diisi.';
        }


        if ($categoryId <= 0) {

            $errors[] =
                'Kategori artikel wajib dipilih.';
        }


        /*
        |--------------------------------------------------------------------------
        | FIX: UPLOAD THUMBNAIL BARU (opsional, sebelumnya tidak diproses)
        |--------------------------------------------------------------------------
        |
        | null = thumbnail tidak diganti (dibiarkan seperti semula di DB)
        |
        */

        $thumbnailPath = null;

        $file =
            $_FILES['thumbnail'] ?? null;


        if (
            $file
            &&
            (
                $file['error']
                ??
                UPLOAD_ERR_NO_FILE
            )
            !== UPLOAD_ERR_NO_FILE
        ) {

            $uploadResult =
                $this->uploadArticleThumbnail(
                    $file
                );


            if (
                !$uploadResult['success']
            ) {

                $errors[] =
                    $uploadResult['message'];

            } else {

                $thumbnailPath =
                    $uploadResult['path'];
            }
        }


        if (!empty($errors)) {

            $this->view(
                'admin/edit',
                [
                    'user' => Auth::user(),

                    'article' => array_merge(
                        $article,
                        [
                            'title' =>
                                $title,

                            'excerpt' =>
                                $excerpt,

                            'content' =>
                                $content,

                            'category_id' =>
                                $categoryId,

                            'thumbnail_path' =>
                                $thumbnailPath
                                ?? $article['thumbnail_path']
                        ]
                    ),

                    'categories' =>
                        $this->articleModel
                        ->getCategories(),

                    'tags' =>
                        $this->tagModel
                        ->getAll(),

                    'selectedTagIds' =>
                        $tagIds,

                    'errors' =>
                        $errors
                ]
            );

            return;
        }


        $success =
            $this->articleModel
            ->updateArticle(
                $id,
                $title,
                $excerpt,
                $content,
                $categoryId,
                $thumbnailPath
            );


        if ($success) {

            // FIX: relasi tag disimpan ulang di sini,
            // sebelumnya syncTags() tidak pernah dipanggil saat edit.
            $this->articleCreateModel
                ->syncTags(
                    $id,
                    $tagIds
                );

            Auth::redirect(
                '/admin/articles?success=' .
                urlencode(
                    'Artikel berhasil diperbarui.'
                )
            );
        }


        Auth::redirect(
            '/admin/articles?error=update_failed'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS ARTIKEL
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {
            Auth::redirect('/admin/articles');
        }


        $success =
            $this->articleModel
            ->deleteArticle($id);


        if ($success) {

            Auth::redirect(
                '/admin/articles?success=' .
                urlencode(
                    'Artikel berhasil dihapus.'
                )
            );
        }


        Auth::redirect(
            '/admin/articles?error=delete_failed'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | F-019
    | KELOLA PENULIS & MITRA
    |--------------------------------------------------------------------------
    */

    public function clients()
    {
        Auth::requireRole('admin');

        $keyword =
            trim(
                $_GET['q'] ?? ''
            );

        $status =
            trim(
                $_GET['status'] ?? ''
            );


        $clients =
            $this->clientModel
            ->getAllClients(
                $keyword,
                $status
            );


        $stats =
            $this->clientModel
            ->getStats();


        $this->view(
            'admin/clients',
            [
                'user' => Auth::user(),
                'clients' => $clients,
                'stats' => $stats,
                'keyword' => $keyword,
                'status' => $status
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | F-020
    | TAMBAH CLIENT
    |--------------------------------------------------------------------------
    */

    public function clientCreate()
    {
        Auth::requireRole('admin');

        $this->view(
            'admin/client_form',
            [
                'user' => Auth::user(),
                'client' => null,
                'errors' => []
            ]
        );
    }


    public function clientStore()
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {
            Auth::redirect('/admin/clients');
        }


        $name =
            trim(
                $_POST['name'] ?? ''
            );

        $email =
            trim(
                $_POST['email'] ?? ''
            );

        $password =
            $_POST['password'] ?? '';

        $status =
            $_POST['account_status']
            ?? 'active';

        $errors = [];


        if ($name === '') {

            $errors[] =
                'Nama wajib diisi.';
        }


        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $errors[] =
                'Email tidak valid.';
        }


        if (
            strlen($password) < 6
        ) {

            $errors[] =
                'Password minimal 6 karakter.';
        }


        if (
            !in_array(
                $status,
                [
                    'active',
                    'inactive'
                ],
                true
            )
        ) {

            $status = 'active';
        }


        if (
            $this->clientModel
            ->emailExists($email)
        ) {

            $errors[] =
                'Email sudah digunakan.';
        }


        if (!empty($errors)) {

            $this->view(
                'admin/client_form',
                [
                    'user' => Auth::user(),

                    'client' => [

                        'name' =>
                            $name,

                        'email' =>
                            $email,

                        'account_status' =>
                            $status
                    ],

                    'errors' =>
                        $errors
                ]
            );

            return;
        }


        $success =
            $this->clientModel
            ->createClient(
                $name,
                $email,
                $password,
                $status
            );


        if ($success) {

            Auth::redirect(
                '/admin/clients?success=' .
                urlencode(
                    'Penulis berhasil ditambahkan.'
                )
            );
        }


        Auth::redirect(
            '/admin/clients?error=create_failed'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT CLIENT
    |--------------------------------------------------------------------------
    */

    public function clientEdit($id)
    {
        Auth::requireRole('admin');

        $client =
            $this->clientModel
            ->getClientById($id);


        if (!$client) {

            http_response_code(404);

            die(
                'Penulis tidak ditemukan.'
            );
        }


        $this->view(
            'admin/client_form',
            [
                'user' => Auth::user(),
                'client' => $client,
                'errors' => []
            ]
        );
    }


    public function clientUpdate($id)
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {
            Auth::redirect('/admin/clients');
        }


        $name =
            trim(
                $_POST['name'] ?? ''
            );

        $email =
            trim(
                $_POST['email'] ?? ''
            );

        $status =
            $_POST['account_status']
            ?? 'active';

        $password =
            $_POST['password'] ?? '';

        $errors = [];


        if ($name === '') {

            $errors[] =
                'Nama wajib diisi.';
        }


        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $errors[] =
                'Email tidak valid.';
        }


        if (
            !in_array(
                $status,
                [
                    'active',
                    'inactive'
                ],
                true
            )
        ) {

            $errors[] =
                'Status akun tidak valid.';
        }


        if (
            $this->clientModel
            ->emailExists(
                $email,
                $id
            )
        ) {

            $errors[] =
                'Email sudah digunakan.';
        }


        if (
            $password !== ''
            &&
            strlen($password) < 6
        ) {

            $errors[] =
                'Password minimal 6 karakter.';
        }


        if (!empty($errors)) {

            $this->view(
                'admin/client_form',
                [
                    'user' => Auth::user(),

                    'client' => [

                        'id' => $id,

                        'name' =>
                            $name,

                        'email' =>
                            $email,

                        'account_status' =>
                            $status
                    ],

                    'errors' =>
                        $errors
                ]
            );

            return;
        }


        $success =
            $this->clientModel
            ->updateClient(
                $id,
                $name,
                $email,
                $status
            );


        if (
            $success
            &&
            $password !== ''
        ) {

            $success =
                $this->clientModel
                ->updatePassword(
                    $id,
                    $password
                );
        }


        if ($success) {

            Auth::redirect(
                '/admin/clients?success=' .
                urlencode(
                    'Data penulis berhasil diperbarui.'
                )
            );
        }


        Auth::redirect(
            '/admin/clients?error=update_failed'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS CLIENT
    |--------------------------------------------------------------------------
    */

    public function clientDelete($id)
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {
            Auth::redirect('/admin/clients');
        }


        $success =
            $this->clientModel
            ->deleteClient($id);


        if ($success) {

            Auth::redirect(
                '/admin/clients?success=' .
                urlencode(
                    'Penulis berhasil dihapus.'
                )
            );
        }


        Auth::redirect(
            '/admin/clients?error=delete_failed'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | F-021
    | KELOLA KATEGORI
    |--------------------------------------------------------------------------
    */

    public function categories()
    {
        Auth::requireRole('admin');

        $keyword =
            trim(
                $_GET['q'] ?? ''
            );


        $categories =
            $this->categoryModel
            ->getAllCategories(
                $keyword
            );


        $this->view(
            'admin/categories',
            [
                'user' => Auth::user(),
                'categories' => $categories,
                'keyword' => $keyword
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH KATEGORI
    |--------------------------------------------------------------------------
    */

    public function categoryCreate()
    {
        Auth::requireRole('admin');

        $this->view(
            'admin/category_form',
            [
                'user' => Auth::user(),
                'category' => null,

                'parentCategories' =>
                    $this->categoryModel
                    ->getParentCategories(),

                'errors' => []
            ]
        );
    }


    public function categoryStore()
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {
            Auth::redirect('/admin/categories');
        }


        $name =
            trim(
                $_POST['name'] ?? ''
            );

        $description =
            trim(
                $_POST['description'] ?? ''
            );

        $slugInput =
            trim(
                $_POST['slug'] ?? ''
            );

        $parentId =
            (int) (
                $_POST['parent_id'] ?? 0
            );


        $parentId =
            $parentId > 0
                ? $parentId
                : null;


        $slug =
            $slugInput !== ''
                ? $this->categoryModel
                    ->makeSlug(
                        $slugInput
                    )
                : $this->categoryModel
                    ->makeSlug(
                        $name
                    );


        $errors = [];


        if ($name === '') {

            $errors[] =
                'Nama kategori wajib diisi.';
        }


        if ($slug === '') {

            $errors[] =
                'Slug kategori tidak valid.';
        }


        if (
            $this->categoryModel
            ->nameExists($name)
        ) {

            $errors[] =
                'Nama kategori sudah digunakan.';
        }


        if (
            $this->categoryModel
            ->slugExists($slug)
        ) {

            $errors[] =
                'Slug kategori sudah digunakan.';
        }


        if (!empty($errors)) {

            $this->view(
                'admin/category_form',
                [
                    'user' => Auth::user(),

                    'category' => [

                        'name' =>
                            $name,

                        'slug' =>
                            $slug,

                        'description' =>
                            $description,

                        'parent_id' =>
                            $parentId
                    ],

                    'parentCategories' =>
                        $this->categoryModel
                        ->getParentCategories(),

                    'errors' =>
                        $errors
                ]
            );

            return;
        }


        $success =
            $this->categoryModel
            ->createCategory(
                $name,
                $slug,
                $description,
                $parentId
            );


        if ($success) {

            Auth::redirect(
                '/admin/categories?success=' .
                urlencode(
                    'Kategori berhasil ditambahkan.'
                )
            );
        }


        Auth::redirect(
            '/admin/categories?error=create_failed'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT KATEGORI
    |--------------------------------------------------------------------------
    */

    public function categoryEdit($id)
    {
        Auth::requireRole('admin');

        $category =
            $this->categoryModel
            ->getCategoryById($id);


        if (!$category) {

            http_response_code(404);

            die(
                'Kategori tidak ditemukan.'
            );
        }


        $this->view(
            'admin/category_form',
            [
                'user' => Auth::user(),

                'category' =>
                    $category,

                'parentCategories' =>
                    $this->categoryModel
                    ->getParentCategories($id),

                'errors' => []
            ]
        );
    }


    public function categoryUpdate($id)
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {
            Auth::redirect('/admin/categories');
        }


        $name =
            trim(
                $_POST['name'] ?? ''
            );

        $slugInput =
            trim(
                $_POST['slug'] ?? ''
            );

        $description =
            trim(
                $_POST['description'] ?? ''
            );

        $parentId =
            (int) (
                $_POST['parent_id'] ?? 0
            );


        $parentId =
            $parentId > 0
                ? $parentId
                : null;


        $slug =
            $this->categoryModel
            ->makeSlug(
                $slugInput !== ''
                    ? $slugInput
                    : $name
            );


        $errors = [];


        if ($name === '') {

            $errors[] =
                'Nama kategori wajib diisi.';
        }


        if (
            $this->categoryModel
            ->nameExists(
                $name,
                $id
            )
        ) {

            $errors[] =
                'Nama kategori sudah digunakan.';
        }


        if (
            $this->categoryModel
            ->slugExists(
                $slug,
                $id
            )
        ) {

            $errors[] =
                'Slug kategori sudah digunakan.';
        }


        if (
            (int) $parentId
            ===
            (int) $id
        ) {

            $errors[] =
                'Kategori tidak boleh menjadi induk dari dirinya sendiri.';
        }


        if (!empty($errors)) {

            $this->view(
                'admin/category_form',
                [
                    'user' => Auth::user(),

                    'category' => [

                        'id' =>
                            $id,

                        'name' =>
                            $name,

                        'slug' =>
                            $slug,

                        'description' =>
                            $description,

                        'parent_id' =>
                            $parentId
                    ],

                    'parentCategories' =>
                        $this->categoryModel
                        ->getParentCategories($id),

                    'errors' =>
                        $errors
                ]
            );

            return;
        }


        $success =
            $this->categoryModel
            ->updateCategory(
                $id,
                $name,
                $slug,
                $description,
                $parentId
            );


        if ($success) {

            Auth::redirect(
                '/admin/categories?success=' .
                urlencode(
                    'Kategori berhasil diperbarui.'
                )
            );
        }


        Auth::redirect(
            '/admin/categories?error=update_failed'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS KATEGORI
    |--------------------------------------------------------------------------
    */

    public function categoryDelete($id)
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {
            Auth::redirect('/admin/categories');
        }


        $result =
            $this->categoryModel
            ->deleteCategory($id);


        if (
            $result['success'] ?? false
        ) {

            Auth::redirect(
                '/admin/categories?success=' .
                urlencode(
                    $result['message']
                )
            );
        }


        Auth::redirect(
            '/admin/categories?error=' .
            urlencode(
                $result['message']
                ??
                'Kategori gagal dihapus.'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | KELOLA IKLAN
    |--------------------------------------------------------------------------
    */

    public function ads()
    {
        Auth::requireRole('admin');

        $ads =
            $this->adModel
            ->getAllAds();


        $this->view(
            'admin/ads',
            [
                'user' => Auth::user(),
                'ads' => $ads
            ]
        );
    }


    public function adCreate()
    {
        Auth::requireRole('admin');

        $this->view(
            'admin/ad_form',
            [
                'user' => Auth::user(),
                'ad' => null,
                'errors' => []
            ]
        );
    }


    public function adStore()
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD']
            !==
            'POST'
        ) {

            Auth::redirect(
                '/admin/ads'
            );
        }


        $title =
            trim(
                $_POST['title'] ?? ''
            );

        $targetUrl =
            trim(
                $_POST['target_url'] ?? ''
            );

        $status =
            $_POST['status']
            ??
            'ACTIVE';

        $sortOrder =
            (int) (
                $_POST['sort_order']
                ??
                0
            );


        $errors = [];


        if ($title === '') {

            $errors[] =
                'Nama iklan wajib diisi.';
        }


        if (
            !filter_var(
                $targetUrl,
                FILTER_VALIDATE_URL
            )
        ) {

            $errors[] =
                'URL tujuan iklan tidak valid.';
        }


        if (
            !in_array(
                $status,
                [
                    'ACTIVE',
                    'INACTIVE'
                ],
                true
            )
        ) {

            $status =
                'ACTIVE';
        }


        $file =
            $_FILES['image']
            ??
            null;

        $imagePath =
            '';


        if (
            !$file
            ||
            (
                $file['error']
                ??
                UPLOAD_ERR_NO_FILE
            )
            ===
            UPLOAD_ERR_NO_FILE
        ) {

            $errors[] =
                'Gambar iklan wajib diupload.';

        } else {

            $uploadResult =
                $this->uploadAdImage(
                    $file
                );


            if (
                !$uploadResult['success']
            ) {

                $errors[] =
                    $uploadResult['message'];

            } else {

                $imagePath =
                    $uploadResult['path'];
            }
        }


        if (!empty($errors)) {

            $this->view(
                'admin/ad_form',
                [
                    'user' => Auth::user(),

                    'ad' => [

                        'title' =>
                            $title,

                        'target_url' =>
                            $targetUrl,

                        'status' =>
                            $status,

                        'sort_order' =>
                            $sortOrder
                    ],

                    'errors' =>
                        $errors
                ]
            );

            return;
        }


        if (
            $this->adModel
            ->createAd(
                $title,
                $imagePath,
                $targetUrl,
                $status,
                $sortOrder
            )
        ) {

            Auth::redirect(
                '/admin/ads?success=' .
                urlencode(
                    'Iklan berhasil ditambahkan.'
                )
            );
        }


        Auth::redirect(
            '/admin/ads?error=' .
            urlencode(
                'Iklan gagal ditambahkan.'
            )
        );
    }


    public function adEdit($id)
    {
        Auth::requireRole('admin');

        $ad =
            $this->adModel
            ->getAdById($id);


        if (!$ad) {

            http_response_code(404);

            die(
                'Iklan tidak ditemukan.'
            );
        }


        $this->view(
            'admin/ad_form',
            [
                'user' => Auth::user(),
                'ad' => $ad,
                'errors' => []
            ]
        );
    }


    public function adUpdate($id)
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD']
            !==
            'POST'
        ) {

            Auth::redirect(
                '/admin/ads'
            );
        }


        $ad =
            $this->adModel
            ->getAdById($id);


        if (!$ad) {

            http_response_code(404);

            die(
                'Iklan tidak ditemukan.'
            );
        }


        $title =
            trim(
                $_POST['title'] ?? ''
            );

        $targetUrl =
            trim(
                $_POST['target_url'] ?? ''
            );

        $status =
            $_POST['status']
            ??
            'ACTIVE';

        $sortOrder =
            (int) (
                $_POST['sort_order']
                ??
                0
            );


        $errors = [];


        if ($title === '') {

            $errors[] =
                'Nama iklan wajib diisi.';
        }


        if (
            !filter_var(
                $targetUrl,
                FILTER_VALIDATE_URL
            )
        ) {

            $errors[] =
                'URL tujuan iklan tidak valid.';
        }


        if (
            !in_array(
                $status,
                [
                    'ACTIVE',
                    'INACTIVE'
                ],
                true
            )
        ) {

            $status =
                'ACTIVE';
        }


        $file =
            $_FILES['image']
            ??
            null;

        $imagePath =
            null;


        if (
            $file
            &&
            (
                $file['error']
                ??
                UPLOAD_ERR_NO_FILE
            )
            !==
            UPLOAD_ERR_NO_FILE
        ) {

            $uploadResult =
                $this->uploadAdImage(
                    $file
                );


            if (
                !$uploadResult['success']
            ) {

                $errors[] =
                    $uploadResult['message'];

            } else {

                $imagePath =
                    $uploadResult['path'];
            }
        }


        if (!empty($errors)) {

            $ad['title'] =
                $title;

            $ad['target_url'] =
                $targetUrl;

            $ad['status'] =
                $status;

            $ad['sort_order'] =
                $sortOrder;


            $this->view(
                'admin/ad_form',
                [
                    'user' => Auth::user(),
                    'ad' => $ad,
                    'errors' => $errors
                ]
            );

            return;
        }


        $success =
            $this->adModel
            ->updateAd(
                $id,
                $title,
                $targetUrl,
                $status,
                $sortOrder,
                $imagePath
            );


        if ($success) {

            Auth::redirect(
                '/admin/ads?success=' .
                urlencode(
                    'Iklan berhasil diperbarui.'
                )
            );
        }


        Auth::redirect(
            '/admin/ads?error=' .
            urlencode(
                'Iklan gagal diperbarui.'
            )
        );
    }


    public function adToggle($id)
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD']
            !==
            'POST'
        ) {

            Auth::redirect(
                '/admin/ads'
            );
        }


        $success =
            $this->adModel
            ->toggleStatus($id);


        Auth::redirect(
            '/admin/ads?' .
            (
                $success
                    ? 'success='
                    : 'error='
            ) .
            urlencode(
                $success
                    ? 'Status iklan berhasil diubah.'
                    : 'Status iklan gagal diubah.'
            )
        );
    }


    public function adDelete($id)
    {
        Auth::requireRole('admin');

        if (
            $_SERVER['REQUEST_METHOD']
            !==
            'POST'
        ) {

            Auth::redirect(
                '/admin/ads'
            );
        }


        $ad =
            $this->adModel
            ->getAdById($id);


        $success =
            $this->adModel
            ->deleteAd($id);


        if (
            $success
            &&
            $ad
        ) {

            $relativePath =
                trim(
                    str_replace(
                        '\\',
                        '/',
                        $ad['image_path']
                        ??
                        ''
                    ),
                    '/'
                );


            $filePath =
                dirname(
                    __DIR__,
                    2
                )
                .
                DIRECTORY_SEPARATOR
                .
                str_replace(
                    '/',
                    DIRECTORY_SEPARATOR,
                    $relativePath
                );


            if (
                $relativePath !== ''
                &&
                is_file(
                    $filePath
                )
            ) {

                @unlink(
                    $filePath
                );
            }
        }


        Auth::redirect(
            '/admin/ads?' .
            (
                $success
                    ? 'success='
                    : 'error='
            ) .
            urlencode(
                $success
                    ? 'Iklan berhasil dihapus.'
                    : 'Iklan gagal dihapus.'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD GAMBAR IKLAN
    |--------------------------------------------------------------------------
    */

    private function uploadAdImage($file)
    {
        if (
            (
                $file['error']
                ??
                UPLOAD_ERR_OK
            )
            !==
            UPLOAD_ERR_OK
        ) {

            return [
                'success' => false,
                'message' =>
                    'Upload gambar gagal.'
            ];
        }


        $maxSize =
            5 * 1024 * 1024;


        if (
            ($file['size'] ?? 0)
            > $maxSize
        ) {

            return [
                'success' => false,
                'message' =>
                    'Ukuran gambar maksimal 5 MB.'
            ];
        }


        $allowed = [

            'image/jpeg' =>
                'jpg',

            'image/png' =>
                'png',

            'image/webp' =>
                'webp',

            'image/gif' =>
                'gif'
        ];


        $mime = '';


        if (
            function_exists(
                'finfo_open'
            )
        ) {

            $finfo =
                finfo_open(
                    FILEINFO_MIME_TYPE
                );

            $mime =
                finfo_file(
                    $finfo,
                    $file['tmp_name']
                );

            finfo_close(
                $finfo
            );

        } else {

            $mime =
                mime_content_type(
                    $file['tmp_name']
                );
        }


        if (
            !isset(
                $allowed[$mime]
            )
        ) {

            return [
                'success' => false,
                'message' =>
                    'Format gambar harus JPG, PNG, WEBP, atau GIF.'
            ];
        }


        $extension =
            $allowed[$mime];


        $filename =
            'ad_' .
            date('Ymd_His') .
            '_' .
            bin2hex(
                random_bytes(5)
            ) .
            '.' .
            $extension;


        $projectRoot =
            dirname(
                __DIR__,
                2
            );


        $uploadDirectory =
            $projectRoot .
            DIRECTORY_SEPARATOR .
            'public' .
            DIRECTORY_SEPARATOR .
            'uploads' .
            DIRECTORY_SEPARATOR .
            'ads';


        if (
            !is_dir(
                $uploadDirectory
            )
        ) {

            if (
                !mkdir(
                    $uploadDirectory,
                    0755,
                    true
                )
            ) {

                return [
                    'success' => false,
                    'message' =>
                        'Folder upload iklan tidak dapat dibuat.'
                ];
            }
        }


        $destination =
            $uploadDirectory .
            DIRECTORY_SEPARATOR .
            $filename;


        if (
            !move_uploaded_file(
                $file['tmp_name'],
                $destination
            )
        ) {

            return [
                'success' => false,
                'message' =>
                    'Gambar gagal disimpan ke server.'
            ];
        }


        return [
            'success' => true,
            'path' =>
                'public/uploads/ads/' .
                $filename
        ];
    }

}