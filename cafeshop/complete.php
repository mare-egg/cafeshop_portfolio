<?php
/*
 * ファイル名:complete.php
 * アクセスURL:http://localhost/SC/cafeshop/complete.php
 * 会員登録確認後にログイン画面にメッセージ表示する
 * 
 * $context['complete']をログイン画面に送る目的のために
 * 存在するファイルです。
 */
namespace cafeshop;
session_start();

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\Bootstrap;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$_SESSION = array();

// セッションクリア
session_destroy();
$complete = '会員登録が完了しました。';
$template = $twig->loadTemplate('login.html.twig');

$context = [];
$context['complete'] = $complete;
//何もdisplayで移すものがない場合は必ずからの配列を入れる
// $template->display([]);
$template->display($context);