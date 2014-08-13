<?php

namespace Krtv\SingleSignOn\Manager\Http;

use Krtv\SingleSignOn\Model\OneTimePassword;
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
     * @param $hash
     * @return OneTimePassword|void
     */
    public function create($hash)
    {
        throw new \BadMethodCallException('Service Provider can\'t create OTP tokens.');
    }

    /**
     * @param $pass
     * @return \Krtv\SingleSignOn\Model\OneTimePassword|null
     */
    public function get($pass)
    {
        return $this->provider->fetch($pass);
    }

    /**
     * @param OneTimePassword $otp
     * @return bool
     */
    public function isValid(OneTimePassword $otp)
    {
        return $otp->getUsed() === false;
    }

    /**
     * Rest service must invalidate OTP token immediate after fetch.
     *
     * @param OneTimePassword $otp
     */
    public function invalidate(OneTimePassword $otp)
    {
    }
}