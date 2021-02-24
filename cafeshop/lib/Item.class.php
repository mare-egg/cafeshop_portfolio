<?php
/**
 * ファイル名：Item.class.php
 * 商品に関するプログラムのクラスファイル
 * ・商品リスト/カテゴリーリスト/商品検索
 * ・ページネーション
 */
namespace cafeshop\lib;

class Item
{
  public $cateArr = [];
  public $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }
  //カテゴリーリストの取得
  public function getCategoryList()
  {
    $table = ' category ';
    $col = ' ctg_id, category_name ';

    $res = $this->db->select($table, $col);
    return $res;
  }

  //商品リストを取得する
  public function getItemdata($ctg_id)
  {
    //カテゴリーによって表示させる商品をかえる
    $table = ' item ';
    $col = ' item_id, item_name, price, image, ctg_id ';
    $where = ($ctg_id !== '') ? ' ctg_id = ? ' : '';
    $arrVal = ($ctg_id !== '') ? [$ctg_id] : [];

    $res = $this->db->select($table,$col,$where,$arrVal);
    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  //ページネーション
  //カテゴリーによる表示&現在のページによる表示
  public function getItemList($ctg_id,$max_view,$now)
  {
    $table = ' item ';
    $col = ' item_id, item_name, price, image, ctg_id ';
    $where = ($ctg_id !== '') ? ' ctg_id = :ctg_id ' : '';

    //カテゴリーを選択された場合
    //ある場合はプレースフォルダは3つ
    if($ctg_id !== '') {

      //カテゴリー選択&ページが1の時
      if($now == 1) {
        $now = $now - 1;
        $arrVal = [$ctg_id,$max_view,$now];
      } else {
      //カテゴリー選択&ページが1以外の時
        $now = ($now-1) * $max_view;
        $arrVal = [$ctg_id,$max_view,$now];
      }
      //カテゴリー選択がされていない、通常時の表示
      //ない場合はプレースフォルダは2つ
    } else {
      //pageが1の時
      if($now == 1) {
        $now = $now - 1;
        $arrVal = [$max_view,$now];
      } else {
        //pageが1以外の時
        $now = ($now-1) * $max_view;
        $arrVal = [$max_view,$now];
      }
    }

    $res = $this->db->selectlist($table,$col,$where,$arrVal,$ctg_id,$max_view,$now);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  //ページネーション実装前のカテゴリー表示（現在未使用）
  //カテゴリーによって表示させるアイテムをかえる
  public function getItemcount($ctg_id)
  {
    $table = ' item ';
    $where = ($ctg_id !== '') ? ' ctg_id = ? ' : '';
    $arrVal = ($ctg_id !== '') ? [$ctg_id] : [];

    $res = $this->db->count($table, $where, $arrVal);
    return $res;
  }

  //商品の詳細情報を取得する
  public function getItemDetailData($item_id)
  {
    $table = ' item ';
    $col = ' item_id, item_name, detail, price, image, ctg_id, stock ';

    $where = ($item_id !== '') ? ' item_id = ? ' : '';
    $arrVal = ($item_id !== '') ? [$item_id] : [];

    $res = $this->db->select($table, $col, $where, $arrVal);
    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  //検索によるデータ取得
  public function selectItemData($title)
  {
    $table = ' item ';
    $col = '';
    $where = ($title !== '') ? " item_name LIKE '%{$title}%'" : '';
    $arrVal = ($title !== '') ? [$title] : [];

    $res = $this->db->select($table,$col,$where,$arrVal);
    return $res;
  }
}
