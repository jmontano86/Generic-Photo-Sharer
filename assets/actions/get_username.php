<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/30/2018
 * Time: 6:02
 */

require_once('../includes/User.php');
header('Content-Type: application/json');

session_start();

$obj = new StdClass();
$obj->username = User::get_user();
$obj->role = User::get_role();

echo json_encode($obj);