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

class CsvRfcWriteStreamFilter extends \php_user_filter
{
    const FILTERNAME_DEFAULT = 'csv.rfc.write';

    private $filternameEnclosure = '"';

    /**
     * @param string $filtername
     *
     * @return bool
     */
    public static function register($filtername = self::FILTERNAME_DEFAULT)
    {
        return stream_filter_register($filtername, 'Ajgl\Csv\Rfc\CsvRfcWriteStreamFilter');
    }

    /**
     * @return bool
     */
    public function onCreate()
    {
        $this->extractEnclosureFromFilternameIfAvailable();

        return true;
    }

    private function extractEnclosureFromFilternameIfAvailable()
    {
        if (strlen($this->filtername) === strlen(self::FILTERNAME_DEFAULT) + 2 && strpos($this->filtername, self::FILTERNAME_DEFAULT.'.') === 0) {
            $this->filternameEnclosure = substr($this->filtername, -1);
        }
    }

    /**
     * @param resource $in
     * @param resource $out
     * @param int      $consumed
     * @param bool     $closing
     *
     * @return int
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        $enclosure = $this->resolveEnclosure();

        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = CsvRfcUtils::fixEnclosureEscape($enclosure, $bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }

    /**
     * @return string
     */
    private function resolveEnclosure()
    {
        if (is_array($this->params) && isset($this->params['enclosure']) && strlen($this->params['enclosure']) === 1) {
            return $this->params['enclosure'];
        }

        return $this->filternameEnclosure;
    }
}
