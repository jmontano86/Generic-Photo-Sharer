<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/7/2018
 * Time: 6:48
 */

require_once('../includes/User.php');
require_once('../includes/SharerDatabase.php');
require_once('../includes/sharer_constants.php');
require_once('../includes/SharerEmail.php');


session_start();

$user = new User();
$user->send_verification();

