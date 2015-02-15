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


// ----- Arrays ----------------------------------------------------------------

// ----- Array concat ----

function array_concat()
{
  $result = Array();

  for ($i = 0; $i < func_num_args(); $i++)
    if (is_array(func_get_arg($i)))
         foreach(func_get_arg($i) as $j => $y) $result[$j] = $y;
    else $result[] = func_get_arg($i);

  return $result;
}


// ----- Graphics --------------------------------------------------------------

// ----- Output to String -----

function imagepng_str($image)
{
  flush();
  ob_start();
  imagepng($image);
  $result = ob_get_contents();
  ob_end_clean();
  return $result;
}

// ----- Color Allocate -----

function color_allocate($image, $color)
{
  $b = ($color)         % 0x100;
  $g = ($color/0x100)   % 0x100;
  $r = ($color/0x10000) % 0x100;

  return imagecolorallocate($image, $r, $g, $b);
}

// ----- Draw sheet -----

function draw_sheet($image, $data)
{
  $data['color'       ] = color_allocate($image, $data['color'       ]);
  $data['border_color'] = color_allocate($image, $data['border_color']);
  $data['shadow_color'] = color_allocate($image, $data['shadow_color']);

  imagefilledrectangle($image, $data['x1']+$data['shadow_width'],
                               $data['y1']+$data['shadow_width'],
                               $data['x2'], $data['y2'],
                               $data['shadow_color']);
  imagefilledrectangle($image, $data['x1'], $data['y1'],
                               $data['x2']-$data['shadow_width'],
                               $data['y2']-$data['shadow_width'],
                               $data['color']);
  imagerectangle      ($image, $data['x1'], $data['y1'],
                               $data['x2']-$data['shadow_width'],
                               $data['y2']-$data['shadow_width'],
                               $data['border_color']);
}

// ----- Draw title2color -----

function title2color_width($data, $titles)
{
  $width = 0;

  foreach ($titles as $i => $title)
  {
    $size = imageftbbox(9, 0, $GLOBALS['myglobals']['font'], $title);
    if ($size[2]-$size[6] > $width) $width = $size[2]-$size[6];
  }

  if (isset($data['sheet'])) $width += $data['sheet']['shadow_width'];

  return 2*$data['paddingx']+$data['height']+$data['offset']+$width;
}

function title2color_height($data, $titles)
{
  return 2*$data['paddingy']+
         count($titles)*$data['height']+(count($titles)-1)*$data['offset']+
         $data['sheet']['shadow_width'];
}

function draw_title2color($image, $data, $titles, $colors)
{
  if (isset($data['sheet']))
  {
    $data['sheet']['x1'] = $data['x'];
    $data['sheet']['y1'] = $data['y'];
    $data['sheet']['x2'] = $data['x']+title2color_width ($data, $titles);
    $data['sheet']['y2'] = $data['y']+title2color_height($data, $titles);
    draw_sheet($image, $data['sheet']);
  }

  $data['color'] = color_allocate($image, $data['color']);
  foreach ($colors as $i => $x) $colors[$i] = color_allocate($image, $x);

  foreach ($titles as $i => $title)
  {
    imagefilledrectangle($image, $data['x']+1+$data['paddingx'],
                                 $data['y']+1+$data['paddingy'],
                                 $data['x']-1+$data['paddingx']+$data['height'],
                                 $data['y']-1+$data['paddingy']+$data['height'],
                                 $colors[$i % count($colors)]);
    imagerectangle      ($image, $data['x']+1+$data['paddingx'],
                                 $data['y']+1+$data['paddingy'],
                                 $data['x']-1+$data['paddingx']+$data['height'],
                                 $data['y']-1+$data['paddingy']+$data['height'],
                                 $data['color']);
    imagettftext($image, 9, 0, $data['x']+$data['paddingx']+$data['height']+$data['offset'],
                               $data['y']+$data['paddingy']+$data['height'],
                               $data['color'], $data['font'], $title);

    $data['y'] += $data['height']+$data['offset'];
  }
}


// ----- Draw piechart -----

function draw_piechart($image, $data, $sectors, $colors, $shadows)
{
  // Colors

  $data['color'] = color_allocate($image, $data['color']);
  foreach ($colors  as $i => $x) $colors [$i] = color_allocate($image, $x);
  foreach ($shadows as $i => $x) $shadows[$i] = color_allocate($image, $x);

  // Shadow

  for ($n = $data['shadow']; $n > 1; $n--)
  {
    $start = 0;
    $end   = 0;

    foreach ($sectors as $i => $percent)
    {
      $end   += $percent;
      imagefilledarc($image, $data['x'], $data['y']+$n,
                             $data['width'], $data['height'],
                             $start*360/100, $end*360/100,
                             $shadows[$i % count($shadows)], IMG_ARC_PIE);
      $start += $percent;
    }
  }

  // Face

  $start = 0;
  $end   = 0;

  foreach ($sectors as $i => $percent)
  {
    $end   += $percent;
    imagefilledarc($image, $data['x'], $data['y'],
                           $data['width'], $data['height'],
                           $start*360/100, $end*360/100,
                           $colors[$i % count($colors)], IMG_ARC_PIE);
    $start += $percent;
  }

  // Titles

  $start = 0;
  $end   = 0;

  foreach ($sectors as $i => $percent)
  {
    $end   += $percent;

    $text = round($percent) . '%';
    $size = imageftbbox(9, 0, $GLOBALS['myglobals']['font'], $text);
    $txtx = $data['x']+cos(($start+$end)*M_PI/100)*($data['width'] +9)/2-1;
    $txty = $data['y']+sin(($start+$end)*M_PI/100)*($data['height']+9)/2+1;

    if ($start+$end < 100) $txty += $size[3]-$size[7]+$data['shadow'];
    if ($start+$end < 150 && $start+$end > 50)
                           $txtx -= $size[2]-$size[6]-3;

    imagettftext($image, 9, 0, $txtx, $txty,
                 $data['color'], $GLOBALS['myglobals']['font'], $text);

    $start += $percent;
  }
}

// ----- Draw piechart advanced -----

function draw_piechart_advanced(&$image, $sheet, $chart, $titlebox, $sectors, $colors, $shadows, $titles)
{
  $chart['x'] = 10+$sheet['padding-left']+round($chart['width' ]/2);
  $chart['y'] = 10+$sheet['padding-top' ]+round($chart['height']/2);

  $titlebox['x'] = 10+2*$sheet['padding-left']+$chart['width'];
  $titlebox['y'] = 10+  $sheet['padding-top' ];

  $sheet['x1'] = 10;
  $sheet['y1'] = 10;
  $sheet['x2'] = $sheet['shadow_width']+$sheet['padding-right' ]+
                 $titlebox['x']+title2color_width($titlebox, $titles);
  $sheet['y2'] = $sheet['shadow_width']+$sheet['padding-bottom']+
                 10+$sheet['padding-top']+$chart['height']+$chart['shadow'];

  if (!$image)
  {
    $image = imagecreate($sheet['x2']+10, $sheet['y2']+10);
    imagecolorallocate($image, 255, 255, 255);
  }

  draw_sheet($image, $sheet);
  draw_piechart($image, $chart, $sectors, $colors, $shadows);
  draw_title2color($image, $titlebox, $titles, $colors);
}


// ----- Draw barchart -----

function draw_barchart_width($data, $sectors)
{
  $colnum = 0;
  foreach ($sectors as $i => $x) { $colnum = count($x); break; }
  return Max($data['width'],
             $data['bar_width']*count($sectors)*$colnum+
             $data['bar_offset']*(count($sectors)-1)+5);
}

function draw_barchart(&$image, $data, $sectors, $colors)
{
  $colnum = 0;
  foreach ($sectors as $i => $x) { $colnum = count($x); break; }

  // Adjust sectors

  $max = 0;

  foreach ($sectors as $i => $x)
    foreach ($x as $j => $y) $max = ($max > $y) ? $max : $y;

  $scale = 8;
  while ($scale*$max > 0.9*$data['height']) $scale = $scale/2;

  // Colors

  $data['color'] = color_allocate($image, $data['color']);
  foreach ($colors  as $i => $x) $colors[$i] = color_allocate($image, $x);

  // Axes

  imageline ($image,
             $data['x'],
             $data['y']+$data['height']+1,
             $data['x']+$data['width'],
             $data['y']+$data['height']+1,
             $data['color']);

  imageline ($image,
             $data['x'],
             $data['y']+$data['height']+1,
             $data['x'],
             $data['y']+$data['height']+1-$data['height'],
             $data['color']);

  // Bars

  foreach ($sectors as $i => $x)
    foreach ($x as $j => $y)
    {
      $xpos = $data['bar_width']*($i*$colnum+$j)+$data['bar_offset']*$i;

      imagefilledrectangle($image,
                           $data['x']+$xpos+4,
                           $data['y']+$data['height']-$scale*$y,
                           $data['x']+$xpos+$data['bar_width']+2,
                           $data['y']+$data['height'],
                           $colors[$j]);
      imagerectangle      ($image,
                           $data['x']+$xpos+4,
                           $data['y']+$data['height']-$scale*$y,
                           $data['x']+$xpos+$data['bar_width']+2,
                           $data['y']+$data['height']+1,
                           $data['color']);

      imagettftext($image, min($data['bar_width']-2, 8), -90,
                   $data['x']+$xpos+5, $data['y']+$data['height']+3,
                   $data['color'], $GLOBALS['myglobals']['font'], $y);
    }
}

// ----- Draw barchart advanced -----

function draw_barchart_advanced(&$image, $sheet, $chart, $titlebox, $sectors, $colors, $titles)
{
  $colnum = 0;
  foreach ($sectors as $i => $x) { $colnum = count($x); break; }

  $chart['x'] = 10+$sheet['padding-left'];
  $chart['y'] = 10+$sheet['padding-top' ];
  $chart['width'] = draw_barchart_width($chart, $sectors);

  $titlebox['x'] = 10+$sheet['padding-left']+$chart['width']+20;
  $titlebox['y'] = 10+$sheet['padding-top' ];

  $sheet['x1'] = 10;
  $sheet['y1'] = 10;
  $sheet['x2'] = $sheet['shadow_width']+$sheet['padding-right' ]+
                 $titlebox['x']+title2color_width($titlebox, $titles);
  $sheet['y2'] = $sheet['shadow_width']+$sheet['padding-bottom']+
                 10+$sheet['padding-top']+$chart['height']+$chart['shadow'];

  if (!$image)
  {
    $image = imagecreate($sheet['x2']+10, $sheet['y2']+10);
    imagecolorallocate($image, 255, 255, 255);
  }

  draw_sheet($image, $sheet);
  draw_barchart($image, $chart, $sectors, $colors);
  draw_title2color($image, $titlebox, $titles, $colors);
}


// ----- MySQL -----------------------------------------------------------------

// ----- Debug Query -----

function mysql_query_debug($str)
{
  $result = mysql_query($str);

  if ($GLOBALS['myglobals']['mysql']['debug'] && mysql_error())
    echo '<hr /><b>Message:</b> ' . mysql_error() . "<br /><b>Query:</b> $str<hr />";

  return $result;
}

// ----- MySQL Safe String -----

function mysql_safe_string(&$dest, $source)
{
  for ($i = 2; $i < func_num_args(); $i++)
    if (isset($source[func_get_arg($i)]))
         $dest[func_get_arg($i)] = mysql_real_escape_string($source[func_get_arg($i)]);
    else $dest[func_get_arg($i)] = '';
}

// ----- MySQL Safe Integer -----

function mysql_safe_integer(&$dest, $source)
{
  for ($i = 2; $i < func_num_args(); $i++)
    if (isset($source[func_get_arg($i)]))
         $dest[func_get_arg($i)] = (integer)$source[func_get_arg($i)];
    else $dest[func_get_arg($i)] = 0;
}


// ----- PHP Configuration -----------------------------------------------------

// ----- Addjust magic quotes

function adjust_magic_quotes()
{
  function strip_slashes_deep($value)
  {
    if (is_array($value)) return array_map('strip_slashes_deep', $value);
    return stripslashes($value);
  }

  $_GET    = strip_slashes_deep($_GET);
  $_POST   = strip_slashes_deep($_POST);
  $_COOKIE = strip_slashes_deep($_COOKIE);
}


// ----- Request ---------------------------------------------------------------

// ----- Get/Post -----

function __get($index)
{
  return isset($_GET [$index]) ? $_GET [$index] : false;
}

function __post($index)
{
  return isset($_POST[$index]) ? $_POST[$index] : false;
}

// ----- Get/Post Entities -----

function __get_entities($index)
{
  return isset($_GET [$index]) ? htmlentities($_GET [$index]) : false;
}

function __post_entities($index)
{
  return isset($_POST[$index]) ? htmlentities($_POST[$index]) : false;
}

// ----- Checked/Selected -----

function __checked($key, $val = false)
{
  if ($val)
       return (isset($_POST[$key]) && $_POST[$key] == $val)  ? 'checked="checked"' : "";
  else return isset($_POST[$key]) ? 'checked="checked"' : "";
}

function __selected($key, $val)
{
  return (isset($_POST[$key]) && $_POST[$key] == $val) ? 'selected="selected"' : "";
}

// ----- URL -------------------------------------------------------------------

// ----- Parse URL -----

function url_domain($url, $strip_www = false, $append_www = false)
{
  $error_reporting = error_reporting(E_ERROR | E_PARSE);
  $url = parse_url($url);
  error_reporting($error_reporting);

  if (!isset($url['host'])) return false;

  $url['host'] = strtolower($url['host']);

  if ($strip_www  && strpos($url['host'], 'www.') === 0)
    return substr($url['host'], 4, strlen($url['host'])-4);

  if ($append_www && strpos($url['host'], 'www.') !== 0)
    return 'www.' . $url['host'];

  return $url['host'];
}

// ----- Adjust URL -----

function url_adjust($url, $strip_www   = false, $append_www   = false,
                          $strip_index = false, $append_index = false)
{
  $error_reporting = error_reporting(E_ERROR | E_PARSE);
  $url = parse_url($url);
  error_reporting($error_reporting);

  // adjust Host

  if (!isset($url['host'])) return false;

  $url['host'] = strtolower($url['host']);

  if ($strip_www  && strpos($url['host'], 'www.') === 0)
    $url['host'] = substr($url['host'], 4, strlen($url['host'])-4);

  if ($append_www && strpos($url['host'], 'www.') !== 0)
    $url['host'] = 'www.' . $url['host'];

  // Adjust path begin

  if (!isset($url['path'])) $url['path'] = '/';

  $url['path'] = explode('/', $url['path']);
  $lastitem    = &$url['path'][count($url['path'])-1];

  // Apend slash after directory

  if ($lastitem != '' && strpos($lastitem, '.') === false)
  {
    $url['path'][] = '';
    $lastitem      = &$url['path'][count($url['path'])-1];
  }

  // Strip/append index

  if (isset($strip_index ) && $lastitem == $strip_index ) $lastitem = '';
  if (isset($append_index) && $lastitem == '') $lastitem = $append_index;

  // Adjust path end

  $url['path'] = implode('/', $url['path']);

  // Result

  $result = $url['scheme'] . '://' . $url['host'] . $url['path'];

  if (isset($url['query'   ])) $result += '?' . $url['query'   ];
  if (isset($url['fragment'])) $result += '#' . $url['fragment'];

  return $result;
}

?>
