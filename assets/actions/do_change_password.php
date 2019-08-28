<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/9/2018
 * Time: 6:39
 */
require_once('../includes/User.php');
require_once('../includes/SharerDatabase.php');
require_once('../includes/SharerEmail.php');
require_once('../includes/sharer_constants.php');
require_once('../includes/utilities.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
session_id($_POST['sess_id']);
session_start();
$user = new User();
$status = $user->change_password(
    get_post_value(User::CHANGE_PASSWORD_USERNAME_KEY),
    get_post_value(User::CHANGE_PASSWORD_CODE_KEY),
    get_post_value(User::CHANGE_PASSWORD_KEY),
    get_post_value(User::CHANGE_PASSWORD_CONFIRMATION_KEY)
);

echo json_encode($status);