<?php
date_default_timezone_set('Europe/Istanbul');
define('DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);
define('PROTOCOL', isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http');
define('PORT', $_SERVER['SERVER_PORT']);
define('SITE_PATH', preg_replace('/index.php$/i', '', $_SERVER['PHP_SELF']));
define('SITE_ROOT', (PROTOCOL . '://' . DOMAIN . (PORT === '80' ? '' : ':' . PORT) . SITE_PATH));
$http_host = $_SERVER["HTTP_HOST"];
$ip = "192.168.0.20";
$development = (DOMAIN== "localhost" || DOMAIN == $ip);
?>