<?php

  class Admin
  {

    public static function loginState()
    {
      if(isset($_SESSION['un']) && isset($_SESSION['sk']))
      {
        return MySQL_Helper::checkSession($_SESSION['sk'],$_SESSION['un']);
      }
      else
        return false;
    }

    public static function showLogin()
    {
      if(SELF::loginState())
      {
        header("Location: /admin/");
      }
      else {
        ShowPage::login();
      }
    }
  }


 ?>
