<?php

namespace Krtv\SingleSignOn\Manager\Http;

use Krtv\SingleSignOn\Model\OneTimePasswordInterface;
use Krtv\SingleSignOn\Manager\OneTimePasswordManagerInterface;
use Krtv\SingleSignOn\Manager\Http\Provider\ProviderInterface;

/**
 * Class OneTimePasswordManager
 * @package Krtv\SingleSignOn\Manager\Http
 */
class OneTimePasswordManager implements OneTimePasswordManagerInterface
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @param ProviderInterface $provider
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Creates OTP
     *
     * @param string $hash
     * @return string
     */
    public function create($hash)
    {
        throw new \BadMethodCallException('Service Provider can\'t create OTP tokens.');
    }

    /**
     * Fetches OTP
     *
     * @param $pass
     * @return OneTimePasswordInterface|null
     */
    public function get($pass)
    {
        return $this->provider->fetch($pass);
    }

    /**
     * Checks if OTP token is valid ot not
     *
     * @param OneTimePasswordInterface $otp
     * @return boolean
     */
    public function isValid(OneTimePasswordInterface $otp)
    {
        return $otp->getUsed() === false;
    }

    /**
     * Rest service must invalidate OTP token immediate after fetch.
     *
     * @param OneTimePasswordInterface $otp
     */
    public function invalidate(OneTimePasswordInterface $otp)
    {
    }
}
