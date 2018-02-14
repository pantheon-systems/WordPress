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

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class CsvRfcUtils
{
    const EOL_WRITE_DEFAULT = "\n";
    const EOL_WRITE_RFC = "\r\n";

    private static $defaultEol = self::EOL_WRITE_DEFAULT;

    private function __construct()
    {
    }

    /**
     * @see http://php.net/manual/en/function.fputcsv.php
     *
     * @param resource $handle
     * @param array    $fields
     * @param string   $delimiter
     * @param string   $enclosure
     * @param string   $escape
     * @param string   $eol
     */
    public static function fPutCsv($handle, array $fields, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = null)
    {
        self::checkPutCsvEscape($escape);

        $eol = self::resolveEol($eol);
        if ($eol !== self::EOL_WRITE_DEFAULT || self::hasAnyValueWithEscapeFollowedByEnclosure($fields, $enclosure)) {
            \fwrite($handle, self::strPutCsv($fields, $delimiter, $enclosure, $eol));
        } else {
            \fputcsv($handle, $fields, $delimiter, $enclosure);
        }
    }

    /**
     * @param array  $fields
     * @param string $enclosure
     *
     * @return bool
     */
    private static function hasAnyValueWithEscapeFollowedByEnclosure(array $fields, $enclosure)
    {
        foreach ($fields as $value) {
            if (strpos($value, '\\'.$enclosure) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string | null $eol
     *
     * @return bool
     */
    private static function resolveEol($eol)
    {
        return $eol === null ? self::$defaultEol : (string) $eol;
    }

    /**
     * @see http://php.net/manual/en/function.fgetcsv.php
     *
     * @param resource $handle
     * @param int      $length
     * @param string   $delimiter
     * @param string   $enclosure
     * @param string   $escape
     *
     * @return array|false|null
     */
    public static function fGetCsv($handle, $length = 0, $delimiter = ',', $enclosure = '"', $escape = '"')
    {
        self::checkGetCsvEscape($enclosure, $escape);

        return \fgetcsv($handle, $length, $delimiter, $enclosure, $enclosure);
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
    public static function strGetCsv($input, $delimiter = ',', $enclosure = '"', $escape = '"')
    {
        self::checkGetCsvEscape($enclosure, $escape);

        return \str_getcsv($input, $delimiter, $enclosure, $enclosure);
    }

    /**
     * This code was borrowed from goodby/csv under MIT LICENSE.
     *
     * @author Hidehito Nozawa <suinyeze@gmail.com>
     *
     * @see    https://github.com/goodby/csv
     * @see    https://github.com/goodby/csv/blob/c6677d9c68323ef734a67a34f3e5feabcafd5b4e/src/Goodby/CSV/Export/Standard/CsvFileObject.php#L46
     *
     * @param array  $fields
     * @param string $delimiter
     * @param string $enclosure
     * @param string $eol
     *
     * @return string
     */
    public static function strPutCsv(array $fields, $delimiter = ',', $enclosure = '"', $eol = self::EOL_WRITE_DEFAULT)
    {
        $file = new \SplTempFileObject();
        $file->fputcsv($fields, $delimiter, $enclosure);
        $file->rewind();

        $line = '';
        while (!$file->eof()) {
            $line .= $file->fgets();
        }

        $line = self::fixEnclosureEscape($enclosure, $line);

        if ($eol !== self::EOL_WRITE_DEFAULT) {
            $line = rtrim($line, "\n").$eol;
        }

        return $line;
    }

    /**
     * Fix the enclosure escape in the given CSV raw line.
     *
     * @param string $enclosure
     * @param string $line
     */
    public static function fixEnclosureEscape($enclosure, $line)
    {
        return \str_replace('\\'.$enclosure, '\\'.$enclosure.$enclosure, $line);
    }

    /**
     * Emits a warning if the escape char is not the default backslash or null.
     *
     * @param string $escape
     */
    public static function checkPutCsvEscape($escape)
    {
        if ($escape !== '\\' && $escape !== null) {
            trigger_error(
                sprintf(
                    "In writing mode, the escape char must be a backslash '\\'. "
                        ."The given escape char '%s' will be ignored.",
                    $escape
                ),
                E_USER_WARNING
            );
        }
    }

    /**
     * Emits a warning if the enclosure char and escape char are different.
     *
     * @param string $enclosure
     * @param string $escape
     */
    public static function checkGetCsvEscape($enclosure, $escape)
    {
        if ($enclosure !== $escape) {
            trigger_error(
                sprintf(
                    'In reading mode, the escape and enclosure chars must be equals. '
                        ."The given escape char '%s' will be ignored.",
                    $escape
                ),
                E_USER_WARNING
            );
        }
    }

    /**
     * @param string $eol
     */
    public static function setDefaultWriteEol($eol)
    {
        self::$defaultEol = $eol;
    }

    /**
     * @return string $eol
     */
    public static function getDefaultWriteEol()
    {
        return self::$defaultEol;
    }
}
