<?php
namespace Lockr;

class LockrSettings implements SettingsInterface
{
    /** @var string $certPath */
    private $certPath;

    /** @var string $host */
    private $host;

    /** @var string $certPassword */
    private $certPassword;

    /**
     * @param string|null $cert_path
     * @param string|null $host
     * @param string|null $cert_password
     */
    public function __construct($cert_path = null, $host = null, $cert_password = null)
    {
        $this->certPath = $cert_path;
        $this->host = $host;
        $this->certPassword = $cert_password;
    }

    /**
     * {@inheritdoc}
     */
    public function getHostname()
    {
        return $this->host ?: 'api.lockr.io';
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        $opts = [];
        if ($this->certPath) {
            if ($this->certPassword) {
                $opts['cert'] = [$this->certPath, $this->certPassword];
            } else {
                $opts['cert'] = $this->certPath;
            }
        }
        return $opts;
    }
}

// ex: ts=4 sts=4 sw=4 et:
