<?php
namespace Lockr\KeyWrapper;

class LockrAes128CtrSha256KeyWrapper implements KeyWrapperInterface
{
    const PREFIX = 'aes-128-ctr-sha256';

    const METHOD = 'aes-128-ctr';

    const HASH_BYTES = 44;

    /**
     * {@inheritdoc}
     */
    public static function enabled()
    {
        return function_exists('openssl_encrypt');
    }

    /**
     * {@inheritdoc}
     */
    public static function encrypt($plaintext, $key = null)
    {
        if (is_null($key)) {
            $key = openssl_random_pseudo_bytes(16);
        }
        $iv_len = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($iv_len);
        $hmac_key = openssl_random_pseudo_bytes(32);
        return self::doEncrypt($plaintext, $key, $iv, $hmac_key);
    }

    /**
     * {@inheritdoc}
     */
    public static function reencrypt($plaintext, $wrapping_key)
    {
        $parts = self::decode($wrapping_key);
        if (!$parts) {
            return false;
        }
        list($key, $iv, $hmac_key) = $parts;
        return self::doEncrypt($plaintext, $key, $iv, $hmac_key);
    }

    /**
     * {@inheritdoc}
     */
    public static function decrypt($ciphertext, $wrapping_key)
    {
        $parts = self::decode($wrapping_key);
        if (!$parts) {
            return false;
        }
        list($key, $iv, $hmac_key) = $parts;

        $hmac = base64_decode(substr($ciphertext, 0, self::HASH_BYTES));
        $ciphertext = base64_decode(substr($ciphertext, self::HASH_BYTES));

        if (!hash_equals($hmac, self::hmac($ciphertext, $hmac_key))) {
            return false;
        }

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($plaintext === false) {
            return false;
        }

        return $plaintext;
    }

    private static function doEncrypt($plaintext, $key, $iv, $hmac_key)
    {
        $ciphertext = openssl_encrypt(
            $plaintext,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        $hmac = self::hmac($ciphertext, $hmac_key);
        return [
            'ciphertext' => base64_encode($hmac) . base64_encode($ciphertext),
            'encoded' => self::encode($key, $iv, $hmac_key),
        ];
    }

    private static function hmac($data, $key)
    {
        return hash_hmac('sha256', $data, $key, true);
    }

    private static function encode($key, $iv, $hmac_key)
    {
        $parts = [
            self::PREFIX,
            base64_encode($key),
            base64_encode($iv),
            base64_encode($hmac_key),
        ];
        return implode('$', $parts);
    }

    private static function decode($wrapping_key)
    {
        $parts = explode('$', $wrapping_key, 4);
        if (!$parts || count($parts) !== 4) {
            return false;
        }
        list($prefix, $key, $iv, $hmac_key) = $parts;
        if ($prefix !== self::PREFIX) {
            return false;
        }
        return [
            base64_decode($key),
            base64_decode($iv),
            base64_decode($hmac_key),
        ];
    }
}

// ex: ts=4 sts=4 sw=4 et:
