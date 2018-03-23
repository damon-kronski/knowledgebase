<?php
include('header.php');
?>

<div class="py-5 text-center">
  <h1>Administration</h1>
</div>
<div class="container">
  <div class="card-deck mb-3 text-center">

    <div class="card mb-4 box-shadow">
      <div class="card-header">
        <h4 class="my-0 font-weight-normal">Entries</h4>
      </div>
      <div class="card-body">
        <ul class="list-unstyled mt-3 mb-4">
          <li><?=MySQL_Helper::countentries();?> entries</li>
          <li> </li>
        </ul>
        <a href="/admin/entriemanagement" class="btn btn-lg btn-block btn-outline-primary">Manage Entries</a>
      </div>
    </div>

    <div class="card mb-4 box-shadow">
      <div class="card-header">
        <h4 class="my-0 font-weight-normal">Users</h4>
      </div>
      <div class="card-body">
        <ul class="list-unstyled mt-3 mb-4">
          <li><?=MySQL_Helper::activeusers();?> user(s) activated</li>
          <li><?=MySQL_Helper::deactiveusers();?> user(s) deactivated</li>
        </ul>
        <button type="button" class="btn btn-lg btn-block btn-outline-primary">Manage Users</button>
      </div>
    </div>

    <div class="card mb-4 box-shadow">
      <div class="card-header">
        <h4 class="my-0 font-weight-normal">Settings</h4>
      </div>
      <div class="card-body">
        <ul class="list-unstyled mt-3 mb-4">
          <li> </li>
          <li> </li>
        </ul>
        <button type="button" class="btn btn-lg btn-block btn-outline-primary">Change Settings</button>
      </div>
    </div>

  </div>
    </div>
<?php
include('footer.php');
?>
