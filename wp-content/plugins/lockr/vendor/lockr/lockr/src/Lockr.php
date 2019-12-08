<?php
namespace Lockr;

use RuntimeException;

use GuzzleHttp\Psr7;
use Symfony\Component\Yaml\Yaml;

use Lockr\KeyWrapper\MultiKeyWrapper;

class Lockr
{
    /** @var LockrClient $client */
    protected $client;

    /** @var SecretInfoInterface $info */
    private $info;

    /** @var string $accountsHost */
    private $accountsHost;

    /**
     * @param LoaderInterface $loader
     */
    public function __construct(
        LockrClient $client,
        SecretInfoInterface $secret_info,
        $accounts_host = 'accounts.lockr.io'
    ) {
        $this->client = $client;
        $this->info = $secret_info;
        $this->accountsHost = $accounts_host;
    }

    public function createCertClient($client_token, array $dn)
    {
        $key = openssl_pkey_new(['private_key_bits' => 2048]);
        if ($key === false) {
            throw new RuntimeException('Could not create private key.');
        }
        if (!openssl_pkey_export($key, $key_text)) {
            throw new RuntimeException('Could not export private key.');
        }
        $csr = openssl_csr_new($dn, $key);
        if ($csr === false) {
            throw new RuntimeException('Could not create CSR.');
        }
        if (!openssl_csr_export($csr, $csr_text)) {
            throw new RuntimeException('Could not export CSR.');
        }

        $query = <<<'EOQ'
mutation CreateCertClient($input: CreateCertClient!) {
  createCertClient(input: $input) {
    env
    auth {
      ... on LockrCert {
        certText
      }
    }
  }
}
EOQ;
        $t0 = microtime(true);
        $data = $this->client->query([
            'query' => $query,
            'variables' => [
                'input' => [
                    'token' => $client_token,
                    'csrText' => $csr_text,
                ],
            ],
        ]);
        $t1 = microtime(true);
        $this->client->getStats()
            ->lockrCallCompleted('create_cert_client', $t1 - $t0);
        return [
            'key_text' => $key_text,
            'cert_text' => $data['createCertClient']['auth']['certText'],
            'env' => $data['createCertClient']['env'],
        ];
    }

    public function createPantheonClient($client_token)
    {
        $query = <<<'EOQ'
mutation CreatePantheonClient($input: CreatePantheonClient!) {
  createPantheonClient(input: $input) {
    id
  }
}
EOQ;
        $t0 = microtime(true);
        $this->client->query([
            'query' => $query,
            'variables' => [
                'input' => [
                    'token' => $client_token,
                ],
            ],
        ]);
        $t1 = microtime(true);
        $this->client->getStats()
            ->lockrCallCompleted('create_pantheon_client', $t1 - $t0);
    }

    /**
     * Whether the request will have a client cert attached.
     *
     * @return bool
     */
    public function hasCert()
    {
        return $this->client->hasCert();
    }

    /**
     * Gets client info
     *
     * @return array
     */
    public function getInfo()
    {
        $query = <<<'EOQ'
{
    self {
        env
        label
        keyring {
            id
            label
            hasCreditCard
            trialEnd
        }
        auth {
            ... on LockrCert {
                expires
            }
        }
    }
}
EOQ;
        $t0 = microtime(true);
        $data = $this->client->query(['query' => $query]);
        $t1 = microtime(true);
        $this->client->getStats()
            ->lockrCallCompleted('get_info', $t1 - $t0);
        return $data['self'];
    }

    /**
     * Creates a secret value by name.
     *
     * @param string $name
     * @param string $value
     * @param string|null $label
     * @param string|null $sovereignty
     *
     * @return string
     */
    public function createSecretValue($name, $value, $label = null, $sovereignty = null)
    {
        $info = $this->info->getSecretInfo($name);
        if (isset($info['wrapping_key'])) {
            $ret = MultiKeyWrapper::reencrypt($value, $info['wrapping_key']);
        } else {
            $ret = MultiKeyWrapper::encrypt($value);
        }
        $info['wrapping_key'] = $ret['encoded'];
        $value = $ret['ciphertext'];
        $query = <<<'EOQ'
mutation EnsureSecret($input: EnsureSecretValue!) {
  ensureSecretValue(input: $input) {
    id
  }
}
EOQ;
        if (is_null($label)) {
            $label = '';
        }
        $input = [
            'name' => $name,
            'label' => $label,
            'value' => base64_encode($value),
        ];
        if (!is_null($sovereignty)) {
            $input['sovereignty'] = $sovereignty;
        }
        $t0 = microtime(true);
        $data = $this->client->query([
            'query' => $query,
            'variables' => [
                'input' => $input,
            ],
        ]);
        $t1 = microtime(true);
        $this->client->getStats()
            ->lockrCallCompleted('create_secret_value', $t1 - $t0);
        $this->info->setSecretInfo($name, $info);
        return $data['ensureSecretValue']['id'];
    }

    /**
     * Gets the latest value of a secret by name.
     *
     * @param string $name
     *
     * @return string
     */
    public function getSecretValue($name)
    {
        $query = <<<'EOQ'
query LatestSecretValue($name: String!) {
    self {
        secret(name: $name) {
            latest {
                value
            }
        }
    }
}
EOQ;
        $t0 = microtime(true);
        $data = $this->client->query([
            'query' => $query,
            'variables' => [
                'name' => $name,
            ],
        ]);
        $t1 = microtime(true);
        $this->client->getStats()
            ->lockrCallCompleted('get_secret_value', $t1 - $t0);
        if (!isset($data['self']['secret']['latest']['value'])) {
            return null;
        }
        $value = $data['self']['secret']['latest']['value'];
        $value = base64_decode($value);
        $info = $this->info->getSecretInfo($name);
        if (isset($info['wrapping_key'])) {
            $wk = $info['wrapping_key'];
            $value = MultiKeyWrapper::decrypt($value, $wk);
        }
        return $value;
    }

    /**
     * Deletes versions of a key in this client's environment.
     *
     * @param string $name
     */
    public function deleteSecretValue($name)
    {
        $query = <<<'EOQ'
mutation Delete($input: DeleteClientVersions!) {
    deleteClientVersions(input: $input)
}
EOQ;
        $t0 = microtime(true);
        $this->client->query([
            'query' => $query,
            'variables' => [
                'input' => [
                    'secretName' => $name,
                ],
            ],
        ]);
        $t1 = microtime(true);
        $this->client->getStats()
            ->lockrCallCompleted('delete_secret_value', $t1 - $t0);
    }

    /**
     * Generates a new random key.
     *
     * @param int $size
     *
     * @return string
     */
    public function generateKey($size = 256)
    {
        $query = <<<'EOQ'
query RandomKey($size: KeySize) {
    randomKey(size: $size)
}
EOQ;
        if ($size !== 256 && $size !== 192 && $size !== 128) {
            throw new \Exception("Invalid key size: {$size}");
        }
        $t0 = microtime(true);
        $data = $this->client->query([
            'query' => $query,
            'variables' => ['size' => "AES{$size}"],
        ]);
        $t1 = microtime(true);
        $this->client->getStats()
            ->lockrCallCompleted('generate_key', $t1 - $t0);
        return base64_decode($data['randomKey']);
    }

    /**
     * Exports secret data to YAML.
     *
     * @return string
     */
    public function exportSecretData()
    {
        $data = $this->info->getAllSecretInfo();
        return Yaml::dump($data, 2, 2);
    }

    /**
     * Imports secret data from YAML.
     *
     * @param string $info_yaml
     */
    public function importSecretData($info_yaml)
    {
        $data = Yaml::parse($info_yaml);
        foreach ($data as $name => $info) {
            $this->info->setSecretInfo($name, $info);
        }
    }

    /**
     * Requests a dev client token for a new or existing keyring.
     *
     * @param string $email
     * @param string $password
     * @param string $keyring_label
     * @param string|null $client_label
     * @param string|null $keyring_id
     */
    public function requestClientToken(
        $email,
        $password,
        $keyring_label,
        $client_label = null,
        $keyring_id = null
    ) {
        $uri = (new Psr7\Uri())
            ->withScheme('https')
            ->withHost($this->accountsHost)
            ->withPath('/lockr-api/register');
        $data = [
            'email' => $email,
            'password' => $password,
            'keyring_label' => $keyring_label,
        ];
        if (!is_null($client_label)) {
            $data['client_label'] = $client_label;
        }
        if (!is_null($keyring_id)) {
            $data['keyring_id'] = $keyring_id;
        }
        $resp = $this->client->getHttpClient()->request('POST', $uri, [
            'headers' => [
                'content-type' => 'application/json',
                'accept' => 'application/json',
            ],
            'json' => $data,
            'timeout' => 30,
        ]);
        return json_decode((string) $resp->getBody(), true);
    }
}

// ex: ts=4 sts=4 sw=4 et:
