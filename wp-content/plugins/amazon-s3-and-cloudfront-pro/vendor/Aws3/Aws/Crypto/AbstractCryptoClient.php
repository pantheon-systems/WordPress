<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\Crypto;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Crypto\Cipher\CipherMethod;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Crypto\Cipher\Cbc;
use DeliciousBrains\WP_Offload_Media\Aws3\GuzzleHttp\Psr7\Stream;
/**
 * @internal
 */
abstract class AbstractCryptoClient
{
    public static $supportedCiphers = ['cbc', 'gcm'];
    /**
     * Returns if the passed cipher name is supported for encryption by the SDK.
     *
     * @param string $cipherName The name of a cipher to verify is registered.
     *
     * @return bool If the cipher passed is in our supported list.
     */
    public static function isSupportedCipher($cipherName)
    {
        return in_array($cipherName, self::$supportedCiphers);
    }
    /**
     * Returns an identifier recognizable by `openssl_*` functions, such as
     * `aes-256-cbc` or `aes-128-ctr`.
     *
     * @param string $cipherName Name of the cipher being used for encrypting
     *                           or decrypting.
     * @param int $keySize Size of the encryption key, in bits, that will be
     *                     used.
     *
     * @return string
     */
    protected abstract function getCipherOpenSslName($cipherName, $keySize);
    /**
     * Constructs a CipherMethod for the given name, initialized with the other
     * data passed for use in encrypting or decrypting.
     *
     * @param string $cipherName Name of the cipher to generate for encrypting.
     * @param string $iv Base Initialization Vector for the cipher.
     * @param int $keySize Size of the encryption key, in bits, that will be
     *                     used.
     *
     * @return CipherMethod
     *
     * @internal
     */
    protected abstract function buildCipherMethod($cipherName, $iv, $keySize);
    /**
     * Performs a reverse lookup to get the openssl_* cipher name from the
     * AESName passed in from the MetadataEnvelope.
     *
     * @param $aesName
     *
     * @return string
     *
     * @internal
     */
    protected abstract function getCipherFromAesName($aesName);
    /**
     * Dependency to provide an interface for building an encryption stream for
     * data given cipher details, metadata, and materials to do so.
     *
     * @param Stream $plaintext Plain-text data to be encrypted using the
     *                          materials, algorithm, and data provided.
     * @param array $cipherOptions Options for use in determining the cipher to
     *                             be used for encrypting data.
     * @param MaterialsProvider $provider A provider to supply and encrypt
     *                                    materials used in encryption.
     * @param MetadataEnvelope $envelope A storage envelope for encryption
     *                                   metadata to be added to.
     *
     * @return AesStreamInterface
     *
     * @internal
     */
    public abstract function encrypt(\DeliciousBrains\WP_Offload_Media\Aws3\GuzzleHttp\Psr7\Stream $plaintext, array $cipherOptions, \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Crypto\MaterialsProvider $provider, \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Crypto\MetadataEnvelope $envelope);
    /**
     * Dependency to provide an interface for building a decryption stream for
     * cipher text given metadata and materials to do so.
     *
     * @param string $cipherText Plain-text data to be decrypted using the
     *                           materials, algorithm, and data provided.
     * @param MaterialsProvider $provider A provider to supply and encrypt
     *                                    materials used in encryption.
     * @param MetadataEnvelope $envelope A storage envelope for encryption
     *                                   metadata to be read from.
     * @param array $cipherOptions Additional verification options.
     *
     * @return AesStreamInterface
     *
     * @internal
     */
    public abstract function decrypt($cipherText, \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Crypto\MaterialsProvider $provider, \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Crypto\MetadataEnvelope $envelope, array $cipherOptions = []);
}
