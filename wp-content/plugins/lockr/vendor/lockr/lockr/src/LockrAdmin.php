<?php
namespace Lockr;

use DateTime;

use GuzzleHttp;
use GuzzleHttp\Psr7;

class LockrAdmin
{
    /** @var LockrClient $client */
    protected $client;

    /**
     * @param LockrClient $client
     */
    public function __construct(LockrClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $label
     * @param bool $has_cc
     * @param DateTime $trial_end
     *
     * @return string
     */
    public function createKeyring($label, $has_cc, DateTime $trial_end)
    {
        $query = <<<'EOQ'
mutation CreateKeyring($input: CreateKeyring!) {
    createKeyring(input: $input) {
        id
    }
}
EOQ;
        $data = $this->client->query([
            'query' => $query,
            'variables' => [
                'input' => [
                    'label' => $label,
                    'hasCreditCard' => $has_cc,
                    'trialEnd' => $trial_end->format(DateTime::RFC3339),
                ],
            ],
        ]);
        return $data['createKeyring']['id'];
    }

    /**
     * @param string $keyring_id
     * @param string $label
     */
    public function updateKeyringLabel($keyring_id, $label)
    {
        $query = <<<'EOQ'
mutation UpdateKeyring($input: UpdateKeyringLabel!) {
    updateKeyringLabel(input: $input) {
        id
    }
}
EOQ;
        $this->client->query([
            'query' => $query,
            'variables' => [
                'input' => [
                    'keyringId' => $keyring_id,
                    'label' => $label,
                ],
            ],
        ]);
    }

    /**
     * @param string $keyring_id
     * @param bool $has_cc
     */
    public function upateKeyringHasCreditCard($keyring_id, $has_cc)
    {
        $query = <<<'EOQ'
mutation UpdateKeyring($input: UpdateKeyringHasCreditCard!) {
    updateKeyringHasCreditCard(input: $input) {
        id
    }
}
EOQ;
        $this->client->query([
            'query' => $query,
            'variables' => [
                'input' => [
                    'keyringId' => $keyring_id,
                    'hasCreditCard' => $has_cc,
                ],
            ],
        ]);
    }

    /**
     * @param string $keyring_id
     * @param string $env
     * @param string $label
     *
     * @return string
     */
    public function createClientToken($keyring_id, $env, $label)
    {
        $query = <<<'EOQ'
mutation CreateClientToken($input: CreateClientToken!) {
  createClientToken(input: $input) {
    token
  }
}
EOQ;
        $data = $this->client->query([
            'query' => $query,
            'variables' => [
                'input' => [
                    'keyringId' => $keyring_id,
                    'clientLabel' => $label,
                    'clientEnv' => $env,
                ],
            ],
        ]);
        return $data['createClientToken']['token'];
    }

    /**
     * Gets a summary of secret usage.
     *
     * @param DateTime $start
     * @param DateTime $end
     *
     * @return array
     */
    public function getUsageSummary(DateTime $start, DateTime $end)
    {
        $query = <<<'EOQ'
query Usage($from: DateTime!, $to: DateTime!) {
    admin {
        usageSummary(from: $from, to: $to) {
            month
            usage
            version {
                env
                secret {
                    id
                    name
                    label
                    keyring {
                        id
                    }
                }
            }
        }
    }
}
EOQ;
        $data = $this->client->query([
            'query' => $query,
            'variables' => [
                'from' => $start->format(DateTime::RFC3339),
                'to' => $end->format(DateTime::RFC3339),
            ],
        ]);
        return $data['admin']['usageSummary'];
    }
}

// ex: ts=4 sts=4 sw=4 et:
