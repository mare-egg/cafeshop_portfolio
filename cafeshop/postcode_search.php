<?php
/*
 * ・郵便番号によるSQL文をInfoクラスに関数作成
 */
namespace cafeshop;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\lib\PDODatabase;
use cafeshop\Bootstrap;
use cafeshop\lib\Session;
use cafeshop\lib\Info;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$info = new Info($db);

//$_GET['']中の値は入力されたらcommon.jsからちゃんと値が入った状態になる
if(isset($_GET['zip1']) === true && isset($_GET['zip2']) === true) {

  //入力された住所を連結させる
  $zip1 = $_GET['zip1'];
  $zip2 = $_GET['zip2'];
  $postcode = $zip1 . $zip2;

  //呼び出したいSQL文をitemクラスから呼び出す
  $cateArr = $info->getPostcodeList();
  //入力された郵便番号によって住所を変更するSQL文を持ってくる
  $res = $info->getPostList($postcode);
  
  //検索対象がない場合$resはbool(false)を返す
  
  //出力結果がajaxに渡される
  //この場合のechoは画面に出力って意味ではなく、
  //echoすることでfunctionのデータの引数に渡される

  //郵便番号0000とすると”以下に掲載がない場合”と表示されるため
  //条件に特定の文字を含まない場合のみtrueとする
  //strops()か、preg_matchを使用
  if($res !== false ) {
    $string = $res[0]['town'];
    $find = '/以下に掲載がない場合/';
    if(preg_match($find,$string) === 0) {
      echo ($res !== "" && count($res) !== 0) ? $res[0]['pref'] . $res[0]['city'] . $res[0]['town'] : '';
    }
  }
} else {
  echo "no";
}