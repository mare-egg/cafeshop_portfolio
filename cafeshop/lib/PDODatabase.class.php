<?php
/*
 * ファイル名：PDODatabase.class.php
 * DB処理に関するプログラムのクラスファイル
 */
namespace cafeshop\lib;

class PDODatabase
{
  private $dbh = null;
  private $db_host = '';
  private $db_user = '';
  private $db_pass = '';
  private $db_name = '';
  private $db_type = '';
  private $order = '';
  private $limit = '';
  private $offset = '';
  private $groupby = '';
  private $leftjoin = '';
  private $on = '';

  public function __construct($db_host,$db_user,$db_pass,$db_name,$db_type)
  {
    $this->dbh = $this->connectDB($db_host, $db_user, $db_pass, $db_name, $db_type);
    $this->db_host = $db_host;
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
    $this->db_name = $db_name;

    $this->order = '';
    $this->limit = '';
    $this->offset = '';
    $this->groupby = '';
    $this->leftjoin = '';
    $this->on = '';
  }

  private function connectDB($db_host, $db_user, $db_pass, $db_name, $db_type)
  {
    //接続エラー発生→PDOExceptionオブジェクトがスローされる→例外処理をキャッチする
    try { 
      switch ($db_type) {
        case 'mysql':
          $dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name;
          $dbh = new \PDO($dsn, $db_user,$db_pass);
          $dbh->query('SET NAMES utf8');
          break;

        case 'pgsql':
          $dsn = 'pgsql:dbname=' . $db_name . 'host=' . $db_host . 'port=5432';
          $dbh = new \PDO($dsn,$db_user,$db_pass);
          break;
      }
    } catch (\PDOException $e) {
      //Mysqlに関するエラー内容
      var_dump($e->getMessage());
      exit();
    }

    return $dbh;
  }

  public function setQuery($query = '', $arrVal = [])
  {
    $stmt = $this->dbh->prepare($query);
    $stmt->execute($arrVal);
  }

  public function select($table,$column = '', $where = '', $arrVal = [])
  {

    $sql = $this->getSql('select',$table,$where,$column);

    $this->sqlLogInfo($sql,$arrVal);    
    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($arrVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    //データを連想配列に格納
    $data = [];
    while($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      array_push($data,$result);
    }

    return $data;
  }

  //カテゴリーごと・全商品のページごとに使い分ける処理
  public function selectlist($table,$column = '', $where = '', $arrVal = [], $ctg_id = '', $max_view,$now)
  {
    $columnKey = ($column !== '') ? $column : "*";
    $whereSQL = ($where !== '') ? ' WHERE ' . $where : '';

    $order = ' item_id DESC ';
    $this->setOrder($order);
    $limit = ' :max ';
    $offset = ' :start ';
    $this->setLimitOff($limit,$offset);
    $other = $this->groupby . " " . $this->order . " " . $this->limit . " " . $this->offset;
    $sql = " SELECT " . $columnKey . " FROM " . $table . $whereSQL . $other;

    $this->sqlLogInfo($sql,$arrVal);
    
    $stmt = $this->dbh->prepare($sql);
    //ctgがある時
    if($ctg_id !== '') {
      //pageが1の時
      if($now == 1) {
        $stmt->bindValue(":ctg_id", $ctg_id, \PDO::PARAM_STR);
        $stmt->bindValue(":max", $max_view, \PDO::PARAM_INT);
        $stmt->bindValue(":start",$now, \PDO::PARAM_INT);
      } else {
        $stmt->bindValue(":ctg_id", $ctg_id, \PDO::PARAM_STR);
        $stmt->bindValue(":max", $max_view, \PDO::PARAM_INT);
        $stmt->bindValue(":start", $now, \PDO::PARAM_INT);
      }
    //ctgがない時
    } else {
      //pageが1の時
      if($now == 1) {
        $stmt->bindValue(":max", $max_view, \PDO::PARAM_INT);
        $stmt->bindValue(":start", $now, \PDO::PARAM_INT);
      } else {
        //pageが1以外の時
        $stmt->bindValue(":max", $max_view, \PDO::PARAM_INT);
        $stmt->bindValue(":start", $now, \PDO::PARAM_INT);
      }
    }

    $res = $stmt->execute();

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }
      
    $data = [];
    while($result = $stmt->fetchAll(\PDO::FETCH_ASSOC)) {
      array_push($data,$result);
    }
    return $data;
  }

  public function selectReview($table,$column = '', $where = '', $arrVal = [])
  {
    
    $sql = $this->getReviewSql('select',$table,$where,$column);

    $this->sqlLogInfo($sql,$arrVal);
    $stmt = $this->dbh->prepare($sql);

    $res = $stmt->execute($arrVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    $data = [];
    while($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      array_push($data,$result);
    }
    return $data;
  }

  public function count($table, $where = '', $arrVal = [])
  {
    $sql = $this->getSql('count', $table, $where);

    $this->sqlLogInfo($sql, $arrVal);
    $stmt = $this->dbh->prepare($sql);

    $res = $stmt->execute($arrVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    return intval($result['NUM']);
  }

  public function setOrder($order = '')
  {
    if ($order !== '') {
      $this->order = ' ORDER BY ' . $order;
    }
  }

  public function setLimitOff($limit = '', $offset = '')
  {
    if ($limit !== "") {
      $this->limit = " LIMIT " . $limit;
    }
    if ($offset !== "") {
      $this->offset = " OFFSET " . $offset;
    }
  }

  public function setGroupBy($groupby)
  {
    if ($groupby !== "") {
      $this->groupby = ' GROUP BY ' . $groupby;
    }
  }

  public function setLeftJoin($leftjoin)
  {
    if($leftjoin !== "") {
      $this->leftjoin = ' LEFT JOIN ' . $leftjoin;
    }
  }

  public function setOn($on)
  {
    if($on !== "") {
      $this->on = ' ON ' . $on;
    }
  }


  private function getSql($type, $table, $where = '', $column = '')
  {
    switch ($type) {
      case 'select':
        $columnKey = ($column !== '') ? $column : "*";
        
        break;

      case 'count':
        $columnKey = 'COUNT(*) AS NUM ';
        break;

      default:
        break;
    }

    $whereSQL = ($where !== '') ? ' WHERE ' . $where : '';
    $other = $this->groupby . " " . $this->order . " " . $this->limit . " " . $this->offset;
    $sql = " SELECT " . $columnKey . " FROM " . $table . $whereSQL . $other;
    return $sql;
  }

  private function getReviewSql($type, $table, $where = '', $column = '')
  {
    switch ($type) {
      case 'select':
        $columnKey = ($column !== '') ? $column : "*";
        
        break;

      case 'count':
        $columnKey = 'COUNT(*) AS NUM ';
        break;

      default:
        break;
    }

    $whereSQL = ($where !== '') ? ' WHERE ' . $where : '';
    $other = $this->groupby . " " . $this->limit . " " . $this->offset . $this->leftjoin . " " . $this->on;
    $order = $this->order; 
    $sql = " SELECT " . $columnKey . " FROM " . $table . $other . $whereSQL . $order;
    return $sql;
  }

  public function insert($table, $insData = [])
  {
    $insDataKey = [];
    $insDataVal = [];
    $preCnt = [];

    $columns = '';
    $preSt = '';

    foreach ($insData as $col => $val) {
      $insDataKey[] = $col;
      $insDataVal[] = $val;
      $preCnt[] = '?';
    }

    $columns = implode(",", $insDataKey);
    $preSt = implode(",", $preCnt);
    
    $sql = " INSERT INTO "
         . $table
         . " ("
         . $columns
         . ") VALUES ("
         . $preSt
         . ") ";
    //cart_inの時
    // INSERT INTO cart (customer_no,item_id) VALUES (?,?)
 
    $this->sqlLogInfo($sql, $insDataVal);

    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($insDataVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    return $res;

  }
  
  //会員登録する際のSQL文作成・実行
  public function insertcomplete($table, $dataArr)
  {
    $column = '';
    $insData = '';
    $insDataVal = [];

    foreach($dataArr as $key => $value) {
      $column .= $key . ', ';
      if ($key === 'cue') {
        $value = implode('_', $value);
      }
      if ($key === 'password') {
        $password = $value;
        $value = password_hash($password, PASSWORD_DEFAULT); 
      }

      $insData .= ($key === '') ? '' . ',' : "'". $value ."'" . ',';
      $insDataVal[] = $value;
    }
    $sql = " INSERT INTO "
    . $table
    . " ("
    . $column
    . " regist_date "
    . ") VALUES ("
    . $insData
    . " NOW() "
    . " ) ";
    $this->sqlLogInfo($sql, $insDataVal);
    $stmt = $this->dbh->prepare($sql);

    $res = $stmt->execute($insDataVal);
    
    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }
    return $res;
  }

  public function update($table, $insData = [], $where, $arrWhereVal = [])
  {
    $arrPreSt = [];
    foreach ($insData as $col => $val) {
      $arrPreSt[] = $col . " =? ";
    }
    $preSt = implode($arrPreSt,',');

    //sql文の作成
    $sql = " UPDATE "
         . $table
         . " SET "
         . $preSt
         . " WHERE "
         . $where;
         
    $updateData = array_merge(array_values($insData), $arrWhereVal);
    $this->sqlLogInfo($sql, $updateData);
    
    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($updateData);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }
    return $res;
  }

  public function getLastId()
  {
    return $this->dbh->lastInsertId();
    //データベースの最後のIDを取ってくる
  }

  private function catchError($errArr = [])
  {
    $errMsg = (!empty($errArr[2]))? $errArr[2]:"";
    die("SQLエラーが発生しました。" . $errArr[2]);
    //die ←exit();とほぼ変わらない意味
  }

  private function makeLogFile()
  {
    $logDir = dirname(__DIR__) . "/logs";
    //もし上記のパスが存在していなければ作成する
    //管理権限777
    if(!file_exists($logDir)) {
      mkdir($logDir, 777);
    }
    $logPath = $logDir . '/cafeshop.log';
    if (!file_exists($logPath)) {
      touch($logPath);
    }
    return $logPath;
  }

  private function sqlLogInfo($str, $arrVal = [])
  {
    $logPath = $this->makeLogFile();

    //implode $arrValをカンマ区切りで連結させて1つの文字列にします。
    $logData = sprintf("[SQL_LOG:%s]: %s [%s]\n", date('Y-m-d H:i:s'), $str, implode(",", $arrVal));
    //第一引数に対して書き込む、第２引数はどの媒体にして送るか、3は書き込み、第３引数はどのファイルに指定しているかを指定。
    error_log($logData, 3, $logPath);
  }

  public function review_select($sql,$mem_id,$item_id)
  {

    $stmt = $this->dbh->prepare($sql);
    $stmt->bindValue(':mem_id', $mem_id,\PDO::PARAM_INT);
    $stmt->bindValue(':item_id', $item_id,\PDO::PARAM_INT);
    $res = $stmt->execute();
    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }
    
    //データを連想配列に格納
    $data = [];
    while($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      array_push($data,$result);
    }
    return $data;
  }

  public function addreview($sql,$add_review=[],$item_id,$mem_id)
  {
    $stmt = $this->dbh->prepare($sql);

    $res = $stmt->execute($add_review);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }
    return $res;
  }

  //検索した商品名をDBからSELECTする
  public function selectitemdata($sql,$where = [])
  {
    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($where);
    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    //データを連想配列に格納
    $data = [];
    while($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      array_push($data,$result);
    }
    return $data;
  }

  public function deleterestpass($table,$where,$arrVal)
  {
    $whereSQL = ($where !== '') ? ' WHERE ' . $where : '';

    $sql = " DELETE FROM ". $table . $whereSQL;

    $this->sqlLogInfo($sql, $arrVal);

    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($arrVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }
    return $res;
  }
}