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


include_once "../api/init.php";

if (isset($_COOKIE["sc_blockcookie"]) && $_COOKIE["sc_blockcookie"] == 'Y') die;

$data = Array("ref"       => null,
              "url"       => null,
              "platform"  => null,
              "agent"     => null,
              "screenx"   => null,
              "screeny"   => null);

foreach ($data as $i => $x) if (!isset($_GET[$i])) die;
foreach ($data as $i => $x) $data[$i] = $_GET[$i];

$data["ip" ] = $_SERVER["REMOTE_ADDR"];

if (isset($_COOKIE['sc1980isl_data']))
{
  $cookie = unserialize($_COOKIE['sc1980isl_data']);

  $data["uid"]       = $cookie["uid"];
  $data["returning"] = (time() > $cookie["time"]+3600) ? 1 : 0;
}
else
{
  $data["uid"]       = mt_rand(0, 127*256*256*256);
  $data["returning"] = 0;
}

$cookie = Array("uid" => $data["uid"], "time" => time());
setcookie("sc1980isl_data", serialize($cookie), time()+365*24*3600, "/");
setcookie("sc1980isl_data", serialize($cookie), time()+365*24*3600, "/");

if ($myglobals['mysql']['rawdata']) $gets->AddData(serialize($data));
$log->AddToLog($data);

?>
