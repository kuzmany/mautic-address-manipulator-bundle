<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Sync\Address;


use Mautic\LeadBundle\Entity\Company;
use Mautic\LeadBundle\Entity\CompanyLead;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\CompanyModel;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\IntegrationDisabledException;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\SkipMappingException;
use MauticPlugin\MauticAddressManipulatorBundle\Integration\AddressManipulatorSettings;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\Compare\AddressCompare;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO\MatchedAddressDTO;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO\MatchedFieldsDTO;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO\MatchingAddressDTO;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\Merger\AddressSyncMerger;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\Validator\AddressSyncValidator;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Logger\AddressSyncLogger;

class AddressSync
{
    /**
     * @var AddressManipulatorSettings
     */
    private $addressManipulatorSettings;

    /**
     * @var CompanyModel
     */
    private $companyModel;

    /**
     * @var AddressSyncValidator
     */
    private $addressSyncValidator;

    /**
     * @var AddressSyncMerger
     */
    private $addressSyncMerger;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var AddressCompare
     */
    private $addressCompare;

    /**
     * AddressSync constructor.
     *
     * @param AddressManipulatorSettings $addressManipulatorSettings
     * @param CompanyModel               $companyModel
     * @param LeadModel                  $leadModel
     * @param AddressSyncValidator       $addressSyncValidator
     * @param AddressSyncMerger          $addressSyncMerger
     * @param AddressCompare             $addressCompare
     */
    public function __construct(
        AddressManipulatorSettings $addressManipulatorSettings,
        CompanyModel $companyModel,
        LeadModel $leadModel,
        AddressSyncValidator $addressSyncValidator,
        AddressSyncMerger $addressSyncMerger,
        AddressCompare $addressCompare
    ) {
        $this->addressManipulatorSettings = $addressManipulatorSettings;
        $this->companyModel               = $companyModel;
        $this->addressSyncValidator       = $addressSyncValidator;
        $this->addressSyncMerger          = $addressSyncMerger;
        $this->leadModel                  = $leadModel;
        $this->addressCompare = $addressCompare;
    }

    /**
     * @param Lead $lead
     *
     * @throws IntegrationDisabledException
     * @throws SkipMappingException
     */
    public function companyAddressSync(Lead $lead)
    {
        if (!$this->addressManipulatorSettings->hasCompanyAddressSync()) {
            throw new IntegrationDisabledException('Company address sync disabled');
        }
        $this->addressCompare->setLead($lead);
        $primaryCompanyLeadEntity = $this->addressCompare->getPrimaryCompanyLeadEntity();
        $company = $primaryCompanyLeadEntity->getCompany();

        $fromAddressDTO = new MatchingAddressDTO($lead->getProfileFields());
        $matchedFieldsDTO          = new MatchedFieldsDTO($this->addressManipulatorSettings->getSettings(), 'company');
        $toAddressDTO = new MatchedAddressDTO($primaryCompanyLeadEntity->getCompany()->getProfileFields(), $matchedFieldsDTO, 'company');
        $this->addressSyncValidator->validate($fromAddressDTO, $toAddressDTO);
        $this->addressCompare->compareAddress($lead);

        $dataToUpdate = $this->addressSyncMerger->dataToUpdate(
            $fromAddressDTO,
            $toAddressDTO
        );

        $this->companyModel->setFieldValues($company, $dataToUpdate);
        $this->companyModel->saveEntity($company);



    }

    /**
     * @param Company           $company
     *
     * @param AddressSyncLogger $addressSyncLogger
     *
     * @throws IntegrationDisabledException
     */
    public function contactAddressSync(Company $company, AddressSyncLogger $addressSyncLogger)
    {
        if (!$this->addressManipulatorSettings->hasContactAddressSync()) {
            throw new IntegrationDisabledException('Contact address sync disabled');
        }
        $companyLeads = $this->companyModel->getCompanyLeadRepository()->findBy(['company' => $company]);
        /** @var CompanyLead $companyLead */
        foreach ($companyLeads as $companyLead) {
            try {
                $lead                 = $this->leadModel->getEntity(
                    $companyLead->getLead()->getId()
                );
                $contactProfileFields = $lead->getProfileFields();
                $companyProfileFields = $companyLead->getCompany()->getProfileFields();

                $matchingCompanyAddressDTO = new MatchingAddressDTO($companyProfileFields, 'company');
                $matchedFieldsDTO          = new MatchedFieldsDTO($this->addressManipulatorSettings->getSettings());
                $matchedContactAddressDTO  = new MatchedAddressDTO($contactProfileFields, $matchedFieldsDTO);

                $this->addressSyncValidator->validate($matchingCompanyAddressDTO, $matchedContactAddressDTO);

                $dataToUpdate = $this->addressSyncMerger->dataToUpdate(
                    $matchingCompanyAddressDTO,
                    $matchedContactAddressDTO
                );

                $this->leadModel->setFieldValues($lead, $dataToUpdate);
                $this->leadModel->saveEntity($lead);

            } catch (SkipMappingException $skipMappingException) {
                $addressSyncLogger->log($skipMappingException->getMessage());
                continue;
            }
        }


    }
}
