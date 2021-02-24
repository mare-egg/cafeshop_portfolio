<?php
/**
 * ファイル名：cart.php
 * カートページの処理を制御するController
 * アクセスURL：http://localhost/SC/cafeshop/cart.php
 */
namespace cafeshop;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\Bootstrap;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Session;
use cafeshop\lib\Item;
use cafeshop\lib\Cart;

$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$ses = new Session($db);
$itm = new Item($db);
$cart = new Cart($db);

if (!isset($_SESSION['user_name'])) {
  header("Location: login.php");
  exit;
}

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);
$context = [];

$ses->checkSession();
$customer_no = $_SESSION['customer_no'];


$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/',$_GET['item_id']) === 1) ? $_GET['item_id'] : '';
$num_id = (isset($_GET['num_id']) === true && preg_match('/^\d+$/',$_GET['num_id']) >= 0) ? $_GET['num_id'] : '1' ;
$crt_id = (isset($_GET['crt_id']) === true && preg_match('/^\d+$/',$_GET['crt_id']) === 1) ? $_GET['crt_id'] : '';

$little = '';

if($item_id !== '') {
  /*[カートに追加する際に同じ商品がカート内にある場合のダブリ防止]
    ・カートに入れる前に入れる商品のデータがカート内にあるか確認
    ・もし同じ商品でデータが取得できた場合は数量のみカウントし、
    上書きした後にカートへ入れる
    ・取得したカート内はダブリ防止目的で取得したデータなので削除
  */
  
  //カートに入れた商品と同じものがカート内にあればtrue
  $mixdata = $cart->startgetCartData($customer_no,$item_id);
  $stock = $mixdata[0]["stock"];
  $mixcrt_id = $mixdata[0]["crt_id"];
  
  //入れる商品と同じ商品がカート内にある場合
  if(isset($mixdata) === true ){
    $num = $mixdata[0]["num"];
    $mixcrt_id = $mixdata[0]["crt_id"];
    $stock = $mixdata[0]["stock"];
    $item_name = $mixdata[0]["item_name"];

    //数量だけを更新する
    $num_id = $num_id + $num;

    //もし在庫数より多く入れていた場合在庫数で上書きし、
    //在庫数より多くは入れられないように制限
    if($num_id >= $stock) {
      $num_id = $stock;
    }
    
    //取得してきたSQLを丸々削除する処理
    $mixdata = $cart->delCartData($mixcrt_id);
  }

  //商品の詳細ページで在庫が0の場合、SOLDOUT表示
  //カートに入れるボタンを非表示にしているが、念のため下記

  //在庫数が0ではない商品の場合にカートに入れる
  if($stock !== '0'){
    $res = $cart->insCartData($customer_no,$item_id,$num_id);
  } else {
    $res = false;
  }
  /*
  -----------------------------------------
  GETで飛んできた商品をカートに入れた後
  headerを用いてcart.phpへ遷移させることで
  リロードしても商品が追加されなくなる
  -----------------------------------------
  */
  header("Location:http://localhost/SC/cafeshop/cart.php");
  if($res === false) {
    exit();
  }
}

//crt_idが設定されていれば、削除する
if($crt_id !== '') {
  $data = $cart->delCartData($crt_id);
  // unset($_GET['crt_id']);
}

//カート情報を取得する
$dataArr = $cart->getCartData($customer_no);

//全商品数、全商品合計、(item_id単体ごとの合計金額)
list($sumNum,$sumPrice) = $cart->getItemAndSumPrice($customer_no);

$context['sumNum'] = $sumNum;
$context['sumPrice'] = $sumPrice;
$context['dataArr'] = $dataArr;
$context['little'] = $little;
$template = $twig->loadTemplate('cart.html.twig');
$template->display($context);