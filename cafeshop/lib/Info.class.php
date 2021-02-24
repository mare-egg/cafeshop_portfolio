<?php
/**
 * ファイル名：Info.class.php
 * 会員情報に関するプログラムのクラスファイル
 * ・住所/郵便検索
 */
namespace cafeshop\lib;

class Info
{
  public $infoArr = [];
  public $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  //住所の取得
  public function getPostcodeList()
  {
    $table = ' postcode ';
    $col = ' pref, city, town ';

    $res = $this->db->select($table, $col);
    return $res;
  }

  //住所の取得 郵便番号によって取得する住所を変える
  public function getPostList($zip)
  {
    $table = ' postcode ';
    $col = 'pref, city, town ';
    $where = ($zip !== '') ? ' zip = ? ' : '';
    $arrVal = ($zip !== '') ? [$zip] : [];

    $res = $this->db->select($table,$col,$where,$arrVal);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }
}