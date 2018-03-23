<?php

$DB_CONFIG = ['username' => '','password' => '','server' => 'localhost','dbname' => 'kb'];

function GET($k,$d = false)
{
  return isset($_GET[$k]) ? $_GET[$k] : $d;
}

function POST($k,$d = false)
{
  return isset($_POST[$k]) ? $_POST[$k] : $d;
}

function SESSION($k,$d = false)
{
  return isset($_SESSION[$k]) ? $_SESSION[$k] : $d;
}

function customCSS($text)
{
  $text = str_replace('<table>','<table class="table">',$text);
  return $text;
}

function doHash($d)
{
  $salt = "64094efe7abbd40a7416fd9";
  return hash("sha512",$salt.$d);
}

function sessionkeyDate()
{
  return date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'-12 hours'));
}

function checkSession()
{
  GLOBAL $DB_CONFIG;

  $key = SESSION('sk','');
  $username = SESSION('un','');
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);

  $stamp = sessionkeyDate();
  $query = "SELECT kb_users.username, kb_users.sessionkey FROM kb_users WHERE kb_users.username = '$username' AND kb_users.sessionkey = '$key' AND kb_users.sessionset >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
  $result = $connection->query($query);
  $connection->close();
  return $result->num_rows > 0;
}

function getRawPage($id)
{
  GLOBAL $DB_CONFIG;
  $data = [];
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);

  $query = "SELECT kb_entries.*, kb_users.fullname FROM kb_entries JOIN kb_users ON kb_users.id = kb_entries.author WHERE kb_entries.id = $id";
  $result = $connection->query($query);
  $row = $result->fetch_assoc();

  $connection->close();
  return $row;
}


function getPage($id)
{
  GLOBAL $DB_CONFIG;
  $data = [];
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);

  $query = "SELECT kb_entries.*, kb_users.fullname FROM kb_entries JOIN kb_users ON kb_users.id = kb_entries.author WHERE kb_entries.id = $id";
  $result = $connection->query($query);
  if ($result->num_rows > 0)
  {
    $row = $result->fetch_assoc();
    $data['title'] = $row['title'];
    $data['author'] = $row['fullname'];
    $data['changedon'] = $row['changedon'];
    $data['content'] = $row['content'];
    $data['tags'] = $row['tags'];
  }
  else
  {
    $data['title'] = '404 - Page Not Found!';
    $data['content'] = '';
    $data['tags'] = '';
    $data['author'] = 'The System';
    $data['changedon'] = 'The Beginning of Time';
  }
  $connection->close();
  return $data;
}

function tagsImplode($array)
{
  $r = '';
    foreach($array as $e)
      $r .= '<a href="/tags/'.trim($e).'" class="badge badge-dark">'.trim($e).'</a> ';
  return $r;
}

function fullURL()
{
  return $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function setURLVar($k,$v)
{
  $url = fullURL();
  $q = strrpos($url,'&'.$k.'=');
  if($q == false)
    $q = strrpos($url,'?'.$k.'=');
  if($q == false)
    return strrpos($url,'?') == false ? $url.'?'.$k.'='.$v : $url.'&'.$k.'='.$v;

  $nUrl = substr($url,0,$q+2+strlen($k)).$v;
  $e = strrpos($url,'&',$q+2+strlen($k));
  if($e == false)
    return $nUrl;
  else
    return $nUrl.substr($url,$e);
}

function paginateSQL($pag = 3)
{
  $p = GET('p',1)-1;
  return "LIMIT ".($pag * $p).",".$pag;
}

function paginateNav($max,$pag = 3)
{
  $p = GET('p',1);
  $maxPage = ceil($max/$pag);
  $data = '<nav aria-label=""><ul class="pagination justify-content-center">';
  $data .= '<li class="page-item '.($p > 1 ? '' : 'disabled').'"><a class="page-link" href="'.setURLVar('p',$p-1).'" tabindex="-1">Previous</a></li>';
  for($i = 1; $i <= $maxPage; $i++)
    $data .= '<li class="page-item"><a class="page-link" href="'.setURLVar('p',$i).'">'.$i.'</a></li>';

  $data .= '<li class="page-item '.($p < $maxPage ? '' : 'disabled').'"><a class="page-link" href="'.setURLVar('p',$p+1).'" tabindex="-1">Next</a></li>';
  $data .= '</ul></nav>';
  return $data;
}
