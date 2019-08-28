<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/1/2018
 * Time: 6:16
 */

function get_post_value($key)
{
    if(!isset($_POST) || !isset($_POST[$key])) {
        return '';
    } else {
        return $_POST[$key];
    }
}

function get_get_value($key)
{
    if(!isset($_GET) || !isset($_GET[$key])) {
        return '';
    } else {
        return $_GET[$key];
    }
}