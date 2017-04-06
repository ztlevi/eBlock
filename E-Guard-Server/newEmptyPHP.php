<?php
$pdo = new PDO("mysql:host=localhost;dbname=mysql","root","daiyue");
$configuration = null;
$query_blockedCategory = ("SELECT Category FROM block_category");
$configuration->block_category = $pdo->query($query_blockedCategory)->fetchAll(PDO::FETCH_COLUMN, 0);
$query_timer = ("SELECT Limitation FROM timer");
$configuration->timer = $pdo->query($query_timer)->fetchAll(PDO::FETCH_COLUMN, 0);
$query_white_list = ("SELECT URL FROM website_white_list");
$configuration->white_list = $pdo->query($query_white_list)->fetchAll(PDO::FETCH_COLUMN, 0);
$query_black_list = ("SELECT URL FROM website_black_list");
$configuration->black_list = $pdo->query($query_black_list)->fetchAll(PDO::FETCH_COLUMN, 0);
$json = json_encode($configuration);
$test;
?>