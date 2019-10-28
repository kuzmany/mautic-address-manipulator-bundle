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
            LeadEvents::ON_LEAD_DETACH => ['onLeadDetach', 0],
        ];
    }

    /**
     * @param LeadEvent $event
     *
     * @throws \MauticPlugin\MauticAddressManipulatorBundle\Exception\IntegrationDisabledException
     * @throws \MauticPlugin\MauticAddressManipulatorBundle\Exception\SyncSettingException
     */
    public function onLeadDetach(LeadEvent $event)
    {
        $this->syncService->companyAddressSync($event->getLead());
        $this->syncService->companyDomainSync($event->getLead());
    }

}
