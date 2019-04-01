<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Serializer;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\StructureShape;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\ListShape;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\MapShape;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Shape;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\TimestampShape;
/**
 * @internal
 */
class QueryParamBuilder
{
    private $methods;
    protected function queryName(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Shape $shape, $default = null)
    {
        if (null !== $shape['queryName']) {
            return $shape['queryName'];
        }
        if (null !== $shape['locationName']) {
            return $shape['locationName'];
        }
        if ($this->isFlat($shape) && !empty($shape['member']['locationName'])) {
            return $shape['member']['locationName'];
        }
        return $default;
    }
    protected function isFlat(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Shape $shape)
    {
        return $shape['flattened'] === true;
    }
    public function __invoke(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\StructureShape $shape, array $params)
    {
        if (!$this->methods) {
            $this->methods = array_fill_keys(get_class_methods($this), true);
        }
        $query = [];
        $this->format_structure($shape, $params, '', $query);
        return $query;
    }
    protected function format(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Shape $shape, $value, $prefix, array &$query)
    {
        $type = 'format_' . $shape['type'];
        if (isset($this->methods[$type])) {
            $this->{$type}($shape, $value, $prefix, $query);
        } else {
            $query[$prefix] = (string) $value;
        }
    }
    protected function format_structure(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\StructureShape $shape, array $value, $prefix, &$query)
    {
        if ($prefix) {
            $prefix .= '.';
        }
        foreach ($value as $k => $v) {
            if ($shape->hasMember($k)) {
                $member = $shape->getMember($k);
                $this->format($member, $v, $prefix . $this->queryName($member, $k), $query);
            }
        }
    }
    protected function format_list(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\ListShape $shape, array $value, $prefix, &$query)
    {
        // Handle empty list serialization
        if (!$value) {
            $query[$prefix] = '';
            return;
        }
        $items = $shape->getMember();
        if (!$this->isFlat($shape)) {
            $locationName = $shape->getMember()['locationName'] ?: 'member';
            $prefix .= ".{$locationName}";
        } elseif ($name = $this->queryName($items)) {
            $parts = explode('.', $prefix);
            $parts[count($parts) - 1] = $name;
            $prefix = implode('.', $parts);
        }
        foreach ($value as $k => $v) {
            $this->format($items, $v, $prefix . '.' . ($k + 1), $query);
        }
    }
    protected function format_map(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\MapShape $shape, array $value, $prefix, array &$query)
    {
        $vals = $shape->getValue();
        $keys = $shape->getKey();
        if (!$this->isFlat($shape)) {
            $prefix .= '.entry';
        }
        $i = 0;
        $keyName = '%s.%d.' . $this->queryName($keys, 'key');
        $valueName = '%s.%s.' . $this->queryName($vals, 'value');
        foreach ($value as $k => $v) {
            $i++;
            $this->format($keys, $k, sprintf($keyName, $prefix, $i), $query);
            $this->format($vals, $v, sprintf($valueName, $prefix, $i), $query);
        }
    }
    protected function format_blob(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Shape $shape, $value, $prefix, array &$query)
    {
        $query[$prefix] = base64_encode($value);
    }
    protected function format_timestamp(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\TimestampShape $shape, $value, $prefix, array &$query)
    {
        $query[$prefix] = \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\TimestampShape::format($value, 'iso8601');
    }
    protected function format_boolean(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Shape $shape, $value, $prefix, array &$query)
    {
        $query[$prefix] = $value ? 'true' : 'false';
    }
}
