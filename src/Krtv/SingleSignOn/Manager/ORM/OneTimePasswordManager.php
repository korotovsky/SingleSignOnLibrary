<?php

namespace Krtv\SingleSignOn\Manager\ORM;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Krtv\SingleSignOn\Manager\OneTimePasswordManagerInterface;
use Krtv\SingleSignOn\Model\OneTimePasswordInterface;

/**
 * Class OneTimePasswordManager
 * @package Krtv\SingleSignOn\Manager\ORM
 */
class OneTimePasswordManager implements OneTimePasswordManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $class;

    /**
     * @param EntityManagerInterface $em
     * @param $modelClass
     */
    public function __construct(EntityManagerInterface $em, $modelClass)
    {
        $this->entityManager = $em;
        $this->class = $modelClass;
    }

    /**
     * Creates OTP
     *
     * @param string $hash
     * @throws \Exception
     * @return string
     */
    public function create($hash)
    {
        $otp = $this->entityManager->getRepository($this->class)->findOneBy(array(
            'hash' => $hash,
        ));

        if (!empty($otp)) {
            throw new \Exception(sprintf('A one-time-password for hash "%s" already exists', $hash));
        }

        $pass = $this->generateRandomValue();

        /** @var OneTimePasswordInterface $otp */
        $otp = new $this->class();
        $otp->setHash($hash);
        $otp->setPassword($pass);
        $otp->setUsed(false);
        $otp->setCreated(new \DateTime());

        try {
            $this->entityManager->persist($otp);
            $this->entityManager->flush();
        } catch (DBALException $e) {
            throw new \Exception('Could not create a one-time-password', $e->getCode(), $e);
        }

        return $otp->getPassword();
    }

    /**
     * Fetches OTP
     *
     * @param $pass
     * @return OneTimePasswordInterface|null
     */
    public function get($pass)
    {
        return $this->entityManager->getRepository($this->class)->findOneBy(array(
            'password' => $pass,
        ));
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
     * Invalidates OTP token
     *
     * @param OneTimePasswordInterface $otp
     * @return void
     */
    public function invalidate(OneTimePasswordInterface $otp)
    {
        $otp->setUsed(true);
        $this->entityManager->flush();
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function generateRandomValue()
    {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new \Exception('Could not produce a cryptographically strong random value. Please install/update the OpenSSL extension.');
        }

        $bytes = openssl_random_pseudo_bytes(64, $strong);

        if (true === $strong && false !== $bytes) {
            return base64_encode($bytes);
        }

        return base64_encode(hash('sha512', uniqid(mt_rand(), true), true));
    }
}
