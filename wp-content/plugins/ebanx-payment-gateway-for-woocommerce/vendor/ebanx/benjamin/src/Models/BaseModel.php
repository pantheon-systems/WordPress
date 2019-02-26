<?php
namespace Ebanx\Benjamin\Models;

abstract class BaseModel
{
    /**
     * Fill the object with the provided $attributes array
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }

            $this->{$key} = $value;
        }
    }

    /**
     * @return string
     */
    public function getShortClassname()
    {
        return basename(str_replace('\\', '/', get_class($this)));
    }
}
