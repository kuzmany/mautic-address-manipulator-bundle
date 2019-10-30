<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\Compare;

use Mautic\LeadBundle\Entity\Company;
use Mautic\LeadBundle\Entity\CompanyLead;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\CompanyModel;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\SkipMappingException;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO\MatchingAddressDTO;

class AddressCompare
{
    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var CompanyModel
     */
    private $companyModel;

    /**
     * @var CompanyLead
     */
    private $primaryCompanyLeadEntity;

    /**
     * @var array
     */
    private $leadsFromPrimaryCompany;

    /**
     * @var Lead
     */
    private $lead;

    /**
     * AddressCompare constructor.
     *
     * @param LeadModel    $leadModel
     * @param CompanyModel $companyModel
     */
    public function __construct(LeadModel $leadModel, CompanyModel $companyModel)
    {
        $this->leadModel = $leadModel;
        $this->companyModel = $companyModel;
    }


    public function compareAddress()
    {
        $this->primaryCompanyLeadEntity = $this->getPrimaryCompanyLeadEntity($this->lead);
        $this->leadsFromPrimaryCompany  = $this->getLeadsByPrimaryCompany(
            $this->primaryCompanyLeadEntity->getCompany()
        );
        $this->addressComparator();
    }

    /**
     * @param Lead $lead
     */
    public function setLead(Lead $lead)
    {
        $this->lead = $this->leadModel->getEntity($lead->getId());
    }


    /**
     * @return CompanyLead
     * @throws SkipMappingException
     */
    public function getPrimaryCompanyLeadEntity()
    {
        /** @var CompanyLead $leadPrimaryCompanyEntity */
        $leadPrimaryCompanyEntity = $this->companyModel->getCompanyLeadRepository()->findOneBy(['lead' => $this->lead, 'primary' => 1]);
        if (!$leadPrimaryCompanyEntity) {
            throw new SkipMappingException('Primary company doesn\'t exist.');
        }

        $company = $this->companyModel->getEntity($leadPrimaryCompanyEntity->getCompany()->getId());
        $leadPrimaryCompanyEntity->setCompany($company);

        return $leadPrimaryCompanyEntity;
    }

    /**
     * @param Company $company
     *
     * @return array
     * @throws SkipMappingException
     */
    private function getLeadsByPrimaryCompany(Company $company)
    {
        $leadsByPrimaryCompany = $this->companyModel->getCompanyLeadRepository()->findBy(['company' => $company, 'primary' => 1]);
        if (!$leadsByPrimaryCompany) {
            throw new SkipMappingException('Primary company doesn\'t exist.');
        }
        $leads = [];
        /** @var CompanyLead $primaryCompany */
        foreach ($leadsByPrimaryCompany as $primaryCompany) {
            $leads[$primaryCompany->getLead()->getId()] = $this->leadModel->getEntity($primaryCompany->getLead()->getId());
        }
        return $leads;
    }

    /**
     * @return array
     */
    private function getLeadsBySearchKey()
    {
        $leads = [];
        /** @var Lead $leadFromPrimaryCompany */
        foreach ($this->leadsFromPrimaryCompany as $leadFromPrimaryCompany) {
            $profileFields = $leadFromPrimaryCompany->getProfileFields();
            $matchingAddress = new MatchingAddressDTO($profileFields);
            if ($matchingAddress->hasAddressKeyForSearch()) {
                $leads[$matchingAddress->getSearchKey()][] = $leadFromPrimaryCompany;
            }
        }
        if (empty($leads)) {
            throw new SkipMappingException('Didn\'t find any address for company'.$this->primaryCompanyLeadEntity->getCompany()->getId());
        }
        return $leads;
    }

    private function addressComparator()
    {
        $matchingAddressDTO = new MatchingAddressDTO($this->lead->getProfileFields());
        $leadsBySearchKey = $this->getLeadsBySearchKey();
        $winnerCount = count($leadsBySearchKey[$matchingAddressDTO->getSearchKey()]);
        if ($winnerCount < 2) {
            throw new SkipMappingException('Address count for lead '.$this->lead->getId().' is '.$winnerCount.'. Expected 2 at least.');
        }
        unset($leadsBySearchKey[$matchingAddressDTO->getSearchKey()]);

        foreach ($leadsBySearchKey as $searchKey=>$lead) {
            $count = array_sum($leadsBySearchKey[$searchKey]);
            if ($count >= $winnerCount) {
                throw new SkipMappingException('Looks like the address '.$searchKey.' has '.$count.' addresses. Address count for lead '.$lead->getId().' is '.$winnerCount.'. ');
            }
        }

    }
}
