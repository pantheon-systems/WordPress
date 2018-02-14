<?php

/*
 * AJGL CSV RFC Component
 *
 * Copyright (C) Antonio J. García Lagar <aj@garcialagar.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wpae\Csv;

class CsvRcfWriter
{
    /**
     * @see http://php.net/manual/en/function.fputcsv.php
     *
     * @param resource $handle
     * @param array $fields
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public static function fputcsv($handle, array $fields, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        CsvRfcUtils::fPutCsv($handle, $fields, $delimiter, $enclosure, $escape);
    }

    /**
     * @see http://php.net/manual/en/function.fgetcsv.php
     *
     * @param resource $handle
     * @param array $length
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     *
     * @return array|false|null
     */
    public static function fgetcsv($handle, $length = 0, $delimiter = ',', $enclosure = '"', $escape = '"')
    {
        return CsvRfcUtils::fGetCsv($handle, $length, $delimiter, $enclosure, $escape);
    }

    /**
     * @see http://php.net/manual/en/function.str_getcsv.php
     *
     * @param string $input
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     *
     * @return array
     */
    public static function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '"')
    {
        return CsvRfcUtils::strGetCsv($input, $delimiter, $enclosure, $escape);
    }

}