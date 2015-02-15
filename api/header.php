<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Site Stats</title>

<link rel="stylesheet" type="text/css" href="api/style.css" />

</head>
<body>

<div class="logo">
Site Stats
</div>

<div class="navigation">
<a href="index.php">Home</a>
<div>::</div>
<a href="query.php?type=bar">Bar Chart</a>
<div>::</div>
<a href="query.php?type=pie">Pie Chart</a>
<div>::</div>
<a href="stored.php">Stored Queries</a>
<div>::</div>
<a href="log.php">Log</a>
<div>::</div>
<a href="blockcookie.php">Blocking Cookie</a>
<?php

$data = $queries->GetAllData();
foreach ($data as $i => $x)
{
  $x = unserialize($x);

  if (isset($x["to_menu"]))
    echo "<div>::</div> <a href=\"chart.php?stored=" . urlencode($i) . "\">" . htmlentities($i) . "</a>";
}

?>
</div>

<div class="yellowline">
</div>
