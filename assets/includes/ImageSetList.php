<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 6/12/2018
 * Time: 5:32
 */

class ImageSetList
{
    const OWNER_KEY = 'owner';
    const USER_KEY = 'user';
    const START_KEY = 'start';
    const LENGTH_KEY = 'length';

    static function fetch_gallery($owner, $user, $start, $length)
    {
        $db = new SharerDatabase();
        return $db->fetch_gallery($owner, $user, $start, $length);
    }
}