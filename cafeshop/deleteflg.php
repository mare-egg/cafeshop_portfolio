<?php
/*
 * ファイル名:deleteflg.php
 * アクセスURL:http://localhost/SC/cafeshop/deleteflg.php
 * 
 * 会員情報を論理削除する処理
 * 
 * 成功したらログイン画面へ
 * 失敗したらマイページへ戻す
 */

namespace cafeshop;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\Bootstrap;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Session;
use cafeshop\lib\Mypage;

$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$ses = new Session($db);
$mypage = new Mypage($db);

$mem_id = $_SESSION['mem_id'];
//$mem_idの会員情報を論理削除する処理
//delete_flg を 0から1にアップデートする
//loginする時に条件としてdelete_flg = 0とする
//loginする時にdelete_flg = 1の場合はログインできない
$date = date("Y-m-d H:i:s");
$res = $mypage->delMemberData($mem_id,$date);

if ($res === true) {
  $_SESSION = array();
  session_destroy();
  header("Location: login.php");
  exit;
} else {
  header("Location: mypage.php");
}