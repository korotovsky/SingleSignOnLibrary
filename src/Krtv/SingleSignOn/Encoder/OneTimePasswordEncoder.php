<?php

namespace Krtv\SingleSignOn\Encoder;

/**
 * Class OneTimePasswordEncoder
 * @package Krtv\SingleSignOn\Encoder
 */
class OneTimePasswordEncoder
{
    const HASH_DELIMITER = ':';

    /**
     * @var string
     */
    private $key;

    /**
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @param $hash
     * @return array
     */
    public function decodeHash($hash)
    {
        return explode(self::HASH_DELIMITER, base64_decode($hash));
    }

    /**
     * @param array $parts
     * @return string
     */
    public function encodeHash(array $parts)
    {
        return base64_encode(implode(self::HASH_DELIMITER, $parts));
    }

    /**
     * @param $username
     * @param $expires
     * @return string
     */
    public function generateHash($username, $expires)
    {
        return hash('sha256', $username.$expires.$this->key);
    }

    /**
     * @param $hash1
     * @param $hash2
     * @return bool
     */
    public function compareHashes($hash1, $hash2)
    {
        if (strlen($hash1) !== $c = strlen($hash2)) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < $c; $i++) {
            $result |= ord($hash1[$i]) ^ ord($hash2[$i]);
        }

        return 0 === $result;
    }

    /**
     * @param $username
     * @param $expires
     * @return string
     */
    public function generateOneTimePasswordValue($username, $expires)
    {
        return $this->encodeHash(array(
            base64_encode($username),
            $expires,
            $this->generateHash($username, $expires)
        ));
    }
}
