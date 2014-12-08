<?php

namespace Krtv\SingleSignOn\Manager;

use Krtv\SingleSignOn\Model\OneTimePassword;

/**
 * Interface OneTimePasswordManagerInterface
 * @package FM\SingleSignOnBundle\Manager
 */
interface OneTimePasswordManagerInterface
{
    /**
     * Create OTP
     *
     * @param string $hash
     * @return string
     */
    public function create($hash);

    /**
     * Fetch OTP
     *
     * @param $pass
     * @return OneTimePassword|null
     */
    public function get($pass);

    /**
     * @param OneTimePassword $otp
     * @return boolean
     */
    public function isValid(OneTimePassword $otp);

    /**
     * @param OneTimePassword $otp
     * @return void
     */
    public function invalidate(OneTimePassword $otp);
}
