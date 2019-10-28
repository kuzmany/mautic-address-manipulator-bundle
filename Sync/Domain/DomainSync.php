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


use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\LeadBundle\Entity\CompanyLead;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\CompanyModel;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\IntegrationDisabledException;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\SkipMappingException;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\SyncSettingException;
use MauticPlugin\MauticAddressManipulatorBundle\Integration\AddressManipulatorSettings;

class DomainSync
{
    /**
     * @var AddressManipulatorSettings
     */
    private $addressManipulatorSettings;

    /**
     * @var ValidationSync
     */
    private $validationSync;

    /**
     * @var WinnerSync
     */
    private $winnerSync;

    /**
     * @var CompanyModel
     */
    private $companyModel;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * DomainSync constructor.
     *
     * @param AddressManipulatorSettings $addressManipulatorSettings
     * @param ValidationSync             $validationSync
     * @param WinnerSync                 $winnerSync
     * @param CompanyModel               $companyModel
     * @param LeadModel                  $leadModel
     */
    public function __construct(
        AddressManipulatorSettings $addressManipulatorSettings,
        ValidationSync $validationSync,
        WinnerSync $winnerSync,
        CompanyModel $companyModel,
        LeadModel $leadModel
    ) {

        $this->addressManipulatorSettings = $addressManipulatorSettings;
        $this->validationSync             = $validationSync;
        $this->winnerSync                 = $winnerSync;
        $this->companyModel               = $companyModel;
        $this->leadModel = $leadModel;
    }

    /**
     * @param Lead $lead
     *
     * @throws IntegrationDisabledException
     * @throws SkipMappingException
     * @throws SyncSettingException
     */
    public function execute(Lead $lead)
    {
        if (!$this->addressManipulatorSettings->hasDomainSync()) {
            throw new IntegrationDisabledException('Domain sync disabled');
        }
        /** @var Lead $lead */
        $lead = $this->leadModel->getEntity($lead->getId());

        $excludeDomains = ArrayHelper::getValue('exclude_domains', $this->addressManipulatorSettings->getSettings());
        $excludeDomains = end($excludeDomains);
       $this->validationSync->validationByLead($lead, $excludeDomains);

        // sync field already exists
        $field = $this->getSyncField();

        $this->syncWinnerDomain($lead, $field);


    }

    /**
     * @return int
     * @throws SyncSettingException
     */
    private function getSyncField()
    {
        $companySyncField = ArrayHelper::getValue('domain_field', $this->addressManipulatorSettings->getSettings());
        if (!$companySyncField) {
            throw new SyncSettingException('Sync field for domain not exist');
        }

        return $companySyncField;
    }

    /**
     * @param Lead $lead
     * @param      $syncField
     *
     * @throws SkipMappingException
     */
    private function syncWinnerDomain(Lead $lead, $syncField)
    {
        /** @var CompanyLead $leadPrimaryCompany */
        $leadPrimaryCompany = $this->companyModel->getCompanyLeadRepository()->findOneBy(['lead' => $lead, 'primary' => 1]);

        $company = $leadPrimaryCompany->getCompany();
        $company = $this->companyModel->getEntity($company->getId());
        if ($company->getFieldValue($syncField)) {
            throw new SkipMappingException();
        }

        $leadCompanies = $this->companyModel->getCompanyLeadRepository()->findBy(
            ['company' => $company, 'primary' => 1]
        );

        $valueTosync   = $this->winnerSync->getWinnerDomain($leadCompanies, $lead);

        $this->companyModel->setFieldValues($company, [$syncField => $valueTosync]);
        $this->companyModel->saveEntity($company);
    }
}
