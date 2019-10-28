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
        try {
            $this->domainSync->execute($lead);
        } catch (SkipMappingException $skipMappingException) {
            $this->addressSyncLogger->log($skipMappingException->getMessage());
        } catch (IntegrationDisabledException $integrationDisabledException) {
            $this->addressSyncLogger->log($integrationDisabledException->getMessage());
        } catch (SyncSettingException $exception) {
            $this->addressSyncLogger->log($exception->getMessage());

        }

    }

    /**
     * @param Lead $lead
     */
    public function companyAddressSync(Lead $lead)
    {
        try {
            $this->addressSync->companyAddressSync($lead);
        } catch (SkipMappingException $skipMappingException) {
            $this->addressSyncLogger->log($skipMappingException->getMessage());
        } catch (IntegrationDisabledException $integrationDisabledException) {
            $this->addressSyncLogger->log($integrationDisabledException->getMessage());
        }

    }

    /**
     * @param Company $company
     */
    public function contactAddressSync(Company $company)
    {
        try {
            $this->addressSync->contactAddressSync($company, $this->addressSyncLogger);
        } catch (IntegrationDisabledException $integrationDisabledException) {
            $this->addressSyncLogger->log($integrationDisabledException->getMessage());
        }
    }
}
