<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/16/2018
 * Time: 6:48
 */


require_once('assets\includes\SharerEmail.php');

$email = new SharerEmail('cis295p@localhost', 'Test Mail Class',
    '<div style"color=red;">This is only a test.</div>');
try {
    $email->send();
}
catch(PEAR_Exception $e) {
    echo "An error has occurred: " . $e;
}
$result = $email->get_status();

if(PEAR::isError($result)) {
    echo $result->getMessage() . '<br>';
} else {
    echo 'Message Sent.';
}