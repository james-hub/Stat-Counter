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


include_once "api/init.php";
include_once "api/header.php";

if (isset($_GET['stored']))
{
  $_POST = unserialize($queries->Get($_GET['stored']));
  $_GET["type"] = $_POST["chart"];
}

?>

<h1>
<?php
if ($_GET["type"] == "bar") echo "Bar Chart - ";
if ($_GET["type"] == "pie") echo "Pie Chart - ";
?>
Build Query
</h1>

<form method="post" action="chart.php">

<table class="form">

<tr><th colspan="2">Select stats:</th></tr>


<?php
// ----- JavaScript ------------------------------------------------------------
?>

<script type="text/javascript">

function change_type(type)
{
  for (var i = 0; i < document.forms[0].elements.length; i++)
    if (document.forms[0].elements[i].name == "method")
      document.forms[0].elements[i].checked =
        document.forms[0].elements[i].value == type;
}

</script>


<?php
// ----- Bar chart -------------------------------------------------------------
?>

<?php if ($_GET["type"] == "bar") { ?>

<tr>
<td>Bar&nbsp;chart:</td>
<td>

<input type="hidden" name="chart" value="bar" />

<select class="form" name="stats">
<?php
foreach($myglobals['stats_bar'] as $i => $x)
  echo "<option value=\"$i\" " . __selected('stats', $i) . ">$x</option>";
?>
</select>

</td>
</tr>

<?php } ?>


<?php
// ----- Pie chart -------------------------------------------------------------
?>

<?php if ($_GET["type"] == "pie") { ?>

<tr>
<td>Pie&nbsp;chart:</td>
<td>

<input type="hidden" name="chart" value="pie" />

<select class="form" name="stats">
<?php
foreach($myglobals['stats_pie'] as $i => $x)
  echo "<option value=\"$i\" " . __selected('stats', $i) . ">$x</option>";
?>
</select>

</td>
</tr>

<?php } ?>


<?php
// ----- Weight ----------------------------------------------------------------
?>

<tr>
  <td>Weigh&nbsp;by:</td>
  <td>
  <select class="form" name="weight">
  <option value="1" <?=__selected('weight', 1);?>>Unique Visitors</option>
  <option value="0" <?=__selected('weight', 0);?>>Pageloads      </option>
  </select>
  </td>
</tr>


<!----- Time frames ----------------------------------------------------------->

<tr><th colspan="2">Select time frames:</th></tr>

<?php
// ----- Simple -----
?>

<tr>
<td>
<input type="radio" name="method" value="simple" <?=__checked("method", "simple");?> />
Last X days:
</td>
<td>
<select name="interval" onchange="change_type('simple');">
<option value="0-1"  <?=__selected("interval", "0-1");?>>Today        </option>
<option value="1-2"  <?=__selected("interval", "1-2");?>>Yesterday    </option>
<option value="0-3"  <?=__selected("interval", "0-3");?>>Last 3 days  </option>
<option value="0-7"  <?=__selected("interval", "0-7");?>>Last 7 days  </option>
<option value="0-30" <?=__selected("interval", "0-30");?>>Last 30 days</option>
</select>
</td>
</tr>

<tr>
<td>
<input type="radio" name="method" value="start-end" <?=__checked("method", "start-end");?> />
Time&nbsp;interval:
</td>
<td>

<?php
// ----- Interval Start -----
?>

<select name="day1" onchange="change_type('start-end');">
<?php
for ($i = 1; $i <= 31; $i++)
  echo "<option value=\"$i\"" . __selected("day1", $i) . ">$i</option>";
?>
</select>
<select name="month1" onchange="change_type('start-end');">
<?php
$months = Array(1 => "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
foreach ($months as $i => $month)
  echo "<option value=\"$i\"" . __selected("month1", $i) . ">$month</option>";
?>
</select>
<select name="year1" onchange="change_type('start-end');">
<?php
for ($i = 2006; $i <= 2010; $i++)
  echo "<option value=\"$i\"" . __selected("year1", $i) . ">$i</option>";
?>
</select>

-

<?php
// ----- Interval End -----
?>

<select name="day2" onchange="change_type('start-end');">
<?php
for ($i = 1; $i <= 31; $i++)
  echo "<option value=\"$i\"" . __selected("day2", $i) . ">$i</option>";
?>
</select>
<select name="month2" onchange="change_type('start-end');">
<?php
$months = Array(1 => "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
foreach ($months as $i => $month)
  echo "<option value=\"$i\"" . __selected("month2", $i) . ">$month</option>";
?>
</select>
<select name="year2" onchange="change_type('start-end');">
<?php
for ($i = 2006; $i <= 2010; $i++)
  echo "<option value=\"$i\"" . __selected("year2", $i) . ">$i</option>";
?>
</select>

</td>
</tr>


<!----- User subset ----------------------------------------------------------->

<tr><th colspan="2">Select subset of users:</th></tr>

<!----- Country ----->

<tr>
<td>Country:</td>
<td>

<select class="form" name="country">
<option value="0"></option>
<?php
$data = $log->tbls['country']->GetAllData();
foreach ($data as $id => $value)
  echo "<option value=\"$id\" " . __selected('country', $id) . ">$value</option>";
?>
</select>

</td>
</tr>

<!----- Referrer domain ----->

<tr>
<td>Referrer&nbsp;domain:</td>
<td>

<select class="form" name="ref_domain">
<option value="0"></option>
<?php
$data = $log->tbls['ref_domain']->GetAllData();
foreach ($data as $id => $value)
  echo "<option value=\"$id\" " . __selected('ref_domain', $id) . ">$value</option>";
?>
</select>

</td>
</tr>

<!----- Referrer URL ----->

<tr>
<td>Referrer&nbsp;URL:</td>
<td>

<select class="form" name="ref_url">
<option value="0"></option>
<?php
$data = $log->tbls['ref_url']->GetAllData();
foreach ($data as $id => $value)
  echo "<option value=\"$id\" " . __selected('ref_url', $id) . ">$value</option>";
?>
</select>

</td>
</tr>

<!----- Visited page ----->

<tr>
<td>Visited&nbsp;page:</td>
<td>

<select class="form" name="url">
<option value="0"></option>
<?php
$data = $log->tbls['url']->GetAllData();
foreach ($data as $id => $value)
  echo "<option value=\"$id\" " . __selected('url', $id) . ">$value</option>";
?>
</select>

</td>
</tr>

<!----- Pageloads ----->

<tr>
<td>Pageloads:</td>
<td>

from

<select name="pageloads_min">
<option value="0" <?=__selected('pageloads_min', 0);?>></option>
<?php
$pageloads = Array(1, 2, 3, 5, 10, 15, 20);
foreach ($pageloads as $i => $x)
  echo "<option value=\"$x\" " . __selected('pageloads_min', $x) . ">$x</option>";
?>
</select>

to

<select name="pageloads_max">
<option value="0"></option>
<?php
$pageloads = Array(1, 2, 3, 5, 10, 15, 20);
foreach ($pageloads as $i => $x)
  echo "<option value=\"$x\" " . __selected('pageloads_max', $x) . ">$x</option>";
?>
</select>

</td>
</tr>


<!----- Save & Submit --------------------------------------------------------->

<tr><td style="line-height: 8px;" colspan="2">&nbsp;</td></tr>

<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Submit" /></td>
</tr>

<tr><td style="line-height: 8px;" colspan="2">&nbsp;</td></tr>

<tr>
<td>Store&nbsp;query&nbsp;as:</td>
<td>
<input class="form" name="query_name" value="<?=__post("query_name");?>" />
&nbsp; &nbsp;
<input type="checkbox" name="to_menu" <?=__checked("to_menu");?> />
<small>Add to menu</small>
</td>
</tr>

<tr>
<td>Notes:</td>
<td>
<textarea class="form" name="query_notes" value="" /><?=__post("query_notes");?></textarea>
<br>
</td>
</tr>

</table>
</form>

<?php include_once "api/footer.php"; ?>
