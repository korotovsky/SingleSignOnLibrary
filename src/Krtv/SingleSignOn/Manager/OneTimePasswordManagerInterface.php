<?php

namespace Krtv\SingleSignOn\Manager;

use Krtv\SingleSignOn\Model\OneTimePasswordInterface;

/**
 * Interface OneTimePasswordManagerInterface
 * @package FM\SingleSignOnBundle\Manager
 */
interface OneTimePasswordManagerInterface
{
    /**
     * Creates OTP
     *
     * @param string $hash
     * @return string
     */
    public function create($hash);

    /**
     * Fetches OTP
     *
     * @param $pass
     * @return OneTimePasswordInterface|null
     */
    public function get($pass);

    /**
     * Checks if OTP token is valid ot not
     *
     * @param OneTimePasswordInterface $otp
     * @return boolean
     */
    public function isValid(OneTimePasswordInterface $otp);

    /**
     * Invalidates OTP token
     *
     * @param OneTimePasswordInterface $otp
     * @return void
     */
    public function invalidate(OneTimePasswordInterface $otp);
}
