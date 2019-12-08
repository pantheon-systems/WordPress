<?php
namespace Lockr\KeyWrapper;

interface KeyWrapperInterface
{
    /**
     * @return bool
     */
    public static function enabled();

    /**
     * Encrypt the given plaintext.
     *
     * @param string $plaintext
     * @param string|null $key
     *
     * @return array
     */
    public static function encrypt($plaintext, $key = null);

    /**
     * Encrypt the given plaintext reusing state.
     *
     * @param string $plaintext
     * @param string $wrapping_key
     *
     * @return array
     */
    public static function reencrypt($plaintext, $wrapping_key);

    /**
     * Decrypt the given ciphertext.
     *
     * @param string $ciphertext
     * @param string $wrapping_key
     *
     * @return string|bool
     */
    public static function decrypt($ciphertext, $wrapping_key);
}

// ex: ts=4 sts=4 sw=4 et:
