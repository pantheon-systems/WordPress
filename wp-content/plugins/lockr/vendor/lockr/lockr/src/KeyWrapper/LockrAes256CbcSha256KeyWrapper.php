<?php
namespace Lockr\KeyWrapper;

class LockrAes256CbcSha256KeyWrapper implements KeyWrapperInterface
{
    const PREFIX = '$1$';
    const METHOD = 'aes-256-cbc';
    const KEY_LEN = 32;
    const IV_LEN = 16;
    const HMAC_KEY_LEN = 32;

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
            $key = random_bytes(self::KEY_LEN);
        }
        $iv = random_bytes(self::IV_LEN);
        return self::doEncrypt($plaintext, $key, $iv);
    }

    /**
     * {@inheritdoc}
     */
    public static function reencrypt($plaintext, $wrapping_key)
    {
        $wrapping_key = substr($wrapping_key, strlen(self::PREFIX));
        $wrapping_key = base64_decode($wrapping_key);
        $iv = random_bytes(self::IV_LEN);
        return self::doEncrypt($plaintext, $wrapping_key, $iv);
    }

    /**
     * {@inheritdoc}
     */
    public static function decrypt($ciphertext, $wrapping_key)
    {
        $wrapping_key = substr($wrapping_key, strlen(self::PREFIX));
        $wrapping_key = base64_decode($wrapping_key);
        $key_data = hash('sha512', $wrapping_key, true);
        $enc_key = substr($key_data, 0, self::KEY_LEN);
        $hmac_key = substr($key_data, self::KEY_LEN);

        $iv = substr($ciphertext, 0, self::IV_LEN);
        $hmac0 = substr($ciphertext, -self::HMAC_KEY_LEN);
        $ciphertext = substr($ciphertext, self::IV_LEN, -self::HMAC_KEY_LEN);

        $hmac1 = self::hmac($iv, $ciphertext, $hmac_key);
        if (!hash_equals($hmac0, $hmac1)) {
            return false;
        }

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::METHOD,
            $enc_key,
            OPENSSL_RAW_DATA,
            $iv
        );
        if ($plaintext === false) {
            return false;
        }
        return $plaintext;
    }

    protected static function doEncrypt($plaintext, $key, $iv)
    {
        $key_data = hash('sha512', $key, true);
        $enc_key = substr($key_data, 0, self::KEY_LEN);
        $hmac_key = substr($key_data, self::KEY_LEN);
        $ciphertext = openssl_encrypt(
            $plaintext,
            self::METHOD,
            $enc_key,
            OPENSSL_RAW_DATA,
            $iv
        );
        $hmac = self::hmac($iv, $ciphertext, $hmac_key);
        return [
            'ciphertext' => $iv . $ciphertext . $hmac,
            'encoded' => self::PREFIX . base64_encode($key),
        ];
    }

    protected static function hmac($iv, $ciphertext, $key)
    {
        $data = self::PREFIX . self::METHOD . $iv . $ciphertext;
        return hash_hmac('sha256', $data, $key, true);
    }
}

// ex: ts=4 sts=4 sw=4 et:
