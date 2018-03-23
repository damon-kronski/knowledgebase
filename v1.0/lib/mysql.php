<?php


class MySQL_Helper
{
  public static $CONFIG = ['username' => '','password' => '','server' => '','dbname' => ''];
  private static $connection;

  public static function bootup()
  {
    SELF::$connection = new mysqli(SELF::$CONFIG['server'], SELF::$CONFIG['username'], SELF::$CONFIG['password'], SELF::$CONFIG['dbname']);
  }

  public static function shutdown()
  {
    SELF::$connection->close();
  }

  public static function getPage($id)
  {
    $data = [];
    $query = "SELECT kb_entries.*, kb_users.fullname FROM kb_entries JOIN kb_users ON kb_users.id = kb_entries.author WHERE kb_entries.id = $id";
    $result = SELF::$connection->query($query);
    if ($result->num_rows > 0)
    {
      $row = $result->fetch_assoc();
      $data['title'] = $row['title'];
      $data['author'] = $row['fullname'];
      $data['changedon'] = $row['changedon'];
      $data['content'] = $row['content'];
    }
    else
    {
      $data['title'] = '404 - Page Not Found!';
      $data['content'] = '';
      $data['author'] = 'The System';
      $data['changedon'] = 'The Beginning of Time';
    }
    return $data;
  }

  public static function managementEntries($p)
  {
    $data = [];
    $pagination = 10;
    $p--;
    $query = "SELECT LEFT(kb_entries.content , 100) as content,kb_entries.id, kb_entries.author, kb_entries.title,kb_entries.createdon,kb_entries.changedon, kb_users.fullname FROM kb_entries JOIN kb_users ON kb_users.id = kb_entries.author LIMIT ".($pagination * $p).",".$pagination;
    $result = SELF::$connection->query($query);
    while($row = $result->fetch_assoc())
    {
      $r = $row;
      $r["content"] = Helper::cutLine($row['content'],3);
      array_push($data,$r);
    }
    return $data;
  }

  public static function managementEntriesCount()
  {
    $pagination = 10;
    return floor((SELF::countentries() / $pagination)+1);
  }

  public static function homeEntries()
  {
    $data = [];
    $query = "SELECT LEFT(kb_entries.content , 100) as content,kb_entries.id, kb_entries.author, kb_entries.title,kb_entries.createdon,kb_entries.changedon, kb_users.fullname FROM kb_entries JOIN kb_users ON kb_users.id = kb_entries.author ORDER BY changedon DESC LIMIT 5";
    $result = SELF::$connection->query($query);
    while($row = $result->fetch_assoc())
    {
      $r = $row;
      $r["content"] = Helper::cutLine($row['content'],3);
      array_push($data,$r);
    }
    return $data;
  }

  public static function searchEntries($q)
  {
    $data = [];
    $query = "SELECT LEFT(kb_entries.content , 100) as content,kb_entries.id, kb_entries.author, kb_entries.title,kb_entries.createdon,kb_entries.changedon, kb_users.fullname FROM kb_entries JOIN kb_users ON kb_users.id = kb_entries.author WHERE kb_entries.title LIKE '%$q%' or kb_entries.content LIKE '%$q%'";
    $result = SELF::$connection->query($query);
    while($row = $result->fetch_assoc())
    {
      $r = $row;
      $r["content"] = Helper::cutLine($row['content'],3);
      array_push($data,$r);
    }
    return $data;
  }

  public static function checkSession($key,$username)
  {
    $stamp = Helper::sessionkeyDate();
    $query = "SELECT kb_users.username, kb_users.sessionkey FROM kb_users WHERE kb_users.username = '$username' AND kb_users.sessionkey = '$key' AND kb_users.sessionset >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $result = SELF::$connection->query($query);
    return $result->num_rows > 0;
  }

  public static function tryLogin($username,$password)
  {
    $p = Helper::hash($password);
    $stamp = date("Y-m-d H:i:s");
    $query = "SELECT kb_users.* FROM kb_users WHERE kb_users.username = '$username' AND kb_users.password = '$p' AND kb_users.active = 1";
    $result = SELF::$connection->query($query);
    $r = $result->num_rows > 0;
    if($r)
    {
      $row = $result->fetch_assoc();
      $sk = Helper::hash($row['username'].$stamp);
      $id = $row['id'];
      $query = "UPDATE kb_users SET sessionkey = '$sk', sessionset = '$stamp' WHERE id = $id";
      SELF::$connection->query($query);
      $_SESSION['sk'] = $sk;
      $_SESSION['un'] = $row['username'];
    }
    return $r;
  }

  public static function fullname()
  {
    $stamp = Helper::sessionkeyDate();
    $username = $_SESSION['un'];
    $key = $_SESSION['sk'];
    $query = "SELECT kb_users.fullname, kb_users.username, kb_users.sessionkey FROM kb_users WHERE kb_users.username = '$username' AND kb_users.sessionkey = '$key' AND kb_users.sessionset >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $result = SELF::$connection->query($query);
    if($result->num_rows > 0)
    {
      $row = $result->fetch_assoc();
      return $row['fullname'];
    }
    return "";
  }

  public static function activeusers()
  {
    $query = "SELECT COUNT(kb_users.id) as cnt FROM kb_users WHERE active = 1";
    $result = SELF::$connection->query($query);
    $row = $result->fetch_assoc();
    return $row['cnt'];
  }

  public static function deactiveusers()
  {
    $query = "SELECT COUNT(kb_users.id) as cnt FROM kb_users WHERE active = 0";
    $result = SELF::$connection->query($query);
    $row = $result->fetch_assoc();
    return $row['cnt'];
  }

  public static function countentries()
  {
    $query = "SELECT COUNT(kb_entries.id) as cnt FROM kb_entries";
    $result = SELF::$connection->query($query);
    $row = $result->fetch_assoc();
    return $row['cnt'];
  }

}
