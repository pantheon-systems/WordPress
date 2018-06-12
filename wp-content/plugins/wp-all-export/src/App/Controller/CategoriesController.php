<?php

namespace Wpae\App\Controller;

use Wpae\App\Service\CategoriesService;
use Wpae\Controller\BaseController;
use Wpae\Http\JsonResponse;
use Wpae\Http\Request;

class CategoriesController extends BaseController
{
    public function indexAction(Request $request)
    {
        $categoriesService = new CategoriesService();

        $categories = array(
            'id' => 0,
            'title' => 'Root',
            'children' => $categoriesService->getTaxonomyHierarchy(0)
        );

       return new JsonResponse($categories);
    }
}