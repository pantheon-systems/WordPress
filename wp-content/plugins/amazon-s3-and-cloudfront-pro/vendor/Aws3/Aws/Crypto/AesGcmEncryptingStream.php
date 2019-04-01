<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\Crypto;

use DeliciousBrains\WP_Offload_Media\Aws3\GuzzleHttp\Psr7;
use DeliciousBrains\WP_Offload_Media\Aws3\GuzzleHttp\Psr7\StreamDecoratorTrait;
use DeliciousBrains\WP_Offload_Media\Aws3\Psr\Http\Message\StreamInterface;
use RuntimeException;
/**
 * @internal Represents a stream of data to be gcm encrypted.
 */
class AesGcmEncryptingStream implements \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Crypto\AesStreamInterface
{
    use StreamDecoratorTrait;
    private $aad;
    private $initializationVector;
    private $key;
    private $keySize;
    private $plaintext;
    private $tag = '';
    private $tagLength;
    /**
     * @param StreamInterface $plaintext
     * @param string $key
     * @param string $initializationVector
     * @param string $aad
     * @param int $tagLength
     * @param int $keySize
     */
    public function __construct(\DeliciousBrains\WP_Offload_Media\Aws3\Psr\Http\Message\StreamInterface $plaintext, $key, $initializationVector, $aad = '', $tagLength = 16, $keySize = 256)
    {
        if (version_compare(PHP_VERSION, '7.1', '<')) {
            throw new \RuntimeException('AES-GCM decryption is only supported in PHP 7.1 or greater');
        }
        $this->plaintext = $plaintext;
        $this->key = $key;
        $this->initializationVector = $initializationVector;
        $this->aad = $aad;
        $this->tagLength = $tagLength;
        $this->keySize = $keySize;
    }
    public function getOpenSslName()
    {
        return "aes-{$this->keySize}-gcm";
    }
    public function getAesName()
    {
        return 'AES/GCM/NoPadding';
    }
    public function getCurrentIv()
    {
        return $this->initializationVector;
    }
    public function createStream()
    {
        return \DeliciousBrains\WP_Offload_Media\Aws3\GuzzleHttp\Psr7\stream_for(openssl_encrypt((string) $this->plaintext, $this->getOpenSslName(), $this->key, OPENSSL_RAW_DATA, $this->initializationVector, $this->tag, $this->aad, $this->tagLength));
    }
    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }
    public function isWritable()
    {
        return false;
    }
}
