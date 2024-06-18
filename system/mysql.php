<?php
  /**
   * 파일 열어서 디비 연결
   */
  function isConnectDb($db)
  {
    $connect = @mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    if($connect == false)
    {
      die("Failed to Database connection.");
    }
	return $connect;
  }

  /**
   * DB에 질의 보내기
   * @param string $sql
   * @return array
   */
  function db_query($sql, $connect)
  {
    @mysqli_query($connect, "SET NAMES UTF8");

    $res = @mysqli_query($connect, $sql);
    if(!$res)
    {
      echo("<br />".$sql."<br />");
      db_error();
      return false;
    }
    else
    {
      return $res;
    }
  }

  function db_insert_id($connect)
  {
    return mysqli_insert_id($connect);
  }

  function db_close($connect)
  {
    mysqli_close($connect);
  }
  
  /**
   * SQL 오류 출력
   */
  function db_error()
  {
    $err_no = mysqli_errno();
    $err_msg = mysqli_error();
    echo "<p class=\"simple-txt\">There is an error in the transmitted query.<br>ERROR_CODE ".$err_no." : ".$err_msg."</p>";
  }

  /**
   * 데이터를 배열로 받아오기
   * @param string $sql
   * @return array
   */
  function db_fetch_array($result)
  {
    $list = array();
    if(mysqli_num_rows($result) > 0)
    {
      while(($row = mysqli_fetch_array($result)))
      {
        foreach($row as $key => $value)
        {
          if(!is_numeric($key))
          {
            $list[$key][] = $value;
          }
        }
      }
    }
    return $list;
  }

  function db_fetch_assoc($result)
  {
    $list = @mysqli_fetch_assoc($result);
    return $list;
  }

  /**
   * 트렌젝션 시작
   */
  function db_tranStart($connect)
  {
    db_query("START TRANSACTION", $connect);
  }

  /**
   * 롤백
   */
  function db_tranRollback($connect)
  {
    db_query("ROLLBACK", $connect);
  }

  /**
   * 커밋
   */
  function db_tranCommit($connect)
  {
    db_query("COMMIT", $connect);
  }
?>