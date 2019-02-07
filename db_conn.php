<?php
$setup_ini = parse_ini_file("/settings.ini", true);

$db_ini = $setup_ini['db'];
$server_ini = $setup_ini['server'];

$conn = mysqli_connect($db_ini['host'], $db_ini['user'], $db_ini['password'], $db_ini['db_name']) or die("Connection to database failed.");