<?php

namespace Wpae\App\Service;


use Wpae\App\Repository\TemplateRepository;

class TemplateManager
{
    /**
     * @var TemplateRepository
     */
    private $templateRepository;

    public function __construct(TemplateRepository $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    public function saveTemplate($params, $post)
    {
        $template = $template = new \PMXE_Template_Record();

        // save template in database
        $template->getByName($params['template']['name'])->set(array(
            'name'    => $params['template']['name'],
            'options' => $post
        ))->save();

        \PMXE_Plugin::$session->set('saved_template', $template->id);
    }

    public function findTemplate($templateId)
    {
        $template = new \PMXE_Template_Record();

        if ($template->getById($templateId)->isEmpty()) {
            throw new \Exception('Template not found');
        }

        return $template;
    }
}