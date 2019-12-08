<?php
namespace Lockr;

class LockrDevSettings extends LockrSettings
{
    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        $options['verify'] = false;
        return $options;
    }
}

// ex: ts=4 sts=4 sw=4 et:
