<?php

namespace Krtv\SingleSignOn\Manager\Http\Provider\Guzzle;

use Krtv\SingleSignOn\Model\OneTimePassword;
use Krtv\SingleSignOn\Manager\Http\Provider\ProviderInterface;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\HttpException;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Class OneTimePasswordProvider
 * @package FM\SingleSignOnBundle\Manager\Http\Provider\Guzzle
 */
class OneTimePasswordProvider implements ProviderInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Client $client
     * @param $resource
     */
    public function __construct(Client $client, $resource, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->client->setBaseUrl($resource);

        $this->logger = $logger;
    }

    /**
     * @param $otp
     * @return OneTimePassword|null|void
     * @throws HttpException
     * @throws \Exception
     */
    public function fetch($otp)
    {
        try {
            $request = $this->client->get(sprintf('?_otp=%s', $otp));
            $response = $request->send();

            // JMSSerializer here ??
            $data = json_decode($response->getBody(true), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                if (null !== $this->logger) {
                    $this->logger->err(sprintf('json_decode error. Gateway response: %s', $response));
                }

                return null;
            }

            $otp = new OneTimePassword();
            $otp->setCreated($data['data']['created_at']);
            $otp->setHash($data['data']['hash']);
            $otp->setPassword($data['data']['password']);
            $otp->setUsed($data['data']['is_used']);

            return $otp;
        } catch (HttpException $e) {
            if (null !== $this->logger) {
                $this->logger->err($e->getMessage());
            }

            return null;
        } catch (\Exception $e) {
            if (null !== $this->logger) {
                $this->logger->crit($e->getMessage());
            }

            throw $e;
        }
    }
}
