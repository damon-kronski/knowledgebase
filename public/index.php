<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include(__DIR__.'/../lib/front_pages.php');
include(__DIR__.'/../lib/parsedown.php');
include(__DIR__.'/../lib/parseextended.php');
include(__DIR__.'/../lib/helper.php');
include(__DIR__.'/../lib/front.php');

session_start();

if(isset($_GET['kb']))
{
  $page = loadKBPage();
}
elseif(GET('page','') == 'kball')
{
  $page = allKBPages();

}
elseif(GET('page','') == 'tags' && isset($_GET['tags']))
{
  $page = strlen($_GET['tags']) > 0 ? searchKBTaged() : showAllTags();

}
elseif (isset($_GET['q'])) {
  $page = searchKBPage();
}
else {
  $page = loadStaticPage();
}

?>

<html>
  <head>
    <title>Knowledge-Base</title>
    <link href="https://dist.damon.ch/bootstrap/bootstrap.min.css" rel="stylesheet"/>
    <link href="/custom.css" rel="stylesheet"/>
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

      <div class="py-5 text-center">
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
