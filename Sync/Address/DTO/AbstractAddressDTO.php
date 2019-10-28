<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO;


abstract class AbstractAddressDTO implements MatchingDTOInterface
{
    /**
     * @var string
     */
    protected $address1;

    /**
     * @var string
     */
    protected $address2;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $zipcode;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $state;


    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zipcode;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return bool
     */
    public function hasAddressKeyForSearch()
    {
        // If not exist one of the search fields, stop
        if (!$this->getAddress1() || !$this->getCity() || !$this->getCountry() || !$this->getZip() || !$this->getState()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getSearchKey()
    {
        return sprintf("%s%s%s%s%s", $this->address1, $this->city, $this->zipcode, $this->country, $this->state);
    }

}
