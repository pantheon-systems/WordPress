<?php
namespace Lockr;

interface LockrStatsInterface
{
    /**
     * Indicates that a call to Lockr was completed.
     *
     * @param string $name
     * @param float $elapsed
     */
    public function lockrCallCompleted($name, $elapsed);
}

// ex: ts=4 sts=4 sw=4 et:
