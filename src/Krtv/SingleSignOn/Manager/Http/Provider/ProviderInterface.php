<?php

namespace Krtv\SingleSignOn\Manager\Http\Provider;

use Krtv\SingleSignOn\Model\OneTimePasswordInterface;

/**
 * Interface ProviderInterface
 * @package FM\SingleSignOnBundle\Manager\Http\Provider
 */
interface ProviderInterface
{
    /**
     * @param $otp
     * @return OneTimePasswordInterface|null
     */
    public function fetch($otp);
}
