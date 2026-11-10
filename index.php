<?php

/*
|--------------------------------------------------------------------------
| INFORA SN
| Front Controller
|--------------------------------------------------------------------------
*/

error_reporting(E_ALL);
ini_set('display_errors', '1');


/*
|--------------------------------------------------------------------------
| CORE
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Auth.php';


/*
|--------------------------------------------------------------------------
| MODEL
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/module/model/User.php';
require_once __DIR__ . '/module/model/Article.php';
require_once __DIR__ . '/module/model/Category.php';
require_once __DIR__ . '/module/model/Tag.php';
require_once __DIR__ . '/module/model/PublicArticle.php';
require_once __DIR__ . '/module/model/AdminArticle.php';
require_once __DIR__ . '/module/model/AdminClient.php';
require_once __DIR__ . '/module/model/AdminCategory.php';
require_once __DIR__ . '/module/model/AdminAd.php'; 

/*
|--------------------------------------------------------------------------
| CONTROLLER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/module/controller/AuthController.php';
require_once __DIR__ . '/module/controller/PublicController.php';
require_once __DIR__ . '/module/controller/ClientController.php';
require_once __DIR__ . '/module/controller/ArticleController.php';
require_once __DIR__ . '/module/controller/AdminController.php';


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Auth::start();


/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/

$router = new Router();


/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

$router->get(
    '/',
    ['PublicController', 'home']
);

$router->get(
    '/category/{slug}',
    ['PublicController', 'category']
);

$router->get(
    '/article/{slug}',
    ['PublicController', 'article']
);

$router->get(
    '/search',
    ['PublicController', 'search']
);


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

$router->get(
    '/login',
    ['AuthController', 'login']
);

$router->post(
    '/login',
    ['AuthController', 'processLogin']
);

$router->get(
    '/register',
    ['AuthController', 'register']
);

$router->post(
    '/register',
    ['AuthController', 'processRegister']
);

$router->get(
    '/logout',
    ['AuthController', 'logout']
);


/*
|--------------------------------------------------------------------------
| CLIENT
|--------------------------------------------------------------------------
*/

$router->get(
    '/client/dashboard',
    ['ClientController', 'dashboard']
);

$router->get(
    '/client/articles',
    ['ClientController', 'articles']
);

$router->get(
    '/client/revisions',
    ['ClientController', 'revisions']
);


/*
|--------------------------------------------------------------------------
| CLIENT - ARTICLE
|--------------------------------------------------------------------------
*/

$router->get(
    '/client/article/create',
    ['ArticleController', 'create']
);

$router->post(
    '/client/article/store',
    ['ArticleController', 'store']
);

$router->get(
    '/client/article/edit/{id}',
    ['ArticleController', 'edit']
);

$router->post(
    '/client/article/update/{id}',
    ['ArticleController', 'update']
);


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
$router->get(
    '/admin/articles/create',
    ['AdminController', 'articleCreate']
);

$router->post(
    '/admin/articles/store',
    ['AdminController', 'articleStore']
);

/*
|--------------------------------------------------------------------------
| Dashboard Admin
|--------------------------------------------------------------------------
*/

$router->get(
    '/admin/dashboard',
    ['AdminController', 'dashboard']
);


/*
|--------------------------------------------------------------------------
| Daftar Semua Artikel
|--------------------------------------------------------------------------
*/

$router->get(
    '/admin/articles',
    ['AdminController', 'articles']
);


/*
|--------------------------------------------------------------------------
| Review Artikel
|--------------------------------------------------------------------------
*/

$router->get(
    '/admin/article/{id}/review',
    ['AdminController', 'review']
);

$router->post(
    '/admin/article/{id}/review',
    ['AdminController', 'processReview']
);


/*
|--------------------------------------------------------------------------
| UBAH STATUS ARTIKEL
|--------------------------------------------------------------------------
|
| INI YANG SEBELUMNYA BELUM TERDAFTAR
|
*/

$router->get(
    '/admin/article/{id}/status',
    ['AdminController', 'status']
);

$router->post(
    '/admin/article/{id}/status',
    ['AdminController', 'processStatus']
);


/*
|--------------------------------------------------------------------------
| EDIT ARTIKEL ADMIN
|--------------------------------------------------------------------------
*/

$router->get(
    '/admin/article/{id}/edit',
    ['AdminController', 'edit']
);

$router->post(
    '/admin/article/{id}/update',
    ['AdminController', 'processEdit']
);


/*
|--------------------------------------------------------------------------
| HAPUS ARTIKEL ADMIN
|--------------------------------------------------------------------------
*/

$router->post(
    '/admin/article/{id}/delete',
    ['AdminController', 'delete']
);

/*
|--------------------------------------------------------------------------
| KELOLA PENULIS & MITRA
|--------------------------------------------------------------------------
*/

$router->get(
    '/admin/clients',
    ['AdminController', 'clients']
);

$router->get(
    '/admin/client/create',
    ['AdminController', 'clientCreate']
);

$router->post(
    '/admin/client/store',
    ['AdminController', 'clientStore']
);

$router->get(
    '/admin/client/{id}/edit',
    ['AdminController', 'clientEdit']
);

$router->post(
    '/admin/client/{id}/update',
    ['AdminController', 'clientUpdate']
);

$router->post(
    '/admin/client/{id}/delete',
    ['AdminController', 'clientDelete']
);

/*
|--------------------------------------------------------------------------
| CLIENT / PENULIS
|--------------------------------------------------------------------------
*/

$router->get(
    '/admin/clients',
    ['AdminController', 'clients']
);

$router->get(
    '/admin/client/create',
    ['AdminController', 'clientCreate']
);

$router->post(
    '/admin/client/store',
    ['AdminController', 'clientStore']
);

$router->get(
    '/admin/client/{id}/edit',
    ['AdminController', 'clientEdit']
);

$router->post(
    '/admin/client/{id}/update',
    ['AdminController', 'clientUpdate']
);

$router->post(
    '/admin/client/{id}/delete',
    ['AdminController', 'clientDelete']
);

/*
|--------------------------------------------------------------------------
| KELOLA KATEGORI BERITA
|--------------------------------------------------------------------------
*/

$router->get(
    '/admin/categories',
    ['AdminController', 'categories']
);

$router->get(
    '/admin/category/create',
    ['AdminController', 'categoryCreate']
);

$router->post(
    '/admin/category/store',
    ['AdminController', 'categoryStore']
);

$router->get(
    '/admin/category/{id}/edit',
    ['AdminController', 'categoryEdit']
);

$router->post(
    '/admin/category/{id}/update',
    ['AdminController', 'categoryUpdate']
);

$router->post(
    '/admin/category/{id}/delete',
    ['AdminController', 'categoryDelete']
);


// TAMBAHKAN ROUTE IKLAN INI DI index.php

$router->get('/admin/ads', ['AdminController', 'ads']);
$router->get('/admin/ads/create', ['AdminController', 'adCreate']);
$router->post('/admin/ads/store', ['AdminController', 'adStore']);
$router->get('/admin/ads/{id}/edit', ['AdminController', 'adEdit']);
$router->post('/admin/ads/{id}/update', ['AdminController', 'adUpdate']);
$router->post('/admin/ads/{id}/toggle', ['AdminController', 'adToggle']);
$router->post('/admin/ads/{id}/delete', ['AdminController', 'adDelete']);

/*
|--------------------------------------------------------------------------
| DISPATCH
|--------------------------------------------------------------------------
*/

$router->dispatch(
    $_SERVER['REQUEST_URI'],
    $_SERVER['REQUEST_METHOD']
);