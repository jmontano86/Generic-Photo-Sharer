<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/5/2018
 * Time: 11:12
 */

require_once('../includes/User.php');
require_once('../includes/SharerDatabase.php');
require_once('../includes/utilities.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
session_id($_POST['sess_id']);

session_start();
$user = new User();
$status = $user->login(
    get_post_value(User::LOGIN_USERNAME_KEY),
    get_post_value(User::LOGIN_PASSWORD_KEY)
);

echo json_encode($status);