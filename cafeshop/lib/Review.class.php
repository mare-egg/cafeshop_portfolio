<?php
/**
 * ファイル名：Review.class.php
 * 商品の口コミに関するプログラムのクラスファイル)
 * 投稿/投稿表示
 */
namespace cafeshop\lib;

class Review
{
  public $cateArr = [];
  public $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  //商品IDによって口コミされたデータをDESC順で表示する処理
  function selectreview($item_id) {

    $table = ' reviews ';
    $col = ' reviews.review_comment, reviews.review_date, reviews.review_item_id, reviews.review_user_id, member.mem_id, member.user_name';
    $where = ($item_id !== '') ? ' reviews.review_item_id = ? ' : '';
    $arrVal = ($item_id !== '') ? [$item_id] : [];
    $leftjoin = ' member ';
    $this->db->setLeftJoin($leftjoin);
    $on = ' reviews.review_user_id = member.mem_id ';
    $this->db->setOn($on);
    $order = 'reviews.review_date DESC';
    $this->db->setOrder($order);

    $res = $this->db->selectReview($table,$col,$where,$arrVal);

    return $res;
  }

  //口コミを投稿するインサート処理
  function add_review($add_review = [],$item_id,$mem_id) {
    $sql = "INSERT INTO
            reviews(
              review_comment,
              review_date,
              review_item_id,
              review_user_id
            )
          VALUES (
            '$add_review',
            NOW(),
            $item_id,
            $mem_id
          )";
    $result = $this->db->addreview($sql,$add_review=[],$item_id,$mem_id);
    return $result;
  }
}