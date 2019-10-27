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


class MatchedAddressDTO implements MatchingDTOInterface
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

    private $object;

    /**
     * @var MatchedFieldsDTO
     */
    private $matchedFields;

    /**
     * @var
     */
    private $matchedFieldsDTO;

    /**
     * MatchedAddressDTO constructor.
     *
     * @param array  $profileFields
     * @param string $object
     */
    public function __construct(array $profileFields, MatchedFieldsDTO $matchedFieldsDTO, $object = '')
   {

       $this->profileFields = $profileFields;
       $this->object        = $object;
       $this->matchedFieldsDTO = $matchedFieldsDTO;

       $this->address1 = $this->getValue($this->matchedFieldsDTO->getAddress1());
       $this->address2 = $this->getValue($this->matchedFieldsDTO->getAddress2());
       $this->city = $this->getValue($this->matchedFieldsDTO->getCity());
       $this->zip = $this->getValue($this->matchedFieldsDTO->getZip());
       $this->country = $this->getValue($this->matchedFieldsDTO->getCountry());
       $this->state = $this->getValue($this->matchedFieldsDTO->getZip());
   }

    /**
     * @param $alias
     *
     * @return string
     */
    private function getValue($alias = '')
    {
        return $alias && isset($this->profileFields[$alias]) ? $this->profileFields[$alias] : '';
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

    /**
     * @return MatchedFieldsDTO
     */
    public function getMatchedFields()
    {
        return $this->matchedFieldsDTO;
    }

}
