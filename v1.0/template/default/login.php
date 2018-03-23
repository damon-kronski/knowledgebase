<?php
include('header.php');
?>

<div class="py-5 text-center">
  <h1>Login</h1>
</div>
  <form class="form-signin" action="/admin/dologin" method="post">
      <div class="form-label-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Username" required="" autofocus="">
      </div>

      <div class="form-label-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required="">
      </div>

      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    </form>

<?php
include('footer.php');
?>
