<?php
/**
 * 注文確定
 * アクセスURL:http://localhost/SC/cafeshop/orderdetermine.php
 */
namespace cafeshop;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\Bootstrap;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Session;
use cafeshop\lib\Cart;
use cafeshop\lib\Mypage;

$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$ses = new Session($db);
$cart = new Cart($db);
$mypage = new Mypage($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);
$context = [];
$message = '';
$err = '';

$ses->checkSession();
$customer_no = $_SESSION['customer_no'];

//カートの中身
//必要情報ピックアップして確認事項として表示
$dataArr = $cart->getCartData($customer_no);

//カートの中身の合計金額と合計数
list($sumNum,$sumPrice) = $cart->getItemAndSumPrice($customer_no);

//確定が押された時
if(isset($_POST['confirm']) && !empty($dataArr)) {
  $mem_id = $_SESSION['mem_id'];
  $memberdata = $mypage->selectConfirmCart($mem_id);
  //注文確定DBにインサートする
  //会員ID 注文日時 商品ID 商品数 商品の合計金額を
  //カート内にある商品数分ループしてインサート処理
  foreach($dataArr as $value => $key) {
    $a[] = $value;
    $b[] = $key;
  }
  $countnum = count($a);
  for($i = 0;$i < $countnum;$i++) {
    $date = date("Y-m-d H:i:s");
    $item_id = $b[$i]["item_id"];
    $num = $b[$i]["num"];
    $singlesumprice = $b[$i]["singlePrice"];
    $res = $cart->insOrderConfirmed($mem_id,$date,$item_id,$num,$singlesumprice);
  }
  if($res === true) {
    $message = '注文確定しました。お届けまで今しばらくお待ちください';
    //注文確定時カートの中身を空にする。UPDATE
    //where customer_noに合致する全てのdelete_flgを1にする
    for($i = 0;$i < $countnum;$i++) {
      $crt_id = $b[$i]["crt_id"];
      $res = $cart->delCartData($crt_id);
    }
    
    if($res === true) {
      //正しくカートの中を空にできた場合

      //itemの在庫数をUPDATEする
      for($i = 0;$i < $countnum;$i++) {
        $item_id = $b[$i]["item_id"];
        $num = $b[$i]["num"];
        $stock = $b[$i]["stock"];
        $stock = $stock - $num;
        $res = $cart->updateStock($stock,$item_id);
      } 
      if($res === true) {
        //在庫数をUPDATE完了
      } else {
        $err = '在庫数UPDATEに失敗しました';
      }
    } else {
      $err = 'カート内の削除に失敗しました。';
    }
  } else {
    $err = '注文に失敗しました。';
  }
}
$context['message'] = $message;
$context['err'] = $err;
$template = $twig->loadTemplate('orderdetermine.html.twig');
$template->display($context);