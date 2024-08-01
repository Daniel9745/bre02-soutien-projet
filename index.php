<?php
session_start();
// charge l'autoload de composer
require "vendor/autoload.php";

if(!isset($_SESSION["csrf_token"]))
{
    $tokenManager = new CSRFTokenManager();
    $token = $tokenManager->generateCSRFToken();

    $_SESSION["csrf_token"] = $token;
}


// charge le contenu du .env dans $_ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$route = null;

if(isset($_GET["route"])){
    $route = $_GET["route"];
}
$router = new Router();
$router -> handleRequest($route);