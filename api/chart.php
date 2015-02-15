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



if (isset($_POST['index'])) echo "<h1>Home</h1>";

// ----- Limits ----------------------------------------------------------------

$limits = Array();

$names  = Array("country", "ref_domain", "ref_url", "url", "pageloads_min", "pageloads_max");

foreach ($names as $i => $name)
  if (isset($_POST[$name]) && (integer)$_POST[$name])
    $limits[$name] = (integer)$_POST[$name];

if (isset($_POST["method"]) && $_POST["method"] == "simple")
{
  list($end, $start) = explode("-", $_POST["interval"]);
  $time  = (integer)(time()/24/3600);
  $limits['time_min'] = 24*3600*($time-$start);
  $limits['time_max'] = 24*3600*($time-$end);
}

if (isset($_POST["method"]) && $_POST["method"] == "start-end")
{
  $limits['time_min'] = mktime(0, 0, 0, $_POST["month1"], $_POST["day1"], $_POST["year1"]);
  $limits['time_max'] = mktime(0, 0, 0, $_POST["month2"], $_POST["day2"], $_POST["year2"])+24*3600;
}


// ----- Pie Chart -------------------------------------------------------------

if (isset($_POST['chart']) && $_POST['chart'] == 'pie')
{
  $result = $log->PieStatsFromLog($_POST['stats'], $_POST['weight'], $limits);

  // ----- Adjust data -----

  if ($_POST['stats'] == 'pageloads')
    $result = DataConvertByRules($myglobals['convert_pageloads'], $result);

  if ($_POST['stats'] == 'time')
    $result = DataConvertByRules($myglobals['convert_timespent'], $result);

  // ----- Sectors -----

  $sectsum = 0;
  $sectors = Array();
  $titles  = Array();

  foreach (DataConvertByCount($myglobals['stats_pie_nchart'], $result) as $i => $entry)
  {
    $sectsum  += $entry['weight'];
    $sectors[] = $entry['weight'];
    $titles [] = $entry['data'];
  }

  foreach ($sectors as $i => $x) $sectors[$i] = 100*$x/$sectsum;

  // ----- Output -----

  $ImageId = piechart($sectors, $titles);

  if (!isset($_POST['index'])) echo "<h1>" . $myglobals['stats_pie'][$_POST['stats']] . " Stats</h1>";
  if (isset($_POST['notes'])) echo "<div class=\"text\">{$_POST['notes']}</div>";
  echo "<img class=\"stats\" src=\"tools/image.php?id=$ImageId\" />";

  $titles = Array($myglobals['stats_pie'][$_POST['stats']], $_POST['weight'] ? 'Visitors' : 'Pageloads');

  datatable(DataConvertByCount($myglobals['stats_pie_ntable'], $result), $titles);
}


// ----- Bar Chart -------------------------------------------------------------

if (isset($_POST['chart']) && $_POST['chart'] == 'bar')
{
  $result = $log->BarStatsFromLog($_POST['stats'], 24*3600, $limits);

  if ($_POST['stats'] == "general")
       $titles = Array("Pageloads", "Unique Visitors", "Returning Visitors");
  else $titles = Array($myglobals['stats_bar'][$_POST['stats']]);

  $sectors = Array();

  foreach ($result as $i => $x)
  {
    unset($x['time']);
    $sectors[] = array_values($x);
  }

  $ImageId = barchart($sectors, $titles);

  if (!isset($_POST['index'])) echo "<h1>" . $myglobals['stats_bar'][$_POST['stats']] . " Stats</h1>";
  if (isset($_POST['notes'])) echo "<div class=\"text\">{$_POST['notes']}</div>";
  echo "<img class=\"stats\" src=\"tools/image.php?id=$ImageId\" />";

  array_unshift($titles, 'Time');
  datatable($result, $titles);
}

?>
