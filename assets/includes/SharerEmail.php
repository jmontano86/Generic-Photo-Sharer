<?php
/**
 * Created by PhpStorm.
 * User: Jeremiah
 * Date: 4/16/2018
 * Time: 19:01
 */

require_once('Mail.php');

class SharerEmail {

    //const HOST = 'ssl://smtp.gmail.com';
    const HOST = 'localhost';
    //const PORT = '465';
    const PORT = '25';
    const USERNAME = 'noreply';
    const PASSWORD = 'noreply';
    const FROM = 'Generic Sharer <noreply@localhost>';

    private $to;
    private $subject;
    private $body;
    private $result;
    public function __construct($to, $subject, $body)
    {
            $this->to = $to;
            $this->subject = $subject;
            $this->body = $body;
    }

    public function send()
    {
        $headers = ['To' => $this->to,
            'From' => SharerEmail::FROM,
            'Subject' => $this->subject,
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/html; charset=utf-8'];

        $transport = ['host' => SharerEmail::HOST,
            'port' => SharerEmail::PORT,
            'username' => SharerEmail::USERNAME,
            'password' => SharerEmail::PASSWORD,
            'auth' => false];

        $smtp = Mail::factory('smtp', $transport);
        $this->result = $smtp->send($this->to, $headers, $this->body);
    }

    public function get_status() {
        return $this->result;
    }




}
