<?php
namespace Lockr;

interface SecretInfoInterface
{
    /**
     * Gets secret info for a secret by name.
     *
     * @param string $name
     *
     * @return array
     */
    public function getSecretInfo($name);

    /**
     * Sets secret info for a secret by name.
     *
     * @param string $name
     * @param array $info
     */
    public function setSecretInfo($name, array $info);

    /**
     * Returns all secret info.
     *
     * @return array
     */
    public function getAllSecretInfo();
}

// ex: ts=4 sts=4 sw=4 et:
