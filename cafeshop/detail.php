<?php
/* 
 * ファイル名:detail.php
 * 商品詳細を表示するプログラム
 * アクセスURL:http://localhost/SC/cafeshop/detail.php
 * 
 */
namespace cafeshop;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\Bootstrap;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Session;
use cafeshop\lib\Item;
use cafeshop\lib\Review;
use cafeshop\lib\Cart;

$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$ses = new Session($db);
$itm = new Item($db);
$review = new Review($db);
$cart = new Cart($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$error = '';
$message = '';
$comment = ''; 
$soldout = '';
$little = '';

$ses->checkSession();
$customer_no = $_SESSION['customer_no'];

//item_idを取得する
$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/', $_GET['item_id']) === 1) ? $_GET['item_id'] : '';

// item_idが取得できていない場合、商品一覧へ遷移させる
if ($item_id === '') {
  header('Location:' . Bootstrap::ENTRY_URL. 'list.php');
}

$username = $_SESSION['user_name'];
$mem_id = $_SESSION['mem_id'];

//カートアイコンにカート内の商品が0なら非表示、あるなら商品数を
//アイコンに表示させるための数字を取得する
$dataArr = $cart->getCartData($customer_no);
$num = count($dataArr);

//口コミ投稿ボタンが押された時
if ($_POST) {
  // 必須項目に情報が入っているかを確認する
  if ( !empty( $_POST['add_review'] )) {
    $add_review = $_POST['add_review'];
    //口コミ投稿した内容をDBにインサートする
    $review_text = $review->add_review($add_review,$item_id,$mem_id);
    if(!$review_text) {
      $error = 'エラーが発生しました。';
    } else {
      $message = "口コミを投稿しました。";
    }
  } else {
    $error = "口コミを入力してください";
  }
}

$cateArr = $itm->getCategoryList();
$itemData = $itm->getItemDetailData($item_id);


//在庫数によってメッセージを表示させる分岐
$itemStock = $itemData[0]["stock"];
if($itemStock > 6 ) {
  $little = '';
} elseif ($itemStock === '0') {
  $soldout = 'SOUD OUT';
} elseif ($itemStock < 6) {
  $little = '残りわずかです';
}

$context = [];

//商品IDごとの口コミレビューを取得
$reviews_data = $review->selectreview($item_id);

//口コミレビューがある場合
if(!empty($reviews_data)) {
  $context['reviews_data'] = $reviews_data;
} else {
  //口コミレビューがない場合
  $comment = 'まだ商品に関する口コミはありません';
  $context['comment'] = $comment;
}

$context['error'] = $error;
$context['message'] = $message;
$context['username'] = $username;
$context['cateArr'] = $cateArr;
$context['itemData'] = $itemData[0];
$context['soldout'] = $soldout;
$context['little'] = $little;
$context['num'] = $num;

$template = $twig->loadTemplate('detail.html.twig');
$template->display($context);