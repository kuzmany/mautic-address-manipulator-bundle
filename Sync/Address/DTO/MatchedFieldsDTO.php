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


class MatchedFieldsDTO extends AbstractAddressDTO
{

    /**
     * @var array
     */
    private $settings;

    private $object;

    /**
     * MatchedFieldsDTO constructor.
     *
     * @param array $settings
     * @param string $object
     */
    public function __construct(array $settings, $object = '')
   {

       $this->settings = $settings;
       $this->object = $object;

       $this->address1 = $this->getValue('address1');
       $this->address2 = $this->getValue('address2');
       $this->city     = $this->getValue('city');
       $this->zipcode  = $this->getValue('zipcode');
       $this->country  = $this->getValue('country');
       $this->state    = $this->getValue('state');
   }

    /**
     * @param $alias
     *
     * @return string
     */
    private function getValue($alias)
    {
        $fieldAlias = $this->object.$alias;
        return isset($this->settings[$fieldAlias]) ? $this->settings[$fieldAlias] : '';
    }


}
