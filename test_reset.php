<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/9/2018
 * Time: 6:07
 */

require_once('assets/includes/common_requires.php');

$user = new User();
$user->send_reset_code('jeremiah');