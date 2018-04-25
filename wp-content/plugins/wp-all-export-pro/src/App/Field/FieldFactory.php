<?php

namespace Wpae\App\Field;

use Wpae\App\Feed\Feed;
use Wpae\App\Service\WooCommerceVersion;

class FieldFactory
{
    private $filters;

    /**
     * @var Feed
     */
    private $feed;

    public function __construct($filters, Feed $feed)
    {
        $this->filters = $filters;
        $this->feed = $feed;
    }

    /**
     * @param $fieldType
     * @param $entry
     * @return Field
     * @throws \Exception
     */
    public function createField($fieldType, $entry)
    {
        $className = $this->getClassName($fieldType);

        if(class_exists($className)) {
            return new $className($entry, $this->filters, $this->feed, new WooCommerceVersion());
        } else {
            throw new \Exception('The field type '.$fieldType.' does not exist');
        }
    }

    /**
     * @param $fieldType
     * @return array
     */
    private function getClassName($fieldType)
    {
        $fieldType = str_replace('_', ' ', $fieldType);
        $fieldType = ucwords($fieldType);
        $fieldType = str_replace(' ', '', $fieldType);
        $className = "Wpae\\App\\Field\\" . $fieldType;

        return $className;
    }
}