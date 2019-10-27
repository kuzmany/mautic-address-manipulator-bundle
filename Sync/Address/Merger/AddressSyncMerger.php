<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\Merger;

use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO\MatchedAddressDTO;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO\MatchingAddressDTO;

class AddressSyncMerger
{

    /**
     * @param MatchingAddressDTO $matchingCompanyAddressDTO
     * @param MatchedAddressDTO  $matchedContactAddressDTO
     *
     * @return array
     */
    public function dataToUpdate(MatchingAddressDTO $matchingCompanyAddressDTO, MatchedAddressDTO $matchedContactAddressDTO)
    {
        $fields = $matchedContactAddressDTO->getMatchedFields();
        $toUpdate = [];
        if ($fields->getAddress1() && $matchingCompanyAddressDTO->getAddress1()) {
            $toUpdate[$fields->getAddress1()] = $matchingCompanyAddressDTO->getAddress1();
        }
        if ($fields->getAddress2() && $matchingCompanyAddressDTO->getAddress2()) {
            $toUpdate[$fields->getAddress2()] = $matchingCompanyAddressDTO->getAddress2();
        }

        if ($fields->getCity() && $matchingCompanyAddressDTO->getCity()) {
            $toUpdate[$fields->getCity()] = $matchingCompanyAddressDTO->getCity();
        }

        if ($fields->getCountry() && $matchingCompanyAddressDTO->getCountry()) {
            $toUpdate[$fields->getCountry()] = $matchingCompanyAddressDTO->getCountry();
        }

        if ($fields->getState() && $matchingCompanyAddressDTO->getState()) {
            $toUpdate[$fields->getState()] = $matchingCompanyAddressDTO->getState();
        }

        return $toUpdate;
    }

}
