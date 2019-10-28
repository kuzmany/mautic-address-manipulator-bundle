<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\Validator;


use MauticPlugin\MauticAddressManipulatorBundle\Exception\SkipMappingException;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO\MatchedAddressDTO;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO\MatchingAddressDTO;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO\MatchingDTOInterface;

class AddressSyncValidator
{

    /**
     * @param MatchingAddressDTO $matchingAddressDTO
     * @param MatchedAddressDTO  $matchedAddressDTO
     *
     * @throws SkipMappingException
     */
    public function validate(
        MatchingAddressDTO $matchingAddressDTO,
        MatchedAddressDTO $matchedAddressDTO
    ) {
        if ($matchedAddressDTO->getAddress1()) {
            throw new SkipMappingException(
                sprintf("Address1 should be empty. Now it's %s", $matchedAddressDTO->getAddress1())
            );
        }

        if (!$this->emptyAddress($matchedAddressDTO)) {
            if (!$this->sameValueDTOValidation($matchingAddressDTO, $matchedAddressDTO)) {
                throw new SkipMappingException('Looks like address without address1 '. $matchingAddressDTO->getSearchKey().' doesn\'t match '.$matchedAddressDTO->getSearchKey());
            }
        }
    }


    /**
     * @param MatchingDTOInterface $matchDTO
     *
     * @return bool
     */
    private function emptyAddress(MatchingDTOInterface $matchDTO)
    {
        if (!$matchDTO->getAddress1() &&
            !$matchDTO->getZip() &&
            !$matchDTO->getCountry() &&
            !$matchDTO->getState() &&
            !$matchDTO->getCity()
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param MatchingDTOInterface $from
     * @param MatchingDTOInterface $to
     *
     * @return bool
     */
    private function sameValueDTOValidation(MatchingDTOInterface $from, MatchingDTOInterface $to)
    {
        // If not exist one of the search fields, stop
        if (!$from->getCity() || !$from->getCountry() || $from->getZip()) {
            return false;
        }

        if (
            $from->getCity() == $to->getCity() &&
            $from->getCountry() == $to->getCountry() &&
            $from->getZip() == $to->getZip() &&
            $from->getState() == $to->getState()
        ) {
            return true;
        }

        return false;

    }
}
