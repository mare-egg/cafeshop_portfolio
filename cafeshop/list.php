<?php
/**
 * ファイル名:list.php
 * 商品一覧を表示するプログラム
 * アクセスURL：http://localhost/SC/cafeshop/list.php
 */
namespace cafeshop;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\Bootstrap;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Session;
use cafeshop\lib\Item;

$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$ses = new Session($db);
$itm = new Item($db);

//セッションにユーザー名がなければログイン画面へ遷移
//あればユーザー名を取得
if (!isset($_SESSION['user_name'])) {
  header("Location: login.php");
  exit;
} else {
  $username = $_SESSION['user_name'];
}

$error = '';
$message = '';

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);
$context = [];

//キーワード検索実行時
if(isset($_GET['title']) === true) {
  
  if($_GET['title'] !== ''){
    //検索文字の空白を除去する
    $title = trim($_GET['title']);
    $context['title'] = $title;
    
    //検索文字で商品名を探す処理
    $searchArr = $itm->selectItemData($title);

    if(empty($searchArr) !== true) {
      //検索が正しく処理された場合

      //検索結果のデータが何件取得されたか
      $message = count($searchArr) . '件見つかりました。';
      $context['searchArr'] = $searchArr;
    } else {
      $error = '検索対象は見つかりませんでした。';
    }
  } else {
    $error = '商品名を入力してください';
  }
}

//SessionKeyを見て、DBへの登録状態をチェックする
$ses->checkSession();

$ctg_id = (isset($_GET['ctg_id']) === true && preg_match('/^[0-9]+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';

//カテゴリーリスト（一覧）を取得する
$cateArr = $itm->getCategoryList();

//商品総数の数を取得
$total_count = $itm->getItemcount($ctg_id);

//1ページごとに表示させる件数を定義
$max_view = 14;

$pages = ceil($total_count / $max_view);


//現在いるページのページ番号を取得
if(!isset($_GET['page_id'])){ 
  $now = 1;
}else{
  $now = $_GET['page_id'];
}

//商品リストを取得する(旧)
// $dataArr = $itm->getItemdata($ctg_id);

//ページごと、カテゴリーごとに対応して商品リストを取得する
$dataArr = $itm->getItemList($ctg_id,$max_view,$now);

$context['error'] = $error;
$context['username'] = $username;
$context['message'] = $message;
$context['cateArr'] = $cateArr;
$context['dataArr'] = $dataArr;
$context['pages'] = $pages;
$context['now'] = $now;
$context['ctg'] = $ctg_id;
$template = $twig->loadTemplate('list.html.twig');
$template->display($context);