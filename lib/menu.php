

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">KB</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="/">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/kb/all">All Entries</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/tags/">All Tags</a>
        </li>
      </ul>
      <form class="form-inline my-2 my-lg-0" method="get" action="/">
        <input name="q" class="form-control mr-sm-2" type="search" placeholder="Search" value="<?=GET('q','');?>" aria-label="Search">
        <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Search</button>
      </form>

      <ul class="navbar-nav">
      <?php
        if(checkSession())
        {
        ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Admin
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="/admin">Admin</a>
            <a class="dropdown-item" href="/admin/new">New Entry</a>
            <?php
            if(isset($_GET['kb']))
            {?>
            <a class="dropdown-item" href="/admin/edit/<?=GET('kb');?>">Edit Entry</a>
            <?php
          } ?>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="/admin/logout">Logout</a>
          </div>
        </li>
        <?php
        }
        else
        {
          ?>
          <li class="nav-item">
            <a class="nav-link" href="/admin">Admin</a>
          </li>
          <?php
        }
        ?>
      </ul>
    </div>
  </nav>
