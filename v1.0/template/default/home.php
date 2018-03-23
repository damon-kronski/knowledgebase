<?php
include('header.php');
?>

<div class="py-5 text-center">
  <h1>Home (Top 5)</h1>
</div>


<div class="container">
  <?php
    foreach(MySQL_Helper::homeEntries() as $d)
    {
  ?>
    <a class="media text-muted pt-3" href="/KB/<?=$d['id'];?>">
      <img data-src="holder.js/32x32?random=yes&text= &size=1" class="mr-2 rounded">
      <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
        <strong class="d-block text-gray-dark"><?=$d['title'];?> <em>(<?=$d['fullname'];?> - <?=$d['changedon'];?>)</em></strong>
        <?=nl2br($d['content']);?>
      </p>
    </a>
  <?php
    }
  ?>
</div>

<?php
include('footer.php');
?>
