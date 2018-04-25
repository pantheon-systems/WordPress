<?php

namespace Wpae\App\Controller;

use Wpae\Controller\BaseController;
use Wpae\Http\JsonResponse;
use Wpae\Http\Request;

class CurrencyController extends BaseController
{
    public function getAction(Request $request)
    {
        $attributeTaxonomies = wc_get_attribute_taxonomies();

        return new JsonResponse($attributeTaxonomies);
    }
}