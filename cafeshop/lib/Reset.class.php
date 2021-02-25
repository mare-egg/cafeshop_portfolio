<?php
/*
 * ファイル名：Reset.class.php
 * パスワード再設定に関するプログラムのクラスファイル
 */
namespace cafeshop\lib;

class Reset
{
  public $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  //再設定リンク送信先のメールアドレスのバリデーション
  public function resetMailCheck($mail)
  {
    $res = false;
    //"/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";

    //メールのバリデーションはfilter_varの方が確実のため採用
    if( filter_var( $mail, FILTER_VALIDATE_EMAIL ) ){
      //正しい形式のメールアドレス
      $res = true;
    }
    return $res;
  }


  //$mailが会員登録済みリストにちゃんとあるかをチェック
  public function searchEmail($mail)
  {
    $table = ' member ';
    $col = ' mem_id, email, user_name ';
    $where = ($mail !== '') ? ' email = ? AND delete_flg = 0 ' : '';
    $arrVal = ($mail !== '') ? [$mail] : [];

    $res = $this->db->select($table,$col,$where,$arrVal);

    //SELECT email FROM member where email = 'aaa@gmail.com' AND delete_flg = 0
    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  
  //ランダムトークンと現在日時＋30分した日時を
  //passresetテーブルに登録する
  public function insertreset($token,$date,$mem_id)
  {
    $table = ' passreset ';
    $insData = [
      'token' => $token,
      'date' => $date,
      'mem_id' => $mem_id
    ];
    return $this->db->insert($table, $insData);
  }

  //passresetテーブルに同じtokenがある場合に全て取得する
  public function selectreset($token)
  {
    $table = ' passreset ';
    $column = '';
    $where = ($token !== '') ? ' token = ? ' : 'false';
    $arrVal = ($token !== '') ? [$token] : [];

    return $this->db->select($table,$column,$where,$arrVal);
  }

  //mem_idが一致する会員情報のパスワードをアップデートする
  public function updatepassword($password,$mem_id,$date)
  {
    $table = ' member ';
    $password = password_hash($password, PASSWORD_DEFAULT); 
    $insData = [
      'password' => $password,
      'update_date' => $date,
    ];
    $where = ' mem_id = ?';
    $arrWhereVal = [$mem_id];

    return $this->db->update($table,$insData,$where,$arrWhereVal);
  }

  //有効リミットが過ぎている無効URLの場合に削除処理
  public function deleterestpass($reset_id)
  {
    $table = ' passreset ';
    $where = ($reset_id !== '') ? ' reset_id = ? ' : 'false';
    $arrVal = ($reset_id !== '') ? [$reset_id] : [];

    return $this->db->deleterestpass($table,$where,$arrVal);
  }
}