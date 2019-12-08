<?php
namespace Lockr\KeyWrapper;

class MultiKeyWrapper implements KeyWrapperInterface
{
    private static $wrappers = [
        LockrAes256CbcSha256KeyWrapper::PREFIX => LockrAes256CbcSha256KeyWrapper::class,
        LockrAes128CtrSha256KeyWrapper::PREFIX => LockrAes128CtrSha256KeyWrapper::class,
        '' => LockrAesCbcKeyWrapper::class,
    ];

    /**
     * {@inheritdoc}
     */
    public static function enabled()
    {
        foreach (self::$wrappers as $wrapper) {
            if ($wrapper::enabled()) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function encrypt($plaintext, $key = null)
    {
        foreach (self::$wrappers as $wrapper) {
            if ($wrapper::enabled()) {
                return $wrapper::encrypt($plaintext, $key);
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function reencrypt($plaintext, $wrapping_key)
    {
        foreach (self::$wrappers as $prefix => $wrapper) {
            if (strpos($wrapping_key, $prefix) === 0) {
                return $wrapper::reencrypt($plaintext, $wrapping_key);
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function decrypt($ciphertext, $wrapping_key)
    {
        foreach (self::$wrappers as $prefix => $wrapper) {
            if (strpos($wrapping_key, $prefix) === 0) {
                return $wrapper::decrypt($ciphertext, $wrapping_key);
            }
        }
        return false;
    }
}

// ex: ts=4 sts=4 sw=4 et:
