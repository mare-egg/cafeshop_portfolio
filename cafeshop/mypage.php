<?php
/**
 * マイページ
 * ------------------------->
 * ：ユーザーアカウント：退会：更新
 * //http://localhost/SC/cafeshop/mypage.php
 * mypage.html.twig
 * ------------------------->
 */
namespace cafeshop;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\Bootstrap;
use cafeshop\lib\PDODatabase;
use cafeshop\master\initMaster;
use cafeshop\lib\Session;
use cafeshop\lib\Common;
use cafeshop\lib\Mypage;
use cafeshop\lib\Cart;

$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$ses = new Session($db);
$mypage = new Mypage($db);
$common = new Common();
$cart = new Cart($db);

if (!isset($_SESSION['user_name'])) {
  header("Location: login.php");
  exit;
} else {
  $username = $_SESSION['user_name'];
  $mem_id = $_SESSION['mem_id'];
}

$mypageArr = $mypage->selectMember($mem_id);
//エラー用メッセージの作成
$errArr = [
  'family_name' => '',
  'first_name' => '',
  'family_name_kana' => '',
  'first_name_kana' => '',
  'user_name' => '',
  'sex' => '',
  'year' => '',
  'month' => '',
  'day' => '',
  'zip1' => '',
  'zip2' => '',
  'address' => '',
  'email' => '',
  'tel1' => '',
  'tel2' => '',
  'tel3' => ''
];
foreach($errArr as $key => $value) {
  $errArr[$key] = '';
}

$template = 'mypage.html.twig';

$mode = '';
if (isset($_POST['confirm']) === true) {
  $mode = 'confirm';
}
if (isset($_POST['update']) === true) {
  $mode = 'update';
}


// ボタンのモードによって処理をかえる
switch ($mode) {
  //更新内容の確認
  case 'confirm' :

    unset($_POST['confirm']);

    $updateArr = $_POST;

    // この値を入れないでPOSTするとUndefinedとなるので未定義の場合は空白状態としてセットしておく
    if (isset($_POST['sex']) === false) {
      $updateArr['sex'] = "";
    }

    // エラーメッセージの配列作成
    $errArr = $common->mypageerrorCheck($updateArr);
    $err_check = $common->getErrorFlg();
  
    if($err_check === true) {
      $template = 'mypageupdate.html.twig';
      $mypageArr = $updateArr;
    }
    break;
    //会員情報を更新
    case 'update' :
      unset($_POST['update']);

      $date = date("Y-m-d H:i:s");
      $updateArr = array_merge($_POST,array('update_date' => $date));

      $mem_id = $_SESSION['mem_id'];
      $table = 'member';
      $where = ($mem_id !== '') ? ' mem_id = ? ' : '';
      $arrWhereVal = ($mem_id !== '') ? [$mem_id] : [];
      $date = date("Y-m-d H:i:s");

      $res = $db->update($table, $updateArr, $where, $arrWhereVal);
      
      if ($res === true) {
        // 更新成功時はショッピングホームへ遷移
        // 更新後のユーザー名をセッションに入れ直している処理
        $updateArr = $mypage->selectMember($mem_id);
        foreach($updateArr as $key){
          $key['user_name'];
        }
        $_SESSION['user_name'] = $key['user_name'];
        header('Location: ' . Bootstrap::ENTRY_URL . 'list.php');
        exit();
      } else {
        // 更新失敗時はマイページ画面に戻る
        $template = 'mypage.html.twig';

        foreach ($updateArr as $key => $value) {
            $errArr[$key] = '';
        }
    }
    break;
}

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);
$context = [];

list($yearArr, $monthArr, $dayArr) = initMaster::getDate();
$sexArr = initMaster::getSex();

//購入履歴
$purchase = $cart->selectpurchase($mem_id);

if(!empty($purchase)) {
  $context['purchasedata'] = $purchase;
} else {
  $comment = 'まだ購入履歴はありません';
  $context['comment'] = $comment;
}

$context['username'] = $username;
$context['mypageArr'] = $mypageArr;
$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;
$context['sexArr'] = $sexArr;
$context['errArr'] = $errArr;
$template = $twig->loadTemplate($template);
$template->display($context);