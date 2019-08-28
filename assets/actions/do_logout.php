<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/4/2018
 * Time: 21:25
 */

require_once('../includes/User.php');

session_start();
$user = new User();
$user->clear_user();

$session_info = session_get_cookie_params();
$_SESSION = [];
setcookie(
    session_name(),
    '',
    0,
    $session_info['path'],
    $session_info['domain'],
    $session_info['secure'],
    $session_info['httponly']
);
session_destroy();