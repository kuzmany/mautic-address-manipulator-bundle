<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Command;

use Mautic\CoreBundle\Command\ModeratedCommand;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\DTO\InputDAO;
use MauticPlugin\MauticAddressManipulatorBundle\Sync\SyncService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class AddressSyncCommand extends ModeratedCommand
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var SyncService
     */
    private $syncService;

    /**
     * NormalizeCommand constructor.
     *
     * @param LeadModel           $leadModel
     * @param SyncService         $syncService
     * @param TranslatorInterface $translator
     */
    public function __construct(
        LeadModel $leadModel,
        SyncService $syncService,
        TranslatorInterface $translator
    ) {
        parent::__construct();
        $this->translator  = $translator;
        $this->syncService = $syncService;
        $this->leadModel   = $leadModel;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mautic:address:manipulator:sync')
            ->setDescription('Address manipulator')
            ->addOption(
                '--start-datetime',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set start date/time for updated values in UTC timezone.',
                '-15 minutes'

            )
            ->addOption(
                '--end-datetime',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set start date/time for updated values in UTC timezone.',
                'now'
            )
            ->setHelp('This command update contact/companies addressbased on plugin settings');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = __CLASS__;
        if (!$this->checkRunStatus($input, $output, $key)) {
            return 0;
        }

        $inputDAO = new InputDAO($input->getOptions());
        $dateFrom = $inputDAO->getStartDateTime()->format('Y-m-d H:i:s');
        $dateTo = $inputDAO->getEndDateTime()->format('Y-m-d H:i:s');

        $contacts = $this->leadModel->getEntities(
            [
                'filter' => [
                    'force'            => [
                        [
                            'column' => 'l.dateModified',
                            'expr'   => 'gt',
                            'value'  => $dateFrom,
                        ],
                        [
                            'column' => 'l.dateModified',
                            'expr'   => 'lt',
                            'value'  => $dateTo,
                        ],
                    ],
                    'ignore_paginator' => true,
                ],
            ]
        );

        /** @var Lead $contact */
        foreach ($contacts as $contact) {
            $this->syncService->companyAddressSync($contact);
        }

        $output->writeln(
            $this->translator->trans(
                'mautic.addressmanipulator.processed_contacts',
                [
                    '%count%' => count($contacts),
                    '%dateFrom%' => $dateFrom,
                    '%dateTo%' => $dateTo
                ]
            )
        );

        return 0;
    }
}
