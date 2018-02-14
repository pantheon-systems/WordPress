<?php

namespace Wpae\App\Field;


class IsBundle extends Field
{
    public function getValue($snippetData)
    {
        return 'no';
    }
    
    public function getFieldName()
    {
        return 'is_bundle';
    }
}