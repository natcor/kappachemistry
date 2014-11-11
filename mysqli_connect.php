<?php # Script 3.8 - mysqli_connect.php #2

//connect to the mysql server

DEFINE ('DB_USER', 'manager');
DEFINE ('DB_PASSWORD', 'PalestPink2014');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'kappachemistry');

$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die('Could not connect to MySQL: ' . mysqli_connect_error());

//Set the encoding

mysqli_set_charset($dbc, 'utf8');

