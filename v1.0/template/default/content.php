<?php
include('header.php');
?>

<div class="py-5 text-center">
  <h1><?=$PAGE['title'];?></h1>
  <p>Created by <em><?=$PAGE['author'];?></em> on <em><?=$PAGE['changedon'];?></em></p>
</div>
<div class="container">
  <?=nl2br($PAGE['content']);?>
</div>

<?php
include('footer.php');
?>
