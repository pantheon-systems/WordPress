<?php
namespace Lockr;

class BlackholeStats implements LockrStatsInterface
{
    /**
     * {@inheritdoc}
     */
    public function lockrCallCompleted($name, $elapsed)
    {
    }
}

// ex: ts=4 sts=4 sw=4 et:
