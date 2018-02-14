<?php

namespace Wpae\App\Repository;


interface TemplateRepository
{
    public function getTemplate($id);

    public function saveTemplate($template);

    public function loadTemplate($id);
}