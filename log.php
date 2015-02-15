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

?>

<h1>Log</h1>

<table class="sitestats">

<tr>
<th>Id&nbsp;#      </th>
<th>Country        </th>
<th>Entry&nbsp;Time</th>
<th>Referrer       </th>
<th>Pageloads      </th>
<th>Platform       </th>
<th>Browser        </th>
<th>Resolution     </th>
</tr>

<?php

function data_prepare($data)
{
  return str_replace(' ', '&nbsp;', htmlentities($data));
}

$class  = 'class="odd"';
$result = $log->ShowLog(0, 100);

foreach ($result as $i => $entry)
{
  ?>
  <tr>
    <td <?=$class;?>><?=data_prepare($entry['id']);?></td>
    <td <?=$class;?>>
    <?php
    if ($entry['country2'] != '99' && $entry['country2'] != '')
      echo "<img src=\"flags/" . strtolower($entry['country2']) . ".png\">";
    ?>
    </td>
    <td <?=$class;?>><?=data_prepare(date("D, j M Y H:i", $entry['time_entry']));?></td>
    <td <?=$class;?>><?=data_prepare($entry['referrer'  ]);?></td>
    <td <?=$class;?>><?=data_prepare($entry['pageloads' ]);?></td>
    <td <?=$class;?>><?=data_prepare($entry['platform'  ]);?></td>
    <td <?=$class;?>><?=data_prepare($entry['browser'   ]);?></td>
    <td <?=$class;?>><?=data_prepare($entry['resolution']);?></td>
  </tr>
  <?php

  $class = $class == 'class="odd"' ? 'class="even"' : 'class="odd"';
}

?>

</table>

<?php include_once "api/footer.php"; ?>
