<?php

namespace Wpae\App\Controller;


use Wpae\Http\JsonResponse;

class SchedulingConnectionController
{
    public function indexAction()
    {
        return new JsonResponse(array('success' => true));
    }
}