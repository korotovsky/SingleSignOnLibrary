<?php

namespace Krtv\SingleSignOn\Model;

/**
 * Interface OneTimePasswordInterface
 * @package Krtv\SingleSignOn\Model
 */
interface OneTimePasswordInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password);

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword();

    /**
     * Set hash
     *
     * @param string $hash
     */
    public function setHash($hash);

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash();

    /**
     * Set used
     *
     * @param boolean $used
     */
    public function setUsed($used);

    /**
     * Get used
     *
     * @return boolean
     */
    public function getUsed();

    /**
     * Set created
     *
     * @param \Datetime $created
     */
    public function setCreated($created);

    /**
     * Get created
     *
     * @return \Datetime
     */
    public function getCreated();
}
