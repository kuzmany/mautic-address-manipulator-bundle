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
     * SyncService constructor.
     *
     * @param DomainSync  $domainSync
     * @param AddressSync $addressSync
     */
    public function __construct(DomainSync $domainSync, AddressSync $addressSync)
    {
        $this->domainSync = $domainSync;
        $this->addressSync = $addressSync;
    }

    /**
     * @param Lead $lead
     *
     * @throws IntegrationDisabledException
     * @throws SyncSettingException
     */
    public function companyDomainSync(Lead $lead)
    {
        try {
            $this->domainSync->execute($lead);
        } catch (SkipMappingException $skipMappingException) {
        }catch (IntegrationDisabledException $integrationDisabledException)
        {
        }catch (SyncSettingException $exception)
        {
        }

    }

    public function companyAddressSync(Lead $lead)
    {
        $this->addressSync->companyAddressSync($lead);
    }

    /**
     * @param Company $company
     *
     * @throws IntegrationDisabledException
     */
    public function contactAddressSync(Company $company)
    {
        $this->addressSync->contactAddressSync($company);
    }
}
