<?php
/**
 * お問い合わせ内容内容と受信メールアドレス設定
 * 受信先は店舗側のメールアドレスにしたいアドレスを設定をする
 * 必要があります
 * 
 * 自身で設定する必要がある箇所は下記の2つ
 * 1.メールアドレス：店舗用@gmail.com
 * 2.パスワード    ：上記メールアドレスのパスワード
 * 
 *・PHPMailerライブラリ使用
 */
use PHPMailer\PHPMailer\PHPMailer;

require_once 'phpmailer/Exception.php';
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';

$mail = new PHPMailer(true);

$alert = '';

if(isset($_POST['submit'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $tel = $_POST['tel'];
  $subject = $_POST['subject'];
  $message = $_POST['message'];

  try{
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->CharSet ='UTF-8';
    $mail->SMTPAuth = true;
    $mail->Username = '***@gmail.com';/*店舗用メールアドレス */
    $mail->Password = '';/*上記メールアドレスのパスワード */
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = '587';

    $mail->setFrom('***@gmail.com');/*店舗用メールアドレス */
    $mail->addAddress('***@gmail.com');/*店舗用メールアドレス */

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = "<h3>氏名 : $name さん<br>メールアドレス: $email <br>TEL : $tel <br><br>お問い合わせ内容(本文)<br>>>$message</h3>";

    $mail->send();
    $alert = "お問い合わせありがとうございます";
  } catch (Exception $e){
    $alert = $e->getMessage() ;
  }
}