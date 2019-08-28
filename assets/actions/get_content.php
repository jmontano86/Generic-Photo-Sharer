<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/10/2018
 * Time: 6:34
 */

header('Content-Type: application/json');

$obj = new StdClass();
$obj->html= "<span style=\"color: red;\">Content</span>";
$obj->count = 3;
echo json_encode($obj);