<?php

namespace Wpae\App\Field;


class Tax extends Field
{
    public function getValue($snippetData)
    {
        return 'Tax value';
    }

    public function getFieldName()
    {
        return 'tax';
    }


}