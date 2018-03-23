<?php


function loadStaticPage()
{
  GLOBAL $staticPages;
  $id = strtolower(GET('page','admin'));
  $id = $id == '' ? 'admin' : $id;
  $parsedown = new Parsedown;
  $parsedown->setSafeMode(true);
  $page = isset($staticPages[$id]) ? $staticPages[$id] : $staticPages['404'];
  $data = [];
  $data['content'] = $page[3] ? file_get_contents(__DIR__.'/../pages/'.$page[2]) : customCSS($parsedown->text(file_get_contents(__DIR__.'/../pages/'.$page[2])));
  $data['pagetitle'] = $page[0];
  $data['subtitle'] = $page[1];
  return $data;
}

function doLogin()
{
  GLOBAL $DB_CONFIG;
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);

  $username = POST('username','');
  $password = POST('password','');

  $p = doHash($password);
  $stamp = date("Y-m-d H:i:s");
  $query = "SELECT kb_users.* FROM kb_users WHERE kb_users.username = '$username' AND kb_users.password = '$p' AND kb_users.active = 1";
  $result = $connection->query($query);
  $r = $result->num_rows > 0;
  if($r)
  {
    $row = $result->fetch_assoc();
    $sk = doHash($row['username'].$stamp);
    $id = $row['id'];
    $query = "UPDATE kb_users SET sessionkey = '$sk', sessionset = '$stamp' WHERE id = $id";
    $connection->query($query);
    $_SESSION['sk'] = $sk;
    $_SESSION['un'] = $row['username'];
  }
  $connection->close();
  return $r;
}

function logout()
{
  GLOBAL $DB_CONFIG;
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);
  $username = SESSION('un','');

  $stamp = '1900-01-01 00:00';
  $query = "UPDATE kb_users SET kb_users.sessionkey = '', kb_users.sessionset = '$stamp' WHERE kb_users.username = '$username'";
  $connection->query($query);
  session_destroy();
  $connection->close();
}

function loadEditPage()
{
  $id = GET('id');
  $page = getRawPage($id);
  $data = [];
  $data['content'] = editorBar().'<br><textarea id="content" name="content" class="form-control content" placeholder="Content">'.$page['content'].'</textarea>';
  $data['content'] = editorBar().'<br><pre onClick="this.contentEditable=\'true\';" id="content" name="content" class="form-control content" placeholder="Content">'.$page['content'].'</pre>';
  $data['pagetitle'] = '<form method="post" action="/admin/edit/'.$page['id'].'"><input type="submit" value="Save" class="btn btn-success btn-lg"> <a class="btn btn-danger btn-lg" href="/admin/delete/'.$page['id'].'">Delete</a> <a class="btn btn-outline-info btn-lg" href="/kb/'.$page['id'].'">Open Entry</a>';
  $data['subtitle'] = '<input class="form-control form-control-lg" type="text" placeholder="Title" name="title" value="'.$page['title'].'"><br><input class="form-control form-control-sm" type="text" placeholder="Tags" name="tags" value="'.$page['tags'].'">';
  return $data;
}

function editorBar()
{

  $s = '<div class="btn-toolbar" role="toolbar" aria-label="">';

  $s .= '<div class="btn-group mr-2" role="group" aria-label="Editor">';
  $s .= '<button type="button" id="editor-b" class="btn btn-secondary"><b>B</b></button>';
  $s .= '<button type="button" id="editor-i" class="btn btn-secondary"><i>I</i></button>';
  $s .= '<button type="button" id="editor-u" class="btn btn-secondary"><u>U</u></button>';
  $s .= '</div>';

  $s .= '<div class="btn-group mr-2" role="group" aria-label="Editor">';
  $s .= '<button type="button" class="btn btn-secondary">URL</button>';
  $s .= '<button type="button" class="btn btn-secondary">Image</button>';
  $s .= '</div>';

  $s .= '</div>';
  $s .= '<script src="/editor.js"></script>';
  return $s;
}

function loadAddPage()
{
  $data = [];
  $data['content'] = '<textarea name="content" class="form-control content" placeholder="Content"></textarea>';
  $data['pagetitle'] = '<form method="post" action="/admin/new"><input type="submit" value="Add" class="btn btn-success btn-lg">';
  $data['subtitle'] = '<input class="form-control form-control-lg" type="text" placeholder="Title" name="title" value=""><br><input class="form-control form-control-sm" type="text" placeholder="Tags" name="tags" value="">';
  return $data;
}

function savePage()
{
  GLOBAL $DB_CONFIG;
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);

  $id = GET('id');
  $title = $connection->real_escape_string(POST('title'));
  $content = $connection->real_escape_string(POST('content'));
  $tags = $connection->real_escape_string(cleanTags(POST('tags')));

  $query = "UPDATE kb_entries SET kb_entries.tags = '$tags', kb_entries.title = '$title', kb_entries.content = '$content' WHERE kb_entries.id = $id";
  $connection->query($query);

  if($connection->affected_rows > 0)
    $_SESSION['errors'][] = "Page saved!";

    if($connection->affected_rows <= 0)
      $_SESSION['errors'][] = "There was an error while saveing your page!";

  $connection->close();
}

function getLoginRole()
{
  GLOBAL $DB_CONFIG;

  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);

  $key = SESSION('sk','');
  $username = SESSION('un','');

  $query = "SELECT kb_users.role FROM kb_users WHERE kb_users.username = '$username' AND kb_users.sessionkey = '$key' AND kb_users.sessionset >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
  $result = $connection->query($query);
  $row = $result->fetch_assoc();
  $id = $row['role'];
  $connection->close();
  return $id;
}

function getLoginID()
{
  GLOBAL $DB_CONFIG;

  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);

  $key = SESSION('sk','');
  $username = SESSION('un','');

  $query = "SELECT kb_users.id FROM kb_users WHERE kb_users.username = '$username' AND kb_users.sessionkey = '$key' AND kb_users.sessionset >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
  $result = $connection->query($query);
  $row = $result->fetch_assoc();
  $id = $row['id'];
  $connection->close();
  return $id;
}

function insertPage()
{
  GLOBAL $DB_CONFIG;

  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);

  $id = getLoginID();
  $title = $connection->real_escape_string(POST('title'));
  $content = $connection->real_escape_string(POST('content'));
  $tags = $connection->real_escape_string(cleanTags(POST('tags')));

  $query = "INSERT INTO kb_entries (author,title,tags,content) VALUES($id,'$title','$tags','$content')";
  $connection->query($query);
  $newID = $connection->insert_id;
  $connection->close();

  if($connection->affected_rows > 0)
    $_SESSION['errors'][] = "Page saved!";

    if($connection->affected_rows <= 0)
      $_SESSION['errors'][] = "There was an error while saveing your page!";

  return $newID;
}

function deleteUser($id)
{
  GLOBAL $DB_CONFIG;

  if(getLoginRole() <> 1)
  {
    $_SESSION['errors'][] = "You aren't an Administrator!";
    header("Location: /admin/users");
    exit;
  }

  if(getLoginID() == $id)
  {
    $_SESSION['errors'][] = "You can't delete yourself!";
    header("Location: /admin/users");
    exit;
  }

  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);
  $query = "DELETE FROM kb_users WHERE kb_users.id = $id";
  $connection->query($query);
  $connection->close();

  if($connection->affected_rows > 0)
    $_SESSION['errors'][] = "User deleted!";

    if($connection->affected_rows <= 0)
      $_SESSION['errors'][] = "There was an error while deleting the user!";

}

function deletePage($id)
{
  GLOBAL $DB_CONFIG;
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);
  $query = "DELETE FROM kb_entries WHERE kb_entries.id = $id";
  $connection->query($query);
  $connection->close();

  if($connection->affected_rows > 0)
    $_SESSION['errors'][] = "Page deleted!";

    if($connection->affected_rows <= 0)
      $_SESSION['errors'][] = "There was an error while deleting the page!";

}

function getUser($id)
{
  GLOBAL $DB_CONFIG;
  $data = [];
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);

  $query = "SELECT kb_users.id, kb_users.username, kb_users.fullname, kb_users.active, kb_users.role, kb_users.createdon, kb_users.changedon FROM kb_users WHERE id = $id";
  $result = $connection->query($query);
  $row = $result->fetch_assoc();

  $connection->close();
  return $row;
}

function getUserList()
{
  GLOBAL $DB_CONFIG;
  $data = [];
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);

  $query = "SELECT kb_users.id, kb_users.username, kb_users.fullname, kb_users.active, kb_users.role, kb_users.createdon, kb_users.changedon FROM kb_users";
  $result = $connection->query($query);
  while($row = $result->fetch_assoc())
  {
    array_push($data,$row);
  }

  $connection->close();
  return $data;
}

function showUserList()
{
  GLOBAL $text_state, $text_role;
  $users = getUserList();

  $data['pagetitle'] = "Users";
  $data['subtitle'] = '<a href="/admin/add-user/" class="btn btn-primary">Add User</a>';

  $data['content'] = '<table class="table table-hover"><tr><th>ID</th><th>Username</th><th>Fullname</th><th>Active</th><th>Role</th><th>Created On</th><th>Changed On</th></tr>';

  foreach($users as $r)
  {
    $data['content'] .= '<tr class="clickable-row" data-href="/admin/user/'.$r['id'].'">';
    $data['content'] .= '<td>'.$r['id'].'</td>';
    $data['content'] .= '<td>'.$r['username'].'</td>';
    $data['content'] .= '<td>'.$r['fullname'].'</td>';
    $data['content'] .= '<td>'.$text_state[$r['active']].'</td>';
    $data['content'] .= '<td>'.$text_role[$r['role']].'</td>';
    $data['content'] .= '<td>'.$r['createdon'].'</td>';
    $data['content'] .= '<td>'.$r['changedon'].'</td>';
    $data['content'] .= '</tr>';
  }

  $data['content'] .= '</table>';

  return $data;
}

function showUser($id)
{
  GLOBAL $text_state, $text_role;
  $r = getUser($id);
  $data['pagetitle'] = "User - ". $r['fullname'];
  $data['subtitle'] = '<form method="post" action="/admin/user/'.$id.'"><input type="submit" value="Save" class="btn btn-success btn-lg">';

  $data['content'] = '<table class="table">';

  $data['content'] .= '<tr><td>ID:</td><td><input type="text" disabled class="form-control" value="'.$r['id'].'"/></td></tr>';
  $data['content'] .= '<tr><td>Username:</td><td><input type="text" name="username" class="form-control" value="'.$r['username'].'"/></td></tr>';
  $data['content'] .= '<tr><td>Fullname:</td><td><input type="text" name="fullname" class="form-control" value="'.$r['fullname'].'"/></td></tr>';
  if($r['id'] == getLoginID())
  {
    $data['content'] .= '<tr><td>Current Password:</td><td><input type="password" name="password" class="form-control"/></td></tr>';
    $data['content'] .= '<tr><td>New Password:</td><td><input type="password" name="passwordnew" class="form-control"/></td></tr>';
    $data['content'] .= '<tr><td>New Password (repeat):</td><td><input type="password" name="passwordrepeat" class="form-control"/></td></tr>';
    $data['content'] .= '<tr><td>Active:</td><td><input type="text" disabled class="form-control" value="'.$text_state[$r['active']].'"/></td></tr>';
    $data['content'] .= '<tr><td>Role:</td><td><input type="text" disabled class="form-control" value="'.$text_role[$r['role']].'"/></td></tr>';
  }
  else
  {
    $data['subtitle'] .=  '<a class="btn btn-danger btn-lg" href="/admin/delete-user/'.$id.'">Delete</a>';
    $data['content'] .= '<tr><td>Active</td><td><select class="form-control" name="active">';
    foreach($text_state as $k => $d)
      $data['content'] .= '<option value="'.$k.'" '.($k == $r['active'] ? 'selected' : '').'>'.$d.'</option>';
    $data['content'] .= '</select></td></tr>';

    $data['content'] .= '<tr><td>Role</td><td><select class="form-control" name="role">';
    foreach($text_role as $k => $d)
      $data['content'] .= '<option value="'.$k.'" '.($k == $r['role'] ? 'selected' : '').'>'.$d.'</option>';
    $data['content'] .= '</select></td></tr>';
  }
  $data['content'] .= '<tr><td>Created On:</td><td><input type="text" disabled class="form-control" value="'.$r['createdon'].'"/></td></tr>';
  $data['content'] .= '<tr><td>Changed On:</td><td><input type="text" disabled class="form-control" value="'.$r['changedon'].'"/></td></tr>';

  $data['content'] .= '</table>';

  return $data;
}

function updateUser()
{
  GLOBAL $DB_CONFIG;

  if(getLoginRole() <> 1)
  {
    $_SESSION['errors'][] = "You aren't an Administrator!";
    header("Location: /admin/users");
    exit;
  }

  $id = GET('id');
  $username = POST('username');
  $fullname = POST('fullname');
  $password = POST('password');
  $passwordnew = POST('passwordnew',true);
  $passwordrepeat = POST('passwordrepeat',false);
  $active = POST('active');
  $role = POST('role');

  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);


  $query = "UPDATE kb_users SET kb_users.username = '$username', kb_users.fullname = '$fullname' WHERE kb_users.id = $id";
  $connection->query($query);

  if($passwordnew == $passwordrepeat && strlen($passwordnew) > 0)
  {
      $query = "UPDATE kb_users SET kb_users.password = '".doHash($passwordnew)."' WHERE kb_users.id = $id AND kb_users.password = '".doHash($password)."'";
      $connection->query($query);
      if($connection->affected_rows <= 0)
        $_SESSION['errors'][] = "The old Password was wrong!";
  }

  if(getLoginID() <> $id && $active <> false && $role <> false)
  {
      $query = "UPDATE kb_users SET kb_users.active = '$active', kb_users.role = '$role' WHERE kb_users.id = $id";
      $connection->query($query);
  }

  if(isset($_POST['passwordnew']) && $passwordnew <> $passwordrepeat)
    $_SESSION['errors'][] = "The new Passwords didn't match!";

  $connection->close();
}

function insertUser()
{
  GLOBAL $DB_CONFIG;

  if(getLoginRole() <> 1)
  {
    $_SESSION['errors'][] = "You aren't an Administrator!";
    header("Location: /admin/users");
    exit;
  }

  $username = POST('username');
  $fullname = POST('fullname');
  $password = POST('password',true);
  $passwordrepeat = POST('passwordrepeat',false);
  $active = POST('active');
  $role = POST('role');
  $newID = -1;

  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);
  if($password == $passwordrepeat && strlen($password) > 0)
  {
    $query = "INSERT INTO kb_users(username, fullname, password, active, role) VALUES ('$username','$fullname','".doHash($password)."',$active,$role)";
    $connection->query($query);

    if($connection->affected_rows > 0)
      $_SESSION['errors'][] = "User saved!";

    if($connection->affected_rows <= 0)
    {
      $_SESSION['errors'][] = "There was an error while saveing the user!";
      $_SESSION['username'] = $username;
      $_SESSION['fullname'] = $fullname;
      $_SESSION['active'] = $active;
      $_SESSION['role'] = $role;
    }

      $newID = $connection->insert_id;
  }
  else
  {
      $_SESSION['errors'][] = $password == $passwordrepeat ? "The Password can't be empty!" : "The Passwords don't match!";

      $_SESSION['username'] = $username;
      $_SESSION['fullname'] = $fullname;
      $_SESSION['active'] = $active;
      $_SESSION['role'] = $role;
  }

  $connection->close();
  return $newID;
}

function cleanTags($t)
{
  $q = explode(',',$t);
  $r = '';
    foreach($q as $e)
      $r .= trim($e).',';
  return substr($r,0,-1);
}

function showAddUser()
{
  GLOBAL $text_state, $text_role;

  $data['pagetitle'] = "Add User";
  $data['subtitle'] = '<form method="post" action="/admin/add-user/"><input type="submit" value="Save" class="btn btn-success btn-lg">';

  $data['content'] = '<table class="table">';

  $data['content'] .= '<tr><td>Username:</td><td><input type="text" name="username" class="form-control" placeholder="Username" value="'.(isset($_SESSION['username']) ? $_SESSION['username'] : '').'"/></td></tr>';
  $data['content'] .= '<tr><td>Fullname:</td><td><input type="text" name="fullname" class="form-control" placeholder="Fullname" value="'.(isset($_SESSION['username']) ? $_SESSION['fullname'] : '').'"/></td></tr>';
  $data['content'] .= '<tr><td>Password:</td><td><input type="password" name="password" class="form-control"/></td></tr>';
    $data['content'] .= '<tr><td>Password (repeat):</td><td><input type="password" name="passwordrepeat" class="form-control"/></td></tr>';
  $data['content'] .= '<tr><td>Active</td><td><select class="form-control" name="active">';
  foreach($text_state as $k => $d)
    $data['content'] .= '<option value="'.$k.'" '.(isset($_SESSION['active']) ? ($_SESSION['active'] == $k ? 'selected' : '') : '').'>'.$d.'</option>';
    $data['content'] .= '</select></td></tr>';

  $data['content'] .= '<tr><td>Role</td><td><select class="form-control" name="role">';
    foreach($text_role as $k => $d)
      $data['content'] .= '<option value="'.$k.'" '.(isset($_SESSION['role']) ? ($_SESSION['role'] == $k ? 'selected' : '') : '').'>'.$d.'</option>';
    $data['content'] .= '</select></td></tr>';

  $data['content'] .= '</table>';

  return $data;
}
