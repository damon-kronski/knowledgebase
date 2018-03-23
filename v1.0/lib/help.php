<?php


class Helper
{
  public static function start()
  {
    session_start();
    MySQL_Helper::bootup();
  }

  public static function end()
  {
    MySQL_Helper::shutdown();
  }

  public static function route()
  {
    if(isset($_GET['id']) && SELF::GET('r','KB') == 'KB')
    {
      ShowPage::content(MySQL_Helper::getPage(SELF::GET('id')));
      return;
    }
    if(isset($_GET['q']) && SELF::GET('r') == 'search')
    {
      ShowPage::search(MySQL_Helper::searchEntries(SELF::GET('q')));
      return;
    }
    if(SELF::GET('r') == 'admin' && SELF::GET('page') == 'login')
    {
      Admin::showLogin();
      return;
    }
    if(SELF::GET('r') == 'admin' && SELF::GET('page') == 'dologin')
    {
      if(MySQL_Helper::tryLogin(SELF::POST('username',''),SELF::POST('password','')))
      {
        header("Location: /admin/overview");
      }
      else
      {
        header("Location: /admin/login");
      }
      return;
    }
    if(SELF::GET('r') == 'admin' && SELF::GET('page') == 'overview')
    {
      if(Admin::loginState())
        ShowPage::overview();
      else
        header("Location: /admin/login");
      return;
    }
    if(SELF::GET('r') == 'admin' && SELF::GET('page') == 'entriemanagement')
    {
      if(Admin::loginState())
        ShowPage::entriemanagement();
      else
        header("Location: /admin/login");
      return;
    }

    if(SELF::GET('r','') == '')
    {
      ShowPage::home();
      return;
    }


  }

  public static function GET($k,$d = false)
  {
    return isset($_GET[$k]) ? $_GET[$k] : $d;
  }

  public static function POST($k,$d = false)
  {
    return isset($_POST[$k]) ? $_POST[$k] : $d;
  }

  public static function cutLine($text,$lines)
  {
    $str = ""; // initialise the string
    $arr = explode("\n", $text);
    if(count($arr) > $lines) { // you've got more than $lines line breaks
       $arr = array_splice($arr, 0, $lines); // reduce the lines to four
       foreach($arr as $line) { $str .= $line."\n"; } // store them all in a string
    } else {
       $str = $text; // there was less or equal to four rows so to us it'all ok
    }
    return $str;
  }

  public static function hash($d)
  {
    $salt = "64094efe7abbd40a7416fd9";
    return hash("sha512",$salt.$d);
  }

  public static function sessionkeyDate()
  {
    return date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'-12 hours'));
  }
}
