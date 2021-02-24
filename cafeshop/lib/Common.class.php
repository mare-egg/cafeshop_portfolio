<?php
/*
 * ファイル名:Common.class.php
 * 会員登録のバリデーション
 * ・新規会員登録/マイページ
 */
namespace cafeshop\lib;

class Common
{
  private $dataArr = [];

  private $errArr = [];

  //初期化
  public function __construct()
  {
  }

  public function errorCheck($dataArr)
  {
    $this->dataArr = $dataArr;

    $this->createErrorMessage();
    $this->familyNameCheck();
    $this->firstNameCheck();
    $this->familyNameKanaCheck();
    $this->firstNameKanaCheck();
    $this->userNameCheck();
    $this->passwordCheck();
    $this->sexCheck();
    $this->birthCheck();
    $this->zipCheck();
    $this->addCheck();
    $this->telCheck();
    $this->mailCheck();
    $this->cueCheck();

    return $this->errArr;
  }

  public function mypageerrorCheck($updateArr)
  {
    $this->dataArr = $updateArr;

    $this->createErrorMessage();
    $this->familyNameCheck();
    $this->firstNameCheck();
    $this->familyNameKanaCheck();
    $this->firstNameKanaCheck();
    $this->userNameCheck();
    $this->sexCheck();
    $this->birthCheck();
    $this->zipCheck();
    $this->addCheck();
    $this->telCheck();
    $this->mailCheck();

    return $this->errArr;
  }

  private function createErrorMessage()
  {
    foreach($this->dataArr as $key => $val) {
      //キーは同じもの、中身はからのものを複製している
      $this->errArr[$key] = '';
    }
  }

  private function familyNameCheck()
  {
    if($this->dataArr['family_name'] === '') {
      $this->errArr['family_name'] = 'お名前(氏)を入力してください';
    } elseif (!preg_match("/^[a-zA-Zぁ-んァ-ヶー一-龠]+$/u", $this->dataArr['family_name'])) {
      $this->errArr['family_name'] = '半角英字・ひらがな・カタカナ・漢字のみ使用してください';
    }
  }

  private function firstNameCheck()
  {
    if($this->dataArr['first_name'] === '') {
      $this->errArr['first_name'] = 'お名前(名)を入力してください';
    } elseif (!preg_match("/^[a-zA-Zぁ-んァ-ヶー一-龠]+$/u", $this->dataArr['family_name'])) {
      $this->errArr['family_name'] = '半角英字・ひらがな・カタカナ・漢字のみ使用してください';
    }
  }

  private function familyNameKanaCheck()
  {
    if($this->dataArr['family_name_kana'] === '') {
      $this->errArr['family_name_kana'] = 'おなまえ(氏)を入力してください';
    } elseif (!preg_match("/^[a-zA-Zぁ-ん]+$/u", $this->dataArr['family_name_kana'])) {
      $this->errArr['family_name_kana'] = '半角英ローマ字・ひらがなのみ使用してください';
    }
  }

  private function firstNameKanaCheck()
  {
    if($this->dataArr['first_name_kana'] === '') {
      $this->errArr['first_name_kana'] = 'おなまえ(名)を入力してください';
    } elseif (!preg_match("/^[a-zA-Zぁ-ん]+$/u", $this->dataArr['first_name_kana'])) {
      $this->errArr['first_name_kana'] = '半角英ローマ字・ひらがなのみ使用してください';
    }
  }

  private function userNameCheck()
  {
    /* 入力されたIDが空か、もしくは適切でないかをチェック */
    if($this->dataArr['user_name'] === '') {
      $this->errArr['user_name'] = 'ユーザー名を入力してください';
    } elseif (!preg_match("/^[a-zA-Z0-9ぁ-んァ-ヶー一-龠]+$/u", $this->dataArr['user_name'])) {
      $this->errArr['user_name'] = '半角英数字・ひらがな・カタカナ・漢字のみ使用可能です'; 
    
      /* 入力されたIDの長さが3文字以上、16文字以下かどうかをチェック*/
    } elseif(mb_strlen($this->dataArr['user_name']) < 3) {
      $this->errArr['user_name'] = 'ユーザー名は3文字以上で設定してください';
    } elseif (mb_strlen($this->dataArr['user_name']) > 16) {
      $this->errArr['user_name'] = 'ユーザー名が長すぎます。16文字以下で設定してください';
    }
  }

  private function passwordCheck()
  {
    if($this->dataArr['password'] === '') {
      $this->errArr['password'] = 'パスワードを入力してください';
    } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $this->dataArr['password'])) {
      $this->errArr['password'] = '半角英数字のみ使用してください';
    } 
    
    if(mb_strlen($this->dataArr['password']) < 6) {
      $this->errArr['password'] = 'パスワードは6文字以上で設定してください';
    } elseif (mb_strlen($this->dataArr['password']) > 32) {
      $this->errArr['password'] = 'パスワードが長すぎます。32文字以下で設定してください';
    }
  }

  private function sexCheck()
  {
    if($this->dataArr['sex'] === '') {
      $this->errArr['sex'] = '性別を選択してください';
    }
  }

  private function birthCheck()
  {
    if($this->dataArr['year'] === '') {
      $this->errArr['year'] = '生年月日の年を選択してください';
    }
    if($this->dataArr['month'] === '') {
      $this->errArr['month'] = '生年月日の月を選択してください';
    }
    if($this->dataArr['day'] === '') {
      $this->errArr['day'] = '生年月日の日を選択してください';
    }

    if(checkdate($this->dataArr['month'], $this->dataArr['day'],
    $this->dataArr['year']) === false) {
      $this->errArr['year'] = '正しい日付を入力してください';
    }

    //日付をUnixタイムスタンプに変換した内容を取得
    //生年月日ー現在の日付が0より大きかった時
    //(今より未来を入力していた場合)
    if(strtotime($this->dataArr['year']. '-' . $this->dataArr['month'] . '-' . 
    $this->dataArr['day']) - strtotime('now') > 0) {
      $this->errArr['year'] = '正しい日付を入力してください';
    }
  }

  private function zipCheck()
  {
    if(preg_match('/^[0-9]{3}$/', $this->dataArr['zip1']) === 0) {
      $this->errArr['zip1'] = '郵便番号の左は半角数字3桁で入力してください';
    }
    if(preg_match('/^[0-9]{4}$/', $this->dataArr['zip2']) === 0) {
      $this->errArr['zip2'] = '右は半角数字4桁で入力してください';
    }
  }

  private function addCheck()
  {
    if($this->dataArr['address'] === '') {
      $this->errArr['address'] = '住所を入力してください';
    } elseif (!preg_match("/^[a-zA-Z0-9ぁ-んー一-龠]+$/u", $this->dataArr['address'])) {
      $this->errArr['address'] = '半角英数字・ひらがな・漢字のみ使用可能です';
    }
  }

  private function mailCheck()
  {
    if( filter_var( $this->dataArr['email'], FILTER_VALIDATE_EMAIL ) ){
      //正しい形式のメールアドレス
    }else{
      $this->errArr['email'] = 'メールアドレスを正しい形式で入力してください';
    }

  }

  private function telCheck()
  {
    if(preg_match('/^\d{1,6}$/', $this->dataArr['tel1']) === 0 || 
      preg_match('/^\d{1,6}$/', $this->dataArr['tel2']) === 0 || 
      preg_match('/^\d{1,6}$/', $this->dataArr['tel3']) === 0 || 
      strlen($this->dataArr['tel1'] . $this->dataArr['tel2'] . 
    $this->dataArr['tel3']) >= 12) {
      $this->errArr['tel1'] = '電話番号は、半角数字で11桁以内で入力してください';
    }
  }
  private function cueCheck()
  {
    if($this->dataArr['cue'] === []) {
      $this->errArr['cue'] = '最低1つ選択してください';
    }
  }

  public function getErrorFlg()
  {
    $err_check = true;
    foreach($this->errArr as $key => $value) {
      if($value !== '') {
        $err_check = false;
      }
    }
    return $err_check;
  }
}