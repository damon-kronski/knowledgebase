<?php

function showAllTags()
{
  GLOBAL $DB_CONFIG;
  $tags = [];
  $data['content'] = '<table class="table table-hover">';

  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);
  $query = "SELECT kb_entries.tags FROM kb_entries GROUP BY kb_entries.tags";
  $result = $connection->query($query);
  while($row = $result->fetch_assoc())
  {
    foreach(explode(',',$row['tags']) as $q)
    {
      if(!in_array(trim($q),$tags))
      {
        $tags[] = trim($q);
        $data['content'] .= '<tr><td><a href="/tags/'.trim($q).'">'.trim($q).'</a></td></tr>';
      }
    }
  }


  $data['content'] .= '</table>';
  $data['pagetitle'] = 'All Tags';
  $data['subtitle'] = '';
  $connection->close();
  return $data;
}

function allPages()
{
  GLOBAL $DB_CONFIG;
  $parsedown = new ParseExtended;
  $parsedown->setSafeMode(true);
  $data = '<ul class="list-unstyled pagelist">';
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);


  $query = "SELECT LEFT(kb_entries.content , 250) as content,kb_entries.tags, kb_entries.id, kb_entries.author, kb_entries.title,kb_entries.createdon,kb_entries.changedon, kb_users.fullname FROM kb_entries JOIN kb_users ON kb_users.id = kb_entries.author ".paginateSQL();
  $result = $connection->query($query);
  while($row = $result->fetch_assoc())
  {
    $tags = tagsImplode(explode(',',$row['tags']));

    $data .= '<div class="media"><div class="media-body">';
    $data .= '<h5 class="mt-0"><a href="/kb/'.$row['id'].'">'.$row['title'].' (<em>'.$row['fullname'].' - '.$row['changedon'].'</em>)</a></h5>';
    $data .= '<p class="mt-0">'.$tags.'</p>';
    $data .= '<p>'.customCSS($parsedown->text(cutLines($row['content']))).'</p>';
    $data .=  '</div></div>';
  }
  $data .= '</ul>';

  $query = "SELECT COUNT(kb_entries.id) as c FROM kb_entries";
  $result = $connection->query($query);
  $row = $result->fetch_assoc();

  $data .= paginateNav($row['c']);

  $connection->close();
  return $data;
}


function searchPages($q)
{
  GLOBAL $DB_CONFIG;
  $parsedown = new ParseExtended;
  $parsedown->setSafeMode(true);
  $data = '<ul class="list-unstyled pagelist">';
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);
  $st = "";

  foreach(explode(' ',$q) as $p)
  {
     $st .= " OR kb_entries.content LIKE '%$p%' OR kb_entries.title LIKE '%$p%'";
  }

  $query = "SELECT LEFT(kb_entries.content , 250) as content,kb_entries.tags, kb_entries.id, kb_entries.author, kb_entries.title,kb_entries.createdon,kb_entries.changedon, kb_users.fullname FROM kb_entries JOIN kb_users ON kb_users.id = kb_entries.author WHERE kb_entries.title LIKE '%$q%' OR kb_entries.content LIKE '%$q%' $st ".paginateSQL();
  $result = $connection->query($query);
  while($row = $result->fetch_assoc())
  {
    $tags = tagsImplode(explode(',',$row['tags']));

    $data .= '<div class="media"><div class="media-body">';
    $data .= '<h5 class="mt-0"><a href="/kb/'.$row['id'].'">'.$row['title'].' (<em>'.$row['fullname'].'</em> - '.$row['changedon'].')</a></h5>';
    $data .= '<p class="mt-0">'.$tags.'</p>';
    $data .= '<p>'.customCSS($parsedown->text(cutLines($row['content']))).'</p>';
    $data .=  '</div></div>';
  }
  $data .= '</ul>';

  $query = "SELECT COUNT(kb_entries.id) as c FROM kb_entries WHERE kb_entries.title LIKE '%$q%' OR kb_entries.content LIKE '%$q%' $st";
  $result = $connection->query($query);
  $row = $result->fetch_assoc();

  $data .= paginateNav($row['c']);

  $connection->close();
  return $data;
}

function cutLines($s)
{
  return implode("\n",array_slice(explode("\n",$s),0,2));
}

function loadKBPage()
{
  $id = GET('kb');
  $parsedown = new Parsedown;
  $parsedown->setSafeMode(true);
  $page = getPage($id);
  $tags = tagsImplode(explode(',',$page['tags']));
  $data = [];
  $data['content'] = customCSS($parsedown->text($page['content']));
  $data['pagetitle'] = $page['title'];
  $data['subtitle'] = "Created by <em>".$page['author']."</em> on <em>".$page['changedon']."</em><br>".$tags;
  return $data;
}

function allKBPages()
{
  $page = allPages();
  $data = [];
  $data['content'] = $page;
  $data['pagetitle'] = "All Entries";
  $data['subtitle'] = '';
  return $data;
}

function searchKBPage()
{
  $q = GET('q','');

  $page = searchPages($q);
  $data = [];
  $data['content'] = $page;
  $data['pagetitle'] = "Search Results for: ".$q;
  $data['subtitle'] = '';
  return $data;
}

function loadStaticPage()
{
  GLOBAL $staticPages;
  $id = strtolower(GET('page','home'));
  $parsedown = new Parsedown;
  $parsedown->setSafeMode(true);
  $page = isset($staticPages[$id]) ? $staticPages[$id] : $staticPages['404'];
  $data = [];
  $data['content'] = customCSS($parsedown->text(file_get_contents(__DIR__.'/../pages/'.$page[2])));
  $data['pagetitle'] = $page[0];
  $data['subtitle'] = $page[1];
  return $data;
}

function searchKBTaged()
{
  $q = GET('tags','');
  $page = searchTaged($q);
  $data = [];
  $data['content'] = $page;
  $data['pagetitle'] = "Tagged Pages: ".$q;
  $data['subtitle'] = '';
  return $data;
}

function searchTaged($q)
{
  GLOBAL $DB_CONFIG;
  $parsedown = new ParseExtended;
  $parsedown->setSafeMode(true);
  $data = '<ul class="list-unstyled pagelist">';
  $connection = new mysqli($DB_CONFIG['server'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['dbname']);

  $query = "SELECT LEFT(kb_entries.content , 100) as content,kb_entries.tags,kb_entries.id, kb_entries.author, kb_entries.title,kb_entries.createdon,kb_entries.changedon, kb_users.fullname FROM kb_entries JOIN kb_users ON kb_users.id = kb_entries.author WHERE kb_entries.tags LIKE '%,$q,%' OR kb_entries.tags LIKE '%,$q' OR kb_entries.tags LIKE '$q' OR kb_entries.tags LIKE '$q,%' ".paginateSQL();

  $result = $connection->query($query);
  while($row = $result->fetch_assoc())
  {
    $tags = tagsImplode(explode(',',$row['tags']));

    $data .= '<div class="media"><div class="media-body">';
    $data .= '<h5 class="mt-0"><a href="/kb/'.$row['id'].'">'.$row['title'].' (<em>'.$row['fullname'].'</em> - '.$row['changedon'].')</a></h5>';
    $data .= '<p class="mt-0">'.$tags.'</p>';
    $data .= '<p>'.customCSS($parsedown->text(cutLines($row['content']))).'</p>';
    $data .=  '</div></div>';
  }
  $data .= '</ul>';


  $query = "SELECT COUNT(kb_entries.id) as c FROM kb_entries WHERE kb_entries.tags LIKE '%,$q,%' OR kb_entries.tags LIKE '%,$q' OR kb_entries.tags LIKE '$q' OR kb_entries.tags LIKE '$q,%'";
  $result = $connection->query($query);
  $row = $result->fetch_assoc();

  $data .= paginateNav($row['c']);


  return $data;
}
