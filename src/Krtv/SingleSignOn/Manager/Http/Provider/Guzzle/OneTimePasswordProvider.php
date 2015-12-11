<?php

namespace Krtv\SingleSignOn\Manager\Http\Provider\Guzzle;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Krtv\SingleSignOn\Model\OneTimePassword;
use Krtv\SingleSignOn\Model\OneTimePasswordInterface;
use Krtv\SingleSignOn\Manager\Http\Provider\ProviderInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OneTimePasswordProvider
 * @package FM\SingleSignOnBundle\Manager\Http\Provider\Guzzle
 */
class OneTimePasswordProvider implements ProviderInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ClientInterface $client
     * @param string          $resource
     * @param LoggerInterface $logger
     */
    public function __construct(ClientInterface $client, $resource, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->resource = $resource;
        $this->logger = $logger;
    }

    /**
     * @param string $otp
     * @return OneTimePasswordInterface|null
     * @throws RequestException
     * @throws \Exception
     */
    public function fetch($otp)
    {
        try {
            $response = $this->client->request('GET', sprintf('%s?_otp=%s', $this->resource, $otp));

            // JMSSerializer here ??
            $data = json_decode($response->getBody()->getContents(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                if (null !== $this->logger) {
                    $this->logger->error(sprintf('json_decode error. Gateway response: %s', $response->getBody()->getContents()));
                }

                return null;
            }

            $otp = new OneTimePassword();
            $otp->setCreated(new \DateTime($data['data']['created_at']));
            $otp->setHash($data['data']['hash']);
            $otp->setPassword($data['data']['password']);
            $otp->setUsed($data['data']['is_used']);

            return $otp;
        } catch (RequestException $e) {
            if (null !== $this->logger) {
                $this->logger->error($e->getMessage());
            }

            return null;
        } catch (\Exception $e) {
            if (null !== $this->logger) {
                $this->logger->critical($e->getMessage());
            }

            throw $e;
        }
    }
}
