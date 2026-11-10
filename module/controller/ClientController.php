<?php

class ClientController extends Controller
{
    private $articleModel;

    public function __construct()
    {
        $this->articleModel = new Article();
    }

    /**
     * Dashboard client.
     */
    public function dashboard()
    {
        Auth::requireRole('client');

        $userId = Auth::id();

        $stats = $this->articleModel
            ->getClientStats($userId);

        $articles = $this->articleModel
            ->getByAuthor($userId);

        $user = Auth::user();

        $this->view(
            'client/dashboard',
            [
                'user' => $user,
                'stats' => $stats,
                'articles' => $articles
            ]
        );
    }

    /**
     * Daftar seluruh artikel milik client.
     */
    public function articles()
    {
        Auth::requireRole('client');

        $userId = Auth::id();

        $articles = $this->articleModel
            ->getByAuthor($userId);

        $user = Auth::user();

        $this->view(
            'client/status',
            [
                'user' => $user,
                'articles' => $articles
            ]
        );
    }

    /**
     * Daftar artikel yang membutuhkan revisi.
     */
    public function revisions()
    {
        Auth::requireRole('client');

        $userId = Auth::id();

        $articles = $this->articleModel
            ->getByAuthor($userId);

        $revisionArticles = [];

        foreach ($articles as $article) {
            if (($article['status'] ?? '') === 'REVISI') {
                $revisionArticles[] = $article;
            }
        }

        $user = Auth::user();

        $this->view(
            'client/revisions',
            [
                'user' => $user,
                'articles' => $revisionArticles
            ]
        );
    }
}