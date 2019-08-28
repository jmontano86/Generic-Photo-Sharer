<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/10/2018
 * Time: 6:09
 */

require_once('assets/includes/SharerDatabase.php');

$db = new SharerDatabase();
var_dump($db->lookup_usernames('cis295p@localhost'));

