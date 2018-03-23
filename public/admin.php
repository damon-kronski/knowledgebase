<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include(__DIR__.'/../lib/text.php');
include(__DIR__.'/../lib/back_pages.php');
include(__DIR__.'/../lib/parsedown.php');
include(__DIR__.'/../lib/helper.php');
include(__DIR__.'/../lib/back.php');

session_start();

if(GET('page') == 'login')
{
  $page = loadStaticPage();
  if(isset($_POST['username']))
  {
    if(doLogin())
      header("Location: /admin/");
    else
      header("Location: /admin/login");
  }
}
else {
  if(checkSession())
  {
    $routed = false;
    switch(GET('page'))
    {
        case 'users':
          $routed = true;
          $page = showUserList();
        break;

        case 'add-user':
          if(getLoginRole() == 1)
          {
            if(isset($_POST['username']))
            {
              $id = insertUser();
              if($id > 0)
                header("Location: /admin/user/".$id);
              else
                header("Location: /admin/add-user");
              exit;
            }
            else {
              $routed = true;
              $page = showAddUser();
              unset($_SESSION['username']);
              unset($_SESSION['fullname']);
              unset($_SESSION['active']);
              unset($_SESSION['role']);
            }
          }
          else
          {
            $routed = true;
            header("Location: /admin/users");
            exit;
          }
        break;

        case 'user':
          if(getLoginRole() == 1)
          {
            if(isset($_POST['username']))
            {
              updateUser();
              header("Location: /admin/user/".GET('id'));
              exit;
            }
            else {
              if(isset($_GET['id']))
              {
                $routed = true;
                $page = showUser($_GET['id']);
              }
            }
          }
          else
          {
            $routed = true;
            header("Location: /admin/users");
            exit;
          }
        break;

        case 'logout':
          $routed = true;
          logout();
          header("Location: /");
          exit;
        break;

        case 'new':
          $routed = true;
          if(isset($_POST['content']))
          {
            $id = insertPage();
            header("Location: /kb/".$id);
            exit;
          }
          $page = loadAddPage();
        break;

        case 'delete':
          if(isset($_GET['id']))
          {
              $routed = true;
              deletePage($_GET['id']);
              header("Location: /");
              exit;
          }
          break;

        case 'delete-user':
          if(isset($_GET['id']))
          {
              $routed = true;
              deleteUser($_GET['id']);
              header("Location: /");
              exit;
          }
          break;

        case 'edit':
          if(isset($_GET['id']))
          {
              $routed = true;
              if(isset($_POST['content']))
              {
                savePage();
                header("Location: /admin/edit/".GET('id'));
                exit;
              }
              $page = loadEditPage();
          }
          break;
    }

    if(!$routed)
    {
      $page = loadStaticPage();
    }
  }
  else {
    header("Location: /admin/login");
  }
}



?>

<html>
  <head>
    <title>Knowledge-Base | Admin</title>
    <link href="https://dist.damon.ch/bootstrap/bootstrap.min.css" rel="stylesheet"/>
    <link href="/custom.css" rel="stylesheet"/>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://dist.damon.ch/jquery/rangyinputs-jquery-src.js"></script>
    <script src="https://dist.damon.ch/bootstrap/bootstrap.min.js"></script>
  </head>
  <body>


    <div class="container">

      <?php
      include(__DIR__.'/../lib/menu.php');
      ?>

      <?php
        foreach(SESSION('errors',[]) as $error)
        {
          ?>
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
              <?=$error;?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php
        }
        unset($_SESSION['errors']);
      ?>


      <div class="py-3 text-center">
        <h1><?=$page['pagetitle'];?></h1>
        <p><?=$page['subtitle'];?></p>
      </div>

      <div class="container">
        <?=$page['content'];?>
      </div>

      <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
      <script src="https://dist.damon.ch/bootstrap/bootstrap.min.js"></script>
      <script>
        jQuery(document).ready(function($) {
            $(".clickable-row").click(function() {
                window.location = $(this).data("href");
            });
        });
      </script>
    </div>
  </body>
</html>
