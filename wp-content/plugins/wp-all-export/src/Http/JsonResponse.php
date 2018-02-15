<?php

namespace Wpae\Http;


class JsonResponse extends Response
{
    protected $headers = array('Content-Type' => 'Application/Json');

    protected function sendContent()
    {
        echo json_encode($this->content);
    }
}