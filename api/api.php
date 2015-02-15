<?php

// Copyright (C) 2005 Ilya S. Lyubinskiy. All rights reserved.
// Technical support: http://www.php-development.ru/
//
// YOU MAY NOT
// (1) Remove or modify this copyright notice.
// (2) Distribute this code, any part or any modified version of it.
//     Instead, you can link to the homepage of this code:
//     http://www.php-development.ru/php-scripts/site-statistics.php
// (3) Use this code, any part or any modified version of it as a part of
//     another product. If you want to do so you should receive my permission.
//
// YOU MAY
// (1) Use this code on your website.
//
// NO WARRANTY
// This code is provided "as is" without warranty of any kind, either
// expressed or implied, including, but not limited to, the implied warranties
// of merchantability and fitness for a particular purpose. You expressly
// acknowledge and agree that use of this code is at your own risk.

// If you find this script useful, you can support my site in the following
// ways:
// 1. Vote for the script at HotScripts.com (you can do it on my site)
// 2. Link to the homepage of this script or to the homepage of my site:
//    http://www.php-development.ru/php-scripts/site-statistics.php
//    http://www.php-development.ru/
//    You will get 50% commission on all orders made by your referrals.
//    More information can be found here:
//    http://www.php-development.ru/affiliates.php

include_once "mylib.php";

// ----- TVarTable -------------------------------------------------------------

class TVarTable
{
  var $tbl = null;

  // ----- Init -----

  function Init($tbl) { $this->tbl = $tbl; }

  // ----- CreateTable -----

  function CreateTable($drop = false)
  {
    if ($drop) mysql_query_debug("drop table {$this->tbl}");
    mysql_query_debug(
    "create table {$this->tbl}
     (name varchar(32) not null unique, value text not null)");
  }

  // ----- Assign -----

  function Assign($name, $value)
  {
    $name   = mysql_real_escape_string($name);
    $value  = mysql_real_escape_string($value);
    $result = mysql_query_debug("select * from {$this->tbl} where name = '$name'");

    if (!mysql_num_rows($result))
         mysql_query_debug("insert into {$this->tbl} values ('$name', '$value')");
    else mysql_query_debug("update {$this->tbl} set value = '$value' where name = '$name'");
  }

  // ----- Delete -----

  function Delete($name)
  {
    $name = mysql_real_escape_string($name);
    mysql_query_debug("delete from {$this->tbl} where name = '$name'");
  }

  // ----- Get -----

  function Get($name)
  {
    $name   = mysql_real_escape_string($name);
    $result = mysql_query_debug("select * from {$this->tbl} where name = '$name'");
    $row    = mysql_fetch_assoc($result);

    return $row ? $row['value'] : '';
  }

  // ----- GetAllData -----

  function GetAllData()
  {
    $result = mysql_query_debug("select * from {$this->tbl} order by name");

    $data = Array();
    while($row = mysql_fetch_assoc($result)) $data[$row['name']] = $row['value'];
    return $data;
  }

  // ----- Inc -----

  function Inc($name)
  {
    $name   = mysql_real_escape_string($name);
    $result = mysql_query_debug("select * from {$this->tbl} where name = '$name'");
    $row    = mysql_fetch_assoc($result);

    mysql_query_debug("update {$this->tbl} set value = value+1 where name = '$name'");

    return $row ? $row['value'] : '';
  }
}


// ----- TStringTable ----------------------------------------------------------

class TStringTable
{
  var $tbl    = null;
  var $prefix = null;

  // ----- Init -----

  function Init($tbl, $prefix)
  {
    $this->tbl    = $tbl;
    $this->prefix = $prefix;
  }

  // ----- CreateTable -----

  function CreateTable($drop = false)
  {
    if ($drop) mysql_query_debug("drop table {$this->tbl}");
    mysql_query_debug(
    "create table {$this->tbl}
     ({$this->prefix}id   smallint unsigned not null auto_increment,
      {$this->prefix}data text              not null,
      primary key ({$this->prefix}id))");
  }

  // ----- AddData -----

  function AddData($data)
  {
    if ($data == '') return 0;
    $data = mysql_real_escape_string($data);
    mysql_query_debug("insert into {$this->tbl} ({$this->prefix}data) values ('$data')");

    $result = mysql_query_debug(
              "select * from {$this->tbl} where {$this->prefix}data = '$data'
               order by {$this->prefix}id desc limit 1");
    $row    = mysql_fetch_assoc($result);
    return $row ? $row["{$this->prefix}id"] : false;
  }

  // ----- ClearData -----

  function ClearData($id)
  {
    $id = (integer)$id;
    if ($id == 0) return '';

    mysql_query_debug("delete from {$this->tbl} where {$this->prefix}id = $id");
  }

  // ----- GetData -----

  function GetData($id)
  {
    $id = (integer)$id;
    if ($id == 0) return '';

    $result = mysql_query_debug("select * from {$this->tbl} where {$this->prefix}id = $id");
    $row    = mysql_fetch_assoc($result);
    return $row ? $row["{$this->prefix}data"] : false;
  }

  // ----- GetAllData -----

  function GetAllData()
  {
    $result = mysql_query_debug("select {$this->prefix}id id, {$this->prefix}data data
                                 from {$this->tbl} order by id");

    $data = Array();
    while($row = mysql_fetch_assoc($result)) $data[$row['id']] = $row['data'];
    return $data;
  }

  // ----- Limit -----

  function Limit($size)
  {
    mysql_query_debug(
    "select @id := {$this->prefix}id from {$this->tbl}
     order by {$this->prefix}id desc limit $size");
    mysql_query_debug(
    "delete from {$this->tbl} where {$this->prefix}id < @id");
  }
}


// ----- THashedStringTable ----------------------------------------------------

class THashedStringTable
{
  var $tbl    = null;
  var $prefix = null;

  // ----- Init -----

  function Init($tbl, $prefix)
  {
    $this->tbl    = $tbl;
    $this->prefix = $prefix;
  }

  // ----- CreateTable -----

  function CreateTable($drop = false)
  {
    if ($drop) mysql_query_debug("drop table {$this->tbl}");
    mysql_query_debug(
    "create table {$this->tbl}
     ({$this->prefix}id     smallint  unsigned not null auto_increment,
      {$this->prefix}hash   smallint  unsigned not null,
      {$this->prefix}data   text               not null,
      {$this->prefix}weight mediumint unsigned not null,
      index {$this->prefix}hash ({$this->prefix}hash),
      primary key ({$this->prefix}id))");
  }

  // ----- AddUniqueData -----

  function AddUniqueData($data)
  {
    if ($data == '') return 0;

    $hash = '0x' . substr(md5($data), 0, 4);
    $data = mysql_real_escape_string($data);

    $result = mysql_query_debug(
              "select * from {$this->tbl} where
               {$this->prefix}hash = $hash && {$this->prefix}data = '$data'
               limit 1");

    if (!mysql_num_rows($result))
    {
      mysql_query_debug(
      "insert into {$this->tbl} ({$this->prefix}hash, {$this->prefix}data)
       values ($hash, '$data')");

      $result = mysql_query_debug(
                "select * from {$this->tbl} where
                 {$this->prefix}hash = $hash && {$this->prefix}data = '$data'
                 limit 1");
    }

    $id = mysql_fetch_assoc($result);
    $id = $id["{$this->prefix}id"];

    mysql_query_debug("update {$this->tbl}
                       set {$this->prefix}weight = {$this->prefix}weight+1
                       where {$this->prefix}id = $id");

    return $id;
  }

  // ----- GetAllData -----

  function GetAllData()
  {
    $result = mysql_query_debug(
              "select {$this->prefix}id id, {$this->prefix}data data
               from {$this->tbl} order by {$this->prefix}weight desc");

    $data = Array();
    while($row = mysql_fetch_assoc($result)) $data[$row['id']] = $row['data'];
    return $data;
  }
}


// ----- TLogTable -------------------------------------------------------------

class TLogTable
{
  var $tbl;

  var $tbls = Array('country'     => null,
                    'ref_domain'  => null,
                    'ref_url'     => null,
                    'url'         => null,
                    'platform'    => null,
                    'agent'       => null,
                    'screen'      => null);

  // ----- Init -----

  function Init($tbl)
  {
    $this->tbl = $tbl;
    foreach ($this->tbls as $i => $x)
    {
      $this->tbls[$i] = new THashedStringTable();
      $this->tbls[$i]->Init("{$tbl}_{$i}", "{$i}_");
    }
  }

  // ----- CreateTable -----

  function CreateTable($drop = false)
  {
    if ($drop) mysql_query_debug("drop table {$this->tbl}_log");
    mysql_query_debug(
    "create table {$this->tbl}_log
     (id         mediumint unsigned not null auto_increment,

      uid        integer      unsigned not null,
      returning  smallint              not null,

      ip         integer      unsigned not null,
      country2   varchar(2)            not null,
      country    smallint     unsigned not null,

      ref_domain smallint     unsigned not null,
      ref_url    smallint     unsigned not null,
      url_path   varchar(254)          not null,
      pageloads  smallint     unsigned not null,

      platform   tinyint      unsigned not null,
      agent      tinyint      unsigned not null,
      screen     tinyint      unsigned not null,

      time_entry integer      unsigned not null,
      time_exit  integer      unsigned not null,

      group_ref  bigint       unsigned not null,
      group_url  bigint       unsigned not null,

      primary key (id))");

    if ($drop) mysql_query_debug("drop table {$this->tbl}_daily");
    mysql_query_debug(
    "create table {$this->tbl}_daily
     (daily_id            integer unsigned not null auto_increment,

      daily_unique        integer unsigned not null,
      daily_returning     integer unsigned not null,
      daily_pageloads     integer unsigned not null,
      daily_time_spent    integer unsigned not null,

      primary key (daily_id))");

    foreach ($this->tbls as $i => $x) $this->tbls[$i]->CreateTable($drop);
  }

  // ----- AddToLog -----

  function AddToLog($data)
  {
    $exec = microtime(true);

    // Adjust URLs

    $data['url'] = url_adjust($data['url'],
                              $GLOBALS['myglobals']['url_strip_www'   ],
                              $GLOBALS['myglobals']['url_append_www'  ],
                              $GLOBALS['myglobals']['url_strip_index' ],
                              $GLOBALS['myglobals']['url_append_index']);

    $data['ref'] = url_adjust($data['ref'],
                              $GLOBALS['myglobals']['ref_strip_www'   ],
                              $GLOBALS['myglobals']['ref_append_www'  ],
                              $GLOBALS['myglobals']['ref_strip_index' ],
                              $GLOBALS['myglobals']['ref_append_index']);

    if (!$data['ref'])
    {
      $data['ref_domain'] = 'No referring link';
      $data['ref']        = 'No referring link';
    }
    else $data['ref_domain'] = url_domain($data['ref'], true);

    foreach ($GLOBALS['myglobals']['domains'] as $i => $x)
      if ($data['ref_domain'] == $i)
      {
        $data['ref_domain'] = 'No referring link';
        $data['ref']        = 'No referring link';
      }

    // Data

    $uid    = (integer)$data['uid'];
    $time   = time();
    $url    = mysql_real_escape_string($this->tbls['url']->AddUniqueData($data['url']));
    $url_ch = mysql_real_escape_string(chr($url%256) . chr((integer)($url/256)));

    // Check if Log exists

    if (!$data['returning'])
      $result = mysql_query_debug("select * from {$this->tbl}_log where uid = $uid
                                   order by id desc limit 1");

    // Add Log

    if ($data['returning'] || !mysql_num_rows($result))
    {
      $ip = explode('.', $data['ip']);
      foreach ($ip as $i => $x) $ip[$i] = (integer)$x;
      $ip = "({$ip[0]}*255*255*255+{$ip[1]}*255*255+{$ip[2]}*255+{$ip[3]})";

      if ($GLOBALS["myglobals"]['mysql']['ipdb'])
      {
        $result = mysql_query_debug(
                  "select * from {$GLOBALS['myglobals']['mysql']['prefix']}_ip
                   where $ip > ip_fr && $ip < ip_to");
        $row    = mysql_fetch_assoc($result);

        if ($row)
        {
          $country  = $this->tbls['country']->AddUniqueData($row["name" ]);
          $country2 = $row["code2"];
        }
        else
        {
          $country  = $this->tbls['country']->AddUniqueData("Unknown");
          $country2 = "99";
        }
      }

      $returning  = $data['returning'] ? 1 : 0;
      $ref_domain = $this->tbls['ref_domain']->AddUniqueData($data['ref_domain']);
      $ref_url    = $this->tbls['ref_url'   ]->AddUniqueData($data['ref'       ]);
      $platform   = $this->tbls['platform'  ]->AddUniqueData($data['platform'  ]);
      $agent      = $this->tbls['agent'     ]->AddUniqueData($data['agent'     ]);
      $screen     = $this->tbls['screen'    ]->AddUniqueData((integer)$data['screenx'] . '*' . (integer)$data['screeny']);

      mysql_query_debug(
      "insert into {$this->tbl}_log
       ( uid,  returning,  ip,  country,    country2,  ref_domain,  ref_url,
         platform,  agent,  screen, time_entry)
       values
       ($uid, $returning, $ip, $country, '$country2', $ref_domain, $ref_url,
        $platform, $agent, $screen, $time)");

      $result = mysql_query_debug("select * from {$this->tbl}_log where uid = $uid
                                   order by id desc limit 1");
    }

    // Update Log

    $row = mysql_fetch_assoc($result);

    mysql_query_debug("update {$this->tbl}_log set
                       url_path  = concat(url_path, '$url_ch'),
                       pageloads = pageloads+1,
                       time_exit = $time
                       where id = {$row['id']}");

    return microtime(true)-$exec;
  }

  // ----- StatsSubset -----

  function StatsSubset($limits)
  {
    foreach ($limits as $i => $x) $limits[$i] = (integer)$x;

    $where = Array();
    $names = Array("country", "ref_domain", "ref_url", "platform", "agent");

    foreach ($names as $i => $x)
      if (isset($limits[$x])) $where[] = "$x = {$limits[$x]}";

    if (isset($limits['pageloads_min']))
      $where[] = "pageloads >= {$limits['pageloads_min']}";
    if (isset($limits['pageloads_max']))
      $where[] = "pageloads <= {$limits['pageloads_max']}";

    if (isset($limits['time_min']))
      $where[] = "time_entry >= {$limits['time_min']}";
    if (isset($limits['time_max']))
      $where[] = "time_entry <= {$limits['time_max']}";

    if (isset($limits['url']))
    {
      $url_ch  = mysql_real_escape_string(chr($limits['url']%256) . chr((integer)($limits['url']/256)));
      $where[] = "url_path REGEXP '$url_ch'";
    }

    return $where;
  }

  // ----- ShowLog -----

  function ShowLog($from, $count)
  {
    $result = mysql_query(
              "select id, returning, ip, time_entry, pageloads, country2,
                      country_data     country,
                      ref_domain_data  referrer,
                      platform_data    platform,
                      agent_data       browser,
                      screen_data      resolution
               from {$this->tbl}_log,
                    {$this->tbl}_country,
                    {$this->tbl}_ref_domain,
                    {$this->tbl}_platform,
                    {$this->tbl}_agent,
                    {$this->tbl}_screen
               where country_id    = country    &&
                     ref_domain_id = ref_domain &&
                     platform_id   = platform   &&
                     agent_id      = agent      &&
                     screen_id     = screen
               order by id desc limit $from, $count");

    $data = Array();
    while($row = mysql_fetch_assoc($result)) $data[] = $row;
    return $data;
  }

  // ----- PieStatsFromLog -----

  function PieStatsFromLog($stats, $by_visitors, $limits)
  {
    $where  = $this->StatsSubset($limits);
    $weight = $by_visitors ? 'count(*)' : 'sum(pageloads)';
    $names  = Array("agent", "country", "ref_domain", "ref_url", "platform", "screen");

    if (in_array($stats, $names))
    {
      $where[] = "{$stats}_id = $stats";
      $where   = 'where ' . implode(' && ', $where);
      $result  = mysql_query_debug(
                 "select {$stats}_data data, $weight weight
                  from {$this->tbl}_log, {$this->tbl}_$stats
                  $where group by $stats order by weight desc");
    }
    else
    {
      $where = count($where) ? 'where ' . implode(' && ', $where) : '';
      if ($stats == "pageloads")
        $result = mysql_query_debug(
                  "select pageloads data, $weight weight
                   from {$this->tbl}_log
                   $where group by pageloads order by weight desc");
      elseif ($stats == "visit_length")
        $result = mysql_query_debug(
                  "select round((time_exit-time_entry)/10) data, $weight weight
                   from {$this->tbl}_log
                   $where group by data order by weight desc");
      else return false;
    }

    $data = Array();
    while($row = mysql_fetch_assoc($result)) $data[] = $row;
    return $data;
  }

  // ----- BarStatsFromLog -----

  function BarStatsFromLog($stats, $interval, $limits)
  {
    $where = $this->StatsSubset($limits);
    $where = count($where) ? 'where ' . implode(' && ', $where) : '';

    if     ($stats == "general"      ) $weight = "sum(pageloads) pageloads_day, count(*) unique_day, sum(returning) returning_day";
    elseif ($stats == "visit_length" ) $weight = "round(avg(time_exit-time_entry)/60, 1) $stats";
    elseif ($stats == "pageloads_day") $weight = "sum(pageloads) $stats";
    elseif ($stats == "pageloads_vis") $weight = "round(avg(pageloads), 1) $stats";
    elseif ($stats == "returning"    ) $weight = "sum(returning) $stats";
    elseif ($stats == "uniques"      ) $weight = "count(*) $stats";
    else return false;

    $interval = (integer)$interval;

    $result = mysql_query_debug(
              "select avg(time_entry) time, $weight from {$this->tbl}_log
               $where group by truncate(time_entry/$interval, 0) order by time");

    $data = Array();
    while($row = mysql_fetch_assoc($result))
    {
      $row['time'] = date("D, j M Y", $row['time']);
      $data[]      = $row;
    }
    return $data;
  }

  // ----- Limit -----

  function Limit($log, $daily)
  {
    mysql_query_debug(
    "select @id := id from {$this->tbl}_log order by id desc limit $log");
    mysql_query_debug("delete from {$this->tbl}_log where id < @id");

    mysql_query_debug(
    "select @id := daily_id from {$this->tbl}_daily
     order by daily_id desc limit $daily");
    mysql_query_debug("delete from {$this->tbl}_daily where daily_id < @id");
  }
}

// ----- Auxiliary -------------------------------------------------------------

// ----- PieChart -----

function PieChart($sectors, $titles)
{
  $colors   = $GLOBALS['myglobals']['colors' ];
  $shadows  = $GLOBALS['myglobals']['shadows'];

  $sheet    = Array('color'          => 0xF0F0F0,
                    'border_color'   => 0xA0A0A0,
                    'shadow_color'   => 0x606060,
                    'shadow_width'   => 3,
                    'padding-top'    => 30,
                    'padding-right'  => 20,
                    'padding-bottom' => 30,
                    'padding-left'   => 40);

  $chart    = Array('color'  => 0x202020,
                    'width'  => 300,
                    'height' => 180,
                    'shadow' => 15);

  $titlebox = Array('color' => 0x202020,
                    'paddingx' => 7,
                    'paddingy' => 5,
                    'height'   => 9,
                    'offset'   => 6,
                    'sheet'    => $sheet,
                    'font'     => $GLOBALS['myglobals']['font']);

  draw_piechart_advanced($image, $sheet, $chart, $titlebox, $sectors, $colors, $shadows, $titles);
  return $GLOBALS['imgs']->AddData(imagepng_str($image));
}

// ----- BarChart -----

function BarChart($sectors, $titles)
{
  $colors   = $GLOBALS['myglobals']['colors' ];

  $sheet    = Array('color'          => 0xF0F0F0,
                    'border_color'   => 0xA0A0A0,
                    'shadow_color'   => 0x606060,
                    'shadow_width'   => 3,
                    'padding-top'    => 20,
                    'padding-right'  => 20,
                    'padding-bottom' => 20,
                    'padding-left'   => 20);

  $chart    = Array('color'      => 0x202020,
                    'width'      => 200,
                    'height'     => 180,
                    'shadow'     => 15,
                    'bar_width'  => 10,
                    'bar_offset' => 5);

  $titlebox = Array('color'    => 0x202020,
                    'paddingx' => 7,
                    'paddingy' => 5,
                    'height'   => 9,
                    'offset'   => 6,
                    'sheet'    => $sheet,
                    'font'     => $GLOBALS['myglobals']['font']);

  draw_barchart_advanced($image, $sheet, $chart, $titlebox, $sectors, $colors, $titles);
  return $GLOBALS['imgs']->AddData(imagepng_str($image));
}

// ----- DataConvertByRules -----

function DataConvertByRules($rules, $data)
{
  $result = Array();
  $rules2 = Array();

  foreach ($rules as $i => $x)
    $result[$i] = Array('data'  => $x, 'weight' =>  0);

  foreach ($data as $i => $x)
    foreach ($rules as $j => $y)
      if ($x['data'] <= $j)
      {
        $result[$j]['weight'] += $x['weight'];
        break;
      }

  foreach($result as $i => $x) if (!$x['weight']) unset($result[$i]);

  return $result;
}

// ----- DataConvertByCount -----

function DataConvertByCount($count, $data)
{
  if (count($data) <= $count) return $data;

  $result = Array();
  $n = 0;

  foreach ($data as $i => $x)
  {
    if ($n <  $count-1) $result[$n] = $x;
    if ($n == $count-1) $result[$count-1] = Array('data' => 'Other', 'weight' => $x['weight']);
    if ($n >  $count-1) $result[$count-1]['weight'] += $x['weight'];
    $n++;
  }

  return $result;
}

// ----- DataTable -----

function DataTable($sectors, $titles)
{
  echo '<table class="sitestats">';

  echo '<tr>';
  foreach ($titles as $i => $x) echo '<th>' . htmlentities($x) . '</th>';
  echo '</tr>';

  $class = 'class="odd"';

  foreach ($sectors as $i => $entry)
  {
    echo '<tr>';
    foreach ($entry as $j => $x) echo "<td $class>" . htmlentities($x) . '</td>';
    echo '</tr>';

    $class = $class == 'class="odd"' ? 'class="even"' : 'class="odd"';
  }

  echo '</table>';
}

?>
