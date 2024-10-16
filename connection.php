<?php
try {
  $json = file_get_contents(__DIR__ . "/config.json");
  $config = json_decode($json, true);
  $host = $config['mysql']['host'];
  $db = $config["mysql"]["database"];
  $dsn = "mysql:host=$host;dbname=$db";
  $conn = new PDO(
    $dsn,
    $config["mysql"]["user"],
    $config["mysql"]["password"]
  );
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  echo "Error mysql <br>".$e->getMessage();
}