<?php

class ShowPage
{
  public static function content($data)
  {
    $PAGE = $data;
    include(__DIR__.'/../template/default/content.php');
  }

  public static function search($data)
  {
    $SEARCHRESULTS = $data;
    include(__DIR__.'/../template/default/search.php');
  }

  public static function login()
  {
    include(__DIR__.'/../template/default/login.php');
  }

  public static function overview()
  {
    include(__DIR__.'/../template/default/overview.php');
  }

  public static function home()
  {
    include(__DIR__.'/../template/default/home.php');
  }

  public static function entriemanagement()
  {
    include(__DIR__.'/../template/default/entriemanagement.php');
  }
}
