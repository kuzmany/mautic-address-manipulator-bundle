<?php

return [
    'name'        => 'MauticAddressManipulatorBundle',
    'description' => 'Address manipulator for Mautic',
    'author'      => 'mtcextendee.com',
    'version'     => '1.0',
    'services'    => [
        'events' => [
            'mautic.addressmanipulator.lead.subscriber' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'mautic.addressmanipulator.sync'
                ],
            ],
            'mautic.addressmanipulator.company.subscriber' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\EventListener\CompanySubscriber::class,
                'arguments' => [
                    'mautic.addressmanipulator.sync'
                ],
            ],
        ],
        'others' => [
            'mautic.addressmanipulator.settings' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\Integration\AddressManipulatorSettings::class,
                'arguments' => [
                    'mautic.helper.integration',
                ],
            ],

            'mautic.addressmanipulator.sync' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\Sync\SyncService::class,
                'arguments' => [
                    'mautic.addressmanipulator.sync.domain',
                    'mautic.addressmanipulator.sync.address',
                    'mautic.addressmanipulator.sync.logger'
                ],
            ],
            'mautic.addressmanipulator.sync.logger' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\Sync\Logger\AddressSyncLogger::class,
                'arguments' => [
                    'mautic.addressmanipulator.settings',
                    'monolog.logger.mautic'
                ],
            ],

            'mautic.addressmanipulator.sync.address' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\AddressSync::class,
                'arguments' => [
                    'mautic.addressmanipulator.settings',
                    'mautic.lead.model.company',
                    'mautic.lead.model.lead',
                    'mautic.addressmanipulator.sync.address.validator',
                    'mautic.addressmanipulator.sync.address.merger',
                    'mautic.addressmanipulator.sync.address.compare'
                ],
            ],
            'mautic.addressmanipulator.sync.address.validator' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\Validator\AddressSyncValidator::class,
                'arguments' => [
                ],
            ],
            'mautic.addressmanipulator.sync.address.merger' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\Merger\AddressSyncMerger::class,
                'arguments' => [
                ],
            ],

            'mautic.addressmanipulator.sync.address.compare' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\Sync\Address\Compare\AddressCompare::class,
                'arguments' => [
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company'
                ],
            ],

            'mautic.addressmanipulator.sync.domain' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\Sync\Domain\DomainSync
                ::class,
                'arguments' => [
                    'mautic.addressmanipulator.settings',
                    'mautic.addressmanipulator.sync.domain.validation',
                    'mautic.addressmanipulator.sync.domain.winner',
                    'mautic.lead.model.company',
                    'mautic.lead.model.lead'
                ],
            ],
            'mautic.addressmanipulator.sync.domain.validation' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\Sync\Domain\ValidationSync::class,
                'arguments' => [
                ],
            ],
            'mautic.addressmanipulator.sync.domain.winner' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\Sync\Domain\WinnerSync::class,
                'arguments' => [
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.addressmanipulator' => [
                'class'     => \MauticPlugin\MauticAddressManipulatorBundle\Integration\AddressManipulatorIntegration::class,
                'arguments' => [
                ],
            ],
        ],
    ],
];
