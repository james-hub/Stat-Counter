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

include_once "init.php";

$log->CreateTable($myglobals['mysql']['drop']);
$imgs->CreateTable($myglobals['mysql']['drop']);
$gets->CreateTable($myglobals['mysql']['drop']);
$vars->CreateTable($myglobals['mysql']['drop']);
$queries->CreateTable($myglobals['mysql']['drop']);

if ($myglobals['mysql']['drop'])
  mysql_query_debug("drop table {$myglobals['mysql']['prefix']}_ip");

mysql_query_debug(
"create table {$myglobals['mysql']['prefix']}_ip
 (ip_fr integer, ip_to integer, code2 char(2), code3 char(3), name varchar(50))");

echo "Setup complete! ";

?>
