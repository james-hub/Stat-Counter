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


// ----- Configuration ---------------------------------------------------------

// ----- Domain activation code -----

$myglobals['domains'] = Array('domain.com' => '', 'www.domain.com' => '');
// Replace domain.com with your domain name. This will exclude your domain
// from referres.

// ----- Paths -----

$myglobals['dir_root'] = "{$_SERVER['DOCUMENT_ROOT']}/sitestats";
// File system path to sitestats directory
$myglobals['src_root'] = "http://www.domain.com/sitestats/";
// Web address of sitestats directory

// ----- MySQL configuration -----

$myglobals['mysql']['host']  = 'localhost';
$myglobals['mysql']['user']  = '';
$myglobals['mysql']['pass']  = '';
$myglobals['mysql']['name']  = '';
// Database information

$myglobals['mysql']['prefix']  = "sitestats";
// Select prefix for all tables
$myglobals['mysql']['debug']   = true;
// Show database query errors
$myglobals['mysql']['drop']    = false;
// Setting $myglobals['mysql']['drop'] will allow replacing tables
// that already exist. If not sure, set it to false
$myglobals['mysql']['rawdata'] = false;
// Log raw data.
$myglobals['mysql']['ipdb']    = true;
// Use ip-to-country database
$myglobals['mysql']['logsize'] = 32*1024;
// The number of entries to keep (one entry per visitor)


// ----- Font -----

$myglobals['font'] = "{$myglobals['dir_root']}/fonts/arial.ttf";

// ----- URL reduction ------

$myglobals['url_strip_www'   ] = true;
$myglobals['url_append_www'  ] = false;
$myglobals['url_strip_index' ] = 'index.php';
$myglobals['url_append_index'] = false;
// Different URLs can refer to the same page. For example,
// http://www.domain.com/ and http://domain.com refer to the same page.
// These settings will help to reduce such URLs to the same form.

// ----- Referrer reduction ------

$myglobals['ref_strip_www'   ] = true;
$myglobals['ref_append_www'  ] = false;
$myglobals['ref_strip_index' ] = false;
$myglobals['ref_append_index'] = false;
// Different URLs can refer to the same page. For example,
// http://www.domain.com/ and http://domain.com refer to the same page.
// These settings will help to reduce such URLs to the same form.

// ----- Bar chart stats ------

$myglobals['stats_pie_nchart'] =  6;
// Number of sectors in the pie diagram
$myglobals['stats_pie_ntable'] = 64;
// Number of entries in the table

$myglobals['stats_pie'] = Array("agent"         => 'Browser',
                                "country"       => 'Country',
                                "ref_domain"    => 'Referrer Domain',
                                "ref_url"       => 'Referrer URL',
                                "pageloads"     => 'Pageloads',
                                "platform"      => 'Platform',
                                "screen"        => 'Screen Resolution',
                                "visit_length"  => 'Visit Length');
// Pie stats. Do not modify.

// ----- Pie chart stats ------

$myglobals['stats_bar'] = Array("general"       => "General",
                                "visit_length"  => "Visit Length",
                                "pageloads_day" => "Pageloads",
                                "pageloads_vis" => "Pageloads Per Visit",
                                "returning"     => "Returning Visitors",
                                "uniques"       => "Unique Visitors");
// Bar stats. Do not modify.

// ----- Pie Chart Conversions ------

$myglobals['convert_pageloads'] = Array(1    => "1 pageload",
                                        3    => "2-3 pageloads",
                                        6    => "4-6 pageloads",
                                        10   => "7-10 pageloads",
                                        15   => "11-15 pageloads",
                                        9999 => "More than 15");
// This breaks pageloads per visit number to 6 intervals. You can change it. 

$myglobals['convert_timespent'] = Array(0    => "Less than 10sec",
                                        3    => "10sec - 30sec",
                                        12   => "30sec - 2min",
                                        60   => "2min - 10min",
                                        180  => "10min - 30min",
                                        9999 => "More than 30min");
// This breaks all visit length to 6 intervals. You can change it. 

// ----- Images ------

$myglobals['colors' ] = Array(0xC85858, 0xC8C858, 0x58C858, 0x58C8C8, 0x5858C8, 0xC858C8);
// Colors for bar and pie diagrams
$myglobals['shadows'] = Array(0x702020, 0x707020, 0x207020, 0x207070, 0x202070, 0x702070);
// Shadows for pie diagrams


// ----- Initialization --------------------------------------------------------

// ----- Errors handling -----

error_reporting(E_ALL);
ini_set("log_errors",     0);
ini_set("display_errors", 1);

// ----- Adjust magic quotes -----

include_once 'mylib.php';

if (get_magic_quotes_gpc()) adjust_magic_quotes();

// ----- Start session -----

ini_set("session.use_cookies", 1);
session_start();

// ----- Connect to database -----

mysql_connect  ($myglobals['mysql']['host'],
                $myglobals['mysql']['user'],
                $myglobals['mysql']['pass']);
mysql_select_db($myglobals['mysql']['name']);


// ----- API -------------------------------------------------------------------

include_once "api.php";

$log = new TLogTable();
$log->Init($myglobals['mysql']['prefix']);

$imgs = new TStringTable();
$imgs->init($myglobals['mysql']['prefix'] . '_imgs', 'imgs_');

$gets = new TStringTable();
$gets->init($myglobals['mysql']['prefix'] . '_gets', 'gets_');

$vars = new TVarTable();
$vars->Init($myglobals['mysql']['prefix'] . '_vars');

$queries = new TVarTable();
$queries->init($myglobals['mysql']['prefix'] . '_query');

$log ->Limit($myglobals['mysql']['logsize'], $myglobals['mysql']['logsize']);
$gets->Limit($myglobals['mysql']['logsize']);

?>
