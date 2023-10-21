<?php
// Bismillahirrahmanirrahim. 
// @kutsalyazilim.com
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

include("vendor/autoload.php");
include("defines.php");
include("sys.php");
include("app.php");
include("bridge.php");
include("config.php");

$startMicroTime = microtime(true);

$app = new app($config);
$app->openCors();


// $isLogin = isset($user["id"]);
// $isAdmin = isset($_SESSION["admin"]);
$now = strtotime(date("d-m-Y H:i:s"));


if (!isset($_GET["m"]) or empty($_GET["m"])) {
    echo $app->response(["message" => "module required"], Response::HTTP_NOT_ACCEPTABLE);
    exit;
}

$module = null;
$moduleName = $_GET["m"];

if (!file_exists('api/' . $moduleName . ".php")) {
    echo $app->response(["message" => "module not found"], Response::HTTP_NOT_FOUND);
    exit;
}

if ($moduleName == 'app') {
    $module = $app;
} else {
    include("api/" . $moduleName . ".php");
    $moduleName = str_replace('-', '_', $moduleName);
    $module = new $moduleName($config);
}

if (!isset($_GET["act"]) or empty($_GET["act"])) {
    echo $app->response(["message" => "action required"]);
    exit;
}

// $moduleName = get_class($module);
$originalAction = $app->camelCase($_GET["act"]);
$act = "act_" . $originalAction;

if (!method_exists($module, $act)) {
    $app->response(["message" => "action not found on module"], Response::HTTP_NOT_FOUND);
    exit;
}

$input = file_get_contents("php://input");
$post = json_decode($input, true); // to array

if(!$post){
    $post = $_POST;
}


$data = $app->escapeStringControl($post);

$response = $module->$act($data, $_GET, $_FILES);
$endMicroTime = microtime(true);

$response = json_decode($response);
$response->_elapsedTime = ($endMicroTime - $startMicroTime);

if($originalAction != 'login'){
    $response->tokenIsValid = $app->jwtTokenControl();
}


echo json_encode($response);

