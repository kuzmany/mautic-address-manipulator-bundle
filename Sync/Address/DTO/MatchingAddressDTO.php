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


class MatchingAddressDTO implements MatchingDTOInterface
{
    /**
     * @var string
     */
    private $address1;

    /**
     * @var string
     */
    private $address2;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $zip;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $state;

    /**
     * @var array
     */
    private $profileFields;

    /**
     * @var string
     */
    private $object;

    public function __construct(array $profileFields, $object= '')
   {
       $this->profileFields = $profileFields;
       $this->object = $object;

       $this->address1 = $this->getValue('address1');
       $this->address2 = $this->getValue('address2');;
       $this->city = $this->getValue('city');
       $this->zip = $this->getValue('zip');
       $this->country = $this->getValue('country');;
       $this->state = $this->getValue('state');;

   }

    /**
     * @param $profileFields
     * @param $object
     * @param $alias
     *
     * @return string
     */
    private function getValue($alias)
    {
        $fieldAlias = $this->object.$alias;
        return isset($this->profileFields[$fieldAlias]) ? $this->profileFields[$fieldAlias] : '';
    }

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
        return $this->zip;
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

}
