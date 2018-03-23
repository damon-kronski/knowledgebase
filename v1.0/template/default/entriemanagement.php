<?php
include('header.php');
?>

<div class="py-5 text-center">
  <h1>Manage Entries</h1>
</div>


<div class="container">
  <table class="table table-striped">
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Author</th>
      <th>Created On</th>
      <th>Changed On</th>
    </tr>
  <?php
    foreach(MySQL_Helper::managementEntries(Helper::GET('p')) as $d)
    {
  ?>
    <tr>
      <td><?=$d['id'];?></td>
      <td><a href="/admin/editentry/<?=$d['id'];?>"><?=$d['title'];?></a></td>
      <td><?=$d['fullname'];?></td>
      <td><?=$d['createdon'];?></td>
      <td><?=$d['changedon'];?></td>
    </tr>
  <?php
    }
  ?>
</table>

  <nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
      <li class="page-item <?=Helper::GET('p',1) == 1 ? 'disabled' : '';?>">
        <a class="page-link" href="?p=<?=Helper::GET('p',1)-1;?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
          <span class="sr-only">Previous</span>
        </a>
      </li>
      <?php
        for($i = 1; $i <= MySQL_Helper::managementEntriesCount(); $i++)
        {
        ?>
          <li class="page-item <?=$i==Helper::GET('p',1) ? 'active' : '';?>">
            <a class="page-link" href="?p=<?=$i;?>"><?=$i;?><?=$i==Helper::GET('p',1) ? '<span class="sr-only">(current)</span>':'';?></a>
          </li>
    <?php
      }
    ?>
      <li class="page-item <?=MySQL_Helper::managementEntriesCount() == Helper::GET('p',1) ? 'disabled' : '';?>">
        <a class="page-link" href="?p=<?=Helper::GET('p',1)+1;?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
          <span class="sr-only">Next</span>
        </a>
      </li>
    </ul>
  </nav>

</div>

<?php
include('footer.php');
?>
