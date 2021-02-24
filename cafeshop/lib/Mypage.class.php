<?php
/**
 * ファイル名：Mypage.class.php
 * マイページに関するプログラムのクラスファイル
 * ・会員登録情報の表示/退会
 */
namespace cafeshop\lib;

class Mypage
{
  private $db = null;

  public function __construct($db = null)
  {
    $this->db = $db;
  }

  //Mypageに表示させる内容
  //登録した際に入力した必須の項目をSELECTする
  public function selectMember($mem_id)
  {
    $table = ' member ';
    $col = ' mem_id, family_name, first_name, family_name_kana, first_name_kana, user_name, sex, year, month, day, zip1, zip2, address, email, tel1, tel2, tel3 ';

    $where = ($mem_id !== '') ? ' mem_id = ? ' : '';
    $arrVal = ($mem_id !== '') ? [$mem_id] : [];

    $res = $this->db->select($table, $col, $where, $arrVal);


    return ($res !== false && count($res) !== 0) ? $res : false;
  }


  public function selectConfirmCart($mem_id)
  {
    $table = ' member ';
    $col = ' family_name, first_name, family_name_kana, first_name_kana, zip1, zip2, address, email, tel1, tel2, tel3 ';

    $where = ($mem_id !== '') ? ' mem_id = ? ' : 'false';
    $arrVal = ($mem_id !== '') ? [$mem_id] : [];

    $res = $this->db->select($table, $col, $where, $arrVal);
    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  public function delMemberData($mem_id,$date)
  {
    $table = ' member ';
    $insData = [
      'delete_date' => $date,
      'delete_flg' => 1,
    ];
    $where = ' mem_id = ? ';
    $arrWhereVal = [$mem_id];
    return $this->db->update($table,$insData,$where,$arrWhereVal);
  }
}