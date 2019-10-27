<?php

namespace MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO;

interface MatchingDTOInterface
{
    /**
     * @return string
     */
    public function getAddress1();

    /**
     * @return string
     */
    public function getAddress2();

    /**
     * @return string
     */
    public function getCity();

    /**
     * @return string
     */
    public function getZip();

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @return string
     */
    public function getState();
}