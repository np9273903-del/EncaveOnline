<?php
session_start();
require __DIR__.'/../config/config.php';
function db(){static $p;return $p??=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function csrf(){return $_SESSION['csrf']??=bin2hex(random_bytes(16));}
function check_csrf(){if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??''))die('Bad request');}
function require_admin(){if(empty($_SESSION['admin'])){header('Location: login.php');exit;}}
function rzp($data){$c=curl_init('https://api.razorpay.com/v1/orders');
curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>1,CURLOPT_USERPWD=>RZP_KEY_ID.':'.RZP_KEY_SECRET,CURLOPT_POST=>1,CURLOPT_HTTPHEADER=>['Content-Type: application/json'],CURLOPT_POSTFIELDS=>json_encode($data),CURLOPT_TIMEOUT=>15]);
$r=json_decode(curl_exec($c),true);curl_close($c);return $r?:[];}
