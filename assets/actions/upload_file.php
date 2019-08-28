<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/20/2018
 * Time: 7:40
 */

require_once('../includes/ImageSet.php');
require_once('../includes/Image.php');
require_once('../includes/User.php');
require_once('../includes/SharerDatabase.php');

header('Content-Type: application/json');
session_start();
$role = User::get_role();
if ($role === '' || $role === 'user') {
    $obj = new StdClass();
    $obj->status = "Error";
    $obj->message = "Operation not allowed!";
    echo json_encode($obj);
    exit;
}
$obj = new StdClass();

$obj->status = 'ok';


$img = new ImageSet($_FILES[ImageSet::FILE_KEY]);
$obj->id = $img->get_id();
echo json_encode($obj);