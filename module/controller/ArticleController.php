<?php

class ArticleController extends Controller
{
    private $articleModel;
    private $categoryModel;
    private $tagModel;

    public function __construct()
    {
        $this->articleModel = new Article();
        $this->categoryModel = new Category();
        $this->tagModel = new Tag();
    }

    /**
     * =========================================================
     * CREATE ARTICLE
     * =========================================================
     */
    public function create()
    {
        Auth::requireRole('client');

        $categories = $this->categoryModel->getAll();
        $tags = $this->tagModel->getAll();

        $this->view(
            'client/article-create',
            [
                'categories' => $categories,
                'tags' => $tags,
                'error' => null,
                'title' => '',
                'excerpt' => '',
                'content' => '',
                'category_id' => 0,
                'selectedTagIds' => [],
                'custom_tags' => ''
            ]
        );
    }

    /**
     * =========================================================
     * STORE ARTICLE
     * =========================================================
     */
    public function store()
    {
        Auth::requireRole('client');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Auth::redirect('/client/dashboard');
        }

        $title = trim($_POST['title'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');

        $selectedTagIds = $this->collectTagIds($_POST['tags'] ?? []);

        /**
         * Tag baru yang diketik user (dipisah koma).
         * Baru dibuat di database setelah semua validasi lolos.
         */
        $customTagNames = $this->parseCustomTags(
            $_POST['custom_tags'] ?? ''
        );

        /**
         * ---------------------------------------------------------
         * VALIDASI DATA
         * ---------------------------------------------------------
         */

        if ($title === '') {
            $this->showCreateError(
                'Judul artikel wajib diisi.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        if ($categoryId <= 0) {
            $this->showCreateError(
                'Kategori artikel wajib dipilih.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        if ($content === '') {
            $this->showCreateError(
                'Isi artikel wajib diisi.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        /**
         * ---------------------------------------------------------
         * CEK KATEGORI
         * ---------------------------------------------------------
         */

        $category = $this->categoryModel->findById($categoryId);

        if (!$category) {
            $this->showCreateError(
                'Kategori tidak ditemukan.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        /**
         * ---------------------------------------------------------
         * VALIDASI TAG
         * ---------------------------------------------------------
         */

        foreach ($selectedTagIds as $tagId) {
            $tag = $this->tagModel->findById($tagId);

            if (!$tag) {
                $this->showCreateError(
                    'Salah satu tag yang dipilih tidak ditemukan.',
                    $title,
                    $excerpt,
                    $content,
                    $categoryId,
                    $selectedTagIds
                );
                return;
            }
        }

        /**
         * ---------------------------------------------------------
         * VALIDASI THUMBNAIL
         * ---------------------------------------------------------
         */

        $thumbnail = $_FILES['thumbnail'] ?? null;

        if (
            !$thumbnail ||
            !isset($thumbnail['error']) ||
            $thumbnail['error'] === UPLOAD_ERR_NO_FILE
        ) {
            $this->showCreateError(
                'Thumbnail artikel wajib diupload.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        if ($thumbnail['error'] !== UPLOAD_ERR_OK) {
            $this->showCreateError(
                'Gagal mengupload thumbnail.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        if (
            !isset($thumbnail['tmp_name']) ||
            !is_uploaded_file($thumbnail['tmp_name'])
        ) {
            $this->showCreateError(
                'File thumbnail tidak valid.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        $maxSize = 5 * 1024 * 1024;

        if ((int) $thumbnail['size'] > $maxSize) {
            $this->showCreateError(
                'Ukuran thumbnail maksimal 5 MB.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        /**
         * ---------------------------------------------------------
         * CEK FORMAT FILE
         * ---------------------------------------------------------
         */

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $mimeType = $finfo->file(
            $thumbnail['tmp_name']
        );

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($allowedTypes[$mimeType])) {
            $this->showCreateError(
                'Format thumbnail hanya boleh JPG, PNG, atau WEBP.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        /**
         * Pastikan file benar-benar gambar.
         */
        $imageInfo = @getimagesize(
            $thumbnail['tmp_name']
        );

        if ($imageInfo === false) {
            $this->showCreateError(
                'File yang diupload bukan gambar yang valid.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        $extension = $allowedTypes[$mimeType];

        /**
         * ---------------------------------------------------------
         * BUAT SLUG
         * ---------------------------------------------------------
         */

        $slug = $this->createSlug($title)
            . '-'
            . time();

        $authorId = Auth::id();

        /**
         * ---------------------------------------------------------
         * SIMPAN ARTIKEL
         * ---------------------------------------------------------
         */

        try {
            $articleId = $this->articleModel->createDraft(
                $authorId,
                $categoryId,
                $title,
                $slug,
                $excerpt,
                $content
            );
        } catch (Throwable $e) {
            error_log(
                'Gagal membuat artikel: ' . $e->getMessage()
            );

            $this->showCreateError(
                'Artikel gagal disimpan.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        if (!$articleId) {
            $this->showCreateError(
                'Artikel gagal disimpan.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        /**
         * ---------------------------------------------------------
         * FOLDER UPLOAD
         * ---------------------------------------------------------
         */

        $uploadDirectory =
            __DIR__ .
            '/../../public/uploads/articles/';

        if (!is_dir($uploadDirectory)) {
            if (!mkdir($uploadDirectory, 0755, true)) {
                $this->articleModel->deleteArticle(
                    $articleId,
                    $authorId
                );

                $this->showCreateError(
                    'Folder upload thumbnail gagal dibuat.',
                    $title,
                    $excerpt,
                    $content,
                    $categoryId,
                    $selectedTagIds
                );
                return;
            }
        }

        /**
         * ---------------------------------------------------------
         * NAMA FILE
         * ---------------------------------------------------------
         */

        $fileName =
            'article_' .
            $articleId .
            '_' .
            bin2hex(random_bytes(8)) .
            '.' .
            $extension;

        $destination =
            $uploadDirectory .
            $fileName;

        /**
         * ---------------------------------------------------------
         * PINDAHKAN FILE
         * ---------------------------------------------------------
         */

        if (
            !move_uploaded_file(
                $thumbnail['tmp_name'],
                $destination
            )
        ) {
            $this->articleModel->deleteArticle(
                $articleId,
                $authorId
            );

            $this->showCreateError(
                'Thumbnail gagal disimpan. Artikel dibatalkan.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        $filePath =
            'public/uploads/articles/' .
            $fileName;

        /**
         * ---------------------------------------------------------
         * SIMPAN THUMBNAIL, TAG, DAN LOG
         * ---------------------------------------------------------
         */

        try {
            $this->articleModel->updateThumbnail(
                $articleId,
                $filePath
            );

            $this->articleModel->addThumbnailImage(
                $articleId,
                $fileName,
                $filePath,
                (int) $thumbnail['size'],
                $extension
            );

            /**
             * Gabungkan tag yang dicentang dengan tag baru
             * yang diketik user. Tag baru dibuat jika belum ada.
             */
            $customTagIds = $this->resolveCustomTagIds(
                $customTagNames
            );

            $allTagIds = array_values(
                array_unique(
                    array_merge(
                        $selectedTagIds,
                        $customTagIds
                    )
                )
            );

            foreach ($allTagIds as $tagId) {
                $this->articleModel->addTag(
                    $articleId,
                    $tagId
                );
            }

            $this->articleModel->addStatusLog(
                $articleId,
                $authorId,
                null,
                'DRAFT',
                'Artikel dibuat sebagai draft.'
            );
        } catch (Throwable $e) {
            error_log(
                'Gagal melengkapi artikel: ' . $e->getMessage()
            );

            if (is_file($destination)) {
                @unlink($destination);
            }

            $this->articleModel->deleteArticle(
                $articleId,
                $authorId
            );

            $this->showCreateError(
                'Artikel gagal diselesaikan.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        /**
         * ---------------------------------------------------------
         * REDIRECT
         * ---------------------------------------------------------
         */

        Auth::redirect(
            '/client/dashboard'
        );
    }

    /**
     * =========================================================
     * EDIT ARTICLE
     * =========================================================
     */
    public function edit($id)
    {
        Auth::requireRole('client');

        $articleId = (int) $id;
        $authorId = Auth::id();

        if ($articleId <= 0) {
            Auth::redirect(
                '/client/dashboard'
            );
        }

        $article =
            $this->articleModel
            ->findByIdAndAuthor(
                $articleId,
                $authorId
            );

        if (!$article) {
            Auth::redirect(
                '/client/dashboard'
            );
        }

        /**
         * Artikel yang boleh diedit client.
         */
        if (
            !in_array(
                $article['status'],
                [
                    'DRAFT',
                    'REVISI'
                ],
                true
            )
        ) {
            Auth::redirect(
                '/client/dashboard'
            );
        }

        $categories =
            $this->categoryModel
            ->getAll();

        $tags =
            $this->tagModel
            ->getAll();

        $selectedTags =
            $this->articleModel
            ->getTags($articleId);

        $selectedTagIds = [];

        foreach ($selectedTags as $tag) {
            $selectedTagIds[] =
                (int) $tag['id'];
        }

        $this->view(
            'client/article-edit',
            [
                'article' => $article,
                'categories' => $categories,
                'tags' => $tags,
                'selectedTagIds' => $selectedTagIds,
                'custom_tags' => '',
                'error' => null
            ]
        );
    }

    /**
     * =========================================================
     * UPDATE ARTICLE
     * =========================================================
     */
    public function update($id)
    {
        Auth::requireRole('client');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Auth::redirect('/client/dashboard');
        }

        $articleId = (int) $id;
        $authorId = Auth::id();

        if ($articleId <= 0) {
            Auth::redirect(
                '/client/dashboard'
            );
        }

        /**
         * ---------------------------------------------------------
         * AMBIL ARTIKEL
         * ---------------------------------------------------------
         */

        $article =
            $this->articleModel
            ->findByIdAndAuthor(
                $articleId,
                $authorId
            );

        if (!$article) {
            Auth::redirect(
                '/client/dashboard'
            );
        }

        /**
         * ---------------------------------------------------------
         * CEK STATUS
         * ---------------------------------------------------------
         */

        if (
            !in_array(
                $article['status'],
                [
                    'DRAFT',
                    'REVISI'
                ],
                true
            )
        ) {
            Auth::redirect(
                '/client/dashboard'
            );
        }

        /**
         * ---------------------------------------------------------
         * AMBIL DATA FORM
         * ---------------------------------------------------------
         */

        $title =
            trim(
                $_POST['title'] ?? ''
            );

        $categoryId =
            (int)
            (
                $_POST['category_id'] ?? 0
            );

        $excerpt =
            trim(
                $_POST['excerpt'] ?? ''
            );

        $content =
            trim(
                $_POST['content'] ?? ''
            );

        $selectedTagIds = $this->collectTagIds(
            $_POST['tags'] ?? []
        );

        $customTagNames = $this->parseCustomTags(
            $_POST['custom_tags'] ?? ''
        );

        /**
         * ---------------------------------------------------------
         * VALIDASI
         * ---------------------------------------------------------
         */

        if ($title === '') {
            $this->showEditError(
                $article,
                'Judul artikel wajib diisi.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        if ($categoryId <= 0) {
            $this->showEditError(
                $article,
                'Kategori artikel wajib dipilih.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        if ($content === '') {
            $this->showEditError(
                $article,
                'Isi artikel wajib diisi.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        /**
         * ---------------------------------------------------------
         * CEK KATEGORI
         * ---------------------------------------------------------
         */

        $category =
            $this->categoryModel
            ->findById($categoryId);

        if (!$category) {
            $this->showEditError(
                $article,
                'Kategori tidak ditemukan.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        /**
         * ---------------------------------------------------------
         * VALIDASI TAG
         * ---------------------------------------------------------
         */

        foreach ($selectedTagIds as $tagId) {
            $tag =
                $this->tagModel
                ->findById($tagId);

            if (!$tag) {
                $this->showEditError(
                    $article,
                    'Salah satu tag yang dipilih tidak ditemukan.',
                    $title,
                    $excerpt,
                    $content,
                    $categoryId,
                    $selectedTagIds
                );
                return;
            }
        }

        /**
         * ---------------------------------------------------------
         * CEK THUMBNAIL BARU
         * ---------------------------------------------------------
         */

        $thumbnail =
            $_FILES['thumbnail'] ?? null;

        $newFilePath = null;
        $newFileName = null;
        $newDestination = null;
        $newFileSize = 0;
        $newExtension = null;

        if (
            $thumbnail &&
            isset($thumbnail['error']) &&
            $thumbnail['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            if ($thumbnail['error'] !== UPLOAD_ERR_OK) {
                $this->showEditError(
                    $article,
                    'Gagal mengupload thumbnail baru.',
                    $title,
                    $excerpt,
                    $content,
                    $categoryId,
                    $selectedTagIds
                );
                return;
            }

            if (
                !isset($thumbnail['tmp_name']) ||
                !is_uploaded_file($thumbnail['tmp_name'])
            ) {
                $this->showEditError(
                    $article,
                    'File thumbnail baru tidak valid.',
                    $title,
                    $excerpt,
                    $content,
                    $categoryId,
                    $selectedTagIds
                );
                return;
            }

            $maxSize = 5 * 1024 * 1024;

            if ((int) $thumbnail['size'] > $maxSize) {
                $this->showEditError(
                    $article,
                    'Ukuran thumbnail maksimal 5 MB.',
                    $title,
                    $excerpt,
                    $content,
                    $categoryId,
                    $selectedTagIds
                );
                return;
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $mimeType = $finfo->file(
                $thumbnail['tmp_name']
            );

            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($allowedTypes[$mimeType])) {
                $this->showEditError(
                    $article,
                    'Format thumbnail hanya boleh JPG, PNG, atau WEBP.',
                    $title,
                    $excerpt,
                    $content,
                    $categoryId,
                    $selectedTagIds
                );
                return;
            }

            $imageInfo = @getimagesize(
                $thumbnail['tmp_name']
            );

            if ($imageInfo === false) {
                $this->showEditError(
                    $article,
                    'File thumbnail bukan gambar yang valid.',
                    $title,
                    $excerpt,
                    $content,
                    $categoryId,
                    $selectedTagIds
                );
                return;
            }

            $newExtension = $allowedTypes[$mimeType];

            /**
             * Folder upload.
             */
            $uploadDirectory =
                __DIR__ .
                '/../../public/uploads/articles/';

            if (!is_dir($uploadDirectory)) {
                if (!mkdir($uploadDirectory, 0755, true)) {
                    $this->showEditError(
                        $article,
                        'Folder upload thumbnail gagal dibuat.',
                        $title,
                        $excerpt,
                        $content,
                        $categoryId,
                        $selectedTagIds
                    );
                    return;
                }
            }

            /**
             * Nama file baru.
             */
            $newFileName =
                'article_' .
                $articleId .
                '_' .
                bin2hex(random_bytes(8)) .
                '.' .
                $newExtension;

            $newDestination =
                $uploadDirectory .
                $newFileName;

            /**
             * Pindahkan file baru.
             */
            if (
                !move_uploaded_file(
                    $thumbnail['tmp_name'],
                    $newDestination
                )
            ) {
                $this->showEditError(
                    $article,
                    'Thumbnail baru gagal disimpan.',
                    $title,
                    $excerpt,
                    $content,
                    $categoryId,
                    $selectedTagIds
                );
                return;
            }

            $newFilePath =
                'public/uploads/articles/' .
                $newFileName;

            $newFileSize =
                (int) $thumbnail['size'];
        }

        /**
         * ---------------------------------------------------------
         * UPDATE DATABASE
         * ---------------------------------------------------------
         */

        try {
            $this->articleModel->updateDraft(
                $articleId,
                $authorId,
                $categoryId,
                $title,
                $excerpt,
                $content
            );

            /**
             * -----------------------------------------------------
             * UPDATE TAG (checkbox + tag baru dari user)
             * -----------------------------------------------------
             */

            $customTagIds = $this->resolveCustomTagIds(
                $customTagNames
            );

            $allTagIds = array_values(
                array_unique(
                    array_merge(
                        $selectedTagIds,
                        $customTagIds
                    )
                )
            );

            $this->articleModel->deleteTags(
                $articleId
            );

            foreach ($allTagIds as $tagId) {
                $this->articleModel->addTag(
                    $articleId,
                    $tagId
                );
            }

            /**
             * -----------------------------------------------------
             * UPDATE THUMBNAIL
             * -----------------------------------------------------
             */

            if ($newFilePath !== null) {
                $oldThumbnail =
                    $this->articleModel
                    ->getThumbnailImage(
                        $articleId
                    );

                $this->articleModel
                    ->removeThumbnailImages(
                        $articleId
                    );

                $this->articleModel
                    ->updateThumbnail(
                        $articleId,
                        $newFilePath
                    );

                $this->articleModel
                    ->addThumbnailImage(
                        $articleId,
                        $newFileName,
                        $newFilePath,
                        $newFileSize,
                        $newExtension
                    );

                /**
                 * Hapus file thumbnail lama.
                 */
                if (
                    $oldThumbnail &&
                    !empty($oldThumbnail['file_path'])
                ) {
                    $oldPhysicalPath =
                        __DIR__ .
                        '/../../' .
                        ltrim(
                            $oldThumbnail['file_path'],
                            '/'
                        );

                    if (is_file($oldPhysicalPath)) {
                        @unlink($oldPhysicalPath);
                    }
                }
            }

            /**
             * -----------------------------------------------------
             * ACTION
             * -----------------------------------------------------
             */

            $action =
                $_POST['action'] ?? 'draft';

            if ($action === 'submit') {
                $this->articleModel
                    ->submitArticle(
                        $articleId,
                        $authorId
                    );

                $this->articleModel
                    ->addStatusLog(
                        $articleId,
                        $authorId,
                        $article['status'],
                        'MENUNGGU_REVIEW',
                        'Artikel dikirim ulang untuk review.'
                    );
            } else {
                /**
                 * Status tidak berubah saat hanya disimpan
                 * (DRAFT tetap DRAFT, REVISI tetap REVISI).
                 */
                $this->articleModel
                    ->addStatusLog(
                        $articleId,
                        $authorId,
                        $article['status'],
                        $article['status'],
                        'Artikel diperbarui.'
                    );
            }
        } catch (Throwable $e) {
            error_log(
                'Gagal update artikel: ' .
                $e->getMessage()
            );

            if (
                $newDestination !== null &&
                is_file($newDestination)
            ) {
                @unlink($newDestination);
            }

            $this->showEditError(
                $article,
                'Artikel gagal diperbarui. Silakan coba lagi.',
                $title,
                $excerpt,
                $content,
                $categoryId,
                $selectedTagIds
            );
            return;
        }

        /**
         * ---------------------------------------------------------
         * REDIRECT
         * ---------------------------------------------------------
         */

        Auth::redirect(
            '/client/dashboard'
        );
    }

    /**
     * =========================================================
     * ERROR CREATE
     * =========================================================
     */
    private function showCreateError(
        $error,
        $title = '',
        $excerpt = '',
        $content = '',
        $categoryId = 0,
        $selectedTagIds = []
    ) {
        $categories =
            $this->categoryModel
            ->getAll();

        $tags =
            $this->tagModel
            ->getAll();

        $this->view(
            'client/article-create',
            [
                'categories' => $categories,
                'tags' => $tags,
                'error' => $error,

                'title' => $title,
                'excerpt' => $excerpt,
                'content' => $content,
                'category_id' => $categoryId,
                'selectedTagIds' => $selectedTagIds,
                'custom_tags' => trim(
                    (string) ($_POST['custom_tags'] ?? '')
                )
            ]
        );
    }

    /**
     * =========================================================
     * ERROR EDIT
     * =========================================================
     */
    private function showEditError(
        $article,
        $error,
        $title = null,
        $excerpt = null,
        $content = null,
        $categoryId = null,
        $selectedTagIds = null
    ) {
        $categories =
            $this->categoryModel
            ->getAll();

        $tags =
            $this->tagModel
            ->getAll();

        /**
         * Jika validasi gagal sebelum data form lengkap,
         * gunakan data artikel lama.
         */
        if ($title === null) {
            $title =
                $article['title'] ?? '';
        }

        if ($excerpt === null) {
            $excerpt =
                $article['excerpt'] ?? '';
        }

        if ($content === null) {
            $content =
                $article['content'] ?? '';
        }

        if ($categoryId === null) {
            $categoryId =
                (int)
                (
                    $article['category_id'] ?? 0
                );
        }

        if ($selectedTagIds === null) {
            $selectedTags =
                $this->articleModel
                ->getTags(
                    $article['id']
                );

            $selectedTagIds = [];

            foreach ($selectedTags as $tag) {
                $selectedTagIds[] =
                    (int) $tag['id'];
            }
        }

        /**
         * Salin nilai terbaru ke data artikel
         * agar view lama tetap bisa membaca
         * $article['title'], dll.
         */
        $article['title'] = $title;
        $article['excerpt'] = $excerpt;
        $article['content'] = $content;
        $article['category_id'] = $categoryId;

        $this->view(
            'client/article-edit',
            [
                'article' => $article,
                'categories' => $categories,
                'tags' => $tags,
                'selectedTagIds' => $selectedTagIds,
                'custom_tags' => trim(
                    (string) ($_POST['custom_tags'] ?? '')
                ),
                'error' => $error
            ]
        );
    }

    /**
     * =========================================================
     * COLLECT TAG IDS (dari checkbox)
     * =========================================================
     */
    private function collectTagIds($rawTags)
    {
        if (!is_array($rawTags)) {
            return [];
        }

        $ids = [];

        foreach ($rawTags as $tagId) {
            $tagId = (int) $tagId;

            if ($tagId > 0) {
                $ids[] = $tagId;
            }
        }

        return array_values(
            array_unique($ids)
        );
    }

    /**
     * =========================================================
     * PARSE CUSTOM TAGS
     * =========================================================
     *
     * Mengubah input "Kesehatan, Hiburan" menjadi array
     * nama tag yang bersih.
     *
     * - dipisah koma
     * - HTML dibuang, spasi berlebih dirapikan
     * - tanda # di depan dibuang
     * - maksimal 30 karakter per tag
     * - "Hukum" dan "hukum" dianggap sama
     * - maksimal 5 tag baru
     */
    private function parseCustomTags($raw)
    {
        $names = [];

        foreach (explode(',', (string) $raw) as $name) {
            $name = trim(strip_tags($name));
            $name = preg_replace('/\s+/u', ' ', $name);
            $name = ltrim($name, '#');
            $name = trim($name);

            if ($name === '') {
                continue;
            }

            $name = mb_substr($name, 0, 30);

            $names[mb_strtolower($name)] = $name;
        }

        return array_slice(
            array_values($names),
            0,
            5
        );
    }

    /**
     * =========================================================
     * RESOLVE CUSTOM TAG IDS
     * =========================================================
     *
     * Cari tag berdasarkan nama. Jika belum ada, buat baru.
     * Mengembalikan array ID tag.
     */
    private function resolveCustomTagIds(array $names)
    {
        $ids = [];

        foreach ($names as $name) {
            $existing = $this->tagModel->findByName($name);

            if ($existing) {
                $ids[] = (int) $existing['id'];
                continue;
            }

            $newId = $this->tagModel->createAndGetId($name);

            if ($newId > 0) {
                $ids[] = $newId;
            }
        }

        return $ids;
    }

    /**
     * =========================================================
     * CREATE SLUG
     * =========================================================
     */
    private function createSlug($text)
    {
        $text = strtolower(
            trim($text)
        );

        /**
         * Hilangkan karakter yang tidak diperlukan.
         */
        $text = preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $text
        );

        $text = trim(
            $text,
            '-'
        );

        if ($text === '') {
            $text = 'artikel';
        }

        return $text;
    }
}