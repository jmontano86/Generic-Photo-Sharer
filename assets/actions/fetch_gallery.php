<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 6/12/2018
 * Time: 5:34
 */

require_once('../includes/User.php');
require_once('../includes/SharerDatabase.php');
require_once('../includes/ImageSetList.php');
require_once('../includes/utilities.php');

session_start();
header('Content-Type: application/json');

$owner = get_get_value(ImageSetList::OWNER_KEY);
$start = get_get_value(ImageSetList::START_KEY);
$user = User::get_user();
$length = get_get_value(ImageSetList::LENGTH_KEY);

$list = ImageSetList::fetch_gallery($owner, $user, $start, $length);

echo json_encode($list);