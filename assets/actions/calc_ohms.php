<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/11/2018
 * Time: 6:11
 */
header("Content-Type: application/json");

$obj = new StdClass();

$volts = $_POST['volts'];
$ohms = $_POST['ohms'];

$obj->volts = $volts;
$obj->ohms = $ohms;
$obj->amps = $volts / $ohms;
$obj->watts = $volts * $obj->amps;

echo json_encode($obj);