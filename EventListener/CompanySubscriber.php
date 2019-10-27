<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\CompanyEvent;
use Mautic\LeadBundle\Event\LeadChangePrimaryCompanyEvent;
use Mautic\LeadBundle\LeadEvents;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\SyncService;

class CompanySubscriber extends CommonSubscriber
{

    /**
     * @var SyncService
     */
    private $syncService;

    /**
     * LeadSubscriber constructor.
     *
     * @param SyncService $syncService
     */
    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::COMPANY_POST_SAVE    => ['onCompanySave', 0],
        ];
    }


    /**
     * @param CompanyEvent $event
     *
     * @throws \MauticPlugin\MauticAddressManipulatorBundle\Exception\IntegrationDisabledException
     */
    public function onCompanySave(CompanyEvent $event)
    {
        $this->syncService->contactAddressSync($event->getCompany());
    }

}
