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

if (isset($_GET['delete'])) $queries->Delete($_GET['delete']);

?>

<h1>Saved queries</h1>

<table class="sitestats">

<tr>
<th>Query </th>
<th>Action</th>
</tr>

<?php

$class  = 'class="odd"';
$result = $queries->GetAllData();

foreach ($result as $name => $query)
{
  ?>
  <tr>
  <td <?=$class;?>><?=htmlentities($name);?></td>
  <td <?=$class;?>>
  <a href="chart.php?stored=<?=urlencode($name);?>">[view]</a>
  <a href="query.php?stored=<?=urlencode($name);?>">[edit]</a>
  <a href="stored.php?delete=<?=urlencode($name);?>">[delete]</a>
  </td>
  </tr>
  <?php

  $class = $class == 'class="odd"' ? 'class="even"' : 'class="odd"';
}

?>

</table>

<?php include_once "api/footer.php"; ?>
