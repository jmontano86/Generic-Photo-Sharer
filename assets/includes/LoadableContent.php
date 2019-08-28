<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/16/2018
 * Time: 20:11
 */

class LoadableContent {
    public $js = '';
    public $html = '';
    public $css = '';

    public function __construct($js, $html, $css)
    {
        $this->css = $css;
        $this->html = $html;
        $this->js = $js;
    }

    public function load()
    {
        header ('Content-Type: application/json');
        echo json_encode($this);
    }
}