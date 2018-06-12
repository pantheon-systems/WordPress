<?php

namespace Wpae\Csv;


class CsvWriter
{
    const CSV_STRATEGY_DEFAULT = 'default';
    const CSV_STRATEGY_CUSTOM = 'custom';

    private $csvStrategy;

    public function __construct($csvStrategy = self::CSV_STRATEGY_DEFAULT)
    {
        $this->csvStrategy = $csvStrategy;
    }

    public function writeCsv($resource, $value, $delimiter)
    {
        if($this->csvStrategy == self::CSV_STRATEGY_DEFAULT) {
            fputcsv($resource, $value, $delimiter);
        } else {
            CsvRcfWriter::fputcsv($resource, $value, $delimiter);
        }
    }
}