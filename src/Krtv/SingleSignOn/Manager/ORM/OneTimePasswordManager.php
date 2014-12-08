<?php

namespace Krtv\SingleSignOn\Manager\ORM;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Krtv\SingleSignOn\Manager\OneTimePasswordManagerInterface;
use Krtv\SingleSignOn\Model\OneTimePassword;

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
     * @param string $hash
     * @return string
     * @throws \Exception
     */
    public function create($hash)
    {
        $otp = $this->entityManager->getRepository($this->class)->findByHash($hash);
        if (!empty($otp)) {
            throw new \Exception(sprintf('A one-time-password for hash "%s" already exists', $hash));
        }

        $password = null;

        $i = 0;

        // 20 tries should be more than enough
        while (++$i < 20) {
            $pass = $this->generateRandomValue();

            /** @var OneTimePassword $otp */
            $otp = new $this->class();

            // We have unique index on `password` field, so try to insert immediate
            // To prevent unnecessary SELECT query
            try {
                $otp->setHash($hash);
                $otp->setPassword($pass);
                $otp->setUsed(false);
                $otp->setCreated(new \DateTime());

                $this->entityManager->persist($otp);
                $this->entityManager->flush();
            } catch (DBALException $e) {
                // Catch all DBAL errors here
            }

            if ($otp->getId() !== null) {
                $password = $otp->getPassword();

                break;
            }
        }

        if ($password === null) {
            throw new \Exception('Could not create a one-time-password');
        }

        return $password;
    }

    /**
     * @param $pass
     * @return \Krtv\SingleSignOn\Model\OneTimePassword|null
     */
    public function get($pass)
    {
        return $this->entityManager->getRepository($this->class)->findOneByPassword($pass);
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
     * @param OneTimePassword $otp
     */
    public function invalidate(OneTimePassword $otp)
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
