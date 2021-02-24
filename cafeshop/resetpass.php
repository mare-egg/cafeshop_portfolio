<?php
/**
 * ファイル名:resetpass.php
 * アクセスURL:http://localhost/SC/cafeshop/resetpass.php
 * パスワード再設定メールアドレス入力画面
 * 1,メールアドレスの入力フォーム
 * 2,メールアドレスのバリデーションチェック
 * 3,メールアドレスがDBに登録されているかチェック
 * 4,登録されている場合、会員IDを取得し、
 * ランダム値トークン、現在日時に＋30分した日時（有効時間）、会員IDをDB登録
 * 5,再設定ページURLにトークンをGETでくっつけたパラメータを送信
 * 6,各自通知を表示
*/

namespace cafeshop;

use PHPMailer\PHPMailer\PHPMailer;

require_once './contact/phpmailer/Exception.php';
require_once './contact/phpmailer/PHPMailer.php';
require_once './contact/phpmailer/SMTP.php';

$mailer = new PHPMailer(true);

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\Bootstrap;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Session;
use cafeshop\lib\Reset;

$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$ses = new Session($db);
$reset = new Reset($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);
$context = [];

$error = '';
$message = '';
$message = "恐れ入りますが、登録されたメールアドレスをご入力いただき、受診されたメールの案内に従ってパスワードの再設定をお願いいたします。";


if(isset($_POST['mail']) === true) {
  $mail = $_POST['mail'];
  if($mail !== "") {
    //入力欄に値が入っている時
    $context['mail'] = $mail;
    $res = $reset->resetMailCheck($mail);
    if($res === true){
      //メールの入力が正しい
      //そのメールアドレスがDB.member内にあるかチェック
      
      $data = $reset->searchEmail($mail);
      
      if(!empty($data) === true) {
        $mem_id = $data[0]['mem_id'];
        $email = $data[0]['email'];
        if($mail === $email) {
          //ここで一致しているのでメール送信を実装する
          
          //送信先
          $to = $mail;
          
          //件名
          $subject = 'パスワード再設定';
          
          //ランダム値のトークンを生成する
          $passResetToken = md5(uniqid(rand(),true));
          
          //現在時刻を取得し、＋30分追加する（有効時間のリミット）
          $date = date("Y-m-d H:i:s",strtotime("+30 minute"));
          $insDate = $reset->insertreset($passResetToken,$date,$mem_id);
          
          //本文：ランダム値をGETでくっつけたパラメータURL
          $link = "http://localhost/SC/cafeshop/resetpassconfirm.php?passReset=$passResetToken"; //URLを渡す
          
          //会員情報のユーザー名
          $name = $data[0]['user_name'];
 
          //メールを送信する内容
          try{
            $mailer->isSMTP();
            $mailer->Host = 'smtp.gmail.com';
            $mailer->SMTPAuth = true;
            $mailer->Username = 'roa.caferia@gmail.com';
            $mailer->Password = 'roapassword';
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailer->Port = '587';
            
            $mailer->setFrom($to);
            $mailer->addAddress($to);
            
            $mailer->isHTML(true);
            $mailer->Subject = 'Roa Caferia (resetpassword)';
            $mailer->Body = "<h4>件名 : $subject <br> $name さんこんにちわ<br><br>以下のリンク先からパスワード再設定をしてください。リンク有効リミットは30分となります。<br>:$link</h4>";
            
            $mailer->send();
          } catch (Exception $e){
            $alert = $e->getMessage() ;
          }
          
          if($res === true){
            //メール送信が実行された場合
            $message = '入力いただいたメールアドレスにパスワード再設定URLをお送りしました。30分以内にパスワード再設定をお願いいたします';
            $context['message'] = $message;
          } else {
            $message = 'ただいま回線が混み合っております。恐れ入りますが、もう一度やり直してください。';
            $context['error'] = $error;
          }
        }
      } else {
        $error = '入力されたメールアドレスは登録されておりません';
        $context['error'] = $error;
      }
    } else {
      $error = '正しい形式でメールアドレスを入力してください';
      $context['error'] = $error;
    }
  } else {
    $error = 'メールアドレスを入力してください';
    $context['error'] = $error;
  }
}

$ses->checkSession();
$context['message'] = $message;
$context['error'] = $error;
$template = $twig->loadTemplate('resetpass.html.twig');
$template->display($context);