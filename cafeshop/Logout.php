<?php
//http://localhost/SC/cafeshop/logout.php
session_start();

require_once dirname(__FILE__) . '/Bootstrap.class.php';
use cafeshop\Bootstrap;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

if (isset($_SESSION["user_name"])) {
    $errorMessage = "ログアウトしました。";
} else {
    $errorMessage = "セッションがタイムアウトしました。";
}

// セッションの変数のクリア
$_SESSION = array();

// セッションクリア
session_destroy();

$context = [];
$context['logout'] = htmlspecialchars($errorMessage, ENT_QUOTES);
$template = $twig->loadTemplate('login.html.twig');
$template->display($context);