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
use Mautic\LeadBundle\Event\LeadChangeCompanyEvent;
use Mautic\LeadBundle\Event\LeadEvent;
use Mautic\LeadBundle\LeadEvents;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\SyncService;

class LeadSubscriber extends CommonSubscriber
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
            LeadEvents::LEAD_COMPANY_CHANGE => ['onLeadCompanyChange', 0],
          //  LeadEvents::LEAD_POST_SAVE => ['onLeadPostSave', 0],
        ];
    }

    /**
     * @param LeadChangeCompanyEvent $event
     */
    public function onLeadCompanyChange(LeadChangeCompanyEvent $event)
    {
        $this->syncService->companyAddressSync($event->getLead());
        $this->syncService->companyDomainSync($event->getLead());
    }

    /**
     * @param LeadEvent $event
     */
    public function onLeadPostSave(LeadEvent $event)
    {
        $this->syncService->companyAddressSync($event->getLead());
        $this->syncService->companyDomainSync($event->getLead());
    }


}
