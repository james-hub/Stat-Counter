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

// ----- Contents --------------------------------------------------------------

?>

<h1>Blocking Cookie</h1>

<div class="text">

<p>
A blocking cookie is stored in your browser
to prevent your own visits to your sites from being logged.
This ensures that your site stats is not skewed by your own visits.
</p>

<form name="fcookie" action="blockcookie.php">
<input name="icookie" type="button" value="Create Blocking Cookie" onclick="switchcookie();" />
</form>

</div>

<script type="text/javascript">

// ----- Javascript ------------------------------------------------------------

// ----- setCookie -----

function setCookie(name, value, expires, path, domain, secure)
{
  document.cookie =
    name+"="+escape(value)+
    (expires ? "; expires="+expires.toGMTString() : "")+
    (path    ? "; path="   +path   : "")+
    (domain  ? "; domain=" +domain : "")+
    (secure  ? "; secure" : "");
}

// ----- setCookieLT -----

function setCookieLT(name, value, lifetime, path, domain, secure)
{
  if (lifetime) lifetime = new Date(Date.parse(new Date())+lifetime*1000);
  setCookie(name, value, lifetime, path, domain, secure);
}

// ----- getCookie -----

function getCookie(name)
{
  cookie = " "+document.cookie;
  offset = cookie.indexOf(" "+name+"=");

  if (offset == -1) return undefined;

  offset += name.length+2;
  end     = cookie.indexOf(";", offset)

  if (end == -1) end = cookie.length;

  return unescape(cookie.substring(offset, end));
}

// ----- Initialize -----


blockcookie = getCookie('sc_blockcookie') == 'Y';
document.forms['fcookie']['icookie'].value =
  blockcookie ? 'Destroy Blocking Cookie' :  'Create Blocking Cookie';

// ----- Switch Cookie -----

function switchcookie()
{
  setCookieLT('sc_blockcookie', blockcookie ? 'N' : 'Y', 5*365*24*3600);
  setCookieLT('sc_blockcookie', blockcookie ? 'N' : 'Y', 5*365*24*3600, '/');

  blockcookie = !blockcookie;
  document.forms['fcookie']['icookie'].value =
    blockcookie ? 'Destroy Blocking Cookie' : 'Create Blocking Cookie';
}

</script>

<?php include_once "api/footer.php"; ?>
