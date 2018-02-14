<?php

namespace Wpae\Http;


class Response
{
    protected $content;

    protected $headers = array(
        'Content-Type' => 'text/html'
    );

    public function __construct($content)
    {
        $this->content = $content;
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
    }

    protected function sendContent()
    {
        echo $this->content;
    }
}