<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Sync\Domain;


use Mautic\LeadBundle\Entity\CompanyLead;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\SkipMappingException;

class WinnerSync
{
    /**
     * @param array $leadCompanies
     * @param Lead  $lead
     *
     * @return false|string|void
     * @throws SkipMappingException
     */
    public function getWinnerDomain(array $leadCompanies, Lead $lead)
    {
        if (!$leadDomain = ValidationDomainSync::domainExists($lead->getEmail())) {
            throw new SkipMappingException('Not find domain in email address '.$lead->getEmail().' ');
        }

        // If just one company exists
        if (count($leadCompanies) == 1) {
            /** @var CompanyLead $leadCompany */
            $leadCompany = end($leadCompanies);
            if ($leadCompany->getLead()->getId() == $lead->getId()) {
                return $leadDomain;
            }
            throw new SkipMappingException('Wrong merge. Company '.$leadCompany->getCompany()->getName().' assigned to '.$lead->getId().' doesn\'t exist.');
        }

        return $this->winnerFromDomains($leadCompanies, $leadDomain);
    }

    /**
     * @param array $leadCompanies
     * @param string $leadDomain
     *
     * @return string
     * @throws SkipMappingException
     */
    private function winnerFromDomains(array $leadCompanies, $leadDomain)
    {
        /** @var CompanyLead $leadCompany */
        $domains = [];
        foreach ($leadCompanies as $leadCompany) {
            if ($domain = ValidationDomainSync::domainExists($leadCompany->getLead()->getEmail())) {
                $domains[$domain][] = 1;
            }
        }

        if (!isset($domains[$leadDomain])) {
            throw new SkipMappingException('Error during sync company domain');
        }

        $winnerCount = array_sum($domains[$leadDomain]);
        unset($domains[$leadDomain]) ;

        foreach ($domains as $domain=>$values) {
            $count = array_sum($domains[$domain]);
            if ($count >= $winnerCount) {
                throw new SkipMappingException('Looks like exists contacts assigned to company with more domain priority like actual domain '.$leadDomain);
            }
        }

        return $leadDomain;
    }

}
