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


class MatchedAddressDTO extends AbstractAddressDTO
{
    /**
     * @var array
     */
    private $profileFields;

    private $object;

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
       $this->city     = $this->getValue($this->matchedFieldsDTO->getCity());
       $this->zipcode  = $this->getValue($this->matchedFieldsDTO->getZip());
       $this->country  = $this->getValue($this->matchedFieldsDTO->getCountry());
       $this->state    = $this->getValue($this->matchedFieldsDTO->getState());
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
     * @return MatchedFieldsDTO
     */
    public function getMatchedFields()
    {
        return $this->matchedFieldsDTO;
    }

}
