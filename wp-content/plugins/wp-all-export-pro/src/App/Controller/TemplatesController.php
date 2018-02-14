<?php

namespace Wpae\App\Controller;

use WP_Error;
use Wpae\App\Repository\WpDbTemplateRepository;
use Wpae\App\Service\TemplateManager;
use Wpae\Controller\BaseController;
use Wpae\Http\JsonResponse;
use Wpae\Http\Request;

class TemplatesController extends BaseController
{
    public function getAction(Request $request)
    {
        $templateId = $request->get('templateId');

        $templateManager = new TemplateManager(new WpDbTemplateRepository());

        $template = $templateManager->findTemplate($templateId);
        $templateOptions = $template->options;

        return new JsonResponse($templateOptions);
    }
}