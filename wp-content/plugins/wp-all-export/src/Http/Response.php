<?php

namespace Wpae\Http;


class Response
{
    protected $content;

    protected $status;

    protected $headers = array(
        'Content-Type' => 'text/html'
    );

    public function __construct($content, $status = 200)
    {
        $this->content = $content;
        $this->status = $status;
    }

    public function render()
    {
        $this->sendHeaders();
        $this->sendContent();
        die;
    }

    protected function sendHeaders()
    {
        foreach($this->headers as $header => $value) {
            header($header.': '.$value);
        }
        http_response_code($this->status);
    }

    protected function sendContent()
    {
        echo $this->content;
    }
}