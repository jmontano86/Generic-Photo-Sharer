<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 6/12/2018
 * Time: 21:58
 */

require_once('../includes/SharerDatabase.php');
require_once('../includes/ImageSet.php');
require_once('../includes/User.php');
require_once('../includes/utilities.php');

session_start();

$id = get_get_value(SharerDatabase::IMAGE_SET_ID_KEY);
$description = get_get_value(SharerDatabase::DESCRIPTION_KEY);
$user = User::get_user();

ImageSet::update_imageset_description($id, $description, $user);