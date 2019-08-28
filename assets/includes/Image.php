<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 5/20/2018
 * Time: 9:45
 */

class Image
{
    private $m_id = 0;

    public function __construct($type, $size, $width, $height, $data)
    {
        $db = new SharerDatabase();

        $this->m_id = $db->insert_image($type, $size, $width, $height, $data);

    }

    public function get_id()
    {
        return $this->m_id;
    }
}