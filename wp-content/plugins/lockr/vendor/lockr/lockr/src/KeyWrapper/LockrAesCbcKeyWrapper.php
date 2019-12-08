<?php
namespace Lockr\KeyWrapper;

class LockrAesCbcKeyWrapper implements KeyWrapperInterface
{
    const CIPHER = MCRYPT_RIJNDAEL_256;

    const MODE = MCRYPT_MODE_CBC;

    /**
     * {@inheritdoc}
     */
    public static function enabled()
    {
        return function_exists('mcrypt_encrypt') && function_exists('openssl_encrypt');
    }

    /**
     * {@inheritdoc}
     */
    public static function encrypt($plaintext, $key = null)
    {
        if (is_null($key)) {
            $key = openssl_random_pseudo_bytes(32);
        }
        $iv_len = mcrypt_get_iv_size(self::CIPHER, self::MODE);
        $iv = mcrypt_create_iv($iv_len);

        $ciphertext = mcrypt_encrypt(self::CIPHER, $key, $plaintext, self::MODE, $iv);
        $ciphertext = base64_encode($ciphertext);
        $wrapping_key = self::encode(self::CIPHER, self::MODE, $iv, $key);
        return [
            'ciphertext' => $ciphertext,
            'encoded' => $wrapping_key,
        ];
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
        list($cipher, $mode, $iv, $key) = $parts;
        $ciphertext = mcrypt_encrypt($cipher, $key, $plaintext, $mode, $iv);
        $ciphertext = base64_encode($ciphertext);
        return [
            'ciphertext' => $ciphertext,
            'encoded' => $wrapping_key,
        ];
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
        list($cipher, $mode, $iv, $key) = $parts;
        $ciphertext = base64_decode($ciphertext);
        $plaintext = mcrypt_decrypt($cipher, $key, $ciphertext, $mode, $iv);
        if ($plaintext === false) {
            return false;
        }
        return trim($plaintext);
    }

    private static function encode($cipher, $mode, $iv, $key)
    {
        $parts = [$cipher, $mode, base64_encode($iv), base64_encode($key)];
        return implode('$', $parts);
    }

    private static function decode($wrapping_key)
    {
        $parts = explode('$', $wrapping_key, 4);
        if (!$parts || count($parts) != 4) {
            return false;
        }
        list($cipher, $mode, $iv, $key) = $parts;
        $iv = base64_decode($iv);
        $key = base64_decode($key);
        return [$cipher, $mode, $iv, $key];
    }
}

// ex: ts=4 sts=4 sw=4 et:
