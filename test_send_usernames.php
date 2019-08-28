<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/10/2018
 * Time: 6:09
 */

require_once('assets/includes/SharerDatabase.php');
require_once('assets/includes/User.php');
require_once('assets/includes/SharerEmail.php');
require_once('assets/includes/sharer_constants.php');

$user = new User();
$user->send_usernames_email('cis295@localhost');

