<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Sync;

use Mautic\LeadBundle\Entity\Company;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\IntegrationDisabledException;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\SkipMappingException;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\SyncSettingException;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\AddressSync;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Domain\DomainSync;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Logger\AddressSyncLogger;

class SyncService
{
    /**
     * @var DomainSync
     */
    private $domainSync;

    /**
     * @var AddressSync
     */
    private $addressSync;

    /**
     * @var AddressSyncLogger
     */
    private $addressSyncLogger;

    /**
     * SyncService constructor.
     *
     * @param DomainSync        $domainSync
     * @param AddressSync       $addressSync
     * @param AddressSyncLogger $addressSyncLogger
     */
    public function __construct(DomainSync $domainSync, AddressSync $addressSync, AddressSyncLogger $addressSyncLogger)
    {
        $this->domainSync        = $domainSync;
        $this->addressSync       = $addressSync;
        $this->addressSyncLogger = $addressSyncLogger;
    }

    /**
     * @param Lead $lead
     */
    public function companyDomainSync(Lead $lead)
    {
        $logPrefix = 'Sync contact domain to company';
        try {
            $this->domainSync->execute($lead);
        } catch (SkipMappingException $skipMappingException) {
            $this->addressSyncLogger->log($skipMappingException->getMessage(), $logPrefix);
        } catch (IntegrationDisabledException $integrationDisabledException) {
            $this->addressSyncLogger->log($integrationDisabledException->getMessage(), $logPrefix);
        } catch (SyncSettingException $exception) {
            $this->addressSyncLogger->log($exception->getMessage(), $logPrefix);

        }

    }

    /**
     * @param Lead $lead
     */
    public function companyAddressSync(Lead $lead)
    {
        $logPrefix = 'Sync contact address to company';
        try {
            $this->addressSync->contactAddressToCompanyAddressSync($lead);
        } catch (SkipMappingException $skipMappingException) {
            $this->addressSyncLogger->log($skipMappingException->getMessage(), $logPrefix);
        } catch (IntegrationDisabledException $integrationDisabledException) {
            $this->addressSyncLogger->log($integrationDisabledException->getMessage(), $logPrefix);
        }

    }

    /**
     * @param Company $company
     */
    public function contactAddressSync(Company $company)
    {
        try {
            $this->addressSync->companyAddressToContactAddressSync($company, $this->addressSyncLogger);
        } catch (IntegrationDisabledException $integrationDisabledException) {
            $this->addressSyncLogger->log($integrationDisabledException->getMessage());
        }
    }
}
