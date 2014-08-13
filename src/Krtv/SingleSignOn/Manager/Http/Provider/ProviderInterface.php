<?php

namespace Krtv\SingleSignOn\Manager\Http\Provider;

use Krtv\SingleSignOn\Model\OneTimePassword;

/**
 * Interface ProviderInterface
 * @package FM\SingleSignOnBundle\Manager\Http\Provider
 */
interface ProviderInterface
{
    /**
     * @param $otp
     * @return OneTimePassword|null
     */
    public function fetch($otp);
} 